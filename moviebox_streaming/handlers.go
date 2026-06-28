package main

import (
	"bytes"
	"context"
	"crypto/hmac"
	"crypto/sha256"
	"database/sql"
	"encoding/base64"
	"encoding/hex"
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"os"
	"os/exec"
	"path/filepath"
	"strconv"
	"strings"
	"sync"
	"time"

	"github.com/caleb-mwasikira/moviebox_streaming/api"
	"github.com/caleb-mwasikira/moviebox_streaming/encoders"
	"github.com/go-chi/chi/v5"
	"github.com/go-playground/validator/v10"
	"github.com/gorilla/websocket"
)

var (
	validate           = validator.New()
	defaultResolutions = []encoders.Resolution{encoders.Res360p, encoders.Res480p, encoders.Res720p}
)

type convertVideoRequest struct {
	VideoUUID   string `json:"video_uuid" validate:"required"`
	VideoTitle  string `json:"video_title" validate:"required"`
	SeriesTitle string `json:"series_title"`
	VideoPath   string `json:"video_path" validate:"required"`
	FilmType    string `json:"film_type" validate:"oneof=movies series movies_trailer series_trailer"`
	CallbackURL string `json:"callback_url" validate:"required"`
	Resolution  string `json:"resolution"` // optional: "360p", "720p", "1080p"; defaults to "720p"
}

func resolutionFromString(s string) (encoders.Resolution, bool) {
	switch s {
	case "360p":
		return encoders.Res360p, true
	case "480p":
		return encoders.Res480p, true
	case "720p":
		return encoders.Res720p, true
	case "1080p":
		return encoders.Res1080p, true
	}
	return encoders.Res720p, false
}

func convertVideo(w http.ResponseWriter, r *http.Request) {
	var req convertVideoRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		api.BadRequest(w, "Invalid JSON body", err)
		return
	}

	if err := validate.Struct(req); err != nil {
		api.BadRequest(w, "Invalid JSON body", err)
		return
	}

	if (req.FilmType == "series" || req.FilmType == "series_trailer") && strings.TrimSpace(req.SeriesTitle) == "" {
		api.BadRequest(w, "series_title is required for series film types", nil)
		return
	}

	key := req.FilmType + ":" + req.VideoUUID
	outputDir := buildOutputDir(req.FilmType, req.VideoTitle, req.SeriesTitle)

	ctx, cancel := context.WithCancel(context.Background())

	activeJobsMu.Lock()
	if _, running := activeJobs[key]; running {
		activeJobsMu.Unlock()
		cancel()
		api.Conflict(w, "Video already processing")
		return
	}
	activeJobs[key] = cancel
	activeJobDirs[key] = outputDir
	jobProgress[key] = 0
	activeJobsMu.Unlock()

	resolutions := defaultResolutions
	if req.Resolution != "" {
		if r, ok := resolutionFromString(req.Resolution); ok {
			resolutions = []encoders.Resolution{r}
		}
	}

	go func() {
		defer func() {
			cancelled := ctx.Err() != nil
			activeJobsMu.Lock()
			delete(activeJobs, key)
			delete(activeJobDirs, key)
			delete(jobProgress, key)
			activeJobsMu.Unlock()

			if cancelled {
				os.RemoveAll(outputDir)
				log.Printf("job [%s] cancelled — removed partial output: %s", key, outputDir)
				return
			}
		}()

		// Extract subtitle tracks from source before HLS encoding (best-effort).
		var subtitles []subtitleTrack
		if req.FilmType == "movies" || req.FilmType == "series" {
			subtitles = extractSubtitles(ctx, req.VideoPath, outputDir)
		}

		var lastStreamPath string
		var lastErr error

		for resIdx, res := range resolutions {
			if ctx.Err() != nil {
				return
			}

			n := len(resolutions)
			resBase := resIdx * 100 / n   // start of this resolution's share
			resEnd := (resIdx + 1) * 100 / n // end of this resolution's share

			cpuEnc := encoders.NewSplitEncoder(false)
			capturedBase, capturedEnd := resBase, resEnd
			cpuEnc.ProgressFn = func(splitPct int) {
				// split step covers the first 40% of this resolution's share
				shareWidth := capturedEnd - capturedBase
				overall := capturedBase + splitPct*shareWidth*40/100/100
				activeJobsMu.Lock()
				jobProgress[key] = overall
				activeJobsMu.Unlock()
			}

			streamPath, err := encoders.NewGPUEncoder().EncodeVideo(ctx, req.VideoPath, outputDir, res)
			if err != nil {
				if ctx.Err() != nil {
					return
				}
				log.Printf("GPU encoding failed for %s, falling back to CPU: %v\n", res.Name, err)
				streamPath, err = cpuEnc.EncodeVideo(ctx, req.VideoPath, outputDir, res)
			}

			if err != nil {
				if ctx.Err() != nil {
					return
				}
				lastErr = err
				log.Printf("Encoding failed for %s: %v\n", res.Name, err)
			} else {
				lastErr = nil
				lastStreamPath = formatStreamPath(HLSDir, streamPath)
				log.Printf("Encoding successful for %s: %s\n", res.Name, lastStreamPath)
				activeJobsMu.Lock()
				jobProgress[key] = resEnd
				activeJobsMu.Unlock()
			}
		}

		var message string
		if lastStreamPath != "" {
			message = "Video conversion successful"
		} else {
			message = "Video conversion failed"
			log.Printf("%v: %v\n", message, lastErr)
		}

		sendVideoConversionResponse(req.VideoUUID, req.FilmType, req.CallbackURL, convertVideoResult{
			Success:   lastStreamPath != "",
			ModelUUID: req.VideoUUID,
			FilmType:  req.FilmType,
			Path:      lastStreamPath,
			Message:   message,
			Subtitles: subtitles,
		})
	}()

	api.OK(w, "processing")
}

