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

  <form x-data="trailerUpload" method="POST" action="{{ route('admin.series.trailer', $series) }}"
    enctype="multipart/form-data" @submit.prevent="upload($el)" class="flex flex-col gap-y-4 max-w-xl">
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
        <span x-show="!uploading">{{ $series->trailer_path ? 'Replace Trailer' : 'Upload Trailer' }}</span>
        <span x-show="uploading && progress < 100" style="display:none">Uploading…</span>
        <span x-show="uploading && progress >= 100" style="display:none">Saving…</span>
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
          @foreach ($season->episodes as $episode)
            {{-- Each episode pair gets its own tbody for Alpine scoping --}}
            <tbody x-data="seriesVideoReplace({{ $episode->is_free ? 'true' : 'false' }}, '{{ route('admin.episodes.toggle-free', $episode) }}')"
              >
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
                    <button type="button" @click="toggleFree"
                      :class="isFree ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500'"
                      class="text-[10px] font-bold uppercase px-2 py-0.5 transition-colors"
                      :title="isFree ? 'Click to remove free access' : 'Click to make free'">
                      <span x-text="isFree ? 'Free' : 'Set Free'">{{ $episode->is_free ? 'Free' : 'Set Free' }}</span>
                    </button>
                    <button type="button" @click="open = !open"
                      class="text-xs font-bold uppercase text-accent hover:text-accent/70 transition-colors">
                      Edit Episode
                    </button>
                    <form method="POST" action="{{ route('admin.episodes.destroy', $episode) }}"
                      onsubmit="return confirm('Delete episode {{ $episode->episode_number }}?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-danger transition-colors">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
              {{-- Collapsible edit-episode row --}}
              <tr style="display:none" x-show="open" class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <td colspan="5" class="py-5 px-4">
                  <div class="max-w-xl flex flex-col gap-y-7">
                    <p class="text-xs font-bold uppercase text-gray-500 tracking-wider">Edit Episode — {{ $episode->label() }}</p>

                    {{-- ── Metadata form ── --}}
                    <form method="POST" action="{{ route('admin.episodes.update', $episode) }}"
                      class="flex flex-col gap-y-4">
                      @csrf @method('PUT')

                      <div class="grid grid-cols-2 gap-4">
                        <div>
                          <label class="block text-xs font-bold uppercase text-gray-500 mb-1.5">Episode #</label>
                          <input class="px-3 py-2 border-base bg-white/40 font-medium text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full text-sm"
                            type="number" name="episode_number" min="1" value="{{ $episode->episode_number }}" readonly disabled>
                        </div>
                        <div class="col-span-2">
                          <label class="block text-xs font-bold uppercase text-gray-500 mb-1.5">Title</label>
                          <input class="px-3 py-2 border-base bg-white/40 font-medium text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full text-sm"
                            type="text" name="title" value="{{ $episode->title }}">
                        </div>
                        <div>
                          <label class="block text-xs font-bold uppercase text-gray-500 mb-1.5">Duration (min)</label>
                          <input class="px-3 py-2 border-base bg-white/40 font-medium text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full text-sm"
                            type="number" name="duration" min="1" value="{{ $episode->duration }}">
                        </div>
                        <div class="col-span-2">
                          <label class="block text-xs font-bold uppercase text-gray-500 mb-1.5">Description</label>
                          <textarea class="px-3 py-2 border-base bg-white/40 font-medium text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full text-sm h-20"
                            name="description">{{ $episode->description }}</textarea>
                        </div>
                        <div class="col-span-2 flex items-center gap-x-3">
                          <input type="checkbox" name="is_free" id="is_free_{{ $episode->id }}" value="1"
                            x-model="isFree"
                            class="w-4 h-4 accent-accent cursor-pointer">
                          <label for="is_free_{{ $episode->id }}" class="text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">
                            Free to watch <span class="font-normal text-gray-500">(no login required)</span>
                          </label>
                        </div>
                      </div>

                      <div>
                        <button type="submit"
                          class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-2 py-2.5 px-5 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                          Save
                        </button>
                      </div>
                    </form>

                    {{-- ── Video form ── --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                      <p class="text-xs font-bold uppercase text-gray-500 tracking-wider mb-4">Replace Video</p>
                      <form method="POST" action="{{ route('admin.episodes.video', $episode) }}"
                        enctype="multipart/form-data" x-ref="form" @submit.prevent="upload($refs.form)"
                        class="flex flex-col gap-y-4">
                        @csrf

                        {{-- Underline tab nav --}}
                        <div class="flex border-b border-gray-200 dark:border-gray-600">
                          <button type="button" @click="tab = 'upload'"
                            :class="tab === 'upload' ? 'border-accent text-accent' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="inline-flex items-center gap-x-1.5 pb-2.5 px-1 mr-5 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors">
                            <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                            Upload File
                          </button>
                          <button type="button" @click="tab = 'url'"
                            :class="tab === 'url' ? 'border-accent text-accent' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="inline-flex items-center gap-x-1.5 pb-2.5 px-1 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors">
                            <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                            Streaming URL
                          </button>
                        </div>

                        {{-- Upload panel --}}
                        <div x-show="tab === 'upload'" class="flex flex-col gap-y-3">
                          <label
                            @dragover.prevent="dragging = true"
                            @dragleave="dragging = false"
                            @drop.prevent="drop($event)"
                            :class="dragging ? 'border-accent' : 'border-gray-300 dark:border-gray-600'"
                            class="flex items-center gap-x-4 w-full border-2 border-dashed hover:border-accent dark:hover:border-accent transition-colors cursor-pointer py-4 px-5 bg-white dark:bg-gray-900/40 group">
                            <svg class="h-6 w-6 fill-gray-400 group-hover:fill-accent transition-colors shrink-0" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                            <div>
                              <span class="block text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-accent transition-colors">Click to browse</span>
                              <span x-text="fileName" class="block text-xs text-gray-500 mt-0.5">MP4, WebM, OGG, MKV</span>
                            </div>
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
                          </div>

                          <p x-show="error" style="display:none" x-text="error" class="text-xs text-danger"></p>
                        </div>

                        {{-- URL panel --}}
                        <div x-show="tab === 'url'" style="display:none" class="flex flex-col gap-y-3">
                          <div class="flex items-stretch border border-gray-300 dark:border-gray-600 focus-within:border-accent transition-colors">
                            <div class="flex items-center px-3 bg-gray-50 dark:bg-gray-800 border-r border-gray-300 dark:border-gray-600 shrink-0">
                              <svg class="h-3.5 w-3.5 fill-gray-400" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                            </div>
                            <input class="flex-1 px-3 py-2.5 bg-white dark:bg-gray-900 text-sm font-medium text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none"
                              type="text" name="streaming_url" placeholder="/stream/series/title/ep01/master.m3u8" autocomplete="off">
                          </div>
                          <p class="text-xs text-gray-500">Relative path or full URL to the HLS master playlist.</p>
                        </div>

                        <div class="flex items-center gap-3">
                          <button type="submit" :disabled="uploading"
                            class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-2 py-2.5 px-5 text-sm text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-50 disabled:cursor-not-allowed"
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

                    {{-- ── Subtitle tracks ── --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                      <p class="text-xs font-bold uppercase text-gray-500 tracking-wider mb-4">Subtitle Tracks</p>

                      @if ($episode->subtitles->isNotEmpty())
                        <div class="mb-4 flex flex-col gap-y-1.5">
                          @foreach ($episode->subtitles as $subtitle)
                            <div class="flex items-center justify-between px-3 py-2 border border-gray-200 dark:border-gray-600">
                              <div class="flex items-center gap-x-2.5">
                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $subtitle->language }}</span>
                                <code class="text-[10px] uppercase text-gray-400 tracking-widest">{{ $subtitle->language_code }}</code>
                                @if ($subtitle->is_default)
                                  <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 bg-accent/20 text-accent">Default</span>
                                @endif
                              </div>
                              <form method="POST" action="{{ route('admin.subtitles.destroy', $subtitle) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[10px] font-bold uppercase text-gray-400 hover:text-red-500 transition-colors">
                                  Remove
                                </button>
                              </form>
                            </div>
                          @endforeach
                        </div>
                      @endif

                      <form method="POST" action="{{ route('admin.episodes.subtitles.store', $episode) }}"
                        enctype="multipart/form-data" x-data="{ subTab: 'upload' }" class="flex flex-col gap-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                          <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1.5">Language</label>
                            <input class="px-3 py-2 border-base bg-white/40 font-medium text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full text-sm"
                              type="text" name="language" placeholder="English">
                          </div>
                          <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-1.5">Code</label>
                            <input class="px-3 py-2 border-base bg-white/40 font-medium text-gray-900 focus:border-accent focus:bg-white focus:outline-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white w-full text-sm"
                              type="text" name="language_code" placeholder="en" maxlength="10">
                          </div>
                        </div>

                        {{-- Tab nav --}}
                        <div class="flex border-b border-gray-200 dark:border-gray-600">
                          <button type="button" @click="subTab = 'upload'"
                            :class="subTab === 'upload' ? 'border-accent text-accent' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="inline-flex items-center gap-x-1.5 pb-2 px-1 mr-5 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors">
                            <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                            Upload File
                          </button>
                          <button type="button" @click="subTab = 'url'"
                            :class="subTab === 'url' ? 'border-accent text-accent' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="inline-flex items-center gap-x-1.5 pb-2 px-1 text-xs font-bold uppercase tracking-wider border-b-2 transition-colors">
                            <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                            Enter URL
                          </button>
                        </div>

                        {{-- Upload panel --}}
                        <div x-show="subTab === 'upload'">
                          <input class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
                            type="file" name="subtitle_file" accept=".vtt,.srt">
                          <p class="mt-1 text-[11px] text-gray-500">VTT or SRT — SRT is auto-converted</p>
                        </div>

                        {{-- URL panel --}}
                        <div x-show="subTab === 'url'" style="display:none">
                          <div class="flex items-stretch border border-gray-300 dark:border-gray-600 focus-within:border-accent transition-colors">
                            <div class="flex items-center px-2.5 bg-gray-50 dark:bg-gray-800 border-r border-gray-300 dark:border-gray-600 shrink-0">
                              <svg class="h-3.5 w-3.5 fill-gray-400" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                            </div>
                            <input class="flex-1 px-3 py-2 bg-white dark:bg-gray-900 text-sm font-medium text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none"
                              type="text" name="subtitle_url" placeholder="/subtitles/en.vtt or https://cdn.example.com/en.vtt" autocomplete="off">
                          </div>
                          <p class="mt-1 text-[11px] text-gray-500">Relative path or absolute URL to a WebVTT file.</p>
                        </div>

                        <div class="flex items-center gap-x-2">
                          <input type="checkbox" name="is_default" id="sub_default_{{ $episode->id }}" value="1"
                            class="w-4 h-4 accent-accent cursor-pointer">
                          <label for="sub_default_{{ $episode->id }}" class="text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">
                            Set as default track
                          </label>
                        </div>
                        <div>
                          <button type="submit"
                            class="inline-flex text-center font-bold leading-none uppercase justify-center gap-x-2 py-2 px-5 text-xs text-gray-900 bg-accent hover:bg-accent/90 transition-colors">
                            Add Subtitle
                          </button>
                        </div>
                      </form>
                    </div>

                  </div>
                </td>
              </tr>
            </tbody>
          @endforeach
        </table>
      @endif

      {{-- Add episode --}}
      <details class="group">
        <summary class="cursor-pointer text-xs font-bold uppercase text-accent hover:text-accent/70 transition-colors list-none">
          + Add Episode
        </summary>
        <form x-data="episodeUpload" method="POST" action="{{ route('admin.episodes.store', $season) }}"
          enctype="multipart/form-data" @submit.prevent="upload($el)" class="mt-5 flex flex-col gap-y-4 max-w-xl">
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

            <div class="col-span-2 flex items-center gap-x-3">
              <input type="checkbox" name="is_free" id="ep_is_free_{{ $season->id }}" value="1"
                class="w-4 h-4 accent-accent cursor-pointer">
              <label for="ep_is_free_{{ $season->id }}" class="text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer">
                Free to watch <span class="font-normal text-gray-500">(no login required)</span>
              </label>
            </div>
          </div>

          <div x-show="uploading" style="display:none" class="flex flex-col gap-y-1.5">
            <div class="flex justify-between text-xs font-bold uppercase text-gray-500">
              <span>Uploading…</span>
              <span x-text="progress < 100 ? progress + '%' : 'Saving…'">0%</span>
            </div>
            <div class="w-full h-2 bg-gray-200 dark:bg-gray-700">
              <div class="h-2 bg-accent transition-all duration-150" :style="'width:' + progress + '%'"></div>
            </div>
            <p class="text-xs text-gray-500">Do not close this tab until the upload completes.</p>
          </div>

          <p x-show="error" style="display:none" x-text="error" class="text-xs text-danger"></p>

          <div>
            <button type="submit" :disabled="uploading"
              class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-3 px-6 text-sm text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-50 disabled:cursor-not-allowed">
              <span x-show="!uploading">Upload Episode</span>
              <span x-show="uploading && progress < 100" style="display:none">Uploading…</span>
              <span x-show="uploading && progress >= 100" style="display:none">Saving…</span>
            </button>
          </div>
        </form>
      </details>

      {{-- Bulk Upload --}}
      <div x-data="bulkUpload({{ $season->id }}, '{{ route('admin.episodes.store', $season) }}', {{ ($season->episodes->max('episode_number') ?? 0) + 1 }})" class="mt-3">
        <button type="button" @click="show = !show"
          class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">
          <span x-text="show ? '− Bulk Upload' : '↑ Bulk Upload'">↑ Bulk Upload</span>
        </button>

        <div x-show="show" style="display:none" class="mt-4 border border-dashed border-gray-300 dark:border-gray-600 p-5">
          <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-bold uppercase text-gray-500 tracking-widest">Bulk Upload Episodes</p>
            <label class="inline-flex items-center gap-x-1.5 cursor-pointer text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors border border-gray-300 dark:border-gray-600 hover:border-accent py-1.5 px-3">
              <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
              Choose Files
              <input type="file" accept="video/mp4,video/webm,video/ogg,video/x-matroska,.mkv" multiple class="sr-only" @change="pick($event)">
            </label>
          </div>

          <template x-if="!queue.length">
            <p class="text-xs text-gray-400">Select multiple video files to queue them. Titles are extracted from filenames and can be edited before uploading.</p>
          </template>

          <template x-if="queue.length > 0">
            <div class="flex flex-col gap-y-2">
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-400" x-text="queue.length + ' file' + (queue.length !== 1 ? 's' : '') + ' queued'"></span>
                <button type="button" @click="uploadAll()"
                  :disabled="queue.every(function(i){ return i.status !== 'pending'; })"
                  class="text-xs font-bold uppercase text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-40 disabled:cursor-not-allowed py-1.5 px-4 transition-colors">
                  Upload All
                </button>
              </div>

              <template x-for="item in queue" :key="item.id">
                <div class="border border-gray-200 dark:border-gray-700 p-3">
                  <div class="flex items-center gap-x-2">
                    <input type="number" x-model.number="item.epNum" min="1"
                      class="w-14 shrink-0 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-2 py-1 text-xs font-bold text-center focus:outline-none focus:border-accent"
                      :disabled="item.status !== 'pending'">
                    <input type="text" x-model="item.title" placeholder="Episode title" required
                      class="flex-1 min-w-0 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-2 py-1 text-sm focus:outline-none focus:border-accent"
                      :disabled="item.status !== 'pending'">
                    <div class="shrink-0 flex items-center gap-x-2">
                      <template x-if="item.status === 'pending'">
                        <div class="flex items-center gap-x-2">
                          <button type="button" @click="upload(item)"
                            class="text-xs font-bold uppercase px-3 py-1 bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
                            Upload
                          </button>
                          <button type="button" @click="remove(item.id)"
                            class="text-xs font-bold text-gray-400 hover:text-red-500 transition-colors">✕</button>
                        </div>
                      </template>
                      <template x-if="item.status === 'uploading'">
                        <span class="text-xs font-bold uppercase text-gray-500 tabular-nums" x-text="item.progress < 100 ? item.progress + '%' : 'Saving…'"></span>
                      </template>
                      <template x-if="item.status === 'done'">
                        <span class="text-xs font-bold uppercase text-green-600 dark:text-green-400">✓ Done</span>
                      </template>
                      <template x-if="item.status === 'error'">
                        <div class="flex items-center gap-x-2">
                          <span class="text-xs text-danger" x-text="item.error"></span>
                          <button type="button" @click="item.status = 'pending'; item.error = ''"
                            class="text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors">Retry</button>
                        </div>
                      </template>
                    </div>
                  </div>
                  <template x-if="item.status === 'uploading'">
                    <div class="mt-2 w-full h-1 bg-gray-200 dark:bg-gray-700">
                      <div class="h-1 bg-accent transition-all duration-150" :style="'width:' + item.progress + '%'"></div>
                    </div>
                  </template>
                  <p class="text-[11px] text-gray-400 mt-1 truncate" x-text="item.file.name"></p>
                </div>
              </template>

              <template x-if="queue.some(function(i){ return i.status === 'done'; })">
                <p class="text-xs text-gray-400 mt-1">Reload the page to see uploaded episodes.</p>
              </template>
            </div>
          </template>
        </div>
      </div>

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

  attachPreview('series-thumbnail', 'series-thumbnail-preview');
  attachPreview('series-posters-input', 'series-posters-preview');
})();

