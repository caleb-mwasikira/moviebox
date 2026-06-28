package main

import (
	"context"
	"flag"
	"log"
	"mime"
	"net"
	"net/http"
	"os"
	"path/filepath"
	"sync"
	"time"

	"github.com/go-chi/chi/v5"
	"github.com/go-chi/chi/v5/middleware"
	_ "github.com/jackc/pgx/v5/stdlib"
	"github.com/joho/godotenv"
)

var (
	host, port      string
	streamingSecret string
	HLSDir          = filepath.Join("./storage")

	activeJobs    = make(map[string]context.CancelFunc) // key -> cancel func
	activeJobDirs = make(map[string]string)             // key -> output dir for cleanup
	jobProgress   = make(map[string]int)
	activeJobsMu  sync.Mutex
)

func init() {
	flag.StringVar(&host, "host", "127.0.0.1", "Host address to run streaming service")
	flag.StringVar(&port, "port", "9090", "Port number to run streaming service")
	flag.Parse()

	if err := godotenv.Load(); err != nil {
		log.Fatalf("Error loading environment variables: %v\n", err)
	}

	streamingSecret = os.Getenv("APP_SECRET")
	if streamingSecret == "" {
		log.Fatal("APP_SECRET env var is required")
	}

	if err := validateAddress(host, port); err != nil {
		log.Fatalf("Invalid server address: %v\n", err)
	}
}

func main() {
	mime.AddExtensionType(".m3u8", "application/vnd.apple.mpegurl")
	mime.AddExtensionType(".ts", "video/mp2t")
	mime.AddExtensionType(".vtt", "text/vtt")

	os.MkdirAll(HLSDir, 0755)

	if err := initDB(); err != nil {
		log.Fatalf("Failed to initialise SQLite: %v\n", err)
	}

	if err := initPG(); err != nil {
		log.Printf("Postgres unavailable (watch party WebSocket auth disabled): %v\n", err)
	}

	r := chi.NewRouter()
	r.Use(middleware.Logger)
	r.Use(middleware.Recoverer)
	r.Use(corsMiddleware)

	r.With(apiKeyAuth).Post("/convert-video", convertVideo)
	r.With(apiKeyAuth).Post("/convert-mkv", convertMkv)
	r.With(apiKeyAuth).Post("/cancel-job", cancelJob)
	r.With(apiKeyAuth).Get("/pending-conversions", getPendingConversions)
	r.With(apiKeyAuth).Post("/retry-pending", retryPending)
	r.With(apiKeyAuth).Get("/internal/subs", serveSubtitleFile)
	r.Get("/status", getStatus)
	r.Get("/ws/watch-party/{token}", watchPartyWS)
	r.With(apiKeyAuth).Post("/watch-party/{token}/broadcast", pushWatchPartyEvent)

	r.Group(func(r chi.Router) {
		r.Use(streamAuth)
		fileServer(r, "/stream", http.Dir(HLSDir))
	})

	// Background worker: retry undelivered callbacks every 30 seconds.
	go func() {
		ticker := time.NewTicker(30 * time.Second)
		defer ticker.Stop()
		for range ticker.C {
			ok, failed := retryPendingOnce()
			if ok+failed > 0 {
				log.Printf("retryWorker: delivered=%d still_pending=%d", ok, failed)
			}
		}
	}()

	addr := net.JoinHostPort(host, port)
	log.Printf("Video streaming service started on %v\n", addr)

	if err := http.ListenAndServe(addr, r); err != nil {
		log.Fatalf("Error starting video service: %v\n", err)
	}
}
