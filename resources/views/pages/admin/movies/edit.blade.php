@extends('layouts.admin')

@section('admin-title', 'Edit Movie')

@section('admin-content')
@php
  $uid      = auth()->id() ?? 0;
  $exp      = time() + 7200;
  $sig      = hash_hmac('sha256', $uid . ':' . $exp, config('services.streaming.secret'));
  $hlsToken = base64_encode($uid . ':' . $exp . ':' . $sig);
  $genres   = ['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy', 'Horror', 'Musical', 'Mystery', 'Romance', 'Science Fiction', 'Thriller', 'Western'];
  $videoAccept = 'video/mp4,video/webm,video/ogg,video/x-matroska,.mkv';
  $tabBtn = 'border-b-2 px-1 pb-2 text-sm font-semibold transition';
@endphp

<x-ui.page-header :title="$movie->title" subtitle="Manage metadata, video, and trailer.">
  <x-slot:actions>
    <a href="{{ route('movies.show', $movie) }}" target="_blank" class="btn btn-secondary btn-sm"><x-ui.icon name="external" class="size-3.5" /> View on site</a>
    <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary btn-sm"><x-ui.icon name="arrow-left" class="size-3.5" /> Back to movies</a>
  </x-slot:actions>
</x-ui.page-header>

<div x-data="stepper(4, {{ $errors->hasAny(['title','genre','year','duration','status','description','thumbnail','posters','posters.*']) ? 0 : ($errors->has('video') ? 1 : ($errors->has('trailer') ? 2 : ($errors->hasAny(['language','language_code','subtitle_file','subtitle_url']) ? 3 : 'null'))) }})" class="space-y-6 lg:grid lg:grid-cols-[14rem_minmax(0,1fr)] lg:items-start lg:gap-8 lg:space-y-0">
<x-admin.stepper :steps="['Details', 'Video', 'Trailer', 'Subtitles']" :done="[true, (bool) $movie->streaming_path, $movie->trailerIsReady(), $movie->subtitles->isNotEmpty()]" />

<div class="min-w-0 space-y-6">

<div x-show="step === 0" class="space-y-6">
{{-- Metadata --}}
<form method="POST" action="{{ route('admin.movies.update', $movie) }}" enctype="multipart/form-data" id="upload-form" class="card">
  @csrf @method('PUT')
  <div class="card-header"><h2 class="card-title">Movie metadata</h2></div>
  <div class="space-y-5 p-5 sm:p-6">
    <div class="grid gap-5 sm:grid-cols-2">
      <div class="sm:col-span-2"><x-ui.field name="title" label="Title" :value="old('title', $movie->title)" /></div>
      <x-admin.genre-select :selected="old('genre', $movie->genre)" />
      <x-ui.field name="year" type="number" label="Year" :value="old('year', $movie->year)" />
      <x-ui.field name="duration" type="number" label="Duration (minutes)" :value="old('duration', $movie->duration)" />
      <x-ui.select name="status" label="Status" :selected="old('status', $movie->status)" :options="['hidden' => 'Hidden', 'published' => 'Published']" />
      <div class="sm:col-span-2"><x-ui.check name="is_free" label="Free to watch" hint="(no login required)" :checked="old('is_free', $movie->is_free)" /></div>
    </div>

    <x-ui.textarea name="description" label="Description" :value="old('description', $movie->description)" rows="5" />

    <div>
      <label class="label">Thumbnail</label>
      @if ($movie->thumbnail)<img src="{{ $movie->thumbnailUrl() }}" alt="" class="mb-3 h-32 w-auto rounded-lg border border-line object-cover">@endif
      <x-ui.file name="thumbnail" id="edit-thumbnail" accept="image/*" hint="Leave blank to keep current thumbnail" />
      <div id="edit-thumbnail-preview" class="mt-3 flex flex-wrap gap-3"></div>
    </div>

    <div>
      <label class="label">Posters</label>
      @if ($movie->posters->isNotEmpty())
        <div class="mb-3 flex flex-wrap gap-3">
          @foreach ($movie->posters as $poster)
            <div class="group relative">
              <img src="{{ $poster->url() }}" alt="" class="h-28 w-auto rounded-lg border border-line object-cover">
              <form method="POST" action="{{ route('admin.posters.destroy', $poster) }}" onsubmit="return confirm('Remove this poster?')" class="absolute right-1.5 top-1.5">
                @csrf @method('DELETE')
                <button type="submit" class="grid size-6 place-items-center rounded-full bg-black/70 text-xs text-white opacity-0 transition hover:bg-red-500 group-hover:opacity-100">✕</button>
              </form>
            </div>
          @endforeach
        </div>
      @endif
      <x-ui.file name="posters[]" id="edit-posters-input" accept="image/*" multiple hint="Select multiple to add more posters" />
      <div id="edit-posters-preview" class="mt-3 flex flex-wrap gap-3"></div>
    </div>

    <button type="submit" class="btn btn-primary">Save metadata</button>
  </div>
