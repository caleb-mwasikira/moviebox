@extends('layouts.app')

@section('title', 'Accept Your Gift')

@section('content')
<div class="container-app max-w-lg py-14 lg:py-20">
  @if ($gift->isClaimed())
    <div class="text-center">
      <span class="mx-auto mb-6 grid size-16 place-items-center rounded-2xl bg-surface-2 text-muted"><x-ui.icon name="check-circle" class="size-8" /></span>
      <h1 class="text-2xl font-bold text-fg">Gift already claimed</h1>
      <p class="mb-8 mt-2 text-sm text-muted">This gift subscription has already been redeemed. Each gift can only be claimed once.</p>
      <a href="{{ route('home') }}" class="btn btn-primary">Go to homepage</a>
    </div>
  @elseif ($gift->isExpired())
    <div class="text-center">
      <span class="mx-auto mb-6 grid size-16 place-items-center rounded-2xl bg-surface-2 text-muted"><x-ui.icon name="clock" class="size-8" /></span>
      <h1 class="text-2xl font-bold text-fg">Gift link expired</h1>
      <p class="mb-8 mt-2 text-sm text-muted">This gift link expired on {{ $gift->expires_at->format('F j, Y') }}. Ask the sender to resend it.</p>
      <a href="{{ route('home') }}" class="btn btn-primary">Go to homepage</a>
    </div>
  @elseif ($gift->status === 'pending')
    <div class="text-center">
      <span class="mx-auto mb-6 grid size-16 place-items-center rounded-2xl bg-surface-2 text-muted"><x-ui.icon name="clock" class="size-8" /></span>
      <h1 class="text-2xl font-bold text-fg">Gift not ready yet</h1>
      <p class="mb-8 mt-2 text-sm text-muted">This gift is still being processed. Please check back shortly or ask the sender if payment was completed.</p>
    </div>
  @else
    <div class="mb-8 text-center">
      <span class="mx-auto mb-6 grid size-16 place-items-center rounded-2xl bg-brand-soft text-brand"><x-ui.icon name="gift" class="size-8" /></span>
      <p class="eyebrow mb-2">You've been gifted</p>
      <h1 class="text-3xl font-extrabold text-fg">{{ $gift->package->name }} subscription</h1>
      <p class="mt-2 text-sm text-muted">A gift from <strong class="text-fg">{{ $gift->sender->name }}</strong></p>
    </div>

    @if ($gift->message)
      <blockquote class="mb-6 rounded-r-xl border-l-4 border-brand bg-brand-soft px-5 py-4">
        <p class="text-sm italic leading-relaxed text-fg-soft">"{{ $gift->message }}"</p>
        <p class="mt-2 text-xs font-semibold text-muted">— {{ $gift->sender->name }}</p>
      </blockquote>
    @endif

    <div class="card card-pad mb-6">
      <ul class="space-y-3 text-sm text-fg-soft">
        @foreach ([
          [true, $gift->package->max_resolution . ' quality'],
          [true, $gift->package->max_screens . ' ' . Str::plural('screen', $gift->package->max_screens)],
          [!$gift->package->has_ads, 'No ads'],
          [true, '1 ' . ucfirst($gift->package->interval) . ' access'],
        ] as [$on, $text])
          @if ($on)<li class="flex items-center gap-3"><span class="grid size-5 place-items-center rounded-full bg-brand-soft text-brand"><x-ui.icon name="check" class="size-3" /></span>{{ $text }}</li>@endif
        @endforeach
      </ul>
    </div>

    @auth
      @if (session('error'))
        <div class="alert alert-error mb-6"><x-ui.icon name="alert" class="mt-0.5 size-5 shrink-0" />{{ session('error') }}</div>
      @endif
      <form method="POST" action="{{ route('gift.claim', $gift->token) }}" x-data="{ loading: false }" @submit="loading = true">
        @csrf
        <x-ui.submit loading="Claiming…">Claim gift &amp; start watching</x-ui.submit>
      </form>
      <p class="mt-3 text-center text-xs text-muted">
        Logged in as <strong class="text-fg-soft">{{ auth()->user()->email }}</strong>. Not you?
        <a href="{{ route('logout') }}" class="underline hover:text-fg" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</a>
      </p>
      <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
    @else
      <p class="mb-5 text-center text-sm text-muted">Sign in or create a free account to claim your gift.</p>
      <div class="flex flex-col gap-3 sm:flex-row">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg flex-1">Sign in</a>
        <a href="{{ route('register') }}" class="btn btn-secondary btn-lg flex-1">Create account</a>
      </div>
    @endauth

    @if ($gift->expires_at)
      <p class="mt-6 text-center text-xs text-muted">This gift expires on {{ $gift->expires_at->format('F j, Y') }}.</p>
    @endif
  @endif
</div>
@endsection