type subtitleTrack struct {
	Language     string `json:"language"`
	LanguageCode string `json:"language_code"`
	Path         string `json:"path"`
	IsDefault    bool   `json:"is_default"`
}

type convertVideoResult struct {
	Success   bool            `json:"success"`
	ModelUUID string          `json:"model_uuid"`
	FilmType  string          `json:"film_type"`
	Path      string          `json:"path"`
	Message   string          `json:"message"`
	Subtitles []subtitleTrack `json:"subtitles,omitempty"`
}

// sendVideoConversionResponse serialises the result, saves it to SQLite, then
// delivers it to callbackURL. If delivery fails the record stays pending for
// later replay via POST /retry-pending.
func sendVideoConversionResponse(videoUUID, filmType, callbackURL string, res convertVideoResult) {
	body, err := json.Marshal(res)
	if err != nil {
		log.Printf("sendVideoConversionResponse: marshal: %v", err)
		return
	}
	logAndDeliver(videoUUID, filmType, callbackURL, body)
}

// extractSubtitles probes inputPath for text-based subtitle tracks and extracts
// each one to WebVTT inside outputDir/subtitles/. Returns paths formatted for
// the /stream file server. Failures are logged and skipped (best-effort).
func extractSubtitles(ctx context.Context, inputPath, outputDir string) []subtitleTrack {
	type ffprobeStream struct {
		Index     int    `json:"index"`
		CodecName string `json:"codec_name"`
		CodecType string `json:"codec_type"`
		Tags      struct {
			Language string `json:"language"`
			Title    string `json:"title"`
		} `json:"tags"`
	}
	type ffprobeOutput struct {
		Streams []ffprobeStream `json:"streams"`
	}

	probeCmd := exec.CommandContext(ctx, "ffprobe",
		"-v", "quiet",
		"-print_format", "json",
		"-show_streams",
		"-select_streams", "s",
		inputPath,
	)
	var probeOut bytes.Buffer
	probeCmd.Stdout = &probeOut
	if err := probeCmd.Run(); err != nil {
		log.Printf("extractSubtitles: ffprobe failed: %v", err)
		return nil
	}

	var probe ffprobeOutput
	if err := json.Unmarshal(probeOut.Bytes(), &probe); err != nil || len(probe.Streams) == 0 {
		return nil
	}

	// Only attempt text-based codecs; skip bitmap subtitles (PGS, VOBSUB, etc.)
	textCodecs := map[string]bool{
		"subrip": true, "srt": true, "ass": true, "ssa": true,
		"webvtt": true, "mov_text": true, "text": true,
	}

	subsDir := filepath.Join(outputDir, "subtitles")
	if err := os.MkdirAll(subsDir, 0755); err != nil {
		log.Printf("extractSubtitles: mkdir failed: %v", err)
		return nil
	}

	seen := make(map[string]int)
	var tracks []subtitleTrack

	for i, stream := range probe.Streams {
		if !textCodecs[stream.CodecName] {
			continue
		}

		langCode := stream.Tags.Language
		if langCode == "" || langCode == "und" {
			langCode = fmt.Sprintf("track%d", i)
		}
		// Deduplicate identical language codes
		if seen[langCode] > 0 {
			langCode = fmt.Sprintf("%s%d", langCode, seen[langCode]+1)
		}
		seen[langCode]++

		outPath := filepath.Join(subsDir, langCode+".vtt")
		extractCmd := exec.CommandContext(ctx, "ffmpeg",
			"-y",
			"-i", inputPath,
			"-map", fmt.Sprintf("0:s:%d", i),
			"-f", "webvtt",
			outPath,
		)
		var extractErr bytes.Buffer
		extractCmd.Stderr = &extractErr
		if err := extractCmd.Run(); err != nil {
			log.Printf("extractSubtitles: track %d (%s) failed: %v\n%s", i, langCode, err, extractErr.String())
			continue
		}

		tracks = append(tracks, subtitleTrack{
			Language:     iso639Name(langCode),
			LanguageCode: langCode,
			Path:         formatStreamPath(HLSDir, outPath),
			IsDefault:    len(tracks) == 0,
		})
		log.Printf("extractSubtitles: extracted %s -> %s", langCode, outPath)
	}
	return tracks
}

