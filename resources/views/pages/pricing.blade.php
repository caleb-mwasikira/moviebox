@extends('layouts.app')

@section('title', 'Pricing')

@section('content')
<div class="container-app max-w-6xl py-14 lg:py-20">

  <div class="mx-auto mb-14 max-w-2xl text-center">
    @if ($hasEverSubscribed)
      <span class="mx-auto mb-6 grid size-16 place-items-center rounded-2xl bg-red-500/10 text-red-500"><x-ui.icon name="alert" class="size-8" /></span>
      <h1 class="text-4xl font-extrabold text-fg">Your subscription has ended</h1>
      <p class="mt-4 text-muted">Renew your plan to continue watching movies and series without interruption.</p>
    @else
      <p class="eyebrow mb-3">Plans &amp; pricing</p>
      <h1 class="text-4xl font-extrabold text-fg sm:text-5xl">Stream without <span class="text-gradient">limits</span></h1>
      <p class="mt-4 text-muted">Pick a plan and start watching today. Cancel or switch at any time.</p>
    @endif
  </div>

  @if ($packages->isEmpty())
    <x-ui.empty icon="package" title="No plans available right now" text="Please check back shortly or get in touch with us.">
      <a href="{{ route('contact') }}" class="btn btn-primary">Contact us</a>
    </x-ui.empty>
  @else
    <div class="grid items-stretch gap-6 md:grid-cols-2 lg:grid-cols-3">
      @foreach ($packages as $package)
        @php
          $isCurrent = $currentPackage && $currentPackage->id === $package->id;
          $popular = !$isCurrent && $packages->count() >= 3 && $loop->index === intdiv($packages->count(), 2);
        @endphp
        <div class="card relative flex flex-col p-7 transition hover:-translate-y-1 {{ $isCurrent ? 'ring-2 ring-brand' : ($popular ? 'ring-2 ring-brand/50' : '') }}">
          @if ($isCurrent)
            <span class="badge badge-brand absolute right-5 top-5">Current plan</span>
          @elseif ($popular)
            <span class="badge bg-brand text-white absolute right-5 top-5">Most popular</span>
          @endif

          <h3 class="text-sm font-semibold uppercase tracking-widest text-muted">{{ $package->name }}</h3>
          <p class="mt-4 flex items-baseline gap-1">
            <span class="text-sm font-semibold text-muted">{{ $package->currency }}</span>
            <span class="text-5xl font-extrabold text-fg">{{ number_format($package->price, 0) }}</span>
            <span class="text-sm text-muted">/{{ $package->interval }}</span>
          </p>
          @if ($package->description)<p class="mt-3 text-sm leading-relaxed text-muted">{{ $package->description }}</p>@endif

          <ul class="my-7 flex-1 space-y-3 border-t border-line pt-7 text-sm">
            @php
              $features = [
                [true, $package->max_resolution . ' quality'],
                [true, $package->max_screens . ' ' . Str::plural('screen', $package->max_screens)],
                [!$package->has_ads, 'No ads'],
                [$package->download_enabled, 'Offline downloads'],
                [$package->watch_party_enabled, 'Watch party'],
              ];
            @endphp
            @foreach ($features as [$on, $text])
              @if ($on)
                <li class="flex items-center gap-3 text-fg-soft"><span class="grid size-5 shrink-0 place-items-center rounded-full bg-brand-soft text-brand"><x-ui.icon name="check" class="size-3" /></span>{{ $text }}</li>
              @endif
            @endforeach
          </ul>

          @if ($isCurrent)
            <span class="btn btn-secondary btn-block pointer-events-none opacity-60">Active plan</span>
          @else
            <a href="{{ route('payment.show', $package->uuid) }}" class="btn {{ $popular ? 'btn-primary' : 'btn-secondary' }} btn-block">
              @auth {{ $currentPackage ? 'Switch plan' : 'Get this plan' }} @else Get started @endauth
            </a>
          @endif

          @auth
            @if ($package->is_giftable)
              <a href="{{ route('gift.show', $package->uuid) }}" class="btn btn-ghost btn-sm btn-block mt-2"><x-ui.icon name="gift" class="size-4" /> Gift this plan</a>
            @endif
          @endauth
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
