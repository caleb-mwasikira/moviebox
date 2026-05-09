@extends('layouts.app')

@section('title', $movie->title)

@section('content')
  <main id="main-content" class="grow">

    <div class="bg-black">
      <div class="max-w-[1360px] mx-auto">
        <video
          class="w-full aspect-video"
          controls
          autoplay
          preload="auto"
          src="{{ $movie->videoUrl() }}"
        ></video>
      </div>
    </div>

    <div class="pt-6 pb-16">
      <div class="container">

        <div class="flex items-start justify-between gap-x-6 flex-wrap gap-y-4">
          <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white md:text-2xl mb-2">
              {{ $movie->title }}
            </h1>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-bold uppercase text-gray-500">
              @if ($movie->genre)
                <span class="text-accent">{{ $movie->genre }}</span>
              @endif
              @if ($movie->year)
                <span>{{ $movie->year }}</span>
              @endif
              @if ($movie->duration)
                <span>{{ $movie->duration }} min</span>
              @endif
            </div>
          </div>
          <a href="{{ route('movies.show', $movie) }}"
            class="shrink-0 inline-flex items-center gap-x-2 text-sm font-bold uppercase text-gray-500 hover:text-accent transition-colors">
            <svg class="h-2 w-1.5 rotate-180 fill-current" viewBox="0 0 6 10"><path d="M1 1l4 4-4 4"/></svg>
            Back
          </a>
        </div>

        @if ($movie->description)
        <div class="mt-5 text-gray-500 dark:text-gray leading-relaxed max-w-3xl">
          {{ $movie->description }}
        </div>
        @endif

      </div>
    </div>

  </main>
@endsection
