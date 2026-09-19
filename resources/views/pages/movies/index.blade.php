@extends('layouts.app')

@section('title', 'Movies')

@section('content')
<x-hero :featured="$featured" />

<div class="container-app py-10 lg:py-14">
  <x-ui.page-header eyebrow="Catalogue" title="Movies" subtitle="Blockbusters, classics and hidden gems — ready to stream." />

  <x-ui.catalog-filters route="movies" :genres="$genres" />

  @if ($movies->isEmpty())
    <x-ui.empty icon="film" title="No movies found" text="Try a different genre or filter.">
      <a href="{{ route('movies') }}" class="btn btn-primary">Show all movies</a>
    </x-ui.empty>
  @else
    <div class="poster-grid">
      @foreach ($movies as $movie)
        <a href="{{ route('movies.show', $movie) }}" class="group block">
          <div class="poster-frame transition duration-300 group-hover:-translate-y-1 group-hover:shadow-pop">
            @if ($movie->thumbnail)
              <img src="{{ $movie->thumbnailUrl() }}" alt="{{ $movie->title }}" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
            @else
              <div class="grid size-full place-items-center text-muted"><x-ui.icon name="film" class="size-10" /></div>
            @endif
            @if ($movie->is_free)<span class="badge absolute left-2 top-2 bg-emerald-500 text-white">Free</span>@endif
            <span class="absolute inset-0 grid place-items-center opacity-0 transition duration-300 group-hover:opacity-100">
              <span class="grid size-12 place-items-center rounded-full bg-brand text-white shadow-lg"><x-ui.icon name="play" class="ml-0.5 size-5" /></span>
            </span>
          </div>
          <div class="mt-3 px-0.5">
            <h3 class="truncate text-sm font-semibold text-fg transition-colors group-hover:text-brand">{{ $movie->title }}</h3>
            @php $meta = array_filter([$movie->genre, $movie->year, $movie->duration ? $movie->duration . ' min' : null]); @endphp
            <p class="mt-0.5 truncate text-xs text-muted">{{ implode(' · ', $meta) }}</p>
          </div>
        </a>
      @endforeach
    </div>
    <div class="mt-12">{{ $movies->links() }}</div>
  @endif
</div>
@endsection
