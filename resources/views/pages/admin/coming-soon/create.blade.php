@extends('layouts.admin')

@section('admin-title', 'New Coming Soon Film')

@section('admin-content')
<x-ui.page-header title="New coming soon" subtitle="Add an upcoming film announcement with optional trailer." />

<form method="POST" action="{{ route('admin.coming-soon.store') }}" enctype="multipart/form-data" id="cs-form" class="space-y-6">
  @csrf

  <div class="card card-pad space-y-5">
    <h2 class="card-title">Details</h2>
    <div class="grid gap-5 sm:grid-cols-2">
      <div class="sm:col-span-2"><x-ui.field name="title" label="Title" :value="old('title')" placeholder="Film title" /></div>
      <x-admin.genre-select :selected="old('genre')" />
      <x-ui.field name="year" type="number" label="Release year" :value="old('year')" :placeholder="date('Y')" />
    </div>
    <x-ui.textarea name="description" label="Description" :value="old('description')" rows="5" placeholder="Brief description of the film…" />
  </div>

  <div class="card card-pad space-y-5">
    <h2 class="card-title">Artwork &amp; trailer</h2>
    <div>
      <x-ui.file name="thumbnail" id="cs-thumbnail" label="Thumbnail" accept="image/*" />
      <div id="cs-thumbnail-preview" class="mt-3 flex flex-wrap gap-3"></div>
    </div>
    <div>
      <x-ui.file name="posters[]" id="cs-posters" label="Posters" hint="Select multiple to add posters" accept="image/*" multiple />
      <div id="cs-posters-preview" class="mt-3 flex flex-wrap gap-3"></div>
    </div>
    <div>
      <x-ui.file name="trailer" id="cs-trailer" label="Trailer" optional hint="MP4, WebM, OGG or MKV — will be transcoded for streaming" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv" />
      <div id="cs-trailer-progress" class="panel mt-3 hidden space-y-2 p-4">
        <div class="flex justify-between text-xs font-semibold uppercase tracking-wider text-muted"><span>Uploading trailer…</span><span id="cs-trailer-label">0%</span></div>
        <div class="progress"><span id="cs-trailer-bar" class="transition-all" style="width:0%"></span></div>
        <p class="text-xs text-muted">Large files may take a few minutes. Do not close this tab.</p>
      </div>
    </div>
  </div>

  <div class="flex gap-3">
    <button type="submit" id="cs-submit" class="btn btn-primary btn-lg">Add film</button>
    <a href="{{ route('admin.coming-soon.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
  </div>
</form>
@endsection

@push('scripts')
<script>
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

  attachPreview('cs-thumbnail', 'cs-thumbnail-preview');
  attachPreview('cs-posters', 'cs-posters-preview');

  var form      = document.getElementById('cs-form');
  var trailerIn = document.getElementById('cs-trailer');
  var progressW = document.getElementById('cs-trailer-progress');
  var bar       = document.getElementById('cs-trailer-bar');
  var label     = document.getElementById('cs-trailer-label');
  var submitBtn = document.getElementById('cs-submit');

  if (form && trailerIn) {
    form.addEventListener('submit', function (e) {
      if (!trailerIn.files.length) return;

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
          submitBtn.textContent = 'Add Film';
          label.textContent = 'Upload failed (' + xhr.status + ') — try again.';
          bar.style.width = '0%';
        }
      });

      xhr.addEventListener('error', function () {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Add Film';
        label.textContent = 'Upload failed — try again.';
        bar.style.width = '0%';
      });

      xhr.open('POST', form.action);
      xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');
      xhr.send(formData);
    });
  }
})();
</script>
@endpush
