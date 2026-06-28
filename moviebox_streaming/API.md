# Streaming service API

Base URL: `http://<host>:8080` (default in code is `localhost:8080`).

CORS is enabled for all origins (`Access-Control-Allow-Origin: *`). Preflight: `OPTIONS` is accepted for the listed methods; the middleware sets CORS headers on every response.

---

## `GET /`

**Purpose:** Serve the embedded upload UI (`static/index.html`).

| Aspect | Value |
|--------|--------|
| **Request** | No body. No query parameters. |
| **Success** | `200 OK`, `Content-Type: text/html` |
| **Response body** | HTML (embedded at build time). |

---

## `GET /player`

**Purpose:** Serve the embedded video player page (`static/player.html`).

| Aspect | Value |
|--------|--------|
| **Request** | No body. No query parameters. |
| **Success** | `200 OK`, `Content-Type: text/html` |
| **Response body** | HTML. |

---

## `POST /upload`

**Purpose:** Accept a single video file, save it under `./uploads`, then (asynchronously) transcode to HLS under `./storage/<uuid>/` and record metadata in SQLite when the first rendition completes.

| Aspect | Value |
|--------|--------|
| **Content-Type** | `multipart/form-data` |
| **Form field** | `video` — one file (required) |
| **Max body size (parsed)** | 100 MB (`ParseMultipartForm(100 << 20)`) |

**Behavior**

- `video_title` stored in the DB is the **uploaded file’s original filename** (`header.Filename`), not a separate form field.
- The handler responds **before** transcoding finishes; encoding runs in a goroutine.

**Success**

| Field | Value |
|-------|--------|
| **Status** | `202 Accepted` |
| **Body** | Plain text, UTF-8: `Upload started for <original filename>` |

**Errors**

| Condition | Status | Body (plain text) |
|-----------|--------|-------------------|
| Missing or invalid `video` file field | `400 Bad Request` | `Invalid file` |
| Failed to create file on disk | `500 Internal Server Error` | `Error saving uploaded video file` |

**Note:** If FFmpeg fails, that is only logged; the client is not notified over HTTP. Clients can use `GET /ws` for completion events.

---

## `GET /stream/…` (HLS static files)

**Purpose:** Serve generated HLS output from `./storage` under the URL prefix `/stream`.

| Aspect | Value |
|--------|--------|
| **Method** | `GET` (and any method the file server handles; typical use is `GET`) |
| **Path** | `/stream/<video_id>/...` — e.g. `master.m3u8`, `360p/index.m3u8`, `360p/seg001.ts` |

**Documented in code comment**

- Master playlist: `/stream/<video_id>/master.m3u8`

Rendition folders: `360p`, `720p` (see `resolutions` in `main.go`).

**Success:** `200` with appropriate `Content-Type` for `.m3u8` / `.ts` (from Go’s `FileServer`).

**Errors:** `404` if the file does not exist on disk.

---

## Summary table

| Method | Path | Response type |
|--------|------|----------------|
| `GET` | `/` | HTML |
| `GET` | `/player` | HTML |
| `POST` | `/upload` | Plain text (`202` on success) |
| `GET` | `/stream/*` | HLS files |
