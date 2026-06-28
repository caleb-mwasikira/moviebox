package main

import (
	"bytes"
	"context"
	"fmt"
	"os/exec"
)

// remuxMkvToMp4 transcodes an MKV file to a browser-compatible H.264/AAC MP4.
// Full video re-encode is necessary because MKV files often carry HEVC/VP9/10-bit
// video that browsers cannot decode. yuv420p ensures 8-bit output compatible with
// all browsers. -movflags +faststart moves the moov atom to the front for
// progressive playback.
func remuxMkvToMp4(ctx context.Context, inputPath, outputPath string) error {
	cmd := exec.CommandContext(ctx, "ffmpeg", "-y",
		"-i", inputPath,
		"-c:v", "libx264",
		"-preset", "fast",
		"-pix_fmt", "yuv420p",
		"-c:a", "aac",
		"-b:a", "192k",
		"-movflags", "+faststart",
		outputPath,
	)
	var stderr bytes.Buffer
	cmd.Stderr = &stderr
	if err := cmd.Run(); err != nil {
		return fmt.Errorf("ffmpeg transcode failed: %w\n%s", err, stderr.String())
	}
	return nil
}
