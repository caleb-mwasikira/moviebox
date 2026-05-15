@props(['film', 'showDescription' => false])

@php $thumb = $film->posters->first()?->url() ?? $film->thumbnailUrl(); @endphp

<div class="flex flex-col gap-y-4 group">

  <a href="{{ route('coming-soon.show', $film) }}" class="block relative overflow-hidden bg-gray-800">

    @if ($thumb)
      <img class="w-full object-cover aspect-[2/3] transition-transform duration-300 group-hover:scale-105"
        src="{{ $thumb }}" alt="{{ $film->title }}">
    @else
      <div class="w-full aspect-[2/3] bg-gray-700 flex items-center justify-center">
        <svg class="w-8 h-8 fill-gray-500" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
      </div>
    @endif

    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors duration-300"></div>

    <div class="absolute top-2.5 left-2.5">
      <span style="background:var(--color-accent,#facc15); color:#111; font-size:9px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:2px 7px;">
        Coming Soon
      </span>
    </div>

    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
      <span class="w-11 h-11 rounded-full bg-black/40 border border-white/60 flex items-center justify-center backdrop-blur-sm group-hover:bg-accent/80 group-hover:border-accent transition-all duration-200 opacity-0 group-hover:opacity-100">
        <svg class="w-4 h-4 fill-white ml-0.5" viewBox="0 0 24 24"><path d="M10 17l5-5-5-5v10z"/></svg>
      </span>
    </div>
  </a>

  <div class="leading-tight">
    <a href="{{ route('coming-soon.show', $film) }}"
      class="block font-bold text-gray-900 dark:text-white mb-1 group-hover:text-accent transition-colors text-sm hover:text-accent">
      {{ $film->title }}
    </a>
    <div class="text-xs text-gray-500">
      @if ($film->genre)
        <span style="color:var(--color-accent,#facc15);">{{ $film->genre }}</span>
      @endif
      @if ($film->genre && $film->year)
        <span class="text-gray-400"> · </span>
      @endif
      @if ($film->year)
        <span>{{ $film->year }}</span>
      @endif
    </div>
    @if ($showDescription && $film->description)
      <p class="mt-1.5 text-xs text-gray-400 leading-relaxed line-clamp-2">{{ $film->description }}</p>
    @endif
    @if ($film->trailerIsReady())
      <button type="button" onclick="openTrailerModal('{{ $film->trailerUrl() }}')"
        class="mt-2 inline-flex items-center gap-x-1.5 text-[10px] font-bold uppercase tracking-wide text-accent hover:underline">
        <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        Watch Trailer
      </button>
    @endif
  </div>

</div>
