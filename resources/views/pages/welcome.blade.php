@extends('layouts.app')

@section('title', 'Home')

@section('content')

{{-- ── Hero carousel ─────────────────────────────────────────── --}}
<x-hero :featured="$featured" />

<div class="container-app space-y-16 pt-12 lg:space-y-20 lg:pt-16">

  {{-- ── Series ────────────────────────────────────────────── --}}
  @if ($series->isNotEmpty())
    <section>
      <div class="mb-6 flex items-end justify-between gap-4">
        <div><p class="eyebrow mb-1.5">Binge-worthy</p><h2 class="section-title">Popular series</h2></div>
        <a href="{{ route('series') }}" class="btn btn-ghost btn-sm">View all <x-ui.icon name="arrow-right" class="size-4" /></a>
      </div>
      <div class="rail">
        @foreach ($series as $show)
          <x-film-card :film="$show" type="series" class="w-36 sm:w-44 lg:w-48" />
        @endforeach
      </div>
    </section>
  @endif

  {{-- ── Trailer spotlight ─────────────────────────────────── --}}
  @if ($trailerFeature)
    @php $poster = $trailerFeature->posters->first(); $img = $poster?->url() ?? $trailerFeature->thumbnailUrl(); @endphp
    <section class="card overflow-hidden">
      <div class="grid lg:grid-cols-5">
        <button type="button" onclick="openTrailerModal('{{ $trailerFeature->trailerUrl() }}')"
          class="group relative block aspect-video overflow-hidden bg-neutral-900 lg:col-span-3 lg:aspect-auto lg:min-h-[22rem]" aria-label="Play trailer">
          @if ($img)<img src="{{ $img }}" alt="{{ $trailerFeature->title }}" loading="lazy" class="absolute inset-0 size-full object-cover transition duration-700 group-hover:scale-105">@endif
          <span class="absolute inset-0 bg-black/30 transition group-hover:bg-black/40"></span>
          <span class="absolute inset-0 grid place-items-center">
            <span class="grid size-20 place-items-center rounded-full bg-brand text-white shadow-[0_0_0_10px_rgb(255_255_255/0.12)] transition group-hover:scale-110"><x-ui.icon name="play" class="ml-1 size-8" /></span>
          </span>
        </button>
        <div class="flex flex-col justify-center p-6 sm:p-10 lg:col-span-2">
          <p class="eyebrow">Trailer spotlight</p>
          <h2 class="mt-3 text-3xl font-bold text-fg">{{ $trailerFeature->title }}</h2>
          <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="badge badge-brand">{{ $trailerFeature->content_type === 'series' ? 'Series' : 'Movie' }}</span>
            @if ($trailerFeature->year)<span class="text-xs text-muted">{{ $trailerFeature->year }}</span>@endif
            @if ($trailerFeature->genre)<span class="text-xs text-muted">· {{ $trailerFeature->genre }}</span>@endif
          </div>
          @if ($trailerFeature->description)<p class="mt-4 line-clamp-4 text-sm leading-relaxed text-muted">{{ $trailerFeature->description }}</p>@endif
          <div class="mt-7 flex flex-wrap gap-3">
            <button type="button" onclick="openTrailerModal('{{ $trailerFeature->trailerUrl() }}')" class="btn btn-primary"><x-ui.icon name="play" class="size-4" /> Watch trailer</button>
            @if ($trailerFeature->content_type === 'series')
              <a href="{{ route('series.show', $trailerFeature->uuid) }}" class="btn btn-secondary">View series</a>
            @elseif ($trailerFeature->streamingUrl())
              <a href="{{ route('movies.show', $trailerFeature->uuid) }}" class="btn btn-secondary">View movie</a>
            @endif
          </div>
        </div>
      </div>
    </section>
  @endif

  {{-- ── Movies ────────────────────────────────────────────── --}}
  <section>
    <div class="mb-6 flex items-end justify-between gap-4">
      <div><p class="eyebrow mb-1.5">Handpicked</p><h2 class="section-title">Movies for you</h2></div>
      <a href="{{ route('movies') }}" class="btn btn-ghost btn-sm">View all <x-ui.icon name="arrow-right" class="size-4" /></a>
    </div>
    @if ($movies->isNotEmpty())
      <div class="rail">
        @foreach ($movies as $movie)
          <x-film-card :film="$movie" class="w-36 sm:w-44 lg:w-48" />
        @endforeach
      </div>
    @else
      <x-ui.empty icon="film" title="No movies yet" text="Check back soon — new titles are added regularly." />
    @endif
  </section>

  {{-- ── Coming soon ───────────────────────────────────────── --}}
  @if ($comingSoon->isNotEmpty())
    <section>
      <div class="mb-6 flex items-end justify-between gap-4">
        <div><p class="eyebrow mb-1.5">On the horizon</p><h2 class="section-title">Coming soon</h2></div>
        <a href="{{ route('coming-soon') }}" class="btn btn-ghost btn-sm">View all <x-ui.icon name="arrow-right" class="size-4" /></a>
      </div>
      <div class="rail">
        @foreach ($comingSoon as $cs)
          <x-coming-soon-card :film="$cs" class="w-36 sm:w-44 lg:w-48" />
        @endforeach
      </div>
    </section>
  @endif

  {{-- ── Collections ───────────────────────────────────────── --}}
  @if (isset($collections) && $collections->isNotEmpty())
    @foreach ($collections as $collection)
      <section>
        <div class="mb-6 flex items-end justify-between gap-4">
          <div>
            <p class="eyebrow mb-1.5">Collection</p>
            <h2 class="section-title">{{ $collection->name }}</h2>
            @if ($collection->description)<p class="mt-1.5 max-w-2xl text-sm text-muted">{{ $collection->description }}</p>@endif
          </div>
          <a href="{{ route('collections.show', $collection) }}" class="btn btn-ghost btn-sm">See all <x-ui.icon name="arrow-right" class="size-4" /></a>
        </div>
        <div class="rail">
          @foreach ($collection->items as $item)
            @if ($item->collectible)
              @php $type = $item->collectible_type === \App\Models\Movie::class ? 'movie' : 'series'; @endphp
              <x-film-card :film="$item->collectible" :type="$type" class="w-36 sm:w-44 lg:w-48" />
            @endif
          @endforeach
        </div>
      </section>
    @endforeach
  @endif

  {{-- ── CTA ───────────────────────────────────────────────── --}}
  <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand to-fuchsia-600 p-8 text-white sm:p-12">
    <div class="absolute -right-16 -top-16 size-64 rounded-full bg-white/10 blur-2xl"></div>
    <div class="relative flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/70">Unlimited access from</p>
        <h2 class="mt-2 text-3xl font-extrabold !text-white sm:text-4xl">
          @if ($cheapestPackage)
            {{ $cheapestPackage->currency ?? '$' }}{{ number_format($cheapestPackage->price, 2) }} <span class="text-xl font-semibold text-white/80">/ {{ $cheapestPackage->interval ?? 'month' }}</span>
          @else
            Subscribe now
          @endif
        </h2>
        <p class="mt-2 max-w-md text-white/80">Stream everything on every device. Cancel anytime.</p>
      </div>
      <div class="flex flex-wrap gap-3">
        @auth
          <a href="{{ route('pricing') }}" class="btn btn-light btn-lg">View plans</a>
          <a href="{{ route('movies') }}" class="btn btn-glass btn-lg">Browse movies</a>
        @else
          <a href="{{ route('register') }}" class="btn btn-light btn-lg">Get started</a>
          <a href="{{ route('pricing') }}" class="btn btn-glass btn-lg">View plans</a>
        @endauth
      </div>
    </div>
  </section>
</div>
@endsection
