@extends('layouts.admin')

@section('admin-title', 'Edit Movie')

@section('admin-content')
<div class="mb-10">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">{{ $movie->title }}</h2>
  <p class="mt-1 text-gray-500 dark:text-gray text-sm">Manage metadata, video, and trailer.</p>
</div>

{{-- Metadata --}}
<section class="mb-14">
  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-600">
    Movie Metadata
  </h3>

<form method="POST" action="{{ route('admin.movies.update', $movie) }}" enctype="multipart/form-data" id="upload-form"
  class="flex flex-col gap-y-6 max-w-2xl">
  @csrf @method('PUT')

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div class="sm:col-span-2">
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="title">Title</label>
      <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('title') border-danger @enderror"
        type="text" name="title" id="title" value="{{ old('title', $movie->title) }}">
      @error('title')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="genre">Genre</label>
      <select class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
        name="genre" id="genre">
        <option value="">— Select genre —</option>
        @foreach (['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy', 'Horror', 'Musical', 'Mystery', 'Romance', 'Science Fiction', 'Thriller', 'Western'] as $genre)
          <option value="{{ $genre }}" {{ old('genre', $movie->genre) === $genre ? 'selected' : '' }}>{{ $genre }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="year">Year</label>
      <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
        type="number" name="year" id="year" value="{{ old('year', $movie->year) }}">
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="duration">Duration (minutes)</label>
      <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
        type="number" name="duration" id="duration" value="{{ old('duration', $movie->duration) }}">
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="status">Status</label>
      <select class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
        name="status" id="status">
        <option value="hidden" {{ old('status', $movie->status) === 'hidden' ? 'selected' : '' }}>Hidden</option>
        <option value="published" {{ old('status', $movie->status) === 'published' ? 'selected' : '' }}>Published</option>
      </select>
    </div>
  </div>

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="description">Description</label>
    <textarea class="block px-4 py-2 border-base bg-white/40 font-medium tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full h-32"
      name="description" id="description">{{ old('description', $movie->description) }}</textarea>
  </div>

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Thumbnail</label>
    @if ($movie->thumbnail)
      <img src="{{ $movie->thumbnailUrl() }}" class="w-20 h-28 object-cover mb-3" alt="">
    @endif
    <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
      type="file" name="thumbnail" id="edit-thumbnail" accept="image/*">
    <p class="mt-1 text-xs text-gray-500">Leave blank to keep current thumbnail</p>
    <div id="edit-thumbnail-preview" class="flex flex-wrap gap-2 mt-3"></div>
  </div>

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Posters</label>
    @if ($movie->posters->isNotEmpty())
      <div class="flex flex-wrap gap-3 mb-4">
        @foreach ($movie->posters as $poster)
          <div class="relative group">
            <img src="{{ $poster->url() }}" class="w-24 h-32 object-cover" alt="">
            <form method="POST" action="{{ route('admin.posters.destroy', $poster) }}"
              onsubmit="return confirm('Remove this poster?')" class="absolute top-1 right-1">
              @csrf @method('DELETE')
              <button type="submit"
                class="w-6 h-6 flex items-center justify-center bg-black/70 text-white text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity hover:bg-danger">
                ✕
              </button>
            </form>
          </div>
        @endforeach
      </div>
    @endif
    <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
      type="file" name="posters[]" id="edit-posters-input" accept="image/*" multiple>
    <p class="mt-1 text-xs text-gray-500">Select multiple to add more posters</p>
    <div id="edit-posters-preview" class="flex flex-wrap gap-2 mt-3"></div>
    @error('posters.*')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
  </div>

  <div>
    <button type="submit"
      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
      Save Metadata
    </button>
  </div>
</form>
</section>

{{-- Video --}}
<section class="mb-14">
  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-600">
    Video
  </h3>

  <div x-data="movieVideoReplace">
    {{-- Status + toggle --}}
    <div class="flex flex-wrap items-center gap-x-5 gap-y-3 mb-5">
      @if ($movie->streaming_path)
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold uppercase tracking-wider rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
          <span class="w-1.5 h-1.5 rounded-full bg-green-500 shrink-0"></span>
          Streaming ready
        </span>
        <code class="text-xs text-gray-400 hidden sm:inline truncate max-w-xs">{{ $movie->streaming_path }}</code>
      @elseif ($movie->video_path)
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold uppercase tracking-wider rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
          <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 shrink-0"></span>
          Processing
        </span>
      @else
        <span class="text-sm text-gray-400">No video uploaded.</span>
      @endif
      <button type="button" @click="open = !open"
        class="text-xs font-bold uppercase tracking-wider py-2 px-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-accent hover:text-accent dark:hover:text-accent transition-colors">
        Replace / Set URL
      </button>
    </div>

    {{-- Expandable panel --}}
    <div style="display:none" x-show="open" class="max-w-xl">

      {{-- Underline tab nav --}}
      <div class="flex border-b border-gray-200 dark:border-gray-600 mb-6">
        <button type="button" @click="tab = 'upload'"
          :class="tab === 'upload' ? 'border-accent text-accent' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
          class="inline-flex items-center gap-x-2 pb-3 px-1 mr-6 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors">
          <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
          Upload File
        </button>
        <button type="button" @click="tab = 'url'"
          :class="tab === 'url' ? 'border-accent text-accent' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
          class="inline-flex items-center gap-x-2 pb-3 px-1 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors">
          <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
          Streaming URL
        </button>
      </div>

      <form method="POST" action="{{ route('admin.movies.video', $movie) }}"
        enctype="multipart/form-data" x-ref="form" @submit.prevent="upload($refs.form)" class="flex flex-col gap-y-5">
        @csrf

        {{-- Upload panel --}}
        <div x-show="tab === 'upload'" class="flex flex-col gap-y-4">
          <label
            @dragover.prevent="dragging = true"
            @dragleave="dragging = false"
            @drop.prevent="drop($event)"
            :class="dragging ? 'border-accent' : 'border-gray-300 dark:border-gray-600'"
            class="flex flex-col items-center justify-center gap-y-3 w-full border-2 border-dashed hover:border-accent dark:hover:border-accent transition-colors cursor-pointer py-10 px-6 text-center bg-gray-50 dark:bg-gray-800/40 group">
            <svg class="h-8 w-8 fill-gray-400 group-hover:fill-accent transition-colors" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-accent transition-colors">Click to browse<span class="hidden sm:inline"> or drag &amp; drop</span></span>
            <span x-text="fileName" class="text-xs text-gray-500">MP4, WebM, OGG, MKV</span>
            <input x-ref="videoInput" type="file" name="video"
              accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv"
              :disabled="tab !== 'upload'"
              class="sr-only"
              @change="fileName = $event.target.files[0] ? $event.target.files[0].name : 'MP4, WebM, OGG, MKV'">
          </label>

          <div x-show="uploading" style="display:none" class="flex flex-col gap-y-1.5">
            <div class="flex justify-between text-xs font-bold uppercase text-gray-500">
              <span>Uploading…</span>
              <span x-text="progress < 100 ? progress + '%' : 'Saving…'">0%</span>
            </div>
            <div class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
              <div class="h-1.5 bg-accent transition-all duration-150 rounded-full" :style="'width:' + progress + '%'"></div>
            </div>
            <p class="text-xs text-gray-500">Do not close this tab until the upload completes.</p>
          </div>

          <p x-show="error" style="display:none" x-text="error" class="text-xs text-danger"></p>
        </div>

        {{-- URL panel --}}
        <div x-show="tab === 'url'" style="display:none" class="flex flex-col gap-y-4">
          <div>
            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">HLS Master Playlist Path or URL</label>
            <div class="flex items-stretch border border-gray-300 dark:border-gray-600 focus-within:border-accent transition-colors">
              <div class="flex items-center px-3 bg-gray-50 dark:bg-gray-800 border-r border-gray-300 dark:border-gray-600 shrink-0">
                <svg class="h-4 w-4 fill-gray-400" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
              </div>
              <input class="flex-1 px-3 py-2.5 bg-white dark:bg-gray-900 font-medium text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none"
                type="text" name="streaming_url" placeholder="/stream/movies/title/master.m3u8" autocomplete="off">
            </div>
            <p class="mt-1.5 text-xs text-gray-500">Relative path (<code>/stream/…</code>) or full URL — sets the HLS source without re-encoding.</p>
          </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-600">
          <button type="submit" :disabled="uploading"
            class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-3 px-6 text-sm text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-50 disabled:cursor-not-allowed"
            x-text="submitLabel">
            Upload
          </button>
          <button type="button" @click="open = false"
            class="text-xs font-bold uppercase text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</section>

{{-- Trailer --}}
<section class="mb-14">
  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-600">
    Trailer
  </h3>

  @if ($movie->trailerIsReady())
    <div class="mb-5">
      <video id="admin-trailer-video" class="w-full max-w-xl bg-black" style="aspect-ratio:16/9;" controls preload="metadata"></video>
      <p class="mt-2 text-xs text-gray-500">{{ basename($movie->trailer_path) }}</p>
    </div>
  @elseif ($movie->trailer_path)
    <div class="mb-5 flex items-center gap-x-3 py-3 px-4 bg-gray-50 dark:bg-gray-800 max-w-xl">
      <svg class="h-4 w-4 fill-gray-400 shrink-0" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
      <span class="text-xs font-bold uppercase text-gray-400 tracking-widest">Trailer processing…</span>
    </div>
  @endif

  <form x-data="trailerUpload" method="POST" action="{{ route('admin.movies.trailer', $movie) }}"
    enctype="multipart/form-data" @submit.prevent="upload($el)" class="flex flex-col gap-y-4 max-w-xl">
    @csrf

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="movie-trailer">
        {{ $movie->trailer_path ? 'Replace Trailer' : 'Upload Trailer' }}
      </label>
      <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
        type="file" name="trailer" id="movie-trailer" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv">
      <p class="mt-1 text-xs text-gray-500">MP4, WebM, OGG or MKV — will be transcoded to MP4</p>
      @error('trailer')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
    </div>

    <div x-show="uploading" style="display:none" class="flex flex-col gap-y-2">
      <div class="flex justify-between text-xs font-bold uppercase text-gray-500">
        <span>Uploading trailer…</span>
        <span x-text="progress < 100 ? progress + '%' : 'Saving…'">0%</span>
      </div>
      <div class="w-full h-2 bg-gray-200 dark:bg-gray-700">
        <div class="h-2 bg-accent transition-all duration-150" :style="'width:' + progress + '%'"></div>
      </div>
      <p class="text-xs text-gray-500">Large files may take a few minutes. Do not close this tab.</p>
    </div>

    <p x-show="error" style="display:none" x-text="error" class="text-xs text-danger"></p>

    <div>
      <button type="submit" :disabled="uploading"
        class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-50 disabled:cursor-not-allowed">
        <span x-show="!uploading">{{ $movie->trailer_path ? 'Replace Trailer' : 'Upload Trailer' }}</span>
        <span x-show="uploading && progress < 100" style="display:none">Uploading…</span>
        <span x-show="uploading && progress >= 100" style="display:none">Saving…</span>
      </button>
    </div>
  </form>
</section>

<div>
  <a href="{{ route('admin.movies.index') }}"
    class="text-sm font-bold text-gray-500 hover:text-accent dark:hover:text-accent transition-colors uppercase">
    ← Back to Movies
  </a>
</div>
@endsection

@push('scripts')
@php
    $uid      = auth()->id() ?? 0;
    $exp      = time() + 7200;
    $sig      = hash_hmac('sha256', $uid . ':' . $exp, env('APP_SECRET', ''));
    $hlsToken = base64_encode($uid . ':' . $exp . ':' . $sig);
@endphp
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
          img.className = 'h-24 w-auto object-cover border border-gray-200 dark:border-gray-600';
          var name = document.createElement('p');
          name.className = 'text-[10px] text-gray-400 mt-1 truncate max-w-[96px]';
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
