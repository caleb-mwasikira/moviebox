<!DOCTYPE html>
<html lang="zxx" class="h-full overflow-x-hidden dark">

<head>

  <!-- Basic Page Needs
  ================================================== -->
  <title>{{ env('APP_NAME') }} - @yield('title')</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Streamer and Youtuber HTML Template">
  <meta name="author" content="Dan Fisher">
  <meta name="keywords" content="youtube, streamer, stream, creator">

  <!-- Favicons
  ================================================== -->
  <link rel="shortcut icon" href="{{ asset('assets/img/str1/favicons/favicon.ico') }}">
  <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/img/str1/favicons/favicon-120.png') }}">
  <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/img/str1/favicons/favicon-152.png') }}">

  <!-- Mobile Specific Metas
  ================================================== -->
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" media="(prefers-color-scheme: dark)"  content="#111827">
  <meta name="theme-color" media="(prefers-color-scheme: light)" content="#ffffff">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @vite('resources/js/app.js')
  @stack('head')

  <!-- Google Web Fonts
  ================================================== -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- CSS
  ================================================== -->
  <!-- Vendor CSS-->
  <link href="{{ asset('assets/vendors/common/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendors/common/swiper/css/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Template CSS-->
  <link href="{{ asset('assets/css/str1/style.css') }}" rel="stylesheet">

  <style>
    .auth-banner-fade-r {
      background: linear-gradient(to right, transparent, #ffffff);
    }
    .dark .auth-banner-fade-r {
      background: linear-gradient(to right, transparent, rgb(17, 24, 39));
    }
    [x-cloak] { display: none !important; }
  </style>

</head>

<body
  class="relative z-10 antialiased font-base text-gray-500 text-sm font-medium lg:text-base h-full bg-white dark:bg-gray-900 overflow-x-hidden dark:text-gray">

  <div id="site-wrapper" class="flex flex-col h-full js-site-wrapper">

    <div class="header-wrapper text-white">
      <header id="site-header" class="text-white">
        <div class="max-w-[1360px] mx-auto px-5">
          <nav class="flex min-h-[64px] items-center gap-x-6 lg:gap-x-11 py-1 lg:min-h-[90px] lg:py-4">
            <!-- Logo -->
            <div class="shrink-0">
              <a href="{{ route('home') }}">
                <svg width="82" height="20" viewBox="0 0 82 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path class="fill-gray-900 dark:fill-white"
                    d="M19.908 20.001H19.231C17.745 20.001 16.597 19.603 15.789 18.806C14.98 18.01 14.576 16.863 14.576 15.364V10.13C14.576 8.632 14.98 7.484 15.789 6.688C16.597 5.891 17.745 5.493 19.231 5.493H25.75C26.163 5.493 26.37 5.692 26.37 6.091V19.405C26.37 19.803 26.163 20.003 25.75 20.003H23.38C22.985 20.003 22.787 19.804 22.787 19.405V18.466H22.674C22.467 18.979 22.1 19.362 21.573 19.619C21.046 19.873 20.491 20.001 19.908 20.001ZM22.786 14.71V8.934C22.786 8.726 22.673 8.621 22.447 8.621H19.795C19.193 8.621 18.77 8.749 18.525 9.005C18.28 9.261 18.157 9.693 18.157 10.299V15.193C18.157 15.799 18.28 16.23 18.525 16.486C18.769 16.743 19.192 16.871 19.795 16.871H20.839C22.137 16.871 22.786 16.15 22.786 14.71Z">
                  </path>
                  <path class="fill-gray-900 dark:fill-white"
                    d="M31.477 20.001H29.079C28.684 20.001 28.486 19.802 28.486 19.403V0.598999C28.486 0.200999 28.684 0.000999451 29.079 0.000999451H31.477C31.872 0.000999451 32.069 0.199999 32.069 0.598999V19.404C32.069 19.802 31.872 20.001 31.477 20.001Z">
                  </path>
                  <path class="fill-gray-900 dark:fill-white"
                    d="M37.176 20.001H34.778C34.383 20.001 34.186 19.802 34.186 19.403V0.598999C34.186 0.200999 34.383 0.000999451 34.778 0.000999451H37.176C37.571 0.000999451 37.769 0.199999 37.769 0.598999V19.404C37.769 19.802 37.571 20.001 37.176 20.001ZM42.425 5.492H45.162C45.387 5.492 45.538 5.559 45.613 5.691C45.688 5.824 45.66 5.995 45.529 6.203L41.776 12.433V12.547L45.698 19.289C45.811 19.478 45.82 19.643 45.726 19.787C45.631 19.929 45.462 20 45.218 20H42.791C42.471 20 42.241 19.963 42.101 19.886C41.959 19.81 41.814 19.65 41.663 19.403L38.079 13.059C37.91 12.755 37.91 12.443 38.079 12.12L41.465 6.061C41.672 5.682 41.992 5.492 42.425 5.492Z">
                  </path>
                  <path class="fill-gray-900 dark:fill-white"
                    d="M46.516 19.403V6.089C46.516 5.691 46.704 5.491 47.08 5.491H49.506C49.902 5.491 50.099 5.69 50.099 6.089V19.403C50.099 19.65 50.057 19.811 49.972 19.886C49.888 19.962 49.743 20 49.534 20H47.108C46.713 20.001 46.516 19.802 46.516 19.403Z">
                  </path>
                  <path class="fill-gray-900 dark:fill-white"
                    d="M51.594 5.492H54.078C54.454 5.492 54.679 5.691 54.755 6.09L57.182 16.787H57.379L59.749 6.09C59.824 5.692 60.06 5.492 60.454 5.492H62.937C63.332 5.492 63.473 5.691 63.361 6.09L59.805 19.376C59.73 19.792 59.477 20 59.044 20H55.46C55.065 20 54.812 19.792 54.698 19.375L51.171 6.089C51.059 5.691 51.2 5.492 51.594 5.492Z">
                  </path>
                  <path class="fill-gray-900 dark:fill-white"
                    d="M64.433 19.403V6.089C64.433 5.691 64.621 5.491 64.997 5.491H67.424C67.819 5.491 68.016 5.69 68.016 6.089V19.403C68.016 19.65 67.974 19.811 67.889 19.886C67.805 19.962 67.659 20 67.451 20H65.025C64.63 20.001 64.433 19.802 64.433 19.403Z">
                  </path>
                  <path class="fill-gray-900 dark:fill-white"
                    d="M75.408 20.001H74.252C72.803 20.001 71.707 19.603 70.965 18.806C70.221 18.01 69.85 16.863 69.85 15.364V10.13C69.85 8.632 70.221 7.484 70.965 6.688C71.707 5.891 72.803 5.493 74.252 5.493H75.408C75.991 5.493 76.547 5.621 77.073 5.877C77.599 6.133 77.967 6.517 78.174 7.029H78.287V0.598999C78.287 0.200999 78.484 0.000999451 78.881 0.000999451H81.278C81.673 0.000999451 81.871 0.199999 81.871 0.598999V19.404C81.871 19.802 81.673 20.002 81.278 20.002H78.881C78.485 20.002 78.287 19.803 78.287 19.404V18.465H78.174C77.967 18.978 77.6 19.361 77.073 19.618C76.547 19.873 75.991 20.001 75.408 20.001ZM78.286 14.71V10.783C78.286 9.342 77.638 8.621 76.341 8.621H75.07C74.467 8.621 74.045 8.749 73.8 9.005C73.555 9.261 73.432 9.693 73.432 10.299V15.193C73.432 15.799 73.554 16.231 73.8 16.487C74.043 16.744 74.466 16.872 75.07 16.872H76.341C77.638 16.871 78.286 16.15 78.286 14.71Z">
                  </path>
                  <path class="fill-accent"
                    d="M46.516 3.045V0.569999C46.516 0.190999 46.704 0.000999451 47.08 0.000999451H49.534C49.911 0.000999451 50.098 0.181999 50.098 0.541999V3.046C50.098 3.425 49.91 3.615 49.534 3.615H47.08C46.703 3.614 46.516 3.425 46.516 3.045Z">
                  </path>
                  <path class="fill-accent"
                    d="M64.433 3.045V0.569999C64.433 0.190999 64.621 0.000999451 64.997 0.000999451H67.451C67.828 0.000999451 68.015 0.181999 68.015 0.541999V3.046C68.015 3.425 67.827 3.615 67.451 3.615H64.997C64.62 3.614 64.433 3.425 64.433 3.045Z">
                  </path>
                  <path class="fill-gray-900 dark:fill-white"
                    d="M3.398 9.838H6.42101L5.413 16.443L11.459 6.79H8.436L11.642 1.71H14.483L9.94801 19.999H4.406L-0.128998 1.71H4.346L3.398 9.838Z">
                  </path>
                </svg>
              </a>
            </div>
            <!-- Logo / End -->

            <!-- Navigation (Desktop) -->
            <ul class="hidden lg:gap-x-0 xl:gap-x-6 text-sm font-bold lg:flex">
              <li class="">
                <a class="group text-gray-900 dark:text-white relative inline-flex items-center gap-x-3 leading-10 px-3.5 xl:px-4.5 after:absolute after:top-[15px] after:left-0 after:bg-current-marker-dark after:transition-transform after:w-1.5 after:h-[9px] dark:after:bg-current-marker after:origin-right hover:after:origin-left after:scale-x-0 hover:after:scale-x-100"
                  href="{{ route('about') }}">
                  About
                </a>
              </li>
              <li class="">
                <a class="group text-gray-900 dark:text-white relative inline-flex items-center gap-x-3 leading-10 px-3.5 xl:px-4.5 after:absolute after:top-[15px] after:left-0 after:bg-current-marker-dark after:transition-transform after:w-1.5 after:h-[9px] dark:after:bg-current-marker after:origin-right hover:after:origin-left after:scale-x-0 hover:after:scale-x-100"
                  href="{{ route('browse') }}">
                  Browse
                </a>
              </li>
              <li class="">
                <a class="group text-gray-900 dark:text-white relative inline-flex items-center gap-x-3 leading-10 px-3.5 xl:px-4.5 after:absolute after:top-[15px] after:left-0 after:bg-current-marker-dark after:transition-transform after:w-1.5 after:h-[9px] dark:after:bg-current-marker after:origin-right hover:after:origin-left after:scale-x-0 hover:after:scale-x-100"
                  href="{{ route('movies') }}">
                  Movies
                </a>
              </li>
              <li class="">
                <a class="group text-gray-900 dark:text-white relative inline-flex items-center gap-x-3 leading-10 px-3.5 xl:px-4.5 after:absolute after:top-[15px] after:left-0 after:bg-current-marker-dark after:transition-transform after:w-1.5 after:h-[9px] dark:after:bg-current-marker after:origin-right hover:after:origin-left after:scale-x-0 hover:after:scale-x-100"
                  href="{{ route('series') }}">
                  Series
                </a>
              </li>
              <li class="">
                <a class="group text-gray-900 dark:text-white relative inline-flex items-center gap-x-3 leading-10 px-3.5 xl:px-4.5 after:absolute after:top-[15px] after:left-0 after:bg-current-marker-dark after:transition-transform after:w-1.5 after:h-[9px] dark:after:bg-current-marker after:origin-right hover:after:origin-left after:scale-x-0 hover:after:scale-x-100"
                  href="{{ route('coming-soon') }}">
                  Coming Soon
                </a>
              </li>
              <li class="">
                <a class="group text-gray-900 dark:text-white relative inline-flex items-center gap-x-3 leading-10 px-3.5 xl:px-4.5 after:absolute after:top-[15px] after:left-0 after:bg-current-marker-dark after:transition-transform after:w-1.5 after:h-[9px] dark:after:bg-current-marker after:origin-right hover:after:origin-left after:scale-x-0 hover:after:scale-x-100"
                  href="{{ route('pricing') }}">
                  Pricing
                </a>
              </li>
              <li class="">
                <a class="group text-gray-900 dark:text-white relative inline-flex items-center gap-x-3 leading-10 px-3.5 xl:px-4.5 after:absolute after:top-[15px] after:left-0 after:bg-current-marker-dark after:transition-transform after:w-1.5 after:h-[9px] dark:after:bg-current-marker after:origin-right hover:after:origin-left after:scale-x-0 hover:after:scale-x-100"
                  href="{{ route('contact') }}">
                  Contact
                </a>
              </li>
            </ul>
            <!-- Navigation (Desktop) / End -->

            <!-- Header Controls -->
            <div class="flex ml-auto -mr-3">

              {{-- Search button --}}
              <button type="button"
                x-data x-on:click="$dispatch('open-search')"
                class="flex items-center py-4 px-2 xl:px-3 text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors"
                aria-label="Search">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
              </button>

              <div class="flex items-center py-4 px-1 sm:px-2">
                <label for="theme-toggle" class="relative inline-flex cursor-pointer items-center">
                  <input type="checkbox" value="" id="theme-toggle" class="peer sr-only">
                  <span
                    class="relative z-10 block h-6 w-11 rounded-full border-2 border-gray-900 after:absolute after:top-0.5 after:left-0.5 after:h-4 after:w-4 after:rounded-full after:bg-gray-900 after:transition-transform peer-checked:after:translate-x-[20px] dark:border-white dark:after:bg-white"></span>
                  <svg role="img" class="pointer-events-none absolute left-[5px] top-1 h-4 w-4 stroke-accent">
                    <use xlink:href="{{ asset('assets/img/yt1/sprite.svg') }}#sun"></use>
                  </svg>
                  <svg role="img" class="pointer-events-none absolute right-[5px] top-1 h-4 w-4 stroke-accent">
                    <use xlink:href="{{ asset('assets/img/yt1/sprite.svg') }}#moon"></use>
                  </svg>
                </label>
              </div>

              @auth
              <div
                class="relative hover:[&>.sub-menu]:lg:visible hover:[&>.sub-menu]:animate-popper-pop-in hover:[&>.sub-menu]:lg:opacity-100">
                <a class="block py-4 px-2 xl:px-3" href="{{ route('account.dashboard') }}">
                  <svg role="img" class="h-6 w-6 fill-gray-900 dark:fill-white">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#user"></use>
                  </svg>
                </a>

                <ul
                  class="sub-menu invisible absolute right-full z-20 -mr-9 flex w-44 origin-top-right flex-col bg-white dark:bg-gray-700 py-3 text-sm font-bold opacity-0 shadow-2xl transition-all">
                  <li class="px-5.5">
                    <a class="flex flex-row-reverse items-center justify-between py-1.5 text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                      href="{{ route('account.profile') }}">My Profile</a>
                  </li>
                  <li class="px-5.5">
                    <a class="flex flex-row-reverse items-center justify-between py-1.5 text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                      href="{{ route('account.watching') }}">Continue Watching</a>
                  </li>
                  <li class="px-5.5">
                    <a class="flex flex-row-reverse items-center justify-between py-1.5 text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                      href="{{ route('account.watchlist') }}">My Watchlist</a>
                  </li>
                  <li class="px-5.5">
                    <a class="flex flex-row-reverse items-center justify-between py-1.5 text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                      href="{{ route('account.billing') }}">Billing</a>
                  </li>
                  <li class="px-5.5 mt-1 pt-1 border-t border-gray-100 dark:border-gray-600">
                    <a class="flex flex-row-reverse items-center justify-between py-1.5 text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                      href="{{ route('profiles.select') }}">Switch Profile</a>
                  </li>
                  <li class="px-5.5">
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit"
                        class="flex flex-row-reverse items-center justify-between py-1.5 text-gray-500 dark:text-gray-400 transition-colors hover:text-red-500 dark:hover:text-red-400 w-full text-left cursor-pointer">
                        Logout
                      </button>
                    </form>
                  </li>
                </ul>

              </div>
              @else
              <a class="block py-4 px-2 xl:px-3 text-xs font-bold uppercase text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors"
                href="{{ route('login') }}">
                Login
              </a>
              @endauth

              <button class="js-menu-toggle -mr-2 inline-flex py-4 px-2 sm:px-3 lg:hidden xl:px-4">
                <svg role="img" class="js-menu-toggle-icon-open h-6 w-6 fill-gray-900 dark:fill-white">
                  <use xlink:href="{{ asset('assets/img/yt1/sprite.svg') }}#menu"></use>
                </svg>
                <svg role="img" class="js-menu-toggle-icon-close hidden h-6 w-6 fill-gray-900 dark:fill-white">
                  <use xlink:href="{{ asset('assets/img/yt1/sprite.svg') }}#menu-close"></use>
                </svg>
              </button>

            </div>
            <!-- Header Controls / End -->

          </nav>
        </div>
      </header>

    </div>

    {{-- Search modal (Alpine.js) --}}
    <div
      x-data="{
        open: false,
        query: '',
        results: [],
        loading: false,
        activeIdx: -1,
        _timer: null,
        _suggestUrl: '{{ route('search.suggestions') }}',
        _browseUrl: '{{ route('browse') }}',
        show() {
          this.open = true;
          this.$nextTick(() => { if (this.$refs.searchInput) this.$refs.searchInput.focus(); });
        },
        hide() {
          this.open = false;
          this.query = '';
          this.results = [];
          this.activeIdx = -1;
          clearTimeout(this._timer);
        },
        onInput() {
          clearTimeout(this._timer);
          this.activeIdx = -1;
          if (this.query.length < 2) { this.results = []; this.loading = false; return; }
          this.loading = true;
          this._timer = setTimeout(() => {
            fetch(this._suggestUrl + '?q=' + encodeURIComponent(this.query), { headers: { 'Accept': 'application/json' } })
              .then(r => r.json())
              .then(data => { this.results = data; this.loading = false; })
              .catch(() => { this.loading = false; });
          }, 280);
        },
        moveDown() { if (this.results.length) this.activeIdx = Math.min(this.activeIdx + 1, this.results.length - 1); },
        moveUp() { this.activeIdx = Math.max(this.activeIdx - 1, -1); },
        go() {
          var r = this.results[this.activeIdx];
          if (r && r.url) { window.location.href = r.url; }
          else { this.submit(); }
        },
        submit() { if (this.query.trim()) window.location.href = this._browseUrl + '?q=' + encodeURIComponent(this.query.trim()); }
      }"
      x-on:open-search.window="show()"
      x-on:keydown.escape.window="if (open) hide()"
      x-on:keydown.window="
        if (($event.key === 'k' && ($event.metaKey || $event.ctrlKey)) ||
            ($event.key === '/' && !['INPUT','TEXTAREA'].includes(document.activeElement.tagName))) {
          $event.preventDefault(); show();
        }
      ">

      {{-- Backdrop --}}
      <div x-show="open" x-cloak
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] bg-black/70 backdrop-blur-sm flex items-start justify-center pt-[10vh] px-4"
        x-on:click.self="hide()">

        {{-- Panel --}}
        <div x-show="open"
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 -translate-y-3 scale-[0.98]"
          x-transition:enter-end="opacity-100 translate-y-0 scale-100"
          x-transition:leave="transition ease-in duration-150"
          x-transition:leave-start="opacity-100 translate-y-0 scale-100"
          x-transition:leave-end="opacity-0 -translate-y-3 scale-[0.98]"
          class="w-full max-w-2xl bg-white dark:bg-gray-900 shadow-2xl overflow-hidden">

          {{-- Input row --}}
          <div class="flex items-center gap-x-3 px-5 h-16 border-b border-gray-200 dark:border-gray-700">
            <svg class="w-5 h-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input
              x-ref="searchInput"
              x-model="query"
              x-on:input="onInput()"
              x-on:keydown.arrow-down.prevent="moveDown()"
              x-on:keydown.arrow-up.prevent="moveUp()"
              x-on:keydown.enter.prevent="results.length ? go() : submit()"
              type="text"
              placeholder="Search movies, series, coming soon…"
              autocomplete="off" spellcheck="false"
              class="flex-1 bg-transparent text-gray-900 dark:text-white placeholder:text-gray-400 text-base font-medium focus:outline-none">
            <button x-on:click="hide()"
              class="shrink-0 p-1 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" aria-label="Close">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M18 6 6 18M6 6l12 12"/>
              </svg>
            </button>
          </div>

          {{-- Loading --}}
          <div x-show="loading" class="flex items-center gap-x-3 px-5 py-5 text-sm text-gray-400">
            <svg class="w-4 h-4 animate-spin fill-current text-accent" viewBox="0 0 24 24">
              <path d="M12 2a10 10 0 0 1 10 10h-2a8 8 0 0 0-8-8V2z"/>
            </svg>
            Searching…
          </div>

          {{-- Results --}}
          <div x-show="results.length > 0 && !loading" class="max-h-[50vh] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800">
            <template x-for="(r, i) in results" :key="i">
              <a :href="r.url ?? (_browseUrl + '?q=' + encodeURIComponent(query))"
                x-on:mouseenter="activeIdx = i"
                :class="activeIdx === i ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'"
                class="flex items-center gap-x-4 px-5 py-3 transition-colors">
                {{-- Thumbnail --}}
                <div class="w-9 h-12 shrink-0 bg-gray-100 dark:bg-gray-800 overflow-hidden">
                  <img x-show="r.thumb" :src="r.thumb" class="w-full h-full object-cover" loading="lazy">
                </div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                  <p class="font-bold text-sm text-gray-900 dark:text-white truncate" x-text="r.title"></p>
                  <p x-show="r.genre" class="text-xs text-gray-500 mt-0.5 truncate" x-text="r.genre"></p>
                </div>
                {{-- Type badge --}}
                <span class="shrink-0 text-[10px] font-bold uppercase tracking-widest px-2 py-0.5"
                  :class="{
                    'bg-accent/20 text-accent': r.type === 'movie',
                    'bg-blue-500/15 text-blue-500': r.type === 'series',
                    'bg-yellow-400/15 text-yellow-600 dark:text-yellow-400': r.type === 'coming_soon'
                  }"
                  x-text="r.label"></span>
              </a>
            </template>
            <div class="px-5 py-3">
              <a :href="_browseUrl + '?q=' + encodeURIComponent(query)"
                class="text-xs font-bold uppercase tracking-widest text-accent hover:text-accent/70 transition-colors">
                View all results for "<span x-text="query"></span>" →
              </a>
            </div>
          </div>

          {{-- No results --}}
          <div x-show="!loading && query.length >= 2 && results.length === 0" class="px-5 py-10 text-center">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <p class="text-sm font-bold text-gray-900 dark:text-white">No results found</p>
            <p class="text-xs text-gray-500 mt-1">Try a different search term</p>
          </div>

          {{-- Empty / genre chips --}}
          <div x-show="query.length < 2 && !loading" class="px-5 py-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-3">Try searching for</p>
            <div class="flex flex-wrap gap-2">
              <template x-for="genre in ['Action', 'Drama', 'Comedy', 'Thriller', 'Horror', 'Sci-Fi']" :key="genre">
                <button x-on:click="query = genre; onInput()"
                  class="text-xs font-bold px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-accent hover:text-gray-900 transition-colors"
                  x-text="genre"></button>
              </template>
            </div>
          </div>

          {{-- Keyboard hints --}}
          <div class="flex items-center gap-x-5 px-5 py-2.5 border-t border-gray-100 dark:border-gray-800 text-[11px] text-gray-400">
            <span><kbd class="bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-1.5 py-0.5 text-[10px] font-mono">↑↓</kbd> navigate</span>
            <span><kbd class="bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-1.5 py-0.5 text-[10px] font-mono">↵</kbd> select</span>
            <span><kbd class="bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-1.5 py-0.5 text-[10px] font-mono">Esc</kbd> close</span>
            <span class="ml-auto hidden sm:inline"><kbd class="bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-1.5 py-0.5 text-[10px] font-mono">/</kbd> or <kbd class="bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-1.5 py-0.5 text-[10px] font-mono">⌘K</kbd> to open</span>
          </div>

        </div>{{-- /panel --}}
      </div>{{-- /backdrop --}}
    </div>{{-- /x-data --}}

    <!-- Mobile Menu -->
    <div
      class="js-mobile-menu fixed left-0 top-[64px] z-50 h-[calc(100dvh-64px)] w-full translate-x-full overflow-hidden bg-white dark:bg-gray-700 transition-transform duration-300 lg:hidden">

      {{-- ── L1: Site / Guest menu (always first) ── --}}
      <div id="mobile-l1" class="absolute inset-0 flex flex-col overflow-y-auto">
        <div class="container flex-1 py-5">
          <ul class="flex flex-col font-bold">
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                href="{{ route('about') }}">About</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                href="{{ route('browse') }}">Browse</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                href="{{ route('movies') }}">Movies</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                href="{{ route('series') }}">Series</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                href="{{ route('coming-soon') }}">Coming Soon</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                href="{{ route('pricing') }}">Pricing</a>
            </li>
            <li class="flex items-center">
              <a class="flex-grow py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                href="{{ route('contact') }}">Contact</a>
            </li>
          </ul>
        </div>

        <div class="container pb-5 border-t border-gray-200/50 dark:border-gray-200/10">
          @auth
          <button type="button" data-nav-to="mobile-l2"
            class="flex items-center justify-between w-full py-4 font-bold text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors">
            My Account
            <svg class="w-3.5 h-3.5 shrink-0 opacity-40" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
          </button>
          @if (auth()->user()->isAdmin())
          <button type="button" data-nav-to="mobile-l3"
            class="flex items-center justify-between w-full py-4 font-bold text-accent hover:text-accent/80 transition-colors border-t border-gray-200/50 dark:border-gray-200/10">
            Admin Panel
            <svg class="w-3.5 h-3.5 shrink-0 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
          </button>
          @endif
          <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-200/50 dark:border-gray-200/10">
            @csrf
            <button type="submit"
              class="w-full text-left py-4 font-bold text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
              Logout
            </button>
          </form>
          @else
          <button type="button" data-nav-to="mobile-l2"
            class="flex items-center justify-between w-full py-4 font-bold text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors">
            Login / Register
            <svg class="w-3.5 h-3.5 shrink-0 opacity-40" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
          </button>
          @endauth
        </div>
      </div>

      {{-- ── L2: Account or Auth menu ── --}}
      <div id="mobile-l2" class="absolute inset-0 flex flex-col overflow-y-auto bg-white dark:bg-gray-700 transition-transform duration-300" style="transform:translateX(100%)">
        <div class="container flex-1 py-5">
          <button type="button" data-nav-close="mobile-l2"
            class="flex items-center gap-x-2 py-3 mb-4 font-bold text-sm text-gray-500 dark:text-gray-400 hover:text-accent dark:hover:text-accent transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
            Back
          </button>

          @auth
          <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">My Account</p>
          <ul class="flex flex-col font-bold">
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal transition-colors hover:text-accent dark:hover:text-accent {{ request()->routeIs('account.profile') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('account.profile') }}">My Profile</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal transition-colors hover:text-accent dark:hover:text-accent {{ request()->routeIs('account.watching') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('account.watching') }}">Continue Watching</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal transition-colors hover:text-accent dark:hover:text-accent {{ request()->routeIs('account.watchlist') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('account.watchlist') }}">My Watchlist</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal transition-colors hover:text-accent dark:hover:text-accent {{ request()->routeIs('account.billing') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('account.billing') }}">Billing Details</a>
            </li>
            <li class="flex items-center">
              <a class="flex-grow py-4 leading-normal transition-colors hover:text-accent dark:hover:text-accent text-gray-900 dark:text-white"
                href="{{ route('profiles.select') }}">Switch Profile</a>
            </li>
          </ul>
          @else
          <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">Account</p>
          <ul class="flex flex-col font-bold">
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                href="{{ route('login') }}">Login</a>
            </li>
            <li class="flex items-center">
              <a class="flex-grow py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                href="{{ route('register') }}">Create Account</a>
            </li>
          </ul>
          @endauth
        </div>

        @auth
        <div class="container pb-5 border-t border-gray-200/50 dark:border-gray-200/10">
          @if (auth()->user()->isAdmin())
          <button type="button" data-nav-to="mobile-l3"
            class="flex items-center justify-between w-full py-4 font-bold text-accent hover:text-accent/80 transition-colors">
            Admin Panel
            <svg class="w-3.5 h-3.5 shrink-0 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
          </button>
          @endif
          <form method="POST" action="{{ route('logout') }}" class="@if(auth()->user()->isAdmin()) border-t border-gray-200/50 dark:border-gray-200/10 @endif">
            @csrf
            <button type="submit"
              class="w-full text-left py-4 font-bold text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
              Logout
            </button>
          </form>
        </div>
        @endauth
      </div>

      {{-- ── L3: Admin menu ── --}}
      @auth
      @if (auth()->user()->isAdmin())
      <div id="mobile-l3" class="absolute inset-0 flex flex-col overflow-y-auto bg-white dark:bg-gray-700 transition-transform duration-300" style="transform:translateX(100%)">
        <div class="container flex-1 py-5">
          <button type="button" data-nav-close="mobile-l3"
            class="flex items-center gap-x-2 py-3 mb-4 font-bold text-sm text-gray-500 dark:text-gray-400 hover:text-accent dark:hover:text-accent transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
            Back
          </button>

          <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-accent">Admin Panel</p>
          <ul class="flex flex-col font-bold">
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal transition-colors hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.dashboard') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal transition-colors hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.movies.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.movies.index') }}">Movies</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal transition-colors hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.series.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.series.index') }}">Series</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal transition-colors hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.coming-soon.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.coming-soon.index') }}">Coming Soon</a>
            </li>
            <li class="flex items-center border-b border-b-gray-200/50 dark:border-b-gray-200/10">
              <a class="flex-grow py-4 leading-normal transition-colors hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.users.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.users.index') }}">Users</a>
            </li>
            <li class="flex items-center">
              <a class="flex-grow py-4 leading-normal transition-colors hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.packages.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('admin.packages.index') }}">Subscription Packages</a>
            </li>
          </ul>
        </div>

        <div class="container pb-5 border-t border-gray-200/50 dark:border-gray-200/10">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
              class="w-full text-left py-4 font-bold text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
              Logout
            </button>
          </form>
        </div>
      </div>
      @endif
      @endauth

    </div>
    <!-- Mobile Menu / End -->

    <script>
    (function () {
      function showPanel(id) {
        var el = document.getElementById(id);
        if (el) el.style.transform = 'translateX(0)';
      }
      function hidePanel(id) {
        var el = document.getElementById(id);
        if (el) el.style.transform = 'translateX(100%)';
      }

      document.addEventListener('click', function (e) {
        var fwd = e.target.closest('[data-nav-to]');
        if (fwd) { showPanel(fwd.getAttribute('data-nav-to')); return; }
        var back = e.target.closest('[data-nav-close]');
        if (back) { hidePanel(back.getAttribute('data-nav-close')); return; }
      });

      var toggle = document.querySelector('.js-menu-toggle');
      if (toggle) {
        toggle.addEventListener('click', function () {
          setTimeout(function () {
            hidePanel('mobile-l2');
            hidePanel('mobile-l3');
          }, 320);
        });
      }
    })();
    </script>

    @if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
      x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
      class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9998] w-full max-w-md px-4">
      <div class="flex items-start gap-x-3 bg-red-600 text-white px-5 py-4 shadow-lg">
        <svg class="h-5 w-5 shrink-0 mt-0.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        <p class="text-sm font-medium leading-snug">{{ session('error') }}</p>
        <button @click="show = false" class="ml-auto shrink-0 text-white/80 hover:text-white">
          <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
      </div>
    </div>
    @endif

    @if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
      x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
      class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9998] w-full max-w-md px-4">
      <div class="flex items-start gap-x-3 bg-green-600 text-white px-5 py-4 shadow-lg">
        <svg class="h-5 w-5 shrink-0 mt-0.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z" style="display:none"/><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        <p class="text-sm font-medium leading-snug">{{ session('success') }}</p>
        <button @click="show = false" class="ml-auto shrink-0 text-white/80 hover:text-white">
          <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
      </div>
    </div>
    @endif

    <main id="main-content" class="grow lg:pt-0">
      @yield('content')
    </main>

    <x-footer />
  </div>

  <dialog id="login-register"
    class="js-vv-modal mx-auto border-none bg-transparent p-0 m-0 backdrop:bg-gray-900/90 h-dvh inset-0 fixed !max-h-dvh w-[470px] max-w-[90%]"
    onclick="if(event.target===this)this.close()">

    <div class="h-screen flex items-center justify-center py-6 shrink-0 w-full">
      <div class="bg-white dark:bg-gray-700 shadow-3xl overflow-y-auto w-full">

        {{-- Header --}}
        <div class="relative flex h-28 items-center bg-[url('/assets/img/str1/samples/modal-header-bg.jpg')] bg-cover bg-center bg-no-repeat px-6 md:px-10 lg:h-[140px]">
          <div class="relative z-[4] text-white">
            <h4 class="text-2xl font-bold lg:mb-0.5 lg:text-3xl xl:text-[32px] xl:leading-none">Login to your account</h4>
            <div class="font-bold leading-none z-[6]">An account is required to watch movies</div>
          </div>

          <div class="absolute inset-0 z-[1] bg-black mix-blend-color"></div>
          <div class="absolute inset-0 z-[2] bg-primary mix-blend-multiply"></div>
          <div class="absolute inset-0 z-[3] bg-gray-900 mix-blend-lighten"></div>

          <button type="button" onclick="document.getElementById('login-register').close()"
            class="absolute top-4 right-4 z-[6] text-white/60 hover:text-white transition-colors leading-none text-2xl">&times;</button>

          <img class="absolute right-0 bottom-0 z-[5] -mt-[22px] max-w-[41%]"
            src="{{ asset('assets/img/str1/samples/modal-header-hero.png') }}" alt="">
        </div>

        {{-- Login form --}}
        <div class="py-6 px-6 lg:px-10 lg:py-9">
          <form class="flex flex-col gap-y-5 lg:gap-y-6" method="POST" action="{{ url('/login') }}">
            @csrf
            <div>
              <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="modal-login-email">Email</label>
              <input
                class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 w-full"
                type="email" name="email" id="modal-login-email" value="{{ old('email') }}" autocomplete="email" required>
              @error('email', 'login')
              <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="modal-login-password">Password</label>
              <input
                class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 w-full"
                type="password" name="password" id="modal-login-password" autocomplete="current-password" required>
              @error('password', 'login')
              <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
              @enderror
            </div>
            <div class="flex justify-between flex-wrap gap-y-3">
              <label class="flex items-center gap-x-3 hover:cursor-pointer" for="modal-remember-me">
                <input name="remember" id="modal-remember-me" type="checkbox"
                  class="border-gray-650 dark:border-gray-650 h-4 w-4 bg-white checked:border-accent checked:bg-white checked:text-accent checked:hover:border-accent checked:hover:bg-white dark:bg-gray-900 dark:checked:border-accent dark:checked:hover:border-accent dark:checked:hover:bg-gray-900">
                <span class="text-xs font-bold uppercase text-gray-900 dark:text-white">Remember Me</span>
              </label>
              @if(Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="text-xs font-bold uppercase text-primary dark:text-white hover:text-accent dark:hover:text-accent transition-colors">Forgot Password?</a>
              @endif
            </div>
            <button type="submit" class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 w-full">
              Login to your account
            </button>
          </form>

          <p class="mt-5 text-sm text-center text-gray-500 dark:text-gray">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-bold text-accent hover:underline">Register</a>
          </p>
        </div>

      </div>
    </div>

  </dialog>

  <!-- Scripts
  ================================================== -->
  <!-- Vendors JS -->
  <script src="{{ asset('assets/vendors/common/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendors/common/swiper/js/swiper-bundle.min.js') }}"></script>

  <!-- Template JS -->
  <script src="{{ asset('assets/js/common.js') }}"></script>
  <script src="{{ asset('assets/js/str1/init.js') }}"></script>

  <script>
  window.APP_AUTH = {!! json_encode(auth()->check()) !!};
  window.OPEN_AUTH_MODAL = {!! json_encode($errors->getBag('login')->any() || session('show_auth_modal')) !!};

  function watchOrLogin(url) {
    if (window.APP_AUTH) {
      window.location.href = url;
    } else {
      localStorage.setItem('moviebox_watch_intended', url);
      var modal = document.getElementById('login-register');
      if (modal && modal.showModal) modal.showModal();
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    if (window.APP_AUTH) {
      var intended = localStorage.getItem('moviebox_watch_intended');
      if (intended) {
        localStorage.removeItem('moviebox_watch_intended');
        window.location.replace(intended);
        return;
      }
    }
    if (window.OPEN_AUTH_MODAL) {
      var modal = document.getElementById('login-register');
      if (modal && modal.showModal) modal.showModal();
    }
  });
  </script>

  @stack('scripts')

  <script>
  document.addEventListener('alpine:init', function () {
    Alpine.data('watchlistToggle', function (initialState, url) {
      return {
        watchlisted: initialState,
        busy: false,
        toggle: function () {
          if (this.busy) return;
          this.busy = true;
          var self = this;
          fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Accept': 'application/json',
            }
          })
          .then(function (r) { return r.json(); })
          .then(function (d) { self.watchlisted = d.watchlisted; self.busy = false; })
          .catch(function () { self.busy = false; });
        }
      };
    });
  });
  </script>

</body>

</html>