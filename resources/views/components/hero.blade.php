@props(['featured'])

@if ($featured->isNotEmpty())
  <section x-data="{ i: 0, n: {{ $featured->count() }}, t: null, tx: 0, ty: 0,
      swipeStart(e) { this.tx = e.touches[0].clientX; this.ty = e.touches[0].clientY; },
      swipeEnd(e) {
        const dx = e.changedTouches[0].clientX - this.tx, dy = e.changedTouches[0].clientY - this.ty;
        // Horizontal, deliberate swipes only — vertical scrolling is left alone.
        if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.5) this.go(dx < 0 ? this.i + 1 : this.i - 1);
      },
      go(k) { this.i = (k + this.n) % this.n; this.restart(); },
      restart() { clearInterval(this.t); this.t = setInterval(() => this.i = (this.i + 1) % this.n, 7000); } }"
    x-init="restart()" @mouseenter="clearInterval(t)" @mouseleave="restart()" @touchstart.passive="swipeStart($event)" @touchend.passive="swipeEnd($event)"
    class="relative isolate touch-pan-y overflow-hidden bg-neutral-950">
    <div class="relative h-[34rem] sm:h-[38rem] lg:h-[44rem]">
      @foreach ($featured as $idx => $f)
        @php
          $bgUrl = $f->posters->isNotEmpty() ? $f->posters->first()->url() : ($f->thumbnail ? $f->thumbnailUrl() : null);
          $fType = $f->content_type;
        @endphp
        <article x-show="i === {{ $idx }}" x-transition:enter="transition duration-700" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
          x-transition:leave="transition duration-500 absolute inset-0" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
          @if ($idx > 0) x-cloak @endif class="absolute inset-0">
          @if ($bgUrl)
            <img src="{{ $bgUrl }}" alt="" class="absolute inset-0 size-full object-cover object-top" @if($idx > 0) loading="lazy" @endif>
          @endif
          <div class="absolute inset-0 bg-gradient-to-r from-neutral-950 via-neutral-950/70 to-neutral-950/10"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-bg via-transparent to-neutral-950/40"></div>

          <div class="container-app relative flex h-full items-end pb-16 sm:pb-20 lg:items-center lg:pb-0">
            <div class="max-w-2xl">
              <div class="flex items-center gap-2">
                <span class="badge bg-brand text-white">
                  @if ($fType === 'coming_soon') Coming soon @elseif ($fType === 'series') Series @else Movie @endif
                </span>
                @if ($f->genre)<span class="badge bg-white/10 text-white backdrop-blur">{{ $f->genre }}</span>@endif
              </div>
              <h1 class="mt-4 text-4xl font-extrabold leading-[1.05] text-white sm:text-5xl lg:text-6xl">{{ $f->title }}</h1>
              <p class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm font-medium text-white/70">
                @if ($f->year)<span>{{ $f->year }}</span>@endif
                @if ($fType === 'movie' && $f->duration)<span>{{ $f->duration }} min</span>@endif
                @if ($fType === 'series')<span>{{ $f->seasons->count() }} {{ Str::plural('Season', $f->seasons->count()) }}</span>@endif
              </p>
              @if ($f->description)
                <p class="mt-4 line-clamp-3 max-w-xl text-base leading-relaxed text-white/75">{{ Str::limit($f->description, 200) }}</p>
              @endif

              <div class="mt-8 flex flex-wrap gap-3">
                @if ($fType === 'movie' && $f->videoIsReady())
                  <a href="{{ route('movies.watch', $f) }}" onclick="event.preventDefault(); watchOrLogin(this.href)" class="btn btn-primary btn-lg">
                    <x-ui.icon name="play" class="size-4" /> Watch now
                  </a>
                @elseif ($fType === 'series')
                  <a href="{{ route('series.show', $f) }}" class="btn btn-primary btn-lg"><x-ui.icon name="play" class="size-4" /> Watch series</a>
                @elseif ($fType === 'coming_soon')
                  <a href="{{ route('coming-soon.show', $f) }}" class="btn btn-primary btn-lg">Details</a>
                @endif
                @if ($f->trailerIsReady())
                  <button type="button" onclick="openTrailerModal('{{ $f->trailerUrl() }}')" class="btn btn-glass btn-lg">
                    <x-ui.icon name="video" class="size-4" /> Trailer
                  </button>
                @endif
              </div>
            </div>
          </div>
        </article>
      @endforeach

      @if ($featured->count() > 1)
        <div class="container-app pointer-events-none absolute inset-x-0 bottom-6 z-10 flex items-center justify-between">
          <div class="pointer-events-auto flex gap-2">
            @foreach ($featured as $idx => $f)
              <button type="button" @click="go({{ $idx }})" aria-label="Slide {{ $idx + 1 }}"
                class="h-1.5 rounded-full transition-all" :class="i === {{ $idx }} ? 'w-8 bg-brand' : 'w-3 bg-white/30 hover:bg-white/50'"></button>
            @endforeach
          </div>
          <div class="pointer-events-auto hidden gap-2 sm:flex">
            <button type="button" @click="go(i - 1)" class="btn btn-icon border border-white/20 bg-black/50 text-white backdrop-blur hover:bg-black/70" aria-label="Previous"><x-ui.icon name="chevron-left" /></button>
            <button type="button" @click="go(i + 1)" class="btn btn-icon border border-white/20 bg-black/50 text-white backdrop-blur hover:bg-black/70" aria-label="Next"><x-ui.icon name="chevron-right" /></button>
          </div>
        </div>
      @endif
    </div>
  </section>
@endif
