// Resumable video uploads. Large files go to /admin/uploads in small pieces; if the connection
// drops, a retry asks the server how much it already has and carries on from there.
const CHUNK = 4 * 1024 * 1024;
const MIN_SIZE = 2 * 1024 * 1024;
const FIELDS = ['video', 'trailer'];
const token = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const wait = (ms) => new Promise((r) => setTimeout(r, ms));

const api = (url, opts = {}) =>
    fetch(url, { credentials: 'same-origin', ...opts, headers: { 'X-CSRF-TOKEN': token(), Accept: 'application/json', ...opts.headers } });

// The same file picked again after a failure or a page reload maps to the same server-side upload.
const fingerprint = (file, field) => `mb-upload:${field}:${file.name}:${file.size}:${file.lastModified}`;
const remember = (key, id) => { try { localStorage.setItem(key, id); } catch (e) { /* storage unavailable */ } };
const recall = (key) => { try { return localStorage.getItem(key); } catch (e) { return null; } };
const forget = (key) => { try { localStorage.removeItem(key); } catch (e) { /* storage unavailable */ } };

async function receivedBytes(id) {
    const res = await api(`/admin/uploads/${id}`);
    return res.ok ? (await res.json()).received : null;
}

async function open(file, key) {
    const saved = recall(key);
    if (saved) {
        const received = await receivedBytes(saved).catch(() => null);
        if (received !== null && received <= file.size) return { id: saved, received };
        forget(key);
    }

    const res = await api('/admin/uploads', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: file.name, size: file.size }),
    });
    if (!res.ok) throw new Error(`Could not start upload (${res.status}).`);
    const { id } = await res.json();
    remember(key, id);

    return { id, received: 0 };
}

async function sendChunk(id, file, offset) {
    const res = await api(`/admin/uploads/${id}`, {
        method: 'PUT',
        headers: { 'X-Upload-Offset': String(offset), 'Content-Type': 'application/octet-stream' },
        body: file.slice(offset, offset + CHUNK),
    });

    // Server is somewhere else than we thought (a reply got lost): adopt its position.
    if (res.status === 409) return (await res.json()).received;
    if (!res.ok) throw new Error(`Upload interrupted (${res.status}).`);

    return (await res.json()).received;
}

async function upload(file, field, onBytes) {
    const key = fingerprint(file, field);
    let { id, received } = await open(file, key);
    onBytes(received);

    while (received < file.size) {
        let attempt = 0;
        for (;;) {
            try {
                received = await sendChunk(id, file, received);
                break;
            } catch (e) {
                if (++attempt > 3) throw new Error('Connection lost. Press Retry to continue from where it stopped.');
                await wait(attempt * 2000);
                // Re-sync with the server before trying again.
                const now = await receivedBytes(id).catch(() => null);
                if (now !== null) received = now;
            }
        }
        onBytes(received);
    }

    return id;
}

/**
 * Builds the FormData for a form (or takes one already built), sending any large video/trailer file in resumable pieces first.
 * The file is swapped for a "{field}_upload_id" entry. Resolves with the (now small) FormData.
 * `onProgress(percent)` covers only the chunked files.
 */
window.prepareResumableUpload = async function (formOrData, onProgress = () => {}) {
    const fd = formOrData instanceof FormData ? formOrData : new FormData(formOrData);
    const jobs = FIELDS.map((field) => [field, fd.get(field)]).filter(([, f]) => f instanceof File && f.size >= MIN_SIZE);
    const total = jobs.reduce((sum, [, f]) => sum + f.size, 0);
    let done = 0;

    for (const [field, file] of jobs) {
        const id = await upload(file, field, (bytes) => onProgress(Math.min(100, Math.floor(((done + bytes) / total) * 100))));
        done += file.size;
        fd.delete(field);
        fd.append(`${field}_upload_id`, id);
    }

    return fd;
};
