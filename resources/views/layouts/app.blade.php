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
                  href="{{ route('events') }}">
                  Events
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
                <a class="block py-4 px-2 xl:px-3" href="{{ route('account') }}">
                  <svg role="img" class="h-6 w-6 fill-gray-900 dark:fill-white">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#user"></use>
                  </svg>
                </a>

                <ul
                  class="sub-menu invisible absolute right-full z-20 -mr-9 flex w-40 origin-top-right flex-col bg-white dark:bg-gray-700 py-3 text-sm font-bold opacity-0 shadow-2xl transition-all">
                  <li class="px-5.5">
                    <a class="flex flex-row-reverse items-center justify-between py-1.5 text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                      href="{{ route('account') }}">
                      Account
                    </a>
                  </li>
                  <li class="px-5.5">
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit"
                        class="flex flex-row-reverse items-center justify-between py-1.5 text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent w-full text-left cursor-pointer">
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

    <!-- Mobile Menu -->
    <div
      class="js-mobile-menu p-t-[64px] fixed left-0 top-[64px] z-50 block h-[calc(100dvh-64px)] w-full translate-x-full overflow-auto bg-white dark:bg-gray-700 py-5 text-primary dark:text-white transition-transform duration-300 lg:hidden">
      <div class="container">
        <!-- Navigation (Mobile) -->
        <ul class="flex flex-col font-bold">
          <li class="flex flex-wrap items-center gap-x-4 border-b border-b-gray-200/50 dark:border-b-gray-200/10">
            <a class="flex-grow gap-x-1 py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
              href="{{ route('about') }}">
              About
            </a>
          </li>
          <li class="flex flex-wrap items-center gap-x-4 border-b border-b-gray-200/50 dark:border-b-gray-200/10">
            <a class="flex-grow gap-x-1 py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
              href="{{ route('browse') }}">
              Browse
            </a>
          </li>
          <li class="flex flex-wrap items-center gap-x-4 border-b border-b-gray-200/50 dark:border-b-gray-200/10">
            <a class="flex-grow gap-x-1 py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
              href="{{ route('movies') }}">
              Movies
            </a>
          </li>
          <li class="flex flex-wrap items-center gap-x-4 border-b border-b-gray-200/50 dark:border-b-gray-200/10">
            <a class="flex-grow gap-x-1 py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
              href="{{ route('series') }}">
              Series
            </a>
          </li>
          <li class="flex flex-wrap items-center gap-x-4 border-b border-b-gray-200/50 dark:border-b-gray-200/10">
            <a class="flex-grow gap-x-1 py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
              href="{{ route('events') }}">
              Events
            </a>
          </li>
          <li class="flex flex-wrap items-center gap-x-4 border-b border-b-gray-200/50 dark:border-b-gray-200/10">
            <a class="flex-grow gap-x-1 py-4 leading-normal text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
              href="{{ route('contact') }}">
              Contact
            </a>
          </li>
        </ul>
        <!-- Navigation (Mobile) / End -->
      </div>
    </div>
    <!-- Mobile Menu / End -->

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
              @error('email')
              <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="modal-login-password">Password</label>
              <input
                class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 w-full"
                type="password" name="password" id="modal-login-password" autocomplete="current-password" required>
              @error('password')
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
  window.OPEN_AUTH_MODAL = {!! json_encode($errors->any() || session('show_auth_modal')) !!};

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

</body>

</html>