@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="flex min-h-[80vh]">

  {{-- Left banner --}}
  <div class="hidden lg:flex lg:w-1/2 relative bg-[url('/assets/img/str1/samples/banner-bg.jpg')] bg-cover bg-center flex-col items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-gray-900/75"></div>
    <div class="relative z-10 text-center text-white px-12 max-w-md">
      <h2 class="text-4xl xl:text-5xl font-bold mb-5 leading-tight">Forgot Password?</h2>
      <p class="text-base xl:text-lg text-gray-300 leading-relaxed">
        No worries. Enter your email and we'll send you a link to reset your password.
      </p>
      <div class="mt-10 flex justify-center gap-3">
        <span class="h-1 w-3 bg-gray-500 rounded-full"></span>
        <span class="h-1 w-3 bg-gray-500 rounded-full"></span>
        <span class="h-1 w-8 bg-accent rounded-full"></span>
      </div>
    </div>
  </div>

  {{-- Right form --}}
  <div class="w-full lg:w-1/2 flex items-center justify-center py-16 px-6 bg-white dark:bg-gray-900">
    <div class="w-full max-w-md">

      <h3 class="text-2xl xl:text-3xl font-bold text-gray-900 dark:text-white mb-1">Reset Password</h3>
      <p class="text-sm text-gray-500 dark:text-gray mb-8">
        Enter the email address linked to your account and we'll email a reset link.
      </p>

      @if (session('status'))
        <div class="mb-6 border border-accent bg-accent/10 px-4 py-3 text-sm text-accent font-medium">
          {{ session('status') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="mb-6 border border-danger bg-danger/10 px-4 py-3 text-sm text-danger font-medium">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-y-5"
        x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="email">
            Email Address
          </label>
          <input
            class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('email') border-danger @enderror"
            type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required>
        </div>

        <div class="pt-2">
          <button type="submit" :disabled="loading"
            class="inline-flex items-center text-center font-bold leading-none transition-colors uppercase justify-center gap-x-2 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 w-full disabled:opacity-60 disabled:cursor-not-allowed">
            <svg x-show="loading" class="animate-spin h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span x-text="loading ? 'Sending…' : 'Send Reset Link'"></span>
          </button>
        </div>
      </form>

      <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray">
        <a href="{{ route('login') }}" class="font-bold text-accent hover:text-accent/80 transition-colors">
          &larr; Back to Login
        </a>
      </p>

    </div>
  </div>

</div>
@endsection
