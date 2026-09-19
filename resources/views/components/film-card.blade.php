@props(['film', 'type' => 'movie'])

@php
  $href  = $type === 'series' ? route('series.show', $film) : route('movies.show', $film);
  $label = $film->genre ?? ($type === 'series' ? 'Series' : 'Film');
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group block']) }}>
  <div class="poster-frame transition duration-300 group-hover:-translate-y-1 group-hover:shadow-pop group-hover:ring-brand/60">
    @if ($film->thumbnail)
      <img src="{{ $film->thumbnailUrl() }}" alt="{{ $film->title }}" loading="lazy"
        class="size-full object-cover transition duration-500 group-hover:scale-105">
    @else
      <div class="grid size-full place-items-center text-muted"><x-ui.icon name="film" class="size-10" /></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 transition duration-300 group-hover:opacity-100"></div>
    <span class="absolute inset-0 grid place-items-center opacity-0 transition duration-300 group-hover:opacity-100">
      <span class="grid size-12 place-items-center rounded-full bg-brand text-white shadow-lg"><x-ui.icon name="play" class="ml-0.5 size-5" /></span>
    </span>
    <div class="absolute left-2 top-2 flex gap-1">
      @if ($type === 'series')<span class="badge bg-black/60 text-white backdrop-blur">Series</span>@endif
      @if (!empty($film->is_free))<span class="badge bg-emerald-500 text-white">Free</span>@endif
    </div>
  </div>
  <div class="mt-3 px-0.5">
    <h3 class="truncate text-sm font-semibold text-fg transition-colors group-hover:text-brand">{{ $film->title }}</h3>
    <p class="mt-0.5 truncate text-xs text-muted">{{ $label }}@if (!empty($film->year)) · {{ $film->year }}@endif</p>
  </div>
</a>
