@extends('layouts.account')

@section('account-title', 'Billing Details')

@section('account-content')
@php
  $user             = auth()->user();
  $userSubscription = $user->currentSubscription;
  $selectedPackage  = $userSubscription?->package;
  // Detect if the active profile belongs to a different account owner
  $activeProfile     = $user->activeProfile();
  $sharingAccount    = $activeProfile && $activeProfile->user_id !== $user->id ? $activeProfile->user : null;
  $ownerSubscription = $sharingAccount?->currentSubscription;
  $ownerPackage      = $ownerSubscription?->package;

  $paymentHistory = $user->userSubscriptions()->with('package')->orderByDesc('starts_at')->get();
  $giftsSent = \App\Models\GiftSubscription::where('sender_id', $user->id)
      ->with('package', 'claimedBy')->orderByDesc('created_at')->get();

  // Detect if the active subscription came from a claimed gift
  $claimedGift = null;
  if ($userSubscription && $selectedPackage) {
      $claimedGift = \App\Models\GiftSubscription::where('claimed_by_id', $user->id)
          ->where('status', 'claimed')
          ->where('subscription_package_id', $selectedPackage->id)
          ->with('sender')->latest('claimed_at')->first();
  }
@endphp

{{-- Subscription --}}
<div class="card">
  <div class="card-header"><div><h2 class="card-title">Subscription</h2><p class="mt-0.5 text-sm text-muted">Your current plan and billing cycle.</p></div></div>
  <div class="p-5 sm:p-6">
    <div class="panel flex flex-wrap items-center justify-between gap-4 p-5">
      @if ($userSubscription && $selectedPackage)
        <div class="flex items-center gap-4">
          <span class="grid size-12 place-items-center rounded-xl bg-brand-soft text-brand"><x-ui.icon name="crown" class="size-6" /></span>
          <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-muted">Current plan</p>
            <div class="flex flex-wrap items-center gap-2">
              <span class="text-lg font-bold text-fg">{{ $selectedPackage->name }}</span>
              @if ($claimedGift)<span class="badge bg-pink-500/12 text-pink-500"><x-ui.icon name="gift" class="size-3" /> Gift</span>@endif
            </div>
            <p class="text-sm text-muted">
              @if ($userSubscription->expires_at) Renews on {{ $userSubscription->expires_at->format('F j, Y') }} @else Lifetime access @endif
            </p>
            @if ($claimedGift?->sender)<p class="mt-0.5 text-xs text-muted">Gifted by <span class="font-semibold text-fg-soft">{{ $claimedGift->sender->name }}</span></p>@endif
          </div>
        </div>
        <span class="badge badge-success">Active</span>
      @elseif ($sharingAccount)
        <div>
          <p class="text-xs font-semibold uppercase tracking-wider text-muted">Current plan</p>
          <p class="text-lg font-bold text-fg">{{ $ownerPackage?->name ?? 'Shared access' }}</p>
          <p class="text-sm text-muted">
            Shared via <span class="font-semibold text-fg-soft">{{ $sharingAccount->name }}</span>'s account. Billing is managed by them.
            @if ($ownerSubscription?->expires_at) Access until {{ $ownerSubscription->expires_at->format('F j, Y') }}. @endif
          </p>
        </div>
        <span class="badge badge-info">Shared</span>
      @else
        <div>
          <p class="text-xs font-semibold uppercase tracking-wider text-muted">Current plan</p>
          <p class="text-lg font-bold text-fg">No active subscription</p>
          <p class="text-sm text-muted">Choose a plan below to start streaming.</p>
        </div>
        <span class="badge badge-neutral">Inactive</span>
      @endif
    </div>
  </div>
</div>

