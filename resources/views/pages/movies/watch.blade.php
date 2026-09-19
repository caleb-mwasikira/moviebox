@extends('layouts.app')

@section('title', $movie->title)

@section('no-footer', '1')

@section('content')
<div x-data="{ info: false }" @keydown.escape.window="info = false">

  {{-- Stage --}}
  <div class="watch-stage">
    <video id="movie-player" controls autoplay preload="auto" crossorigin="anonymous" playsinline>
      @foreach ($movie->subtitles as $subtitle)
        <track kind="subtitles"
          src="{{ $subtitle->fileUrl() }}"
          srclang="{{ $subtitle->language_code }}"
          label="{{ $subtitle->language }}"
          {{ $subtitle->is_default ? 'default' : '' }}>
      @endforeach
    </video>
    <x-player.flashes />
  </div>

  {{-- Info / control bar --}}
  <div class="border-t border-line bg-surface">
    <div class="container-app flex flex-wrap items-center justify-between gap-4 py-4">
      <div class="min-w-0">
        <h1 class="truncate text-lg font-bold text-fg">{{ $movie->title }}</h1>
        <p class="text-xs font-medium uppercase tracking-wide text-muted">{{ collect([$movie->genre, $movie->year])->filter()->implode(' • ') }}</p>
      </div>

      <div class="flex items-center gap-1.5">
        <x-player.tools video="movie-player" :subtitles="$movie->subtitles" />
        <button type="button" @click="info = true" class="btn btn-secondary btn-icon" title="Movie info"><x-ui.icon name="info" class="size-[18px]" /></button>
        <a href="{{ route('movies.show', $movie) }}" class="btn btn-secondary btn-icon" title="Back to movie"><x-ui.icon name="arrow-left" class="size-[18px]" /></a>
      </div>
    </div>
  </div>

  {{-- Info drawer --}}
  <div x-show="info" x-cloak class="fixed inset-0 z-50">
    <div x-show="info" x-transition.opacity class="absolute inset-0 bg-black/70" @click="info = false"></div>
    <aside x-show="info" x-transition:enter="transition duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
      x-transition:leave="transition duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
      class="absolute inset-y-0 right-0 flex w-[26rem] max-w-[92vw] flex-col border-l border-line bg-surface shadow-pop">
      <div class="flex items-center justify-between border-b border-line px-5 py-4">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-muted">Movie info</h2>
        <button type="button" @click="info = false" class="btn btn-ghost btn-icon" aria-label="Close"><x-ui.icon name="x" class="size-4" /></button>
      </div>
      <div class="flex-1 overflow-y-auto p-6">
        <h2 class="text-2xl font-bold text-fg">{{ $movie->title }}</h2>
        <div class="mt-3 flex flex-wrap items-center gap-2">
          @if ($movie->genre)<span class="badge badge-brand">{{ $movie->genre }}</span>@endif
          @if ($movie->year)<span class="badge badge-neutral">{{ $movie->year }}</span>@endif
          @if ($movie->duration)<span class="badge badge-neutral">{{ $movie->duration }} min</span>@endif
        </div>
        @if ($movie->description)<p class="mt-5 text-sm leading-relaxed text-muted">{{ $movie->description }}</p>@endif
      </div>
    </aside>
  </div>
</div>

<x-player.engine id="movie-player" type="movie" :uuid="$movie->uuid" :stream="$movie->streaming_path ? $movie->streamingUrl() : null" :fallback="$movie->videoUrl()" :saved="$savedPosition ?? null" />
@endsection
