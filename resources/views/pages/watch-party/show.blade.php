@extends('layouts.app')

@section('title', 'Watch Party — ' . $party->title())

@push('head')
  <style>
    .wp-wrap {
      position: relative;
      overflow: hidden;
      height: calc(100svh - 64px);
      display: flex;
      flex-direction: column;
    }

    @media (min-width: 1024px) {
      .wp-wrap {
        height: calc(100vh - 90px);
      }
    }

    .wp-video-col {
      background: #000;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      flex: 1;
      min-height: 0;
      overflow: hidden;
      position: relative;
    }

    .wp-video-col video {
      display: block;
      width: 100%;
      background: #000;
      flex: 1;
      min-height: 0;
      object-fit: contain;
    }

    /* ── Bottom bar ─────────────────────────────────────────────── */
    .wp-bar {
      width: 100%;
      min-height: 72px;
      padding: 12px 16px;
      display: flex;
      align-items: center;
      gap: 10px;
      background: #080e1a;
      border-top: 1px solid #1e293b;
      flex-shrink: 0;
      box-sizing: border-box;
      position: relative;
    }

    .wp-bar-info {
      flex: 1;
      min-width: 0;
      overflow: hidden;
    }

    .wp-bar-title {
      font-size: .8rem;
      font-weight: 700;
      color: #fff;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      line-height: 1.2;
    }

    .wp-bar-episode {
      font-size: .6rem;
      color: #475569;
      margin-top: 1px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* Live / status pill */
    .wp-bar-status {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      margin-top: 3px;
    }

    .wp-status-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .wp-status-dot.live {
      background: #4ade80;
      animation: wp-pulse 2.2s ease-in-out infinite;
    }

    .wp-status-dot.syncing {
      background: #facc15;
      animation: wp-pulse 1s ease-in-out infinite;
    }

    .wp-status-dot.idle {
      background: #334155;
    }

    @keyframes wp-pulse {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: .3;
      }
    }

    .wp-status-label {
      font-size: .58rem;
      font-weight: 700;
      letter-spacing: .06em;
      text-transform: uppercase;
    }

    .wp-bar-sep {
      width: 1px;
      height: 28px;
      background: #1e293b;
      flex-shrink: 0;
    }

    /* ── Icon buttons ───────────────────────────────────────────── */
    .wp-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      background: transparent;
      color: #475569;
      border: none;
      cursor: pointer;
      transition: color .15s, background .15s;
      position: relative;
      flex-shrink: 0;
      border-radius: 2px;
    }

    .wp-btn:hover {
      color: #e2e8f0;
      background: rgba(255, 255, 255, .07);
    }

    .wp-btn.wp-active {
      color: #facc15;
      background: rgba(250, 204, 21, .12);
    }

    .wp-btn:disabled {
      opacity: .25;
      cursor: default;
      pointer-events: none;
    }

    /* Grouped buttons section */
    .wp-bar-btns {
      display: flex;
      align-items: center;
      gap: 2px;
      background: #0f172a;
      border: 1px solid #1e293b;
      padding: 2px;
      flex-shrink: 0;
    }

    /* Destructive (end) button */
    .wp-btn-danger {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      background: rgba(239, 68, 68, .1);
      color: #ef4444;
      border: 1px solid rgba(239, 68, 68, .2);
      cursor: pointer;
      transition: background .15s, border-color .15s;
      flex-shrink: 0;
      border-radius: 2px;
    }

    .wp-btn-danger:hover {
      background: rgba(239, 68, 68, .2);
      border-color: rgba(239, 68, 68, .4);
    }

    /* Leave button */
    .wp-btn-leave {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      background: rgba(255, 255, 255, .04);
      color: #94a3b8;
      border: 1px solid #1e293b;
      cursor: pointer;
      transition: background .15s, color .15s;
      flex-shrink: 0;
      border-radius: 2px;
    }

    .wp-btn-leave:hover {
      background: rgba(255, 255, 255, .08);
      color: #e2e8f0;
    }

    .wp-badge {
      position: absolute;
      top: 2px;
      right: 2px;
      min-width: 14px;
      height: 14px;
      border-radius: 7px;
      background: #ef4444;
      color: #fff;
      font-size: .48rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 3px;
      line-height: 1;
    }

    /* ── Sidebar ────────────────────────────────────────────────── */
    .wp-sidebar {
      position: fixed;
      top: 0;
      right: 0;
      width: min(320px, 100vw);
      height: 100%;
      z-index: 9999;
      background: #0f172a;
      box-shadow: -4px 0 28px rgba(0, 0, 0, .7);
      transform: translateX(100%);
      transition: transform .28s cubic-bezier(.4, 0, .2, 1);
      display: flex;
      flex-direction: column;
      border-left: 1px solid #1e293b;
      overflow: hidden;
    }

    .wp-sidebar.open {
      transform: translateX(0);
    }

    .wp-sidebar-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .6);
      z-index: 9998;
      opacity: 0;
      visibility: hidden;
      transition: opacity .28s;
    }

    .wp-sidebar-overlay.show {
      opacity: 1;
      visibility: visible;
    }

    /* ── Sidebar tabs ───────────────────────────────────────────── */
    .wp-tabs {
      display: flex;
      align-items: stretch;
      border-bottom: 1px solid #1e293b;
      flex-shrink: 0;
    }

    .wp-tab {
      flex: 1;
      padding: 11px 4px;
      font-size: .6rem;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      text-align: center;
      color: #6b7280;
      cursor: pointer;
      border: none;
      background: none;
      border-bottom: 2px solid transparent;
      transition: color .15s, border-color .15s;
    }

    .wp-tab:hover {
      color: #d1d5db;
    }

    .wp-tab.active {
      color: #facc15;
      border-bottom-color: #facc15;
    }

    .wp-tab-close {
      width: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6b7280;
      cursor: pointer;
      border: none;
      background: none;
      border-bottom: 2px solid transparent;
      flex-shrink: 0;
    }

    .wp-tab-close:hover {
      color: #fff;
    }

    /* ── Sidebar panels ─────────────────────────────────────────── */
    .wp-sidebar-body {
      flex: 1;
      position: relative;
      overflow: hidden;
    }

    .wp-panel {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .wp-panel-scroll {
      flex: 1;
      overflow-y: auto;
      min-height: 0;
    }

    /* ── Chat ───────────────────────────────────────────────────── */
    .wp-chat-list {
      flex: 1;
      overflow-y: auto;
      min-height: 0;
      padding: 12px 14px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .wp-chat-msg {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .wp-chat-msg.mine {
      align-items: flex-end;
    }

    .wp-chat-bubble {
      padding: 6px 10px;
      font-size: .78rem;
      color: #e2e8f0;
      background: #1e293b;
      word-break: break-word;
      border-radius: 2px;
    }

    .wp-chat-msg.mine .wp-chat-bubble {
      background: rgba(250, 204, 21, .15);
      color: #fef9c3;
    }

    .wp-chat-msg.system {
      justify-content: center;
    }

    .wp-chat-msg.system .wp-chat-bubble {
      background: rgba(255, 255, 255, .05);
      color: #475569;
      font-size: .65rem;
      font-style: italic;
      text-align: center;
      max-width: 100%;
    }

    .wp-chat-meta {
      font-size: .58rem;
      color: #64748b;
    }

    .wp-chat-input {
      display: flex;
      border-top: 1px solid #1e293b;
      flex-shrink: 0;
    }

    .wp-chat-input input {
      flex: 1;
      background: #0a0f1a;
      border: none;
      outline: none;
      color: #e2e8f0;
      padding: 10px 12px;
      font-size: .78rem;
    }

    .wp-chat-input input::placeholder {
      color: #475569;
    }

    .wp-chat-input button {
      padding: 10px 14px;
      background: #facc15;
      color: #111827;
      font-size: .65rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em;
      border: none;
      cursor: pointer;
      flex-shrink: 0;
      transition: background .15s;
    }

    .wp-chat-input button:hover {
      background: #fde047;
    }

    /* ── Members ────────────────────────────────────────────────── */
    .wp-members-header {
      padding: 14px 16px 10px;
      border-bottom: 1px solid #1e293b;
      flex-shrink: 0;
    }

    .wp-members-header-label {
      font-size: .58rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .1em;
      color: #475569;
    }

    .wp-member-row {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 16px;
      border-bottom: 1px solid rgba(30, 41, 59, .6);
      transition: background .1s;
    }

    .wp-member-row:hover {
      background: rgba(255, 255, 255, .03);
    }

    .wp-member-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: #1e293b;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .6rem;
      font-weight: 800;
      color: #facc15;
      flex-shrink: 0;
      text-transform: uppercase;
    }

    .wp-member-avatar.is-host {
      background: rgba(250, 204, 21, .15);
    }

    .wp-member-info {
      flex: 1;
      min-width: 0;
    }

    .wp-member-name {
      font-size: .78rem;
      font-weight: 600;
      color: #e2e8f0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .wp-member-badge {
      display: inline-flex;
      align-items: center;
      gap: 3px;
      font-size: .5rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .07em;
      color: #facc15;
      background: rgba(250, 204, 21, .1);
      padding: 1px 5px;
      margin-left: 5px;
      vertical-align: middle;
    }

    .wp-member-status {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: .6rem;
      color: #4ade80;
      margin-top: 1px;
    }

    .wp-member-dot {
      width: 5px;
      height: 5px;
      border-radius: 50%;
      background: #4ade80;
    }

    /* ── Episode rows ───────────────────────────────────────────── */
    .wp-ep-row {
      display: flex;
      align-items: center;
      gap: 11px;
      padding: 10px 14px;
      border-bottom: 1px solid #1e293b;
      transition: background .1s;
    }

    .wp-ep-row.ep-clickable {
      cursor: pointer;
    }

    .wp-ep-row.ep-clickable:hover {
      background: rgba(255, 255, 255, .04);
    }

    .wp-ep-row.ep-active {
      background: rgba(250, 204, 21, .07);
      padding-left: 11px;
      border-left: 3px solid #facc15;
    }

    .wp-ep-num {
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 28px;
      height: 28px;
      background: #1e293b;
      font-size: .65rem;
      font-weight: 700;
      color: #475569;
    }

    .wp-ep-num.now {
      background: #facc15;
      color: #111;
    }

    /* ── Chat improvements ──────────────────────────────────────── */
    .wp-chat-avatar {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background: #1e293b;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .5rem;
      font-weight: 800;
      color: #94a3b8;
      flex-shrink: 0;
      text-transform: uppercase;
      align-self: flex-end;
    }

    .wp-chat-msg {
      display: flex;
      align-items: flex-end;
      gap: 6px;
    }

    .wp-chat-msg.mine {
      flex-direction: row-reverse;
    }

    .wp-chat-content {
      display: flex;
      flex-direction: column;
      gap: 2px;
      max-width: 82%;
      min-width: 0;
    }

    .wp-chat-msg.mine .wp-chat-content {
      align-items: flex-end;
    }

    .wp-chat-msg.system .wp-chat-content {
      max-width: 90%;
      align-items: center;
    }

    @media (min-width: 1024px) {
      .wp-sidebar {
        width: 340px;
      }
    }

    /* ── Share popover ──────────────────────────────────────────── */
    /* Combined progress bar — overlaid on top of the controls bar (non-host only) */
    .wp-progress-bar {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: rgba(255, 255, 255, .06);
      pointer-events: none;
      z-index: 10;
    }

    .wp-progress-mine {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      background: rgba(148, 163, 184, .45);
      transition: width .5s linear;
    }

    .wp-progress-host-tick {
      position: absolute;
      top: -1px;
      width: 2px;
      height: 5px;
      background: #facc15;
      box-shadow: 0 0 5px rgba(250, 204, 21, .7);
      transform: translateX(-50%);
      transition: left .8s linear;
    }

    /* Go Live button */
    .wp-go-live {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 9px;
      background: rgba(250, 204, 21, .15);
      border: 1px solid rgba(250, 204, 21, .35);
      color: #facc15;
      font-size: .52rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .08em;
      cursor: pointer;
      flex-shrink: 0;
      transition: background .15s, border-color .15s;
      border-radius: 2px;
      white-space: nowrap;
    }

    .wp-go-live:hover {
      background: rgba(250, 204, 21, .28);
      border-color: rgba(250, 204, 21, .6);
    }

    .wp-share-pop {
      position: absolute;
      bottom: calc(100% + 8px);
      right: 0;
      width: 260px;
      background: #0a0f1a;
      border: 1px solid #1e293b;
      box-shadow: 0 8px 32px rgba(0, 0, 0, .7);
      padding: 12px;
      z-index: 1000;
    }

    .wp-share-pop-label {
      font-size: .55rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .1em;
      color: #475569;
      margin-bottom: 8px;
    }

    .wp-share-row {
      display: flex;
      gap: 6px;
      margin-bottom: 8px;
    }

    .wp-share-input {
      flex: 1;
      min-width: 0;
      background: #0f172a;
      border: 1px solid #1e293b;
      color: #64748b;
      padding: 7px 9px;
      font-size: .68rem;
      outline: none;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .wp-share-copy {
      flex-shrink: 0;
      padding: 7px 10px;
      background: #1e293b;
      border: none;
      cursor: pointer;
      color: #e2e8f0;
      font-size: .6rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em;
      transition: background .15s;
    }

    .wp-share-copy:hover {
      background: #334155;
    }

    .wp-share-wa {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      width: 100%;
      padding: 8px 10px;
      background: #25D366;
      color: #fff;
      font-size: .62rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em;
      text-decoration: none;
      transition: background .15s;
    }

    .wp-share-wa:hover {
      background: #1ebe5d;
    }

    /* ── Lobby ──────────────────────────────────────────────── */
    .wp-lobby {
      position: absolute;
      inset: 0;
      z-index: 20;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .wp-lobby-bg {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: .2;
    }

    .wp-lobby-grad {
      position: absolute;
      inset: 0;
      background: linear-gradient(to top, #020408 0%, rgba(2, 4, 8, .8) 50%, rgba(2, 4, 8, .5) 100%);
    }

    .wp-lobby-body {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 22px;
      padding: 28px 24px;
      max-width: 460px;
      width: 100%;
    }

    .wp-lobby-label {
      font-size: .58rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .18em;
      color: #facc15;
    }

    .wp-lobby-title {
      font-size: clamp(1.1rem, 5vw, 1.75rem);
      font-weight: 800;
      color: #fff;
      line-height: 1.2;
      margin-top: -6px;
    }

    .wp-lobby-subtitle {
      font-size: .7rem;
      color: #475569;
      margin-top: -10px;
    }

    .wp-lobby-avatars {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .wp-lobby-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: #1e293b;
      border: 2px solid #020408;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .55rem;
      font-weight: 800;
      color: #facc15;
      text-transform: uppercase;
      margin-left: -7px;
      flex-shrink: 0;
    }

    .wp-lobby-avatar:first-child {
      margin-left: 0;
    }

    .wp-lobby-count {
      font-size: .62rem;
      color: #475569;
      margin-top: 4px;
    }

    .wp-lobby-start-btn {
      display: inline-flex;
      align-items: center;
      gap: 9px;
      padding: 11px 32px;
      background: #facc15;
      color: #111827;
      font-size: .75rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .08em;
      border: none;
      cursor: pointer;
      border-radius: 2px;
      transition: background .15s, transform .1s;
    }

    .wp-lobby-start-btn:hover {
      background: #fde047;
    }

    .wp-lobby-start-btn:active {
      transform: scale(.97);
    }

    .wp-lobby-waiting {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: .68rem;
      color: #475569;
    }

    .wp-lobby-pulse {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #facc15;
      flex-shrink: 0;
      animation: wp-pulse 1.4s ease-in-out infinite;
    }

    .wp-lobby-host {
      font-size: .58rem;
      color: #1e293b;
    }

    .wp-lobby-actions {
      position: absolute;
      bottom: 14px;
      right: 14px;
      display: flex;
      gap: 4px;
    }

    /* ── Panels pop-menu ────────────────────────────────────────── */
    .wp-panels-pop {
      position: absolute;
      bottom: calc(100% + 6px);
      right: 0;
      background: #0a0f1a;
      border: 1px solid #1e293b;
      box-shadow: 0 8px 24px rgba(0, 0, 0, .6);
      min-width: 130px;
      z-index: 1000;
    }

    .wp-panels-pop-item {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 9px 14px;
      font-size: .7rem;
      font-weight: 600;
      color: #94a3b8;
      cursor: pointer;
      border: none;
      background: none;
      width: 100%;
      text-align: left;
      transition: background .1s, color .1s;
    }

    .wp-panels-pop-item:hover {
      background: rgba(255, 255, 255, .05);
      color: #e2e8f0;
    }

    .wp-panels-pop-item.pop-active {
      color: #facc15;
      background: rgba(250, 204, 21, .08);
    }

    .wp-panels-pop-item+.wp-panels-pop-item {
      border-top: 1px solid #1e293b;
    }

    .wp-panels-pop-badge {
      margin-left: auto;
      background: #ef4444;
      color: #fff;
      border-radius: 4px;
      font-size: .5rem;
      font-weight: 800;
      padding: 1px 5px;
    }

    /* ── Play / pause flash ─────────────────────────────────────── */
    .wp-playpause-flash {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 16;
      pointer-events: none;
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: rgba(0, 0, 0, .42);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
    }

    @keyframes wp-pp-show {
      0% {
        opacity: 0;
        transform: translate(-50%, -50%) scale(.68);
      }

      14% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.06);
      }

      42% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
      }

      100% {
        opacity: 0;
        transform: translate(-50%, -50%) scale(.9);
      }
    }

    .wp-playpause-flash.active {
      animation: wp-pp-show .65s ease-out forwards;
    }

    /* ── Double-tap skip flash ──────────────────────────────────── */
    .wp-skip-flash {
      position: absolute;
      top: 40%;
      transform: translateY(-50%);
      z-index: 16;
      pointer-events: none;
      width: 68px;
      height: 68px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .14);
      backdrop-filter: blur(2px);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 2px;
      opacity: 0;
    }

    .wp-skip-flash.left {
      left: 20px;
    }

    .wp-skip-flash.right {
      right: 20px;
    }

    .wp-skip-flash svg {
      width: 20px;
      height: 20px;
      color: #fff;
    }

    .wp-skip-flash span {
      font-size: .52rem;
      font-weight: 800;
      color: #fff;
      letter-spacing: .04em;
    }

    @keyframes wp-skip-show {
      0% {
        opacity: 0;
        transform: translateY(-50%) scale(.72);
      }

      14% {
        opacity: 1;
        transform: translateY(-50%) scale(1.06);
      }

      45% {
        opacity: 1;
        transform: translateY(-50%) scale(1);
      }

      100% {
        opacity: 0;
        transform: translateY(-56%) scale(.88);
      }
    }

    .wp-skip-flash.active {
      animation: wp-skip-show .68s ease-out forwards;
    }

    .wp-lobby-trailer-btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 9px 22px;
      background: transparent;
      border: 1px solid rgba(255, 255, 255, .18);
      color: #e2e8f0;
      font-size: .72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .07em;
      cursor: pointer;
      border-radius: 2px;
      transition: border-color .15s, background .15s;
    }

    .wp-lobby-trailer-btn:hover {
      border-color: rgba(255, 255, 255, .4);
      background: rgba(255, 255, 255, .06);
    }

    [x-cloak] {
      display: none !important;
    }
  </style>