// iso639Names maps common ISO 639-2/B codes to display names.
var iso639Names = map[string]string{
	"eng": "English", "fre": "French", "fra": "French",
	"spa": "Spanish", "ger": "German", "deu": "German",
	"ita": "Italian", "por": "Portuguese", "rus": "Russian",
	"jpn": "Japanese", "chi": "Chinese", "zho": "Chinese",
	"ara": "Arabic", "hin": "Hindi", "swa": "Swahili",
	"kor": "Korean", "dut": "Dutch", "nld": "Dutch",
	"pol": "Polish", "tur": "Turkish", "vie": "Vietnamese",
	"ind": "Indonesian", "tha": "Thai", "ukr": "Ukrainian",
	"swe": "Swedish", "nor": "Norwegian", "dan": "Danish",
	"fin": "Finnish", "heb": "Hebrew", "ces": "Czech",
	"ron": "Romanian", "hun": "Hungarian", "ell": "Greek",
}

func iso639Name(code string) string {
	if name, ok := iso639Names[code]; ok {
		return name
	}
	return strings.ToUpper(code)
}

// serveSubtitleFile handles GET /internal/subs?path=/stream/... (apiKeyAuth).
// Laravel calls this server-to-server in the video callback to download extracted
// VTT files and store them in public storage.
func serveSubtitleFile(w http.ResponseWriter, r *http.Request) {
	streamPath := r.URL.Query().Get("path")
	if !strings.HasSuffix(streamPath, ".vtt") {
		http.Error(w, `{"error":"only .vtt files"}`, http.StatusBadRequest)
		return
	}

	rel := strings.TrimPrefix(streamPath, "/stream")
	filePath := filepath.Join(HLSDir, filepath.Clean(rel))

	// Prevent path traversal
	absHLS, _ := filepath.Abs(HLSDir)
	absFile, _ := filepath.Abs(filePath)
	if !strings.HasPrefix(absFile, absHLS) {
		http.Error(w, `{"error":"forbidden"}`, http.StatusForbidden)
		return
	}

	w.Header().Set("Content-Type", "text/vtt; charset=utf-8")
	http.ServeFile(w, r, filePath)
}

// deliver POSTs body to callbackURL. A 5xx response is treated as delivery failure.
func deliver(callbackURL string, body []byte) error {
	if strings.TrimSpace(callbackURL) == "" {
		return fmt.Errorf("empty callback url")
	}
	resp, err := http.DefaultClient.Post(callbackURL, "application/json", bytes.NewReader(body))
	if err != nil {
		return err
	}
	defer resp.Body.Close()
	if resp.StatusCode >= 500 {
		return fmt.Errorf("callback returned %d", resp.StatusCode)
	}
	return nil
}

// logAndDeliver saves the payload to SQLite then attempts delivery.
// On success it marks the record delivered. On failure the record stays
// pending so it can be retried via POST /retry-pending.
func logAndDeliver(videoUUID, filmType, callbackURL string, body []byte) {
	id, err := saveConversion(videoUUID, filmType, callbackURL, string(body))
	if err != nil {
		log.Printf("logAndDeliver: sqlite save failed: %v", err)
	}

	if err := deliver(callbackURL, body); err != nil {
		log.Printf("logAndDeliver: callback failed for %s (record %d kept pending): %v", videoUUID, id, err)
		return
	}

	if id > 0 {
		markDelivered(id)
	}
}

// Maiah Manser - With A Smile

type jobStatus struct {
	FilmType  string `json:"film_type"`
	ModelUUID string `json:"model_uuid"`
	Progress  int    `json:"progress"`
}

func getStatus(w http.ResponseWriter, r *http.Request) {
	activeJobsMu.Lock()
	var jobs []jobStatus
	for key, pct := range jobProgress {
		parts := strings.SplitN(key, ":", 2)
		if len(parts) != 2 {
			continue
		}
		jobs = append(jobs, jobStatus{FilmType: parts[0], ModelUUID: parts[1], Progress: pct})
	}
	activeJobsMu.Unlock()

	if jobs == nil {
		jobs = []jobStatus{}
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]interface{}{"jobs": jobs})
}

type cancelJobRequest struct {
	VideoUUID string `json:"video_uuid" validate:"required"`
}

