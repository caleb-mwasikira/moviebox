@extends('layouts.app')

@section('title', $episode->label() . ' — ' . $series->title)

@section('no-footer', '1')

@section('content')
<div x-data="{ eps: false }" @keydown.escape.window="eps = false">

  {{-- Stage --}}
  <div class="watch-stage">
    <video id="episode-player" controls autoplay preload="auto" crossorigin="anonymous" playsinline>
      @foreach ($episode->subtitles as $subtitle)
        <track kind="subtitles"
          src="{{ $subtitle->fileUrl() }}"
          srclang="{{ $subtitle->language_code }}"
          label="{{ $subtitle->language }}"
          {{ $subtitle->is_default ? 'default' : '' }}>
      @endforeach
    </video>
    <x-player.flashes />

    {{-- Pause overlay --}}
    <div id="pause-overlay" class="pause-overlay">
      @if ($prev)
        <a href="{{ route('series.watch', [$series, $prev]) }}" class="po-btn" title="Previous episode"><svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6 8.5 6V6z"/></svg></a>
      @else
        <span class="po-btn ep-disabled"><svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6 8.5 6V6z"/></svg></span>
      @endif

      <button type="button" id="pause-play-btn" class="po-btn play" title="Play"><svg width="32" height="32" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></button>

      @if ($next)
        <a href="{{ route('series.watch', [$series, $next]) }}" class="po-btn" title="Next episode"><svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6v12zm2.5-6 5.5 3.9V8.1L8.5 12zM16 6h2v12h-2z"/></svg></a>
      @else
        <span class="po-btn ep-disabled"><svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6v12zm2.5-6 5.5 3.9V8.1L8.5 12zM16 6h2v12h-2z"/></svg></span>
      @endif
    </div>

    {{-- Autoplay next episode --}}
    @if ($next)
      <div id="autoplay-card" class="autoplay-card">
        <p class="mb-1 text-[10px] font-bold uppercase tracking-[0.18em] text-white/40">Up next</p>
        <p class="mb-3.5 text-sm font-semibold leading-snug text-white">{{ $next->label() }}</p>
        <div class="flex items-center gap-3">
          <div class="relative size-11 shrink-0">
            <svg width="44" height="44" viewBox="0 0 44 44" style="transform:rotate(-90deg)">
              <circle cx="22" cy="22" r="18" fill="none" stroke="rgba(255,255,255,.12)" stroke-width="3"/>
              <circle id="autoplay-ring" cx="22" cy="22" r="18" fill="none" stroke="#7c63ff" stroke-width="3" stroke-linecap="round" stroke-dasharray="113.1" stroke-dashoffset="0"/>
            </svg>
            <span id="autoplay-count" class="absolute inset-0 grid place-items-center text-xs font-black text-white">5</span>
          </div>
          <div class="flex flex-1 flex-col gap-1.5">
            <a href="{{ route('series.watch', [$series, $next]) }}" class="btn btn-primary btn-sm">Play now</a>
            <button type="button" onclick="cancelAutoplay()" class="btn btn-glass btn-sm">Cancel</button>
          </div>
        </div>
      </div>
    @endif
  </div>

  {{-- Info / control bar --}}
  <div class="border-t border-line bg-surface">
    <div class="container-app flex flex-wrap items-center justify-between gap-4 py-4">
      <div class="min-w-0">
        <h1 class="truncate text-lg font-bold text-fg">{{ $episode->label() }}</h1>
        <a href="{{ route('series.show', $series) }}" class="text-xs font-medium uppercase tracking-wide text-muted hover:text-brand">{{ $series->title }}</a>
      </div>

      <div class="flex items-center gap-1.5">
        <x-player.tools video="episode-player" :subtitles="$episode->subtitles" />
        @if ($prev)
          <a href="{{ route('series.watch', [$series, $prev]) }}" class="btn btn-secondary btn-icon" title="Previous episode"><x-ui.icon name="chevron-left" class="size-[18px]" /></a>
        @else
          <span class="btn btn-secondary btn-icon pointer-events-none opacity-40"><x-ui.icon name="chevron-left" class="size-[18px]" /></span>
        @endif
        @if ($next)
          <a href="{{ route('series.watch', [$series, $next]) }}" class="btn btn-secondary btn-icon" title="Next episode"><x-ui.icon name="chevron-right" class="size-[18px]" /></a>
        @else
          <span class="btn btn-secondary btn-icon pointer-events-none opacity-40"><x-ui.icon name="chevron-right" class="size-[18px]" /></span>
        @endif
        <button type="button" id="toggle-episodes" @click="eps = true" class="btn btn-primary btn-sm"><x-ui.icon name="list" class="size-4" /> Episodes</button>
      </div>
    </div>
  </div>

  {{-- Episodes drawer --}}
  <div x-show="eps" x-cloak class="fixed inset-0 z-50">
    <div x-show="eps" x-transition.opacity class="absolute inset-0 bg-black/70" @click="eps = false"></div>
    <aside x-show="eps" x-transition:enter="transition duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
      x-transition:leave="transition duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
      class="absolute inset-y-0 right-0 flex w-[26rem] max-w-[92vw] flex-col border-l border-line bg-surface shadow-pop">
      <div class="flex items-center justify-between border-b border-line px-5 py-4">
        <div class="min-w-0">
          <p class="truncate text-sm font-semibold text-fg">{{ $series->title }}</p>
          <p class="text-xs text-muted">{{ $episode->label() }}</p>
        </div>
        <button type="button" id="close-sidebar" @click="eps = false" class="btn btn-ghost btn-icon" aria-label="Close"><x-ui.icon name="x" class="size-4" /></button>
      </div>
      <div class="flex-1 overflow-y-auto">
        @foreach ($series->seasons as $season)
          <div class="sticky top-0 z-[1] border-b border-line bg-surface-2 px-5 py-2 text-[11px] font-semibold uppercase tracking-wider text-muted">{{ $season->label() }}</div>
          @foreach ($season->episodes as $ep)
            @php $isCurrent = $ep->id === $episode->id; $ready = $ep->videoIsReady(); @endphp
            <a href="{{ $ready ? route('series.watch', [$series, $ep]) : '#' }}"
              class="flex items-center gap-3 border-b border-line px-5 py-3.5 transition {{ $isCurrent ? 'border-l-[3px] border-l-brand bg-brand-soft' : 'hover:bg-surface-2' }} {{ $ready ? '' : 'pointer-events-none opacity-50' }}">
              <span class="grid size-8 shrink-0 place-items-center rounded-lg text-xs font-bold {{ $isCurrent ? 'bg-brand text-white' : 'bg-surface-3 text-muted' }}">
                @if ($isCurrent)<x-ui.icon name="play" class="size-3.5" />@else{{ str_pad($ep->episode_number, 2, '0', STR_PAD_LEFT) }}@endif
              </span>
              <span class="min-w-0 flex-1">
                <span class="block truncate text-sm font-semibold {{ $isCurrent ? 'text-brand' : 'text-fg' }}">{{ $ep->title }}</span>
                <span class="text-[11px] text-muted">{{ $ready ? $ep->duration . ' min' : 'Processing' }}</span>
              </span>
            </a>
          @endforeach
        @endforeach
      </div>
    </aside>
  </div>
