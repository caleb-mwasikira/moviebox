@extends('layouts.account')

@section('account-title', 'Continue Watching')

@section('account-content')
<div class="card">
  <div class="card-header"><div><h2 class="card-title">Continue watching</h2><p class="mt-0.5 text-sm text-muted">Pick up where you left off.</p></div></div>

  @if ($items->isEmpty())
    <div class="p-6">
      <x-ui.empty icon="play" title="Nothing in progress" text="Start watching a movie or series to see it here.">
        <a href="{{ route('movies') }}" class="btn btn-primary">Browse movies</a>
      </x-ui.empty>
    </div>
  @else
    <ul class="divide-y divide-line">
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
        <li>
          <a href="{{ $watchRoute }}" class="group flex items-center gap-4 px-5 py-4 transition hover:bg-surface-2/60 sm:px-6">
            <span class="relative h-20 w-14 shrink-0 overflow-hidden rounded-lg bg-surface-3">
              @if ($thumb)<img src="{{ $thumb }}" alt="" class="size-full object-cover">@endif
              <span class="absolute inset-x-0 bottom-0 h-1 bg-black/40"><span class="block h-full bg-brand" style="width: {{ $pct }}%"></span></span>
            </span>
            <span class="min-w-0 flex-1">
              <span class="block truncate font-semibold text-fg transition-colors group-hover:text-brand">{{ $title }}</span>
              @if ($subtitle)<span class="block truncate text-xs text-muted">{{ $subtitle }}</span>@endif
              <span class="mt-2 flex items-center gap-3">
                <span class="progress max-w-56 flex-1"><span style="width: {{ $pct }}%"></span></span>
                <span class="text-xs font-semibold tabular-nums text-muted">{{ $pct }}%</span>
              </span>
              <span class="mt-1 block text-[11px] text-muted">{{ $item->watched_at->diffForHumans() }}</span>
            </span>
            <span class="btn btn-secondary btn-sm hidden shrink-0 sm:inline-flex"><x-ui.icon name="play" class="size-3.5" /> Continue</span>
            <x-ui.icon name="chevron-right" class="size-5 shrink-0 text-muted sm:hidden" />
          </a>
        </li>
      @endforeach
    </ul>
  @endif
</div>
@endsection
