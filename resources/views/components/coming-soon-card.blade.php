@props(['film', 'showDescription' => false])

@php $thumb = $film->posters->first()?->url() ?? $film->thumbnailUrl(); @endphp

<div {{ $attributes->merge(['class' => 'group']) }}>
  <a href="{{ route('coming-soon.show', $film) }}" class="poster-frame block transition duration-300 group-hover:-translate-y-1 group-hover:shadow-pop">
    @if ($thumb)
      <img src="{{ $thumb }}" alt="{{ $film->title }}" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
    @else
      <div class="grid size-full place-items-center text-muted"><x-ui.icon name="film" class="size-10" /></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
    <span class="badge absolute left-2 top-2 bg-amber-400 text-neutral-900">Coming soon</span>
  </a>

  <div class="mt-3 px-0.5">
    <a href="{{ route('coming-soon.show', $film) }}" class="block truncate text-sm font-semibold text-fg transition-colors hover:text-brand">{{ $film->title }}</a>
    <p class="mt-0.5 truncate text-xs text-muted">
      @if ($film->genre)<span class="text-brand">{{ $film->genre }}</span>@endif
      @if ($film->genre && $film->year) · @endif
      @if ($film->year){{ $film->year }}@endif
    </p>
    @if ($showDescription && $film->description)
      <p class="mt-1.5 line-clamp-2 text-xs leading-relaxed text-muted">{{ $film->description }}</p>
    @endif
    @if ($film->trailerIsReady())
      <button type="button" onclick="openTrailerModal('{{ $film->trailerUrl() }}')"
        class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-brand hover:underline">
        <x-ui.icon name="play" class="size-3" /> Watch trailer
      </button>
    @endif
  </div>
</div>
