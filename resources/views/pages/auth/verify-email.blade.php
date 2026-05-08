@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="flex min-h-[80vh] justify-center">

  {{-- Right content --}}
  <div class="w-full lg:w-1/2 flex items-center justify-center py-16 px-6 bg-white dark:bg-gray-900">
    <div class="w-full max-w-md">

      <h3 class="text-2xl xl:text-3xl font-bold text-gray-900 dark:text-white mb-1">Verify your Email</h3>
      <p class="text-sm text-gray-500 dark:text-gray mb-8">
        Thanks for signing up! Please click the verification link sent to
        <strong class="text-gray-900 dark:text-white font-bold">{{ auth()->user()->email }}</strong>.
      </p>

      @if (session('status') === 'verification-link-sent')
        <div class="mb-6 border border-accent bg-accent/10 px-4 py-3 text-sm text-accent font-medium">
          A new verification link has been sent to your email address.
        </div>
      @endif

      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit"
          class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 w-full">
          Resend Verification Email
        </button>
      </form>

      <div class="mt-6 text-center">
        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit"
            class="text-xs font-bold uppercase text-gray-500 hover:text-accent dark:hover:text-accent transition-colors">
            Log Out
          </button>
        </form>
      </div>

    </div>
  </div>

</div>
@endsection