{{-- Change plan --}}
@if (! $sharingAccount)
  <div class="card">
    <div class="card-header"><div><h2 class="card-title">Change plan</h2><p class="mt-0.5 text-sm text-muted">Upgrade or downgrade your streaming experience.</p></div></div>
    <div class="grid gap-4 p-5 sm:p-6 md:grid-cols-2 xl:grid-cols-3">
      @foreach ($packages as $package)
        @php $isCurrent = $package->name === $selectedPackage?->name; @endphp
        <div class="flex flex-col rounded-2xl border p-5 {{ $isCurrent ? 'border-brand bg-brand-soft/40' : 'border-line' }}">
          <div class="mb-4 flex items-start justify-between gap-2">
            <div>
              <h3 class="text-xs font-semibold uppercase tracking-wider text-muted">{{ $package->name }}</h3>
              <p class="mt-1 text-2xl font-bold text-fg">{{ $package->currency }} {{ number_format($package->price, 2) }}<span class="text-xs font-medium text-muted">/{{ $package->interval }}</span></p>
            </div>
            @if ($isCurrent)<span class="badge badge-brand">Current</span>@endif
          </div>
          <ul class="mb-6 flex-1 space-y-2.5 text-sm text-fg-soft">
            @foreach ([
              [true, $package->max_resolution . ' quality'],
              [true, $package->max_screens . ' ' . Str::plural('screen', $package->max_screens)],
              [!$package->has_ads, 'No ads'],
              [$package->download_enabled, 'Download your movies anytime'],
              [$package->watch_party_enabled, 'Watch party'],
            ] as [$on, $text])
              @if ($on)<li class="flex items-center gap-2.5"><x-ui.icon name="check" class="size-4 shrink-0 text-brand" />{{ $text }}</li>@endif
            @endforeach
          </ul>
          @if ($isCurrent)
            <span class="btn btn-secondary btn-block pointer-events-none opacity-60">Active plan</span>
          @else
            <a href="{{ route('payment.show', $package->uuid) }}" class="btn btn-primary btn-block">Switch plan</a>
          @endif
          @if ($package->is_giftable)
            <a href="{{ route('gift.show', $package->uuid) }}" class="btn btn-ghost btn-sm btn-block mt-2"><x-ui.icon name="gift" class="size-4" /> Gift</a>
          @endif
        </div>
      @endforeach
    </div>
  </div>

  {{-- Payment history --}}
  <div class="card">
    <div class="card-header"><div><h2 class="card-title">Payment history</h2><p class="mt-0.5 text-sm text-muted">All subscription cycles on your account.</p></div></div>
    @if ($paymentHistory->isEmpty())
      <div class="p-6"><x-ui.empty icon="wallet" title="No payment history yet" /></div>
    @else
      <div class="overflow-x-auto">
        <table class="table">
          <thead><tr><th>Plan</th><th>Amount</th><th>Period</th><th class="text-right">Status</th></tr></thead>
          <tbody>
            @foreach ($paymentHistory as $sub)
              @php $pkg = $sub->package; @endphp
              <tr>
                <td><p class="font-semibold text-fg">{{ $pkg?->name ?? 'Unknown plan' }}</p>@if ($pkg?->interval)<p class="text-xs text-muted">{{ ucfirst($pkg->interval) }}</p>@endif</td>
                <td class="font-semibold text-fg">{{ $pkg ? $pkg->currency . ' ' . number_format($pkg->price, 2) : '—' }}</td>
                <td class="whitespace-nowrap">{{ $sub->starts_at->format('M j, Y') }} <span class="text-muted">→ {{ $sub->expires_at ? $sub->expires_at->format('M j, Y') : 'Lifetime' }}</span></td>
                <td class="text-right">@if ($sub->status === 'active')<span class="badge badge-success">Active</span>@else<span class="badge badge-neutral">Expired</span>@endif</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- Gifts sent --}}
  @if ($giftsSent->isNotEmpty())
    <div class="card">
      <div class="card-header"><div><h2 class="card-title">Gifts sent</h2><p class="mt-0.5 text-sm text-muted">Subscriptions you've gifted to others.</p></div></div>
      <div class="overflow-x-auto">
        <table class="table">
          <thead><tr><th>Plan</th><th>Recipient</th><th class="text-right">Status</th></tr></thead>
          <tbody>
            @foreach ($giftsSent as $gift)
              <tr>
                <td><p class="font-semibold text-fg">{{ $gift->package?->name ?? 'Unknown plan' }}</p><p class="text-xs text-muted">{{ $gift->created_at->format('M j, Y') }}</p></td>
                <td><p class="truncate">{{ $gift->recipient_email }}</p>@if ($gift->status === 'claimed' && $gift->claimedBy)<p class="text-xs text-muted">by {{ $gift->claimedBy->name }}</p>@endif</td>
                <td class="text-right">
                  @if ($gift->status === 'claimed')<span class="badge badge-success">Claimed</span>
                  @elseif ($gift->status === 'sent' && ! $gift->isExpired())
                    <span class="badge badge-info">Sent</span>
                    <a href="{{ route('gift.sent', $gift->token) }}" class="ml-2 text-xs font-semibold text-brand hover:underline">View / edit</a>
                  @elseif ($gift->status === 'sent' && $gift->isExpired())<span class="badge badge-neutral">Expired</span>
                  @else<span class="badge badge-warning">Pending payment</span>@endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
@endif
@endsection
