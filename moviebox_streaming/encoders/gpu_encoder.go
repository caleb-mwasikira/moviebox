package encoders

import (
	"bytes"
	"context"
	"fmt"
	"log"
	"os"
	"os/exec"
	"path/filepath"
	"runtime"
	"strconv"
	"strings"
	"time"
)

const (
	MAX_PARALLEL = 2
)

var _ HLSEncoder = (*GPUEncoder)(nil)

type GPUEncoder struct{}

func NewGPUEncoder() *GPUEncoder {
	return &GPUEncoder{}
}

func (e *GPUEncoder) EncodeVideo(ctx context.Context, inputPath, outputDir string, res Resolution) (string, error) {
	resDir := filepath.Join(outputDir, res.Name)
	if err := os.MkdirAll(resDir, 0755); err != nil {
		return "", fmt.Errorf("error creating dir: %v", err)
	}

	log.Printf("EncodeVideo: GPUEncoder %v: %v\n", res.Name, inputPath)
	cfg := getEncodeConfig()

	manifestPath := filepath.Join(resDir, "index.m3u8")
	segmentPattern := filepath.Join(resDir, "seg%03d.ts")

	vf := fmt.Sprintf(
		"format=p010le,hwupload=extra_hw_frames=256,vpp_qsv=w=-1:h=%d:format=nv12",
		res.Height,
	)

	cmd := exec.CommandContext(ctx,
		"ffmpeg",
		"-y",
		// QSV device init
		"-init_hw_device", "qsv=hw",
		"-filter_hw_device", "hw",
		// Fix timestamp issues
		"-fflags", "+genpts",
		"-i", inputPath,
		// Video filter chain
		"-vf", vf,
		// Video encode
		"-c:v", "h264_qsv",
		"-preset", cfg.preset,
		"-async_depth", "8",
		"-bf", "3",
		"-g", "60",
		"-b:v", res.Bitrate,
		"-maxrate", res.Bitrate,
		"-bufsize", "10M",
		"-low_power", "0",
		"-fps_mode", "cfr",
		// Audio
		"-c:a", "aac",
		"-b:a", "128k",
		// HLS
		"-hls_time", "6",
		"-hls_playlist_type", "vod",
		"-hls_flags", "independent_segments",
		"-hls_segment_type", "mpegts",
		"-hls_list_size", "0",
		"-hls_segment_filename", segmentPattern,
		// Output
		"-f", "hls",
		manifestPath,
	)

	var stderr bytes.Buffer
	cmd.Stdout = os.Stdout
	cmd.Stderr = &stderr

	if err := cmd.Run(); err != nil {
		return "", fmt.Errorf(
			"GPU encoding failed: %w\nFFMPEG LOG:\n%s",
			err,
			stderr.String(),
		)
	}

	masterPath, err := createMasterPlaylist(outputDir, []Resolution{res})
	if err != nil {
		return manifestPath, nil
	}
	return masterPath, nil
}

// func (e *GPUEncoder) EncodeVideo(inputPath, outputDir string, res Resolution) (string, error) {
// 	// 1. Ensure a fresh directory exists for this specific resolution
// 	os.RemoveAll(outputDir)
// 	if err := os.MkdirAll(outputDir, 0755); err != nil {
// 		return "", fmt.Errorf("error creating dir: %v", err)
// 	}

// 	log.Printf("gpu-encoder: %v %v\n", res.Name, inputPath)

// 	cfg := getEncodeConfig()

// 	// 2. Use the 'res.Height' and 'res.Bitrate' fields from the passed Resolution
// 	out := filepath.Join(outputDir, "index.m3u8")
// 	// cmd := exec.Command("ffmpeg", "-y",
// 	// 	"-hwaccel", "qsv",
// 	// 	"-c:v", "h264_qsv",
// 	// 	"-i", inputPath,
// 	// 	"-vf", fmt.Sprintf("vpp_qsv=w=-1:h=%d", res.Height),
// 	// 	"-c:v", "h264_qsv",
// 	// 	"-preset", cfg.preset,
// 	// 	"-b:v", res.Bitrate,
// 	// 	"-c:a", "aac", "-b:a", "128k",
// 	// 	"-hls_time", "6",
// 	// 	"-hls_list_size", "0",
// 	// 	"-hls_segment_filename", filepath.Join(outputDir, "seg%03d.ts"),
// 	// 	"-f", "hls", out,
// 	// )

// 	// 1. -init_hw_device qsv=hw: Global device handle
// 	// 2. format=nv12: CRITICAL - Ensures software frames match what the GPU expects
// 	// 3. hwupload: Moves those NV12 frames to GPU memory
// 	// 4. vpp_qsv: Performs the actual hardware scaling
// 	cmd := exec.Command("ffmpeg", "-y",
// 		"-init_hw_device", "qsv=hw",
// 		"-filter_hw_device", "hw",
// 		"-i", inputPath,
// 		"-vf", fmt.Sprintf("format=nv12,hwupload=extra_hw_frames=64,vpp_qsv=w=-1:h=%d", res.Height),
// 		"-c:v", "h264_qsv",
// 		"-preset", cfg.preset,
// 		"-b:v", res.Bitrate,
// 		"-c:a", "aac", "-b:a", "128k",
// 		"-hls_time", "6",
// 		"-hls_list_size", "0",
// 		"-hls_segment_filename", filepath.Join(outputDir, "seg%03d.ts"),
// 		"-f", "hls", filepath.Join(outputDir, "index.m3u8"),
// 	)

// 	var stderr bytes.Buffer
// 	cmd.Stderr = &stderr

// 	if err := cmd.Run(); err != nil {
// 		// Return the full FFmpeg stderr to help debug future permission or path issues
// 		return "", fmt.Errorf("%w - %s", err, stderr.String())
// 	}
// 	return out, nil
// }

type encodeConfig struct {
	preset  string
	threads int
}

// getEncodeConfig picks encoding settings based on current system load and time of day.
// Off-peak hours (22:00–06:00) or a normalised load below 0.3 -> heavy mode (better quality).
// Otherwise -> light mode (preserve CPU headroom for the HTTP server).
func getEncodeConfig() encodeConfig {
	cpus := runtime.NumCPU()
	normalizedLoad := getLoadAvg() / float64(cpus)

	offPeak := func() bool {
		h := time.Now().Hour()
		return h >= 22 || h < 6
	}()

	if offPeak || normalizedLoad < 0.3 {
		return encodeConfig{
			preset:  "medium", // "medium" is generally safe for QSV
			threads: max(2, cpus/MAX_PARALLEL),
		}
	}

	return encodeConfig{
		preset:  "veryfast",
		threads: max(1, cpus/(MAX_PARALLEL*2)),
	}
}

// getLoadAvg returns the 1-minute load average from /proc/loadavg (Linux).
// Returns 0 on error so the caller defaults to the heavier profile.
func getLoadAvg() float64 {
	data, err := os.ReadFile("/proc/loadavg")
	if err != nil {
		return 0
	}
	fields := strings.Fields(string(data))
	if len(fields) == 0 {
		return 0
	}
	load, _ := strconv.ParseFloat(fields[0], 64)
	return load
}
