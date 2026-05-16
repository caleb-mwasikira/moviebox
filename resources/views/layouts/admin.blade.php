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
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.movies.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.movies.index') }}">Movies</a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.series.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.series.index') }}">Series</a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.coming-soon.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.coming-soon.index') }}">Coming Soon</a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.users.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.users.index') }}">Users</a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.packages.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.packages.index') }}">Subscription Packages</a>
            </li>
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
