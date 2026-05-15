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
      width: 100%;
      padding: 12px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
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
      z-index: 9999;
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
      z-index: 9998;
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
  </style>
@endpush

@section('content')
  <main id="main-content" class="grow">

    <div class="watch-wrap">
      <div class="watch-sidebar-overlay"></div>

      {{-- ── Video Column ── --}}
      <div class="watch-video-col">
        <video id="movie-player" controls autoplay preload="auto"></video>

        <div class="watch-meta-bar">
          <div class="flex flex-col">
            <span class="text-white font-bold text-sm">{{ $movie->title }}</span>
            <span class="text-gray-500 text-xs uppercase">{{ $movie->genre }} • {{ $movie->year }}</span>
          </div>

          <div class="flex items-center gap-x-3">
            {{-- Picture-in-Picture --}}
            <button id="pip-btn" title="Picture in Picture" class="flex items-center justify-center w-9 h-9 bg-white/10 text-white hover:bg-white/20 transition-colors" style="display:none!important">
              <svg id="pip-icon-enter" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 11h-8v6h8v-6zm4 8V4.98C23 3.88 22.1 3 21 3H3C1.9 3 1 3.88 1 4.98V19c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2zm-2 .02H3V5h18v14.02z"/>
              </svg>
              <svg id="pip-icon-exit" width="20" height="20" fill="currentColor" viewBox="0 0 24 24" style="display:none">
                <path d="M21 3H3C1.9 3 1 3.88 1 4.98V19c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V4.98C23 3.88 22.1 3 21 3zm-2 16.02H5V5h14v14.02zM11 11H7v4h4v-4z"/>
              </svg>
            </button>
            {{-- Toggle Button --}}
            <button id="toggle-sidebar" class="flex items-center justify-center w-9 h-9 bg-accent/20 text-accent border border-accent/50 hover:bg-accent hover:text-black transition-colors">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
            </button>
            <a href="{{ route('movies.show', $movie) }}" class="flex items-center justify-center w-9 h-9 bg-white/10 text-white hover:bg-white/20 transition-colors">
              <svg class="h-3 w-3 rotate-180 fill-current" viewBox="0 0 6 10"><path d="M1 1l4 4-4 4"/></svg>
            </a>
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
  (function () {
    @php
      $uid = Auth::id() ?? 0;
      $exp = time() + 7200;
      $sig = hash_hmac('sha256', $uid . ':' . $exp, env('APP_SECRET', ''));
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
  })();
</script>
@endpush