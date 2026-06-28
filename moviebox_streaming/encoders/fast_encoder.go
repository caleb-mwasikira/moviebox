package encoders

import (
	"bytes"
	"context"
	"fmt"
	"log"
	"os"
	"os/exec"
	"path/filepath"
)

var _ HLSEncoder = (*FastEncoder)(nil)

type FastEncoder struct{}

func NewFastEncoder() *FastEncoder {
	return &FastEncoder{}
}

func (e *FastEncoder) EncodeVideo(ctx context.Context, inputPath, outputDir string, res Resolution) (string, error) {
	resDir := filepath.Join(outputDir, res.Name)
	if err := os.MkdirAll(resDir, 0755); err != nil {
		return "", fmt.Errorf("error creating dir: %v", err)
	}

	log.Printf("fast-encoder: %v %v\n", res.Name, inputPath)

	out := filepath.Join(resDir, "index.m3u8")
	cmd := exec.CommandContext(ctx, "ffmpeg", "-y",
		"-i", inputPath,
		"-c:v", "copy",
		"-c:a", "aac",
		"-b:a", "128k",
		"-hls_time", "6",
		"-hls_list_size", "0",
		"-hls_segment_filename", filepath.Join(resDir, "seg%03d.ts"),
		"-f", "hls", out,
	)

	var stderr bytes.Buffer
	cmd.Stderr = &stderr
	if err := cmd.Run(); err != nil {
		return "", fmt.Errorf("%w - %s", err, stderr.String())
	}

	masterPath, err := createMasterPlaylist(outputDir, []Resolution{res})
	if err != nil {
		return out, nil
	}
	return masterPath, nil
}
