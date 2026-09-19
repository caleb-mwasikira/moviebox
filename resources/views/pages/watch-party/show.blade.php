@extends('layouts.app')

@section('title', 'Watch Party — ' . $party->title())

@section('no-footer', '1')

@section('content')
@php $user = auth()->user(); @endphp

{{-- ── Continue watching alone modal ─────────────────────────────── --}}
@if ($watchAloneUrl || $isSeries)
  <div id="wp-alone-modal" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/85 p-4 backdrop-blur-sm" onclick="if(event.target===this) wpAloneClose()">
    <div class="card relative w-full max-w-sm p-8 text-center shadow-pop">
      <button type="button" onclick="wpAloneClose()" class="btn btn-ghost btn-icon absolute right-3 top-3" title="Close"><x-ui.icon name="x" class="size-4" /></button>
      <p class="eyebrow mb-3">Continue watching alone</p>
      <h2 class="text-xl font-bold text-fg">{{ $party->title() }}</h2>
      <p class="mb-6 mt-2 text-sm text-muted">Watch this on your own account.<br>A subscription is required.</p>
      <div class="mb-4 flex flex-col gap-2.5">
        <a id="wp-alone-login-link" href="{{ $watchAloneUrl ?? '#' }}" class="btn btn-primary"><x-ui.icon name="log-out" class="size-4 rotate-180" /> I have an account — log in</a>
        <a href="{{ route('register') }}" class="btn btn-secondary"><x-ui.icon name="user" class="size-4" /> I'm new — create account</a>
      </div>
      <button type="button" onclick="wpAloneClose()" class="text-xs text-muted underline hover:text-fg">Stay in party</button>
    </div>
  </div>
  <script>
    function wpAloneOpen(url) {
      const modal = document.getElementById('wp-alone-modal');
      const link  = document.getElementById('wp-alone-login-link');
      if (url && link) link.href = url;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }
    function wpAloneClose() {
      const modal = document.getElementById('wp-alone-modal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
    window.addEventListener('wp:continue-alone', function (e) {
      wpAloneOpen(e.detail?.url || null);
    });
  </script>
@endif

{{-- ── Non-room states ─────────────────────────────────────────── --}}
@if ($party->isEnded())
  <div class="flex min-h-[70vh] items-center justify-center px-6">
    <div class="max-w-sm text-center">
      <span class="mx-auto mb-6 grid size-16 place-items-center rounded-2xl bg-surface-2 text-muted"><x-ui.icon name="popcorn" class="size-8" /></span>
      <p class="eyebrow mb-2">Party ended</p>
      <h1 class="text-2xl font-bold text-fg">This party has ended</h1>
      <p class="mb-8 mt-3 text-sm text-muted">The host ended the session. Thanks for watching!</p>
      <a href="{{ route('movies') }}" class="btn btn-primary">Browse content</a>
    </div>
  </div>

@elseif (!$user && !$isMember)
  <div class="flex min-h-[70vh] items-center justify-center px-6 py-12" x-data>
    <div class="w-full max-w-sm text-center">
      <span class="mx-auto mb-6 grid size-16 place-items-center rounded-2xl bg-brand-soft text-brand"><x-ui.icon name="popcorn" class="size-8" /></span>
      <p class="eyebrow mb-2">You're invited</p>
      <h1 class="text-2xl font-bold text-fg">Join the watch party</h1>
      <p class="mt-3 text-sm text-muted"><strong class="text-fg">{{ $party->host->name }}</strong> is watching <strong class="text-fg">{{ $party->title() }}</strong>.</p>
      <p class="mb-6 mt-1 text-sm text-muted">{{ $party->members->count() }} {{ Str::plural('person', $party->members->count()) }} already watching.</p>

      <form method="POST" action="{{ route('watch-party.guest-join', $party->token) }}" class="text-left" x-data x-init="$nextTick(() => $el.querySelector('[name=name]')?.focus())">
        @csrf
        <input type="text" name="name" placeholder="Your name (shows in chat)" required minlength="2" maxlength="50"
          class="input @error('name') input-error @enderror" value="{{ old('name') }}" @keydown.stop>
        @error('name')<p class="field-error">{{ $message }}</p>@enderror
        <button type="submit" class="btn btn-primary btn-lg btn-block mt-3">Join as guest</button>
      </form>

      @if ($watchAloneUrl)
        <button type="button" @click="$dispatch('wp:continue-alone')" class="mt-4 text-xs text-muted underline hover:text-fg">Continue watching alone instead</button>
      @endif

      <p class="divider my-6">have an account?</p>
      <div class="flex gap-3">
        <a href="{{ route('login') }}" class="btn btn-secondary flex-1">Log in</a>
        <a href="{{ route('register') }}" class="btn btn-secondary flex-1">Create account</a>
      </div>
    </div>
  </div>

@elseif (!$canAccess)
  <div class="flex min-h-[70vh] items-center justify-center px-6">
    <div class="max-w-sm text-center">
      <span class="mx-auto mb-6 grid size-16 place-items-center rounded-2xl bg-surface-2 text-muted"><x-ui.icon name="lock" class="size-8" /></span>
      <p class="eyebrow mb-2">Upgrade required</p>
      <h1 class="text-2xl font-bold text-fg">Watch party is a premium feature</h1>
      <p class="mb-8 mt-3 text-sm text-muted"><strong class="text-fg">{{ $party->host->name }}</strong> is waiting. Upgrade to join.</p>
      <a href="{{ route('pricing') }}" class="btn btn-primary btn-lg">View plans</a>
    </div>
  </div>

@elseif (!$isMember)
  <div class="flex min-h-[70vh] items-center justify-center px-6" x-data>
    <div class="max-w-sm text-center">
      <span class="mx-auto mb-6 grid size-16 place-items-center rounded-2xl bg-brand-soft text-brand"><x-ui.icon name="popcorn" class="size-8" /></span>
      <p class="eyebrow mb-2">You're invited</p>
      <h1 class="text-2xl font-bold text-fg">Join the party?</h1>
      <p class="mt-3 text-sm text-muted"><strong class="text-fg">{{ $party->host->name }}</strong> is watching <strong class="text-fg">{{ $party->title() }}</strong>.</p>
      <p class="mb-8 mt-1 text-sm text-muted">{{ $party->members->count() }} {{ Str::plural('person', $party->members->count()) }} already watching.</p>
      <a href="{{ route('watch-party.join', $party->token) }}" class="btn btn-primary btn-lg">Join party</a>
      @if ($watchAloneUrl)
        <div class="mt-4"><button type="button" @click="$dispatch('wp:continue-alone')" class="text-xs text-muted underline hover:text-fg">Continue watching alone instead</button></div>
      @endif
    </div>
  </div>

@else
  {{-- ── Party room ────────────────────────────────────────────── --}}
  <div class="relative flex h-[calc(100svh-4rem)] flex-col overflow-hidden bg-black" x-data="watchParty()" x-init="init()">

    {{-- Kicked --}}
    <div x-show="kicked" x-cloak class="flex flex-1 items-center justify-center bg-bg px-6">
      <div class="max-w-sm text-center">
        <span class="mx-auto mb-6 grid size-16 place-items-center rounded-2xl bg-red-500/12 text-red-500"><x-ui.icon name="ban" class="size-8" /></span>
        <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-red-500">Removed</p>
        <h1 class="text-2xl font-bold text-fg">You were removed from this party</h1>
        <p class="mb-8 mt-3 text-sm text-muted">The host removed you. Contact them to get back in.</p>
        @if ($isGuest)
          <a href="{{ route('movies') }}" class="btn btn-primary">Browse content</a>
        @else
          <a href="{{ route('account.watch-party.index') }}" class="btn btn-primary">Back to parties</a>
        @endif
      </div>
    </div>

    {{-- Sidebar overlay --}}
    <div class="absolute inset-0 z-30 bg-black/60 transition-opacity" :class="activeSidebar ? 'opacity-100' : 'pointer-events-none opacity-0'" @click="activeSidebar = null"></div>

    {{-- Sidebar --}}
    <aside class="absolute inset-y-0 right-0 z-40 flex w-[22rem] max-w-[92vw] flex-col border-l border-line bg-surface shadow-pop transition-transform duration-300"
      :class="activeSidebar ? 'translate-x-0' : 'translate-x-full'">
      <div class="flex items-center gap-1 border-b border-line p-2">
        <button type="button" class="nav-link flex-1 text-center" :class="activeSidebar === 'members' && 'is-active'" @click="activeSidebar = 'members'">Members</button>
        @if ($isSeries)
          <button type="button" class="nav-link flex-1 text-center" :class="activeSidebar === 'episodes' && 'is-active'" @click="activeSidebar = 'episodes'">Episodes</button>
        @endif
        <button type="button" class="nav-link flex-1 text-center" :class="activeSidebar === 'chat' && 'is-active'" @click="switchToChat()">Chat</button>
        <button type="button" class="btn btn-ghost btn-icon" @click="activeSidebar = null" aria-label="Close"><x-ui.icon name="x" class="size-4" /></button>
      </div>

      <div class="relative min-h-0 flex-1">
        {{-- Members --}}
        <div class="absolute inset-0 flex flex-col" x-show="activeSidebar === 'members'" x-cloak>
          <p class="border-b border-line px-4 py-3 text-[11px] font-semibold uppercase tracking-widest text-muted">Online · <span x-text="members.length">{{ $party->members->count() }}</span></p>
          <div class="flex-1 overflow-y-auto p-2">
            <template x-for="m in members" :key="m.id">
              <div class="flex items-center gap-3 rounded-xl px-3 py-2.5">
                <span class="grid size-9 shrink-0 place-items-center rounded-full text-sm font-bold uppercase" :class="m.is_host ? 'bg-brand text-white' : 'bg-surface-3 text-fg'" x-text="m.name.charAt(0)"></span>
                <div class="min-w-0">
                  <p class="flex items-center gap-2 truncate text-sm font-semibold text-fg"><span x-text="m.name" class="truncate"></span><span x-show="m.is_host" class="badge badge-brand">Host</span></p>
                  <p class="flex items-center gap-1.5 text-xs text-muted"><span class="size-1.5 rounded-full bg-emerald-500"></span> Online</p>
                </div>
              </div>
            </template>
          </div>
        </div>

        {{-- Episodes --}}
        @if ($isSeries)
          <div class="absolute inset-0 flex flex-col" x-show="activeSidebar === 'episodes'" x-cloak>
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
              <p class="text-[11px] font-semibold uppercase tracking-widest text-muted">Playlist</p>
              <p class="text-xs text-muted">{{ $episodePlaylist->count() }} episodes</p>
            </div>
            <div class="flex-1 overflow-y-auto">
              @foreach ($episodePlaylist as $i => $ep)
                <div class="flex items-center gap-3 border-b border-line px-4 py-3 transition"
                  x-bind:class="{
                    'bg-brand-soft': currentEpisodeId === {{ $ep['id'] }},
                    'cursor-pointer hover:bg-surface-2': {{ ($isHost || $isGuest) ? 'true' : 'false' }} && currentEpisodeId !== {{ $ep['id'] }},
                  }"
                  @if ($isHost)
                    @click="currentEpisodeId !== {{ $ep['id'] }} && jumpToEpisode({{ $ep['id'] }})"
                  @elseif ($isGuest)
                    @click="currentEpisodeId !== {{ $ep['id'] }} && $dispatch('wp:continue-alone', { url: {{ Js::from($ep['url'] ?? '') }} })"
                  @endif>
                  <span class="grid size-8 shrink-0 place-items-center rounded-lg text-xs font-bold" :class="currentEpisodeId === {{ $ep['id'] }} ? 'bg-brand text-white' : 'bg-surface-3 text-muted'">
                    <span x-show="currentEpisodeId === {{ $ep['id'] }}">▶</span>
                    <span x-show="currentEpisodeId !== {{ $ep['id'] }}">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                  </span>
                  <div class="min-w-0 flex-1">
                    <p class="truncate text-sm" :class="currentEpisodeId === {{ $ep['id'] }} ? 'font-semibold text-brand' : 'text-fg-soft'">{{ $ep['label'] }}</p>
                    @if ($isHost)<p x-show="currentEpisodeId === {{ $ep['id'] }}" class="text-[11px] text-muted">Now playing</p>@endif
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif

        {{-- Chat --}}
        <div class="absolute inset-0 flex flex-col" x-show="activeSidebar === 'chat'" x-cloak>
          <div class="flex-1 space-y-3 overflow-y-auto p-4" id="chat-scroll">
            <template x-for="msg in messages" :key="msg.id">
              <div :class="msg.is_system ? 'text-center' : (msg.is_mine ? 'flex flex-row-reverse gap-2' : 'flex gap-2')">
                <span x-show="!msg.is_mine && !msg.is_system" class="grid size-7 shrink-0 place-items-center rounded-full bg-surface-3 text-xs font-bold uppercase text-fg" x-text="msg.name.charAt(0)"></span>
                <div :class="msg.is_mine ? 'items-end' : ''" class="flex min-w-0 max-w-[85%] flex-col" x-bind:style="msg.is_system ? 'max-width:100%' : ''">
                  <p class="mb-0.5 text-[11px] text-muted" x-show="!msg.is_mine && !msg.is_system">
                    <span x-text="msg.name" class="font-semibold text-fg-soft"></span>
                    <span x-show="msg.is_host" class="text-brand"> · Host</span>
                    <span x-text="' · ' + msg.time"></span>
                  </p>
                  <p class="mb-0.5 text-[11px] text-muted" x-show="msg.is_mine && !msg.is_system" x-text="msg.time"></p>
                  <p class="break-words text-sm" :class="msg.is_system ? 'text-xs italic text-muted' : (msg.is_mine ? 'rounded-2xl rounded-tr-sm bg-brand px-3.5 py-2 text-white' : 'rounded-2xl rounded-tl-sm bg-surface-2 px-3.5 py-2 text-fg')" x-text="msg.message"></p>
                </div>
              </div>
            </template>
            <div x-show="messages.length === 0" class="flex h-full flex-col items-center justify-center gap-2 px-4 text-center">
              <x-ui.icon name="message" class="size-8 text-muted" />
              <p class="text-sm font-medium text-fg-soft">No messages yet</p>
              <p class="text-xs text-muted">Say hi to the group!</p>
            </div>
          </div>
          <div class="flex gap-2 border-t border-line p-3">
            <input type="text" x-model="chatMessage" class="input" placeholder="{{ $isHost ? 'Message… /kick name to remove' : 'Say something…' }}" @keydown.enter.prevent="sendChat()" maxlength="500">
            <button type="button" @click="sendChat()" class="btn btn-primary btn-icon shrink-0" aria-label="Send"><x-ui.icon name="send" class="size-4" /></button>
          </div>
        </div>
      </div>
    </aside>

    {{-- ── Video area ─────────────────────────────────────────── --}}
    <div class="relative min-h-0 flex-1 bg-black" x-show="!kicked" x-cloak>
      <div id="wp-playpause-flash" class="wp-playpause-flash">
        <svg id="wp-pp-play" width="30" height="30" fill="#fff" viewBox="0 0 24 24" style="display:none"><path d="M8 5v14l11-7z"/></svg>
        <svg id="wp-pp-pause" width="30" height="30" fill="#fff" viewBox="0 0 24 24" style="display:none"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
      </div>
      <div id="wp-skip-flash-left" class="wp-skip-flash left">
        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M11.99 5V1l-5 5 5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6h-2c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/></svg>
        <span>10s</span>
      </div>
      <div id="wp-skip-flash-right" class="wp-skip-flash right">
        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12.01 5V1l5 5-5 5V7c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6h2c0 4.42-3.58 8-8 8s-8-3.58-8-8 3.58-8 8-8z"/></svg>
        <span>10s</span>
      </div>

      {{-- Lobby overlay --}}
      <div class="absolute inset-0 z-[15] flex items-center justify-center overflow-hidden bg-neutral-950" x-show="(inLobby || hostOnline === false) && !watchingTrailer" x-cloak>
        @php $lobbyPoster = $party->watchable?->thumbnailUrl(); @endphp
        @if ($lobbyPoster)<img class="absolute inset-0 size-full scale-110 object-cover opacity-30 blur-xl" src="{{ $lobbyPoster }}" alt="">@endif
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-black/30"></div>
        <div class="relative flex max-w-md flex-col items-center gap-5 px-6 text-center">
          <p class="eyebrow">Watch party · Lobby</p>
          <h2 class="text-3xl font-extrabold !text-white sm:text-4xl">{{ $party->title() }}</h2>
          <p class="text-sm text-white/60" x-text="members.length + ' ' + (members.length === 1 ? 'person' : 'people') + ' in the lobby'"></p>
          <div class="flex -space-x-2">
            <template x-for="(m, i) in members.slice(0, 6)" :key="m.id">
              <span class="grid size-10 place-items-center rounded-full border-2 border-black bg-surface-3 text-sm font-bold uppercase text-fg" x-text="m.name.charAt(0)"></span>
            </template>
          </div>

          @if ($isHost)
            <button type="button" class="btn btn-primary btn-lg" @click="toggleLobby()"><x-ui.icon name="play" class="size-4" /> Start party</button>
            <p class="text-xs text-white/40">You can return to the lobby anytime from the controls bar</p>
          @else
            <div class="inline-flex items-center gap-2.5 text-sm text-white/70"><span class="size-2 animate-pulse rounded-full bg-brand"></span> Waiting for host to start…</div>
            <div class="flex flex-wrap justify-center gap-2">
              @if ($trailerSrc)<button type="button" class="wp-pill" @click="watchTrailer()"><x-ui.icon name="play" class="size-3" /> Watch trailer</button>@endif
              @if ($watchAloneUrl)<button type="button" class="wp-pill" @click="$dispatch('wp:continue-alone')">Watch alone</button>@endif
            </div>
          @endif
        </div>
      </div>

      @if (!$isHost && $trailerSrc)
        <div x-show="watchingTrailer" x-cloak class="absolute left-1/2 top-3 z-[25] -translate-x-1/2">
          <button type="button" @click="stopTrailer()" class="wp-pill bg-black/60 backdrop-blur"><x-ui.icon name="arrow-left" class="size-3" /> Back to lobby</button>
        </div>
      @endif

      <video id="party-video" controls preload="metadata" class="size-full bg-black object-contain" @if ($isHost) @play="onHostPlay()" @pause="onHostPause()" @seeked="onHostSeeked()" @endif></video>
    </div>

    {{-- ── Controls bar ───────────────────────────────────────── --}}
    <div class="relative z-10 border-t border-white/10 bg-neutral-950 text-white" x-show="!kicked" x-cloak>
      @if (!$isHost)
        <div class="relative h-1 w-full bg-white/10">
          <div class="h-full bg-white/50" :style="'width:' + myProgressPct + '%'"></div>
          <div class="absolute inset-y-[-2px] w-0.5 bg-brand" :style="'left:' + hostProgressPct + '%'"></div>
        </div>
      @endif

      <div class="flex flex-wrap items-center gap-x-3 gap-y-2 px-4 py-3">
        {{-- Info --}}
        <div class="mr-auto min-w-0">
          <p class="truncate text-sm font-semibold">{{ $party->title() }}</p>
          @if ($isSeries)
            <p class="truncate text-xs text-white/50" x-text="currentEpisodeLabel || ''">&nbsp;</p>
          @endif
          <div class="flex items-center gap-1.5 text-[11px] font-semibold">
            @if ($isHost)
              <span class="size-1.5 rounded-full bg-emerald-400"></span><span class="text-emerald-400">Host · Live</span>
            @else
              <span class="size-1.5 rounded-full" x-cloak :class="isLive ? 'bg-emerald-400' : 'bg-amber-400 animate-pulse'"></span>
              <span x-cloak :class="isLive ? 'text-emerald-400' : 'text-amber-400'" x-text="isLive ? 'Watching' : 'Behind'"></span>
            @endif
          </div>
        </div>

        @if ($isGuest)
          <a href="{{ route('login') }}" class="wp-pill" title="You're watching as a guest. Log in to save your history.">Guest · {{ $guestName }}</a>
        @endif

        @if (!$isHost)
          <button type="button" x-show="!isLive" @click="goLive()" class="wp-pill !border-brand !text-brand" x-cloak title="Jump to host's position"><x-ui.icon name="play" class="size-3" /> Go live</button>
        @endif

        <div class="flex items-center gap-1.5">
          <button type="button" @click="skip(-10)" class="wp-btn" title="Rewind 10 s (←)"><x-ui.icon name="rewind" class="size-[18px]" /></button>
          <button type="button" @click="skip(10)" class="wp-btn" title="Forward 10 s (→)"><x-ui.icon name="forward" class="size-[18px]" /></button>
        </div>

        @if ($isSeries)
          <div class="flex items-center gap-1.5">
            <button type="button" @click="prevEpisodeAction()" :disabled="{{ ($isHost || $isGuest) ? '!hasPrevEpisode' : 'true' }}" class="wp-btn" title="Previous episode"><x-ui.icon name="chevron-left" class="size-[18px]" /></button>
            <button type="button" @click="nextEpisodeAction()" :disabled="{{ ($isHost || $isGuest) ? '!hasNextEpisode' : 'true' }}" class="wp-btn" title="Next episode"><x-ui.icon name="chevron-right" class="size-[18px]" /></button>
          </div>
        @endif

        @if ($isHost)
          <button type="button" x-show="!inLobby" @click="toggleLobby()" class="wp-pill" x-cloak title="Send everyone to lobby">Lobby</button>
        @endif

        <span class="hidden h-6 w-px bg-white/10 sm:block"></span>

        <div class="flex items-center gap-1.5">
          <button type="button" id="pip-btn" class="wp-btn" title="Picture in picture" style="display:none">
            <svg id="pip-icon-enter" width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M19 11h-8v6h8v-6zm4 8V4.98C23 3.88 22.1 3 21 3H3C1.9 3 1 3.88 1 4.98V19c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2zm-2 .02H3V5h18v14.02z"/></svg>
            <svg id="pip-icon-exit" width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="display:none"><path d="M21 3H3C1.9 3 1 3.88 1 4.98V19c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V4.98C23 3.88 22.1 3 21 3zm-2 16.02H5V5h14v14.02zM11 11H7v4h4v-4z"/></svg>
          </button>

          {{-- Panels --}}
          <div x-data="{ panelsOpen: false }" class="relative">
            <button type="button" @click="panelsOpen = !panelsOpen" :class="['chat','members','episodes'].includes(activeSidebar) && 'wp-active'" class="wp-btn" title="Panels">
              <span x-show="unreadChat > 0" class="absolute -right-1 -top-1 grid min-w-4 place-items-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-4" x-text="unreadChat > 9 ? '9+' : unreadChat"></span>
              <x-ui.icon name="message" class="size-[18px]" />
            </button>
            <div x-show="panelsOpen" x-cloak @click.away="panelsOpen = false" class="menu-panel absolute bottom-full right-0 mb-2 w-44">
              <button type="button" @click="switchToChat(); panelsOpen = false" :class="activeSidebar === 'chat' && '!text-brand'" class="menu-item"><x-ui.icon name="message" class="size-4" /> Chat <span x-show="unreadChat > 0" class="ml-auto badge bg-red-500 text-white" x-text="unreadChat > 9 ? '9+' : unreadChat"></span></button>
              <button type="button" @click="activeSidebar = activeSidebar === 'members' ? null : 'members'; panelsOpen = false" :class="activeSidebar === 'members' && '!text-brand'" class="menu-item"><x-ui.icon name="users" class="size-4" /> Members</button>
              @if ($isSeries)
                <button type="button" @click="activeSidebar = activeSidebar === 'episodes' ? null : 'episodes'; panelsOpen = false" :class="activeSidebar === 'episodes' && '!text-brand'" class="menu-item"><x-ui.icon name="list" class="size-4" /> Episodes</button>
              @endif
            </div>
          </div>

          {{-- Share --}}
          @if ($isHost)
            <div x-data="{ shareOpen: false, copied: false }" class="relative">
              <button type="button" @click="shareOpen = !shareOpen" :class="shareOpen && 'wp-active'" class="wp-btn" title="Share party"><x-ui.icon name="share" class="size-4" /></button>
              <div x-show="shareOpen" x-cloak @click.away="shareOpen = false" class="menu-panel absolute bottom-full right-0 mb-2 w-80 p-3">
                <p class="label">Invite link</p>
                <div class="flex gap-1.5">
                  <input type="text" readonly class="input !py-2 text-xs" value="{{ route('watch-party.show', $party->token) }}" onclick="this.select()">
                  <button type="button" class="btn btn-primary btn-sm"
                    @click="navigator.clipboard.writeText('{{ route('watch-party.show', $party->token) }}'); copied = true; setTimeout(() => { copied = false; }, 2000)"
                    x-text="copied ? '✓ Copied' : 'Copy'"></button>
                </div>
                <a href="https://wa.me/?text={{ urlencode('Join my Watch Party on ' . config('app.name') . '! 🎬 ' . $party->title() . ': ' . route('watch-party.show', $party->token)) }}"
                  target="_blank" rel="noopener" class="btn btn-block mt-2 bg-[#25D366] text-white hover:bg-[#1ebe5d]">Share on WhatsApp</a>
              </div>
            </div>
          @else
            <button type="button" x-data="{ copied: false }"
              @click="navigator.clipboard.writeText('{{ route('watch-party.show', $party->token) }}'); copied = true; setTimeout(() => copied = false, 2000)"
              :class="copied && 'wp-active'" class="wp-btn" title="Copy invite link">
              <x-ui.icon name="copy" class="size-4" x-show="!copied" /><x-ui.icon name="check" class="size-4" x-show="copied" x-cloak />
            </button>
          @endif
        </div>

        <span class="hidden h-6 w-px bg-white/10 sm:block"></span>

        {{-- Leave / end --}}
        @if ($isHost)
          <form method="POST" action="{{ route('watch-party.end', $party->token) }}" x-data @submit.prevent="if(confirm('End the party for everyone?')) $el.submit()">
            @csrf @method('DELETE')
            <button type="submit" class="wp-btn !bg-red-500/20 !text-red-400 hover:!bg-red-500 hover:!text-white" title="End party"><x-ui.icon name="x" class="size-4" /></button>
          </form>
        @elseif ($isGuest)
          <a href="{{ route('home') }}" class="wp-btn !bg-red-500/20 !text-red-400 hover:!bg-red-500 hover:!text-white" title="Leave party" onclick="return confirm('Leave this watch party?')"><x-ui.icon name="log-out" class="size-4" /></a>
        @else
          <form method="POST" action="{{ route('watch-party.leave', $party->token) }}" x-data @submit.prevent="if(confirm('Leave this watch party?')) $el.submit()">
            @csrf
            <button type="submit" class="wp-btn !bg-red-500/20 !text-red-400 hover:!bg-red-500 hover:!text-white" title="Leave party"><x-ui.icon name="log-out" class="size-4" /></button>
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