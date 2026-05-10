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
      /* Key change: Ensure video fits in the visible window */
      max-height: calc(100vh - 100px);
      background: #000;
    }

    .watch-meta {
      width: 100%; /* Ensure meta spans full width under video */
      padding: 12px 16px;
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      box-sizing: border-box;
    }

    .watch-wrap {
      display: block;
      position: relative;
      overflow-x: hidden;
    }

    .watch-video-col {
      background: #000;
      width: 100%;
    }

    .watch-video-col video {
      display: block;
      width: 100%;
      aspect-ratio: 16/9;
    }

    .watch-meta {
      padding: 12px 16px;
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
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
      .watch-sidebar {
        width: 400px;
      }

      /* Wider on desktop */
    }
  </style>
@endpush

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
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
      const video = document.getElementById('episode-player');
      function loadHls(src) {
        if (Hls.isSupported()) {
          const hls = new Hls();
          hls.loadSource(src);
          hls.attachMedia(video);
        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
          video.src = src;
        }
      }
      const hlsSrc = @json($episode->streamingUrl());
      if (hlsSrc) loadHls(hlsSrc); else video.src = @json($episode->videoUrl());
    })();
  </script>
@endpush

@section('content')
  <main class="grow">
    <div class="watch-wrap">
      <div class="watch-sidebar-overlay"></div>

      <div class="watch-video-col">
        <video id="episode-player" controls autoplay preload="auto"></video>

        <div class="watch-meta">
          <div style="min-width:0; flex:1;">
            <p class="watch-meta-title">{{ $episode->label() }}</p>
            <a href="{{ route('series.show', $series) }}" class="watch-meta-series">{{ $series->title }}</a>
          </div>
          <div class="watch-nav-btns">
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