@extends('layouts.account')

@section('account-title', 'New Watch Party')

@section('account-content')

@php
  $seriesJson = $series->map(fn($s) => [
    'id'      => $s->id,
    'title'   => $s->title,
    'seasons' => $s->seasons->map(fn($season) => [
      'id'       => $season->id,
      'label'    => $season->label(),
      'episodes' => $season->episodes->map(fn($ep) => [
        'id'    => $ep->id,
        'label' => 'E' . str_pad($ep->episode_number, 2, '0', STR_PAD_LEFT) . ' — ' . $ep->title,
      ])->values(),
    ])->values(),
  ])->values();
@endphp

<div class="max-w-2xl" x-data="createParty()" x-init="init()">

  <div class="mb-8">
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white">New Watch Party</h2>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pick something to watch, invite friends, and stream in sync.</p>
  </div>

  <form method="POST" action="{{ route('account.watch-party.store') }}" @submit="prepareSubmit">
    @csrf

    @if ($errors->any())
      <div class="mb-6 border border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700 px-5 py-4">
        <ul class="space-y-1">
          @foreach ($errors->all() as $e)
            <li class="text-sm text-red-600 dark:text-red-400">{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <input type="hidden" name="watchable_type" :value="type">
    <input type="hidden" name="movie_id" :value="movieId">
    <input type="hidden" name="series_id" :value="seriesId">

    <template x-for="id in selectedEpisodes" :key="id">
      <input type="hidden" name="episode_ids[]" :value="id">
    </template>

    <div class="flex flex-col gap-y-5">

      {{-- Content type toggle --}}
      <div class="border border-gray-200 dark:border-gray-700 dark:bg-gray-900">
        <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 dark:bg-gray-800/50 dark:bg-gray-800/50">
          <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Content Type</p>
        </div>
        <div class="p-5">
          <div class="grid grid-cols-2 gap-3">

            {{-- Movie option --}}
            <label @click="type = 'movie'; seriesId = ''; selectedEpisodes = []"
              :class="type === 'movie'
                ? 'border-accent bg-accent/5 dark:bg-accent/10 shadow-sm'
                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
              class="relative flex items-center gap-x-3.5 border-2 p-4 cursor-pointer transition-all select-none">
              <div :class="type === 'movie' ? 'bg-accent/15 text-accent' : 'bg-gray-100 dark:bg-gray-800 text-gray-400'"
                class="p-2 shrink-0 transition-colors">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                  <path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/>
                </svg>
              </div>
              <div>
                <p :class="type === 'movie' ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                  class="font-bold text-sm leading-tight transition-colors">Movie</p>
                <p class="text-[11px] text-gray-400 mt-0.5">One film</p>
              </div>
              <span x-show="type === 'movie'"
                class="absolute top-2 right-2 w-4 h-4 bg-accent flex items-center justify-center">
                <svg class="w-2.5 h-2.5 fill-gray-900" viewBox="0 0 24 24">
                  <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
              </span>
            </label>

            {{-- Series option --}}
            <label @click="type = 'series'; movieId = ''"
              :class="type === 'series'
                ? 'border-accent bg-accent/5 dark:bg-accent/10 shadow-sm'
                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
              class="relative flex items-center gap-x-3.5 border-2 p-4 cursor-pointer transition-all select-none">
              <div :class="type === 'series' ? 'bg-accent/15 text-accent' : 'bg-gray-100 dark:bg-gray-800 text-gray-400'"
                class="p-2 shrink-0 transition-colors">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                  <path d="M21 3H3c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h5v2h8v-2h5c1.11 0 2-.89 2-2V5c0-1.11-.89-2-2-2zm0 14H3V5h18v12zm-5-6l-7 4V7z"/>
                </svg>
              </div>
              <div>
                <p :class="type === 'series' ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                  class="font-bold text-sm leading-tight transition-colors">Series</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Pick episodes</p>
              </div>
              <span x-show="type === 'series'"
                class="absolute top-2 right-2 w-4 h-4 bg-accent flex items-center justify-center">
                <svg class="w-2.5 h-2.5 fill-gray-900" viewBox="0 0 24 24">
                  <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
              </span>
            </label>

          </div>
        </div>
      </div>

      {{-- ── Movie picker --}}
      <div x-show="type === 'movie'" x-collapse
        class="border border-gray-200 dark:border-gray-700 dark:bg-gray-900">
        <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 dark:bg-gray-800/50 dark:bg-gray-800/50">
          <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Select Movie</p>
        </div>
        <div class="p-5">
          <select x-model="movieId"
            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white px-4 py-3 text-sm focus:border-accent outline-none transition-colors appearance-none">
            <option value="">— Choose a movie —</option>
            @foreach ($movies as $movie)
              <option value="{{ $movie->id }}">{{ $movie->title }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- ── Series picker + episode checklist --}}
      <div x-show="type === 'series'" x-collapse
        class="border border-gray-200 dark:border-gray-700 dark:bg-gray-900">
        <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 dark:bg-gray-800/50 dark:bg-gray-800/50">
          <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Select Series</p>
        </div>
        <div class="p-5 border-b border-gray-100 dark:border-gray-700/60">
          <select x-model="seriesId" @change="selectedEpisodes = []"
            class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white px-4 py-3 text-sm focus:border-accent outline-none transition-colors appearance-none">
            <option value="">— Choose a series —</option>
            <template x-for="s in allSeries" :key="s.id">
              <option :value="s.id" x-text="s.title"></option>
            </template>
          </select>
        </div>

        {{-- Episode checklist --}}
        <div x-show="currentSeries" x-collapse>

          {{-- Toolbar --}}
          <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-gray-700/60 bg-gray-50/70 dark:bg-gray-800/30">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
              Episodes
              <span class="ml-1.5 text-accent" x-text="selectedEpisodes.length ? '· ' + selectedEpisodes.length + ' selected' : ''"></span>
            </p>
            <div class="flex items-center gap-x-3">
              <button type="button" @click="selectAll()"
                class="text-[10px] font-bold uppercase text-accent hover:underline tracking-wide">All</button>
              <span class="text-gray-600 dark:text-gray-600 text-xs">·</span>
              <button type="button" @click="selectedEpisodes = []"
                class="text-[10px] font-bold uppercase text-gray-400 hover:text-gray-900 dark:hover:text-white tracking-wide">Clear</button>
            </div>
          </div>

          <div class="divide-y divide-gray-100 dark:divide-gray-700/60 max-h-80 overflow-y-auto">
            <template x-for="season in currentSeries?.seasons ?? []" :key="season.id">
              <div>
                {{-- Season header --}}
                <button type="button" @click="toggleSeason(season)"
                  class="flex items-center justify-between w-full px-5 py-2.5 text-left bg-gray-50/60 dark:bg-gray-800/40 hover:bg-gray-100/60 dark:hover:bg-gray-800/70 transition-colors">
                  <span class="text-[10px] font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400" x-text="season.label"></span>
                  <span class="text-[10px] font-bold uppercase tracking-wide transition-colors"
                    :class="isSeasonAllSelected(season) ? 'text-accent' : 'text-gray-400'"
                    x-text="isSeasonAllSelected(season) ? '✓ All' : season.episodes.length + ' eps'">
                  </span>
                </button>

                {{-- Episode rows --}}
                <template x-for="ep in season.episodes" :key="ep.id">
                  <label @click="toggleEpisode(ep.id)"
                    class="flex items-center gap-x-3 px-6 py-2.5 cursor-pointer transition-colors border-t border-gray-100 dark:border-gray-700/40"
                    :class="selectedEpisodes.includes(ep.id)
                      ? 'bg-accent/5 dark:bg-accent/8 hover:bg-accent/10'
                      : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'">
                    <div :class="selectedEpisodes.includes(ep.id)
                        ? 'bg-accent border-accent'
                        : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800'"
                      class="w-4 h-4 border-2 shrink-0 flex items-center justify-center transition-colors">
                      <svg x-show="selectedEpisodes.includes(ep.id)" class="w-2.5 h-2.5 fill-gray-900" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                      </svg>
                    </div>
                    <span class="text-sm transition-colors"
                      :class="selectedEpisodes.includes(ep.id) ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400'"
                      x-text="ep.label"></span>
                  </label>
                </template>
              </div>
            </template>
          </div>
        </div>
      </div>

      {{-- ── Submit ───────────────────────────────────────────────────────── --}}
      <div class="flex items-center gap-x-4 pt-1">
        <button type="submit"
          :disabled="!canSubmit"
          class="font-bold uppercase py-3.5 px-8 text-sm bg-accent text-gray-900 hover:bg-accent/90 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
          Create Party
        </button>
        <a href="{{ route('account.watch-party.index') }}"
          class="text-sm font-bold uppercase text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
          Cancel
        </a>
        <p x-show="type === 'series' && !canSubmit && seriesId"
          class="text-xs text-gray-400 italic">
          Select at least one episode.
        </p>
      </div>

    </div>
  </form>
