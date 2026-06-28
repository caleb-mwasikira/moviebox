@extends('layouts.app')

@section('title', $movie->title)

@push('head')
  <style>
    .watch-wrap {
      display: block;
      position: relative;
      overflow-x: hidden;
    }

    /* Video Container Logic */
    .watch-video-col {
      background: #000;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .watch-video-col video {
      display: block;
      width: 100%;
      max-height: calc(100svh - 64px);
      background: #000;
    }

    @media (min-width: 768px) {
      .watch-video-col video {
        max-height: calc(100vh - 120px);
      }
    }

    .watch-meta-bar {
      position: relative;
      z-index: 2;
      width: 100%;
      padding: 10px 14px;
      background: #111;
      border-top: 1px solid #222;
    }

    /* Sliding Sidebar Base */
    .watch-sidebar {
      position: fixed;
      top: 0;
      right: 0;
      width: 320px;
      height: 100%;
      z-index: 4;
      background: var(--watch-sidebar-bg, #fff);
      box-shadow: -5px 0 25px rgba(0,0,0,0.5);
      transform: translateX(100%);
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      flex-direction: column;
    }

    .watch-sidebar.open {
      transform: translateX(0);
    }

    .watch-sidebar-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.7);
      z-index: 3;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s;
    }

    .watch-sidebar-overlay.show {
      opacity: 1;
      visibility: visible;
    }

    .dark .watch-sidebar { --watch-sidebar-bg: #111827; }

    @media (min-width: 1024px) {
        .watch-sidebar { width: 400px; }
    }

    /* ── Skip / play-pause flash indicators ── */
    .watch-video-wrap { position: relative; width: 100%; }
    .watch-flash {
      position: absolute; top: 50%; transform: translateY(-50%);
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      gap: 3px; padding: 14px 20px; border-radius: 50px;
      background: rgba(0,0,0,.6); color: #fff;
      font-size: 10px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;
      pointer-events: none; opacity: 0; z-index: 1;
    }
    .watch-flash.skip-back  { left: 10%; }
    .watch-flash.skip-fwd   { right: 10%; }
    .watch-flash.pp         { left: 50%; transform: translate(-50%, -50%); }
    @keyframes watch-flash-fade { 0%, 20% { opacity: 1; } 100% { opacity: 0; } }
    .watch-flash.active { animation: watch-flash-fade .65s ease-out forwards; }

    /* ── Speed picker ── */
    .speed-picker { position: relative; }
    .speed-menu {
      display: none; position: absolute; bottom: calc(100% + 8px); right: 0;
      width: 96px; background: #111827; border: 1px solid #374151;
      box-shadow: 0 10px 40px rgba(0,0,0,.6); z-index: 1;
    }
    .speed-menu.open { display: block; }
    .speed-item {
      display: block; width: 100%; padding: 9px 14px;
      font-size: .68rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: .05em; color: #9ca3af; background: transparent;
      border: none; border-top: 1px solid #1f2937; cursor: pointer; text-align: left;
    }
    .speed-item:first-child { border-top: none; }
    .speed-item:hover { background: rgba(255,255,255,.08); color: #fff; }
    .speed-item.active { color: #facc15; }
  </style>
@endpush

@section('content')
  <main id="main-content" class="grow">

    <div class="watch-wrap">
      <div class="watch-sidebar-overlay"></div>

      {{-- ── Video Column ── --}}
      <div class="watch-video-col">
        <div class="watch-video-wrap">
          <video id="movie-player" controls autoplay preload="auto" crossorigin="anonymous">
            @foreach ($movie->subtitles as $subtitle)
              <track kind="subtitles"
                src="{{ $subtitle->fileUrl() }}"
                srclang="{{ $subtitle->language_code }}"
                label="{{ $subtitle->language }}"
                {{ $subtitle->is_default ? 'default' : '' }}>
            @endforeach
          </video>
          <div id="flash-back" class="watch-flash skip-back">
            <svg width="26" height="26" fill="currentColor" viewBox="0 0 24 24">
              <path d="M11.99 5V1l-5 5 5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6h-2c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/>
              <text x="7.5" y="15.5" font-size="5.5" font-weight="900" font-family="sans-serif" fill="currentColor" text-anchor="middle">10</text>
            </svg>
            −10s
          </div>
          <div id="flash-fwd" class="watch-flash skip-fwd">
            <svg width="26" height="26" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12.01 5V1l5 5-5 5V7c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6h2c0 4.42-3.58 8-8 8s-8-3.58-8-8 3.58-8 8-8z"/>
              <text x="16.5" y="15.5" font-size="5.5" font-weight="900" font-family="sans-serif" fill="currentColor" text-anchor="middle">10</text>
            </svg>
            +10s
          </div>
          <div id="flash-pp" class="watch-flash pp"></div>
        </div>

        <div class="watch-meta-bar">
          {{-- Single action bar --}}
          <div class="flex items-center justify-end gap-x-1.5">
            {{-- Picture-in-Picture --}}
            <button id="pip-btn" title="Picture in Picture"
              class="flex items-center justify-center w-8 h-8 bg-white/10 text-white hover:bg-white/20 transition-colors"
              style="display:none!important">
              <svg id="pip-icon-enter" width="17" height="17" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 11h-8v6h8v-6zm4 8V4.98C23 3.88 22.1 3 21 3H3C1.9 3 1 3.88 1 4.98V19c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2zm-2 .02H3V5h18v14.02z"/>
              </svg>
              <svg id="pip-icon-exit" width="17" height="17" fill="currentColor" viewBox="0 0 24 24" style="display:none">
                <path d="M21 3H3C1.9 3 1 3.88 1 4.98V19c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V4.98C23 3.88 22.1 3 21 3zm-2 16.02H5V5h14v14.02zM11 11H7v4h4v-4z"/>
              </svg>
            </button>

            {{-- Subtitle drawer trigger --}}
            @if ($movie->subtitles->isNotEmpty())
            @php $defaultSubIdx = $movie->subtitles->search(fn($s) => $s->is_default); @endphp
            <div x-data="subtitlePicker('movie-player', {{ $defaultSubIdx !== false ? $defaultSubIdx : 'null' }})">
              <button @click="open = true"
                :class="active ? 'border-accent text-accent bg-accent/20' : 'border-transparent text-white bg-white/10'"
                class="flex items-center justify-center w-8 h-8 border hover:bg-white/20 transition-colors"
                title="Subtitles">
                <svg width="17" height="17" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 4H5c-1.11 0-2 .9-2 2v12c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H5V6h14v12zM7 15h4v-2H9v-2h2V9H7v6zm6 0h4v-2h-2v-2h2V9h-4v6z"/>
                </svg>
              </button>
              {{-- Subtitle drawer backdrop --}}
              <div x-show="open" style="display:none" class="fixed inset-0 bg-black/70 z-[9998]" @click="open = false"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
              </div>
              {{-- Subtitle drawer panel --}}
              <div x-show="open" style="display:none" class="fixed top-0 right-0 h-full w-72 bg-gray-900 border-l border-gray-800 z-[9999] flex flex-col shadow-2xl"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-800">
                  <span class="text-[10px] font-black uppercase tracking-[.18em] text-gray-400">Subtitles</span>
                  <button @click="open = false" class="text-gray-500 hover:text-white transition-colors">
                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                  </button>
                </div>
                <div class="overflow-y-auto flex-1">
                  <button @click="off()"
                    class="w-full flex items-center justify-between px-5 py-3 text-sm font-bold hover:bg-white/10 transition-colors"
                    :class="!active ? 'text-accent' : 'text-gray-400 hover:text-white'">
                    <span>Off</span>
                    <svg x-show="!active" width="14" height="14" fill="currentColor" viewBox="0 0 24 24" class="text-accent shrink-0">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                  </button>
                  @foreach ($movie->subtitles as $i => $subtitle)
                  <button @click="setTrack({{ $i }})"
                    class="w-full flex items-center justify-between gap-x-3 px-5 py-3 text-sm font-bold hover:bg-white/10 transition-colors border-t border-gray-800/60"
                    :class="trackIndex === {{ $i }} ? 'text-white' : 'text-gray-400 hover:text-white'">
                    <span class="flex items-center gap-x-3 min-w-0">
                      <span class="shrink-0 text-[9px] font-black uppercase tracking-wide px-1.5 py-[2px]"
                        :class="trackIndex === {{ $i }} ? 'bg-accent text-gray-900' : 'bg-gray-800 text-gray-500'">
                        {{ strtoupper(substr($subtitle->language_code, 0, 3)) }}
                      </span>
                      <span class="truncate">{{ $subtitle->language }}</span>
                    </span>
                    <svg x-show="trackIndex === {{ $i }}" width="14" height="14" fill="currentColor" viewBox="0 0 24 24" class="text-accent shrink-0">
                      <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                  </button>
                  @endforeach
                </div>
              </div>
            </div>
            @endif

            {{-- Speed picker --}}
            <div class="speed-picker">
              <button id="speed-btn" onclick="toggleSpeedMenu()"
                class="flex items-center justify-center w-8 h-8 bg-white/10 text-white hover:bg-white/20 transition-colors font-bold"
                style="font-size:.7rem;letter-spacing:.04em;" title="Playback Speed">1×</button>
              <div id="speed-menu" class="speed-menu">
                <button class="speed-item" data-speed="0.5"  onclick="setSpeed(0.5)">0.5×</button>
                <button class="speed-item" data-speed="0.75" onclick="setSpeed(0.75)">0.75×</button>
                <button class="speed-item active" data-speed="1"    onclick="setSpeed(1)">Normal</button>
                <button class="speed-item" data-speed="1.25" onclick="setSpeed(1.25)">1.25×</button>
                <button class="speed-item" data-speed="1.5"  onclick="setSpeed(1.5)">1.5×</button>
                <button class="speed-item" data-speed="1.75" onclick="setSpeed(1.75)">1.75×</button>
                <button class="speed-item" data-speed="2"    onclick="setSpeed(2)">2×</button>
              </div>
            </div>

            {{-- Sidebar toggle --}}
            <button id="toggle-sidebar" class="flex items-center justify-center w-8 h-8 bg-accent/20 text-accent border border-accent/50 hover:bg-accent hover:text-black transition-colors">
              <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
            </button>

            {{-- Back link --}}
            <a href="{{ route('movies.show', $movie) }}" class="flex items-center justify-center w-8 h-8 bg-white/10 text-white hover:bg-white/20 transition-colors">
              <svg class="h-2.5 w-2.5 rotate-180 fill-current" viewBox="0 0 6 10"><path d="M1 1l4 4-4 4"/></svg>
            </a>
          </div>

          {{-- Title section --}}
          <div class="mt-2 pt-2 border-t border-white/[.06]">
            <span class="text-white font-bold text-sm truncate block leading-snug">{{ $movie->title }}</span>
            <span class="text-gray-500 text-[11px] uppercase tracking-wide">{{ $movie->genre }} • {{ $movie->year }}</span>
          </div>
        </div>
      </div>

      {{-- ── Sliding Sidebar ── --}}
      <div class="watch-sidebar">
        <div class="p-4 border-bottom border-gray-100 dark:border-gray-800 flex items-center justify-between">
          <h2 class="font-bold uppercase text-xs tracking-widest text-gray-500">Movie Info</h2>
          <button id="close-sidebar" class="text-gray-400 hover:text-white">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>

        <div class="p-6 overflow-y-auto flex-1">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $movie->title }}</h1>
          <div class="flex flex-wrap gap-4 text-xs font-bold uppercase text-gray-500 mb-6">
            <span class="text-accent">{{ $movie->genre }}</span>
            <span>{{ $movie->year }}</span>
            <span>{{ $movie->duration }} min</span>
          </div>

          @if ($movie->description)
            <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
              {{ $movie->description }}
            </p>
          @endif

          {{-- You can add "Related Movies" or "Cast" here --}}
        </div>
      </div>
    </div>

  </main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
  function subtitlePicker(videoId, defaultIndex) {
    return {
      open: false,
      trackIndex: defaultIndex,
      get active() { return this.trackIndex !== null && this.trackIndex !== false; },
      init() {
        const idx = (this.trackIndex !== null && this.trackIndex !== false) ? this.trackIndex : -1;
        const vid = document.getElementById(videoId);
        const apply = function () {
          const t = vid.textTracks;
          // Set every track to at least 'hidden' so the browser fetches the VTT
          // files. 'disabled' (the browser default) suppresses all network requests.
          for (let i = 0; i < t.length; i++) t[i].mode = i === idx ? 'showing' : 'hidden';
        };
        apply();
        vid.addEventListener('loadedmetadata', apply, { once: true });
      },
      setTrack(index) {
        const t = document.getElementById(videoId).textTracks;
        for (let i = 0; i < t.length; i++) t[i].mode = i === index ? 'showing' : 'hidden';
        this.trackIndex = index;
        this.open = false;
      },
      off() {
        const t = document.getElementById(videoId).textTracks;
        for (let i = 0; i < t.length; i++) t[i].mode = 'hidden';
        this.trackIndex = null;
        this.open = false;
      },
    };
  }
</script>
<script>
  (function () {
    @php
      $uid = Auth::id() ?? 0;
      $exp = time() + 7200;
      $sig = hash_hmac('sha256', $uid . ':' . $exp, config('services.streaming.secret'));
      $hlsToken = base64_encode($uid . ':' . $exp . ':' . $sig);
    @endphp
    const hlsToken = @json($hlsToken);

    // ── HLS Player ──
    const video = document.getElementById('movie-player');
    let hlsInst = null;

    function loadHls(src) {
      if (Hls.isSupported()) {
        if (hlsInst) hlsInst.destroy();
        hlsInst = new Hls({
          xhrSetup: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + hlsToken);
          },
        });
        hlsInst.loadSource(src);
        hlsInst.attachMedia(video);
      } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = src;
      }
    }

    @if ($movie->streaming_path)
      loadHls(@json($movie->streamingUrl()));
    @else
      video.src = @json($movie->videoUrl());
    @endif

    // ── Resume playback ──
    const _key       = 'mvbox_pos_' + @json($movie->uuid);
    const _uuid      = @json($movie->uuid);
    const _isAuth    = @json(auth()->check());
    const _csrf      = @json(csrf_token());
    const _serverPos = @json($savedPosition ?? null);
    let _saveTimer   = null;

    function _syncServer(position, duration) {
      fetch('/watch/progress', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf },
        body: JSON.stringify({ type: 'movie', uuid: _uuid, position: position, duration: duration }),
        keepalive: true,
      }).catch(function () {});
    }

    function _save() {
      const t = video.currentTime, d = video.duration;
      if (!t || !d || t < 5) return;
      if (d - t < 30) {
        localStorage.removeItem(_key);
      } else {
        localStorage.setItem(_key, t.toFixed(1));
        if (_isAuth) _syncServer(t, d);
      }
    }

    video.addEventListener('timeupdate', function () {
      if (_saveTimer) return;
      _saveTimer = setTimeout(function () { _saveTimer = null; _save(); }, 5000);
    });
    video.addEventListener('pause', _save);
    video.addEventListener('ended', function () { localStorage.removeItem(_key); });
    video.addEventListener('loadedmetadata', function () {
      const localPos  = parseFloat(localStorage.getItem(_key) || '0');
      const resumePos = (_serverPos && _serverPos > localPos) ? _serverPos : localPos;
      if (resumePos > 5 && video.duration && resumePos < video.duration - 30) {
        video.currentTime = resumePos;
      }
    });

    // ── Picture-in-Picture ──
    const pipBtn       = document.getElementById('pip-btn');
    const pipIconEnter = document.getElementById('pip-icon-enter');
    const pipIconExit  = document.getElementById('pip-icon-exit');

    if (document.pictureInPictureEnabled) {
      pipBtn.style.removeProperty('display');

      pipBtn.addEventListener('click', function () {
        if (document.pictureInPictureElement) {
          document.exitPictureInPicture();
        } else {
          video.requestPictureInPicture();
        }
      });

      video.addEventListener('enterpictureinpicture', function () {
        pipIconEnter.style.display = 'none';
        pipIconExit.style.display  = '';
      });

      video.addEventListener('leavepictureinpicture', function () {
        pipIconEnter.style.display = '';
        pipIconExit.style.display  = 'none';
      });
    }

    // ── Skip & keyboard shortcuts ──
    const PLAY_ICON  = '<svg width="34" height="34" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>';
    const PAUSE_ICON = '<svg width="34" height="34" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';

    function showFlash(id, html) {
      const el = document.getElementById(id);
      if (!el) return;
      if (html !== undefined) el.innerHTML = html;
      el.classList.remove('active');
      void el.offsetWidth;
      el.classList.add('active');
    }

    window.skipVideo = function (seconds) {
      video.currentTime = Math.max(0, Math.min(video.duration || Infinity, video.currentTime + seconds));
      showFlash(seconds < 0 ? 'flash-back' : 'flash-fwd');
    };

    document.addEventListener('keydown', function (e) {
      if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName) || e.target.isContentEditable) return;
      switch (e.key) {
        case ' ': case 'k':
          e.preventDefault();
          if (video.paused) { video.play(); showFlash('flash-pp', PLAY_ICON); }
          else { video.pause(); showFlash('flash-pp', PAUSE_ICON); }
          break;
        case 'ArrowLeft':
          e.preventDefault(); skipVideo(-10); break;
        case 'ArrowRight':
          e.preventDefault(); skipVideo(10); break;
        case 'f': case 'F':
          e.preventDefault();
          document.fullscreenElement ? document.exitFullscreen()
            : (video.requestFullscreen && video.requestFullscreen().catch(() => {}));
          break;
      }
    });

    // ── Speed picker ──
    const speedBtn  = document.getElementById('speed-btn');
    const speedMenu = document.getElementById('speed-menu');

    window.toggleSpeedMenu = function () {
      speedMenu.classList.toggle('open');
    };

    window.setSpeed = function (rate) {
      video.playbackRate = rate;
      speedBtn.textContent = rate === 1 ? '1×' : rate + '×';
      speedMenu.querySelectorAll('.speed-item').forEach(function (el) {
        el.classList.toggle('active', parseFloat(el.dataset.speed) === rate);
      });
      speedMenu.classList.remove('open');
    };

    document.addEventListener('click', function (e) {
      if (!speedBtn.contains(e.target) && !speedMenu.contains(e.target)) {
        speedMenu.classList.remove('open');
      }
    });
  })();
</script>
@endpush