@extends('layouts.app')

@section('title', $series->title)

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        <div class="mb-8 md:mb-10">
          <a href="{{ route('series') }}"
            class="inline-flex items-center gap-x-2 text-sm font-bold uppercase text-gray-500 hover:text-accent transition-colors">
            <svg class="h-2 w-1.5 rotate-180 fill-current" viewBox="0 0 6 10"><path d="M1 1l4 4-4 4"/></svg>
            All Series
          </a>
        </div>

        {{-- Header: thumbnail + metadata --}}
        <div class="grid grid-cols-12 gap-x-5 md:gap-x-6 lg:gap-x-7.5 gap-y-8 mb-14 md:mb-20">

          <div class="col-span-full sm:col-span-4 md:col-span-3">
            @if ($series->posters->isNotEmpty())
              <img class="w-full aspect-[2/3] object-cover"
                src="{{ $series->posters->first()->url() }}" alt="{{ $series->title }}">
            @elseif ($series->thumbnail)
              <img class="w-full aspect-[2/3] object-cover"
                src="{{ $series->thumbnailUrl() }}" alt="{{ $series->title }}">
            @else
              <div class="w-full aspect-[2/3] bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <svg class="h-14 w-14 fill-gray-400" viewBox="0 0 24 24"><path d="M4 2h16a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/></svg>
              </div>
            @endif
          </div>

          <div class="col-span-full sm:col-span-8 md:col-span-9">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white md:text-3xl lg:text-4xl lg:leading-none mb-4">
              {{ $series->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-bold uppercase text-gray-500 mb-6">
              @if ($series->genre)
                <span class="text-accent">{{ $series->genre }}</span>
              @endif
              @if ($series->year)
                <span>{{ $series->year }}</span>
              @endif
              <span>{{ $series->seasons->count() }} {{ Str::plural('Season', $series->seasons->count()) }}</span>
            </div>

            @if ($series->description)
            <div class="text-gray-500 dark:text-gray leading-relaxed">
              {{ $series->description }}
            </div>
            @endif
          </div>

        </div>

        {{-- Seasons / Episodes --}}
        @if ($series->seasons->isEmpty())
          <div class="py-16 text-center text-gray-500 font-bold uppercase text-sm tracking-widest border border-gray-200 dark:border-gray-600">
            No seasons added yet.
          </div>
        @else
          <div class="flex flex-col gap-y-2">
            @foreach ($series->seasons as $season)
            <details class="group border border-gray-200 dark:border-gray-600" @if($loop->first) open @endif>
              <summary class="flex items-center justify-between gap-x-4 px-5 py-4 cursor-pointer font-bold text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors list-none">
                <span class="text-base lg:text-lg">
                  {{ $season->label() }}
                  @if ($season->title) — {{ $season->title }} @endif
                </span>
                <span class="shrink-0 text-xs text-gray-500 font-bold uppercase">
                  {{ $season->episodes->count() }} {{ Str::plural('Episode', $season->episodes->count()) }}
                </span>
              </summary>

              <div class="border-t border-gray-200 dark:border-gray-600">
                @if ($season->episodes->isEmpty())
                  <div class="px-5 py-6 text-sm text-gray-500">No episodes yet.</div>
                @else
                  <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($season->episodes as $episode)
                    <details class="group/ep px-5 py-4">
                      <summary class="flex w-full items-start justify-between gap-x-4 cursor-pointer list-none">
                        <div class="flex-1 min-w-0">
                          <div class="font-bold text-gray-900 dark:text-white text-sm leading-tight mb-0.5 group-open/ep:text-accent transition-colors">
                            {{ $episode->label() }}
                          </div>
                          @if ($episode->description)
                          <div class="text-xs text-gray-500 leading-snug line-clamp-2">{{ $episode->description }}</div>
                          @endif
                        </div>
                        <div class="shrink-0 flex items-center gap-x-3">
                          @if ($episode->duration)
                            <span class="text-xs text-gray-500 font-bold">{{ $episode->duration }} min</span>
                          @endif
                          @if ($episode->videoIsReady())
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-accent text-gray-900">
                              <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </span>
                          @else
                            <span class="text-xs text-gray-400 font-bold uppercase">Processing</span>
                          @endif
                        </div>
                      </summary>

                      @if ($episode->videoIsReady())
                      <div class="mt-4">
                        <video
                          class="w-full aspect-video bg-black"
                          controls
                          preload="none"
                          src="{{ $episode->videoUrl() }}"
                        ></video>
                      </div>
                      @endif
                    </details>
                    @endforeach
                  </div>
                @endif
              </div>
            </details>
            @endforeach
          </div>
        @endif

      </div>
    </div>

  </main>
@endsection