</div>

<script id="series-data" type="application/json">
  {!! $seriesJson->toJson() !!}
</script>

@push('scripts')
<script>
function createParty() {
  const allSeries = JSON.parse(document.getElementById('series-data').textContent);

  return {
    type: '{{ old('watchable_type', 'movie') }}',
    movieId: '{{ old('movie_id', '') }}',
    seriesId: '{{ old('series_id', '') }}',
    selectedEpisodes: [],
    allSeries,

    init() {
      @if (old('episode_ids'))
        this.selectedEpisodes = {!! json_encode(array_map('intval', old('episode_ids', []))) !!};
      @endif
    },

    get currentSeries() {
      return this.allSeries.find(s => s.id == this.seriesId) || null;
    },

    get canSubmit() {
      if (this.type === 'movie') return !!this.movieId;
      return !!this.seriesId && this.selectedEpisodes.length > 0;
    },

    seasonEpisodeIds(season) {
      return season.episodes.map(e => e.id);
    },

    isSeasonAllSelected(season) {
      const ids = this.seasonEpisodeIds(season);
      return ids.length > 0 && ids.every(id => this.selectedEpisodes.includes(id));
    },

    toggleSeason(season) {
      const ids = this.seasonEpisodeIds(season);
      if (this.isSeasonAllSelected(season)) {
        this.selectedEpisodes = this.selectedEpisodes.filter(id => !ids.includes(id));
      } else {
        ids.forEach(id => {
          if (!this.selectedEpisodes.includes(id)) this.selectedEpisodes.push(id);
        });
      }
    },

    toggleEpisode(id) {
      const idx = this.selectedEpisodes.indexOf(id);
      if (idx === -1) {
        this.selectedEpisodes.push(id);
      } else {
        this.selectedEpisodes.splice(idx, 1);
      }
    },

    selectAll() {
      if (!this.currentSeries) return;
      this.selectedEpisodes = this.currentSeries.seasons.flatMap(s => s.episodes.map(e => e.id));
    },

    prepareSubmit() {},
  };
}
</script>
@endpush
@endsection