</form>

</div>

<div x-show="step === 1" x-cloak class="space-y-6">
{{-- Video --}}
<section class="card" x-data="movieVideoReplace">
  <div class="card-header"><h2 class="card-title">Video</h2></div>
  <div class="space-y-5 p-5 sm:p-6">
    <div class="flex flex-wrap items-center gap-3">
      @if ($movie->streaming_path)
        <span class="badge badge-success"><span class="size-1.5 rounded-full bg-emerald-500"></span> Streaming ready</span>
        <code class="rounded-md bg-surface-2 px-2 py-1 text-xs text-muted">{{ $movie->streaming_path }}</code>
      @elseif ($movie->video_path)
        <span class="badge badge-warning"><span class="size-1.5 animate-pulse rounded-full bg-amber-500"></span> Processing</span>
      @else
        <span class="badge badge-neutral">No video uploaded</span>
      @endif
      <div class="ml-auto flex flex-wrap items-center gap-2">
        <x-admin.video-preview :title="$movie->title" :src="$movie->streaming_path ? $movie->streamingUrl() : ($movie->video_path ? $movie->videoUrl() : null)" :hls="(bool) $movie->streaming_path"
          :report-url="route('admin.movies.corrupted', $movie)" :clear-url="route('admin.movies.corrupted.clear', $movie)" :corrupted="$movie->is_corrupted" :token="$hlsToken" />
        <button type="button" @click="open = !open" class="btn btn-secondary btn-sm">Replace / set URL</button>
      </div>
    </div>

    <div x-show="open" x-cloak x-transition class="panel space-y-4 p-5">
      <div class="flex gap-5 border-b border-line">
        <button type="button" @click="tab = 'upload'" :class="tab === 'upload' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-fg'" class="{{ $tabBtn }}">Upload file</button>
        <button type="button" @click="tab = 'url'" :class="tab === 'url' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-fg'" class="{{ $tabBtn }}">Streaming URL</button>
      </div>

      <form method="POST" action="{{ route('admin.movies.video', $movie) }}" enctype="multipart/form-data" x-ref="form" @submit.prevent="upload($refs.form)" class="space-y-4">
        @csrf
        <div x-show="tab === 'upload'" class="space-y-3">
          <label @dragover.prevent="dragging = true" @dragleave="dragging = false" @drop.prevent="drop($event)"
            :class="dragging ? 'border-brand bg-brand-soft' : 'border-line-strong'"
            class="flex cursor-pointer flex-col items-center justify-center gap-1 rounded-2xl border-2 border-dashed px-6 py-10 text-center transition">
            <x-ui.icon name="upload" class="size-7 text-muted" />
            <span class="text-sm font-semibold text-fg">Click to browse<span class="font-normal text-muted"> or drag &amp; drop</span></span>
            <span class="text-xs text-muted" x-text="fileName">MP4, WebM, OGG, MKV</span>
            <input x-ref="videoInput" type="file" name="video" accept="{{ $videoAccept }}" :disabled="tab !== 'upload'" class="sr-only"
              @change="fileName = $event.target.files[0] ? $event.target.files[0].name : 'MP4, WebM, OGG, MKV'">
          </label>
          <div x-show="uploading" x-cloak class="space-y-2">
            <div class="flex justify-between text-xs font-semibold uppercase tracking-wider text-muted"><span>Uploading…</span><span x-text="progress < 100 ? progress + '%' : 'Saving…'">0%</span></div>
            <div class="progress"><span class="transition-all" :style="'width:' + progress + '%'"></span></div>
            <p class="text-xs text-muted">Do not close this tab until the upload completes.</p>
          </div>
          <p x-show="error" x-cloak x-text="error" class="field-error"></p>
        </div>

        <div x-show="tab === 'url'" x-cloak>
          <label class="label">HLS master playlist path or URL</label>
          <input type="text" name="streaming_url" placeholder="/stream/movies/title/master.m3u8" autocomplete="off" class="input">
          <p class="hint">Relative path (<code>/stream/…</code>) or full URL — sets the HLS source without re-encoding.</p>
        </div>

        <div class="flex gap-2">
          <button type="submit" :disabled="uploading" x-text="submitLabel" class="btn btn-primary">Upload</button>
          <button type="button" @click="open = false" class="btn btn-secondary">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</section>

