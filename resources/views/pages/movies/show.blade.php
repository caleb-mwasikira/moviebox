@extends('layouts.app')

@section('title', $movie->title)

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        <div class="mb-8 md:mb-10">
          <a href="{{ route('movies') }}"
            class="inline-flex items-center gap-x-2 text-sm font-bold uppercase text-gray-500 hover:text-accent transition-colors">
            <svg class="h-2 w-1.5 rotate-180 fill-current" viewBox="0 0 6 10"><path d="M1 1l4 4-4 4"/></svg>
            All Movies
          </a>
        </div>

        <div class="grid grid-cols-12 gap-x-5 md:gap-x-6 lg:gap-x-7.5 gap-y-12">

          {{-- Left: poster gallery --}}
          <div class="col-span-full md:col-span-5 lg:col-span-4">
            <div class="sticky top-8">
              @if ($movie->posters->isNotEmpty())
                <div class="swiper js-vv-product-swiper mb-6 aspect-[2/3] w-full overflow-hidden">
                  <div class="swiper-wrapper">
                    @foreach ($movie->posters as $poster)
                    <div class="swiper-slide">
                      <img class="h-full w-full object-cover" src="{{ $poster->url() }}" alt="{{ $movie->title }}">
                    </div>
                    @endforeach
                  </div>
                </div>

                @if ($movie->posters->count() > 1)
                <div class="swiper js-vv-product-swiper-nav w-full">
                  <div class="swiper-wrapper">
                    @foreach ($movie->posters as $poster)
                    <div class="swiper-slide">
                      <img class="aspect-square w-full cursor-pointer object-cover opacity-100 transition-opacity duration-300 hover:opacity-50"
                        src="{{ $poster->url() }}" alt="">
                    </div>
                    @endforeach
                  </div>
                </div>
                @endif
              @elseif ($movie->thumbnail)
                <img class="w-full aspect-[2/3] object-cover" src="{{ $movie->thumbnailUrl() }}" alt="{{ $movie->title }}">
              @else
                <div class="w-full aspect-[2/3] bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                  <svg class="h-16 w-16 fill-gray-400" viewBox="0 0 24 24"><path d="M4 2h16a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/></svg>
                </div>
              @endif
            </div>
          </div>

          {{-- Right: metadata + video --}}
          <div class="col-span-full md:col-span-7 lg:col-span-8">

            <div class="mb-10 md:mb-14">
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white md:text-3xl lg:text-4xl lg:leading-none mb-4">
                {{ $movie->title }}
              </h1>

              <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-bold uppercase text-gray-500 mb-6">
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

              @if ($movie->description)
              <div class="text-gray-500 dark:text-gray leading-relaxed mb-8">
                {{ $movie->description }}
              </div>
              @endif

              {{-- Video player --}}
              @if ($movie->videoIsReady())
                <div class="w-full bg-black aspect-video">
                  <video
                    class="w-full h-full"
                    controls
                    preload="metadata"
                    src="{{ $movie->videoUrl() }}"
                  ></video>
                </div>
              @else
                <div class="w-full aspect-video bg-gray-100 dark:bg-gray-700 flex flex-col items-center justify-center gap-y-3">
                  <svg class="h-10 w-10 fill-gray-400" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                  <span class="text-xs font-bold uppercase text-gray-400 tracking-widest">Video processing...</span>
                </div>
              @endif
            </div>

          </div>

        </div>

      </div>
    </div>

  </main>
@endsection
