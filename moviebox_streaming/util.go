package main

import (
	"log"
	"path/filepath"
	"strings"
)

// buildOutputDir returns the base output directory for the given film type and titles.
// Encoders receive this directory and create resolution subdirs (720p/, 1080p/) inside it.
//
//	movies          → storage/movies/{slug(videoTitle)}
//	movies_trailer  → storage/movies/{slug(videoTitle)}/trailers
//	series          → storage/series/{slug(seriesTitle)}/{slug(videoTitle)}
//	series_trailer  → storage/series/{slug(seriesTitle)}/trailers
func buildOutputDir(filmType, videoTitle, seriesTitle string) string {
	switch filmType {
	case "movies":
		return filepath.Join(HLSDir, "movies", slugify(videoTitle))
	case "movies_trailer":
		return filepath.Join(HLSDir, "movies", slugify(videoTitle), "trailers")
	case "series":
		return filepath.Join(HLSDir, "series", slugify(seriesTitle), slugify(videoTitle))
	case "series_trailer":
		return filepath.Join(HLSDir, "series", slugify(seriesTitle), "trailers")
	default:
		return filepath.Join(HLSDir, slugify(videoTitle))
	}
}

// slugify converts a title into a lowercase, hyphen-separated filesystem-safe slug.
func slugify(s string) string {
	s = strings.ToLower(s)
	var b strings.Builder
	for _, r := range s {
		if (r >= 'a' && r <= 'z') || (r >= '0' && r <= '9') {
			b.WriteRune(r)
		} else {
			b.WriteByte('-')
		}
	}
	slug := b.String()
	for strings.Contains(slug, "--") {
		slug = strings.ReplaceAll(slug, "--", "-")
	}
	slug = strings.Trim(slug, "-")
	if len(slug) > 80 {
		slug = slug[:80]
	}
	return slug
}

// formatStreamPath returns a web-accessible path relative to the /stream endpoint.
// e.g. "./storage/movies/slug/master.m3u8" -> "/stream/movies/slug/master.m3u8"
func formatStreamPath(baseDir, fullPath string) string {
	rel, err := filepath.Rel(baseDir, fullPath)
	if err != nil {
		log.Printf("Path transformation error: %v", err)
		return fullPath
	}
	return "/" + filepath.ToSlash(filepath.Join("stream", rel))
}
