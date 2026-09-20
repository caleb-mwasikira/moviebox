<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
@include('layouts.partials.head')

@php
  $u = auth()->user();
  $unreadContacts = \Gate::allows('view contacts') ? \App\Models\Contact::unread()->count() : 0;

  // [label, route, icon, active pattern, gate]
  $groups = [
    'Overview' => [
      ['Dashboard', 'admin.dashboard', 'dashboard', 'admin.dashboard', null],
      ['Analytics', 'admin.analytics', 'chart', 'admin.analytics', 'view analytics'],
    ],
    'Content' => [
      ['Movies', 'admin.movies.index', 'film', 'admin.movies.*', 'view movies'],
      ['Series', 'admin.series.index', 'tv', 'admin.series.*', 'view series'],
      ['Coming soon', 'admin.coming-soon.index', 'calendar', 'admin.coming-soon.*', 'view coming soon'],
      ['Collections', 'admin.collections.index', 'folder', 'admin.collections.*', 'view collections'],
    ],
    'People' => [
      ['Users', 'admin.users.index', 'users', 'admin.users.*', 'view users'],
      ['Roles', 'admin.roles.index', 'shield', 'admin.roles.*', 'assign permissions'],
    ],
    'Finance' => [
      ['Packages', 'admin.packages.index', 'package', 'admin.packages.*', 'view packages'],
      ['Payments', 'admin.payments.index', 'wallet', 'admin.payments.*', 'view payments'],
      ['Gifts', 'admin.gifts.index', 'gift', 'admin.gifts.*', '__admin'],
    ],
    'System' => [
      ['Messages', 'admin.contacts.index', 'inbox', 'admin.contacts.*', 'view contacts'],
      ['Test emails', 'admin.test-email.show', 'mail', 'admin.test-email.*', 'send test emails'],
      ['Activity log', 'admin.activity.index', 'activity', 'admin.activity.*', '__admin'],
      ['Trash', 'admin.trash.index', 'trash', 'admin.trash.*', '__admin'],
    ],
  ];

  $statusMessages = [
    'movie-created'     => 'Movie uploaded successfully.',
    'movie-processing'  => 'Movie saved — video is being processed in the background.',
    'movie-updated'     => 'Movie updated successfully.',
    'movie-deleted'     => 'Movie deleted.',
    'series-created'    => 'Series created. Add seasons below.',
    'series-updated'    => 'Series updated successfully.',
    'series-deleted'    => 'Series deleted.',
    'season-added'      => 'Season added successfully.',
    'season-deleted'    => 'Season deleted.',
    'episode-processing'    => 'Episode saved — video is being processed in the background.',
    'movie-reprocessing'    => 'HLS encoding triggered for this movie.',
    'series-reprocessing'   => 'HLS encoding triggered for unprocessed episodes.',
    'episode-added'     => 'Episode uploaded successfully.',
    'episode-deleted'   => 'Episode deleted.',
    'poster-deleted'    => 'Poster removed.',
    'streaming-url-saved'   => 'Streaming URL saved successfully.',
    'coming-soon-created'   => 'Film added to Coming Soon.',
    'coming-soon-updated'   => 'Film details updated.',
    'coming-soon-deleted'   => 'Film removed from Coming Soon.',
    'trailer-processing'    => 'Trailer uploaded and is being processed for streaming.',
    'collection-created'    => 'Collection created.',
    'collection-updated'    => 'Collection updated.',
    'collection-deleted'    => 'Collection deleted.',
    'item-added'            => 'Item added to collection.',
    'item-removed'          => 'Item removed from collection.',
  ];
  $flashStatus = $statusMessages[session('status')] ?? null;
@endphp

