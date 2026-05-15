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

      <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-y-5">
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

        <button type="submit" class="inline-flex justify-center items-center font-bold leading-none uppercase py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 transition-colors w-full">
          Login to your account
        </button>
      </form>

      <p class="mt-6 text-sm text-center text-gray-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-bold text-accent hover:underline">Register</a>
      </p>

    </div>
  </div>

</div>
@endsection
