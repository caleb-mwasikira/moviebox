@extends('layouts.admin')

@section('admin-title', 'Edit Coming Soon')

@section('admin-content')
<div class="mb-10 flex items-start justify-between gap-x-6">
  <div>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">{{ $film->title }}</h2>
    <p class="mt-1 text-gray-500 dark:text-gray text-sm">Manage details, trailer, and release video.</p>
  </div>
  <form action="{{ route('admin.coming-soon.toggle-featured', $film) }}" method="POST" class="shrink-0 mt-1">
    @csrf @method('PATCH')
    <button type="submit"
      class="inline-flex items-center gap-x-1.5 text-xs font-bold uppercase tracking-wide py-2 px-4 border transition-colors
        {{ $film->featured
          ? 'border-accent text-accent hover:bg-accent/10'
          : 'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-accent hover:text-accent' }}">
      <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
      </svg>
      {{ $film->featured ? 'Featured' : 'Mark as Featured' }}
    </button>
  </form>
</div>

@if (session('status') === 'coming-soon-updated')
  <div class="mb-6 border border-green-500 bg-green-500/10 px-4 py-3 text-sm font-medium text-green-700 dark:text-green-400">
    Film details updated.
  </div>
@elseif (session('status') === 'trailer-processing')
  <div class="mb-6 border border-yellow-500 bg-yellow-500/10 px-4 py-3 text-sm font-medium text-yellow-700 dark:text-yellow-400">
    Trailer uploaded and is being processed for streaming.
  </div>
@endif