func cancelJob(w http.ResponseWriter, r *http.Request) {
	var req cancelJobRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		api.BadRequest(w, "Invalid JSON body", err)
		return
	}
	if err := validate.Struct(req); err != nil {
		api.BadRequest(w, "Invalid JSON body", err)
		return
	}

	suffix := ":" + req.VideoUUID
	activeJobsMu.Lock()
	var found int
	for key, cancel := range activeJobs {
		if strings.HasSuffix(key, suffix) {
			cancel()
			found++
		}
	}
	activeJobsMu.Unlock()

	if found == 0 {
		http.Error(w, `{"message":"No active jobs found for this UUID"}`, http.StatusNotFound)
		return
	}
	api.OK(w, fmt.Sprintf("Cancelled %d job(s)", found))
}

// getPendingConversions returns all conversion results that have not yet been
// successfully delivered to their Laravel callback URL.
func getPendingConversions(w http.ResponseWriter, r *http.Request) {
	records, err := pendingRecords()
	if err != nil {
		log.Printf("getPendingConversions: %v", err)
		http.Error(w, `{"error":"database error"}`, http.StatusInternalServerError)
		return
	}
	if records == nil {
		records = []conversionRecord{}
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]interface{}{"pending": records})
}

// retryPendingOnce attempts delivery for every pending record and returns
// (succeeded, failed) counts. Safe to call from both the HTTP handler and
// the background retry worker.
func retryPendingOnce() (succeeded, failed int) {
	records, err := pendingRecords()
	if err != nil {
		log.Printf("retryPendingOnce: pendingRecords: %v", err)
		return
	}
	for _, rec := range records {
		if err := deliver(rec.CallbackURL, []byte(rec.Body)); err != nil {
			log.Printf("retryPendingOnce: record %d (%s) still failing: %v", rec.ID, rec.VideoUUID, err)
			failed++
			continue
		}
		markDelivered(rec.ID)
		succeeded++
	}
	return
}

// retryPending re-fires the callback for every pending (undelivered) conversion
// record. Call this from Laravel after it recovers from downtime.
func retryPending(w http.ResponseWriter, r *http.Request) {
	records, err := pendingRecords()
	if err != nil {
		log.Printf("retryPending: %v", err)
		http.Error(w, `{"error":"database error"}`, http.StatusInternalServerError)
		return
	}

	succeeded, failed := retryPendingOnce()

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(map[string]interface{}{
		"total":     len(records),
		"delivered": succeeded,
		"failed":    failed,
	})
}

type convertMkvRequest struct {
	VideoUUID   string `json:"video_uuid"   validate:"required"`
	InputPath   string `json:"input_path"   validate:"required"`
	OutputPath  string `json:"output_path"  validate:"required"`
	CallbackURL string `json:"callback_url" validate:"required"`
}

func convertMkv(w http.ResponseWriter, r *http.Request) {
	var req convertMkvRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		api.BadRequest(w, "Invalid JSON body", err)
		return
	}
	if err := validate.Struct(req); err != nil {
		api.BadRequest(w, "Invalid JSON body", err)
		return
	}

	key := "mkv:" + req.VideoUUID
	ctx, cancel := context.WithCancel(context.Background())

	activeJobsMu.Lock()
	if _, running := activeJobs[key]; running {
		activeJobsMu.Unlock()
		cancel()
		api.Conflict(w, "MKV conversion already in progress")
		return
	}
	activeJobs[key] = cancel
	activeJobsMu.Unlock()

	go func() {
		defer func() {
			activeJobsMu.Lock()
			delete(activeJobs, key)
			activeJobsMu.Unlock()
		}()

		err := remuxMkvToMp4(ctx, req.InputPath, req.OutputPath)

		var message string
		if err != nil {
			message = fmt.Sprintf("MKV to MP4 conversion failed: %v", err)
			log.Printf("%v\n", message)
		} else {
			message = "MKV to MP4 conversion successful"
			log.Printf("%v: %v -> %v\n", message, req.InputPath, req.OutputPath)
			os.Remove(req.InputPath)
		}

		body, _ := json.Marshal(map[string]interface{}{
			"success":    err == nil,
			"video_uuid": req.VideoUUID,
			"message":    message,
		})
		logAndDeliver(req.VideoUUID, "mkv", req.CallbackURL, body)
	}()

	api.OK(w, "processing")
}

// apiKeyAuth protects server-to-server endpoints (e.g. /convert-video).
// The caller must send: X-Api-Key: <APP_SECRET>
func apiKeyAuth(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		if r.Header.Get("X-Api-Key") != streamingSecret {
			api.Unauthorized(w, "Unauthorized to view this resource")
			return
		}
		next.ServeHTTP(w, r)
	})
}

