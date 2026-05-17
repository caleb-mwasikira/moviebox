@extends('layouts.app')

@section('title', 'Verify Your Email')

@section('content')
<div class="flex min-h-screen">

  {{-- Left: banner --}}
  <div class="hidden md:flex md:w-5/12 lg:w-1/2 relative overflow-hidden flex-col">
    <div class="absolute inset-0 bg-gray-800"></div>
    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(101,76,255,0.65) 0%, rgba(17,24,39,0.88) 100%);"></div>
    <div class="absolute inset-y-0 right-0 w-1/2 auth-banner-fade-r"></div>

    <div class="relative z-10 flex flex-col justify-between h-full p-10 lg:p-14">
      <a href="{{ route('home') }}" class="text-white font-bold text-lg tracking-wide uppercase">
        {{ config('app.name') }}
      </a>
      <div>
        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-3 leading-tight">
          Check your<br>inbox.
        </h2>
        <p class="text-gray-300 text-sm lg:text-base leading-relaxed">
          We sent a 6-digit code to your email address.<br>It expires in 15 minutes.
        </p>
      </div>
    </div>
  </div>

  {{-- Right: form --}}
  <div class="flex-1 flex items-center justify-center px-6 py-14 bg-white dark:bg-gray-900">
    <div class="w-full max-w-md">

      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Verify your email</h1>
        <p class="text-sm text-gray-500">
          We sent a code to <span class="font-bold text-gray-900 dark:text-white">{{ $email }}</span>.
        </p>
      </div>

      @if (session('status') === 'verification-sent')
        <div class="mb-6 border border-accent bg-accent/10 px-4 py-3 text-sm text-accent font-medium">
          A new verification code has been sent to your email.
        </div>
      @endif

      <form method="POST" action="{{ route('verification.verify') }}" class="flex flex-col gap-y-5"
        x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div>
          <label class="block text-sm font-bold uppercase text-gray-500 mb-2" for="otp">6-Digit Code</label>
          <input
            class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 w-full text-center text-2xl tracking-[0.5em]"
            type="text" name="otp" id="otp" value="{{ old('otp') }}"
            inputmode="numeric" pattern="\d{6}" maxlength="6"
            autocomplete="one-time-code" required autofocus>
          @error('otp')
            <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit" :disabled="loading"
          class="inline-flex justify-center items-center gap-x-2 font-bold leading-none uppercase py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 transition-colors w-full disabled:opacity-60 disabled:cursor-not-allowed">
          <svg x-show="loading" class="animate-spin h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <span x-text="loading ? 'Verifying…' : 'Verify Email'"></span>
        </button>
      </form>

      <div class="mt-6 flex flex-col gap-y-3 text-sm text-center text-gray-500">
        <p>
          Didn't receive a code?
          <form method="POST" action="{{ route('verification.send') }}" class="inline">
            @csrf
            <button type="submit" class="font-bold text-accent hover:underline">Resend code</button>
          </form>
        </p>
        <p>
          Wrong account?
          <a href="{{ route('logout') }}" class="font-bold text-gray-500 hover:text-accent transition-colors"
            onclick="event.preventDefault(); document.getElementById('logout-form-verify').submit();">Sign out</a>
          <form id="logout-form-verify" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </p>
      </div>

    </div>
  </div>

</div>
@endsection