{{-- Encoding quality --}}
<section class="card" x-data='encodingQuality({
  streamingUrl: @json($movie->streamingUrl()),
  statusUrl: @json(route("admin.movies.encoding-status", $movie)),
  reprocessUrl: @json(route("admin.movies.reprocess", $movie)),
  encodeQualityUrl: @json(route("admin.movies.encode-quality", $movie)),
  hasVideo: @json((bool) $movie->video_path),
  hlsToken: @json($hlsToken),
  csrf: @json(csrf_token()),
})'>
  <div class="card-header">
    <h2 class="card-title">Encoding quality</h2>
    @if ($movie->video_path)
      <button type="button" @click="reencodeAll()" :disabled="encoding" class="btn btn-secondary btn-sm"><x-ui.icon name="refresh" class="size-3.5" /> Re-encode all</button>
    @endif
  </div>
  <div class="space-y-5 p-5 sm:p-6">
    <div x-show="encoding" x-cloak class="panel space-y-2 p-4">
      <div class="flex justify-between text-xs font-semibold uppercase tracking-wider text-muted">
        <span x-text="activeQuality ? 'Encoding ' + activeQuality + '…' : 'Encoding…'">Encoding…</span>
        <span x-text="progress + '%'">0%</span>
      </div>
      <div class="progress"><span class="transition-all" :class="progress === 0 ? 'w-full animate-pulse opacity-60' : ''" :style="progress > 0 ? 'width:' + progress + '%' : ''"></span></div>
      <p class="text-xs text-muted">This may take several minutes. You can leave this page.</p>
    </div>

    <div>
      <p class="label">Available encodings</p>
      <div class="flex flex-wrap gap-2">
        <template x-for="q in allQualities" :key="q">
          <span class="badge px-3 py-1.5"
            :class="encoded.includes(q) ? 'badge-success' : (encoding && activeQuality === q ? 'badge-warning' : 'badge-neutral')">
            <span class="size-1.5 rounded-full" :class="encoded.includes(q) ? 'bg-emerald-500' : (encoding && activeQuality === q ? 'animate-pulse bg-amber-400' : 'bg-muted/50')"></span>
            <span x-text="q"></span>
          </span>
        </template>
        <span x-show="loading && !encoded.length" x-cloak class="text-xs text-muted">Checking…</span>
      </div>
    </div>

    @if ($movie->video_path)
      <div>
        <p class="label">Re-encode a single quality</p>
        <div class="flex flex-wrap gap-2">
          @foreach (['360p', '480p', '720p', '1080p'] as $quality)
            <button type="button" @click="reencode('{{ $quality }}')" :disabled="encoding" class="btn btn-secondary btn-sm">{{ $quality }}</button>
          @endforeach
        </div>
        <p class="hint">Each quality encodes independently — the master playlist is updated additively.</p>
      </div>
    @else
      <p class="text-sm text-muted">Upload a source video file to enable re-encoding.</p>
    @endif
  </div>
</section>

</div>

