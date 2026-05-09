@extends('layouts.app')

@section('title', 'Forgot Password')

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
          Forgot your<br>password?
        </h2>
        <p class="text-gray-300 text-sm lg:text-base leading-relaxed">
          No worries. We'll send a verification code to your email.
        </p>
      </div>
    </div>
  </div>

  {{-- Right: form --}}
  <div class="flex-1 flex items-center justify-center px-6 py-14 bg-white dark:bg-gray-900">
    <div class="w-full max-w-md">

      <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Reset your password</h1>
        <p class="text-sm text-gray-500">Enter your email and we'll send you a 6-digit code.</p>
      </div>

      @if(session('status'))
      <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-400">
        {{ session('status') }}
      </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-y-5">
        @csrf

        <div>
          <label class="block text-sm font-bold uppercase text-gray-500 mb-2" for="email">Email Address</label>
          <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 w-full"
            type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
          @error('email')
          <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit" class="inline-flex justify-center items-center font-bold leading-none uppercase py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 transition-colors w-full">
          Send Verification Code
        </button>
      </form>

      <p class="mt-6 text-sm text-center text-gray-500">
        Remembered it?
        <a href="{{ route('login') }}" class="font-bold text-accent hover:underline">Back to Login</a>
      </p>

    </div>
  </div>

</div>
@endsection
