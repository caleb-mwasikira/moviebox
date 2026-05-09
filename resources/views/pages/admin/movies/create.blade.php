@extends('layouts.admin')

@section('admin-title', 'Upload Movie')

@section('admin-content')
<div class="mb-10">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Upload Movie</h2>
</div>

<form method="POST" action="{{ route('admin.movies.store') }}" enctype="multipart/form-data"
  id="upload-form" class="flex flex-col gap-y-6 max-w-2xl">
  @csrf

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div class="sm:col-span-2">
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="title">Title</label>
      <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('title') border-danger @enderror"
        type="text" name="title" id="title" value="{{ old('title') }}">
      @error('title')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="genre">Genre</label>
      <select class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
        name="genre" id="genre">
        <option value="">— Select genre —</option>
        @foreach (['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy', 'Horror', 'Musical', 'Mystery', 'Romance', 'Science Fiction', 'Thriller', 'Western'] as $genre)
          <option value="{{ $genre }}" {{ old('genre') === $genre ? 'selected' : '' }}>{{ $genre }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="year">Year</label>
      <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
        type="number" name="year" id="year" value="{{ old('year') }}" placeholder="e.g. 2024">
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="status">Status</label>
      <select class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
        name="status" id="status">
        <option value="hidden" {{ old('status', 'hidden') === 'hidden' ? 'selected' : '' }}>Hidden</option>
        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
      </select>
    </div>
  </div>

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="description">Description</label>
    <textarea class="block px-4 py-2 border-base bg-white/40 font-medium tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full h-32 @error('description') border-danger @enderror"
      name="description" id="description">{{ old('description') }}</textarea>
    @error('description')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="thumbnail">Thumbnail Image</label>
    <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
      type="file" name="thumbnail" id="thumbnail" accept="image/*">
    @error('thumbnail')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
    <div id="thumbnail-preview" class="flex flex-wrap gap-2 mt-3"></div>
  </div>

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Posters <span class="normal-case font-normal text-gray-400">(optional — select multiple)</span></label>
    <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
      type="file" name="posters[]" id="posters-input" accept="image/*" multiple>
    @error('posters.*')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
    <div id="posters-preview" class="flex flex-wrap gap-2 mt-3"></div>
  </div>

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="video">Video File <span class="text-danger">*</span></label>
    <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer @error('video') border-danger @enderror"
      type="file" name="video" id="video" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv">
    <p class="mt-1 text-xs text-gray-500">MP4, WebM, OGG or MKV</p>
    @error('video')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="trailer">Trailer <span class="normal-case font-normal text-gray-400">(optional)</span></label>
    <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
      type="file" name="trailer" id="trailer" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv">
    <p class="mt-1 text-xs text-gray-500">Short preview clip shown on the movie page</p>
    @error('trailer')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
  </div>

  {{-- Upload progress --}}
  <div id="upload-progress-wrap" class="hidden flex flex-col gap-y-2">
    <div class="flex justify-between text-xs font-bold uppercase text-gray-500">
      <span>Uploading to server…</span>
      <span id="upload-progress-label">0%</span>
    </div>
    <div class="w-full h-2 bg-gray-200 dark:bg-gray-700">
      <div id="upload-progress-bar" class="h-2 bg-accent transition-all duration-150" style="width:0%"></div>
    </div>
    <p class="text-xs text-gray-500">Large files may take a few minutes. Do not close this tab.</p>
  </div>

  <div class="flex gap-x-4 pt-2">
    <button type="submit" id="upload-submit"
      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-50 disabled:cursor-not-allowed">
      Upload Movie
    </button>
    <a href="{{ route('admin.movies.index') }}"
      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 hover:border-accent">
      Cancel
    </a>
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