<div x-show="step === 2" x-cloak class="space-y-6">
{{-- Trailer --}}
<section class="card">
  <div class="card-header"><h2 class="card-title">Trailer</h2></div>
  <div class="space-y-5 p-5 sm:p-6">
    @if ($movie->trailerIsReady())
      <div>
        <video id="admin-trailer-video" controls preload="metadata" class="aspect-video w-full max-w-xl rounded-xl bg-black"></video>
        <p class="mt-2 text-xs text-muted">{{ basename($movie->trailer_path) }}</p>
      </div>
    @elseif ($movie->trailer_path)
      <span class="badge badge-warning"><span class="size-1.5 animate-pulse rounded-full bg-amber-500"></span> Trailer processing…</span>
    @endif

    <form x-data="trailerUpload" method="POST" action="{{ route('admin.movies.trailer', $movie) }}" enctype="multipart/form-data" @submit.prevent="upload($el)" class="max-w-xl space-y-4">
      @csrf
      <x-ui.file name="trailer" id="movie-trailer" :label="$movie->trailer_path ? 'Replace trailer' : 'Upload trailer'" hint="MP4, WebM, OGG or MKV — will be transcoded to MP4" :accept="$videoAccept" />
      <div x-show="uploading" x-cloak class="space-y-2">
        <div class="flex justify-between text-xs font-semibold uppercase tracking-wider text-muted"><span>Uploading trailer…</span><span x-text="progress < 100 ? progress + '%' : 'Saving…'">0%</span></div>
        <div class="progress"><span class="transition-all" :style="'width:' + progress + '%'"></span></div>
        <p class="text-xs text-muted">Large files may take a few minutes. Do not close this tab.</p>
      </div>
      <p x-show="error" x-cloak x-text="error" class="field-error"></p>
      <button type="submit" :disabled="uploading" class="btn btn-primary">
        <span x-show="!uploading">{{ $movie->trailer_path ? 'Replace trailer' : 'Upload trailer' }}</span>
        <span x-show="uploading && progress < 100" x-cloak>Uploading…</span>
        <span x-show="uploading && progress >= 100" x-cloak>Saving…</span>
      </button>
    </form>
  </div>
</section>

</div>

<div x-show="step === 3" x-cloak class="space-y-6">
{{-- Subtitles --}}
<section class="card">
  <div class="card-header"><h2 class="card-title">Subtitle tracks</h2></div>
  <div class="space-y-6 p-5 sm:p-6">
    @if ($movie->subtitles->isNotEmpty())
      <ul class="divide-y divide-line rounded-xl border border-line">
        @foreach ($movie->subtitles as $subtitle)
          <li class="flex items-center justify-between gap-3 px-4 py-3">
            <div class="flex items-center gap-3">
              <span class="text-sm font-semibold text-fg">{{ $subtitle->language }}</span>
              <code class="rounded-md bg-surface-2 px-2 py-0.5 text-xs text-muted">{{ $subtitle->language_code }}</code>
              @if ($subtitle->is_default)<span class="badge badge-brand">Default</span>@endif
            </div>
            <form method="POST" action="{{ route('admin.subtitles.destroy', $subtitle) }}">@csrf @method('DELETE')<button type="submit" class="btn btn-danger-soft btn-sm">Remove</button></form>
          </li>
        @endforeach
      </ul>
    @else
      <p class="text-sm text-muted">No subtitle tracks yet.</p>
    @endif

    <form method="POST" action="{{ route('admin.movies.subtitles.store', $movie) }}" enctype="multipart/form-data" x-data="{ subTab: 'upload' }" class="panel max-w-xl space-y-4 p-5">
      @csrf
      <p class="text-sm font-semibold text-fg">Add subtitle track</p>
      <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="label">Language</label><input type="text" name="language" placeholder="English" required class="input"></div>
        <div><label class="label">Language code</label><input type="text" name="language_code" placeholder="en" maxlength="10" required class="input"></div>
      </div>
      <div class="flex gap-5 border-b border-line">
        <button type="button" @click="subTab = 'upload'" :class="subTab === 'upload' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-fg'" class="{{ $tabBtn }}">Upload file</button>
        <button type="button" @click="subTab = 'url'" :class="subTab === 'url' ? 'border-brand text-brand' : 'border-transparent text-muted hover:text-fg'" class="{{ $tabBtn }}">Enter URL</button>
      </div>
      <div x-show="subTab === 'upload'">
        <input type="file" name="subtitle_file" accept=".vtt,.srt" class="input">
        <p class="hint">WebVTT (.vtt) or SubRip (.srt) — SRT is auto-converted to VTT</p>
      </div>
      <div x-show="subTab === 'url'" x-cloak>
        <label class="label">Subtitle URL</label>
        <input type="text" name="subtitle_url" placeholder="/subtitles/en.vtt or https://cdn.example.com/en.vtt" autocomplete="off" class="input">
        <p class="hint">Relative path (e.g. <code>/subtitles/en.vtt</code>) or absolute URL to a WebVTT file.</p>
      </div>
      <x-ui.check name="is_default" id="sub_default_movie" label="Set as default track" />
      <button type="submit" class="btn btn-primary">Add subtitle track</button>
    </form>
  </div>