// streamAuth protects the /stream file server.
// The caller must send: Authorization: Bearer <token>
// where token = base64(uid:exp:hmac) and hmac = HMAC-SHA256(uid:exp, APP_SECRET).
func streamAuth(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		raw := r.Header.Get("Authorization")
		if !strings.HasPrefix(raw, "Bearer ") {
			api.Unauthorized(w, "Unauthorized to view this resource")
			return
		}

		decoded, err := base64.StdEncoding.DecodeString(strings.TrimPrefix(raw, "Bearer "))
		if err != nil {
			api.Unauthorized(w, "Unauthorized to view this resource")
			return
		}

		// token format: uid:exp:hmac_hex
		parts := strings.SplitN(string(decoded), ":", 3)
		if len(parts) != 3 {
			api.Unauthorized(w, "Unauthorized to view this resource")
			return
		}
		uid, expStr, sig := parts[0], parts[1], parts[2]

		exp, err := strconv.ParseInt(expStr, 10, 64)
		if err != nil || time.Now().Unix() > exp {
			api.Unauthorized(w, "Token expired")
			return
		}

		mac := hmac.New(sha256.New, []byte(streamingSecret))
		mac.Write([]byte(uid + ":" + expStr))
		if !hmac.Equal([]byte(sig), []byte(hex.EncodeToString(mac.Sum(nil)))) {
			api.Unauthorized(w, "Unauthorized to view this resource")
			return
		}

		next.ServeHTTP(w, r)
	})
}

func corsMiddleware(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Access-Control-Allow-Origin", "*")
		w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
		w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization, X-Api-Key")
		if r.Method == "OPTIONS" {
			w.WriteHeader(http.StatusNoContent)
			return
		}
		next.ServeHTTP(w, r)
	})
}

func fileServer(r chi.Router, path string, root http.FileSystem) {
	fs := http.StripPrefix(path, http.FileServer(root))
	r.Get(path+"/*", http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		fs.ServeHTTP(w, r)
	}))
}

// ── Watch Party WebSocket ─────────────────────────────────────────────────────

var (
	pgDB   *sql.DB // Postgres connection used for party membership checks
	appURL string
)

// initPG opens the Postgres connection using the DB_* env vars.
func initPG() error {
	appURL = strings.TrimRight(os.Getenv("APP_URL"), "/")
	h := os.Getenv("DB_HOST")
	p := os.Getenv("DB_PORT")
	db := os.Getenv("DB_DATABASE")
	u := os.Getenv("DB_USERNAME")
	pw := os.Getenv("DB_PASSWORD")

	if h == "" || db == "" || u == "" {
		return fmt.Errorf("DB_HOST, DB_DATABASE, DB_USERNAME env vars are required")
	}
	if p == "" {
		p = "5432"
	}

	dsn := fmt.Sprintf("host=%s port=%s dbname=%s user=%s password=%s sslmode=disable", h, p, db, u, pw)
	var err error
	pgDB, err = sql.Open("pgx", dsn)
	if err != nil {
		return err
	}
	pgDB.SetMaxOpenConns(5)
	pgDB.SetMaxIdleConns(2)
	return pgDB.Ping()
}

// wpMember is the presence identity broadcast to all clients in a party room.
type wpMember struct {
	ID     int64  `json:"id"`
	Name   string `json:"name"`
	IsHost bool   `json:"is_host"`
}

// wpClient represents one active WebSocket connection.
type wpClient struct {
	conn    *websocket.Conn
	send    chan []byte
	token   string // party token
	partyID int64
	member  wpMember
}

// wpClientMsg is the JSON envelope clients send over the WebSocket.
type wpClientMsg struct {
	Type      string  `json:"type"`
	Message   string  `json:"message"`
	Position  float64 `json:"position"`
	IsPlaying bool    `json:"is_playing"`
	EpisodeID int64   `json:"episode_id"`
}

// episodeRow holds the episode columns needed for WatchPartyEpisodeChanged events.
type episodeRow struct {
	ID            int64
	EpisodeNumber int
	Title         string
	VideoPath     string
	StreamingPath string
}

// wpHub tracks every connected client, grouped by party token.
type wpHub struct {
	mu      sync.RWMutex
	parties map[string]map[*wpClient]bool
}

var hub = &wpHub{parties: make(map[string]map[*wpClient]bool)}

// wpMessage is the JSON envelope delivered to WebSocket clients.
type wpMessage struct {
	Event string          `json:"event"`
	Data  json.RawMessage `json:"data"`
}

// wpPushRequest is the body Laravel POSTs to /watch-party/{token}/broadcast.
type wpPushRequest struct {
	Event         string          `json:"event"`
	Data          json.RawMessage `json:"data"`
	ExcludeUserID int64           `json:"exclude_user_id,omitempty"`
}

func marshalMsg(event string, data any) []byte {
	d, _ := json.Marshal(data)
	msg, _ := json.Marshal(wpMessage{Event: event, Data: d})
	return msg
}

// join adds c to the hub, sends presence.here to c, and presence.joining to everyone else.
func (h *wpHub) join(c *wpClient) {
	h.mu.Lock()
	if h.parties[c.token] == nil {
		h.parties[c.token] = make(map[*wpClient]bool)
	}
	h.parties[c.token][c] = true
	members := make([]wpMember, 0, len(h.parties[c.token]))
	for cl := range h.parties[c.token] {
		members = append(members, cl.member)
	}
	h.mu.Unlock()

	select {
	case c.send <- marshalMsg("presence.here", members):
	default:
	}
	h.broadcastTo(c.token, marshalMsg("presence.joining", c.member), c)
}

