@extends('layouts.account')

@section('account-title', 'Continue Watching')

@section('account-content')
<div class="bg-white shadow-3xl p-4 sm:p-8 md:p-10 lg:p-14 dark:bg-gray-700">

  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white mb-1">Continue Watching</h2>
  <p class="text-sm text-gray-500 dark:text-gray mb-8">
    Pick up where you left off.
  </p>

  @if ($items->isEmpty())
    <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
      <svg class="mx-auto mb-3 w-10 h-10 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
        <path d="M8 5v14l11-7z"/>
      </svg>
      <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">Nothing in progress</p>
      <p class="text-xs text-gray-400 mt-1 mb-4">Start watching a movie or series to see it here.</p>
      <a href="{{ route('movies') }}"
         class="inline-block text-xs font-bold uppercase px-5 py-2.5 bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
        Browse Movies
      </a>
    </div>
  @else

    {{-- ── Mobile cards (< md) ── --}}
    <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700">
      @foreach ($items as $item)
        @php
          $isMovie   = $item->watchable_type === 'movie';
          $watchable = $item->watchable;
          if ($isMovie) {
            $title      = $watchable->title;
            $subtitle   = null;
            $thumb      = $watchable->posters->first()?->url() ?? $watchable->thumbnailUrl();
            $watchRoute = route('movies.watch', $watchable);
          } else {
            $series     = $watchable->season->series;
            $title      = $series->title;
            $subtitle   = $watchable->label();
            $thumb      = $series->posters->first()?->url() ?? $series->thumbnailUrl();
            $watchRoute = route('series.watch', [$series, $watchable]);
          }
          $pct = $item->percentComplete();
        @endphp

        <a href="{{ $watchRoute }}" class="flex gap-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
          {{-- Thumbnail with progress bar --}}
          <div class="relative shrink-0 w-14 h-20 bg-gray-100 dark:bg-gray-800 overflow-hidden">
            @if ($thumb)
              <img src="{{ $thumb }}" alt="" class="w-full h-full object-cover">
            @endif
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/30">
              <div class="h-full bg-accent" style="width: {{ $pct }}%"></div>
            </div>
          </div>

          {{-- Info --}}
          <div class="flex-1 min-w-0 flex flex-col justify-center py-1">
            <p class="font-bold text-gray-900 dark:text-white text-sm leading-tight truncate group-hover:text-accent transition-colors">
              {{ $title }}
            </p>
            @if ($subtitle)
              <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $subtitle }}</p>
            @endif
            <div class="flex items-center gap-x-2 mt-2">
              <div class="flex-1 bg-gray-200 dark:bg-gray-700 h-1 rounded-full overflow-hidden">
                <div class="h-full bg-accent rounded-full" style="width: {{ $pct }}%"></div>
              </div>
              <span class="text-[10px] font-bold text-gray-400 tabular-nums shrink-0">{{ $pct }}%</span>
            </div>
            <p class="text-[10px] text-gray-400 mt-1">{{ $item->watched_at->diffForHumans() }}</p>
          </div>

          {{-- Arrow --}}
          <div class="shrink-0 flex items-center pl-1">
            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-accent transition-colors" fill="currentColor" viewBox="0 0 24 24">
              <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/>
            </svg>
          </div>
        </a>
      @endforeach
    </div>

    {{-- ── Desktop table (≥ md) ── --}}
    <div class="hidden md:block">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
            <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Title</th>
            <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Progress</th>
            <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4 hidden lg:table-cell">Last Watched</th>
            <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
          @foreach ($items as $item)
            @php
              $isMovie   = $item->watchable_type === 'movie';
              $watchable = $item->watchable;
              if ($isMovie) {
                $title      = $watchable->title;
                $subtitle   = null;
                $thumb      = $watchable->posters->first()?->url() ?? $watchable->thumbnailUrl();
                $watchRoute = route('movies.watch', $watchable);
              } else {
                $series     = $watchable->season->series;
                $title      = $series->title;
                $subtitle   = $watchable->label();
                $thumb      = $series->posters->first()?->url() ?? $series->thumbnailUrl();
                $watchRoute = route('series.watch', [$series, $watchable]);
              }
              $pct = $item->percentComplete();
            @endphp

            <tr class="group">
              <td class="py-4 pr-4">
                <div class="flex items-center gap-x-3">
                  <div class="relative shrink-0 w-10 h-14 bg-gray-100 dark:bg-gray-700 overflow-hidden">
                    @if ($thumb)
                      <img src="{{ $thumb }}" alt="" class="w-full h-full object-cover">
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/30">
                      <div class="h-full bg-accent" style="width: {{ $pct }}%"></div>
                    </div>
                  </div>
                  <div class="min-w-0">
                    <p class="font-bold text-gray-900 dark:text-white truncate max-w-[200px] lg:max-w-xs">{{ $title }}</p>
                    @if ($subtitle)
                      <p class="text-xs text-gray-500 mt-0.5">{{ $subtitle }}</p>
                    @endif
                  </div>
                </div>
              </td>
              <td class="py-4 pr-4">
                <div class="flex items-center gap-x-3 w-32 lg:w-44">
                  <div class="flex-1 bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                    <div class="h-full bg-accent rounded-full" style="width: {{ $pct }}%"></div>
                  </div>
                  <span class="text-xs font-bold text-gray-500 tabular-nums shrink-0 w-8 text-right">{{ $pct }}%</span>
                </div>
              </td>
              <td class="py-4 pr-4 text-gray-500 dark:text-gray-400 text-xs whitespace-nowrap hidden lg:table-cell">
                {{ $item->watched_at->diffForHumans() }}
              </td>
              <td class="py-4 text-right">
                <a href="{{ $watchRoute }}"
                   class="inline-flex items-center gap-x-1.5 font-bold text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors text-xs uppercase group-hover:text-accent">
                  <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                  Continue
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  @endif

</div>
@endsection
