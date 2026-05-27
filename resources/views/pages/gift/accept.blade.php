@extends('layouts.app')

@section('title', 'Accept Your Gift')

@section('content')
<main class="grow">
  <div class="py-16 lg:py-24">
    <div class="container max-w-lg">

      @if ($gift->isClaimed())

        {{-- Already claimed --}}
        <div class="text-center">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 mb-6">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Gift already claimed</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
            This gift subscription has already been redeemed. Each gift can only be claimed once.
          </p>
          <a href="{{ route('home') }}"
            class="inline-flex font-bold uppercase tracking-wide py-3 px-8 text-xs bg-gray-900 text-black hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors">
            Go to Homepage
          </a>
        </div>

      @elseif ($gift->isExpired())

        {{-- Expired --}}
        <div class="text-center">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 mb-6">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Gift link expired</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
            This gift link expired on {{ $gift->expires_at->format('F j, Y') }}. Ask the sender to resend it.
          </p>
          <a href="{{ route('home') }}"
            class="inline-flex font-bold uppercase tracking-wide py-3 px-8 text-xs bg-gray-900 text-white hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors">
            Go to Homepage
          </a>
        </div>

      @elseif ($gift->status === 'pending')

        {{-- Payment not confirmed yet --}}
        <div class="text-center">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 mb-6">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Gift not ready yet</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
            This gift is still being processed. Please check back shortly or ask the sender if payment was completed.
          </p>
        </div>

      @else

        {{-- Valid gift — ready to claim --}}
        <div class="text-center mb-10">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-accent/10 mb-6">
            <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112.83 0L12 8zm0 0V6a2 2 0 10-2.83 0L12 8zm-7 4h14M5 12a2 2 0 01-2-2V6h3.5m10 6a2 2 0 002-2V6h-3.5"/>
            </svg>
          </div>
          <p class="text-xs font-bold uppercase tracking-widest text-accent mb-2">You've been gifted</p>
          <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">
            {{ $gift->package->name }} Subscription
          </h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            A gift from <strong class="text-gray-700 dark:text-gray-300">{{ $gift->sender->name }}</strong>
          </p>
        </div>

        @if ($gift->message)
          <div class="border-l-[3px] border-accent bg-accent/5 dark:bg-accent/10 px-5 py-4 mb-8">
            <p class="text-sm italic text-gray-700 dark:text-gray-300 leading-relaxed">"{{ $gift->message }}"</p>
            <p class="mt-2 text-xs font-semibold text-gray-400">— {{ $gift->sender->name }}</p>
          </div>
        @endif

        {{-- Package details --}}
        <div class="border border-gray-200 dark:border-gray-600 p-6 mb-8">
          <ul class="space-y-3">
            <li class="flex items-center gap-x-2 text-sm text-gray-900 dark:text-white font-medium">
              <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
              </svg>
              {{ $gift->package->max_resolution }} Quality
            </li>
            <li class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray-300">
              <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
              </svg>
              {{ $gift->package->max_screens }} {{ Str::plural('Screen', $gift->package->max_screens) }}
            </li>
            @if (!$gift->package->has_ads)
              <li class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray-300">
                <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
                No Ads
              </li>
            @endif
            <li class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray-300">
              <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
              </svg>
              1 {{ ucfirst($gift->package->interval) }} access
            </li>
          </ul>
        </div>

        @auth
          {{-- Logged in — claim directly --}}
          @if (session('error'))
            <div class="mb-6 border border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700 px-5 py-3 text-sm text-red-600 dark:text-red-400">
              {{ session('error') }}
            </div>
          @endif

          <form method="POST" action="{{ route('gift.claim', $gift->token) }}"
            x-data="{ loading: false }" @submit="loading = true">
            @csrf
            <button type="submit" :disabled="loading"
              class="w-full flex items-center justify-center gap-x-2 font-bold uppercase tracking-wide py-4 text-sm bg-accent text-gray-900 hover:bg-accent/90 transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
              <svg x-show="loading" class="animate-spin h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <span x-text="loading ? 'Claiming…' : 'Claim Gift & Start Watching'"></span>
            </button>
          </form>

          <p class="mt-3 text-center text-xs text-gray-400">
            Logged in as <strong class="text-gray-600 dark:text-gray-300">{{ auth()->user()->email }}</strong>.
            Not you?
            <a href="{{ route('logout') }}" class="underline hover:text-gray-900 dark:hover:text-white"
              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</a>
          </p>
          <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>

        @else
          {{-- Guest — prompt to log in or register --}}
          <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-6">
            Sign in or create a free account to claim your gift.
          </p>
          <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('login') }}"
              class="flex-1 inline-flex justify-center font-bold uppercase tracking-wide py-3 px-6 text-sm bg-gray-900 hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors">
              Sign In
            </a>
            <a href="{{ route('register') }}"
              class="flex-1 inline-flex justify-center font-bold uppercase tracking-wide py-3 px-6 text-sm border border-gray-900 dark:border-white text-gray-900 dark:text-white hover:bg-gray-900 hover:text-white dark:hover:bg-white dark:hover:text-gray-900 transition-colors">
              Create Account
            </a>
          </div>
        @endauth

        @if ($gift->expires_at)
          <p class="mt-6 text-center text-[11px] text-gray-400">
            This gift expires on {{ $gift->expires_at->format('F j, Y') }}.
          </p>
        @endif

      @endif

    </div>
  </div>
</main>
@endsection
