@extends('layouts.app')

@section('title')Admin — @yield('admin-title', 'Dashboard')@endsection

@push('head')
  @vite('resources/js/app.js')
@endpush

@push('scripts')
<script type="module">
  function showToast(text, href, bg, color) {
    const container = document.getElementById('vt-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.style.cssText = `display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:14px 16px;background:${bg};color:${color};font-size:0.8125rem;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.25);min-width:280px;max-width:340px;`;

    const link = document.createElement('a');
    link.href = href;
    link.style.cssText = 'color:inherit;text-decoration:none;flex:1;';
    link.textContent = text;

    const btn = document.createElement('button');
    btn.textContent = '✕';
    btn.style.cssText = 'background:none;border:none;cursor:pointer;color:inherit;font-size:1rem;line-height:1;flex-shrink:0;';
    btn.onclick = () => toast.remove();

    toast.appendChild(link);
    toast.appendChild(btn);
    container.appendChild(toast);

    setTimeout(() => toast.remove(), 8000);
  }

  window.Echo.private('admin-notifications.{{ auth()->id() }}')
    .listen('VideoReady', (e) => {
      showToast(`✓ "${e.label}" video ready`, e.editUrl, '#eab308', '#111827');
    })
    .listen('VideoStreamingReady', (e) => {
      showToast(`▶ "${e.label}" HLS stream ready`, e.editUrl, 'rgb(101,76,255)', '#fff');
      document.dispatchEvent(new CustomEvent('video-ready', { detail: e }));
    })
    .listen('VideoEncodingProgress', (e) => {
      document.dispatchEvent(new CustomEvent('encoding-progress', { detail: e }));
    });
</script>
@endpush

@section('content')
<div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
  <div class="container">
    <div class="flex flex-col lg:flex-row gap-y-8 lg:gap-x-12 xl:gap-x-16">

      <aside class="hidden lg:block lg:w-52 xl:w-60 shrink-0">
        <nav class="lg:sticky lg:top-8">
          <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
            <span class="text-xs font-bold uppercase text-accent tracking-widest">Admin Panel</span>
          </div>
          <ul class="flex flex-row flex-wrap gap-x-6 gap-y-2 lg:flex-col lg:gap-x-0 lg:gap-y-1 font-bold">
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.dashboard') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            @can('view analytics')
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.analytics') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.analytics') }}">Analytics</a>
            </li>
            @endcan
            @can('view movies')
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.movies.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.movies.index') }}">Movies</a>
            </li>
            @endcan
            @can('view series')
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.series.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.series.index') }}">Series</a>
            </li>
            @endcan
            @can('view coming soon')
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.coming-soon.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.coming-soon.index') }}">Coming Soon</a>
            </li>
            @endcan
            @can('view collections')
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.collections.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.collections.index') }}">Collections</a>
            </li>
            @endcan
            @can('view users')
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.users.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.users.index') }}">Users</a>
            </li>
            @endcan
            @can('assign permissions')
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.roles.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.roles.index') }}">Roles</a>
            </li>
            @endcan
            @can('view packages')
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.packages.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.packages.index') }}">Subscription Packages</a>
            </li>
            @endcan
            @can('view payments')
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.payments.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.payments.index') }}">Payments</a>
            </li>
            @endcan
            @can('send test emails')
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.test-email.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.test-email.show') }}">Test Emails</a>
            </li>
            @endcan
            @can('view contacts')
            <li>
              @php $unreadContacts = \App\Models\Contact::unread()->count(); @endphp
              <a class="flex items-center gap-x-2 py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.contacts.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.contacts.index') }}">
                Messages
                @if ($unreadContacts > 0)
                  <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold bg-accent text-gray-900 rounded-full">{{ $unreadContacts }}</span>
                @endif
              </a>
            </li>
            @endcan
            @if (auth()->user()->isAdmin())
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.activity.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.activity.index') }}">Activity Log</a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.trash.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.trash.index') }}">Trash</a>
            </li>
            @endif
            <li class="lg:mt-4 lg:pt-4 lg:border-t lg:border-gray-200 lg:dark:border-gray-600">
              <a class="block py-1.5 transition-all text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent"
                href="{{ route('account.dashboard') }}">My Account</a>
            </li>
          </ul>

          <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
            <span class="text-xs font-bold uppercase text-gray-400 tracking-widest">Site</span>
          </div>
          <ul class="mt-3 flex flex-row flex-wrap gap-x-6 gap-y-2 lg:flex-col lg:gap-x-0 lg:gap-y-1 font-bold">
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('home') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('home') }}">Home</a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('browse') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('browse') }}">Browse</a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('movies') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('movies') }}">Movies</a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('series') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('series') }}">Series</a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('coming-soon') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('coming-soon') }}">Coming Soon</a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('contact') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('contact') }}">Contact</a>
            </li>
          </ul>

          <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
            <label for="theme-toggle"
              class="flex items-center gap-x-2.5 cursor-pointer text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors font-bold text-sm">
              <svg class="w-4 h-4 shrink-0 dark:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
              </svg>
              <svg class="w-4 h-4 shrink-0 hidden dark:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
              </svg>
              <span class="dark:hidden">Light Mode</span>
              <span class="hidden dark:block">Dark Mode</span>
            </label>
          </div>
        </nav>
      </aside>

      <div class="flex-1 min-w-0">
        @if (session('error'))
          <div class="mb-6 border border-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-700 dark:text-red-400 font-medium">
            {{ session('error') }}
          </div>
        @endif

        @if (session('status'))
          @php
            $messages = [
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
            $msg = $messages[session('status')] ?? null;
          @endphp
          @if ($msg)
            <div class="mb-6 border border-accent bg-accent/10 px-4 py-3 text-sm text-accent font-medium">
              {{ $msg }}
            </div>
          @endif
        @endif

        @yield('admin-content')
      </div>

    </div>
  </div>
</div>

<div id="vt-container"
  style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none;">
</div>
<style>#vt-container > div { pointer-events: auto; }</style>
@endsection
