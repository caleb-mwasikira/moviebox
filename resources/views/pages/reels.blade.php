@extends('layouts.app')

@section('title', 'Reels')

@section('no-footer', '1')

@php
  $__uid = Auth::id() ?? 0;
  $__exp = time() + 7200;
  $__sig = hash_hmac('sha256', $__uid . ':' . $__exp, config('services.streaming.secret'));
  $__hlsToken = base64_encode($__uid . ':' . $__exp . ':' . $__sig);
@endphp

@section('content')
@if ($feed->isEmpty())
  <div class="container-app py-16">
    <x-ui.empty icon="film" title="Nothing to watch yet" text="New titles will appear here as soon as they're ready.">
      <a href="{{ route('movies') }}" class="btn btn-primary">Browse movies</a>
    </x-ui.empty>
  </div>
@else
<div x-data="reels()" x-init="init()" @keydown.window="onKey($event); wake()" @pointerdown="wake(); down($event)" @pointerup="up($event)" @pointercancel="start = null" @wheel.prevent="wheel($event)" @click="tap($event)" @pointermove.passive="wake()" x-cloak
  class="relative h-[calc(100dvh-4rem)] touch-none select-none overflow-hidden bg-black text-white">

  {{-- Gesture surface --}}
  <div class="absolute inset-0" x-ref="surface">

    <div class="absolute inset-0" :class="anim ? 'reel-anim-' + anim : ''" :key="anim + i + e">
      {{-- Backdrop / poster --}}
      <img x-show="cur.poster && (locked || error)" :src="cur.poster" alt="" class="absolute inset-0 z-[1] size-full object-cover" :class="error ? 'opacity-80' : 'opacity-60 blur-sm'">
      <video x-ref="video" class="absolute inset-0 size-full bg-black object-contain" playsinline preload="auto" :loop="cur.episodes.length === 1"></video>
    </div>

    {{-- Double-tap seek indicators --}}
    <div x-show="seek === 'left'" x-cloak x-transition.opacity class="pointer-events-none absolute left-[10%] top-1/2 z-20 -translate-y-1/2 rounded-full bg-black/55 px-5 py-3 text-sm font-bold backdrop-blur">
      <span class="inline-flex items-center gap-1.5"><x-ui.icon name="rewind" class="size-5" /> 10s</span>
    </div>
    <div x-show="seek === 'right'" x-cloak x-transition.opacity class="pointer-events-none absolute right-[10%] top-1/2 z-20 -translate-y-1/2 rounded-full bg-black/55 px-5 py-3 text-sm font-bold backdrop-blur">
      <span class="inline-flex items-center gap-1.5">10s <x-ui.icon name="forward" class="size-5" /></span>
    </div>

    {{-- Play / pause flash --}}
    <div class="pointer-events-none absolute left-1/2 top-1/2 z-20 grid size-20 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-black/55 backdrop-blur transition duration-300"
      :class="flash ? 'scale-100 opacity-100' : 'scale-75 opacity-0'">
      <x-ui.icon name="play" class="size-8" x-show="!playing" /><x-ui.icon name="pause" class="size-8" x-show="playing" x-cloak />
    </div>

    {{-- Paid video the viewer can't play: same translucent card as "unavailable"; swipes pass through --}}
    <div x-show="locked" x-cloak x-transition.opacity class="pointer-events-none absolute inset-0 z-40 grid place-items-center bg-black/45 px-6 backdrop-blur-[2px]">
      <div class="pointer-events-auto w-full max-w-sm rounded-2xl border border-white/15 bg-neutral-900/70 p-6 text-center shadow-2xl backdrop-blur-xl" @click.stop role="alertdialog">
        <span class="mx-auto mb-4 grid size-12 place-items-center rounded-2xl bg-brand/25 text-brand"><x-ui.icon name="lock" class="size-6" /></span>
        <h2 class="text-lg font-bold !text-white">Content unavailable</h2>
        <p class="mt-2 text-sm leading-relaxed text-white/75"
          x-text="'This ' + (ep.kind === 'movie' ? 'movie' : 'episode') + ' is for subscribers only. ' + (authed ? 'Subscribe to keep watching.' : 'Sign in or subscribe to watch it.')"></p>
        <div class="mt-5 flex flex-wrap justify-center gap-2">
          <template x-if="!authed"><button type="button" class="btn btn-glass btn-sm" @click="openAuthModal()">Sign in</button></template>
          <a href="{{ route('pricing') }}" class="btn btn-primary"><x-ui.icon name="lock" class="size-4" /> Unlock</a>
        </div>
        <p class="mt-4 text-xs text-white/50">Swipe up for more free videos and trailers</p>
      </div>
    </div>

    {{-- Unavailable: translucent popup, the backdrop lets swipes pass through --}}
    <div x-show="error" x-cloak x-transition.opacity class="pointer-events-none absolute inset-0 z-40 grid place-items-center bg-black/45 px-6 backdrop-blur-[2px]">
      <div class="pointer-events-auto w-full max-w-sm rounded-2xl border border-white/15 bg-neutral-900/70 p-6 text-center shadow-2xl backdrop-blur-xl" @click.stop role="alertdialog" aria-live="assertive">
        <span class="mx-auto mb-4 grid size-12 place-items-center rounded-2xl bg-red-500/20 text-red-300"><x-ui.icon name="alert" class="size-6" /></span>
        <h2 class="text-lg font-bold !text-white" x-text="error && error.title"></h2>
        <p class="mt-2 text-sm leading-relaxed text-white/75" x-text="error && error.text"></p>
        <div class="mt-5 flex flex-wrap justify-center gap-2">
          <button type="button" class="btn btn-glass btn-sm" @click="retry()"><x-ui.icon name="refresh" class="size-3.5" /> Try again</button>
          <button type="button" class="btn btn-primary btn-sm" @click="skip()">Skip <x-ui.icon name="arrow-right" class="size-3.5" /></button>
        </div>
      </div>
    </div>
  </div>

  {{-- Top: playback progress (always visible) + season episode counter (fades) --}}
  <div class="pointer-events-none absolute inset-x-0 top-0 z-30">
    <div class="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-black/70 to-transparent transition-opacity duration-500" :class="ui ? 'opacity-100' : 'opacity-0'"></div>
    <div class="relative h-1 w-full bg-white/20"><div class="h-full bg-brand" :style="'width:' + pct + '%'"></div></div>
    <div class="relative px-4 pt-3 text-xs font-semibold text-white/85 transition-opacity duration-500" :class="ui ? 'opacity-100' : 'opacity-0'" x-show="counterText" x-cloak x-text="counterText"></div>
  </div>

  {{-- Bottom: info --}}
  <div class="pointer-events-none absolute inset-x-0 bottom-0 z-30 bg-gradient-to-t from-black via-black/70 to-transparent px-5 pb-6 pt-24 transition-opacity duration-500 sm:px-8" :class="ui ? 'opacity-100' : 'opacity-0'" :inert="!ui">
    <div class="pointer-events-auto max-w-xl" @click.stop>
      <div class="mb-2 flex flex-wrap items-center gap-2">
        <span class="badge bg-brand text-white" x-text="badge"></span>
        <span class="badge bg-white/15 text-white" x-show="cur.genre" x-text="cur.genre"></span>
        <span class="text-xs text-white/60" x-show="cur.year" x-text="cur.year"></span>
      </div>
      <h1 class="text-2xl font-extrabold !text-white sm:text-3xl" x-text="cur.title"></h1>
      <p class="mt-1 text-sm font-medium text-white/80" x-show="ep.kind === 'episode'" x-text="ep.label"></p>
      <p class="mt-2 line-clamp-2 text-sm text-white/70" x-text="cur.description"></p>
      <div class="mt-4 flex items-center gap-2">
        <a :href="cur.url" class="btn btn-glass btn-sm">Details <x-ui.icon name="arrow-right" class="size-3.5" /></a>
        <button type="button" class="btn btn-glass btn-sm" @click="toggleMute()">
          <x-ui.icon name="volume-off" class="size-4" x-show="muted" /><x-ui.icon name="volume" class="size-4" x-show="!muted" x-cloak />
          <span x-text="muted ? 'Unmute' : 'Mute'"></span>
        </button>
      </div>
    </div>
    {{-- Sequence indicator: trailer + the season / movie being watched --}}
    <template x-if="cur.episodes.length > 1 && seqIdx.length <= 16">
      <div class="pointer-events-auto mt-5 flex gap-1" @click.stop>
        <template x-for="(idx, n) in seqIdx" :key="idx">
          <button type="button" @click="goEp(idx)" class="h-1.5 flex-1 rounded-full transition"
            :class="n < seqPos ? 'bg-white' : (n === seqPos ? 'bg-brand' : 'bg-white/25 hover:bg-white/40')" :aria-label="cur.episodes[idx].label"></button>
        </template>
      </div>
    </template>
  </div>

  {{-- Desktop nav --}}
  <div class="pointer-events-none absolute right-4 top-1/2 z-30 hidden -translate-y-1/2 flex-col gap-2 transition-opacity duration-500 sm:flex" :class="ui ? 'opacity-100' : 'opacity-0'" :inert="!ui" @click.stop>
    <button type="button" class="btn btn-glass btn-icon pointer-events-auto" @click="prevTitle()" :disabled="i === 0" aria-label="Previous title"><x-ui.icon name="chevron-up" /></button>
    <button type="button" class="btn btn-glass btn-icon pointer-events-auto" @click="nextTitle()" :disabled="i === feed.length - 1" aria-label="Next title"><x-ui.icon name="chevron-down" /></button>
  </div>
  <template x-if="cur.episodes.length > 1">
    <div class="pointer-events-none absolute inset-y-0 left-3 right-3 z-30 hidden items-center justify-between transition-opacity duration-500 sm:flex" :class="ui ? 'opacity-100' : 'opacity-0'" :inert="!ui" @click.stop>
      <button type="button" class="btn btn-glass btn-icon pointer-events-auto" @click="prevEp()" :disabled="e === 0" aria-label="Previous episode"><x-ui.icon name="chevron-left" /></button>
      <button type="button" class="btn btn-glass btn-icon pointer-events-auto mr-14" @click="nextEp()" :disabled="e === cur.episodes.length - 1" aria-label="Next episode"><x-ui.icon name="chevron-right" /></button>
    </div>
  </template>

  {{-- First-time hint --}}
  <div x-show="hint" x-cloak x-transition.opacity @click.stop="dismissHint()" @pointerdown.stop :class="!hint && 'pointer-events-none'" class="absolute inset-0 z-40 grid place-items-center bg-black/70 px-6 text-center">
    <div class="max-w-xs space-y-4">
      <div class="mx-auto grid size-16 place-items-center rounded-2xl bg-brand/20 text-brand"><x-ui.icon name="film" class="size-8" /></div>
      <h2 class="text-2xl font-bold !text-white">Swipe to explore</h2>
      <ul class="space-y-2 text-sm text-white/80">
        <li class="flex items-center justify-center gap-2"><x-ui.icon name="chevron-up" class="size-4" /><x-ui.icon name="chevron-down" class="size-4" /> Up / down — next or previous title</li>
        <li class="flex items-center justify-center gap-2"><x-ui.icon name="chevron-left" class="size-4" /><x-ui.icon name="chevron-right" class="size-4" /> Left / right — previous or next episode</li>
        <li class="text-white/60">Tap to pause · arrow keys work too</li>
      </ul>
      <button type="button" class="btn btn-primary">Got it</button>
    </div>
  </div>
</div>
@endif
@endsection

@if ($feed->isNotEmpty())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
function reels() {
  const FEED = @json($feed);
  const HLS_TOKEN = @json($__hlsToken);
  const THRESHOLD = 50;
  const SEEK_SECONDS = 10;
  const BASE = @json(url('/reels'));
  const START = @json($startVid);

  return {
    feed: FEED,
    authed: @json(auth()->check()),
    i: 0, e: 0,
    anim: '',
    playing: false, muted: true, wantsSound: false, playId: 0, flash: false, hint: false, error: null, ready: false, pct: 0,
    start: null, wheelLock: false, hls: null, flashTimer: null, stallTimer: null, ui: true, idleTimer: null, seek: null, seekTimer: null, lastTap: null, tapTimer: null,

    get cur() { return this.feed[this.i]; },
    get ep() { return this.cur.episodes[this.e]; },
    get badge() {
      if (this.ep.kind === 'trailer') return this.cur.type === 'coming_soon' ? 'Coming soon trailer' : 'Trailer';
      return this.cur.type === 'series' ? 'Series' : 'Movie';
    },
    // Season being watched (for a trailer: the first episode's season).
    get baseSeason() {
      if (this.ep.kind === 'episode') return this.ep.season;
      const first = this.cur.episodes.find(x => x.kind === 'episode');
      return first ? first.season : null;
    },
    // Episodes of that season.
    get seasonEps() { return this.cur.episodes.map((x, k) => k).filter(k => this.cur.episodes[k].kind === 'episode' && this.cur.episodes[k].season === this.baseSeason); },
    // Everything shown in the bottom indicator: the trailer, then the season's episodes (or the movie).
    get seqIdx() { return this.cur.episodes.map((x, k) => k).filter(k => { const v = this.cur.episodes[k]; return v.kind !== 'episode' || v.season === this.baseSeason; }); },
    get seqPos() { return this.seqIdx.indexOf(this.e); },
    get counterText() {
      if (this.ep.kind === 'episode') return 'Season ' + this.ep.season + '  ·  Episode ' + (this.seasonEps.indexOf(this.e) + 1) + ' of ' + this.seasonEps.length;
      if (this.ep.kind === 'trailer' && this.cur.episodes.length > 1) return 'Trailer  ·  swipe left to watch';
      return '';
    },
    get locked() { return !!this.ep.locked; },
    get video() { return this.$refs.video; },

    init() {
      try {
        // Sound preference persists across visits; default is muted until the user unmutes once.
        this.wantsSound = localStorage.getItem('reels_sound') === '1';
        this.muted = !this.wantsSound;
        this.hint = !localStorage.getItem('reels_hint_seen');
      } catch (_) { this.hint = true; }

      // Open on the video named in the URL (the server already puts its title first).
      if (START) {
        FEED.some((item, a) => item.episodes.some((ep, b) => ep.vid === START && (this.i = a, this.e = b, true)));
      }

      const v = this.video;
      v.addEventListener('play', () => { this.playing = true; this.wake(); });
      v.addEventListener('pause', () => { this.playing = false; this.wake(); });
      v.addEventListener('playing', () => { this.ready = true; this.error = null; clearTimeout(this.stallTimer); });
      v.addEventListener('timeupdate', () => { this.pct = v.duration ? (v.currentTime / v.duration) * 100 : 0; });
      v.addEventListener('ended', () => { if (this.cur.episodes.length > 1) this.advance(); });
      v.addEventListener('error', () => {
        if (!v.getAttribute('src') && !this.hls) return;
        const code = v.error && v.error.code;
        this.fail(code === 2 ? 'network' : (code === 3 ? 'decode' : (code === 4 ? 'unsupported' : 'unknown')));
      });

      this.load();
    },

    // ── Playback ──
    load() {
      this.wake();
      // Every video has its own shareable URL.
      history.replaceState(null, '', BASE + '/' + this.ep.vid);
      const v = this.video;
      if (this.hls) { this.hls.destroy(); this.hls = null; }
      v.pause(); v.removeAttribute('src'); v.load();
      this.ready = false; this.error = null; this.pct = 0; clearTimeout(this.stallTimer);
      const ep = this.ep;
      if (ep.locked || !ep.src) return;

      v.muted = this.muted;
      if (ep.hls && window.Hls && Hls.isSupported()) {
        this.hls = new Hls({
          xhrSetup: (xhr) => xhr.setRequestHeader('Authorization', 'Bearer ' + HLS_TOKEN),
          startLevel: 0, abrEwmaDefaultEstimate: 1500000, capLevelToPlayerSize: true,
          maxBufferLength: 20, maxMaxBufferLength: 40,
        });
        this.hls.loadSource(ep.src);
        this.hls.attachMedia(v);
        this.hls.on(Hls.Events.ERROR, (_, d) => {
          if (!d.fatal) return;
          const http = d.response && d.response.code;
          if (http === 401 || http === 403) this.fail('forbidden');
          else if (http === 404) this.fail('missing');
          else if (d.type === Hls.ErrorTypes.MEDIA_ERROR) this.fail('decode');
          else this.fail('network');
        });
      } else {
        v.src = ep.src;
      }
      this.play();
      // No picture after a while → tell the user instead of leaving a blank reel.
      this.stallTimer = setTimeout(() => { if (!this.ready) this.fail('slow'); }, 15000);
    },

    play() {
      const v = this.video;
      const id = ++this.playId;
      v.muted = this.muted;
      v.play().catch((err) => {
        // Ignore rejections from outdated requests (a swipe calls load(), which aborts the
        // previous play()) and anything other than a genuine autoplay block.
        if (id !== this.playId || err.name !== 'NotAllowedError') return;

        // Autoplay with sound blocked — play muted, then restore the saved
        // preference on the user's first interaction.
        this.muted = true; v.muted = true;
        v.play().catch(() => {});
        if (this.wantsSound) {
          const restore = () => { this.muted = false; this.video.muted = false; };
          ['pointerdown', 'keydown', 'touchstart'].forEach(t => window.addEventListener(t, restore, { once: true, passive: true }));
        }
      });
    },

    // ── Auto-hide the overlay after a few seconds without interaction ──
    wake() {
      this.ui = true;
      clearTimeout(this.idleTimer);
      // Keep it visible while paused, on errors, lock screens and the first-run hint.
      this.idleTimer = setTimeout(() => {
        if (this.playing && !this.error && !this.locked && !this.hint) this.ui = false;
      }, 3500);
    },

    // ── Unavailable-video handling ──
    fail(kind) {
      clearTimeout(this.stallTimer);
      if (this.error || this.locked) return;
      const reasons = {
        offline:     ['You appear to be offline', 'Check your internet connection and try again.'],
        network:     ['Video unavailable', "The video couldn't be reached. Your connection may have dropped, or the streaming server is temporarily down."],
        slow:        ['Taking too long to load', 'Your connection seems slow, so this video has not started. Try again or skip to the next one.'],
        decode:      ['Video unavailable', 'This video file looks damaged, so it cannot be played right now. We are looking into it.'],
        unsupported: ['Video unavailable', "Your device or browser can't play this video format."],
        forbidden:   ['Access denied', 'Your session may have expired, or your plan does not include this video. Sign in again and retry.'],
        missing:     ['Video unavailable', 'This video is no longer available. It may have been removed or is still being processed.'],
        unknown:     ['Video unavailable', 'Something went wrong while loading this video.'],
      };
      const [title, text] = reasons[navigator.onLine === false ? 'offline' : kind] || reasons.unknown;
      this.error = { title, text };
      this.ui = true;
    },
    retry() { this.load(); },
    skip() { this.error = null; if (this.e < this.cur.episodes.length - 1) this.nextEp(); else this.nextTitle(); },

    tap(ev) {
      if (this.hint || this.locked || this.error || ev.target.closest('a,button')) return;

      // Outer thirds: double-tap seeks (left = back, right = forward); the centre just plays/pauses.
      const rect = this.$root.getBoundingClientRect();
      const x = ev.clientX !== undefined ? (ev.clientX - rect.left) / rect.width : 0.5;
      const zone = x < 0.33 ? 'left' : (x > 0.67 ? 'right' : 'center');

      if (zone === 'center' || ev.clientX === undefined) { this.togglePlay(); return; }

      const now = Date.now();
      if (this.lastTap && this.lastTap.zone === zone && now - this.lastTap.time < 320) {
        // Second (or further) quick tap on the same side.
        clearTimeout(this.tapTimer);
        this.lastTap = { zone, time: now };
        this.seekBy(zone === 'left' ? -SEEK_SECONDS : SEEK_SECONDS);
        return;
      }

      // First tap: wait briefly to see whether a second one follows before toggling playback.
      this.lastTap = { zone, time: now };
      clearTimeout(this.tapTimer);
      this.tapTimer = setTimeout(() => { this.lastTap = null; this.togglePlay(); }, 320);
    },

    togglePlay() {
      const v = this.video;
      if (v.paused) this.play(); else v.pause();
      this.flash = true;
      clearTimeout(this.flashTimer);
      this.flashTimer = setTimeout(() => this.flash = false, 500);
    },

    seekBy(sec) {
      const v = this.video;
      const dur = isFinite(v.duration) ? v.duration : Infinity;
      v.currentTime = Math.max(0, Math.min(dur, v.currentTime + sec));
      this.seek = sec < 0 ? 'left' : 'right';
      clearTimeout(this.seekTimer);
      this.seekTimer = setTimeout(() => this.seek = null, 600);
    },

    toggleMute() {
      this.muted = !this.muted;
      this.video.muted = this.muted;
      this.wantsSound = !this.muted;
      try { localStorage.setItem('reels_sound', this.wantsSound ? '1' : '0'); } catch (_) {}
    },

    // ── Navigation ──
    go(dir) {
      this.anim = '';
      requestAnimationFrame(() => { this.anim = dir; });
      this.load();
    },
    nextTitle() { if (this.i < this.feed.length - 1) { this.i++; this.e = 0; this.go('up'); } },
    prevTitle() { if (this.i > 0) { this.i--; this.e = 0; this.go('down'); } },
    nextEp() { if (this.e < this.cur.episodes.length - 1) { this.e++; this.go('left'); } },
    prevEp() { if (this.e > 0) { this.e--; this.go('right'); } },
    goEp(idx) {
      if (idx === this.e) return;
      const dir = idx > this.e ? 'left' : 'right';
      this.e = idx; this.go(dir);
    },
    advance() { if (this.e < this.cur.episodes.length - 1) this.nextEp(); else this.nextTitle(); },

    // ── Input ──
    down(ev) { this.start = { x: ev.clientX, y: ev.clientY, t: Date.now() }; },
    up(ev) {
      if (!this.start) return;
      const dx = ev.clientX - this.start.x, dy = ev.clientY - this.start.y;
      this.start = null;
      if (Math.max(Math.abs(dx), Math.abs(dy)) < THRESHOLD) return;
      this.swiped = true; setTimeout(() => this.swiped = false, 50);
      if (Math.abs(dy) > Math.abs(dx)) { dy < 0 ? this.nextTitle() : this.prevTitle(); }
      else { dx < 0 ? this.nextEp() : this.prevEp(); }
    },
    wheel(ev) {
      if (this.wheelLock) return;
      const ax = Math.abs(ev.deltaX), ay = Math.abs(ev.deltaY);
      if (Math.max(ax, ay) < 30) return;
      this.wheelLock = true; setTimeout(() => this.wheelLock = false, 600);
      if (ay >= ax) { ev.deltaY > 0 ? this.nextTitle() : this.prevTitle(); }
      else { ev.deltaX > 0 ? this.nextEp() : this.prevEp(); }
    },
    onKey(ev) {
      if (['INPUT', 'TEXTAREA', 'SELECT'].includes(ev.target.tagName) || ev.metaKey || ev.ctrlKey) return;
      const map = { ArrowDown: () => this.nextTitle(), ArrowUp: () => this.prevTitle(), ArrowRight: () => this.nextEp(), ArrowLeft: () => this.prevEp(),
        ' ': () => this.tap({ target: document.body }), k: () => this.tap({ target: document.body }), m: () => this.toggleMute() };
      if (map[ev.key]) { ev.preventDefault(); map[ev.key](); }
    },
    dismissHint() {
      this.hint = false;
      try { localStorage.setItem('reels_hint_seen', '1'); } catch (_) {}
    },
  };
}
</script>
@endpush
@endif
