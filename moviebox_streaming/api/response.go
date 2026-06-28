package api

import (
	"encoding/json"
	"log"
	"net/http"
)

func BadRequest(w http.ResponseWriter, message string, err error) {
	if err != nil {
		log.Printf("%v: %v\n", message, err)
	}
	w.WriteHeader(http.StatusBadRequest)
	w.Write([]byte(message))
}

func Conflict(w http.ResponseWriter, message string) {
	errBody := map[string]string{
		"error": message,
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusConflict)
	json.NewEncoder(w).Encode(errBody)
}

func OK(w http.ResponseWriter, message string) {
	body := map[string]string{
		"status": message,
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusAccepted)
	json.NewEncoder(w).Encode(body)
}

func Unauthorized(w http.ResponseWriter, message string) {
	w.WriteHeader(http.StatusUnauthorized)
	w.Write([]byte(message))
}
