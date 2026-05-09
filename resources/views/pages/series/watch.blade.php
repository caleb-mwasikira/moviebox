@extends('layouts.app')

@section('title', $series->title . ' — ' . $episode->label())

@push('head')
  <style>
    /* ── Watch page layout ─────────────────────────────────────────────── */
    .watch-wrap {
      display: block;
    }

    .watch-video-col {
      background: #000;
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
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
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
      gap: 4px;
      flex-shrink: 0;
    }

    .watch-nav-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      background: rgba(255, 255, 255, .1);
      color: #fff;
      transition: background .15s, color .15s;
      flex-shrink: 0;
    }

    .watch-nav-btn:hover {
      background: var(--color-accent, #facc15);
      color: #111;
    }

    .watch-nav-btn.disabled {
      opacity: .25;
      pointer-events: none;
    }

    .watch-sidebar {
      background: var(--watch-sidebar-bg, #fff);
      border-top: 1px solid #e5e7eb;
    }

    .watch-sidebar-header {
      position: sticky;
      top: 0;
      z-index: 10;
      background: var(--watch-sidebar-bg, #fff);
      border-bottom: 1px solid #e5e7eb;
      padding: 10px 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .watch-sidebar-desk-nav {
      display: none;
      align-items: center;
      gap: 4px;
      flex-shrink: 0;
    }

    .watch-sidebar-desk-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      color: #6b7280;
      transition: color .15s;
    }

    .watch-sidebar-desk-btn:hover {
      color: var(--color-accent, #facc15);
    }

    .watch-sidebar-desk-btn.disabled {
      opacity: .3;
      pointer-events: none;
    }

    .watch-season-header {
      padding: 6px 16px;
      background: #f9fafb;
      border-bottom: 1px solid #e5e7eb;
    }

    .watch-ep-row {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 10px 16px;
      border-bottom: 1px solid #f3f4f6;
      text-decoration: none;
      transition: background .15s;
    }

    .watch-ep-row:hover {
      background: #f9fafb;
    }

    .watch-ep-row.active {
      background: rgba(250, 204, 21, .08);
      border-left: 2px solid var(--color-accent, #facc15);
    }

    .watch-ep-row.inactive {
      opacity: .5;
      cursor: not-allowed;
      pointer-events: none;
    }

    .watch-ep-num {
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 28px;
      height: 28px;
      margin-top: 2px;
      background: #f3f4f6;
      font-size: 10px;
      font-weight: 700;
      color: #6b7280;
    }

    .watch-ep-num.active {
      background: var(--color-accent, #facc15);
      color: #111;
    }

    .watch-ep-title {
      font-size: .75rem;
      font-weight: 700;
      color: #111;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .watch-ep-title.active {
      color: var(--color-accent, #facc15);
    }

    .watch-ep-dur {
      font-size: 10px;
      color: #9ca3af;
      margin-top: 2px;
    }

    /* Dark mode */
    .dark .watch-sidebar {
      --watch-sidebar-bg: #111827;
      border-top-color: #374151;
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

    .dark .watch-ep-row:hover {
      background: #1f2937;
    }

    .dark .watch-ep-num {
      background: #374151;
      color: #9ca3af;
    }

    .dark .watch-ep-title {
      color: #fff;
    }

    .dark .watch-sidebar-desk-btn {
      color: #9ca3af;
    }

    /* Desktop layout */
    @media (min-width: 1024px) {
      .watch-wrap {
        display: flex;
        align-items: flex-start;
      }

      .watch-video-col {
        flex: 1 1 0%;
        min-width: 0;
      }

      .watch-nav-btns {
        display: none;
      }

      /* hidden on desktop — use sidebar nav */
      .watch-sidebar {
        width: 320px;
        flex-shrink: 0;
        border-top: none;
        border-left: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        max-height: 100vh;
        overflow-y: auto;
      }

      .dark .watch-sidebar {
        border-left-color: #374151;
      }

      .watch-sidebar-desk-nav {
        display: flex;
      }
    }

    @media (min-width: 1280px) {
      .watch-sidebar {
        width: 384px;
      }
    }
  </style>
@endpush

@section('content')
  <main id="main-content" class="grow">

    <div class="watch-wrap">

      {{-- ── Video column ─────────────────────────────────────── --}}
      <div class="watch-video-col">

        <video id="episode-player" controls autoplay preload="auto" src="{{ $episode->videoUrl() }}"></video>

        {{-- Title + mobile prev/next --}}
        <div class="watch-meta">
          <div style="min-width:0; flex:1;">
            <p class="watch-meta-title">{{ $episode->label() }}</p>
            <a href="{{ route('series.show', $series) }}" class="watch-meta-series">{{ $series->title }}</a>
          </div>
          <div class="watch-nav-btns">
            <a href="{{ $prev ? route('series.watch', [$series, $prev]) : '#' }}"
              class="watch-nav-btn {{ $prev ? '' : 'disabled' }}" title="{{ $prev?->label() ?? 'No previous episode' }}">
              <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 6h2v12H6zm3.5 6 8.5 6V6z" />
              </svg>
            </a>
            <a href="{{ $next ? route('series.watch', [$series, $next]) : '#' }}"
              class="watch-nav-btn {{ $next ? '' : 'disabled' }}" title="{{ $next?->label() ?? 'No next episode' }}">
              <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 18l8.5-6L6 6v12zm2.5-6 5.5 3.9V8.1L8.5 12zM16 6h2v12h-2z" />
              </svg>
            </a>
          </div>
        </div>

      </div>

      {{-- ── Episode sidebar ──────────────────────────────────── --}}
      <div class="watch-sidebar">

        {{-- Sidebar header --}}
        <div class="watch-sidebar-header">
          <div style="min-width:0; flex:1;">
            <p
              style="font-size:.75rem; font-weight:700; text-transform:uppercase; color: var(--tw-text, #fff); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
              {{ $series->title }}
            </p>
            <p
              style="font-size:10px; color:#9ca3af; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
              {{ $episode->label() }}
            </p>
          </div>
          <div class="watch-sidebar-desk-nav">
            <a href="{{ $prev ? route('series.watch', [$series, $prev]) : '#' }}"
              class="watch-sidebar-desk-btn {{ $prev ? '' : 'disabled' }}"
              title="{{ $prev?->label() ?? 'No previous episode' }}">
              <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 6h2v12H6zm3.5 6 8.5 6V6z" />
              </svg>
            </a>
            <a href="{{ $next ? route('series.watch', [$series, $next]) : '#' }}"
              class="watch-sidebar-desk-btn {{ $next ? '' : 'disabled' }}"
              title="{{ $next?->label() ?? 'No next episode' }}">
              <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 18l8.5-6L6 6v12zm2.5-6 5.5 3.9V8.1L8.5 12zM16 6h2v12h-2z" />
              </svg>
            </a>
          </div>
        </div>

        {{-- Episode list by season --}}
        @foreach ($series->seasons as $season)
          <div>
            <div class="watch-season-header">
              <span style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#6b7280;">
                {{ $season->label() }}@if ($season->title) — {{ $season->title }}@endif
              </span>
            </div>
            @foreach ($season->episodes as $ep)
              @php $isCurrent = $ep->id === $episode->id; @endphp
              @if ($ep->videoIsReady())
                <a href="{{ route('series.watch', [$series, $ep]) }}" class="watch-ep-row {{ $isCurrent ? 'active' : '' }}">
                  <div class="watch-ep-num {{ $isCurrent ? 'active' : '' }}">
                    @if ($isCurrent)
                      <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                      </svg>
                    @else
                      {{ str_pad($ep->episode_number, 2, '0', STR_PAD_LEFT) }}
                    @endif
                  </div>
                  <div style="flex:1; min-width:0;">
                    <p class="watch-ep-title {{ $isCurrent ? 'active' : '' }}">{{ $ep->title }}</p>
                    @if ($ep->duration)
                      <p class="watch-ep-dur">{{ $ep->duration }} min</p>
                    @endif
                  </div>
                </a>
              @else
                <div class="watch-ep-row inactive">
                  <div class="watch-ep-num">{{ str_pad($ep->episode_number, 2, '0', STR_PAD_LEFT) }}</div>
                  <div style="flex:1; min-width:0;">
                    <p class="watch-ep-title">{{ $ep->title }}</p>
                    <p class="watch-ep-dur" style="text-transform:uppercase; letter-spacing:.05em;">Processing</p>
                  </div>
                </div>
              @endif
            @endforeach
          </div>
        @endforeach

      </div>

    </div>

  </main>
@endsection