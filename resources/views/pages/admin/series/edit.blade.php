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
    id="series-meta-form" class="flex flex-col gap-y-6 max-w-2xl">
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
        type="file" name="thumbnail" id="series-thumbnail" accept="image/*">
      <div id="series-thumbnail-preview" class="flex flex-wrap gap-2 mt-3"></div>
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
        type="file" name="posters[]" id="series-posters-input" accept="image/*" multiple>
      <p class="mt-1 text-xs text-gray-500">Select multiple to add more posters</p>
      <div id="series-posters-preview" class="flex flex-wrap gap-2 mt-3"></div>
    </div>
    <div>
      <button type="submit"
        class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
        Save Metadata
      </button>
    </div>
  </form>
</section>

{{-- Trailer --}}
<section class="mb-14">
  <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-600">
    Trailer
  </h3>

  @if ($series->trailerIsReady())
    <div class="mb-5">
      <video id="admin-trailer-video" class="w-full max-w-xl bg-black" style="aspect-ratio:16/9;" controls preload="metadata"></video>
      <p class="mt-2 text-xs text-gray-500">{{ basename($series->trailer_path) }}</p>
    </div>
  @elseif ($series->trailer_path)
    <div class="mb-5 flex items-center gap-x-3 py-3 px-4 bg-gray-50 dark:bg-gray-800 max-w-xl">
      <svg class="h-4 w-4 fill-gray-400 shrink-0" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
      <span class="text-xs font-bold uppercase text-gray-400 tracking-widest">Trailer processing…</span>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.series.trailer', $series) }}" enctype="multipart/form-data"
    id="trailer-upload-form" class="flex flex-col gap-y-4 max-w-xl">
    @csrf

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="trailer">
        {{ $series->trailer_path ? 'Replace Trailer' : 'Upload Trailer' }}
      </label>
      <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
        type="file" name="trailer" id="series-trailer" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv">
      <p class="mt-1 text-xs text-gray-500">MP4, WebM, OGG or MKV — will be transcoded to MP4</p>
      @error('trailer')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
    </div>

    {{-- Upload progress --}}
    <div id="trailer-progress-wrap" class="hidden flex flex-col gap-y-2">
      <div class="flex justify-between text-xs font-bold uppercase text-gray-500">
        <span>Uploading trailer…</span>
        <span id="trailer-progress-label">0%</span>
      </div>
      <div class="w-full h-2 bg-gray-200 dark:bg-gray-700">
        <div id="trailer-progress-bar" class="h-2 bg-accent transition-all duration-150" style="width:0%"></div>
      </div>
      <p class="text-xs text-gray-500">Large files may take a few minutes. Do not close this tab.</p>
    </div>

    <div>
      <button type="submit" id="trailer-submit"
        class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-50 disabled:cursor-not-allowed">
        Upload Trailer
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
              <th class="pb-2 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Video</th>
              <th class="pb-2 font-bold uppercase text-xs text-gray-500 tracking-widest"></th>
            </tr>
          </thead>
          <tbody>
            @foreach ($season->episodes as $episode)
              {{-- Episode row --}}
              <tr class="border-b border-gray-100 dark:border-gray-700">
                <td class="py-2 pr-4 text-gray-500">{{ str_pad($episode->episode_number, 2, '0', STR_PAD_LEFT) }}</td>
                <td class="py-2 pr-4 font-bold text-gray-900 dark:text-white">{{ $episode->title }}</td>
                <td class="py-2 pr-4 text-gray-500">{{ $episode->duration ? $episode->duration . ' min' : '—' }}</td>
                <td class="py-2 pr-4">
                  @if ($episode->streaming_path)
                    <span class="inline-flex items-center gap-1 text-xs font-bold uppercase text-green-600 dark:text-green-400">
                      <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>Ready
                    </span>
                  @elseif ($episode->video_path)
                    <span class="inline-flex items-center gap-1 text-xs font-bold uppercase text-yellow-600 dark:text-yellow-400">
                      <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 inline-block"></span>Processing
                    </span>
                  @else
                    <span class="text-xs text-gray-400">—</span>
                  @endif
                </td>
                <td class="py-2 text-right">
                  <div class="flex items-center justify-end gap-4">
                    <button type="button"
                      onclick="toggleVideoReplace('{{ $episode->id }}')"
                      class="text-xs font-bold uppercase text-accent hover:text-accent/70 transition-colors">
                      Replace Video
                    </button>
                    <form method="POST" action="{{ route('admin.episodes.destroy', $episode) }}"
                      onsubmit="return confirm('Delete episode {{ $episode->episode_number }}?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-danger transition-colors">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
              {{-- Collapsible video-replace row --}}
              <tr id="video-replace-{{ $episode->id }}" class="hidden border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <td colspan="5" class="py-5 px-4">
                  <form method="POST" action="{{ route('admin.episodes.video', $episode) }}"
                    enctype="multipart/form-data" data-video-replace-form="{{ $episode->id }}"
                    class="flex flex-col gap-y-4 max-w-xl">
                    @csrf
                    <p class="text-xs font-bold uppercase text-gray-500 tracking-wider">Replace video — {{ $episode->label() }}</p>

                    {{-- Underline tab nav --}}
                    <div class="flex border-b border-gray-200 dark:border-gray-600">
                      <button type="button" data-tab-btn="upload"
                        class="inline-flex items-center gap-x-1.5 pb-2.5 px-1 mr-5 text-xs font-bold uppercase tracking-wider border-b-2 border-accent text-accent transition-colors">
                        <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                        Upload File
                      </button>
                      <button type="button" data-tab-btn="url"
                        class="inline-flex items-center gap-x-1.5 pb-2.5 px-1 text-xs font-bold uppercase tracking-wider border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                        <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                        Streaming URL
                      </button>
                    </div>

                    {{-- Upload panel --}}
                    <div data-tab-panel="upload" class="flex flex-col gap-y-3">
                      <label data-drop-zone
                        class="flex items-center gap-x-4 w-full border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-accent dark:hover:border-accent transition-colors cursor-pointer py-4 px-5 bg-white dark:bg-gray-900/40 group">
                        <svg class="h-6 w-6 fill-gray-400 group-hover:fill-accent transition-colors shrink-0" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                        <div>
                          <span class="block text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-accent transition-colors">Click to browse</span>
                          <span data-file-name class="block text-xs text-gray-500 mt-0.5">MP4, WebM, OGG, MKV</span>
                        </div>
                        <input type="file" name="video" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv" class="sr-only">
                      </label>

                      <div data-replace-progress class="hidden flex flex-col gap-y-1.5">
                        <div class="flex justify-between text-xs font-bold uppercase text-gray-500">
                          <span>Uploading…</span>
                          <span data-replace-label>0%</span>
                        </div>
                        <div class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                          <div data-replace-bar class="h-1.5 bg-accent transition-all duration-150 rounded-full" style="width:0%"></div>
                        </div>
                      </div>
                    </div>

                    {{-- URL panel --}}
                    <div data-tab-panel="url" class="hidden flex flex-col gap-y-3">
                      <div class="flex items-stretch border border-gray-300 dark:border-gray-600 focus-within:border-accent transition-colors">
                        <div class="flex items-center px-3 bg-gray-50 dark:bg-gray-800 border-r border-gray-300 dark:border-gray-600 shrink-0">
                          <svg class="h-3.5 w-3.5 fill-gray-400" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                        </div>
                        <input class="flex-1 px-3 py-2.5 bg-white dark:bg-gray-900 text-sm font-medium text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none"
                          type="text" name="streaming_url" placeholder="/stream/series/title/ep01/master.m3u8" autocomplete="off">
                      </div>
                      <p class="text-xs text-gray-500">Relative path or full URL to the HLS master playlist.</p>
                    </div>

                    <div class="flex items-center gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                      <button type="submit" data-replace-submit
                        class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-2 py-2.5 px-5 text-sm text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-50 disabled:cursor-not-allowed">
                        Upload
                      </button>
                      <button type="button" onclick="toggleVideoReplace('{{ $episode->id }}')"
                        class="text-xs font-bold uppercase text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                        Cancel
                      </button>
                    </div>
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
            <div class="col-span-2">
              <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Episode Title</label>
              <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full"
                type="text" name="title">
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Video File <span class="text-danger">*</span></label>
              <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
                type="file" name="video" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv">
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
  var src = @json($series->trailerIsReady() ? $series->trailerUrl() : '');
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

  attachPreview('series-thumbnail', 'series-thumbnail-preview');
  attachPreview('series-posters-input', 'series-posters-preview');

  // ── Trailer upload form ───────────────────────────────────────
  var trailerForm  = document.getElementById('trailer-upload-form');
  var trailerWrap  = document.getElementById('trailer-progress-wrap');
  var trailerBar   = document.getElementById('trailer-progress-bar');
  var trailerLabel = document.getElementById('trailer-progress-label');
  var trailerBtn   = document.getElementById('trailer-submit');

  if (trailerForm) {
    trailerForm.addEventListener('submit', function (e) {
      var input = document.getElementById('series-trailer');
      if (!input || !input.files.length) return; // nothing selected — let browser show validation

      e.preventDefault();
      var formData = new FormData(trailerForm);
      var xhr = new XMLHttpRequest();

      trailerWrap.classList.remove('hidden');
      trailerBtn.disabled = true;
      trailerBtn.textContent = 'Uploading…';

      xhr.upload.addEventListener('progress', function (ev) {
        if (!ev.lengthComputable) return;
        var pct = Math.round((ev.loaded / ev.total) * 100);
        trailerBar.style.width = pct + '%';
        trailerLabel.textContent = pct + '%';
        if (pct === 100) { trailerLabel.textContent = 'Saving…'; trailerBtn.textContent = 'Saving…'; }
      });

      xhr.addEventListener('load', function () {
        if (xhr.status >= 200 && xhr.status < 400) {
          window.location.href = xhr.responseURL;
        } else {
          trailerBtn.disabled = false;
          trailerBtn.textContent = 'Upload Trailer';
          trailerLabel.textContent = 'Upload failed (' + xhr.status + ') — try again.';
          trailerBar.style.width = '0%';
        }
      });

      xhr.addEventListener('error', function () {
        trailerBtn.disabled = false;
        trailerBtn.textContent = 'Upload Trailer';
        trailerLabel.textContent = 'Upload failed — try again.';
        trailerBar.style.width = '0%';
      });

      xhr.open('POST', trailerForm.action);
      xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');
      xhr.send(formData);
    });
  }

  // ── Video replace toggle ─────────────────────────────────────
  window.toggleVideoReplace = function (id) {
    var row = document.getElementById('video-replace-' + id);
    if (row) row.classList.toggle('hidden');
  };

  // ── Video replace XHR ────────────────────────────────────────
  document.querySelectorAll('[data-video-replace-form]').forEach(function (form) {
    var progressWrap  = form.querySelector('[data-replace-progress]');
    var progressBar   = form.querySelector('[data-replace-bar]');
    var progressLabel = form.querySelector('[data-replace-label]');
    var submitBtn     = form.querySelector('[data-replace-submit]');

    // Tab toggle
    function activateTab(tab) {
      var fileInput = form.querySelector('[name="video"]');
      form.querySelectorAll('[data-tab-btn]').forEach(function (b) {
        var active = b.dataset.tabBtn === tab;
        b.classList.toggle('border-accent', active);
        b.classList.toggle('text-accent', active);
        b.classList.toggle('border-transparent', !active);
        b.classList.toggle('text-gray-500', !active);
      });
      form.querySelectorAll('[data-tab-panel]').forEach(function (panel) {
        panel.classList.toggle('hidden', panel.dataset.tabPanel !== tab);
      });
      if (fileInput) {
        fileInput.disabled = (tab !== 'upload');
        if (tab !== 'upload') {
          fileInput.value = '';
          var fn = form.querySelector('[data-file-name]');
          if (fn) fn.textContent = 'MP4, WebM, OGG, MKV';
        }
      }
      if (submitBtn) submitBtn.textContent = tab === 'upload' ? 'Upload' : 'Save URL';
    }

    form.querySelectorAll('[data-tab-btn]').forEach(function (btn) {
      btn.addEventListener('click', function () { activateTab(btn.dataset.tabBtn); });
    });

    // Drop zone wiring
    var dropZone  = form.querySelector('[data-drop-zone]');
    var fileInput = form.querySelector('[name="video"]');
    if (dropZone && fileInput) {
      fileInput.addEventListener('change', function () {
        var fn = form.querySelector('[data-file-name]');
        if (fn) fn.textContent = fileInput.files.length ? fileInput.files[0].name : 'MP4, WebM, OGG, MKV';
      });
      dropZone.addEventListener('dragover', function (e) { e.preventDefault(); dropZone.classList.add('border-accent'); });
      dropZone.addEventListener('dragleave', function () { dropZone.classList.remove('border-accent'); });
      dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('border-accent');
        if (e.dataTransfer.files.length) {
          fileInput.files = e.dataTransfer.files;
          var fn = form.querySelector('[data-file-name]');
          if (fn) fn.textContent = e.dataTransfer.files[0].name;
        }
      });
    }

    form.addEventListener('submit', function (e) {
      var fileInput = form.querySelector('[name="video"]');
      if (!fileInput || !fileInput.files.length) return; // URL mode — regular submit

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
          submitBtn.textContent = 'Upload';
          progressLabel.textContent = 'Upload failed (' + xhr.status + ') — try again.';
          progressBar.style.width = '0%';
        }
      });

      xhr.addEventListener('error', function () {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Upload';
        progressLabel.textContent = 'Upload failed — try again.';
        progressBar.style.width = '0%';
      });

      xhr.open('POST', form.action);
      xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');
      xhr.send(formData);
    });
  });

  // ── Episode upload progress ───────────────────────────────────
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
        if (xhr.status >= 200 && xhr.status < 400) {
          window.location.href = xhr.responseURL;
        } else {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Upload Episode';
          progressLabel.textContent = 'Upload failed (status ' + xhr.status + ') — try again.';
          progressBar.style.width = '0%';
          progressWrap.classList.remove('hidden');
        }
      });

      xhr.addEventListener('error', function () {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Upload Episode';
        progressLabel.textContent = 'Upload failed — try again.';
        progressBar.style.width = '0%';
      });

      xhr.open('POST', form.action);
      xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');
      xhr.send(formData);
    });
  });
})();
</script>
@endpush