</div>

<x-player.engine id="episode-player" type="episode" :uuid="$episode->uuid" :stream="$episode->streamingUrl()" :fallback="$episode->videoUrl()" :saved="$savedPosition ?? null" />

@push('scripts')
<script>
(function () {
  const video = document.getElementById('episode-player');

  @if ($next)
  // ── Autoplay next episode ──
  const NEXT_URL      = @json(route('series.watch', [$series, $next]));
  const COUNTDOWN     = 5;
  const circumference = 2 * Math.PI * 18;
  const apCard  = document.getElementById('autoplay-card');
  const apCount = document.getElementById('autoplay-count');
  const apRing  = document.getElementById('autoplay-ring');
  let apTimer  = null;
  let apActive = false;

  function showAutoplay() {
    if (apActive || !apCard) return;
    apActive = true;
    let secs = COUNTDOWN;
    apCount.textContent = secs;
    apRing.style.strokeDashoffset = '0';
    apCard.classList.add('show');
    apTimer = setInterval(function () {
      secs--;
      apCount.textContent = secs;
      apRing.style.strokeDashoffset = String(circumference * (1 - secs / COUNTDOWN));
      if (secs <= 0) { clearInterval(apTimer); window.location.href = NEXT_URL; }
    }, 1000);
  }
  window.cancelAutoplay = function () {
    clearInterval(apTimer); apTimer = null; apActive = false;
    if (apCard) apCard.classList.remove('show');
  };
  video.addEventListener('timeupdate', function () {
    if (!apActive && video.duration > 0 && video.duration - video.currentTime <= COUNTDOWN) showAutoplay();
  });
  video.addEventListener('ended', showAutoplay);
  video.addEventListener('seeked', function () {
    if (apActive && video.duration > 0 && video.duration - video.currentTime > COUNTDOWN) cancelAutoplay();
  });
  @endif

  // ── Pause overlay ──
  const pauseOverlay = document.getElementById('pause-overlay');
  function showPauseOverlay() { pauseOverlay.classList.add('show'); }
  function hidePauseOverlay() { pauseOverlay.classList.remove('show'); }
  video.addEventListener('pause',  showPauseOverlay);
  video.addEventListener('play',   hidePauseOverlay);
  video.addEventListener('ended',  hidePauseOverlay);
  // Show if autoplay is blocked and video starts paused
  video.addEventListener('loadedmetadata', function () {
    if (video.paused) showPauseOverlay();
  });
  pauseOverlay.addEventListener('click', function (e) {
    if (e.target.closest('a')) return;
    video.play().catch(function () {});
  });
})();
</script>
@endpush
@endsection