// ── Alpine components ─────────────────────────────────────────
document.addEventListener('alpine:init', function () {
  Alpine.data('seriesVideoReplace', function (isFree, toggleFreeUrl) {
    return {
      open: false,
      tab: 'upload',
      uploading: false,
      progress: 0,
      fileName: 'MP4, WebM, OGG, MKV',
      dragging: false,
      error: '',
      isFree: isFree,
      toggleFreeUrl: toggleFreeUrl,
      toggleFree: function () {
        var self = this;
        fetch(self.toggleFreeUrl, {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) { self.isFree = data.is_free; })
        .catch(function () {});
      },
      get submitLabel() {
        if (this.tab === 'url') return 'Save URL';
        if (this.uploading) return this.progress < 100 ? 'Uploading…' : 'Saving…';
        return 'Upload';
      },
      upload: function (form) {
        var input = form.querySelector('[name="video"]');
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
      },
      drop: function (e) {
        this.dragging = false;
        var files = e.dataTransfer.files;
        if (!files.length) return;
        if (this.$refs.videoInput) { this.$refs.videoInput.files = files; }
        this.fileName = files[0].name;
      }
    };
  });

  Alpine.data('episodeUpload', function () {
    return {
      uploading: false,
      progress: 0,
      error: '',
      upload: function (form) {
        var input = form.querySelector('[name="video"]');
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
      }
    };
  });

  Alpine.data('bulkUpload', function (seasonId, storeUrl, startEpNum) {
    return {
      show: false,
      queue: [],
      nextNum: startEpNum,
      titleFromFile: function (filename) {
        return filename
          .replace(/\.[^.]+$/, '')
          .replace(/[_\-\.]+/g, ' ')
          .replace(/\b\w/g, function (c) { return c.toUpperCase(); })
          .trim();
      },
      pick: function (event) {
        var self = this;
        Array.from(event.target.files).forEach(function (file) {
          self.queue.push({
            id: Math.random().toString(36).slice(2),
            file: file,
            title: self.titleFromFile(file.name),
            epNum: self.nextNum++,
            status: 'pending',
            progress: 0,
            error: '',
          });
        });
        event.target.value = '';
      },
      remove: function (id) {
        this.queue = this.queue.filter(function (item) { return item.id !== id; });
      },
      upload: function (item) {
        item.status = 'uploading';
        item.progress = 0;
        item.error = '';
        var fd = new FormData();
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        fd.append('episode_number', item.epNum);
        fd.append('title', item.title);
        fd.append('video', item.file);
        var xhr = new XMLHttpRequest();
        var self = this;
        xhr.upload.addEventListener('progress', function (ev) {
          if (ev.lengthComputable) item.progress = Math.round(ev.loaded / ev.total * 100);
        });
        xhr.addEventListener('load', function () {
          if (xhr.status >= 200 && xhr.status < 400) {
            item.status = 'done';
          } else {
            item.status = 'error';
            item.error = 'Upload failed (' + xhr.status + ')';
          }
        });
        xhr.addEventListener('error', function () {
          item.status = 'error';
          item.error = 'Network error — try again.';
        });
        xhr.open('POST', storeUrl);
        xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');
        xhr.send(fd);
      },
      uploadAll: function () {
        var self = this;
        this.queue
          .filter(function (i) { return i.status === 'pending'; })
          .forEach(function (item) { self.upload(item); });
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
