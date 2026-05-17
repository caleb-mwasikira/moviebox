@extends('layouts.app')

@section('title', 'Reset Password')

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
          Almost there.<br>Choose a new password.
        </h2>
        <p class="text-gray-300 text-sm lg:text-base leading-relaxed">
          Pick something strong and memorable.
        </p>
      </div>
    </div>
  </div>

  {{-- Right: form --}}
  <div class="flex-1 flex items-center justify-center px-6 py-14 bg-white dark:bg-gray-900">
    <div class="w-full max-w-md">

      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Set new password</h1>
        <p class="text-sm text-gray-500">Your identity has been verified. Choose a new password below.</p>
      </div>

      <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-y-5"
        x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div>
          <label class="block text-sm font-bold uppercase text-gray-500 mb-2" for="password">New Password</label>
          <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 w-full"
            type="password" name="password" id="password" autocomplete="new-password" required autofocus>
          @error('password')
          <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-sm font-bold uppercase text-gray-500 mb-2" for="password_confirmation">Confirm New Password</label>
          <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 w-full"
            type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" required>
        </div>

        <button type="submit" :disabled="loading"
          class="inline-flex justify-center items-center gap-x-2 font-bold leading-none uppercase py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 transition-colors w-full disabled:opacity-60 disabled:cursor-not-allowed">
          <svg x-show="loading" class="animate-spin h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <span x-text="loading ? 'Please wait…' : 'Reset Password'"></span>
        </button>
      </form>

    </div>
  </div>

</div>
@endsection
