@extends('layouts.admin')

@section('admin-title', 'Upload Movie')

@section('admin-content')
<x-ui.page-header title="Upload movie" subtitle="Add a new title to the catalogue." />

<form method="POST" action="{{ route('admin.movies.store') }}" enctype="multipart/form-data" id="upload-form" class="space-y-6">
  @csrf

  <div class="card card-pad space-y-5">
    <h2 class="card-title">Details</h2>
    <div class="grid gap-5 sm:grid-cols-2">
      <div class="sm:col-span-2"><x-ui.field name="title" label="Title" :value="old('title')" /></div>
      <x-ui.select name="genre" label="Genre" placeholder="— Select genre —" :selected="old('genre')"
        :options="['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy', 'Horror', 'Musical', 'Mystery', 'Romance', 'Science Fiction', 'Thriller', 'Western']" />
      <x-ui.field name="year" type="number" label="Year" :value="old('year')" placeholder="e.g. 2024" />
      <x-ui.select name="status" label="Status" :selected="old('status', 'hidden')" :options="['hidden' => 'Hidden', 'published' => 'Published']" />
      <div class="flex items-end pb-2.5"><x-ui.check name="is_free" label="Free to watch" hint="(no login required)" :checked="old('is_free')" /></div>
    </div>
    <x-ui.textarea name="description" label="Description" :value="old('description')" rows="5" />
  </div>

  <div class="card card-pad space-y-5">
    <h2 class="card-title">Artwork</h2>
    <div>
      <x-ui.file name="thumbnail" label="Thumbnail image" accept="image/*" />
      <div id="thumbnail-preview" class="mt-3 flex flex-wrap gap-3"></div>
    </div>
    <div>
      <x-ui.file name="posters[]" id="posters-input" label="Posters" optional hint="Select multiple images" accept="image/*" multiple />
      <div id="posters-preview" class="mt-3 flex flex-wrap gap-3"></div>
    </div>
  </div>

  <div class="card card-pad space-y-5">
    <h2 class="card-title">Media</h2>
    <x-ui.file name="video" label="Video file" required hint="MP4, WebM, OGG or MKV" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv" />
    <x-ui.file name="trailer" label="Trailer" optional hint="Short preview clip shown on the movie page" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv" />
    <x-admin.upload-progress />
  </div>

  <div class="flex gap-3">
    <button type="submit" id="upload-submit" class="btn btn-primary btn-lg disabled:opacity-50">Upload movie</button>
    <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
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
          img.className = 'h-24 w-auto object-cover rounded-lg border border-line';
          var name = document.createElement('p');
          name.className = 'text-[10px] text-muted mt-1 truncate max-w-[96px]';
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

  // ── Upload progress via XHR ───────────────────────────────────
  var form = document.getElementById('upload-form');
  var progressWrap = document.getElementById('upload-progress-wrap');
  var progressBar = document.getElementById('upload-progress-bar');
  var progressLabel = document.getElementById('upload-progress-label');
  var submitBtn = document.getElementById('upload-submit');

  if (!form || !progressWrap) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    var videoInput = document.getElementById('video');
    if (!videoInput || !videoInput.files.length) {
      form.submit();
      return;
    }

    var formData = new FormData(form);
    var xhr = new XMLHttpRequest();

    progressWrap.classList.remove('hidden');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Uploading…';

    xhr.upload.addEventListener('progress', function (e) {
      if (!e.lengthComputable) return;
      var pct = Math.round((e.loaded / e.total) * 100);
      progressBar.style.width = pct + '%';
      progressLabel.textContent = pct + '%';
      if (pct === 100) {
        progressLabel.textContent = 'Saving…';
        submitBtn.textContent = 'Saving…';
      }
    });

    xhr.addEventListener('load', function () {
      window.location.href = xhr.responseURL;
    });

    xhr.addEventListener('error', function () {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Upload Movie';
      progressLabel.textContent = 'Upload failed — try again.';
      progressBar.style.width = '0%';
    });

    xhr.open('POST', form.action);
    xhr.send(formData);
  });
})();
</script>
@endpush
