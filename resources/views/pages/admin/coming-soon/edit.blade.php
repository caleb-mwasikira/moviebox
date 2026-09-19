@extends('layouts.admin')

@section('admin-title', 'Edit Coming Soon')

@section('admin-content')
@php $videoAccept = 'video/mp4,video/webm,video/ogg,video/x-matroska,.mkv'; @endphp

<x-ui.page-header :title="$film->title" subtitle="Manage details, trailer, and release video.">
  <x-slot:actions>
    <form action="{{ route('admin.coming-soon.toggle-featured', $film) }}" method="POST">
      @csrf @method('PATCH')
      <button type="submit" class="btn btn-sm {{ $film->featured ? 'btn-outline !border-brand !text-brand' : 'btn-secondary' }}">{{ $film->featured ? '★ Featured' : '☆ Mark as featured' }}</button>
    </form>
    <a href="{{ route('admin.coming-soon.index') }}" class="btn btn-secondary btn-sm"><x-ui.icon name="arrow-left" class="size-3.5" /> Back</a>
  </x-slot:actions>
</x-ui.page-header>

@if (session('status') === 'trailer-processing')
  <div class="alert alert-success"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />Trailer uploaded and is being processed for streaming.</div>
@endif

{{-- Metadata --}}
<form method="POST" action="{{ route('admin.coming-soon.update', $film) }}" enctype="multipart/form-data" class="card">
  @csrf @method('PUT')
  <div class="card-header"><h2 class="card-title">Film details</h2></div>
  <div class="space-y-5 p-5 sm:p-6">
    <div class="grid gap-5 sm:grid-cols-2">
      <div class="sm:col-span-2"><x-ui.field name="title" label="Title" :value="old('title', $film->title)" /></div>
      <x-ui.select name="genre" label="Genre" placeholder="— Select genre —" :selected="old('genre', $film->genre)"
        :options="['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy', 'Horror', 'Musical', 'Mystery', 'Romance', 'Science Fiction', 'Thriller', 'Western']" />
      <x-ui.field name="year" type="number" label="Release year" :value="old('year', $film->year)" />
    </div>
    <x-ui.textarea name="description" label="Description" :value="old('description', $film->description)" rows="5" />

    <div>
      <label class="label">Thumbnail</label>
      @if ($film->thumbnail)<img src="{{ $film->thumbnailUrl() }}" alt="" class="mb-3 h-32 w-auto rounded-lg border border-line object-cover">@endif
      <x-ui.file name="thumbnail" id="cs-edit-thumb" accept="image/*" hint="Leave blank to keep current thumbnail" />
      <div id="cs-edit-thumb-preview" class="mt-3 flex flex-wrap gap-3"></div>
    </div>

    <div>
      <label class="label">Posters</label>
      @if ($film->posters->isNotEmpty())
        <div class="mb-3 flex flex-wrap gap-3">
          @foreach ($film->posters as $poster)
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
      <x-ui.file name="posters[]" id="cs-edit-posters" accept="image/*" multiple hint="Select multiple to add more posters" />
      <div id="cs-edit-posters-preview" class="mt-3 flex flex-wrap gap-3"></div>
    </div>

    <button type="submit" class="btn btn-primary">Save details</button>
  </div>
</form>