</section>
</div>

<div class="flex items-center justify-between">
  <button type="button" @click="prev()" x-show="step > 0" class="btn btn-secondary"><x-ui.icon name="arrow-left" class="size-4" /> Back</button>
  <span x-show="step === 0"></span>
  <button type="button" @click="next()" x-show="!last" class="btn btn-primary">Next <x-ui.icon name="arrow-right" class="size-4" /></button>
  <a href="{{ route('admin.movies.index') }}" x-show="last" x-cloak class="btn btn-primary">Done</a>
</div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
// ── Admin trailer preview ─────────────────────────────────────
(function () {
  var video = document.getElementById('admin-trailer-video');
  if (!video) return;
  var src = @json($movie->trailerIsReady() ? $movie->trailerUrl() : '');
  if (!src) return;
  var hlsToken = @json($hlsToken);
  if (Hls.isSupported()) {
    var hls = new Hls({ xhrSetup: function (xhr) { xhr.setRequestHeader('Authorization', 'Bearer ' + hlsToken); } });
    hls.loadSource(src);
    hls.attachMedia(video);
  } else {
    video.src = src;
  }
})();

// ── Image previews ────────────────────────────────────────────
(function () {
  function attachPreview(inputId, containerId) {
    var input = document.getElementById(inputId);
    var container = document.getElementById(containerId);
    if (!input || !container) return;
    input.addEventListener('change', function () {
      container.innerHTML = '';
      Array.from(input.files).forEach(function (file) {
        if (!file.type.startsWith('image/')) return;
        var reader = new FileReader();
        reader.onload = function (e) {
          var wrap = document.createElement('div');
          wrap.className = 'relative';
          var img = document.createElement('img');
          img.src = e.target.result;
          img.className = 'h-24 w-auto rounded-lg border border-line object-cover';
          var name = document.createElement('p');
          name.className = 'mt-1 max-w-[96px] truncate text-[10px] text-muted';
          name.textContent = file.name;
          wrap.appendChild(img);
          wrap.appendChild(name);
          container.appendChild(wrap);
        };
        reader.readAsDataURL(file);
      });
    });
  }

  attachPreview('edit-thumbnail', 'edit-thumbnail-preview');
  attachPreview('edit-posters-input', 'edit-posters-preview');
})();

