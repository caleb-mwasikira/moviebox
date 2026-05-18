@extends('layouts.account')

@section('account-title', 'Billing Details')

@section('account-content')
  <div class="flex flex-col gap-y-8">

    {{-- Subscription Status --}}
    @php
      $user             = auth()->user();
      $userSubscription = $user->currentSubscription;
      $selectedPackage  = $userSubscription?->package;

      // Detect if the active profile belongs to a different account owner
      $activeProfile     = $user->activeProfile();
      $sharingAccount    = $activeProfile && $activeProfile->user_id !== $user->id
                             ? $activeProfile->user
                             : null;
      $ownerSubscription = $sharingAccount?->currentSubscription;
      $ownerPackage      = $ownerSubscription?->package;

      $paymentHistory = $user->userSubscriptions()
          ->with('package')
          ->orderByDesc('starts_at')
          ->get();

      $giftsSent = \App\Models\GiftSubscription::where('sender_id', $user->id)
          ->with('package', 'claimedBy')
          ->orderByDesc('created_at')
          ->get();
    @endphp

    <div class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700">
      <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Subscription</h2>
      <p class="text-sm text-gray-500 dark:text-gray mb-8">Your current plan and billing cycle.</p>

      <div class="flex items-center justify-between flex-wrap gap-4 border border-gray-200 dark:border-gray-600 p-6">
        @if($userSubscription && $selectedPackage)
          <div>
            <div class="text-xs font-bold uppercase text-gray-500 mb-1">Current Plan</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $selectedPackage->name }}</div>
            <div class="text-sm text-gray-500 dark:text-gray mt-1">
              @if($userSubscription->expires_at)
                Renews on {{ $userSubscription->expires_at->format('F j, Y') }}
              @else
                Lifetime Access
              @endif
            </div>
          </div>
          <span
            class="inline-flex items-center px-3 py-1 text-xs font-bold uppercase bg-accent/10 text-accent border border-accent">
            Active
          </span>
        @elseif ($sharingAccount)
          <div>
            <div class="text-xs font-bold uppercase text-gray-500 mb-1">Current Plan</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">
              {{ $ownerPackage?->name ?? 'Shared Access' }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray mt-1">
              Shared via <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $sharingAccount->name }}</span>'s account.
              Billing is managed by them.
              @if ($ownerSubscription?->expires_at)
                Access until {{ $ownerSubscription->expires_at->format('F j, Y') }}.
              @endif
            </div>
          </div>
          <span class="inline-flex items-center px-3 py-1 text-xs font-bold uppercase bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-900/20 dark:border-blue-700 dark:text-blue-400">
            Shared
          </span>
        @else
          <div>
            <div class="text-xs font-bold uppercase text-gray-500 mb-1">Current Plan</div>
            <div class="text-lg font-bold text-gray-900 dark:text-white">No active subscription</div>
            <div class="text-sm text-gray-500 dark:text-gray mt-1">Choose a plan below to start streaming.</div>
          </div>
          <span class="inline-flex items-center px-3 py-1 text-xs font-bold uppercase bg-gray-100 text-gray-500 border border-gray-200 dark:bg-gray-800 dark:border-gray-600">
            Inactive
          </span>
        @endif
      </div>
    </div>

    {{-- Change Plan / Pricing Tiers — hidden for shared-account users --}}
    @if (! $sharingAccount)
    <div class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700">
      <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Change Plan</h2>
      <p class="text-sm text-gray-500 dark:text-gray mb-8">Upgrade or downgrade your streaming experience.</p>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($packages as $package)
          @php
            $isCurrent = $package->name === $selectedPackage?->name;
          @endphp

          <div
            class="flex flex-col border {{ $isCurrent ? 'border-accent' : 'border-gray-200 dark:border-gray-600' }} p-6 transition-all duration-150">
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-xs font-bold uppercase text-gray-500 mb-1">{{ $package->name }}</h3>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ $package->currency }} {{ number_format($package->price, 2) }}<span
                    class="text-xs font-medium text-gray-500">/{{ $package->interval }}</span>
                </div>
              </div>
              @if($isCurrent)
                <span class="text-[10px] font-bold uppercase py-1 px-2 bg-accent text-gray-900">Current</span>
              @endif
            </div>

            <ul class="flex-grow space-y-3 mb-8">
              <li class="flex items-center gap-x-2 text-sm text-gray-900 dark:text-white font-medium">
                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ $package->max_resolution }} Quality
              </li>
              <li class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray">
                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ $package->max_screens }} {{ Str::plural('Screen', $package->max_screens) }}
              </li>
              @if(!$package->has_ads)
                <li class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray">
                  <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                  </svg>
                  No Ads
                </li>
              @endif
              @if ($package->download_enabled)
              <li class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray">
                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
                Download your movies anytime
              </li>
              @endif
              @if ($package->watch_party_enabled)
              <li class="flex items-center gap-x-2 text-sm text-gray-600 dark:text-gray">
                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
                Watch Party
              </li>
              @endif
            </ul>

            @if ($isCurrent)
              <span class="w-full inline-flex text-center font-bold leading-none uppercase justify-center py-3 px-4 text-xs bg-gray-200 text-gray-400 cursor-not-allowed dark:bg-gray-600">
                Active Plan
              </span>
            @else
              <a href="{{ route('payment.show', $package->uuid) }}"
                class="w-full inline-flex text-center font-bold leading-none transition-colors uppercase justify-center py-3 px-4 text-xs bg-gray-900 hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent">
                Switch Plan
              </a>
            @endif
            @if ($package->is_giftable)
            <a href="{{ route('gift.show', $package->uuid) }}"
              class="mt-2 w-full inline-flex justify-center gap-x-1.5 font-bold uppercase py-2 px-4 text-xs border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-accent hover:text-accent transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112.83 0L12 8zm0 0V6a2 2 0 10-2.83 0L12 8zm-7 4h14M5 12a2 2 0 01-2-2V6h3.5m10 6a2 2 0 002-2V6h-3.5"/>
              </svg>
              Gift
            </a>
            @endif
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Payment History — own accounts only --}}
    @if (! $sharingAccount)
    <div class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700">
      <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Payment History</h2>
      <p class="text-sm text-gray-500 dark:text-gray mb-8">All subscription cycles on your account.</p>

      @if ($paymentHistory->isEmpty())
        <div class="py-12 text-center border border-dashed border-gray-200 dark:border-gray-600">
          <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No payment history yet</p>
        </div>
      @else
        <div class="border border-gray-200 dark:border-gray-600 divide-y divide-gray-100 dark:divide-gray-700">

          <div class="hidden sm:grid grid-cols-12 px-5 py-3 bg-gray-50 dark:bg-gray-800">
            <span class="col-span-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Plan</span>
            <span class="col-span-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">Amount</span>
            <span class="col-span-3 text-[10px] font-bold uppercase tracking-widest text-gray-400">Period</span>
            <span class="col-span-2 text-[10px] font-bold uppercase tracking-widest text-gray-400 text-right">Status</span>
          </div>

          @foreach ($paymentHistory as $sub)
            @php $pkg = $sub->package; @endphp
            <div class="grid grid-cols-2 sm:grid-cols-12 items-start gap-y-1 gap-x-4 px-5 py-4">

              <div class="col-span-2 sm:col-span-4">
                <p class="font-bold text-sm text-gray-900 dark:text-white">{{ $pkg?->name ?? 'Unknown Plan' }}</p>
                @if ($pkg?->interval)
                  <p class="text-xs text-gray-400 mt-0.5">{{ ucfirst($pkg->interval) }}</p>
                @endif
              </div>

              <div class="sm:col-span-3">
                <p class="font-bold text-sm text-gray-900 dark:text-white">
                  {{ $pkg ? $pkg->currency . ' ' . number_format($pkg->price, 2) : '—' }}
                </p>
              </div>

              <div class="sm:col-span-3">
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $sub->starts_at->format('M j, Y') }}</p>
                @if ($sub->expires_at)
                  <p class="text-xs text-gray-400 mt-0.5">→ {{ $sub->expires_at->format('M j, Y') }}</p>
                @else
                  <p class="text-xs text-gray-400 mt-0.5">Lifetime</p>
                @endif
              </div>

              <div class="sm:col-span-2 sm:text-right">
                @if ($sub->status === 'active')
                  <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-accent/10 text-accent border border-accent">Active</span>
                @else
                  <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-gray-100 text-gray-400 border border-gray-200 dark:bg-gray-800 dark:border-gray-600">Expired</span>
                @endif
              </div>

            </div>
          @endforeach

        </div>
      @endif
    </div>
    @endif

    {{-- Gifts Sent — own accounts only --}}
    @if (! $sharingAccount && $giftsSent->isNotEmpty())
    <div class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700">
      <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Gifts Sent</h2>
      <p class="text-sm text-gray-500 dark:text-gray mb-8">Subscriptions you've gifted to others.</p>

      <div class="border border-gray-200 dark:border-gray-600 divide-y divide-gray-100 dark:divide-gray-700">

        <div class="hidden sm:grid grid-cols-12 px-5 py-3 bg-gray-50 dark:bg-gray-800">
          <span class="col-span-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Plan</span>
          <span class="col-span-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">Recipient</span>
          <span class="col-span-4 text-[10px] font-bold uppercase tracking-widest text-gray-400 text-right">Status</span>
        </div>

        @foreach ($giftsSent as $gift)
          <div class="grid grid-cols-2 sm:grid-cols-12 items-start gap-y-1 gap-x-4 px-5 py-4">

            <div class="col-span-2 sm:col-span-4">
              <p class="font-bold text-sm text-gray-900 dark:text-white">{{ $gift->package?->name ?? 'Unknown Plan' }}</p>
              <p class="text-xs text-gray-400 mt-0.5">{{ $gift->created_at->format('M j, Y') }}</p>
            </div>

            <div class="sm:col-span-4">
              <p class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ $gift->recipient_email }}</p>
              @if ($gift->status === 'claimed' && $gift->claimedBy)
                <p class="text-xs text-gray-400 mt-0.5">by {{ $gift->claimedBy->name }}</p>
              @endif
            </div>

            <div class="sm:col-span-4 sm:text-right">
              @if ($gift->status === 'claimed')
                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-accent/10 text-accent border border-accent">Claimed</span>
              @elseif ($gift->status === 'sent' && ! $gift->isExpired())
                <div class="inline-flex flex-col items-end gap-y-1">
                  <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-900/20 dark:border-blue-700 dark:text-blue-400">Pending Claim</span>
                  <a href="{{ route('gift.sent', $gift->token) }}" class="text-[10px] text-gray-400 hover:text-accent transition-colors underline">View / Edit</a>
                </div>
              @elseif ($gift->status === 'sent' && $gift->isExpired())
                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-gray-100 text-gray-400 border border-gray-200 dark:bg-gray-800 dark:border-gray-600">Expired</span>
              @else
                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-gray-100 text-gray-400 border border-gray-200 dark:bg-gray-800 dark:border-gray-600">Pending Payment</span>
              @endif
            </div>

          </div>
        @endforeach

      </div>
    </div>
    @endif

  </div>
@endsection