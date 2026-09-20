// Background video uploads. A title (movie, series, episode) is saved first; its video or trailer
// is then sent from the browser in small pieces while the admin keeps working. If the connection
// drops, a retry asks the server how much it already holds and continues from there.
const CHUNK = 4 * 1024 * 1024;
const PARALLEL = 2;
const token = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const wait = (ms) => new Promise((r) => setTimeout(r, ms));

const api = (url, opts = {}) =>
    fetch(url, { credentials: 'same-origin', ...opts, headers: { 'X-CSRF-TOKEN': token(), Accept: 'application/json', ...opts.headers } });

async function receivedBytes(id) {
    const res = await api(`/admin/uploads/${id}`);
    return res.ok ? (await res.json()).received : null;
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

async function sendFile(id, file, onBytes) {
    let received = await receivedBytes(id);
    if (received === null) throw new Error('This upload no longer exists on the server.');
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
                const now = await receivedBytes(id).catch(() => null);
                if (now !== null) received = now;
            }
        }
        onBytes(received);
    }
}

export function uploadsStore() {
    return {
        items: [],   // uploads driven by this tab
        remote: {},  // server-side progress of uploads on this page (may be driven by another tab)
        _poll: null,
        _seen: new Set(),
        _ids: new Set(),

        /** plan: {id, field, url, name, size, label?} from the server; file: the chosen File. */
        add(plan, file, label = '') {
            if (this.items.some((i) => i.id === plan.id)) return;
            this.items.push({ id: plan.id, field: plan.field, url: plan.url, file, label: label || plan.name, status: 'queued', percent: 0, error: '' });
            this.pump();
        },

        find(id) { return this.items.find((i) => i.id === id); },
        get busy() { return this.items.some((i) => ['queued', 'uploading', 'finalizing'].includes(i.status)); },

        /** What a page should show for an upload id: this tab's live state, else the server's. */
        state(id) {
            const local = this.find(id);
            if (local) return { status: local.status, percent: local.percent, error: local.error, local: true };
            const r = this.remote[id];
            if (r) return { status: r.stalled ? 'stalled' : 'uploading', percent: r.percent, error: '', local: false };
            return null;
        },

        /** Badge text for an upload: live percent when known, else the last value the server rendered. */
        label(id, fallbackPercent = 0) {
            const s = this.state(id);
            if (!s) return `Processing — uploading ${fallbackPercent}%`;
            return s.status === 'stalled' ? 'Processing — upload interrupted' : `Processing — uploading ${s.percent}%`;
        },

        retry(item) {
            item.status = 'queued';
            item.error = '';
            this.pump();
        },

        async cancel(item) {
            this.items = this.items.filter((i) => i !== item);
            item.cancelled = true;
            await api(`/admin/uploads/${item.id}`, { method: 'DELETE' }).catch(() => {});
            this.pump();
        },

        dismiss(item) { this.items = this.items.filter((i) => i !== item); },

        pump() {
            const running = this.items.filter((i) => ['uploading', 'finalizing'].includes(i.status)).length;
            this.items.filter((i) => i.status === 'queued').slice(0, Math.max(0, PARALLEL - running)).forEach((i) => this.run(i));
        },

        async run(item) {
            item.status = 'uploading';
            try {
                await sendFile(item.id, item.file, (bytes) => {
                    item.percent = Math.min(100, Math.floor((bytes / item.file.size) * 100));
                });
                if (item.cancelled) return;

                item.status = 'finalizing';
                const body = new FormData();
                body.append('_token', token());
                body.append(`${item.field}_upload_id`, item.id);
                const res = await fetch(item.url, { method: 'POST', body, credentials: 'same-origin', headers: { Accept: 'text/html' } });
                if (!res.ok) throw new Error(`Could not attach the video (${res.status}). Press Retry.`);

                item.status = 'done';
                item.percent = 100;
                window.dispatchEvent(new CustomEvent('mb-upload-done', { detail: item }));
                // Edit pages show the new "Processing" state after a refresh.
                if (window.__mbReloadOnDone && !this.busy && !window.__mbSaving) location.reload();
            } catch (e) {
                if (item.cancelled) return;
                item.status = 'error';
                item.error = e.message;
            }
            this.pump();
        },

        /** Poll the server for these uploads (edit pages); reload once one finishes and nothing else is running. */
        watch(ids) {
            ids.forEach((id) => this._ids.add(id));
            if (this._poll || !this._ids.size) return;

            const tick = async () => {
                const list = [...this._ids];
                const res = await api('/admin/uploads/progress?' + list.map((id) => 'ids[]=' + encodeURIComponent(id)).join('&')).catch(() => null);
                if (!res || !res.ok) return;
                const data = await res.json();
                this.remote = data;
                Object.keys(data).forEach((id) => this._seen.add(id));
                const finished = list.some((id) => this._seen.has(id) && !data[id] && !this.find(id));
                if (finished && !this.busy && !window.__mbSaving) location.reload();
            };
            tick();
            this._poll = setInterval(tick, 4000);
        },

        /** Continue an upload that lost its browser: the admin picks the same file again. */
        resume(plan, file) {
            if (file.name !== plan.name || file.size !== plan.size) {
                return `That isn't the same file. Choose “${plan.name}”.`;
            }
            this.add(plan, file);
            return '';
        },
    };
}

