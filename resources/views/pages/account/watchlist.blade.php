@extends('layouts.account')

@section('account-title', 'Watch Later')

@section('account-content')
<div class="bg-white shadow-3xl p-4 sm:p-8 md:p-10 lg:p-14 dark:bg-gray-700">

  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white mb-1">Watch Later</h2>
  <p class="text-sm text-gray-500 dark:text-gray mb-8">
    Titles you've saved to watch.
  </p>

  @if ($items->isEmpty())
    <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
      <svg class="h-10 w-10 mx-auto mb-4 text-gray-300 dark:text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
      </svg>
      <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">Your watch later list is empty</p>
      <div class="mt-4 flex justify-center gap-x-3">
        <a href="{{ route('movies') }}" class="text-xs font-bold uppercase text-accent hover:underline">Browse Movies</a>
        <span class="text-gray-300">·</span>
        <a href="{{ route('series') }}" class="text-xs font-bold uppercase text-accent hover:underline">Browse Series</a>
      </div>
    </div>
  @else
    <div class="flex flex-col divide-y divide-gray-100 dark:divide-gray-700">
      @foreach ($items as $item)
        @php
          $content  = $item->watchable;
          $isSeries = $content instanceof \App\Models\Series;
          $route    = $isSeries
              ? route('series.show', $content)
              : route('movies.show', $content);
          $poster   = $content->posters->first()?->url() ?? ($content->thumbnail ? $content->thumbnailUrl() : null);
          $type     = $isSeries ? 'series' : 'movie';
        @endphp

        <div x-data="watchlistToggle(true, '{{ route('watchlist.toggle', [$type, $content->id]) }}')"
          x-show="watchlisted"
          class="group flex items-center gap-x-3 sm:gap-x-4 py-3 sm:py-4">

          <a href="{{ $route }}" class="shrink-0 w-12 h-16 sm:w-14 sm:h-20 bg-gray-100 dark:bg-gray-600 overflow-hidden">
            @if ($poster)
              <img src="{{ $poster }}" alt="{{ $content->title }}"
                class="w-full h-full object-cover group-hover:opacity-80 transition-opacity">
            @else
              <div class="w-full h-full flex items-center justify-center">
                <svg class="h-5 w-5 fill-gray-400" viewBox="0 0 24 24"><path d="M4 2h16a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/></svg>
              </div>
            @endif
          </a>

          <div class="flex-1 min-w-0">
            <a href="{{ $route }}" class="block">
              <p class="text-sm font-bold text-gray-900 dark:text-white leading-tight truncate">{{ $content->title }}</p>
              <p class="text-xs text-gray-500 mt-0.5">
                {{ $isSeries ? 'Series' : 'Movie' }}
                @if ($content->year) · {{ $content->year }} @endif
                @if ($content->genre) <span class="hidden sm:inline">· {{ $content->genre }}</span> @endif
              </p>
            </a>
          </div>

          <div class="shrink-0 flex items-center gap-x-2">
            <a href="{{ $route }}"
              class="inline-flex items-center justify-center gap-x-1.5 font-bold leading-none uppercase py-2 px-3 sm:px-4 text-xs text-gray-900 bg-accent hover:bg-accent transition-colors">
              <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              <span class="hidden sm:inline">{{ $isSeries ? 'View' : 'Watch' }}</span>
            </a>
            <button type="button" @click="toggle()" :disabled="busy"
              class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors"
              title="Remove from watch later">
              <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
          </div>

        </div>
      @endforeach
    </div>
  @endif

</div>
@endsection
