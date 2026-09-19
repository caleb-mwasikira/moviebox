@extends('layouts.app')

@section('title', $movie->title)

@section('content')
<x-ui.guest-banner />

@php
  $poster = $movie->posters->isNotEmpty() ? $movie->posters->first()->url() : $movie->thumbnailUrl();
@endphp

<section class="relative isolate overflow-hidden border-b border-line">
  @if ($poster)
    <img src="{{ $poster }}" alt="" class="absolute inset-0 -z-10 size-full scale-110 object-cover opacity-30 blur-2xl dark:opacity-25">
  @endif
  <div class="absolute inset-0 -z-10 bg-gradient-to-b from-bg/40 via-bg/70 to-bg"></div>

  <div class="container-app py-8 lg:py-12">
    <a href="{{ route('movies') }}" class="mb-8 inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-brand">
      <x-ui.icon name="arrow-left" class="size-4" /> All movies
    </a>

    <div class="grid gap-8 md:grid-cols-12 lg:gap-14">
      {{-- Poster gallery --}}
      <div class="md:col-span-5 lg:col-span-4" x-data="{ active: 0 }">
        <div class="mx-auto max-w-sm md:sticky md:top-24">
          <div class="poster-frame !rounded-2xl shadow-pop">
            @if ($movie->posters->isNotEmpty())
              @foreach ($movie->posters as $idx => $p)
                <img x-show="active === {{ $idx }}" @if($idx > 0) x-cloak @endif src="{{ $p->url() }}" alt="{{ $movie->title }}" class="size-full object-cover">
              @endforeach
            @elseif ($movie->thumbnail)
              <img src="{{ $movie->thumbnailUrl() }}" alt="{{ $movie->title }}" class="size-full object-cover">
            @else
              <div class="grid size-full place-items-center text-muted"><x-ui.icon name="film" class="size-14" /></div>
            @endif
          </div>
          @if ($movie->posters->count() > 1)
            <div class="mt-3 grid grid-cols-5 gap-2">
              @foreach ($movie->posters as $idx => $p)
                <button type="button" @click="active = {{ $idx }}" class="aspect-square overflow-hidden rounded-lg ring-2 transition" :class="active === {{ $idx }} ? 'ring-brand' : 'ring-transparent opacity-60 hover:opacity-100'">
                  <img src="{{ $p->url() }}" alt="" class="size-full object-cover">
                </button>
              @endforeach
            </div>
          @endif
        </div>
      </div>

      {{-- Details --}}
      <div class="md:col-span-7 lg:col-span-8">
        <div class="flex flex-wrap items-center gap-2">
          @if ($movie->genre)<span class="badge badge-brand">{{ $movie->genre }}</span>@endif
          @if ($movie->is_free)<span class="badge badge-success">Free to watch</span>@endif
        </div>
        <h1 class="mt-4 text-4xl font-extrabold leading-tight text-fg lg:text-5xl">{{ $movie->title }}</h1>

        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-medium text-muted">
          @if ($movie->year)<span class="inline-flex items-center gap-1.5"><x-ui.icon name="calendar" class="size-4" />{{ $movie->year }}</span>@endif
          @if ($movie->duration)<span class="inline-flex items-center gap-1.5"><x-ui.icon name="clock" class="size-4" />{{ $movie->duration }} min</span>@endif
        </div>

        @if ($movie->description)
          <p class="mt-6 max-w-3xl text-base leading-relaxed text-fg-soft">{{ $movie->description }}</p>
        @endif

        <div class="mt-8 flex flex-wrap items-center gap-3">
          @if ($movie->videoIsReady())
            @if ($movie->is_free)
              <a href="{{ route('movies.watch', $movie) }}" class="btn btn-primary btn-lg"><x-ui.icon name="play" class="size-4" /> Watch free</a>
            @else
              <a href="{{ route('movies.watch', $movie) }}" onclick="event.preventDefault(); watchOrLogin(this.href)" class="btn btn-primary btn-lg">
                <x-ui.icon name="play" class="size-4" /> Watch full movie
              </a>
            @endif
          @else
            <span class="btn btn-secondary btn-lg pointer-events-none opacity-70"><x-ui.icon name="clock" class="size-4" /> Video processing…</span>
          @endif

          @if ($movie->trailerIsReady())
            <button type="button" onclick="openTrailerModal('{{ $movie->trailerUrl() }}')" class="btn btn-outline btn-lg"><x-ui.icon name="video" class="size-4" /> Trailer</button>
          @endif
        </div>

        <div class="mt-4 flex flex-wrap items-start gap-3">
          <x-ui.watchlist-button type="movie" :id="$movie->id" :saved="$isWatchlisted" />
          @auth
            @if (auth()->user()->canDownload() && $movie->video_path)
              <a href="{{ route('movies.download', $movie) }}" class="btn btn-secondary"><x-ui.icon name="download" class="size-4" /> Download</a>
            @endif
          @endauth
          <x-ui.share-button type="movie" :id="$movie->id" :message="'Hey! Check out *' . $movie->title . '* on ' . config('app.name') . ' — I think you\'ll love it! Watch it here:'" />
        </div>
      </div>
    </div>
  </div>
</section>

@if ($related->isNotEmpty())
  <section class="container-app pt-12">
    <div class="mb-6 flex items-end justify-between gap-4">
      <h2 class="section-title">More like this</h2>
      <a href="{{ route('movies', ['genre' => $movie->genre]) }}" class="btn btn-ghost btn-sm">View all <x-ui.icon name="arrow-right" class="size-4" /></a>
    </div>
    <div class="rail">
      @foreach ($related as $film)
        <x-film-card :film="$film" type="movie" class="w-36 sm:w-44 lg:w-48" />
      @endforeach
    </div>
  </section>
@endif
@endsection