window.addEventListener('beforeunload', (e) => {
    if (window.Alpine?.store('uploads')?.busy) {
        e.preventDefault();
        e.returnValue = '';
    }
});

/**
 * Saves a create form without its video/trailer files, then hands those files to the upload
 * store so they upload in the background. Resolves with the server's reply (title, links, uploads).
 */
window.createWithBackgroundUploads = async function (source, fields, url = source.action) {
    window.__mbSaving = (window.__mbSaving || 0) + 1;
    try {
        return await saveWithoutFiles(source, fields, url);
    } finally {
        window.__mbSaving--;
    }
};

async function saveWithoutFiles(source, fields, url) {
    const fd = source instanceof FormData ? source : new FormData(source);
    const files = {};

    fields.forEach((field) => {
        const file = fd.get(field);
        fd.delete(field);
        if (file instanceof File && file.size) {
            files[field] = file;
            fd.append(`${field}_name`, file.name);
            fd.append(`${field}_size`, String(file.size));
        }
    });

    const res = await fetch(url, {
        method: 'POST', body: fd, credentials: 'same-origin', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token() },
    });

    if (res.status === 422) {
        const body = await res.json();
        throw new Error(Object.values(body.errors ?? {}).flat().join(' ') || body.message);
    }
    if (!res.ok) throw new Error(`Could not save (${res.status}). Please try again.`);

    const data = await res.json();
    data.uploads.forEach((plan) => window.Alpine.store('uploads').add(plan, files[plan.field], `${data.title} — ${plan.kind}`));

    return data;
}

/** Starts a background upload for an existing title (replacing its video or trailer). */
window.startBackgroundUpload = async function ({ targetType, targetId, kind, url, label }, file) {
    const res = await api('/admin/uploads', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: file.name, size: file.size, target_type: targetType, target_id: targetId, kind }),
    });
    if (!res.ok) throw new Error(`Could not start the upload (${res.status}).`);
    const { id } = await res.json();
    window.Alpine.store('uploads').add({ id, field: kind, url, name: file.name, size: file.size }, file, label);

    return id;
};

/**
 * Shared state for edit-page components that start or watch an upload.
 * cfg: {targetType, targetId, kind, url, label, upload?: {id, name, size}}
 */
window.uploadMixin = function (cfg) {
    return {
        cfg,
        uploadId: cfg.upload?.id ?? null,
        pickError: '',

        initUploads() {
            window.__mbReloadOnDone = true;
            if (this.uploadId) window.Alpine.store('uploads').watch([this.uploadId]);
        },

        /** This tab's live state for the upload, else the server's; null when nothing is uploading. */
        up() {
            return this.uploadId ? window.Alpine.store('uploads').state(this.uploadId) : null;
        },

        /** Starts uploading the file chosen in `form`; without a file, submits the form normally (e.g. a URL). */
        beginUpload(form, { optional = false } = {}) {
            const input = form.querySelector(`[name="${this.cfg.kind}"]`);
            if (!input || !input.files.length) {
                if (optional) form.submit();
                else this.pickError = 'Please select a file first.';
                return;
            }

            this.pickError = '';
            window.startBackgroundUpload(this.cfg, input.files[0])
                .then((id) => {
                    this.uploadId = id;
                    window.Alpine.store('uploads').watch([id]);
                    input.value = '';
                    if ('open' in this) this.open = false;
                })
                .catch((e) => { this.pickError = e.message; });
        },

        /** The admin re-selects the file of an interrupted upload to carry on from the server's offset. */
        resumeWith(event) {
            const file = event.target.files[0];
            event.target.value = '';
            if (!file || !this.cfg.upload) return;
            const plan = { id: this.uploadId, field: this.cfg.kind, url: this.cfg.url, name: this.cfg.upload.name, size: this.cfg.upload.size };
            this.pickError = window.Alpine.store('uploads').resume(plan, file);
        },
    };
};