{{-- Trailer --}}
<section class="card">
  <div class="card-header"><h2 class="card-title">Trailer</h2></div>
  <div class="space-y-5 p-5 sm:p-6">
    @if ($film->trailerIsReady())
      <div>
        <video id="cs-trailer-preview" controls preload="metadata" class="aspect-video w-full max-w-xl rounded-xl bg-black"></video>
        <p class="mt-2 text-xs text-muted">{{ basename($film->trailer_path) }}</p>
      </div>
    @elseif ($film->trailer_path)
      <span class="badge badge-warning"><span class="size-1.5 animate-pulse rounded-full bg-amber-500"></span> Trailer processing…</span>
    @endif

    <form method="POST" action="{{ route('admin.coming-soon.trailer', $film) }}" enctype="multipart/form-data" id="cs-trailer-form" class="max-w-xl space-y-4">
      @csrf
      <x-ui.file name="trailer" id="cs-trailer-input" :label="$film->trailer_path ? 'Replace trailer' : 'Upload trailer'" hint="MP4, WebM, OGG or MKV — transcoded to HLS for streaming" :accept="$videoAccept" />
      <div id="cs-tprogress-wrap" class="panel hidden space-y-2 p-4">
        <div class="flex justify-between text-xs font-semibold uppercase tracking-wider text-muted"><span>Uploading trailer…</span><span id="cs-tprogress-label">0%</span></div>
        <div class="progress"><span id="cs-tprogress-bar" class="transition-all" style="width:0%"></span></div>
        <p class="text-xs text-muted">Do not close this tab until the upload completes.</p>
      </div>
      <button type="submit" id="cs-trailer-submit" class="btn btn-primary">Upload trailer</button>
    </form>
  </div>
</section>

{{-- Release video --}}
<section class="card">
  <div class="card-header"><div><h2 class="card-title">Release video</h2><p class="mt-0.5 text-sm text-muted">Upload the full film video to move this title out of Coming Soon and publish it immediately.</p></div></div>

  <div class="flex gap-1 border-b border-line px-5 sm:px-6">
    <button type="button" id="cs-tab-btn-movie" data-tab="movie" onclick="switchReleaseTab('movie')" class="-mb-px border-b-2 border-brand px-4 py-3 text-sm font-semibold text-fg transition">Movie</button>
    <button type="button" id="cs-tab-btn-series" data-tab="series" onclick="switchReleaseTab('series')" class="-mb-px border-b-2 border-transparent px-4 py-3 text-sm font-semibold text-muted transition hover:text-fg">Series — S01E01</button>
  </div>

  {{-- Movie tab --}}
  <div id="cs-tab-movie" class="p-5 sm:p-6">
    <form method="POST" action="{{ route('admin.coming-soon.video', $film) }}" enctype="multipart/form-data" id="cs-video-form" class="max-w-xl space-y-4">
      @csrf
      <label for="cs-video-input" class="flex cursor-pointer flex-col items-center justify-center gap-1 rounded-2xl border-2 border-dashed border-line-strong px-6 py-10 text-center transition hover:border-brand">
        <x-ui.icon name="upload" class="size-7 text-muted" />
        <span class="text-sm font-semibold text-fg">Click to select video<span class="font-normal text-muted"> or drag &amp; drop</span></span>
        <span id="cs-video-filename" class="text-xs text-muted">MP4, WebM, OGG, MKV</span>
      </label>
      <input id="cs-video-input" type="file" name="video" accept="{{ $videoAccept }}" class="sr-only">
      <div id="cs-vprogress-wrap" class="panel hidden space-y-2 p-4">
        <div class="flex justify-between text-xs font-semibold uppercase tracking-wider text-muted"><span>Uploading…</span><span id="cs-vprogress-label">0%</span></div>
        <div class="progress"><span id="cs-vprogress-bar" class="transition-all" style="width:0%"></span></div>
        <p class="text-xs text-muted">Do not close this tab until the upload completes.</p>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <button type="submit" id="cs-video-submit" class="btn btn-primary">Upload &amp; publish</button>
        <p class="text-xs text-muted">The film will be published immediately after encoding completes.</p>
      </div>
    </form>
  </div>

  {{-- Series tab --}}
  <div id="cs-tab-series" class="p-5 sm:p-6" style="display:none">
    <p class="mb-4 text-sm text-muted">Creates <strong class="text-fg">Season 1</strong> with Episode 1. Additional seasons and episodes can be added from the series page afterward.</p>
    <form method="POST" action="{{ route('admin.coming-soon.promote-series', $film) }}" enctype="multipart/form-data" id="cs-series-form" class="max-w-xl space-y-4">
      @csrf
      <div><label class="label" for="cs-episode-title">Episode 1 title</label><input type="text" name="episode_title" id="cs-episode-title" required placeholder="e.g. Pilot" class="input"></div>
      <label for="cs-series-video-input" class="flex cursor-pointer flex-col items-center justify-center gap-1 rounded-2xl border-2 border-dashed border-line-strong px-6 py-10 text-center transition hover:border-brand">
        <x-ui.icon name="upload" class="size-7 text-muted" />
        <span class="text-sm font-semibold text-fg">Click to select episode video<span class="font-normal text-muted"> or drag &amp; drop</span></span>
        <span id="cs-series-video-filename" class="text-xs text-muted">MP4, WebM, OGG, MKV</span>
      </label>
      <input id="cs-series-video-input" type="file" name="video" accept="{{ $videoAccept }}" class="sr-only">
      <div id="cs-sprogress-wrap" class="panel hidden space-y-2 p-4">
        <div class="flex justify-between text-xs font-semibold uppercase tracking-wider text-muted"><span>Uploading…</span><span id="cs-sprogress-label">0%</span></div>
        <div class="progress"><span id="cs-sprogress-bar" class="transition-all" style="width:0%"></span></div>
        <p class="text-xs text-muted">Do not close this tab until the upload completes.</p>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <button type="submit" id="cs-series-submit" class="btn btn-primary">Create series &amp; go live</button>
        <p class="text-xs text-muted">Series will be published immediately after encoding completes.</p>
      </div>
    </form>
  </div>