@endpush

@section('content')
  @php $user = auth()->user(); @endphp

  {{-- ── Continue Watching Alone modal ──────────────────────────────────── --}}
  @if ($watchAloneUrl || $isSeries)
    <div id="wp-alone-modal"
      style="display:none;position:fixed;inset:0;z-index:10000;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.88);"
      onclick="if(event.target===this) wpAloneClose()">
      <div style="background:#0f172a;border:1px solid #1e293b;box-shadow:0 24px 60px rgba(0,0,0,.8);max-width:380px;width:100%;padding:2rem 2rem 1.5rem;text-align:center;position:relative;">
        <button onclick="wpAloneClose()"
          style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;cursor:pointer;color:#475569;padding:.25rem;line-height:1;"
          onmouseover="this.style.color='#94a3b8'" onmouseout="this.style.color='#475569'"
          title="Close">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
        <p style="font-size:.52rem;font-weight:800;text-transform:uppercase;letter-spacing:.18em;color:#475569;margin-bottom:.75rem;">
          Continue Watching Alone
        </p>
        <h2 style="font-size:1.1rem;font-weight:800;color:#f1f5f9;line-height:1.3;margin-bottom:.5rem;">
          {{ $party->title() }}
        </h2>
        <p style="font-size:.72rem;color:#475569;margin-bottom:1.5rem;">
          Watch this on your own account.<br>A subscription is required.
        </p>
        <div style="display:flex;flex-direction:column;gap:.5rem;margin-bottom:1.25rem;">
          <a id="wp-alone-login-link" href="{{ $watchAloneUrl ?? '#' }}"
            style="display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.8rem 1.5rem;background:#facc15;color:#111827;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;text-decoration:none;transition:background .15s;"
            onmouseover="this.style.background='#fde047'" onmouseout="this.style.background='#facc15'">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
            I have an account — Log In
          </a>
          <a href="{{ route('register') }}"
            style="display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.8rem 1.5rem;background:transparent;border:1px solid #1e293b;color:#94a3b8;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;text-decoration:none;transition:border-color .15s,color .15s;"
            onmouseover="this.style.borderColor='#334155';this.style.color='#e2e8f0'" onmouseout="this.style.borderColor='#1e293b';this.style.color='#94a3b8'">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            I'm new — Create Account
          </a>
        </div>
        <button onclick="wpAloneClose()"
          style="font-size:.62rem;color:#64748b;text-decoration:underline;background:none;border:none;cursor:pointer;padding:.25rem;"
          onmouseover="this.style.color='#94a3b8'" onmouseout="this.style.color='#64748b'">
          Stay in party
        </button>
      </div>
    </div>
    <script>
      function wpAloneOpen(url) {
        const modal = document.getElementById('wp-alone-modal');
        const link  = document.getElementById('wp-alone-login-link');
        if (url && link) link.href = url;
        modal.style.display = 'flex';
      }
      function wpAloneClose() {
        document.getElementById('wp-alone-modal').style.display = 'none';
      }
      window.addEventListener('wp:continue-alone', function (e) {
        wpAloneOpen(e.detail?.url || null);
      });
    </script>
  @endif

  {{-- ── Non-room states ─────────────────────────────────────────────────── --}}

  @if ($party->isEnded())
    <div class="flex items-center justify-center min-h-[70vh] px-6">
      <div class="text-center max-w-sm">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-800 mb-6">
          <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
        <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Party Ended</p>
        <h1 class="text-xl font-bold text-white mb-3">This party has ended</h1>
        <p class="text-sm text-gray-400 mb-8">The host ended the session. Thanks for watching!</p>
        <a href="{{ route('browse') }}"
          class="inline-flex font-bold uppercase py-3 px-8 text-xs bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
          Browse Content
        </a>
      </div>
    </div>

  @elseif (!$user && !$isMember)
    <div class="flex items-center justify-center min-h-[70vh] px-6 py-12" x-data>
      <div class="text-center max-w-sm">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-accent/10 mb-6">
          <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </div>
        <p class="text-xs font-bold uppercase tracking-widest text-accent mb-2">You're Invited</p>
        <h1 class="text-xl font-bold text-white mb-2">Join the Watch Party</h1>
        <p class="text-sm text-gray-400 mb-1">
          <strong class="text-white">{{ $party->host->name }}</strong> is watching
          <strong class="text-white">{{ $party->title() }}</strong>.
        </p>
        <p class="text-sm text-gray-400 mb-6">
          {{ $party->members->count() }} {{ Str::plural('person', $party->members->count()) }} already watching.
        </p>

        {{-- Guest join form --}}
        <form method="POST" action="{{ route('watch-party.guest-join', $party->token) }}"
              class="max-w-xs mx-auto"
              x-data x-init="$nextTick(() => $el.querySelector('[name=name]')?.focus())">
          @csrf
          <input type="text" name="name" placeholder="Your name (shows in chat)"
            required minlength="2" maxlength="50"
            class="w-full mb-3 bg-gray-900 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-700' }} text-white placeholder-gray-600 px-4 py-3 text-sm focus:border-accent focus:outline-none"
            value="{{ old('name') }}"
            @keydown.stop>
          @error('name')
            <p class="text-xs text-red-400 mb-3 text-left">{{ $message }}</p>
          @enderror
          <button type="submit"
            class="w-full inline-flex justify-center font-bold uppercase py-3 px-8 text-sm bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
            Join as Guest
          </button>
        </form>

        @if ($watchAloneUrl)
          <button @click="$dispatch('wp:continue-alone')"
            class="mt-4 text-xs text-gray-600 hover:text-gray-400 underline transition-colors cursor-pointer">
            Continue watching alone instead
          </button>
        @endif

        <div class="flex items-center gap-3 mt-6 mb-4 max-w-xs mx-auto">
          <div class="flex-1 h-px bg-gray-800"></div>
          <span class="text-xs text-gray-700">have an account?</span>
          <div class="flex-1 h-px bg-gray-800"></div>
        </div>
        <div class="flex gap-3 max-w-xs mx-auto">
          <a href="{{ route('login') }}"
            class="flex-1 inline-flex justify-center font-bold uppercase py-2.5 text-xs border border-gray-700 text-gray-500 hover:border-gray-500 hover:text-gray-300 transition-colors">
            Log In
          </a>
          <a href="{{ route('register') }}"
            class="flex-1 inline-flex justify-center font-bold uppercase py-2.5 text-xs border border-gray-700 text-gray-500 hover:border-gray-500 hover:text-gray-300 transition-colors">
            Create Account
          </a>
        </div>
      </div>
    </div>

  @elseif (!$canAccess)
    <div class="flex items-center justify-center min-h-[70vh] px-6">
      <div class="text-center max-w-sm">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-800 mb-6">
          <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Upgrade Required</p>
        <h1 class="text-xl font-bold text-white mb-3">Watch Party is a premium feature</h1>
        <p class="text-sm text-gray-400 mb-8">
          <strong class="text-white">{{ $party->host->name }}</strong> is waiting. Upgrade to join.
        </p>
        <a href="{{ route('pricing') }}"
          class="inline-flex font-bold uppercase py-3 px-8 text-sm bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
          View Plans
        </a>
      </div>
    </div>

  @elseif (!$isMember)
    <div class="flex items-center justify-center min-h-[70vh] px-6" x-data>
      <div class="text-center max-w-sm">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-accent/10 mb-6">
          <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <p class="text-xs font-bold uppercase tracking-widest text-accent mb-2">You're Invited</p>
        <h1 class="text-xl font-bold text-white mb-2">Join the party?</h1>
        <p class="text-sm text-gray-400 mb-1">
          <strong class="text-white">{{ $party->host->name }}</strong> is watching
          <strong class="text-white">{{ $party->title() }}</strong>.
        </p>
        <p class="text-sm text-gray-400 mb-8">
          {{ $party->members->count() }} {{ Str::plural('person', $party->members->count()) }} already watching.
        </p>
        <a href="{{ route('watch-party.join', $party->token) }}"
          class="inline-flex font-bold uppercase py-3 px-8 text-sm bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
          Join Party
        </a>
        @if ($watchAloneUrl)
          <div class="mt-4">
            <button @click="$dispatch('wp:continue-alone')"
              class="text-xs text-gray-600 hover:text-gray-400 underline transition-colors cursor-pointer">
              Continue watching alone instead
            </button>
          </div>
        @endif
      </div>
    </div>

  @else

    {{-- ── Party room ──────────────────────────────────────────────────────── --}}
    <div class="wp-wrap" x-data="watchParty()" x-init="init()">

      {{-- Kicked screen --}}
      <div x-show="kicked" x-cloak class="flex items-center justify-center min-h-[70vh] px-6">
        <div class="text-center max-w-sm">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-red-900/30 mb-6">
            <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
          </div>
          <p class="text-xs font-bold uppercase tracking-widest text-red-400 mb-2">Removed</p>
          <h1 class="text-xl font-bold text-white mb-3">You were removed from this party</h1>
          <p class="text-sm text-gray-400 mb-8">The host removed you. Contact them to get back in.</p>
          @if ($isGuest)
            <a href="{{ route('browse') }}"
              class="inline-flex font-bold uppercase py-3 px-8 text-xs bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
              Browse Content
            </a>
          @else
            <a href="{{ route('account.watch-party.index') }}"
              class="inline-flex font-bold uppercase py-3 px-8 text-xs bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
              Back to Parties
            </a>
          @endif
        </div>
      </div>

      {{-- Sidebar overlay --}}
      <div class="wp-sidebar-overlay" :class="activeSidebar ? 'show' : ''" @click="activeSidebar = null"></div>

      {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
      <div class="wp-sidebar" :class="activeSidebar ? 'open' : ''">

        {{-- Tab bar --}}
        <div class="wp-tabs">
          <button class="wp-tab" :class="activeSidebar === 'members' ? 'active' : ''"
            @click="activeSidebar = 'members'">Members</button>
          @if ($isSeries)
            <button class="wp-tab" :class="activeSidebar === 'episodes' ? 'active' : ''"
              @click="activeSidebar = 'episodes'">Episodes</button>
          @endif
          <button class="wp-tab" :class="activeSidebar === 'chat' ? 'active' : ''" @click="switchToChat()">Chat</button>
          <button class="wp-tab-close" @click="activeSidebar = null">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
              <path
                d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
            </svg>
          </button>
        </div>

        <div class="wp-sidebar-body">

          {{-- Members panel --}}
          <div class="wp-panel" x-show="activeSidebar === 'members'" x-cloak>
            <div class="wp-members-header">
              <p class="wp-members-header-label">
                Online · <span x-text="members.length">{{ $party->members->count() }}</span>
              </p>
            </div>
            <div class="wp-panel-scroll">
              <template x-for="m in members" :key="m.name">
                <div class="wp-member-row">
                  <div class="wp-member-avatar" :class="m.is_host ? 'is-host' : ''" x-text="m.name.charAt(0)"></div>
                  <div class="wp-member-info">
                    <p class="wp-member-name">
                      <span x-text="m.name"></span>
                      <span x-show="m.is_host" class="wp-member-badge">Host</span>
                    </p>
                    <p class="wp-member-status">
                      <span class="wp-member-dot"></span> Online
                    </p>
                  </div>
                </div>
              </template>
            </div>
          </div>

          {{-- Episodes panel --}}
          @if ($isSeries)
            <div class="wp-panel" x-show="activeSidebar === 'episodes'" x-cloak>
              <div class="px-4 py-3 border-b border-gray-800 flex items-center justify-between flex-shrink-0">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                  Playlist
                </p>
                <p class="text-[10px] text-gray-600">{{ $episodePlaylist->count() }} episodes</p>
              </div>
              <div class="wp-panel-scroll">
                @foreach ($episodePlaylist as $i => $ep)
                  <div class="wp-ep-row" x-bind:class="{
                          'ep-active': currentEpisodeId === {{ $ep['id'] }},
                          'ep-clickable': {{ ($isHost || $isGuest) ? 'true' : 'false' }} && currentEpisodeId !== {{ $ep['id'] }},
                        }"
                        @if ($isHost)
                          @click="currentEpisodeId !== {{ $ep['id'] }} && jumpToEpisode({{ $ep['id'] }})"
                        @elseif ($isGuest)
                          @click="currentEpisodeId !== {{ $ep['id'] }} && $dispatch('wp:continue-alone', { url: {{ Js::from($ep['url'] ?? '') }} })"
                        @endif>
                    <div class="wp-ep-num" :class="currentEpisodeId === {{ $ep['id'] }} ? 'now' : ''">
                      <span x-show="currentEpisodeId === {{ $ep['id'] }}">▶</span>
                      <span x-show="currentEpisodeId !== {{ $ep['id'] }}">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div style="flex:1; min-width:0;">
                      <p class="text-sm leading-tight truncate transition-colors"
                        :class="currentEpisodeId === {{ $ep['id'] }} ? 'text-accent font-bold' : 'text-gray-300'">
                        {{ $ep['label'] }}
                      </p>
                      @if ($isHost)
                        <p x-show="currentEpisodeId === {{ $ep['id'] }}" class="text-[10px] text-gray-600 mt-0.5">Now playing</p>
                      @endif
                    </div>
                    @if ($isHost || $isGuest)
                      <svg x-show="currentEpisodeId !== {{ $ep['id'] }}"
                        class="w-3.5 h-3.5 text-gray-700 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                      </svg>
                    @endif
                  </div>
                @endforeach
              </div>
            </div>
          @endif

          {{-- Chat panel --}}
          <div class="wp-panel" x-show="activeSidebar === 'chat'" x-cloak>
            <div class="wp-chat-list" id="chat-scroll">
              <template x-for="msg in messages" :key="msg.id">
                <div class="wp-chat-msg" :class="msg.is_system ? 'system' : (msg.is_mine ? 'mine' : '')">
                  {{-- Avatar: other-user messages only --}}
                  <div x-show="!msg.is_mine && !msg.is_system" class="wp-chat-avatar" x-text="msg.name.charAt(0)"></div>
                  <div class="wp-chat-content">
                    <div class="wp-chat-meta" x-show="!msg.is_mine && !msg.is_system">
                      <span x-text="msg.name"></span>
                      <span x-show="msg.is_host" style="color:#facc15"> · Host</span>
                      <span x-text="' · ' + msg.time" style="color:#334155"></span>
                    </div>
                    <div class="wp-chat-meta" x-show="msg.is_mine && !msg.is_system" x-text="msg.time"></div>
                    <div class="wp-chat-bubble" x-text="msg.message"></div>
                  </div>
                </div>
              </template>
              <div x-show="messages.length === 0"
                class="flex flex-col items-center justify-center h-full gap-2 text-center px-4">
                <svg class="w-8 h-8 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z" />
                </svg>
                <p class="text-xs text-gray-600">No messages yet</p>
                <p class="text-[10px] text-gray-700">Say hi to the group!</p>
              </div>
            </div>
            <div class="wp-chat-input">
              <input type="text" x-model="chatMessage"
                placeholder="{{ $isHost ? 'Message… /kick name to remove' : 'Say something…' }}"
                @keydown.enter.prevent="sendChat()" maxlength="500">
              <button @click="sendChat()">Send</button>
            </div>
          </div>

        </div>
      </div>

      {{-- ── Video area ───────────────────────────────────────────────────── --}}
      <div class="wp-video-col" x-show="!kicked" x-cloak>

        {{-- Play / pause flash indicator --}}
        <div id="wp-playpause-flash" class="wp-playpause-flash">
          <svg id="wp-pp-play" width="30" height="30" fill="#fff" viewBox="0 0 24 24" style="display:none">
            <path d="M8 5v14l11-7z" />
          </svg>
          <svg id="wp-pp-pause" width="30" height="30" fill="#fff" viewBox="0 0 24 24" style="display:none">
            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
          </svg>
        </div>

        {{-- Double-tap skip flash indicators --}}
        <div id="wp-skip-flash-left" class="wp-skip-flash left">
          <svg fill="currentColor" viewBox="0 0 24 24">
            <path d="M6 6h2v12H6zm3.5 6 8.5 6V6z" />
          </svg>
          <span>10s</span>
        </div>
        <div id="wp-skip-flash-right" class="wp-skip-flash right">
          <svg fill="currentColor" viewBox="0 0 24 24">
            <path d="M6 18l8.5-6L6 6v12zm2.5-6 5.5 3.9V8.1L8.5 12zM16 6h2v12h-2z" />
          </svg>
          <span>10s</span>
        </div>

        {{-- ── Lobby overlay ────────────────────────────────────────────────── --}}
        <div class="wp-lobby" x-show="(inLobby || hostOnline === false) && !watchingTrailer" x-cloak>
          @php $lobbyPoster = $party->watchable?->thumbnailUrl(); @endphp
          @if ($lobbyPoster)
            <img class="wp-lobby-bg" src="{{ $lobbyPoster }}" alt="">
          @endif
          <div class="wp-lobby-grad"></div>
          <div class="wp-lobby-body">
            <p class="wp-lobby-label">Watch Party · Lobby</p>
            <h2 class="wp-lobby-title">{{ $party->title() }}</h2>
            <p class="wp-lobby-subtitle"
              x-text="members.length + ' ' + (members.length === 1 ? 'person' : 'people') + ' in the lobby'"></p>

            {{-- Stacked member avatars --}}
            <div>
              <div class="wp-lobby-avatars">
                <template x-for="(m, i) in members.slice(0, 6)" :key="m.id">
                  <div class="wp-lobby-avatar" x-text="m.name.charAt(0)"></div>
                </template>
              </div>
              <p class="wp-lobby-count"
                x-text="members.length + (members.length === 1 ? ' person' : ' people') + ' waiting'"></p>
            </div>

            @if ($isHost)
              <button class="wp-lobby-start-btn" @click="toggleLobby()">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M8 5v14l11-7z" />
                </svg>
                Start Party
              </button>
              <p class="wp-lobby-host">You can return to lobby anytime from the controls bar</p>
            @else
              <div class="wp-lobby-waiting">
                <span class="wp-lobby-pulse"></span>
                Waiting for host to start…
              </div>
              @if ($trailerSrc)
                <button class="wp-lobby-trailer-btn cursor-pointer" @click="watchTrailer()">
                  <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z" />
                  </svg>
                  Watch Trailer
                </button>
              @endif
              @if ($watchAloneUrl)
                <button @click="$dispatch('wp:continue-alone')" class="wp-lobby-trailer-btn"
                  style="border-color:rgba(148,163,184,.15);color:#475569;font-size:.6rem;">
                  Watch Alone
                </button>
              @endif
            @endif
          </div>
        </div>

        {{-- Back to lobby pill shown while trailer plays --}}
        @if (!$isHost && $trailerSrc)
          <div x-show="watchingTrailer" x-cloak
            style="position:absolute;top:12px;left:50%;transform:translateX(-50%);z-index:25;display:flex;gap:8px;align-items:center;">
            <button @click="stopTrailer()" class="wp-lobby-trailer-btn" style="padding:6px 14px;font-size:.6rem;">
              <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
              </svg>
              Back to Lobby
            </button>
          </div>
        @endif

        <video id="party-video" controls preload="metadata" @if ($isHost) @play="onHostPlay()" @pause="onHostPause()"
        @seeked="onHostSeeked()" @endif>
        </video>

        {{-- ── Bottom controls bar ────────────────────────────────────────── --}}
        <div class="wp-bar">

          {{-- Combined progress bar: my position (gray) + host position (yellow tick) --}}
          @if (!$isHost)
            <div class="wp-progress-bar">
              <div class="wp-progress-mine" :style="'width:' + myProgressPct + '%'"></div>
              <div class="wp-progress-host-tick" :style="'left:' + hostProgressPct + '%'"></div>
            </div>
          @endif

          {{-- Left: title + status --}}
          <div class="wp-bar-info">
            <p class="wp-bar-title">{{ $party->title() }}</p>
            @if ($isSeries)
              <p class="wp-bar-episode" x-text="currentEpisodeLabel || ''">&nbsp;</p>
              @if (!$isHost)
                <div class="wp-bar-status">
                  <span class="wp-status-dot" x-cloak :class="isLive ? 'live' : 'syncing'"></span>
                  <span class="wp-status-label" x-cloak :style="isLive ? 'color:#4ade80' : 'color:#facc15'"
                    x-text="isLive ? 'Watching' : 'Behind'"></span>
                </div>
              @endif
            @else
              <div class="wp-bar-status">
                @if ($isHost)
                  <span class="wp-status-dot live"></span>
                  <span class="wp-status-label" style="color:#4ade80">Host · Live</span>
                @else
                  <span class="wp-status-dot" x-cloak :class="isLive ? 'live' : 'syncing'"></span>
                  <span class="wp-status-label" x-cloak :style="isLive ? 'color:#4ade80' : 'color:#facc15'"
                    x-text="isLive ? 'Watching' : 'Behind'"></span>
                @endif
              </div>
            @endif
          </div>

          {{-- Guest badge --}}
          @if ($isGuest)
            <a href="{{ route('login') }}" class="wp-go-live"
              style="border-color:rgba(148,163,184,.2);color:#475569;font-size:.5rem;"
              title="You're watching as a guest. Log in to save your history.">
              Guest · {{ $guestName }}
            </a>
          @endif

          {{-- Go Live: snap member to host position --}}
          @if (!$isHost)
            <button x-show="!isLive" @click="goLive()" class="wp-go-live" x-cloak title="Jump to host's position">
              <svg width="9" height="9" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z" />
              </svg>
              Go Live
            </button>
          @endif

          {{-- Skip ±10 s --}}
          <div class="wp-bar-btns">
            <button @click="skip(-10)" class="wp-btn" title="Rewind 10 s (←)">
              <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M11.99 5V1l-5 5 5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6h-2c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z" />
                <text x="7.5" y="15.5" font-size="5.5" font-weight="900" font-family="sans-serif" fill="currentColor"
                  text-anchor="middle">10</text>
              </svg>
            </button>
            <button @click="skip(10)" class="wp-btn" title="Forward 10 s (→)">
              <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M12.01 5V1l5 5-5 5V7c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6h2c0 4.42-3.58 8-8 8s-8-3.58-8-8 3.58-8 8-8z" />
                <text x="16.5" y="15.5" font-size="5.5" font-weight="900" font-family="sans-serif" fill="currentColor"
                  text-anchor="middle">10</text>
              </svg>
            </button>
          </div>

          {{-- Episode prev / next (series) --}}
          @if ($isSeries)
            <div class="wp-bar-btns">
              <button @click="prevEpisodeAction()" :disabled="{{ ($isHost || $isGuest) ? '!hasPrevEpisode' : 'true' }}" class="wp-btn"
                title="Previous episode">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M6 6h2v12H6zm3.5 6 8.5 6V6z" />
                </svg>
              </button>
              <button @click="nextEpisodeAction()" :disabled="{{ ($isHost || $isGuest) ? '!hasNextEpisode' : 'true' }}" class="wp-btn"
                title="Next episode">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M6 18l8.5-6L6 6v12zm2.5-6 5.5 3.9V8.1L8.5 12zM16 6h2v12h-2z" />
                </svg>
              </button>
            </div>
          @endif

          {{-- Return to Lobby (host only, shown when lobby is off) --}}
          @if ($isHost)
            <button x-show="!inLobby" @click="toggleLobby()" class="wp-go-live" x-cloak title="Send everyone to lobby">
              <svg width="9" height="9" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
              </svg>
              Lobby
            </button>
          @endif

          <div class="wp-bar-sep"></div>

          {{-- Right: panel toggles --}}
          <div class="wp-bar-btns">

            {{-- Picture-in-Picture --}}
            <button id="pip-btn" class="wp-btn" title="Picture in Picture" style="display:none">
              <svg id="pip-icon-enter" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M19 11h-8v6h8v-6zm4 8V4.98C23 3.88 22.1 3 21 3H3C1.9 3 1 3.88 1 4.98V19c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2zm-2 .02H3V5h18v14.02z" />
              </svg>
              <svg id="pip-icon-exit" width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="display:none">
                <path
                  d="M21 3H3C1.9 3 1 3.88 1 4.98V19c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V4.98C23 3.88 22.1 3 21 3zm-2 16.02H5V5h14v14.02zM11 11H7v4h4v-4z" />
              </svg>
            </button>

            {{-- Panels (Chat + Members + Episodes collapsed) --}}
            <div x-data="{ panelsOpen: false }" class="relative">
              <button @click="panelsOpen = !panelsOpen"
                :class="(activeSidebar === 'chat' || activeSidebar === 'members' || activeSidebar === 'episodes') ? 'wp-active' : ''"
                class="wp-btn" title="Panels">
                <span x-show="unreadChat > 0" class="wp-badge" x-text="unreadChat > 9 ? '9+' : unreadChat"></span>
                <svg width="17" height="17" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M4 8h4V4H4v4zm6 12h4v-4h-4v4zm-6 0h4v-4H4v4zm0-6h4v-4H4v4zm6 0h4v-4h-4v4zm6-10v4h4V4h-4zm-6 4h4V4h-4v4zm6 6h4v-4h-4v4zm0 6h4v-4h-4v4z" />
                </svg>
              </button>
              <div x-show="panelsOpen" @click.away="panelsOpen = false" class="wp-panels-pop" style="display:none">
                <button @click="switchToChat(); panelsOpen = false" :class="activeSidebar === 'chat' ? 'pop-active' : ''"
                  class="wp-panels-pop-item">
                  <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z" />
                  </svg>
                  Chat
                  <span x-show="unreadChat > 0" class="wp-panels-pop-badge"
                    x-text="unreadChat > 9 ? '9+' : unreadChat"></span>
                </button>
                <button @click="activeSidebar = activeSidebar === 'members' ? null : 'members'; panelsOpen = false"
                  :class="activeSidebar === 'members' ? 'pop-active' : ''" class="wp-panels-pop-item">
                  <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                    <path
                      d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                  </svg>
                  Members
                </button>
                @if ($isSeries)
                  <button @click="activeSidebar = activeSidebar === 'episodes' ? null : 'episodes'; panelsOpen = false"
                    :class="activeSidebar === 'episodes' ? 'pop-active' : ''" class="wp-panels-pop-item">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                      <path
                        d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z" />
                    </svg>
                    Episodes
                  </button>
                @endif
              </div>
            </div>

            {{-- Share (host: popover; guest: copy) --}}
            @if ($isHost)
              <div x-data="{ shareOpen: false, copied: false }" class="relative">
                <button @click="shareOpen = !shareOpen" :class="shareOpen ? 'wp-active' : ''" class="wp-btn"
                  title="Share party">
                  <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                    <path
                      d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z" />
                  </svg>
                </button>
                <div x-show="shareOpen" @click.away="shareOpen = false" class="wp-share-pop" style="display:none">
                  <p class="wp-share-pop-label">Invite link</p>
                  <div class="wp-share-row">
                    <input type="text" readonly class="wp-share-input" value="{{ route('watch-party.show', $party->token) }}"
                      onclick="this.select()">
                    <button class="wp-share-copy"
                      @click="navigator.clipboard.writeText('{{ route('watch-party.show', $party->token) }}'); copied = true; setTimeout(() => { copied = false; }, 2000)"
                      x-text="copied ? '✓ Copied' : 'Copy'"></button>
                  </div>
                  <a href="https://wa.me/?text={{ urlencode('Join my Watch Party on ' . config('app.name') . '! 🎬 ' . $party->title() . ': ' . route('watch-party.show', $party->token)) }}"
                    target="_blank" rel="noopener" class="wp-share-wa">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
                      <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" />
                    </svg>
                    Share on WhatsApp
                  </a>
                </div>
              </div>
            @else
              <button x-data="{ copied: false }"
                @click="navigator.clipboard.writeText('{{ route('watch-party.show', $party->token) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                :class="copied ? 'wp-active' : ''" class="wp-btn" title="Copy invite link">
                <svg x-show="!copied" width="15" height="15" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z" />
                </svg>
                <svg x-show="copied" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#facc15">
                  <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                </svg>
              </button>
            @endif

          </div>

          <div class="wp-bar-sep"></div>

          {{-- Leave / End --}}
          @if ($isHost)
            <form method="POST" action="{{ route('watch-party.end', $party->token) }}" x-data
              @submit.prevent="if(confirm('End the party for everyone?')) $el.submit()">
              @csrf @method('DELETE')
              <button type="submit" class="wp-btn-danger" title="End party">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                </svg>
              </button>
            </form>
          @elseif ($isGuest)
            {{-- Guests just navigate away — no server-side leave record --}}
            <a href="{{ route('home') }}" class="wp-btn-leave" title="Leave party"
              onclick="return confirm('Leave this watch party?')">
              <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24">
                <path
                  d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
              </svg>
            </a>
          @else
            <form method="POST" action="{{ route('watch-party.leave', $party->token) }}" x-data
              @submit.prevent="if(confirm('Leave this watch party?')) $el.submit()">
              @csrf
              <button type="submit" class="wp-btn-leave" title="Leave party">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
                </svg>
              </button>
            </form>
          @endif
        </div>
      </div>

    </div>
  @endif

