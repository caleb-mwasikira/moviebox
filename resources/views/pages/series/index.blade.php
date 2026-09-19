@extends('layouts.app')

@section('title', 'Series')

@section('content')
<x-hero :featured="$featured" />

<div class="container-app py-10 lg:py-14">
  <x-ui.page-header eyebrow="Catalogue" title="Series" subtitle="Start a new obsession — every season, every episode." />

  <x-ui.catalog-filters route="series" :genres="$genres" />

  @if ($series->isEmpty())
    <x-ui.empty icon="tv" title="No series found" text="Try a different genre or filter.">
      <a href="{{ route('series') }}" class="btn btn-primary">Show all series</a>
    </x-ui.empty>
  @else
    <div class="poster-grid">
      @foreach ($series as $show)
        <a href="{{ route('series.show', $show) }}" class="group block">
          <div class="poster-frame transition duration-300 group-hover:-translate-y-1 group-hover:shadow-pop">
            @if ($show->thumbnail)
              <img src="{{ $show->thumbnailUrl() }}" alt="{{ $show->title }}" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
            @else
              <div class="grid size-full place-items-center text-muted"><x-ui.icon name="tv" class="size-10" /></div>
            @endif
            <div class="absolute left-2 top-2 flex gap-1">
              <span class="badge bg-black/60 text-white backdrop-blur">Series</span>
              @if (!empty($show->free_episodes_count) && $show->free_episodes_count > 0)<span class="badge bg-emerald-500 text-white">Free eps</span>@endif
            </div>
          </div>
          <div class="mt-3 px-0.5">
            <h3 class="truncate text-sm font-semibold text-fg transition-colors group-hover:text-brand">{{ $show->title }}</h3>
            @php $meta = array_filter([$show->genre, $show->year]); @endphp
            <p class="mt-0.5 truncate text-xs text-muted">{{ implode(' · ', $meta) }}</p>
          </div>
        </a>
      @endforeach
    </div>
    <div class="mt-12">{{ $series->links() }}</div>
  @endif
</div>
@endsection