</section>

<script>
function switchReleaseTab(tab) {
  var isMovie = tab === 'movie';
  document.getElementById('cs-tab-movie').style.display  = isMovie ? '' : 'none';
  document.getElementById('cs-tab-series').style.display = isMovie ? 'none' : '';
  var activeClass   = 'border-brand text-fg';
  var inactiveClass = 'border-transparent text-muted hover:text-fg';
  var base = '-mb-px border-b-2 px-4 py-3 text-sm font-semibold transition ';
  document.getElementById('cs-tab-btn-movie').className  = base + (isMovie  ? activeClass : inactiveClass);
  document.getElementById('cs-tab-btn-series').className = base + (!isMovie ? activeClass : inactiveClass);
}
</script>
@endsection

@push('scripts')
@php
  $uid      = auth()->id() ?? 0;
  $exp      = time() + 7200;
  $sig      = hash_hmac('sha256', $uid . ':' . $exp, config('services.streaming.secret'));
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
          img.className = 'h-24 w-auto rounded-lg border border-line object-cover';
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
  bindXhrUpload('cs-series-form',  'video',   'cs-sprogress-wrap', 'cs-sprogress-bar', 'cs-sprogress-label', 'cs-series-submit',  'Create Series & Go Live');

  // ── Drop zone helper ──────────────────────────────────────────
  function bindDropZone(inputId, fileNameId) {
    var input    = document.getElementById(inputId);
    var nameEl   = document.getElementById(fileNameId);
    var zone     = document.querySelector('label[for="' + inputId + '"]');
    var fallback = 'MP4, WebM, OGG, MKV';

    if (input && nameEl) {
      input.addEventListener('change', function () {
        nameEl.textContent = input.files.length ? input.files[0].name : fallback;
      });
    }
    if (zone && input) {
      zone.addEventListener('dragover',  function (e) { e.preventDefault(); zone.classList.add('border-brand', 'bg-brand-soft'); });
      zone.addEventListener('dragleave', function ()  { zone.classList.remove('border-brand', 'bg-brand-soft'); });
      zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('border-brand', 'bg-brand-soft');
        if (e.dataTransfer.files.length) {
          input.files = e.dataTransfer.files;
          if (nameEl) nameEl.textContent = e.dataTransfer.files[0].name;
        }
      });
    }
  }

  bindDropZone('cs-video-input',        'cs-video-filename');
  bindDropZone('cs-series-video-input', 'cs-series-video-filename');
})();
</script>
@endpush
