@extends('layouts.account')

@section('account-title', 'Continue Watching')

@section('account-content')
  <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Continue Watching</h2>

  @if ($items->isEmpty())
    <p class="text-gray-500 dark:text-gray-400 text-sm">
      You haven't started watching anything yet.
      <a href="{{ route('movies') }}" class="text-accent font-bold hover:underline">Browse movies</a>
    </p>
  @else
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
            <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Title</th>
            <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Progress</th>
            <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Last watched</th>
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

            <tr>
              <td class="py-4 pr-4">
                <div class="flex items-center gap-x-3">
                  @if ($thumb)
                    <img src="{{ $thumb }}" alt="" class="w-10 h-14 object-cover shrink-0">
                  @else
                    <div class="w-10 h-14 bg-gray-100 dark:bg-gray-700 shrink-0"></div>
                  @endif
                  <div>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $title }}</span>
                    @if ($subtitle)
                      <p class="text-xs text-gray-500 mt-0.5">{{ $subtitle }}</p>
                    @endif
                  </div>
                </div>
              </td>
              <td class="py-4 pr-4">
                <div class="flex items-center gap-x-3 min-w-[120px]">
                  <div class="flex-1 bg-gray-200 dark:bg-gray-700 h-1.5 overflow-hidden rounded-full">
                    <div class="h-full bg-accent rounded-full" style="width: {{ $pct }}%"></div>
                  </div>
                  <span class="text-xs text-gray-500 shrink-0">{{ $pct }}%</span>
                </div>
              </td>
              <td class="py-4 pr-4 text-gray-500 dark:text-gray-400 text-xs">
                {{ $item->watched_at->diffForHumans() }}
              </td>
              <td class="py-4 text-right">
                <a href="{{ $watchRoute }}"
                   class="font-bold text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors text-xs uppercase">
                  Continue
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
@endsection
