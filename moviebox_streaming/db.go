package main

import (
	"database/sql"
	"log"
	"path/filepath"
	"time"

	_ "modernc.org/sqlite"
)

var db *sql.DB

func initDB() error {
	dbPath := filepath.Join(HLSDir, "conversions.db")
	var err error
	db, err = sql.Open("sqlite", dbPath)
	if err != nil {
		return err
	}
	db.SetMaxOpenConns(1)

	_, err = db.Exec(`
		CREATE TABLE IF NOT EXISTS conversion_log (
			id           INTEGER PRIMARY KEY AUTOINCREMENT,
			video_uuid   TEXT    NOT NULL,
			film_type    TEXT    NOT NULL,
			callback_url TEXT    NOT NULL,
			body         TEXT    NOT NULL,
			delivered    INTEGER NOT NULL DEFAULT 0,
			created_at   TEXT    NOT NULL
		);
		CREATE INDEX IF NOT EXISTS idx_pending ON conversion_log (delivered)
			WHERE delivered = 0;
	`)
	return err
}

type conversionRecord struct {
	ID          int64  `json:"id"`
	VideoUUID   string `json:"video_uuid"`
	FilmType    string `json:"film_type"`
	CallbackURL string `json:"callback_url"`
	Body        string `json:"body"`
	CreatedAt   string `json:"created_at"`
}

func saveConversion(videoUUID, filmType, callbackURL, body string) (int64, error) {
	res, err := db.Exec(
		`INSERT INTO conversion_log (video_uuid, film_type, callback_url, body, delivered, created_at)
		 VALUES (?, ?, ?, ?, 0, ?)`,
		videoUUID, filmType, callbackURL, body, time.Now().UTC().Format(time.RFC3339),
	)
	if err != nil {
		return 0, err
	}
	return res.LastInsertId()
}

func markDelivered(id int64) {
	if _, err := db.Exec(`UPDATE conversion_log SET delivered = 1 WHERE id = ?`, id); err != nil {
		log.Printf("markDelivered(%d): %v", id, err)
	}
}

func pendingRecords() ([]conversionRecord, error) {
	rows, err := db.Query(
		`SELECT id, video_uuid, film_type, callback_url, body, created_at
		 FROM conversion_log WHERE delivered = 0 ORDER BY created_at ASC`,
	)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var out []conversionRecord
	for rows.Next() {
		var r conversionRecord
		if err := rows.Scan(&r.ID, &r.VideoUUID, &r.FilmType, &r.CallbackURL, &r.Body, &r.CreatedAt); err != nil {
			log.Printf("pendingRecords scan: %v", err)
			continue
		}
		out = append(out, r)
	}
	return out, rows.Err()
}
