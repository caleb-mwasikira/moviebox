package main

import (
	"bytes"
	"context"
	"fmt"
	"os/exec"
)

// remuxMkvToMp4 copies the video stream from an MKV into an MP4 container and
// re-encodes audio to AAC. Video is stream-copied (fast, lossless). Audio
// re-encoding is necessary because MKV files commonly carry AC3/DTS/TrueHD
// tracks that browsers cannot decode, which causes crackling during playback.
// -movflags +faststart moves the moov atom to the front for progressive playback.
func remuxMkvToMp4(ctx context.Context, inputPath, outputPath string) error {
	cmd := exec.CommandContext(ctx, "ffmpeg", "-y",
		"-i", inputPath,
		"-c:v", "copy",
		"-c:a", "aac",
		"-b:a", "192k",
		"-movflags", "+faststart",
		outputPath,
	)
	var stderr bytes.Buffer
	cmd.Stderr = &stderr
	if err := cmd.Run(); err != nil {
		return fmt.Errorf("ffmpeg remux failed: %w\n%s", err, stderr.String())
	}
	return nil
}