{{-- ── Metadata ── --}}
<section class="mb-14">
  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-600">
    Film Details
  </h3>

  <form method="POST" action="{{ route('admin.coming-soon.update', $film) }}" enctype="multipart/form-data"
    class="flex flex-col gap-y-6 max-w-2xl">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
      <div class="sm:col-span-2">
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="title">Title</label>
        <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('title') border-danger @enderror"
          type="text" name="title" id="title" value="{{ old('title', $film->title) }}">
        @error('title')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="genre">Genre</label>
        <select class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
          name="genre" id="genre">
          <option value="">— Select genre —</option>
          @foreach (['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy', 'Horror', 'Musical', 'Mystery', 'Romance', 'Science Fiction', 'Thriller', 'Western'] as $genre)
            <option value="{{ $genre }}" {{ old('genre', $film->genre) === $genre ? 'selected' : '' }}>{{ $genre }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="year">Release Year</label>
        <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
          type="number" name="year" id="year" value="{{ old('year', $film->year) }}">
      </div>
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="description">Description</label>
      <textarea class="block px-4 py-2 border-base bg-white/40 font-medium tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full h-32"
        name="description" id="description">{{ old('description', $film->description) }}</textarea>
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Thumbnail</label>
      @if ($film->thumbnail)
        <img src="{{ $film->thumbnailUrl() }}" class="w-20 h-28 object-cover mb-3" alt="">
      @endif
      <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
        type="file" name="thumbnail" id="cs-edit-thumb" accept="image/*">
      <p class="mt-1 text-xs text-gray-500">Leave blank to keep current thumbnail</p>
      <div id="cs-edit-thumb-preview" class="flex flex-wrap gap-2 mt-3"></div>
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Posters</label>
      @if ($film->posters->isNotEmpty())
        <div class="flex flex-wrap gap-3 mb-4">
          @foreach ($film->posters as $poster)
            <div class="relative group">
              <img src="{{ $poster->url() }}" class="w-24 h-32 object-cover" alt="">
              <form method="POST" action="{{ route('admin.posters.destroy', $poster) }}"
                onsubmit="return confirm('Remove this poster?')" class="absolute top-1 right-1">
                @csrf @method('DELETE')
                <button type="submit"
                  class="w-6 h-6 flex items-center justify-center bg-black/70 text-white text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity hover:bg-danger">✕</button>
              </form>
            </div>
          @endforeach
        </div>
      @endif
      <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
        type="file" name="posters[]" id="cs-edit-posters" accept="image/*" multiple>
      <p class="mt-1 text-xs text-gray-500">Select multiple to add more posters</p>
      <div id="cs-edit-posters-preview" class="flex flex-wrap gap-2 mt-3"></div>
    </div>

    <div>
      <button type="submit"
        class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
        Save Details
      </button>
    </div>
  </form>
</section>

{{-- ── Trailer ── --}}
<section class="mb-14">
  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-600">
    Trailer
  </h3>

  @if ($film->trailerIsReady())
    <div class="mb-5">
      <video id="cs-trailer-preview" class="w-full max-w-xl bg-black" style="aspect-ratio:16/9;" controls preload="metadata"></video>
      <p class="mt-2 text-xs text-gray-500">{{ basename($film->trailer_path) }}</p>
    </div>
  @elseif ($film->trailer_path)
    <div class="mb-5 flex items-center gap-x-3 py-3 px-4 bg-gray-50 dark:bg-gray-800 max-w-xl">
      <svg class="h-4 w-4 fill-gray-400 shrink-0" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
      <span class="text-xs font-bold uppercase text-gray-400 tracking-widest">Trailer processing…</span>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.coming-soon.trailer', $film) }}" enctype="multipart/form-data"
    id="cs-trailer-form" class="flex flex-col gap-y-4 max-w-xl">
    @csrf

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="cs-trailer-input">
        {{ $film->trailer_path ? 'Replace Trailer' : 'Upload Trailer' }}
      </label>
      <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
        type="file" name="trailer" id="cs-trailer-input" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv">
      <p class="mt-1 text-xs text-gray-500">MP4, WebM, OGG or MKV — transcoded to HLS for streaming</p>
    </div>

    <div id="cs-tprogress-wrap" class="hidden flex flex-col gap-y-2">
      <div class="flex justify-between text-xs font-bold uppercase text-gray-500">
        <span>Uploading trailer…</span>
        <span id="cs-tprogress-label">0%</span>
      </div>
      <div class="w-full h-2 bg-gray-200 dark:bg-gray-700">
        <div id="cs-tprogress-bar" class="h-2 bg-accent transition-all duration-150" style="width:0%"></div>
      </div>
      <p class="text-xs text-gray-500">Do not close this tab until the upload completes.</p>
    </div>

    <div>
      <button type="submit" id="cs-trailer-submit"
        class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-50 disabled:cursor-not-allowed">
        Upload Trailer
      </button>
    </div>
  </form>
</section>

{{-- ── Release Video ── --}}
<section class="mb-14">
  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 pb-3 border-b border-gray-200 dark:border-gray-600">
    Release Video
  </h3>
  <p class="text-sm text-gray-500 mb-6">
    Upload the full film video to move this title out of Coming Soon and publish it immediately.
  </p>

  <form method="POST" action="{{ route('admin.coming-soon.video', $film) }}" enctype="multipart/form-data"
    id="cs-video-form" class="flex flex-col gap-y-5 max-w-xl">
    @csrf

    <label for="cs-video-input"
      class="flex flex-col items-center justify-center gap-y-3 w-full border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-accent dark:hover:border-accent transition-colors cursor-pointer py-10 px-6 text-center bg-gray-50 dark:bg-gray-800/40 group">
      <svg class="h-8 w-8 fill-gray-400 group-hover:fill-accent transition-colors" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
      <span class="text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-accent transition-colors">
        Click to select video<span class="hidden sm:inline"> or drag &amp; drop</span>
      </span>
      <span id="cs-video-filename" class="text-xs text-gray-500">MP4, WebM, OGG, MKV</span>
    </label>
    <input id="cs-video-input" type="file" name="video" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv" class="sr-only">

    <div id="cs-vprogress-wrap" class="hidden flex flex-col gap-y-1.5">
      <div class="flex justify-between text-xs font-bold uppercase text-gray-500">
        <span>Uploading…</span>
        <span id="cs-vprogress-label">0%</span>
      </div>
      <div class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
        <div id="cs-vprogress-bar" class="h-1.5 bg-accent transition-all duration-150 rounded-full" style="width:0%"></div>
      </div>
      <p class="text-xs text-gray-500">Do not close this tab until the upload completes.</p>
    </div>

    <div>
      <button type="submit" id="cs-video-submit"
        class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-3 px-6 text-sm text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-50 disabled:cursor-not-allowed">
        Upload &amp; Publish
      </button>
      <p class="mt-2 text-xs text-gray-400">The film will be published immediately after encoding completes.</p>
    </div>
  </form>
</section>

<div>
  <a href="{{ route('admin.coming-soon.index') }}"
    class="text-sm font-bold text-gray-500 hover:text-accent dark:hover:text-accent transition-colors uppercase">
    ← Back to Coming Soon
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
(function () {
  // ── Trailer preview ───────────────────────────────────────────
  var trailerVideo = document.getElementById('cs-trailer-preview');
  if (trailerVideo) {
    var hlsToken = @json($hlsToken);
    var src = @json($film->trailerIsReady() ? $film->trailerUrl() : '');
    if (src) {
      if (Hls.isSupported()) {
        var hls = new Hls({ xhrSetup: function (xhr) { xhr.setRequestHeader('Authorization', 'Bearer ' + hlsToken); } });
        hls.loadSource(src);
        hls.attachMedia(trailerVideo);
      } else {
        trailerVideo.src = src;
      }
    }
  }

  // ── Image previews ────────────────────────────────────────────
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
          var img = document.createElement('img');
          img.src = e.target.result;
          img.className = 'h-24 w-auto object-cover border border-gray-200 dark:border-gray-600';
          wrap.appendChild(img);
          container.appendChild(wrap);
        };
        reader.readAsDataURL(file);
      });
    });
  }

  attachPreview('cs-edit-thumb', 'cs-edit-thumb-preview');
  attachPreview('cs-edit-posters', 'cs-edit-posters-preview');

  // ── Trailer XHR upload ────────────────────────────────────────
  function bindXhrUpload(formId, inputName, progressWrapId, barId, labelId, submitId, submitLabel) {
    var form      = document.getElementById(formId);
    var progressW = document.getElementById(progressWrapId);
    var bar       = document.getElementById(barId);
    var label     = document.getElementById(labelId);
    var submitBtn = document.getElementById(submitId);

    if (!form || !progressW) return;

    form.addEventListener('submit', function (e) {
      var input = form.querySelector('[name="' + inputName + '"]');
      if (!input || !input.files.length) return;

      e.preventDefault();
      var formData = new FormData(form);
      var xhr = new XMLHttpRequest();

      progressW.classList.remove('hidden');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Uploading…';

      xhr.upload.addEventListener('progress', function (ev) {
        if (!ev.lengthComputable) return;
        var pct = Math.round((ev.loaded / ev.total) * 100);
        bar.style.width = pct + '%';
        label.textContent = pct + '%';
        if (pct === 100) { label.textContent = 'Saving…'; submitBtn.textContent = 'Saving…'; }
      });

      xhr.addEventListener('load', function () {
        if (xhr.status >= 200 && xhr.status < 400) {
          window.location.href = xhr.responseURL;
        } else {
          submitBtn.disabled = false;
          submitBtn.textContent = submitLabel;
          label.textContent = 'Upload failed (' + xhr.status + ') — try again.';
          bar.style.width = '0%';
        }
      });

      xhr.addEventListener('error', function () {
        submitBtn.disabled = false;
        submitBtn.textContent = submitLabel;
        label.textContent = 'Upload failed — try again.';
        bar.style.width = '0%';
      });

      xhr.open('POST', form.action);
      xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');
      xhr.send(formData);
    });
  }

  bindXhrUpload('cs-trailer-form', 'trailer', 'cs-tprogress-wrap', 'cs-tprogress-bar', 'cs-tprogress-label', 'cs-trailer-submit', 'Upload Trailer');
  bindXhrUpload('cs-video-form',   'video',   'cs-vprogress-wrap', 'cs-vprogress-bar', 'cs-vprogress-label', 'cs-video-submit',   'Upload & Publish');

  // ── Video drop zone ───────────────────────────────────────────
  var fileInput   = document.getElementById('cs-video-input');
  var fileNameEl  = document.getElementById('cs-video-filename');
  var dropZone    = document.querySelector('label[for="cs-video-input"]');

  if (fileInput && fileNameEl) {
    fileInput.addEventListener('change', function () {
      fileNameEl.textContent = fileInput.files.length ? fileInput.files[0].name : 'MP4, WebM, OGG, MKV';
    });
  }
  if (dropZone && fileInput) {
    dropZone.addEventListener('dragover', function (e) { e.preventDefault(); dropZone.classList.add('border-accent'); });
    dropZone.addEventListener('dragleave', function () { dropZone.classList.remove('border-accent'); });
    dropZone.addEventListener('drop', function (e) {
      e.preventDefault();
      dropZone.classList.remove('border-accent');
      if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        if (fileNameEl) fileNameEl.textContent = e.dataTransfer.files[0].name;
      }
    });
  }
})();
</script>
@endpush