// leave removes c from the hub and broadcasts presence.leaving to remaining clients.
func (h *wpHub) leave(c *wpClient) {
	h.mu.Lock()
	if clients, ok := h.parties[c.token]; ok {
		delete(clients, c)
		if len(clients) == 0 {
			delete(h.parties, c.token)
		}
	}
	h.mu.Unlock()
	h.broadcastTo(c.token, marshalMsg("presence.leaving", map[string]int64{"id": c.member.ID}), nil)
}

// broadcastTo sends msg to every client in the party, skipping exclude (nil = send to all).
func (h *wpHub) broadcastTo(token string, msg []byte, exclude *wpClient) {
	h.mu.RLock()
	clients := make([]*wpClient, 0, len(h.parties[token]))
	for c := range h.parties[token] {
		if c != exclude {
			clients = append(clients, c)
		}
	}
	h.mu.RUnlock()
	for _, c := range clients {
		select {
		case c.send <- msg:
		default: // slow client — drop
		}
	}
}

// broadcastExcludingUser sends msg to every client except the one with the given user ID.
func (h *wpHub) broadcastExcludingUser(token string, msg []byte, excludeUserID int64) {
	h.mu.RLock()
	clients := make([]*wpClient, 0, len(h.parties[token]))
	for c := range h.parties[token] {
		if c.member.ID != excludeUserID {
			clients = append(clients, c)
		}
	}
	h.mu.RUnlock()
	for _, c := range clients {
		select {
		case c.send <- msg:
		default:
		}
	}
}

const (
	wsWriteWait  = 10 * time.Second
	wsPongWait   = 60 * time.Second
	wsPingPeriod = (wsPongWait * 9) / 10
)

var wsUpgrader = websocket.Upgrader{
	ReadBufferSize:  1024,
	WriteBufferSize: 4096,
	CheckOrigin:     func(r *http.Request) bool { return true },
}

func (c *wpClient) writePump() {
	ticker := time.NewTicker(wsPingPeriod)
	defer func() {
		ticker.Stop()
		c.conn.Close()
	}()
	for {
		select {
		case msg, ok := <-c.send:
			c.conn.SetWriteDeadline(time.Now().Add(wsWriteWait))
			if !ok {
				c.conn.WriteMessage(websocket.CloseMessage, []byte{})
				return
			}
			if err := c.conn.WriteMessage(websocket.TextMessage, msg); err != nil {
				return
			}
		case <-ticker.C:
			c.conn.SetWriteDeadline(time.Now().Add(wsWriteWait))
			if err := c.conn.WriteMessage(websocket.PingMessage, nil); err != nil {
				return
			}
		}
	}
}

func (c *wpClient) readPump() {
	defer func() {
		hub.leave(c)
		close(c.send)
		c.conn.Close()
	}()
	c.conn.SetReadLimit(4096)
	c.conn.SetReadDeadline(time.Now().Add(wsPongWait))
	c.conn.SetPongHandler(func(string) error {
		c.conn.SetReadDeadline(time.Now().Add(wsPongWait))
		return nil
	})
	for {
		_, raw, err := c.conn.ReadMessage()
		if err != nil {
			break
		}
		var m wpClientMsg
		if json.Unmarshal(raw, &m) != nil {
			continue
		}
		switch m.Type {
		case "chat":
			c.handleChat(m.Message)
		case "sync":
			c.handleSync(m.Position, m.IsPlaying)
		case "episode.next":
			c.handleEpisodeNext()
		case "episode.jump":
			c.handleEpisodeJump(m.EpisodeID)
		case "get_host_position":
			c.handleGetHostPosition()
		}
	}
}

// watchPartyWS handles GET /ws/watch-party/{token}?auth=<hlsToken>
// Validates the bearer token, checks party membership via Postgres, then
// upgrades to WebSocket and pumps messages until the connection closes.
func watchPartyWS(w http.ResponseWriter, r *http.Request) {
	partyToken := chi.URLParam(r, "token")

	userID, err := parseStreamToken(r.URL.Query().Get("auth"))
	if err != nil {
		http.Error(w, `{"error":"unauthorized"}`, http.StatusUnauthorized)
		return
	}

	member, partyID, err := fetchPartyMember(r.Context(), partyToken, userID)
	if err != nil {
		log.Printf("watchPartyWS: membership check failed (party=%s user=%d): %v", partyToken, userID, err)
		http.Error(w, `{"error":"forbidden"}`, http.StatusForbidden)
		return
	}

	conn, err := wsUpgrader.Upgrade(w, r, nil)
	if err != nil {
		log.Printf("watchPartyWS: upgrade: %v", err)
		return
	}

	c := &wpClient{
		conn:    conn,
		send:    make(chan []byte, 256),
		token:   partyToken,
		partyID: partyID,
		member:  *member,
	}
	hub.join(c)
	go c.writePump()
	c.readPump() // blocks until the connection closes
}

