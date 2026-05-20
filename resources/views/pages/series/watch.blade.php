@extends('layouts.app')

@section('title'){{ $series->title }} — {{ $episode->label() }}@endsection

@push('head')
  <style>
    /* ── Watch page layout ─────────────────────────────────────────────── */
    .watch-wrap {
      display: block;
      position: relative;
      overflow-x: hidden;
    }

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
      background: #000;
      max-height: calc(100svh - 64px);
    }

    @media (min-width: 768px) {
      .watch-video-col video {
        aspect-ratio: 16/9;
        max-height: calc(100vh - 100px);
      }
    }

    .watch-meta {
      width: 100%;
      padding: 12px 16px;
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      box-sizing: border-box;
    }

    .watch-meta-title {
      font-size: .875rem;
      font-weight: 700;
      color: #fff;
    }

    .watch-meta-series {
      font-size: .75rem;
      color: #6b7280;
    }

    .watch-meta-series:hover {
      color: var(--color-accent, #facc15);
    }

    .watch-nav-btns {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-shrink: 0;
    }

    /* Toggle Button (Now visible on all screens) */
    .watch-menu-toggle {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      background: rgba(250, 204, 21, 0.2);
      color: var(--color-accent, #facc15);
      border: 1px solid var(--color-accent, #facc15);
      cursor: pointer;
    }

    .watch-nav-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      background: rgba(255, 255, 255, .1);
      color: #fff;
    }

    .watch-nav-btn.disabled {
      opacity: .25;
      pointer-events: none;
    }

    /* ── UNIVERSAL SLIDING SIDEBAR ── */
    .watch-sidebar {
      position: fixed;
      top: 0;
      right: 0;
      width: 320px;
      height: 100%;
      z-index: 9999;
      background: var(--watch-sidebar-bg, #fff);
      box-shadow: -5px 0 25px rgba(0, 0, 0, 0.5);
      transform: translateX(100%);
      /* Hidden by default on ALL screens */
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      flex-direction: column;
    }

    .watch-sidebar.open {
      transform: translateX(0);
      /* Slides in */
    }

    .watch-sidebar-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.7);
      z-index: 9998;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s;
    }

    .watch-sidebar-overlay.show {
      opacity: 1;
      visibility: visible;
    }

    .watch-sidebar-header {
      padding: 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #e5e7eb;
    }

    .watch-sidebar-content {
      flex: 1;
      overflow-y: auto;
    }

    /* ── LIST STYLES ── */
    .watch-season-header {
      padding: 8px 16px;
      background: #f9fafb;
      border-bottom: 1px solid #e5e7eb;
    }

    .watch-ep-row {
      display: flex;
      gap: 12px;
      padding: 12px 16px;
      border-bottom: 1px solid #f3f4f6;
      text-decoration: none;
    }

    .watch-ep-row.active {
      background: rgba(250, 204, 21, .08);
      border-left: 3px solid var(--color-accent, #facc15);
    }

    .watch-ep-num {
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 30px;
      height: 30px;
      background: #f3f4f6;
      font-size: 11px;
      font-weight: 700;
      color: #6b7280;
    }

    .watch-ep-num.active {
      background: var(--color-accent, #facc15);
      color: #111;
    }

    .watch-ep-title {
      font-size: .813rem;
      font-weight: 700;
      color: #111;
    }

    .watch-ep-title.active {
      color: var(--color-accent, #facc15);
    }

    /* Dark mode */
    .dark .watch-sidebar {
      --watch-sidebar-bg: #111827;
    }

    .dark .watch-sidebar-header {
      border-bottom-color: #374151;
    }

    .dark .watch-season-header {
      background: #1f2937;
      border-bottom-color: #374151;
    }

    .dark .watch-ep-row {
      border-bottom-color: #1f2937;
    }

    .dark .watch-ep-num {
      background: #374151;
      color: #9ca3af;
    }

    .dark .watch-ep-title {
      color: #fff;
    }

    @media (min-width: 1024px) {
      .watch-sidebar { width: 400px; }
    }

    /* ── Autoplay countdown card ── */
    .autoplay-card {
      position: absolute;
      bottom: 1rem;
      right: 1rem;
      width: 252px;
      padding: 1rem;
      background: rgba(9,11,20,.88);
      border: 1px solid rgba(255,255,255,.1);
      backdrop-filter: blur(8px);
      z-index: 30;
      transform: translateX(calc(100% + 1.25rem));
      transition: transform .35s cubic-bezier(.4,0,.2,1);
    }
    .autoplay-card.show { transform: translateX(0); }
    #autoplay-ring { transition: stroke-dashoffset .95s linear; }

    /* ── Skip / play-pause flash indicators ── */
    .watch-video-wrap { position: relative; width: 100%; }
    .watch-flash {
      position: absolute; top: 50%; transform: translateY(-50%);
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      gap: 3px; padding: 14px 20px; border-radius: 50px;
      background: rgba(0,0,0,.6); color: #fff;
      font-size: 10px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;
      pointer-events: none; opacity: 0; z-index: 20;
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
      box-shadow: 0 10px 40px rgba(0,0,0,.6); z-index: 60;
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
      // ── SIDEBAR CONTROLS ──
      const sidebar = document.querySelector('.watch-sidebar');
      const overlay = document.querySelector('.watch-sidebar-overlay');
      const openBtn = document.getElementById('toggle-episodes');
      const closeBtn = document.getElementById('close-sidebar');

      const openMenu = () => { sidebar.classList.add('open'); overlay.classList.add('show'); };
      const closeMenu = () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); };

      openBtn.addEventListener('click', openMenu);
      closeBtn.addEventListener('click', closeMenu);
      overlay.addEventListener('click', closeMenu);

      // ── HLS PLAYER ──
      @php
        $uid = Auth::id() ?? 0;
        $exp = time() + 7200;
        $sig = hash_hmac('sha256', $uid . ':' . $exp, env('APP_SECRET', ''));
        $hlsToken = base64_encode($uid . ':' . $exp . ':' . $sig);
      @endphp
      const hlsToken = @json($hlsToken);

      const video = document.getElementById('episode-player');
      function loadHls(src) {
        if (Hls.isSupported()) {
          const hls = new Hls({
            xhrSetup: function (xhr) {
              xhr.setRequestHeader('Authorization', 'Bearer ' + hlsToken);
            },
          });
          hls.loadSource(src);
          hls.attachMedia(video);
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
          video.src = src;
        }
      }
      const hlsSrc = @json($episode->streamingUrl());
      if (hlsSrc) loadHls(hlsSrc); else video.src = @json($episode->videoUrl());

      // ── Resume playback ──
      const _key       = 'mvbox_pos_' + @json($episode->uuid);
      const _uuid      = @json($episode->uuid);
      const _isAuth    = @json(auth()->check());
      const _csrf      = @json(csrf_token());
      const _serverPos = @json($savedPosition ?? null);
      let _saveTimer   = null;

      function _syncServer(position, duration) {
        fetch('/watch/progress', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': _csrf },
          body: JSON.stringify({ type: 'episode', uuid: _uuid, position: position, duration: duration }),
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

      // ── Autoplay next episode ──
      @if ($next)
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
    })();
  </script>
@endpush

@section('content')
  <main class="grow">
    <div class="watch-wrap">
      <div class="watch-sidebar-overlay"></div>

      <div class="watch-video-col">
        <div class="watch-video-wrap">
          <video id="episode-player" controls autoplay preload="auto" crossorigin="anonymous">
            @foreach ($episode->subtitles as $subtitle)
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
          @if ($next)
          <div id="autoplay-card" class="autoplay-card">
            <p style="font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.15em;color:rgba(255,255,255,.4);margin-bottom:.5rem;">Up Next</p>
            <p style="font-size:.8rem;font-weight:700;color:#fff;margin-bottom:.9rem;line-height:1.3;">{{ $next->label() }}</p>
            <div style="display:flex;align-items:center;gap:.75rem;">
              <div style="position:relative;width:44px;height:44px;flex-shrink:0;">
                <svg width="44" height="44" viewBox="0 0 44 44" style="transform:rotate(-90deg);">
                  <circle cx="22" cy="22" r="18" fill="none" stroke="rgba(255,255,255,.12)" stroke-width="3"/>
                  <circle id="autoplay-ring" cx="22" cy="22" r="18" fill="none" stroke="#facc15" stroke-width="3"
                    stroke-dasharray="113.1" stroke-dashoffset="0"/>
                </svg>
                <span id="autoplay-count" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:900;color:#fff;">5</span>
              </div>
              <div style="display:flex;flex-direction:column;gap:.4rem;flex:1;">
                <a href="{{ route('series.watch', [$series, $next]) }}"
                  style="display:flex;justify-content:center;align-items:center;gap:.35rem;font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;padding:.55rem 0;background:#facc15;color:#111;text-decoration:none;">
                  <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                  Play Now
                </a>
                <button onclick="cancelAutoplay()"
                  style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;padding:.5rem;background:transparent;border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.6);cursor:pointer;width:100%;">
                  Cancel
                </button>
              </div>
            </div>
          </div>
          @endif
        </div>

        <div class="watch-meta">
          <div style="min-width:0; flex:1;">
            <p class="watch-meta-title">{{ $episode->label() }}</p>
            <a href="{{ route('series.show', $series) }}" class="watch-meta-series">{{ $series->title }}</a>
          </div>
          <div class="watch-nav-btns">
            {{-- Picture-in-Picture --}}
            <button id="pip-btn" title="Picture in Picture" class="watch-nav-btn" style="display:none!important">
              <svg id="pip-icon-enter" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 11h-8v6h8v-6zm4 8V4.98C23 3.88 22.1 3 21 3H3C1.9 3 1 3.88 1 4.98V19c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2zm-2 .02H3V5h18v14.02z"/>
              </svg>
              <svg id="pip-icon-exit" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" style="display:none">
                <path d="M21 3H3C1.9 3 1 3.88 1 4.98V19c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V4.98C23 3.88 22.1 3 21 3zm-2 16.02H5V5h14v14.02zM11 11H7v4h4v-4z"/>
              </svg>
            </button>

            {{-- Subtitle picker --}}
            @if ($episode->subtitles->isNotEmpty())
            @php $defaultSubIdx = $episode->subtitles->search(fn($s) => $s->is_default); @endphp
            <div x-data="subtitlePicker('episode-player', {{ $defaultSubIdx !== false ? $defaultSubIdx : 'null' }})" class="relative">
              <button @click="open = !open"
                :class="active ? 'border-accent text-accent' : 'border-transparent text-white'"
                class="watch-nav-btn border hover:bg-white/20 transition-colors"
                title="Subtitles">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 4H5c-1.11 0-2 .9-2 2v12c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H5V6h14v12zM7 15h4v-2H9v-2h2V9H7v6zm6 0h4v-2h-2v-2h2V9h-4v6z"/>
                </svg>
              </button>
              <div x-show="open" @click.away="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                class="absolute bottom-full right-0 mb-2 w-44 bg-gray-900 border border-gray-700 shadow-2xl z-50"
                style="display:none">
                <button @click="off()"
                  :class="!active ? 'text-accent' : 'text-gray-300 hover:text-white'"
                  class="w-full text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wide hover:bg-white/10 transition-colors">
                  Off
                </button>
                @foreach ($episode->subtitles as $i => $subtitle)
                <button @click="setTrack({{ $i }})"
                  :class="trackIndex === {{ $i }} ? 'text-accent' : 'text-gray-300 hover:text-white'"
                  class="w-full text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wide hover:bg-white/10 transition-colors border-t border-gray-800">
                  {{ $subtitle->language }}
                </button>
                @endforeach
              </div>
            </div>
            @endif

            {{-- Speed picker --}}
            <div class="speed-picker">
              <button id="speed-btn" onclick="toggleSpeedMenu()" class="watch-nav-btn" style="font-size:.7rem;font-weight:800;letter-spacing:.04em;" title="Playback Speed">1×</button>
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

            {{-- Skip buttons --}}
            <button onclick="skipVideo(-10)" class="watch-nav-btn" title="Back 10s (←)">
              <svg width="19" height="19" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.99 5V1l-5 5 5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6h-2c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/>
                <text x="7.5" y="15.5" font-size="5.5" font-weight="900" font-family="sans-serif" fill="currentColor" text-anchor="middle">10</text>
              </svg>
            </button>
            <button onclick="skipVideo(10)" class="watch-nav-btn" title="Forward 10s (→)">
              <svg width="19" height="19" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12.01 5V1l5 5-5 5V7c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6h2c0 4.42-3.58 8-8 8s-8-3.58-8-8 3.58-8 8-8z"/>
                <text x="16.5" y="15.5" font-size="5.5" font-weight="900" font-family="sans-serif" fill="currentColor" text-anchor="middle">10</text>
              </svg>
            </button>

            {{-- Toggle Button for all screen sizes --}}
            <button id="toggle-episodes" class="watch-menu-toggle">
              <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" />
              </svg>
            </button>

            <a href="{{ $prev ? route('series.watch', [$series, $prev]) : '#' }}"
              class="watch-nav-btn {{ $prev ? '' : 'disabled' }}">
              <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 6h2v12H6zm3.5 6 8.5 6V6z" />
              </svg>
            </a>
            <a href="{{ $next ? route('series.watch', [$series, $next]) : '#' }}"
              class="watch-nav-btn {{ $next ? '' : 'disabled' }}">
              <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 18l8.5-6L6 6v12zm2.5-6 5.5 3.9V8.1L8.5 12zM16 6h2v12h-2z" />
              </svg>
            </a>
          </div>
        </div>
      </div>

      {{-- Sidebar (Hidden by transform: translateX(100%)) --}}
      <div class="watch-sidebar">
        <div class="watch-sidebar-header">
          <div>
            <p style="font-size:.75rem; font-weight:700; text-transform:uppercase; color: var(--tw-text, #fff);">
              {{ $series->title }}</p>
            <p style="font-size:10px; color:#9ca3af;">{{ $episode->label() }}</p>
          </div>
          <button id="close-sidebar" style="background:none; border:none; color:#6b7280; cursor:pointer; padding:4px;">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
              <path
                d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
            </svg>
          </button>
        </div>

        <div class="watch-sidebar-content">
          @foreach ($series->seasons as $season)
            <div class="watch-season-header">
              <span style="font-size:10px; font-weight:700; text-transform:uppercase;">{{ $season->label() }}</span>
            </div>
            @foreach ($season->episodes as $ep)
              @php $isCurrent = $ep->id === $episode->id; @endphp
              <a href="{{ $ep->videoIsReady() ? route('series.watch', [$series, $ep]) : '#' }}"
                class="watch-ep-row {{ $isCurrent ? 'active' : '' }} {{ !$ep->videoIsReady() ? 'inactive' : '' }}">
                <div class="watch-ep-num {{ $isCurrent ? 'active' : '' }}">
                  {{ $isCurrent ? '▶' : str_pad($ep->episode_number, 2, '0', STR_PAD_LEFT) }}</div>
                <div style="flex:1;">
                  <p class="watch-ep-title {{ $isCurrent ? 'active' : '' }}">{{ $ep->title }}</p>
                  <p style="font-size:10px; color:#9ca3af; margin-top:2px;">
                    {{ $ep->videoIsReady() ? $ep->duration . ' min' : 'Processing' }}</p>
                </div>
              </a>
            @endforeach
          @endforeach
        </div>
      </div>
    </div>
  </main>
@endsection