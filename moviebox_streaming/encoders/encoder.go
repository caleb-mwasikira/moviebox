package encoders

import (
	"context"
	"encoding/json"
	"os/exec"
	"strconv"
)

type HLSEncoder interface {
	EncodeVideo(ctx context.Context, inputPath, outputDir string, res Resolution) (string, error)
}

type Resolution struct {
	Name      string
	Height    int
	Bitrate   string
	Bandwidth int
}

var (
	Res360p  = Resolution{"360p", 360, "800k", 800_000}
	Res480p  = Resolution{"480p", 480, "1200k", 1_200_000}
	Res720p  = Resolution{"720p", 720, "2800k", 2_800_000}
	Res1080p = Resolution{"1080p", 1080, "5000k", 5_000_000}
)

func getVideoDuration(videoPath string) (float64, error) {
	cmd := exec.Command("ffprobe",
		"-v", "quiet",
		"-print_format", "json",
		"-show_format",
		videoPath,
	)
	output, err := cmd.Output()
	if err != nil {
		return 0, err
	}
	var result struct {
		Format struct {
			Duration string `json:"duration"`
		} `json:"format"`
	}
	if err := json.Unmarshal(output, &result); err != nil {
		return 0, err
	}
	return strconv.ParseFloat(result.Format.Duration, 64)
}
