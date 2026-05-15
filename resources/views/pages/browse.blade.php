@extends('layouts.app')

@section('title', 'Browse')

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        <div class="flex flex-col gap-y-6 sm:flex-row sm:items-center sm:justify-between mb-12 md:mb-16 lg:mb-20">

          <div>
            <h1 class="text-2xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3xl md:text-4xl">
              @if ($query) Search Results @else Browse All @endif
            </h1>
            @if ($query)
              <p class="mt-1 text-sm text-gray-500">
                Results for <span class="font-bold text-gray-900 dark:text-white">"{{ $query }}"</span>
              </p>
            @endif
          </div>

          <div class="flex flex-wrap gap-x-6 gap-y-3 items-center">

            {{-- Type filter --}}
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

            {{-- Genre filter --}}
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

        @php
          $items = collect();
          foreach ($movies as $movie) {
              $items->push(['type' => 'movie', 'item' => $movie]);
          }
          foreach ($series as $show) {
              $items->push(['type' => 'series', 'item' => $show]);
          }
        @endphp

        @php
          $hasResults = $items->isNotEmpty() || $comingSoon->isNotEmpty();
        @endphp

        @if (!$hasResults)
          <div class="py-24 text-center text-gray-500 font-bold uppercase text-sm tracking-widest">
            @if ($query)
              No results found for "{{ $query }}"
            @else
              Nothing found.
            @endif
          </div>
        @else

        {{-- Movies & Series grid --}}
        @if ($items->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-5 gap-y-9 md:gap-x-6 lg:gap-x-7.5">
          @foreach ($items as $entry)
          @php $item = $entry['item']; $isMovie = $entry['type'] === 'movie'; @endphp
          <div>
            <figure class="group relative mb-4 overflow-hidden bg-gray-100 dark:bg-gray-700">
              <a href="{{ $isMovie ? route('movies.show', $item) : route('series.show', $item) }}">
                @if ($item->thumbnail)
                  <img class="aspect-[2/3] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                    src="{{ $item->thumbnailUrl() }}" alt="{{ $item->title }}">
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
                @php $meta = array_filter([$item->genre, $item->year]); @endphp
                {{ implode(' · ', $meta) }}
              </div>
            </div>
          </div>
          @endforeach
        </div>
        @endif

        {{-- Coming Soon results (search only) --}}
        @if ($comingSoon->isNotEmpty())
        <div class="{{ $items->isNotEmpty() ? 'mt-16' : '' }}">
          <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-700 uppercase tracking-widest text-xs">
            Coming Soon
          </h2>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-5 gap-y-9 md:gap-x-6 lg:gap-x-7.5">
            @foreach ($comingSoon as $cs)
            @php $csThumb = $cs->posters->first()?->url() ?? $cs->thumbnailUrl(); @endphp
            <div>
              <figure class="group relative mb-4 overflow-hidden bg-gray-800">
                @if ($csThumb)
                  <img class="aspect-[2/3] w-full object-cover transition-transform duration-300 group-hover:scale-110 opacity-90"
                    src="{{ $csThumb }}" alt="{{ $cs->title }}">
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
                  @php $meta = array_filter([$cs->genre, $cs->year]); @endphp
                  {{ implode(' · ', $meta) }}
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>

        {{-- Minimal trailer modal for browse page --}}
        <div id="browse-trailer-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.92); align-items:center; justify-content:center;"
          onclick="if(event.target===this){this.style.display='none'; document.getElementById('browse-trailer-video').src='';}">
          <button onclick="this.closest('#browse-trailer-modal').style.display='none'; document.getElementById('browse-trailer-video').src='';"
            style="position:absolute; top:20px; right:24px; background:none; border:none; color:#fff; font-size:28px; line-height:1; cursor:pointer;">&#x2715;</button>
          <video id="browse-trailer-video" style="width:90vw; max-width:1100px; aspect-ratio:16/9; background:#000; display:block;" controls></video>
        </div>
        @endif

        @endif

      </div>
    </div>

  </main>
@endsection
