package encoders

import (
	"bufio"
	"bytes"
	"context"
	"fmt"
	"log"
	"os"
	"os/exec"
	"path/filepath"
	"strconv"
	"strings"
	"sync"
)

const (
	CHUNK_DURATION = 60
)

var _ HLSEncoder = (*SplitEncoder)(nil)

type SplitEncoder struct {
	UseGPU     bool
	ProgressFn func(int)
}

func NewSplitEncoder(useGPU bool) *SplitEncoder {
	return &SplitEncoder{UseGPU: useGPU}
}

func (e *SplitEncoder) EncodeVideo(ctx context.Context, inputPath, outputDir string, res Resolution) (string, error) {
	log.Printf("split-encoder: %v %v\n", res.Name, inputPath)

	chunksDir := outputDir + "-chunks"
	resDir := filepath.Join(outputDir, res.Name)

	// Phase 1: Split into chunks
	chunks, err := splitIntoChunks(ctx, inputPath, chunksDir, e.ProgressFn)
	if err != nil {
		return "", err
	}
	defer os.RemoveAll(chunksDir)

	if err := os.MkdirAll(resDir, 0755); err != nil {
		return "", fmt.Errorf("error creating dir: %v", err)
	}

	log.Printf("split-encoder: [%v chunks] %v %v\n", len(chunks), res.Name, inputPath)

	sem := make(chan struct{}, MAX_PARALLEL)
	var wg sync.WaitGroup

	// Phase 2: Parallel Encoding for the specific resolution
	for i, chunkPath := range chunks {
		// IMPORTANT: Each chunk needs its own subdirectory for the merge logic to work
		chunkOutDir := filepath.Join(resDir, fmt.Sprintf("chunk%03d", i))

		sem <- struct{}{}
		wg.Add(1)

		go func(r Resolution, in, out string) {
			defer wg.Done()
			defer func() { <-sem }()

			if e.UseGPU {
				_, err := NewGPUEncoder().EncodeVideo(ctx, in, out, r)
				if err != nil {
					log.Printf("GPU chunk encode failed [%s]: %v", in, err)
				}
			} else {
				os.MkdirAll(out, 0755)

				vf := fmt.Sprintf("scale=-2:%d", r.Height)
				cmd := exec.CommandContext(ctx, "ffmpeg", "-y",
					"-i", in,
					"-c:v", "libx264",
					"-preset", "fast",
					"-pix_fmt", "yuv420p",
					"-vf", vf,
					"-b:v", r.Bitrate,
					"-c:a", "aac",
					"-b:a", "128k",
					"-hls_time", "4",
					"-hls_list_size", "0",
					"-hls_segment_filename", filepath.Join(out, "seg%03d.ts"),
					filepath.Join(out, "index.m3u8"),
				)
				var stderr bytes.Buffer
				cmd.Stderr = &stderr

				if err := cmd.Run(); err != nil {
					log.Printf("CPU encode failed for chunk %s: %v — %s", in, err, stderr.String())
				}
			}
		}(res, chunkPath, chunkOutDir)
	}
	wg.Wait()

	// Phase 3: Merge playlists for this resolution only
	if err := mergeChunkPlaylists(resDir, len(chunks)); err != nil {
		return "", err
	}

	// Phase 4: Create master playlist
	return createMasterPlaylist(outputDir, []Resolution{res})
}

// splitIntoChunks splits the video into chunkDuration-second segments using stream copy.
// progressFn is called with 0–99 as the split proceeds.
func splitIntoChunks(ctx context.Context, inputPath, chunksDir string, progressFn func(pct int)) ([]string, error) {
	if err := os.MkdirAll(chunksDir, 0755); err != nil {
		return nil, err
	}

	totalDuration, err := getVideoDuration(inputPath)
	if err != nil {
		log.Printf("splitIntoChunks: ffprobe warning (progress disabled): %v", err)
		totalDuration = 0
	}

	pattern := filepath.Join(chunksDir, "chunk%03d.mkv")
	var stderr bytes.Buffer
	cmd := exec.CommandContext(ctx, "ffmpeg", "-y",
		"-i", inputPath,
		"-codec", "copy",
		"-f", "segment",
		"-segment_time", strconv.Itoa(CHUNK_DURATION),
		"-reset_timestamps", "1",
		"-progress", "pipe:1", "-nostats",
		pattern,
	)
	cmd.Stderr = &stderr

	stdoutPipe, err := cmd.StdoutPipe()
	if err != nil {
		return nil, err
	}
	if err := cmd.Start(); err != nil {
		return nil, err
	}

	var readerDone sync.WaitGroup
	readerDone.Add(1)
	go func() {
		defer readerDone.Done()
		scanner := bufio.NewScanner(stdoutPipe)
		for scanner.Scan() {
			if totalDuration <= 0 || progressFn == nil {
				continue
			}
			line := scanner.Text()
			if strings.HasPrefix(line, "out_time_us=") {
				usStr := strings.TrimSpace(strings.TrimPrefix(line, "out_time_us="))
				us, err := strconv.ParseInt(usStr, 10, 64)
				if err == nil && us > 0 {
					pct := int(float64(us) / (totalDuration * 1_000_000) * 100)
					if pct > 99 {
						pct = 99
					}
					progressFn(pct)
				}
			}
		}
	}()

	err = cmd.Wait()
	readerDone.Wait()
	if err != nil {
		return nil, fmt.Errorf("%w — %s", err, stderr.String())
	}

	var chunks []string
	for i := 0; ; i++ {
		p := filepath.Join(chunksDir, fmt.Sprintf("chunk%03d.mkv", i))
		if _, err := os.Stat(p); os.IsNotExist(err) {
			break
		}
		chunks = append(chunks, p)
	}
	if len(chunks) == 0 {
		return nil, fmt.Errorf("segment muxer produced no chunks")
	}
	return chunks, nil
}

