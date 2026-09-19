@extends('layouts.auth')

@section('title', 'Verify email')

@section('content')
  <h1 class="text-3xl font-bold text-fg">Verify your email</h1>
  <p class="mt-2 text-sm text-muted">We sent a code to <span class="font-semibold text-fg">{{ $email }}</span>.</p>

  @if (session('status') === 'verification-sent')
    <div class="alert alert-success mt-6"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />A new verification code has been sent to your email.</div>
  @endif

  <form method="POST" action="{{ route('verification.verify') }}" class="mt-8 space-y-5" x-data="{ loading: false }" @submit="loading = true">
    @csrf
    <x-ui.field name="otp" label="6-digit code" :value="old('otp')" inputmode="numeric" pattern="\d{6}" maxlength="6" autocomplete="one-time-code" required autofocus
      class="text-center text-2xl font-bold tracking-[0.5em]" />
    <x-ui.submit loading="Verifying…">Verify email</x-ui.submit>
  </form>

  <div class="mt-8 space-y-3 text-center text-sm text-muted">
    <form method="POST" action="{{ route('verification.send') }}">
      @csrf
      Didn't receive a code? <button type="submit" class="font-semibold text-brand hover:underline">Resend code</button>
    </form>
    <p>Wrong account?
      <a href="{{ route('logout') }}" class="font-semibold text-fg-soft hover:text-brand"
        onclick="event.preventDefault(); document.getElementById('logout-form-verify').submit();">Sign out</a>
    </p>
    <form id="logout-form-verify" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
  </div>
@endsection