// pushWatchPartyEvent handles POST /watch-party/{token}/broadcast  (apiKeyAuth)
// Laravel calls this endpoint to push any named event to all connected clients
// in a party. Optionally pass exclude_user_id to skip one client (e.g. the sender).
func pushWatchPartyEvent(w http.ResponseWriter, r *http.Request) {
	token := chi.URLParam(r, "token")

	var push wpPushRequest
	if err := json.NewDecoder(r.Body).Decode(&push); err != nil {
		api.BadRequest(w, "Invalid JSON body", err)
		return
	}

	msg, err := json.Marshal(wpMessage{Event: push.Event, Data: push.Data})
	if err != nil {
		api.BadRequest(w, "Marshal error", err)
		return
	}

	if push.ExcludeUserID != 0 {
		hub.broadcastExcludingUser(token, msg, push.ExcludeUserID)
	} else {
		hub.broadcastTo(token, msg, nil)
	}

	api.OK(w, "broadcasted")
}

// parseStreamToken validates the HLS bearer token and returns the user ID.
// Token format: base64(uid:exp:hmac_hex) — same as the streamAuth middleware.
func parseStreamToken(raw string) (int64, error) {
	decoded, err := base64.StdEncoding.DecodeString(raw)
	if err != nil {
		return 0, fmt.Errorf("invalid token encoding")
	}
	parts := strings.SplitN(string(decoded), ":", 3)
	if len(parts) != 3 {
		return 0, fmt.Errorf("invalid token format")
	}
	uid, expStr, sig := parts[0], parts[1], parts[2]

	exp, err := strconv.ParseInt(expStr, 10, 64)
	if err != nil || time.Now().Unix() > exp {
		return 0, fmt.Errorf("token expired")
	}

	mac := hmac.New(sha256.New, []byte(streamingSecret))
	mac.Write([]byte(uid + ":" + expStr))
	if !hmac.Equal([]byte(sig), []byte(hex.EncodeToString(mac.Sum(nil)))) {
		return 0, fmt.Errorf("invalid token signature")
	}
	return strconv.ParseInt(uid, 10, 64)
}

// fetchPartyMember queries Postgres to confirm the user is an active (non-kicked)
// member of the party and returns their identity for presence tracking.
func fetchPartyMember(ctx context.Context, partyToken string, userID int64) (*wpMember, int64, error) {
	if pgDB == nil {
		return nil, 0, fmt.Errorf("postgres not available")
	}
	var m wpMember
	var hostID, partyID int64
	err := pgDB.QueryRowContext(ctx, `
		SELECT u.id, u.name, wp.host_id, wp.id
		FROM watch_party_members wpm
		JOIN users u ON u.id = wpm.user_id
		JOIN watch_parties wp ON wp.id = wpm.watch_party_id
		WHERE wp.token = $1
		  AND wpm.user_id = $2
		  AND wpm.kicked_at IS NULL
		  AND wp.status != 'ended'
	`, partyToken, userID).Scan(&m.ID, &m.Name, &hostID, &partyID)
	if err != nil {
		return nil, 0, err
	}
	m.IsHost = m.ID == hostID
	return &m, partyID, nil
}

// ── Client message handlers ───────────────────────────────────────────────────

func (c *wpClient) handleChat(message string) {
	message = strings.TrimSpace(message)
	if message == "" || len(message) > 500 || pgDB == nil {
		return
	}
	var msgID int64
	var createdAt time.Time
	err := pgDB.QueryRowContext(context.Background(), `
		INSERT INTO watch_party_messages (watch_party_id, user_id, message, is_system, created_at, updated_at)
		VALUES ($1, $2, $3, false, NOW(), NOW())
		RETURNING id, created_at
	`, c.partyID, c.member.ID, message).Scan(&msgID, &createdAt)
	if err != nil {
		log.Printf("handleChat: insert failed: %v", err)
		return
	}
	hub.broadcastTo(c.token, marshalMsg("WatchPartyChatMessage", map[string]any{
		"id":        msgID,
		"user_id":   c.member.ID,
		"name":      c.member.Name,
		"message":   message,
		"is_host":   c.member.IsHost,
		"time":      createdAt.Format("15:04"),
		"is_system": false,
	}), nil)
}

func (c *wpClient) handleSync(position float64, isPlaying bool) {
	if !c.member.IsHost || pgDB == nil {
		return
	}
	_, err := pgDB.ExecContext(context.Background(), `
		UPDATE watch_parties
		SET playback_position = $1, is_playing = $2, playback_updated_at = NOW(), status = 'live'
		WHERE id = $3
	`, position, isPlaying, c.partyID)
	if err != nil {
		log.Printf("handleSync: update failed: %v", err)
		return
	}
	hub.broadcastExcludingUser(c.token, marshalMsg("WatchPartySync", map[string]any{
		"position":   position,
		"is_playing": isPlaying,
	}), c.member.ID)
}