// ── Alpine components ─────────────────────────────────────────
document.addEventListener('alpine:init', function () {
  function videoUpload(inputName) {
    return function (form) {
      var input = form.querySelector('[name="' + inputName + '"]');
      if (!input || !input.files.length) { form.submit(); return; }
      var fd = new FormData(form);
      var xhr = new XMLHttpRequest();
      var self = this;
      self.uploading = true; self.progress = 0; self.error = '';
      xhr.upload.addEventListener('progress', function (ev) {
        if (ev.lengthComputable) self.progress = Math.round(ev.loaded / ev.total * 100);
      });
      xhr.addEventListener('load', function () {
        if (xhr.status >= 200 && xhr.status < 400) { window.location.href = xhr.responseURL; }
        else { self.uploading = false; self.error = 'Upload failed (' + xhr.status + ') — try again.'; }
      });
      xhr.addEventListener('error', function () { self.uploading = false; self.error = 'Upload failed — try again.'; });
      xhr.open('POST', form.action);
      xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');
      xhr.send(fd);
    };
  }

  Alpine.data('movieVideoReplace', function () {
    return {
      open: false,
      tab: 'upload',
      uploading: false,
      progress: 0,
      fileName: 'MP4, WebM, OGG, MKV',
      dragging: false,
      error: '',
      get submitLabel() {
        if (this.tab === 'url') return 'Save URL';
        if (this.uploading) return this.progress < 100 ? 'Uploading…' : 'Saving…';
        return 'Upload';
      },
      upload: videoUpload('video'),
      drop: function (e) {
        this.dragging = false;
        var files = e.dataTransfer.files;
        if (!files.length) return;
        if (this.$refs.videoInput) { this.$refs.videoInput.files = files; }
        this.fileName = files[0].name;
      }
    };
  });

  Alpine.data('encodingQuality', function (opts) {
    return {
      allQualities: ['360p', '480p', '720p', '1080p'],
      encoded: [],
      loading: false,
      encoding: false,
      progress: 0,
      activeQuality: null,
      _pollTimer: null,

      init: function () {
        this.loadQualities();
        this.pollStatus();
      },

      destroy: function () {
        if (this._pollTimer) { clearInterval(this._pollTimer); this._pollTimer = null; }
      },

      loadQualities: function () {
        if (!opts.streamingUrl) return;
        var self = this;
        self.loading = true;
        fetch(opts.streamingUrl, { headers: { 'Authorization': 'Bearer ' + opts.hlsToken } })
          .then(function (r) { return r.ok ? r.text() : ''; })
          .then(function (text) {
            var found = [];
            text.split('\n').forEach(function (line) {
              line = line.trim();
              if (line === '360p/index.m3u8') found.push('360p');
              else if (line === '480p/index.m3u8') found.push('480p');
              else if (line === '720p/index.m3u8') found.push('720p');
              else if (line === '1080p/index.m3u8') found.push('1080p');
            });
            self.encoded = found;
            self.loading = false;
          })
          .catch(function () { self.loading = false; });
      },

      pollStatus: function () {
        var self = this;
        fetch(opts.statusUrl)
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (data.encoding) {
              self.encoding = true;
              self.progress = data.progress;
              if (!self._pollTimer) {
                self._pollTimer = setInterval(function () { self.pollStatus(); }, 3000);
              }
            } else {
              var wasEncoding = self.encoding;
              self.encoding = false;
              self.progress = 0;
              self.activeQuality = null;
              if (self._pollTimer) { clearInterval(self._pollTimer); self._pollTimer = null; }
              if (wasEncoding) { self.loadQualities(); }
            }
          })
          .catch(function () {});
      },

      _postEncode: function (url, body, onSuccess) {
        var self = this;
        var params = new URLSearchParams(body);
        params.append('_token', opts.csrf);
        fetch(url, {
          method: 'POST',
          headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
          body: params.toString(),
        })
          .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
          .then(function (res) {
            if (!res.ok) {
              self.encoding = false;
              alert(res.data.error || 'Re-encode failed. Check the streaming service.');
              return;
            }
            onSuccess();
          })
          .catch(function (err) {
            self.encoding = false;
            alert('Request failed: ' + err.message);
          });
      },

      reencode: function (quality) {
        if (!confirm('Re-encode at ' + quality + '? This will cancel any running job.')) return;
        var self = this;
        self.activeQuality = quality;
        self.encoding = true;
        self.progress = 0;
        self._postEncode(opts.encodeQualityUrl, { resolution: quality }, function () {
          if (!self._pollTimer) {
            self._pollTimer = setInterval(function () { self.pollStatus(); }, 3000);
          }
        });
      },

      reencodeAll: function () {
        if (!confirm('Re-encode at all qualities (360p, 480p, 720p)? This will cancel any running job.')) return;
        var self = this;
        self.activeQuality = null;
        self.encoding = true;
        self.progress = 0;
        self._postEncode(opts.reprocessUrl, {}, function () {
          if (!self._pollTimer) {
            self._pollTimer = setInterval(function () { self.pollStatus(); }, 3000);
          }
        });
      }
    };
  });

  Alpine.data('trailerUpload', function () {
    return {
      uploading: false,
      progress: 0,
      error: '',
      upload: function (form) {
        var input = form.querySelector('[name="trailer"]');
        if (!input || !input.files.length) {
          this.error = 'Please select a file first.';
          return;
        }
        var fd = new FormData(form);
        var xhr = new XMLHttpRequest();
        var self = this;
        self.uploading = true; self.progress = 0; self.error = '';
        xhr.upload.addEventListener('progress', function (ev) {
          if (ev.lengthComputable) self.progress = Math.round(ev.loaded / ev.total * 100);
        });
        xhr.addEventListener('load', function () {
          if (xhr.status >= 200 && xhr.status < 400) { window.location.href = xhr.responseURL; }
          else { self.uploading = false; self.error = 'Upload failed (' + xhr.status + ') — try again.'; }
        });
        xhr.addEventListener('error', function () { self.uploading = false; self.error = 'Upload failed — try again.'; });
        xhr.open('POST', form.action);
        xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');
        xhr.send(fd);
      }
    };
  });
});
</script>
@endpush
