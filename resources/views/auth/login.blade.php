@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="flex min-h-screen">

  {{-- Left: banner --}}
  <div class="hidden md:flex md:w-5/12 lg:w-1/2 relative overflow-hidden flex-col">
    @if($banner ?? null)
      <img src="{{ $banner }}" alt="" class="absolute inset-0 w-full h-full object-cover">
    @else
      <div class="absolute inset-0 bg-gray-800"></div>
    @endif
    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(101,76,255,0.65) 0%, rgba(17,24,39,0.88) 100%);"></div>
    <div class="absolute inset-y-0 right-0 w-1/2 auth-banner-fade-r"></div>

    <div class="relative z-10 flex flex-col justify-between h-full p-10 lg:p-14">
      <a href="{{ route('home') }}" class="text-white font-bold text-lg tracking-wide uppercase">
        {{ config('app.name') }}
      </a>
      <div>
        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-3 leading-tight">
          Watch Anywhere.<br>Stream Everything.
        </h2>
        <p class="text-gray-300 text-sm lg:text-base leading-relaxed">
          Movies, series and trailers — all in one place.
        </p>
      </div>
    </div>
  </div>

  {{-- Right: form --}}
  <div class="flex-1 flex items-center justify-center px-6 py-14 bg-white dark:bg-gray-900">
    <div class="w-full max-w-md">

      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Welcome back</h1>
        <p class="text-sm text-gray-500">Login to continue watching.</p>
      </div>

      @if (session('status'))
        <div class="mb-6 border border-accent/60 bg-accent/10 px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
          {{ session('status') }}
        </div>
      @endif

      @if (session('banned'))
        <div id="banned-toast"
             class="mb-6 flex items-start justify-between gap-x-3 border border-red-500 bg-red-500/10 px-4 py-3">
          <div class="flex items-start gap-x-3">
            <svg class="mt-0.5 w-4 h-4 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ session('banned') }}</p>
          </div>
          <button type="button" onclick="document.getElementById('banned-toast').remove()"
                  class="shrink-0 text-gray-400 hover:text-red-500 transition-colors">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
          </button>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-y-5"
        x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div>
          <label class="block text-sm font-bold uppercase text-gray-500 mb-2" for="email">Email</label>
          <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 w-full"
            type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
          @error('email')
          <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-sm font-bold uppercase text-gray-500 mb-2" for="password">Password</label>
          <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 w-full"
            type="password" name="password" id="password" autocomplete="current-password" required>
          @error('password')
          <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
          @enderror
        </div>

        <div class="flex justify-between items-center flex-wrap gap-y-2">
          <label class="flex items-center gap-x-2 cursor-pointer">
            <input type="checkbox" name="remember" class="h-4 w-4 border-gray-300 text-accent focus:ring-accent">
            <span class="text-xs font-bold uppercase text-gray-900 dark:text-white">Remember Me</span>
          </label>
          <a href="{{ route('password.request') }}" class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">
            Forgot Password?
          </a>
        </div>

        <button type="submit" :disabled="loading"
          class="inline-flex justify-center items-center gap-x-2 font-bold leading-none uppercase py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 transition-colors w-full disabled:opacity-60 disabled:cursor-not-allowed">
          <svg x-show="loading" class="animate-spin h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <span x-text="loading ? 'Please wait…' : 'Login to your account'"></span>
        </button>
      </form>

      <div class="relative my-6" style="margin: 20px 0px;">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
        </div>
        <div class="relative flex justify-center">
          <span class="bg-white dark:bg-gray-900 px-3 text-xs font-bold uppercase tracking-widest text-gray-400">or continue with</span>
        </div>
      </div>

      <div class="flex gap-3">
        <a href="{{ route('social.redirect', 'google') }}"
          class="flex-1 flex items-center justify-center gap-2 py-3 border border-gray-200 dark:border-gray-700 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
          <svg width="18" height="18" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
          </svg>
          Google
        </a>
        <a href="{{ route('social.redirect', 'facebook') }}"
          class="flex-1 flex items-center justify-center gap-2 py-3 border border-gray-200 dark:border-gray-700 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2">
            <path d="M24 12.073C24 5.404 18.627 0 12 0S0 5.404 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.418c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
          </svg>
          Facebook
        </a>
      </div>

      <p class="mt-6 text-sm text-center text-gray-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-bold text-accent hover:underline">Register</a>
      </p>

    </div>
  </div>

</div>
@endsection
