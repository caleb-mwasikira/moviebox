@extends('layouts.app')

@section('title', 'Series')

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        <div class="flex gap-x-8 justify-between items-center mb-12 md:mb-16 lg:mb-20">

          <div>
            <h1 class="text-2xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3xl md:text-4xl">Series</h1>
          </div>

          <nav>
            <ul class="flex flex-wrap gap-x-4 lg:gap-x-7 text-sm lg:text-base font-bold">
              <li>
                <a class="transition-colors {{ !request('genre') && !request('free') ? 'text-accent' : 'text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent' }}"
                  href="{{ route('series') }}">All</a>
              </li>
              <li>
                <a class="transition-colors {{ request('free') ? 'text-green-500' : 'text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent' }}"
                  href="{{ route('series', ['free' => 1]) }}">Free</a>
              </li>
              @foreach ($genres as $genre)
              <li>
                <a class="transition-colors {{ request('genre') === $genre ? 'text-accent' : 'text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent' }}"
                  href="{{ route('series', ['genre' => $genre]) }}">{{ $genre }}</a>
              </li>
              @endforeach
            </ul>
          </nav>

        </div>

        @if ($series->isEmpty())
          <div class="py-24 text-center text-gray-500 font-bold uppercase text-sm tracking-widest">
            No series available yet.
          </div>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-x-5 gap-y-9 md:gap-x-6 lg:gap-x-7.5">
          @foreach ($series as $show)
          <div>
            <figure class="group relative mb-4 overflow-hidden bg-gray-100 dark:bg-gray-700">
              <a href="{{ route('series.show', $show) }}">
                @if ($show->thumbnail)
                  <img class="aspect-[2/3] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                    src="{{ $show->thumbnailUrl() }}" alt="{{ $show->title }}">
                @else
                  <div class="aspect-[2/3] w-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                    <svg class="h-12 w-12 fill-gray-400" viewBox="0 0 24 24"><path d="M4 2h16a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm8 3a5 5 0 1 0 0 10A5 5 0 0 0 12 5zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6zM5 19h14v-1a7 7 0 0 0-14 0v1z"/></svg>
                  </div>
                @endif
                <div class="absolute top-2 left-2">
                  <span class="bg-primary text-white text-[10px] font-bold uppercase px-2 py-0.5">Series</span>
                </div>
                @if (!empty($show->free_episodes_count) && $show->free_episodes_count > 0)
                <div class="absolute top-2 right-2">
                  <span class="bg-green-500 text-white text-[10px] font-bold uppercase px-2 py-0.5 tracking-wider">Free</span>
                </div>
                @endif
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-accent/80 duration-300">
                  <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-10 aspect-square bg-gray-900 grid place-items-center text-white scale-90 group-hover:scale-100 transition-all duration-300">
                    <svg class="h-4 w-4 fill-white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                  </div>
                </div>
              </a>
            </figure>
            <div>
              <h3 class="font-bold text-gray-900 dark:text-white text-sm leading-tight mb-1">
                <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                  href="{{ route('series.show', $show) }}">{{ $show->title }}</a>
              </h3>
              <div class="text-xs text-gray-500 leading-snug">
                @php $meta = array_filter([$show->genre, $show->year]); @endphp
                {{ implode(' · ', $meta) }}
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <div class="pt-10 md:pt-14 lg:pt-24">
          {{ $series->links() }}
        </div>
        @endif

      </div>
    </div>

  </main>
@endsection