@endsection

@if ($isMember && !$party->isEnded())
  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
      (function () {
        const IS_HOST = @json($isHost);
        const IS_GUEST = @json($isGuest);
        const IS_SERIES = @json($isSeries);
        const PARTY_TOKEN = @json($party->token);
        const MY_USER_ID = @json($isGuest ? $guestId : $user?->id);
        const HOST_USER_ID = @json($party->host_id);
        const HLS_TOKEN = @json($hlsToken);
        const MAIN_SRC = @json($videoSrc ?? '');
        const TRAILER_SRC = @json($trailerSrc ?? '');
        const USE_HLS = @json($useHls);
        const EPISODE_PLAYLIST = @json($episodePlaylist);

        const WS_URL = @json($streamingWsUrl);

        const video = document.getElementById('party-video');
        const stateUrl = '/watch-party/' + PARTY_TOKEN + '/state';
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        let hlsInst = null;
        let _wpComp = null;
        const STORAGE_KEY = 'wp_pos_' + PARTY_TOKEN;

        function showPlayPauseFlash(playing) {
          const el = document.getElementById('wp-playpause-flash');
          const playIcon = document.getElementById('wp-pp-play');
          const pauseIcon = document.getElementById('wp-pp-pause');
          if (!el) return;
          playIcon.style.display = playing ? '' : 'none';
          pauseIcon.style.display = playing ? 'none' : '';
          el.classList.remove('active');
          void el.offsetWidth;
          el.classList.add('active');
        }

        function showSkipFlash(side) {
          const el = document.getElementById('wp-skip-flash-' + side);
          if (!el) return;
          el.classList.remove('active');
          void el.offsetWidth; // reflow to restart animation
          el.classList.add('active');
        }

        function loadVideo(src, useHls, autoplay) {
          if (!src) return;
          if (hlsInst) { hlsInst.destroy(); hlsInst = null; }
          if (useHls && Hls.isSupported()) {
            hlsInst = new Hls({
              xhrSetup: xhr => xhr.setRequestHeader('Authorization', 'Bearer ' + HLS_TOKEN),
              startLevel: -1,
              abrEwmaDefaultEstimate: 1000000,
              maxBufferLength: 60,
              maxMaxBufferLength: 120,
              maxBufferSize: 60 * 1000 * 1000,
            });
            hlsInst.loadSource(src);
            hlsInst.attachMedia(video);
            if (autoplay) hlsInst.on(Hls.Events.MANIFEST_PARSED, () => video.play().catch(() => { }));
          } else {
            video.src = src;
            if (autoplay) video.play().catch(() => { });
          }
        }

        // Picture-in-Picture
        (function () {
          const pipBtn = document.getElementById('pip-btn');
          const pipIconEnter = document.getElementById('pip-icon-enter');
          const pipIconExit = document.getElementById('pip-icon-exit');

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
              pipIconExit.style.display = '';
            });
            video.addEventListener('leavepictureinpicture', function () {
              pipIconEnter.style.display = '';
              pipIconExit.style.display = 'none';
            });
          }
        })();

        // Host: push sync to Go over the existing WebSocket connection.
        function pushSync() {
          if (!ws || ws.readyState !== WebSocket.OPEN) return;
          ws.send(JSON.stringify({ type: 'sync', position: video.currentTime, is_playing: !video.paused }));
        }

        if (IS_HOST) {
          setInterval(pushSync, 4000);
        }

        // Native WebSocket → Go streaming service
        let ws = null;
        let wsReconnectDelay = 1000;

        function connectWS() {
          const url = WS_URL + '/ws/watch-party/' + PARTY_TOKEN + '?auth=' + encodeURIComponent(HLS_TOKEN);
          ws = new WebSocket(url);

          ws.addEventListener('open', () => { wsReconnectDelay = 1000; });

          ws.addEventListener('message', (e) => {
            let msg;
            try { msg = JSON.parse(e.data); } catch { return; }
            const data = msg.data;

            if (msg.event === 'presence.here') {
              if (_wpComp) {
                _wpComp.members = data;
                if (!IS_HOST) _wpComp.hostOnline = data.some(m => m.id === HOST_USER_ID);
              }
            } else if (msg.event === 'presence.joining') {
              if (_wpComp) {
                if (!_wpComp.members.some(m => m.id === data.id)) _wpComp.members.push(data);
                if (!IS_HOST && data.id === HOST_USER_ID) _wpComp.hostOnline = true;
              }
            } else if (msg.event === 'presence.leaving') {
              if (_wpComp) {
                _wpComp.members = _wpComp.members.filter(m => m.id !== data.id);
                if (!IS_HOST && data.id === HOST_USER_ID) _wpComp.hostOnline = false;
              }
            } else if (msg.event === 'WatchPartySync') {
              if (!_wpComp || IS_HOST) return;
              _wpComp.hostPosition = data.position;
              _wpComp.hostIsPlaying = !!data.is_playing;
              _wpComp._hostSyncedAt = Date.now();
            } else if (msg.event === 'WatchPartyChatMessage') {
              if (!_wpComp) return;
              if (_wpComp.messages.some(m => m.id === data.id)) return;
              const chatMsg = { ...data, is_mine: data.user_id === MY_USER_ID };
              _wpComp.messages.push(chatMsg);
              if (_wpComp.activeSidebar === 'chat') {
                _wpComp.$nextTick(() => _wpComp.scrollChat());
              } else if (!chatMsg.is_mine && !chatMsg.is_system) {
                _wpComp.unreadChat++;
              }
            } else if (msg.event === 'WatchPartyEnded') {
              localStorage.removeItem(STORAGE_KEY);
              window.location.reload();
            } else if (msg.event === 'WatchPartyKicked') {
              if (!_wpComp) return;
              if (data.user_id === MY_USER_ID) {
                _wpComp.kicked = true;
              } else {
                _wpComp.members = _wpComp.members.filter(m => m.id !== data.user_id);
              }
            } else if (msg.event === 'WatchPartyLobbyChanged') {
              if (!_wpComp) return;
              const hadTrailer = _wpComp.watchingTrailer;
              _wpComp.inLobby = !!data.active;
              _wpComp.watchingTrailer = false;
              if (hadTrailer) loadVideo(MAIN_SRC, USE_HLS, false);
            } else if (msg.event === 'WatchPartyEpisodeChanged') {
              if (!_wpComp) return;
              _wpComp.currentEpisodeId = data.current_episode_id;
              loadVideo(data.episode.src, data.episode.use_hls, IS_HOST);
              video.currentTime = 0;
            }
          });

          ws.addEventListener('close', () => {
            if (_wpComp && _wpComp.kicked) return; // don't reconnect after kick
            setTimeout(connectWS, wsReconnectDelay);
            wsReconnectDelay = Math.min(wsReconnectDelay * 2, 30_000);
          });

          ws.addEventListener('error', () => { ws.close(); });
        }

        connectWS();

        window.watchParty = function () {
          const playlist = EPISODE_PLAYLIST;

          return {
            members: @json($party->members->filter(fn($m) => !$m->kicked_at)->map(fn($m) => ['id' => $m->user_id, 'name' => $m->user->name, 'is_host' => $m->user_id === $party->host_id])->values()),
            kicked: false,
            inLobby: @json($lobbyActive),
            hostOnline: null,
            watchingTrailer: false,
            myPosition: 0,
            myProgressPct: 0,
            hostPosition: 0,
            hostIsPlaying: false,
            _hostSyncedAt: 0,
            hostProgressPct: 0,
            isLive: true,
            currentEpisodeId: @json($currentEpisodeId),
            activeSidebar: null,
            messages: [],
            chatMessage: '',
            unreadChat: 0,

            get _hostLivePosition() {
              if (this._hostSyncedAt === 0) return this.hostPosition;
              const elapsed = this.hostIsPlaying ? (Date.now() - this._hostSyncedAt) / 1000 : 0;
              return this.hostPosition + elapsed;
            },

            get hasNextEpisode() {
              if (!IS_SERIES || !playlist.length) return false;
              const idx = playlist.findIndex(e => e.id === this.currentEpisodeId);
              return idx !== -1 && idx < playlist.length - 1;
            },

            get hasPrevEpisode() {
              if (!IS_SERIES || !playlist.length) return false;
              return playlist.findIndex(e => e.id === this.currentEpisodeId) > 0;
            },

            get currentEpisodeLabel() {
              if (!IS_SERIES || !playlist.length) return null;
              const ep = playlist.find(e => e.id === this.currentEpisodeId);
              return ep ? ep.label : null;
            },

            init() {
              _wpComp = this;
              loadVideo(MAIN_SRC, USE_HLS, false);
              this.loadInitialState();
              const self = this;

              let _lastSavedAt = 0;
              video.addEventListener('timeupdate', function () {
                self.myPosition = video.currentTime;
                const dur = video.duration;
                self.myProgressPct = (dur > 0) ? Math.min(100, (video.currentTime / dur) * 100) : 0;
                if (!IS_HOST && dur > 0) {
                  self.hostProgressPct = Math.min(100, (self._hostLivePosition / dur) * 100);
                  const diff = self._hostLivePosition - self.myPosition;
                  if (self.isLive && diff > 12) self.isLive = false;
                  else if (!self.isLive && diff < 8) self.isLive = true;
                }
                const now = Date.now();
                if (now - _lastSavedAt > 5000 && video.currentTime > 1) {
                  localStorage.setItem(STORAGE_KEY, video.currentTime);
                  _lastSavedAt = now;
                }
              });

              // Restore saved position once metadata is ready
              let _posRestored = false;
              function tryRestorePosition() {
                if (_posRestored) return;
                const saved = parseFloat(localStorage.getItem(STORAGE_KEY) || '0');
                if (saved > 1) {
                  _posRestored = true;
                  video.currentTime = saved;
                }
              }
              video.addEventListener('loadedmetadata', tryRestorePosition);
              video.addEventListener('canplay', tryRestorePosition);

              window.addEventListener('pagehide', function () {
                if (video.currentTime > 1) localStorage.setItem(STORAGE_KEY, video.currentTime);
              });

              // Double-tap on video to skip ±10 s (mobile)
              let _lastTapTime = 0;
              video.addEventListener('touchend', function (e) {
                if (e.changedTouches.length !== 1) return;
                const touch = e.changedTouches[0];
                const now = Date.now();
                const elapsed = now - _lastTapTime;
                if (elapsed < 300 && elapsed > 40) {
                  const rect = video.getBoundingClientRect();
                  const x = touch.clientX - rect.left;
                  if (x < rect.width * 0.4) {
                    self.skip(-10);
                    e.preventDefault();
                  } else if (x > rect.width * 0.6) {
                    self.skip(10);
                    e.preventDefault();
                  }
                  _lastTapTime = 0;
                } else {
                  _lastTapTime = now;
                }
              }, { passive: false });

              // Play / pause flash
              video.addEventListener('play', () => showPlayPauseFlash(true));
              video.addEventListener('pause', () => { if (!video.ended) showPlayPauseFlash(false); });

              // Keyboard shortcuts
              document.addEventListener('keydown', function (e) {
                if (['INPUT', 'TEXTAREA'].includes(e.target.tagName)) return;
                if (e.metaKey || e.ctrlKey || e.altKey) return;
                switch (e.key) {
                  case ' ':
                  case 'k':
                    e.preventDefault();
                    if (video.paused) video.play().catch(() => { });
                    else video.pause();
                    if (IS_HOST) pushSync();
                    break;
                  case 'ArrowLeft':
                    e.preventDefault();
                    self.skip(-10);
                    break;
                  case 'ArrowRight':
                    e.preventDefault();
                    self.skip(10);
                    break;
                  case 'f':
                  case 'F':
                    e.preventDefault();
                    if (document.fullscreenElement) document.exitFullscreen();
                    else video.requestFullscreen().catch(() => { });
                    break;
                }
              });

            },

            // One-time fetch on join: loads chat history + initial host position
            async loadInitialState() {
              try {
                const res = await fetch(stateUrl + '?since=0', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if (data.messages && data.messages.length) {
                  this.messages = data.messages.map(m => ({ ...m, is_mine: m.user_id === MY_USER_ID }));
                }
                if (!IS_HOST && data.position != null) {
                  this.hostPosition = data.position;
                  this.hostIsPlaying = !!data.is_playing;
                  this._hostSyncedAt = Date.now();
                }
                if (data.lobby_active != null) {
                  this.inLobby = !!data.lobby_active;
                }
              } catch (_) { }
            },

            switchToChat() {
              this.activeSidebar = this.activeSidebar === 'chat' ? null : 'chat';
              if (this.activeSidebar === 'chat') {
                this.unreadChat = 0;
                this.$nextTick(() => this.scrollChat());
              }
            },

            scrollChat() {
              const el = document.getElementById('chat-scroll');
              if (el) el.scrollTop = el.scrollHeight;
            },

            skip(seconds) {
              const dur = video.duration;
              video.currentTime = Math.max(0, Math.min(isFinite(dur) ? dur : Infinity, video.currentTime + seconds));
              if (IS_HOST) pushSync();
              showSkipFlash(seconds < 0 ? 'left' : 'right');
            },

            goLive() {
              const target = this._hostLivePosition;
              if (target > 0) {
                video.currentTime = target;
              }
              if (this.hostIsPlaying) {
                video.play().catch(() => { });
              } else {
                video.pause();
              }
            },

            async toggleLobby() {
              try {
                const res = await fetch('/watch-party/' + PARTY_TOKEN + '/lobby', {
                  method: 'PATCH',
                  headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.inLobby = !!data.lobby_active;
              } catch (_) { }
            },

            watchTrailer() {
              if (!TRAILER_SRC) return;
              this.watchingTrailer = true;
              loadVideo(TRAILER_SRC, true, true);
            },

            stopTrailer() {
              this.watchingTrailer = false;
              video.pause();
              loadVideo(MAIN_SRC, USE_HLS, false);
            },

            onHostPlay() { pushSync(); },
            onHostPause() { pushSync(); },
            onHostSeeked() { pushSync(); },

            nextEpisodeAction() {
              const idx = playlist.findIndex(e => e.id === this.currentEpisodeId);
              if (idx === -1 || idx >= playlist.length - 1) return;
              if (IS_GUEST) {
                window.dispatchEvent(new CustomEvent('wp:continue-alone', { detail: { url: playlist[idx + 1].url } }));
                return;
              }
              if (!ws || ws.readyState !== WebSocket.OPEN) return;
              ws.send(JSON.stringify({ type: 'episode.next' }));
            },

            prevEpisodeAction() {
              const idx = playlist.findIndex(e => e.id === this.currentEpisodeId);
              if (idx <= 0) return;
              if (IS_GUEST) {
                window.dispatchEvent(new CustomEvent('wp:continue-alone', { detail: { url: playlist[idx - 1].url } }));
                return;
              }
              this.jumpToEpisode(playlist[idx - 1].id);
            },

            jumpToEpisode(episodeId) {
              if (IS_GUEST) {
                const ep = playlist.find(e => e.id === episodeId);
                if (ep?.url) window.dispatchEvent(new CustomEvent('wp:continue-alone', { detail: { url: ep.url } }));
                return;
              }
              if (!ws || ws.readyState !== WebSocket.OPEN) return;
              ws.send(JSON.stringify({ type: 'episode.jump', episode_id: episodeId }));
            },

            async sendChat() {
              const msg = this.chatMessage.trim();
              if (!msg) return;

              if (IS_HOST && msg.startsWith('/kick ')) {
                this.chatMessage = '';
                const username = msg.slice(6).trim();
                const res = await fetch('/watch-party/' + PARTY_TOKEN + '/kick', {
                  method: 'PATCH',
                  headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                  body: JSON.stringify({ username }),
                });
                const data = await res.json();
                if (!res.ok) {
                  this.messages.push({ id: Date.now(), name: '', message: data.error || 'User not found.', is_system: true, is_mine: false, is_host: false, time: '' });
                  this.$nextTick(() => this.scrollChat());
                }
                return;
              }

              if (IS_HOST && msg.startsWith('/unkick ')) {
                this.chatMessage = '';
                const username = msg.slice(8).trim();
                const res = await fetch('/watch-party/' + PARTY_TOKEN + '/unkick', {
                  method: 'PATCH',
                  headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                  body: JSON.stringify({ username }),
                });
                const data = await res.json();
                if (!res.ok) {
                  this.messages.push({ id: Date.now(), name: '', message: data.error || 'User not found.', is_system: true, is_mine: false, is_host: false, time: '' });
                  this.$nextTick(() => this.scrollChat());
                }
                return;
              }

              if (!ws || ws.readyState !== WebSocket.OPEN) return;
              this.chatMessage = '';
              ws.send(JSON.stringify({ type: 'chat', message: msg }));
            },
          };
        };
      })();
    </script>
  @endpush
@endif