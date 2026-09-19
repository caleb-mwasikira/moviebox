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
  <x-ui.page-header title="New watch party" subtitle="Pick something to watch, invite friends, and stream in sync." />

  <form method="POST" action="{{ route('account.watch-party.store') }}" @submit="prepareSubmit" class="space-y-5">
    @csrf
    @if ($errors->any())
      <div class="alert alert-error !block">
        <ul class="space-y-1">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <input type="hidden" name="watchable_type" :value="type">
    <input type="hidden" name="movie_id" :value="movieId">
    <input type="hidden" name="series_id" :value="seriesId">
    <template x-for="id in selectedEpisodes" :key="id"><input type="hidden" name="episode_ids[]" :value="id"></template>

    {{-- Content type --}}
    <div class="card">
      <div class="card-header"><h2 class="card-title">Content type</h2></div>
      <div class="grid grid-cols-2 gap-3 p-5">
        <label @click="type = 'movie'; seriesId = ''; selectedEpisodes = []"
          :class="type === 'movie' ? 'border-brand bg-brand-soft' : 'border-line hover:border-line-strong'"
          class="relative flex cursor-pointer select-none items-center gap-3.5 rounded-2xl border-2 p-4 transition">
          <span :class="type === 'movie' ? 'bg-brand text-white' : 'bg-surface-2 text-muted'" class="grid size-10 place-items-center rounded-xl transition"><x-ui.icon name="film" /></span>
          <span><span class="block text-sm font-semibold text-fg">Movie</span><span class="text-xs text-muted">One film</span></span>
          <x-ui.icon name="check-circle" class="absolute right-3 top-3 size-4 text-brand" x-show="type === 'movie'" />
        </label>
        <label @click="type = 'series'; movieId = ''"
          :class="type === 'series' ? 'border-brand bg-brand-soft' : 'border-line hover:border-line-strong'"
          class="relative flex cursor-pointer select-none items-center gap-3.5 rounded-2xl border-2 p-4 transition">
          <span :class="type === 'series' ? 'bg-brand text-white' : 'bg-surface-2 text-muted'" class="grid size-10 place-items-center rounded-xl transition"><x-ui.icon name="tv" /></span>
          <span><span class="block text-sm font-semibold text-fg">Series</span><span class="text-xs text-muted">Pick episodes</span></span>
          <x-ui.icon name="check-circle" class="absolute right-3 top-3 size-4 text-brand" x-show="type === 'series'" />
        </label>
      </div>
    </div>

    {{-- Movie picker --}}
    <div x-show="type === 'movie'" x-transition class="card">
      <div class="card-header"><h2 class="card-title">Select movie</h2></div>
      <div class="p-5">
        <select x-model="movieId" class="select">
          <option value="">— Choose a movie —</option>
          @foreach ($movies as $movie)<option value="{{ $movie->id }}">{{ $movie->title }}</option>@endforeach
        </select>
      </div>
    </div>

    {{-- Series picker --}}
    <div x-show="type === 'series'" x-cloak x-transition class="card overflow-hidden">
      <div class="card-header"><h2 class="card-title">Select series</h2></div>
      <div class="border-b border-line p-5">
        <select x-model="seriesId" @change="selectedEpisodes = []" class="select">
          <option value="">— Choose a series —</option>
          <template x-for="s in allSeries" :key="s.id"><option :value="s.id" x-text="s.title"></option></template>
        </select>
      </div>

      <div x-show="currentSeries" x-transition>
        <div class="flex items-center justify-between bg-surface-2/60 px-5 py-3">
          <p class="text-xs font-semibold uppercase tracking-wider text-muted">Episodes <span class="ml-1.5 text-brand" x-text="selectedEpisodes.length ? '· ' + selectedEpisodes.length + ' selected' : ''"></span></p>
          <div class="flex items-center gap-3 text-xs font-semibold">
            <button type="button" @click="selectAll()" class="text-brand hover:underline">Select all</button>
            <button type="button" @click="selectedEpisodes = []" class="text-muted hover:text-fg">Clear</button>
          </div>
        </div>
        <div class="max-h-80 divide-y divide-line overflow-y-auto">
          <template x-for="season in currentSeries?.seasons ?? []" :key="season.id">
            <div>
              <button type="button" @click="toggleSeason(season)" class="flex w-full items-center justify-between bg-surface-2/40 px-5 py-2.5 text-left transition hover:bg-surface-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-fg-soft" x-text="season.label"></span>
                <span class="text-xs font-semibold" :class="isSeasonAllSelected(season) ? 'text-brand' : 'text-muted'" x-text="isSeasonAllSelected(season) ? '✓ All' : season.episodes.length + ' eps'"></span>
              </button>
              <template x-for="ep in season.episodes" :key="ep.id">
                <label @click.prevent="toggleEpisode(ep.id)" class="flex cursor-pointer items-center gap-3 border-t border-line px-6 py-2.5 transition" :class="selectedEpisodes.includes(ep.id) ? 'bg-brand-soft' : 'hover:bg-surface-2/50'">
                  <span :class="selectedEpisodes.includes(ep.id) ? 'border-brand bg-brand text-white' : 'border-line-strong bg-surface'" class="grid size-4 shrink-0 place-items-center rounded border-2 transition">
                    <svg x-show="selectedEpisodes.includes(ep.id)" class="size-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><path d="M20 6 9 17l-5-5"/></svg>
                  </span>
                  <span class="text-sm" :class="selectedEpisodes.includes(ep.id) ? 'font-medium text-fg' : 'text-fg-soft'" x-text="ep.label"></span>
                </label>
              </template>
            </div>
          </template>
        </div>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-3 pt-1">
      <button type="submit" :disabled="!canSubmit" class="btn btn-primary btn-lg"><x-ui.icon name="popcorn" class="size-4" /> Create party</button>
      <a href="{{ route('account.watch-party.index') }}" class="btn btn-ghost btn-lg">Cancel</a>
      <p x-show="type === 'series' && !canSubmit && seriesId" x-cloak class="text-xs italic text-muted">Select at least one episode.</p>
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
