@extends('layouts.app')

@section('title', $series->title)

@section('content')
<x-ui.guest-banner />

@php
  $poster = $series->posters->isNotEmpty() ? $series->posters->first()->url() : $series->thumbnailUrl();
  $canDownload = auth()->check() && auth()->user()->canDownload();
@endphp

<section class="relative isolate overflow-hidden border-b border-line">
  @if ($poster)
    <img src="{{ $poster }}" alt="" class="absolute inset-0 -z-10 size-full scale-110 object-cover opacity-30 blur-2xl dark:opacity-25">
  @endif
  <div class="absolute inset-0 -z-10 bg-gradient-to-b from-bg/40 via-bg/70 to-bg"></div>

  <div class="container-app py-8 lg:py-12">
    <a href="{{ route('series') }}" class="mb-8 inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-brand">
      <x-ui.icon name="arrow-left" class="size-4" /> All series
    </a>

    <div class="grid gap-8 sm:grid-cols-12 lg:gap-12">
      <div class="sm:col-span-4 lg:col-span-3">
        <div class="poster-frame mx-auto max-w-xs !rounded-2xl shadow-pop">
          @if ($poster)
            <img src="{{ $poster }}" alt="{{ $series->title }}" class="size-full object-cover">
          @else
            <div class="grid size-full place-items-center text-muted"><x-ui.icon name="tv" class="size-14" /></div>
          @endif
        </div>
      </div>

      <div class="sm:col-span-8 lg:col-span-9">
        <div class="flex flex-wrap items-center gap-2">
          <span class="badge badge-info">Series</span>
          @if ($series->genre)<span class="badge badge-brand">{{ $series->genre }}</span>@endif
        </div>
        <h1 class="mt-4 text-4xl font-extrabold leading-tight text-fg lg:text-5xl">{{ $series->title }}</h1>
        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-medium text-muted">
          @if ($series->year)<span class="inline-flex items-center gap-1.5"><x-ui.icon name="calendar" class="size-4" />{{ $series->year }}</span>@endif
          <span class="inline-flex items-center gap-1.5"><x-ui.icon name="tv" class="size-4" />{{ $series->seasons->count() }} {{ Str::plural('Season', $series->seasons->count()) }}</span>
        </div>
        @if ($series->description)
          <p class="mt-6 max-w-3xl text-base leading-relaxed text-fg-soft">{{ $series->description }}</p>
        @endif

        <div class="mt-8 flex flex-wrap items-start gap-3">
          @if ($series->trailerIsReady())
            <button type="button" onclick="openTrailerModal('{{ $series->trailerUrl() }}')" class="btn btn-primary btn-lg"><x-ui.icon name="video" class="size-4" /> Watch trailer</button>
          @endif
          <x-ui.watchlist-button type="series" :id="$series->id" :saved="$isWatchlisted" />
          <x-ui.share-button type="series" :id="$series->id" :message="'Hey! I\'ve been watching *' . $series->title . '* on ' . config('app.name') . ' and it\'s seriously good. Check it out:'" />
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Seasons / episodes --}}
<section class="container-app pt-12">
  <h2 class="section-title mb-6">Episodes</h2>

  @if ($series->seasons->isEmpty())
    <x-ui.empty icon="tv" title="No seasons added yet" text="Episodes will appear here soon." />
  @else
    <div class="space-y-3">
      @foreach ($series->seasons as $season)
        <details class="card group overflow-hidden" @if ($loop->first) open @endif>
          <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 transition hover:bg-surface-2/60">
            <span class="flex items-center gap-3 font-semibold text-fg">
              <x-ui.icon name="chevron-right" class="size-4 text-muted transition group-open:rotate-90" />
              {{ $season->label() }}@if ($season->title) — {{ $season->title }}@endif
            </span>
            <span class="badge badge-neutral">{{ $season->episodes->count() }} {{ Str::plural('episode', $season->episodes->count()) }}</span>
          </summary>

          <div class="border-t border-line">
            @if ($season->episodes->isEmpty())
              <p class="px-5 py-6 text-sm text-muted">No episodes yet.</p>
            @else
              <ul class="divide-y divide-line">
                @foreach ($season->episodes as $episode)
                  <li class="flex items-center gap-4 px-5 py-4">
                    <div class="min-w-0 flex-1">
                      <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm font-semibold text-fg">{{ $episode->label() }}</span>
                        @if ($episode->is_free)<span class="badge badge-success">Free</span>@endif
                      </div>
                      @if ($episode->description)<p class="mt-0.5 line-clamp-2 text-xs text-muted">{{ $episode->description }}</p>@endif
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                      @if ($episode->duration)<span class="hidden text-xs font-medium text-muted sm:inline">{{ $episode->duration }} min</span>@endif
                      @if ($canDownload && $episode->video_path)
                        <a href="{{ route('episodes.download', $episode) }}" class="btn btn-secondary btn-icon" title="Download for offline viewing"><x-ui.icon name="download" class="size-4" /></a>
                      @endif
                      @if ($episode->videoIsReady())
                        @if ($episode->is_free)
                          <a href="{{ route('series.watch', [$series, $episode]) }}" class="btn btn-primary btn-sm"><x-ui.icon name="play" class="size-3.5" /> Watch free</a>
                        @else
                          <a href="{{ route('series.watch', [$series, $episode]) }}" onclick="event.preventDefault(); watchOrLogin(this.href)" class="btn btn-primary btn-icon" title="Watch">
                            <x-ui.icon name="play" class="size-4" />
                          </a>
                        @endif
                      @else
                        <span class="badge badge-warning">Processing</span>
                      @endif
                    </div>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        </details>
      @endforeach
    </div>
  @endif
</section>

@if ($related->isNotEmpty())
  <section class="container-app pt-14">
    <div class="mb-6 flex items-end justify-between gap-4">
      <h2 class="section-title">More like this</h2>
      <a href="{{ route('series', ['genre' => $series->genre]) }}" class="btn btn-ghost btn-sm">View all <x-ui.icon name="arrow-right" class="size-4" /></a>
    </div>
    <div class="rail">
      @foreach ($related as $film)
        <x-film-card :film="$film" type="series" class="w-36 sm:w-44 lg:w-48" />
      @endforeach
    </div>
  </section>
@endif
@endsection
