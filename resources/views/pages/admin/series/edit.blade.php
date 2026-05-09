@extends('layouts.admin')

@section('admin-title', 'Manage Series')

@section('admin-content')
<div class="mb-10">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">{{ $series->title }}</h2>
  <p class="mt-1 text-gray-500 dark:text-gray text-sm">Manage metadata, seasons, and episodes.</p>
</div>

{{-- Metadata --}}
<section class="mb-14">
  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-600">
    Series Metadata
  </h3>

  <form method="POST" action="{{ route('admin.series.update', $series) }}" enctype="multipart/form-data"
    class="flex flex-col gap-y-6 max-w-2xl">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
      <div class="sm:col-span-2">
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="title">Title</label>
        <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
          type="text" name="title" id="title" value="{{ old('title', $series->title) }}">
      </div>
      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="genre">Genre</label>
        <select class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
          name="genre" id="genre">
          <option value="">— Select genre —</option>
          @foreach (['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Fantasy', 'Horror', 'Musical', 'Mystery', 'Romance', 'Science Fiction', 'Thriller', 'Western'] as $genre)
            <option value="{{ $genre }}" {{ old('genre', $series->genre) === $genre ? 'selected' : '' }}>{{ $genre }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="year">Year</label>
        <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
          type="number" name="year" id="year" value="{{ old('year', $series->year) }}">
      </div>
      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="status">Status</label>
        <select class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:bg-caret-white dark:focus:bg-gray-900"
          name="status" id="status">
          <option value="hidden" {{ old('status', $series->status) === 'hidden' ? 'selected' : '' }}>Hidden</option>
          <option value="published" {{ old('status', $series->status) === 'published' ? 'selected' : '' }}>Published</option>
        </select>
      </div>
    </div>
    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="description">Description</label>
      <textarea class="block px-4 py-2 border-base bg-white/40 font-medium tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full h-28"
        name="description" id="description">{{ old('description', $series->description) }}</textarea>
    </div>
    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Thumbnail</label>
      @if ($series->thumbnail)
        <img src="{{ $series->thumbnailUrl() }}" class="w-20 h-28 object-cover mb-3" alt="">
      @endif
      <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
        type="file" name="thumbnail" accept="image/*">
    </div>
    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Posters</label>
      @if ($series->posters->isNotEmpty())
        <div class="flex flex-wrap gap-3 mb-4">
          @foreach ($series->posters as $poster)
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
    </div>
    <div>
      <button type="submit"
        class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
        Save Metadata
      </button>
    </div>
  </form>
</section>

{{-- Seasons --}}
<section class="mb-14">
  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-600">
    Seasons &amp; Episodes
  </h3>

  @forelse ($series->seasons as $season)
    <div class="mb-10 border border-gray-200 dark:border-gray-600 p-6">
      <div class="flex items-center justify-between mb-5">
        <h4 class="font-bold text-gray-900 dark:text-white text-lg">{{ $season->label() }}
          @if ($season->title) <span class="text-gray-500 font-normal text-base">— {{ $season->title }}</span>@endif
        </h4>
        <form method="POST" action="{{ route('admin.seasons.destroy', $season) }}"
          onsubmit="return confirm('Delete {{ $season->label() }}? All its episodes will be removed.')">
          @csrf @method('DELETE')
          <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-danger transition-colors">Delete Season</button>
        </form>
      </div>

      {{-- Episode list --}}
      @if ($season->episodes->isNotEmpty())
        <table class="w-full text-sm mb-6">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
              <th class="pb-2 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">#</th>
              <th class="pb-2 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Title</th>
              <th class="pb-2 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Duration</th>
              <th class="pb-2 font-bold uppercase text-xs text-gray-500 tracking-widest"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach ($season->episodes as $episode)
              <tr>
                <td class="py-2 pr-4 text-gray-500">{{ str_pad($episode->episode_number, 2, '0', STR_PAD_LEFT) }}</td>
                <td class="py-2 pr-4 font-bold text-gray-900 dark:text-white">{{ $episode->title }}</td>
                <td class="py-2 pr-4 text-gray-500">{{ $episode->duration ? $episode->duration . ' min' : '—' }}</td>
                <td class="py-2 text-right">
                  <form method="POST" action="{{ route('admin.episodes.destroy', $episode) }}"
                    onsubmit="return confirm('Delete episode {{ $episode->episode_number }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-danger transition-colors">Delete</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif

      {{-- Add episode --}}
      <details class="group">
        <summary class="cursor-pointer text-xs font-bold uppercase text-accent hover:text-accent/70 transition-colors list-none">
          + Add Episode
        </summary>
        <form method="POST" action="{{ route('admin.episodes.store', $season) }}" enctype="multipart/form-data"
          data-upload-form="episode" class="mt-5 flex flex-col gap-y-4 max-w-xl">
          @csrf
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Episode #</label>
              <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full"
                type="number" name="episode_number" min="1" value="{{ $season->episodes->max('episode_number') + 1 }}">
            </div>
            <div>
              <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Duration (min)</label>
              <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full"
                type="number" name="duration" min="1">
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Episode Title</label>
              <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full"
                type="text" name="title">
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Video File <span class="text-danger">*</span></label>
              <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
                type="file" name="video" accept="video/mp4,video/webm,video/ogg">
            </div>
          </div>

          {{-- Upload progress --}}
          <div data-progress-wrap class="hidden flex flex-col gap-y-1.5">
            <div class="flex justify-between text-xs font-bold uppercase text-gray-500">
              <span>Uploading…</span>
              <span data-progress-label>0%</span>
            </div>
            <div class="w-full h-2 bg-gray-200 dark:bg-gray-700">
              <div data-progress-bar class="h-2 bg-accent transition-all duration-150" style="width:0%"></div>
            </div>
            <p class="text-xs text-gray-500">Do not close this tab until the upload completes.</p>
          </div>

          <div>
            <button type="submit" data-upload-submit
              class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-3 px-6 text-sm text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-50 disabled:cursor-not-allowed">
              Upload Episode
            </button>
          </div>
        </form>
      </details>
    </div>
  @empty
    <p class="text-gray-500 dark:text-gray mb-8">No seasons yet. Add the first season below.</p>
  @endforelse

  {{-- Add season --}}
  <form method="POST" action="{{ route('admin.seasons.store', $series) }}"
    class="flex flex-wrap items-end gap-4 max-w-xl border border-dashed border-gray-300 dark:border-gray-600 p-5">
    @csrf
    <div class="flex-1 min-w-28">
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Season Number</label>
      <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full"
        type="number" name="season_number" min="1" value="{{ $nextSeasonNumber }}">
    </div>
    <div class="flex-[2]">
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Season Title <span class="normal-case text-gray-400">(optional)</span></label>
      <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full"
        type="text" name="title" placeholder="e.g. The Beginning">
    </div>
    <div>
      <button type="submit"
        class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center py-[14px] px-6 text-sm text-gray-900 border border-accent hover:bg-accent/10">
        + Add Season
      </button>
    </div>
  </form>
</section>

<div>
  <a href="{{ route('admin.series.index') }}"
    class="text-sm font-bold text-gray-500 hover:text-accent dark:hover:text-accent transition-colors uppercase">
    ← Back to Series
  </a>
</div>
@endsection

@push('scripts')
<script>
(function () {
  document.querySelectorAll('[data-upload-form="episode"]').forEach(function (form) {
    var progressWrap = form.querySelector('[data-progress-wrap]');
    var progressBar  = form.querySelector('[data-progress-bar]');
    var progressLabel = form.querySelector('[data-progress-label]');
    var submitBtn    = form.querySelector('[data-upload-submit]');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var videoInput = form.querySelector('[name="video"]');
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
        submitBtn.textContent = 'Upload Episode';
        progressLabel.textContent = 'Upload failed — try again.';
        progressBar.style.width = '0%';
      });

      xhr.open('POST', form.action);
      xhr.send(formData);
    });
  });
})();
</script>
@endpush