// handleGetHostPosition responds to a member's position-check request.
// It queries the DB for the host's last recorded position, extrapolates
// forward by elapsed time if the video is playing, and sends the result
// only to the requesting client.
func (c *wpClient) handleGetHostPosition() {
	if c.member.IsHost || pgDB == nil {
		return
	}
	var position float64
	var isPlaying bool
	var updatedAt time.Time
	err := pgDB.QueryRowContext(context.Background(), `
		SELECT playback_position, is_playing, playback_updated_at
		FROM watch_parties WHERE id = $1
	`, c.partyID).Scan(&position, &isPlaying, &updatedAt)
	if err != nil {
		log.Printf("handleGetHostPosition: query failed (party=%d): %v", c.partyID, err)
		return
	}
	if isPlaying {
		position += time.Since(updatedAt).Seconds()
	}
	select {
	case c.send <- marshalMsg("host_position", map[string]any{
		"position":   position,
		"is_playing": isPlaying,
	}):
	default:
	}
}

func (c *wpClient) handleEpisodeNext() {
	if !c.member.IsHost || pgDB == nil {
		return
	}
	var ep episodeRow
	err := pgDB.QueryRowContext(context.Background(), `
		WITH curr AS (
			SELECT wpe.sort_order
			FROM watch_parties wp
			JOIN watch_party_episodes wpe ON wpe.watch_party_id = wp.id
			                              AND wpe.episode_id = wp.current_episode_id
			WHERE wp.id = $1
		)
		SELECT e.id, e.episode_number, e.title,
		       COALESCE(e.video_path, '') AS video_path,
		       COALESCE(e.streaming_path, '') AS streaming_path
		FROM watch_party_episodes wpe
		JOIN episodes e ON e.id = wpe.episode_id
		WHERE wpe.watch_party_id = $1
		  AND wpe.sort_order > (SELECT sort_order FROM curr)
		ORDER BY wpe.sort_order ASC
		LIMIT 1
	`, c.partyID).Scan(&ep.ID, &ep.EpisodeNumber, &ep.Title, &ep.VideoPath, &ep.StreamingPath)
	if err != nil {
		log.Printf("handleEpisodeNext: no next episode (party=%d): %v", c.partyID, err)
		return
	}
	c.broadcastEpisodeChange(ep)
}

func (c *wpClient) handleEpisodeJump(episodeID int64) {
	if !c.member.IsHost || episodeID == 0 || pgDB == nil {
		return
	}
	var ep episodeRow
	err := pgDB.QueryRowContext(context.Background(), `
		SELECT e.id, e.episode_number, e.title,
		       COALESCE(e.video_path, '') AS video_path,
		       COALESCE(e.streaming_path, '') AS streaming_path
		FROM watch_party_episodes wpe
		JOIN episodes e ON e.id = wpe.episode_id
		WHERE wpe.watch_party_id = $1 AND wpe.episode_id = $2
	`, c.partyID, episodeID).Scan(&ep.ID, &ep.EpisodeNumber, &ep.Title, &ep.VideoPath, &ep.StreamingPath)
	if err != nil {
		log.Printf("handleEpisodeJump: episode %d not in playlist (party=%d): %v", episodeID, c.partyID, err)
		return
	}
	c.broadcastEpisodeChange(ep)
}

func (c *wpClient) broadcastEpisodeChange(ep episodeRow) {
	_, err := pgDB.ExecContext(context.Background(), `
		UPDATE watch_parties
		SET current_episode_id = $1, playback_position = 0, is_playing = false, playback_updated_at = NOW()
		WHERE id = $2
	`, ep.ID, c.partyID)
	if err != nil {
		log.Printf("broadcastEpisodeChange: update failed: %v", err)
		return
	}
	src, useHls := buildEpisodeSrc(ep.VideoPath, ep.StreamingPath)
	hub.broadcastTo(c.token, marshalMsg("WatchPartyEpisodeChanged", map[string]any{
		"current_episode_id": ep.ID,
		"episode": map[string]any{
			"id":      ep.ID,
			"label":   fmt.Sprintf("E%02d — %s", ep.EpisodeNumber, ep.Title),
			"src":     src,
			"use_hls": useHls,
		},
	}), nil)
}

// buildEpisodeSrc mirrors Episode::streamingUrl() / Episode::videoUrl() from Laravel.
func buildEpisodeSrc(videoPath, streamingPath string) (src string, useHls bool) {
	if streamingPath != "" {
		base := strings.TrimRight(os.Getenv("STREAMING_SERVICE_URL"), "/")
		return base + "/" + strings.TrimLeft(streamingPath, "/"), true
	}
	return appURL + "/storage/" + strings.TrimLeft(videoPath, "/"), false
}
