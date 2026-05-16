@extends('layouts.app')

@section('title', 'Subscription Ended')

@section('content')
  <main class="grow">
    <div class="py-16 lg:py-24">
      <div class="container max-w-5xl">

        {{-- Header --}}
        <div class="text-center mb-12">
          @if ($hasEverSubscribed)
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-500/10 mb-6">
              <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
              </svg>
            </div>
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-3">Your subscription has ended</h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
              Renew your plan to continue watching movies and series without interruption.
            </p>
          @else
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-accent/10 mb-6">
              <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-3">Choose a plan to get started</h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
              Pick a plan and start watching movies and series today.
            </p>
          @endif
        </div>

        @if (session('success'))
          <div class="mb-8 flex items-center gap-x-3 bg-accent/10 border border-accent p-4">
            <svg class="w-5 h-5 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ session('success') }}</span>
          </div>
        @endif

        @if (session('error'))
          <div class="mb-8 flex items-center gap-x-3 bg-red-500/10 border border-red-500 p-4">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ session('error') }}</span>
          </div>
        @endif

        {{-- Plans --}}
        @if ($packages->isNotEmpty())
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($packages as $package)
              <div class="flex flex-col border border-gray-200 dark:border-gray-600 p-6">
                <div class="mb-4">
                  <h3 class="text-xs font-bold uppercase text-gray-500 mb-1">{{ $package->name }}</h3>
                  <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $package->currency }} {{ number_format($package->price, 2) }}<span
                      class="text-xs font-medium text-gray-500">/{{ $package->interval }}</span>
                  </div>
                  @if ($package->description)
                    <p class="text-xs text-gray-500 mt-2">{{ $package->description }}</p>
                  @endif
                </div>

                <ul class="flex-grow space-y-3 mb-8">
                  <li class="flex items-center gap-x-2 text-sm text-gray-900 dark:text-white font-medium">
                    <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $package->max_resolution }} Quality
                  </li>
                  <li class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $package->max_screens }} {{ Str::plural('Screen', $package->max_screens) }}
                  </li>
                  @if (!$package->has_ads)
                    <li class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray-300">
                      <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                      </svg>
                      No Ads
                    </li>
                  @endif
                  @if ($package->download_enabled)
                    <li class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray-300">
                      <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                      </svg>
                      Downloads Enabled
                    </li>
                  @endif
                </ul>

                <form action="{{ route('account.update-plan', $package->uuid) }}" method="POST">
                  @csrf
                  <button type="submit"
                    class="w-full inline-flex justify-center font-bold uppercase py-3 px-4 text-xs bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
                    {{ $hasEverSubscribed ? 'Renew' : 'Subscribe' }} — {{ $package->name }}
                  </button>
                </form>
              </div>
            @endforeach
          </div>
        @else
          <p class="text-center text-gray-500">No plans available at the moment. Please contact support.</p>
        @endif

        <p class="text-center text-xs text-gray-500 mt-8">
          Need help?
          <a href="{{ route('contact') }}" class="text-accent hover:underline">Contact us</a>
        </p>

      </div>
    </div>
  </main>
@endsection
