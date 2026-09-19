@extends('layouts.app')

@section('title', 'Browse')

@section('content')
<div class="container-app py-10 lg:py-14">

  <x-ui.page-header :title="$query ? 'Search results' : 'Browse all'"
    :subtitle="$query ? 'Results for “' . $query . '”' : 'Explore our full catalogue of movies and series.'">
    <x-slot:actions>
      @if ($query)
        <a href="{{ route('browse', array_filter(['type' => $type !== 'all' ? $type : null, 'genre' => $genre])) }}" class="btn btn-secondary btn-sm">
          <x-ui.icon name="x" class="size-4" /> Clear search
        </a>
      @endif
    </x-slot:actions>
  </x-ui.page-header>

  {{-- Filters --}}
  <div class="mb-10 flex flex-wrap items-center justify-between gap-4">
    <div class="inline-flex rounded-xl border border-line bg-surface p-1">
      @foreach (['all' => 'All', 'movies' => 'Movies', 'series' => 'Series'] as $key => $label)
        <a href="{{ route('browse', array_filter(['genre' => $genre, 'type' => $key === 'all' ? null : $key, 'q' => $query])) }}"
          class="rounded-lg px-4 py-1.5 text-sm font-semibold transition {{ $type === $key ? 'bg-brand text-white shadow' : 'text-fg-soft hover:text-fg' }}">{{ $label }}</a>
      @endforeach
    </div>

    @if ($genres->isNotEmpty())
      <select class="select !w-auto min-w-44"
        onchange="
          var params = new URLSearchParams();
          @if($type !== 'all') params.set('type', '{{ $type }}'); @endif
          @if($query) params.set('q', @js($query)); @endif
          if (this.value) params.set('genre', this.value);
          window.location = '{{ route('browse') }}' + (params.toString() ? '?' + params.toString() : '');
        ">
        <option value="">All genres</option>
        @foreach ($genres as $g)
          <option value="{{ $g }}" @selected($genre === $g)>{{ $g }}</option>
        @endforeach
      </select>
    @endif
  </div>

  @php
    $ssrItems = $paged;
    $hasResults = $ssrItems->isNotEmpty() || $comingSoon->isNotEmpty();
  @endphp

  @if (!$hasResults)
    <x-ui.empty icon="search" :title="$query ? 'No results for “' . $query . '”' : 'Nothing found'" text="Try a different keyword or clear your filters.">
      <a href="{{ route('browse') }}" class="btn btn-primary">Reset filters</a>
    </x-ui.empty>
  @else
    <div x-data="browsePager({
        hasMore: @json($hasMore),
        nextPage: 2,
        baseUrl: '{{ route('browse', array_filter(['type' => $type !== 'all' ? $type : null, 'genre' => $genre, 'q' => $query])) }}'
      })">

      <div class="poster-grid">
        @foreach ($ssrItems as $entry)
          @php $item = $entry['item']; $isMovie = $entry['type'] === 'movie'; @endphp
          <x-film-card :film="$item" :type="$isMovie ? 'movie' : 'series'" />
        @endforeach

        <template x-for="item in loadedItems" :key="item.url">
          <a :href="item.url" class="group block">
            <div class="poster-frame transition duration-300 group-hover:-translate-y-1 group-hover:shadow-pop">
              <template x-if="item.thumbnailUrl">
                <img :src="item.thumbnailUrl" :alt="item.title" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
              </template>
              <template x-if="!item.thumbnailUrl">
                <div class="grid size-full place-items-center text-muted"><x-ui.icon name="film" class="size-10" /></div>
              </template>
              <div class="absolute left-2 top-2 flex gap-1">
                <template x-if="item.type === 'series'"><span class="badge bg-black/60 text-white backdrop-blur">Series</span></template>
                <template x-if="item.isFree"><span class="badge bg-emerald-500 text-white">Free</span></template>
              </div>
            </div>
            <div class="mt-3 px-0.5">
              <h3 class="truncate text-sm font-semibold text-fg transition-colors group-hover:text-brand" x-text="item.title"></h3>
              <p class="mt-0.5 truncate text-xs text-muted" x-text="[item.genre, item.year].filter(Boolean).join(' · ')"></p>
            </div>
          </a>
        </template>
      </div>

      {{-- Loading skeleton --}}
      <div x-show="loading" x-cloak class="poster-grid mt-8">
        @for ($i = 0; $i < 6; $i++)
          <div class="animate-pulse">
            <div class="aspect-[2/3] rounded-xl bg-surface-3"></div>
            <div class="mt-3 h-3.5 w-4/5 rounded bg-surface-3"></div>
            <div class="mt-2 h-3 w-2/5 rounded bg-surface-3"></div>
          </div>
        @endfor
      </div>

      <p x-show="!hasMore && !loading && loadedItems.length > 0" x-cloak class="mt-10 text-center text-xs font-semibold uppercase tracking-widest text-muted">— End of results —</p>
      <div x-ref="sentinel" class="mt-8 h-1"></div>
    </div>

    @if ($comingSoon->isNotEmpty())
      <section class="mt-16">
        <h2 class="section-title mb-6 border-b border-line pb-3">Coming soon</h2>
        <div class="poster-grid">
          @foreach ($comingSoon as $cs)
            <x-coming-soon-card :film="$cs" />
          @endforeach
        </div>
      </section>
    @endif
  @endif
</div>
@endsection

@push('scripts')
<script>
function browsePager({ hasMore, nextPage, baseUrl }) {
  return {
    hasMore,
    nextPage,
    baseUrl,
    loading: false,
    loadedItems: [],
    observer: null,

    init() {
      if (!this.hasMore) return;
      this.observer = new IntersectionObserver(
        (entries) => {
          if (entries[0].isIntersecting && this.hasMore && !this.loading) {
            this.loadMore();
          }
        },
        { rootMargin: '300px' }
      );
      this.$nextTick(() => {
        if (this.$refs.sentinel) this.observer.observe(this.$refs.sentinel);
      });
    },

    async loadMore() {
      if (this.loading || !this.hasMore) return;
      this.loading = true;

      try {
        const url = new URL(this.baseUrl, window.location.origin);
        url.searchParams.set('page', this.nextPage);

        const res = await fetch(url.toString(), {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (!res.ok) throw new Error('Request failed');
        const data = await res.json();

        this.loadedItems.push(...data.items);
        this.hasMore = data.hasMore;
        this.nextPage++;
      } catch (e) {
        console.error('Browse load error', e);
      } finally {
        this.loading = false;
      }
    },
  };
}
</script>
@endpush
