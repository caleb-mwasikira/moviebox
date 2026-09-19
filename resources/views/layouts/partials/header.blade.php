@php
  $user = auth()->user();
  $links = array_merge(
    [
      ['Movies', route('movies'), request()->routeIs('movies*')],
      ['Series', route('series'), request()->routeIs('series*')],
    ],
    [['Reels', route('reels'), request()->routeIs('reels')]],
    [['Pricing', route('pricing'), request()->routeIs('pricing')]],
  );
  $hasAdmin = $user && ($user->isAdmin() || $user->roles->isNotEmpty());
@endphp

<header x-data="{ drawer: false }" x-effect="document.body.classList.toggle('overflow-hidden', drawer)"
  class="sticky top-0 z-40 border-b border-line bg-bg/80 backdrop-blur-xl">
  <div class="container-app flex h-16 items-center gap-3 sm:gap-6">
    <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ config('app.name') }} home"><x-ui.logo /></a>

    {{-- Desktop nav --}}
    <nav class="hidden items-center gap-1 lg:flex">
      @foreach ($links as [$label, $url, $active])
        <a href="{{ $url }}" class="nav-link" @if($active) aria-current="page" @endif>{{ $label }}</a>
      @endforeach

      <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @keydown.escape="open = false">
        <button type="button" class="nav-link inline-flex items-center gap-1" @click="open = !open" :aria-expanded="open">
          Discover <x-ui.icon name="chevron-down" class="size-4 transition" ::class="open && 'rotate-180'" />
        </button>
        <div x-show="open" x-cloak x-transition.opacity.duration.150ms class="absolute left-0 top-full z-50 pt-2">
          <div class="menu-panel w-[34rem] p-5">
            <div class="grid grid-cols-2 gap-6">
              <div>
                <div class="mb-2 flex items-center justify-between">
                  <a href="{{ route('coming-soon') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-widest text-fg hover:text-brand">Coming soon <x-ui.icon name="arrow-right" class="size-3" /></a>
                </div>
                @if (isset($megaComingSoon) && $megaComingSoon->isNotEmpty())
                  <ul class="space-y-1">
                    @foreach ($megaComingSoon as $cs)
                      <li>
                        <a href="{{ route('coming-soon.show', $cs) }}" class="flex items-center gap-3 rounded-lg p-1.5 hover:bg-surface-2">
                          <span class="block h-11 w-8 shrink-0 overflow-hidden rounded bg-surface-3">
                            @if ($cs->thumbnailUrl())<img src="{{ $cs->thumbnailUrl() }}" alt="" class="size-full object-cover">@endif
                          </span>
                          <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-fg">{{ $cs->title }}</span>
                            @if ($cs->genre)<span class="text-xs text-muted">{{ $cs->genre }}</span>@endif
                          </span>
                        </a>
                      </li>
                    @endforeach
                  </ul>
                @else
                  <p class="text-sm text-muted">Nothing announced yet.</p>
                @endif
              </div>
              <div>
                <p class="mb-2 text-[11px] font-semibold uppercase tracking-widest text-muted">Collections</p>
                @if (isset($megaCollections) && $megaCollections->isNotEmpty())
                  <ul class="space-y-1">
                    @foreach ($megaCollections as $col)
                      <li>
                        <a href="{{ route('collections.show', $col) }}" class="flex items-center gap-3 rounded-lg p-1.5 hover:bg-surface-2">
                          <span class="grid size-9 shrink-0 place-items-center overflow-hidden rounded bg-surface-3 text-muted">
                            @if ($col->thumbnail)<img src="{{ $col->thumbnailUrl() }}" alt="" class="size-full object-cover">@else<x-ui.icon name="folder" class="size-4" />@endif
                          </span>
                          <span class="truncate text-sm font-semibold text-fg">{{ $col->name }}</span>
                        </a>
                      </li>
                    @endforeach
                  </ul>
                @else
                  <p class="text-sm text-muted">No collections yet.</p>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      <a href="{{ route('about') }}" class="nav-link" @if(request()->routeIs('about')) aria-current="page" @endif>About</a>
      <a href="{{ route('contact') }}" class="nav-link" @if(request()->routeIs('contact')) aria-current="page" @endif>Contact</a>
    </nav>

    <div class="ml-auto flex items-center gap-0.5 sm:gap-1.5">
      <button type="button" x-data @click="$dispatch('open-search')"
        class="hidden h-9 items-center gap-2 rounded-lg border border-line bg-surface px-3 text-sm text-muted transition hover:border-line-strong hover:text-fg md:flex">
        <x-ui.icon name="search" class="size-4" /> <span>Search</span>
        <kbd class="ml-4 rounded border border-line bg-surface-2 px-1.5 text-[10px] font-medium">/</kbd>
      </button>
      <button type="button" x-data @click="$dispatch('open-search')" class="btn btn-ghost btn-icon md:hidden" aria-label="Search">
        <x-ui.icon name="search" />
      </button>

      <button type="button" onclick="toggleTheme()" class="btn btn-ghost btn-icon" aria-label="Toggle theme">
        <x-ui.icon name="sun" class="hidden dark:block" />
        <x-ui.icon name="moon" class="dark:hidden" />
      </button>

      @auth
        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
          <button type="button" @click="open = !open" class="flex items-center rounded-full ring-2 ring-transparent transition hover:ring-brand/40" aria-label="Account menu" :aria-expanded="open">
            @if ($user->avatarUrl())
              <img src="{{ $user->avatarUrl() }}" alt="" class="size-9 rounded-full object-cover">
            @else
              <span class="grid size-9 place-items-center rounded-full bg-brand-soft text-xs font-bold text-brand">{{ $user->initials() }}</span>
            @endif
          </button>
          <div x-show="open" x-cloak x-transition.origin.top.right class="absolute right-0 top-full z-50 mt-2 w-64">
            <div class="menu-panel">
              <div class="px-3 py-2.5">
                <p class="truncate text-sm font-semibold text-fg">{{ $user->name }}</p>
                <p class="truncate text-xs text-muted">{{ $user->email }}</p>
              </div>
              <div class="my-1 h-px bg-line"></div>
              <a href="{{ route('account.dashboard') }}" class="menu-item"><x-ui.icon name="dashboard" class="size-4" /> Dashboard</a>
              <a href="{{ route('account.profile') }}" class="menu-item"><x-ui.icon name="user" class="size-4" /> My profile</a>
              <a href="{{ route('account.watching') }}" class="menu-item"><x-ui.icon name="play" class="size-4" /> Continue watching</a>
              <a href="{{ route('account.watchlist') }}" class="menu-item"><x-ui.icon name="bookmark" class="size-4" /> Watchlist</a>
              <a href="{{ route('account.billing') }}" class="menu-item"><x-ui.icon name="credit-card" class="size-4" /> Billing</a>
              @if ($hasAdmin)
                <a href="{{ route('admin.dashboard') }}" class="menu-item text-brand"><x-ui.icon name="shield" class="size-4" /> Admin panel</a>
              @endif
              <div class="my-1 h-px bg-line"></div>
              <a href="{{ route('profiles.select') }}" class="menu-item"><x-ui.icon name="users" class="size-4" /> Switch profile</a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-item hover:!bg-red-500/10 hover:!text-red-500"><x-ui.icon name="log-out" class="size-4" /> Sign out</button>
              </form>
            </div>
          </div>
        </div>
      @else
        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm hidden sm:inline-flex">Sign in</a>
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">Get started</a>
      @endauth

      <button type="button" class="btn btn-ghost btn-icon lg:hidden" @click="drawer = true" aria-label="Open menu"><x-ui.icon name="menu" /></button>
    </div>
  </div>

  {{-- Mobile drawer (teleported: the header's backdrop-filter would otherwise trap fixed positioning) --}}
  <template x-teleport="body">
  <div x-show="drawer" x-cloak class="fixed inset-0 z-50 lg:hidden" @keydown.escape.window="drawer = false">
    <div x-show="drawer" x-transition.opacity class="absolute inset-0 bg-black/60" @click="drawer = false"></div>
    <aside x-show="drawer" x-transition:enter="transition duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
      x-transition:leave="transition duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
      class="absolute inset-y-0 right-0 flex w-[min(20rem,88vw)] flex-col overflow-y-auto border-l border-line bg-surface p-5">
      <div class="mb-6 flex items-center justify-between">
        <x-ui.logo />
        <button type="button" class="btn btn-ghost btn-icon" @click="drawer = false" aria-label="Close menu"><x-ui.icon name="x" /></button>
      </div>
      <nav class="flex flex-col gap-1">
        <a href="{{ route('home') }}" class="side-link {{ request()->routeIs('home') ? 'is-active' : '' }}"><x-ui.icon name="home" /> Home</a>
        @foreach ($links as [$label, $url, $active])
          <a href="{{ $url }}" class="side-link {{ $active ? 'is-active' : '' }}">{{ $label }}</a>
        @endforeach
        <p class="mt-2 px-3 text-[11px] font-semibold uppercase tracking-widest text-muted">Discover</p>
        <a href="{{ route('coming-soon') }}" class="side-link {{ request()->routeIs('coming-soon*') ? 'is-active' : '' }}"><x-ui.icon name="calendar" /> Coming soon</a>
        @foreach (($megaCollections ?? collect()) as $col)
          <a href="{{ route('collections.show', $col) }}" class="side-link"><x-ui.icon name="folder" /> {{ $col->name }}</a>
        @endforeach
        <a href="{{ route('about') }}" class="side-link">About</a>
        <a href="{{ route('contact') }}" class="side-link">Contact</a>
      </nav>
      <div class="mt-auto border-t border-line pt-6">
        @auth
          <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-widest text-muted">My account</p>
          <nav class="flex flex-col gap-1">
            <a href="{{ route('account.dashboard') }}" class="side-link"><x-ui.icon name="dashboard" /> Dashboard</a>
            <a href="{{ route('account.profile') }}" class="side-link"><x-ui.icon name="user" /> My profile</a>
            <a href="{{ route('account.watching') }}" class="side-link"><x-ui.icon name="play" /> Continue watching</a>
            <a href="{{ route('account.watchlist') }}" class="side-link"><x-ui.icon name="bookmark" /> Watchlist</a>
            <a href="{{ route('account.billing') }}" class="side-link"><x-ui.icon name="credit-card" /> Billing</a>
            <a href="{{ route('account.watch-party.index') }}" class="side-link"><x-ui.icon name="popcorn" /> Watch party</a>
            <a href="{{ route('profiles.select') }}" class="side-link"><x-ui.icon name="users" /> Switch profile</a>
            @if ($hasAdmin)<a href="{{ route('admin.dashboard') }}" class="side-link text-brand"><x-ui.icon name="shield" /> Admin panel</a>@endif
          </nav>
          <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="btn btn-secondary btn-block"><x-ui.icon name="log-out" class="size-4" /> Sign out</button>
          </form>
        @else
          <div class="grid gap-2">
            <a href="{{ route('login') }}" class="btn btn-secondary">Sign in</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Get started</a>
          </div>
        @endauth
      </div>
    </aside>
  </div>
  </template>
</header>
