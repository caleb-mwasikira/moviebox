@extends('layouts.admin')

@section('admin-title', 'Add Series')

@section('admin-content')
<x-ui.page-header title="Add series" subtitle="After saving you can add seasons and episodes." />

<form method="POST" action="{{ route('admin.series.store') }}" enctype="multipart/form-data" id="upload-form" class="space-y-6 lg:grid lg:grid-cols-[14rem_minmax(0,1fr)] lg:items-start lg:gap-8 lg:space-y-0" x-data="stepper(2, {{ $errors->hasAny(['trailer', 'thumbnail', 'posters', 'posters.*']) ? 1 : 0 }}, false)" @keydown.enter="if (!last && $event.target.tagName === 'INPUT') { $event.preventDefault(); next(); }">
  @csrf

  <x-admin.stepper :steps="['Details', 'Artwork & trailer']" />

  <div class="min-w-0 space-y-6">

  <div class="card card-pad space-y-5" x-show="step === 0">
    <h2 class="card-title">Details</h2>
    <div class="grid gap-5 sm:grid-cols-2">
      <div class="sm:col-span-2"><x-ui.field name="title" label="Title" :value="old('title')" /></div>
      <x-ui.select name="genre" label="Genre" placeholder="— Select genre —" :selected="old('genre')"
        :options="['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy', 'Horror', 'Musical', 'Mystery', 'Romance', 'Science Fiction', 'Thriller', 'Western']" />
      <x-ui.field name="year" type="number" label="Year" :value="old('year')" placeholder="e.g. 2024" />
      <x-ui.select name="status" label="Status" :options="['hidden' => 'Hidden', 'published' => 'Published']" />
    </div>
    <x-ui.textarea name="description" label="Description" :value="old('description')" rows="5" />
  </div>

  <div class="card card-pad space-y-5" x-show="step === 1" x-cloak>
    <h2 class="card-title">Artwork &amp; trailer</h2>
    <div>
      <x-ui.file name="thumbnail" label="Thumbnail image" accept="image/*" />
      <div id="thumbnail-preview" class="mt-3 flex flex-wrap gap-3"></div>
    </div>
    <div>
      <x-ui.file name="posters[]" id="posters-input" label="Posters" optional hint="Select multiple images" accept="image/*" multiple />
      <div id="posters-preview" class="mt-3 flex flex-wrap gap-3"></div>
    </div>
    <x-ui.file name="trailer" label="Trailer" optional hint="Short preview clip shown on the series page" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv" />
    <p class="alert alert-info"><x-ui.icon name="info" class="mt-0.5 size-4 shrink-0" />Episode videos are uploaded per episode after creating the series.</p>
    <x-admin.upload-progress />
  </div>

  <div class="flex flex-wrap items-center gap-3">
    <button type="button" @click="prev()" x-show="step > 0" x-cloak class="btn btn-secondary btn-lg"><x-ui.icon name="arrow-left" class="size-4" /> Back</button>
    <button type="button" @click="next()" x-show="!last" class="btn btn-primary btn-lg">Next <x-ui.icon name="arrow-right" class="size-4" /></button>
    <button type="submit" id="upload-submit" x-show="last" x-cloak class="btn btn-primary btn-lg disabled:opacity-50">Create series</button>
    <a href="{{ route('admin.series.index') }}" class="btn btn-ghost btn-lg ml-auto">Cancel</a>
  </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
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

  attachPreview('thumbnail', 'thumbnail-preview');
  attachPreview('posters-input', 'posters-preview');

  // ── XHR upload for trailer ────────────────────────────────────
  var form         = document.getElementById('upload-form');
  var progressWrap = document.getElementById('upload-progress-wrap');
  var progressBar  = document.getElementById('upload-progress-bar');
  var progressLabel= document.getElementById('upload-progress-label');
  var submitBtn    = document.getElementById('upload-submit');

  if (form) {
    form.addEventListener('submit', function (e) {
      var trailerInput = document.getElementById('trailer');
      if (!trailerInput || !trailerInput.files.length) return; // no trailer — regular submit

      e.preventDefault();
      var formData = new FormData(form);
      var xhr = new XMLHttpRequest();

      progressWrap.classList.remove('hidden');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Uploading…';

      xhr.upload.addEventListener('progress', function (ev) {
        if (!ev.lengthComputable) return;
        var pct = Math.round((ev.loaded / ev.total) * 100);
        progressBar.style.width = pct + '%';
        progressLabel.textContent = pct + '%';
        if (pct === 100) { progressLabel.textContent = 'Saving…'; submitBtn.textContent = 'Saving…'; }
      });

      xhr.addEventListener('load', function () {
        if (xhr.status >= 200 && xhr.status < 400) {
          window.location.href = xhr.responseURL;
        } else {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Create Series';
          progressLabel.textContent = 'Upload failed (' + xhr.status + ') — try again.';
          progressBar.style.width = '0%';
        }
      });

      xhr.addEventListener('error', function () {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create Series';
        progressLabel.textContent = 'Upload failed — try again.';
        progressBar.style.width = '0%';
      });

      xhr.open('POST', form.action);
      xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');
      xhr.send(formData);
    });
  }
})();
</script>
@endpush
