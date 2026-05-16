@props(['film', 'type' => 'movie'])

@php
  $href  = $type === 'series' ? route('series.show', $film) : route('movies.show', $film);
  $label = $film->genre ?? ($type === 'series' ? 'Series' : 'Film');
@endphp

<a class="flex flex-col gap-y-5 group" href="{{ $href }}">
  <figure class="relative overflow-hidden bg-gray-100 dark:bg-gray-700">
    @if ($film->thumbnail)
      <img class="w-full object-cover aspect-[17/22] transition-transform duration-300 group-hover:scale-110"
        src="{{ $film->thumbnailUrl() }}" alt="{{ $film->title }}">
    @else
      <div class="w-full aspect-[17/22] bg-gray-200 dark:bg-gray-600"></div>
    @endif
    @if (!empty($film->is_free))
      <div class="absolute top-2 left-2">
        <span class="bg-green-500 text-white text-[10px] font-bold uppercase px-2 py-0.5 tracking-wider">Free</span>
      </div>
    @endif
    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-accent/80 duration-300">
      <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-7.5 aspect-square bg-gray-900 grid place-items-center text-white scale-90 group-hover:scale-100 transition-all duration-300">
        <svg class="h-2.5 aspect-square" fill="currentColor">
          <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#plus-thinest"></use>
        </svg>
      </div>
    </div>
  </figure>
  <div class="leading-tight">
    <h5 class="font-bold text-gray-900 dark:text-white mb-1.5 group-hover:text-accent transition-colors">{{ $film->title }}</h5>
    <div class="text-gray-500 text-sm">{{ $label }}</div>
  </div>
</a>
