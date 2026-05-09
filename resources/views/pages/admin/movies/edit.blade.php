@extends('layouts.admin')

@section('admin-title', 'Edit Movie')

@section('admin-content')
<div class="mb-10">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Edit Movie</h2>
  <p class="mt-1 text-gray-500 dark:text-gray text-sm">{{ $movie->title }}</p>
</div>

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
      type="file" name="thumbnail" accept="image/*">
    <p class="mt-1 text-xs text-gray-500">Leave blank to keep current thumbnail</p>
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
      type="file" name="posters[]" accept="image/*" multiple>
    <p class="mt-1 text-xs text-gray-500">Select multiple to add more posters</p>
    @error('posters.*')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Video File</label>
    <p class="text-xs text-gray-500 mb-2">Current: <span class="font-bold text-gray-900 dark:text-white">{{ basename($movie->video_path) }}</span></p>
    <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
      type="file" name="video" accept="video/mp4,video/webm,video/ogg">
    <p class="mt-1 text-xs text-gray-500">Leave blank to keep current video</p>
  </div>

  {{-- Upload progress (only visible when a new video is being uploaded) --}}
  <div id="upload-progress-wrap" class="hidden flex flex-col gap-y-2">
    <div class="flex justify-between text-xs font-bold uppercase text-gray-500">
      <span>Uploading video…</span>
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
      Save Changes
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
  var form = document.getElementById('upload-form');
  var progressWrap = document.getElementById('upload-progress-wrap');
  var progressBar = document.getElementById('upload-progress-bar');
  var progressLabel = document.getElementById('upload-progress-label');
  var submitBtn = document.getElementById('upload-submit');

  if (!form || !progressWrap) return;

  form.addEventListener('submit', function (e) {
    var videoInput = form.querySelector('[name="video"]');
    if (!videoInput || !videoInput.files.length) return; // no new video — regular submit

    e.preventDefault();

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
      submitBtn.textContent = 'Save Changes';
      progressLabel.textContent = 'Upload failed — try again.';
      progressBar.style.width = '0%';
    });

    xhr.open('POST', form.action);
    xhr.send(formData);
  });
})();
</script>
@endpush
