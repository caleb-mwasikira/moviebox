@extends('layouts.app')

@section('title', 'Home')

@section('content')

{{-- ── Hero carousel ─────────────────────────────────────────── --}}
@if ($featured->isNotEmpty())
  <section x-data="{ i: 0, n: {{ $featured->count() }}, t: null,
      go(k) { this.i = (k + this.n) % this.n; this.restart(); },
      restart() { clearInterval(this.t); this.t = setInterval(() => this.i = (this.i + 1) % this.n, 7000); } }"
    x-init="restart()" @mouseenter="clearInterval(t)" @mouseleave="restart()"
    class="relative isolate overflow-hidden bg-neutral-950">
    <div class="relative h-[34rem] sm:h-[38rem] lg:h-[44rem]">
      @foreach ($featured as $idx => $f)
        @php
          $bgUrl = $f->posters->isNotEmpty() ? $f->posters->first()->url() : ($f->thumbnail ? $f->thumbnailUrl() : null);
          $fType = $f->content_type;
        @endphp
        <article x-show="i === {{ $idx }}" x-transition:enter="transition duration-700" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
          x-transition:leave="transition duration-500 absolute inset-0" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
          @if ($idx > 0) x-cloak @endif class="absolute inset-0">
          @if ($bgUrl)
            <img src="{{ $bgUrl }}" alt="" class="absolute inset-0 size-full object-cover object-top" @if($idx > 0) loading="lazy" @endif>
          @endif
          <div class="absolute inset-0 bg-gradient-to-r from-neutral-950 via-neutral-950/70 to-neutral-950/10"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-bg via-transparent to-neutral-950/40"></div>

          <div class="container-app relative flex h-full items-end pb-16 sm:pb-20 lg:items-center lg:pb-0">
            <div class="max-w-2xl">
              <div class="flex items-center gap-2">
                <span class="badge bg-brand text-white">
                  @if ($fType === 'coming_soon') Coming soon @elseif ($fType === 'series') Series @else Movie @endif
                </span>
                @if ($f->genre)<span class="badge bg-white/10 text-white backdrop-blur">{{ $f->genre }}</span>@endif
              </div>
              <h1 class="mt-4 text-4xl font-extrabold leading-[1.05] text-white sm:text-5xl lg:text-6xl">{{ $f->title }}</h1>
              <p class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm font-medium text-white/70">
                @if ($f->year)<span>{{ $f->year }}</span>@endif
                @if ($fType === 'movie' && $f->duration)<span>{{ $f->duration }} min</span>@endif
                @if ($fType === 'series')<span>{{ $f->seasons->count() }} {{ Str::plural('Season', $f->seasons->count()) }}</span>@endif
              </p>
              @if ($f->description)
                <p class="mt-4 line-clamp-3 max-w-xl text-base leading-relaxed text-white/75">{{ Str::limit($f->description, 200) }}</p>
              @endif

              <div class="mt-8 flex flex-wrap gap-3">
                @if ($fType === 'movie' && $f->videoIsReady())
                  <a href="{{ route('movies.watch', $f) }}" onclick="event.preventDefault(); watchOrLogin(this.href)" class="btn btn-primary btn-lg">
                    <x-ui.icon name="play" class="size-4" /> Watch now
                  </a>
                @elseif ($fType === 'series')
                  <a href="{{ route('series.show', $f) }}" class="btn btn-primary btn-lg"><x-ui.icon name="play" class="size-4" /> Watch series</a>
                @elseif ($fType === 'coming_soon')
                  <a href="{{ route('coming-soon.show', $f) }}" class="btn btn-primary btn-lg">Details</a>
                @endif
                @if ($f->trailerIsReady())
                  <button type="button" onclick="openTrailerModal('{{ $f->trailerUrl() }}')" class="btn btn-glass btn-lg">
                    <x-ui.icon name="video" class="size-4" /> Trailer
                  </button>
                @endif
              </div>
            </div>
          </div>
        </article>
      @endforeach

      @if ($featured->count() > 1)
        <div class="container-app pointer-events-none absolute inset-x-0 bottom-6 z-10 flex items-center justify-between">
          <div class="pointer-events-auto flex gap-2">
            @foreach ($featured as $idx => $f)
              <button type="button" @click="go({{ $idx }})" aria-label="Slide {{ $idx + 1 }}"
                class="h-1.5 rounded-full transition-all" :class="i === {{ $idx }} ? 'w-8 bg-brand' : 'w-3 bg-white/30 hover:bg-white/50'"></button>
            @endforeach
          </div>
          <div class="pointer-events-auto hidden gap-2 sm:flex">
            <button type="button" @click="go(i - 1)" class="btn btn-icon border border-white/20 bg-black/50 text-white backdrop-blur hover:bg-black/70" aria-label="Previous"><x-ui.icon name="chevron-left" /></button>
            <button type="button" @click="go(i + 1)" class="btn btn-icon border border-white/20 bg-black/50 text-white backdrop-blur hover:bg-black/70" aria-label="Next"><x-ui.icon name="chevron-right" /></button>
          </div>
        </div>
      @endif
    </div>
  </section>
@endif

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
          <a href="{{ route('browse') }}" class="btn btn-glass btn-lg">Browse films</a>
        @else
          <a href="{{ route('register') }}" class="btn btn-light btn-lg">Get started</a>
          <a href="{{ route('pricing') }}" class="btn btn-glass btn-lg">View plans</a>
        @endauth
      </div>
    </div>
  </section>
</div>
@endsection