// mergeChunkPlaylists reads resDir/chunk{i}/index.m3u8 for i in [0, numChunks)
// and writes a single resDir/index.m3u8. A #EXT-X-DISCONTINUITY tag is inserted
// between chunks because each was encoded independently with reset timestamps.
func mergeChunkPlaylists(resDir string, numChunks int) error {
	type seg struct{ inf, file string }
	type chunkData struct{ segs []seg }

	chunks := make([]chunkData, 0, numChunks)
	var maxTargetDur float64

	for i := 0; i < numChunks; i++ {
		playlistPath := filepath.Join(resDir, fmt.Sprintf("chunk%03d", i), "index.m3u8")
		data, err := os.ReadFile(playlistPath)
		if err != nil {
			return fmt.Errorf("read chunk%03d playlist: %w", i, err)
		}
		lines := strings.Split(strings.ReplaceAll(string(data), "\r\n", "\n"), "\n")
		var cd chunkData
		for j := 0; j < len(lines); j++ {
			line := strings.TrimSpace(lines[j])
			if strings.HasPrefix(line, "#EXT-X-TARGETDURATION:") {
				dur, _ := strconv.ParseFloat(strings.TrimPrefix(line, "#EXT-X-TARGETDURATION:"), 64)
				if dur > maxTargetDur {
					maxTargetDur = dur
				}
			} else if strings.HasPrefix(line, "#EXTINF:") {
				j++
				if j < len(lines) {
					segFile := strings.TrimSpace(lines[j])
					if segFile != "" && !strings.HasPrefix(segFile, "#") {
						cd.segs = append(cd.segs, seg{
							inf:  line,
							file: fmt.Sprintf("chunk%03d/%s", i, segFile),
						})
					}
				}
			}
		}
		chunks = append(chunks, cd)
	}

	if maxTargetDur == 0 {
		maxTargetDur = 6
	}
	targetDur := int(maxTargetDur)
	if float64(targetDur) < maxTargetDur {
		targetDur++
	}

	f, err := os.Create(filepath.Join(resDir, "index.m3u8"))
	if err != nil {
		return err
	}
	defer f.Close()

	fmt.Fprintln(f, "#EXTM3U")
	fmt.Fprintln(f, "#EXT-X-VERSION:3")
	fmt.Fprintf(f, "#EXT-X-TARGETDURATION:%d\n", targetDur)
	fmt.Fprintln(f, "#EXT-X-MEDIA-SEQUENCE:0")
	for i, chunk := range chunks {
		if i > 0 {
			fmt.Fprintln(f, "#EXT-X-DISCONTINUITY")
		}
		for _, s := range chunk.segs {
			fmt.Fprintln(f, s.inf)
			fmt.Fprintln(f, s.file)
		}
	}
	fmt.Fprintln(f, "#EXT-X-ENDLIST")
	return nil
}

// createMasterPlaylist writes (or updates) an HLS master playlist.
// If a master.m3u8 already exists, it reads the previously encoded resolutions
// and merges them with the newly completed ones so no quality level is lost.
func createMasterPlaylist(outputPath string, completed []Resolution) (string, error) {
	playlistPath := filepath.Join(outputPath, "master.m3u8")

	all := mergeResolutions(readMasterResolutions(playlistPath), completed)

	f, err := os.Create(playlistPath)
	if err != nil {
		return "", err
	}
	defer f.Close()

	fmt.Fprintln(f, "#EXTM3U")
	for _, res := range all {
		fmt.Fprintf(f, "#EXT-X-STREAM-INF:BANDWIDTH=%d\n", res.Bandwidth)
		fmt.Fprintf(f, "%s/index.m3u8\n", res.Name)
	}
	return playlistPath, nil
}

// readMasterResolutions parses an existing master.m3u8 and returns known resolutions listed in it.
func readMasterResolutions(playlistPath string) []Resolution {
	data, err := os.ReadFile(playlistPath)
	if err != nil {
		return nil
	}
	known := []Resolution{Res360p, Res480p, Res720p, Res1080p}
	var found []Resolution
	for _, line := range strings.Split(string(data), "\n") {
		line = strings.TrimSpace(line)
		for _, r := range known {
			if line == r.Name+"/index.m3u8" {
				found = append(found, r)
				break
			}
		}
	}
	return found
}

// mergeResolutions deduplicates and returns all resolutions ordered 360p→720p→1080p.
func mergeResolutions(existing, incoming []Resolution) []Resolution {
	seen := make(map[string]bool)
	for _, r := range existing {
		seen[r.Name] = true
	}
	for _, r := range incoming {
		seen[r.Name] = true
	}
	var result []Resolution
	for _, r := range []Resolution{Res360p, Res480p, Res720p, Res1080p} {
		if seen[r.Name] {
			result = append(result, r)
		}
	}
	return result
}
