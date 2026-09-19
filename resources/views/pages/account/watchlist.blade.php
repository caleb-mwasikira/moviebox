@extends('layouts.account')

@section('account-title', 'Watch Later')

@section('account-content')
<div class="card">
  <div class="card-header"><div><h2 class="card-title">Watch later</h2><p class="mt-0.5 text-sm text-muted">Titles you've saved to watch.</p></div></div>

  @if ($items->isEmpty())
    <div class="p-6">
      <x-ui.empty icon="bookmark" title="Your watch later list is empty" text="Save movies and series to find them here.">
        <a href="{{ route('movies') }}" class="btn btn-primary">Browse movies</a>
        <a href="{{ route('series') }}" class="btn btn-secondary">Browse series</a>
      </x-ui.empty>
    </div>
  @else
    <ul class="divide-y divide-line">
      @foreach ($items as $item)
        @php
          $content  = $item->watchable;
          $isSeries = $content instanceof \App\Models\Series;
          $route    = $isSeries ? route('series.show', $content) : route('movies.show', $content);
          $poster   = $content->posters->first()?->url() ?? ($content->thumbnail ? $content->thumbnailUrl() : null);
          $type     = $isSeries ? 'series' : 'movie';
        @endphp
        <li x-data="watchlistToggle(true, '{{ route('watchlist.toggle', [$type, $content->id]) }}')" x-show="watchlisted" x-transition class="group flex items-center gap-4 px-5 py-4 sm:px-6">
          <a href="{{ $route }}" class="block h-20 w-14 shrink-0 overflow-hidden rounded-lg bg-surface-3">
            @if ($poster)
              <img src="{{ $poster }}" alt="{{ $content->title }}" class="size-full object-cover transition group-hover:opacity-80">
            @else
              <span class="grid size-full place-items-center text-muted"><x-ui.icon name="film" /></span>
            @endif
          </a>
          <a href="{{ $route }}" class="min-w-0 flex-1">
            <p class="truncate font-semibold text-fg">{{ $content->title }}</p>
            <p class="mt-0.5 text-xs text-muted">
              {{ $isSeries ? 'Series' : 'Movie' }}@if ($content->year) · {{ $content->year }}@endif
              @if ($content->genre)<span class="hidden sm:inline"> · {{ $content->genre }}</span>@endif
            </p>
          </a>
          <div class="flex shrink-0 items-center gap-2">
            <a href="{{ $route }}" class="btn btn-primary btn-sm"><x-ui.icon name="play" class="size-3.5" /><span class="hidden sm:inline">{{ $isSeries ? 'View' : 'Watch' }}</span></a>
            <button type="button" @click="toggle()" class="btn btn-ghost btn-icon hover:!text-red-500" title="Remove from watch later"><x-ui.icon name="x" class="size-4" /></button>
          </div>
        </li>
      @endforeach
    </ul>
  @endif
</div>
@endsection
