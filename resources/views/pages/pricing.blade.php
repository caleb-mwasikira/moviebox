@extends('layouts.app')

@section('title', 'Pricing')

@section('content')
<main class="grow">
  <div class="py-16 lg:py-24">
    <div class="container max-w-5xl">

      {{-- Header --}}
      <div class="text-center mb-14">
        @if ($hasEverSubscribed)
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-500/10 mb-6">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
          </div>
          <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">Your subscription has ended</h1>
          <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto text-sm leading-relaxed">
            Renew your plan to continue watching movies and series without interruption.
          </p>
        @else
          <p class="text-xs font-bold uppercase tracking-widest text-accent mb-3">Plans & Pricing</p>
          <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Stream without limits
          </h1>
          <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto text-sm leading-relaxed">
            Pick a plan and start watching movies and series today. Cancel or switch at any time.
          </p>
        @endif
      </div>

      @if ($packages->isEmpty())
        <div class="py-16 text-center border border-dashed border-gray-200 dark:border-gray-600">
          <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No plans available at the moment.</p>
          <a href="{{ route('contact') }}" class="mt-4 inline-block text-xs text-accent hover:underline">Contact us</a>
        </div>
      @else

        {{-- Plan cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
          @foreach ($packages as $package)
            @php $isCurrent = $currentPackage && $currentPackage->id === $package->id; @endphp
            <div class="flex flex-col border {{ $isCurrent ? 'border-accent' : 'border-gray-200 dark:border-gray-600' }} p-6 transition-all duration-150 relative">

              @if ($isCurrent)
                <span class="absolute top-4 right-4 text-[10px] font-bold uppercase py-1 px-2 bg-accent text-gray-900">Current</span>
              @endif

              <div class="mb-6">
                <h3 class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400 mb-1">{{ $package->name }}</h3>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">
                  {{ $package->currency }}&nbsp;{{ number_format($package->price, 0) }}<span class="text-xs font-medium text-gray-500">/{{ $package->interval }}</span>
                </div>
                @if ($package->description)
                  <p class="text-xs text-gray-500 mt-2 leading-relaxed">{{ $package->description }}</p>
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
                    Offline Downloads
                  </li>
                @endif
                @if ($package->watch_party_enabled)
                  <li class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4 text-accent shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                    Watch Party
                  </li>
                @endif
              </ul>

              @if ($isCurrent)
                <div class="w-full text-center font-bold uppercase py-3 px-4 text-xs bg-gray-100 text-gray-400 dark:bg-gray-600 dark:text-gray-400 cursor-not-allowed">
                  Active Plan
                </div>
              @else
                <a href="{{ route('payment.show', $package->uuid) }}"
                  class="w-full inline-flex justify-center font-bold uppercase py-3 px-4 text-xs bg-gray-900 hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors">
                  @auth {{ $currentPackage ? 'Switch Plan' : 'Get This Plan' }} @else Get Started @endauth
                </a>
              @endif
              @auth
                @if ($package->is_giftable)
                <a href="{{ route('gift.show', $package->uuid) }}"
                  class="mt-2 w-full inline-flex justify-center gap-x-1.5 font-bold uppercase py-2.5 px-4 text-xs border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-accent hover:text-accent transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112.83 0L12 8zm0 0V6a2 2 0 10-2.83 0L12 8zm-7 4h14M5 12a2 2 0 01-2-2V6h3.5m10 6a2 2 0 002-2V6h-3.5"/>
                  </svg>
                  Gift this plan
                </a>
                @endif
              @endauth

            </div>
          @endforeach
        </div>

      @endif
    </div>
  </div>
</main>
@endsection