<body class="min-h-screen" x-data="{ nav: false }" @keydown.escape.window="nav = false">
  <div class="flex min-h-screen">

    {{-- Sidebar --}}
    <div x-show="nav" x-cloak x-transition.opacity class="fixed inset-0 z-40 bg-black/60 lg:hidden" @click="nav = false"></div>
    <aside class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-line bg-surface transition-transform duration-200 lg:translate-x-0" :class="nav && '!translate-x-0'">
      <div class="flex h-16 shrink-0 items-center justify-between border-b border-line px-5">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5" title="Go to website">
          <x-ui.logo />
        </a>
        <button type="button" class="btn btn-ghost btn-icon lg:hidden" @click="nav = false" aria-label="Close"><x-ui.icon name="x" /></button>
      </div>

      <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
        @foreach ($groups as $group => $items)
          @php
            $visible = collect($items)->filter(fn ($i) => $i[4] === null || ($i[4] === '__admin' ? $u->isAdmin() : \Gate::allows($i[4])));
          @endphp
          @if ($visible->isNotEmpty())
            <div>
              <p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-muted">{{ $group }}</p>
              <div class="space-y-0.5">
                @foreach ($visible as [$label, $route, $icon, $pattern])
                  <a href="{{ route($route) }}" class="side-link {{ request()->routeIs($pattern) ? 'is-active' : '' }}">
                    <x-ui.icon :name="$icon" /> <span class="flex-1">{{ $label }}</span>
                    @if ($route === 'admin.contacts.index' && $unreadContacts > 0)
                      <span class="grid min-w-5 place-items-center rounded-full bg-brand px-1.5 text-[10px] font-bold leading-5 text-white">{{ $unreadContacts }}</span>
                    @endif
                  </a>
                @endforeach
              </div>
            </div>
          @endif
        @endforeach
      </nav>

      <div class="shrink-0 border-t border-line p-3">
        <a href="{{ route('home') }}" class="side-link"><x-ui.icon name="external" /> View site</a>
        <a href="{{ route('account.dashboard') }}" class="side-link"><x-ui.icon name="user" /> My account</a>
      </div>
    </aside>

    {{-- Main --}}
    <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
      <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-line bg-bg/80 px-4 backdrop-blur-xl sm:px-6">
        <button type="button" class="btn btn-ghost btn-icon lg:hidden" @click="nav = true" aria-label="Open menu"><x-ui.icon name="menu" /></button>
        <div class="min-w-0">
          <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-brand">Admin</p>
          <h1 class="truncate text-base font-bold leading-tight text-fg">@yield('admin-title', 'Dashboard')</h1>
        </div>

        <div class="ml-auto flex items-center gap-1.5">
          <button type="button" onclick="toggleTheme()" class="btn btn-ghost btn-icon" aria-label="Toggle theme">
            <x-ui.icon name="sun" class="hidden dark:block" /><x-ui.icon name="moon" class="dark:hidden" />
          </button>
          <div class="flex items-center gap-2.5 pl-2">
            @if ($u->avatarUrl())
              <img src="{{ $u->avatarUrl() }}" alt="" class="size-8 rounded-full object-cover">
            @else
              <span class="grid size-8 place-items-center rounded-full bg-brand-soft text-xs font-bold text-brand">{{ $u->initials() }}</span>
            @endif
            <span class="hidden text-sm font-semibold text-fg sm:block">{{ $u->name }}</span>
          </div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-ghost btn-icon" title="Sign out"><x-ui.icon name="log-out" /></button>
          </form>
        </div>
      </header>

      <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="mx-auto max-w-7xl space-y-6">
          @if (session('error'))
            <div class="alert alert-error"><x-ui.icon name="alert" class="mt-0.5 size-5 shrink-0" />{{ session('error') }}</div>
          @endif
          @if ($flashStatus)
            <div class="alert alert-success"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />{{ $flashStatus }}</div>
          @endif

          @yield('admin-content')
        </div>
      </main>
    </div>
  </div>

  {{-- Realtime toasts --}}
  <div id="vt-container" class="pointer-events-none fixed bottom-6 right-6 z-[9999] flex w-80 max-w-[calc(100vw-3rem)] flex-col gap-2.5"></div>

  <script>
    function toggleTheme() {
      var dark = !document.documentElement.classList.contains('dark');
      document.documentElement.classList.toggle('dark', dark);
      try { localStorage.setItem('str1-color-theme', dark ? 'dark' : 'light'); } catch (e) {}
    }
  </script>

  <script type="module">
    function showToast(text, href, tone) {
      const container = document.getElementById('vt-container');
      if (!container) return;

      const toast = document.createElement('div');
      toast.className = 'pointer-events-auto flex items-start justify-between gap-3 rounded-xl border px-4 py-3 text-sm font-semibold shadow-lg ' +
        (tone === 'brand' ? 'border-brand/30 bg-brand text-white' : 'border-amber-400/40 bg-amber-400 text-neutral-900');

      const link = document.createElement('a');
      link.href = href;
      link.className = 'flex-1';
      link.textContent = text;

      const btn = document.createElement('button');
      btn.textContent = '✕';
      btn.className = 'shrink-0 leading-none opacity-70 hover:opacity-100';
      btn.onclick = () => toast.remove();

      toast.appendChild(link);
      toast.appendChild(btn);
      container.appendChild(toast);
      setTimeout(() => toast.remove(), 8000);
    }

    window.Echo.private('admin-notifications.{{ auth()->id() }}')
      .listen('VideoReady', (e) => {
        showToast(`✓ "${e.label}" video ready`, e.editUrl, 'amber');
      })
      .listen('VideoStreamingReady', (e) => {
        showToast(`▶ "${e.label}" HLS stream ready`, e.editUrl, 'brand');
        document.dispatchEvent(new CustomEvent('video-ready', { detail: e }));
      })
      .listen('VideoEncodingProgress', (e) => {
        document.dispatchEvent(new CustomEvent('encoding-progress', { detail: e }));
      });
  </script>

  <x-admin.upload-tray />
  @stack('scripts')
</body>
</html>
