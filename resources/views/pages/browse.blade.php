@extends('layouts.app')

@section('title', 'Browse')

@push('head')
<style>
  @keyframes skeleton-pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
  .skeleton-pulse { animation: skeleton-pulse 1.4s ease-in-out infinite; }
</style>
@endpush

@section('content')
  <main id="main-content" class="grow lg:pt-0">
    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        {{-- Header + filters --}}
        <div class="flex flex-col gap-y-6 sm:flex-row sm:items-center sm:justify-between mb-12 md:mb-16 lg:mb-20">
          <div>
            <h1 class="text-2xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3xl md:text-4xl">
              @if ($query) Search Results @else Browse All @endif
            </h1>
            @if ($query)
              <p class="mt-1 text-sm text-gray-500 flex items-center gap-x-2 flex-wrap">
                Results for <span class="font-bold text-gray-900 dark:text-white">"{{ $query }}"</span>
                <a href="{{ route('browse', array_filter(['type' => $type !== 'all' ? $type : null, 'genre' => $genre])) }}"
                   class="inline-flex items-center gap-x-1 text-xs font-bold uppercase tracking-wide text-gray-400 hover:text-accent transition-colors border border-gray-300 dark:border-gray-600 hover:border-accent px-2 py-0.5">
                  <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                  Clear
                </a>
              </p>
            @endif
          </div>

          <div class="flex flex-wrap gap-x-6 gap-y-3 items-center">
            <nav>
              <ul class="flex flex-wrap gap-x-5 text-sm font-bold">
                <li>
                  <a class="transition-colors {{ $type === 'all' ? 'text-accent' : 'text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent' }}"
                    href="{{ route('browse', array_filter(['genre' => $genre, 'q' => $query])) }}">All</a>
                </li>
                <li>
                  <a class="transition-colors {{ $type === 'movies' ? 'text-accent' : 'text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent' }}"
                    href="{{ route('browse', array_filter(['genre' => $genre, 'type' => 'movies', 'q' => $query])) }}">Movies</a>
                </li>
                <li>
                  <a class="transition-colors {{ $type === 'series' ? 'text-accent' : 'text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent' }}"
                    href="{{ route('browse', array_filter(['genre' => $genre, 'type' => 'series', 'q' => $query])) }}">Series</a>
                </li>
              </ul>
            </nav>

            @if ($genres->isNotEmpty())
            <select
              id="genre-filter"
              onchange="
                var params = new URLSearchParams();
                @if($type !== 'all') params.set('type', '{{ $type }}'); @endif
                if (this.value) params.set('genre', this.value);
                window.location = '{{ route('browse') }}' + (params.toString() ? '?' + params.toString() : '');
              "
              class="border-base bg-white/40 pl-4 pr-9 h-10 font-bold text-sm text-gray-900 transition-all duration-150 bg-caret bg-[length:8px_5px] bg-[position:right_12px_center] focus:border-accent focus:outline-0 focus:ring-0 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:bg-caret-white">
              <option value="">All Genres</option>
              @foreach ($genres as $g)
                <option value="{{ $g }}" {{ $genre === $g ? 'selected' : '' }}>{{ $g }}</option>
              @endforeach
            </select>
            @endif
          </div>
        </div>

        {{-- ── Infinite-scroll container ── --}}
        @php
          $ssrItems = $paged;
          $hasResults = $ssrItems->isNotEmpty() || $comingSoon->isNotEmpty();
        @endphp

        @if (!$hasResults)
          <div class="py-24 text-center text-gray-500 font-bold uppercase text-sm tracking-widest">
            @if ($query) No results found for "{{ $query }}" @else Nothing found. @endif
          </div>
        @else

        <div
          x-data="browsePager({
            hasMore: @json($hasMore),
            nextPage: 2,
            baseUrl: '{{ route('browse', array_filter(['type' => $type !== 'all' ? $type : null, 'genre' => $genre, 'q' => $query])) }}'
          })"
        >

          {{-- Grid: SSR first page + Alpine-appended pages --}}
          <div
            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-5 gap-y-9 md:gap-x-6 lg:gap-x-7.5"
            id="browse-grid"
          >

            {{-- Server-rendered first page --}}
            @foreach ($ssrItems as $entry)
            @php $item = $entry['item']; $isMovie = $entry['type'] === 'movie'; @endphp
            <div>
              <figure class="group relative mb-4 overflow-hidden bg-gray-100 dark:bg-gray-700">
                <a href="{{ $isMovie ? route('movies.show', $item) : route('series.show', $item) }}">
                  @if ($item->thumbnailUrl())
                    <img class="aspect-[2/3] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                      src="{{ $item->thumbnailUrl() }}" alt="{{ $item->title }}" loading="lazy">
                  @else
                    <div class="aspect-[2/3] w-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                      <svg class="h-12 w-12 fill-gray-400" viewBox="0 0 24 24"><path d="M4 2h16a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/></svg>
                    </div>
                  @endif
                  @if (!$isMovie)
                    <div class="absolute top-2 left-2">
                      <span class="bg-primary text-white text-[10px] font-bold uppercase px-2 py-0.5">Series</span>
                    </div>
                  @endif
                  @if ($isMovie && !empty($item->is_free))
                    <div class="absolute top-2 right-2">
                      <span class="bg-green-500 text-white text-[10px] font-bold uppercase px-2 py-0.5 tracking-wider">Free</span>
                    </div>
                  @endif
                  <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-accent/80 duration-300">
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-10 aspect-square bg-gray-900 grid place-items-center text-white scale-90 group-hover:scale-100 transition-all duration-300">
                      <svg class="h-4 w-4 fill-white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                  </div>
                </a>
              </figure>
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white text-sm leading-tight mb-1">
                  <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                    href="{{ $isMovie ? route('movies.show', $item) : route('series.show', $item) }}">{{ $item->title }}</a>
                </h3>
                <div class="text-xs text-gray-500 leading-snug">
                  {{ implode(' · ', array_filter([$item->genre, $item->year])) }}
                </div>
              </div>
            </div>
            @endforeach

            {{-- Alpine-appended pages --}}
            <template x-for="item in loadedItems" :key="item.url">
              <div>
                <figure class="group relative mb-4 overflow-hidden bg-gray-100 dark:bg-gray-700">
                  <a :href="item.url">
                    <template x-if="item.thumbnailUrl">
                      <img class="aspect-[2/3] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                        :src="item.thumbnailUrl" :alt="item.title" loading="lazy">
                    </template>
                    <template x-if="!item.thumbnailUrl">
                      <div class="aspect-[2/3] w-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                        <svg class="h-12 w-12 fill-gray-400" viewBox="0 0 24 24"><path d="M4 2h16a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/></svg>
                      </div>
                    </template>
                    <template x-if="item.type === 'series'">
                      <div class="absolute top-2 left-2">
                        <span class="bg-primary text-white text-[10px] font-bold uppercase px-2 py-0.5">Series</span>
                      </div>
                    </template>
                    <template x-if="item.isFree">
                      <div class="absolute top-2 right-2">
                        <span class="bg-green-500 text-white text-[10px] font-bold uppercase px-2 py-0.5 tracking-wider">Free</span>
                      </div>
                    </template>
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-accent/80 duration-300">
                      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-10 aspect-square bg-gray-900 grid place-items-center text-white scale-90 group-hover:scale-100 transition-all duration-300">
                        <svg class="h-4 w-4 fill-white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                      </div>
                    </div>
                  </a>
                </figure>
                <div>
                  <h3 class="font-bold text-gray-900 dark:text-white text-sm leading-tight mb-1">
                    <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent" :href="item.url" x-text="item.title"></a>
                  </h3>
                  <div class="text-xs text-gray-500 leading-snug" x-text="[item.genre, item.year].filter(Boolean).join(' · ')"></div>
                </div>
              </div>
            </template>

          </div>{{-- /grid --}}

          {{-- Loading skeleton --}}
          <div
            x-show="loading"
            x-transition:enter="transition-opacity duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-5 gap-y-9 md:gap-x-6 lg:gap-x-7.5 mt-9"
          >
            @for ($i = 0; $i < 10; $i++)
            <div class="skeleton-pulse">
              <div class="aspect-[2/3] w-full bg-gray-200 dark:bg-gray-700 mb-4"></div>
              <div class="h-3.5 bg-gray-200 dark:bg-gray-700 rounded mb-2 w-4/5"></div>
              <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-2/5"></div>
            </div>
            @endfor
          </div>

          {{-- End-of-list message --}}
          <div
            x-show="!hasMore && !loading && loadedItems.length > 0"
            class="mt-10 text-center text-xs font-bold uppercase tracking-widest text-gray-400"
          >
            — end of results —
          </div>

          {{-- Scroll sentinel --}}
          <div x-ref="sentinel" class="h-1 mt-8"></div>

        </div>{{-- /x-data --}}

        {{-- Coming Soon results (search only) --}}
        @if ($comingSoon->isNotEmpty())
        <div class="mt-16">
          <h2 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-6 pb-3 border-b border-gray-200 dark:border-gray-700">
            Coming Soon
          </h2>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-5 gap-y-9 md:gap-x-6 lg:gap-x-7.5">
            @foreach ($comingSoon as $cs)
            @php $csThumb = $cs->posters->first()?->url() ?? $cs->thumbnailUrl(); @endphp
            <div>
              <figure class="group relative mb-4 overflow-hidden bg-gray-800">
                @if ($csThumb)
                  <img class="aspect-[2/3] w-full object-cover transition-transform duration-300 group-hover:scale-110 opacity-90"
                    src="{{ $csThumb }}" alt="{{ $cs->title }}" loading="lazy">
                @else
                  <div class="aspect-[2/3] w-full bg-gray-700 flex items-center justify-center">
                    <svg class="h-12 w-12 fill-gray-500" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                  </div>
                @endif
                <div class="absolute top-2 left-2">
                  <span style="background:var(--color-accent,#facc15); color:#111; font-size:9px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:2px 7px;">Coming Soon</span>
                </div>
                @if ($cs->trailerIsReady())
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-black/50 duration-300 flex items-center justify-center cursor-pointer"
                  onclick="document.getElementById('browse-trailer-modal').style.display='flex'; var v=document.getElementById('browse-trailer-video'); v.src='{{ $cs->trailerUrl() }}';">
                  <span class="w-10 h-10 rounded-full bg-accent/90 flex items-center justify-center">
                    <svg class="w-4 h-4 fill-white ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                  </span>
                </div>
                @endif
              </figure>
              <div>
                <h3 class="font-bold text-gray-900 dark:text-white text-sm leading-tight mb-1">{{ $cs->title }}</h3>
                <div class="text-xs text-gray-500 leading-snug">
                  {{ implode(' · ', array_filter([$cs->genre, $cs->year])) }}
                </div>
              </div>
            </div>
            @endforeach
          </div>

          {{-- Trailer modal --}}
          <div id="browse-trailer-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.92); align-items:center; justify-content:center;"
            onclick="if(event.target===this){this.style.display='none'; document.getElementById('browse-trailer-video').src='';}">
            <button onclick="this.closest('#browse-trailer-modal').style.display='none'; document.getElementById('browse-trailer-video').src='';"
              style="position:absolute; top:20px; right:24px; background:none; border:none; color:#fff; font-size:28px; line-height:1; cursor:pointer;">&#x2715;</button>
            <video id="browse-trailer-video" style="width:90vw; max-width:1100px; aspect-ratio:16/9; background:#000; display:block;" controls></video>
          </div>
        </div>
        @endif

        @endif {{-- /hasResults --}}

      </div>
    </div>
  </main>
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
