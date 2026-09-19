@extends('layouts.auth')

@section('title', 'Enter code')

@section('content')
  <h1 class="text-3xl font-bold text-fg">Enter verification code</h1>
  <p class="mt-2 text-sm text-muted">
    @if ($email)
      We sent a code to <span class="font-semibold text-fg">{{ $email }}</span>.
    @else
      Enter the 6-digit code from your email.
    @endif
  </p>

  <form method="POST" action="{{ route('password.verify-otp.store') }}" class="mt-8 space-y-5">
    @csrf
    <input type="hidden" name="email" value="{{ old('email', $email) }}">
    <x-ui.field name="otp" label="6-digit code" :value="old('otp')" inputmode="numeric" pattern="\d{6}" maxlength="6" autocomplete="one-time-code" required autofocus
      class="text-center text-2xl font-bold tracking-[0.5em]" />
    <button type="submit" class="btn btn-primary btn-lg btn-block">Verify code</button>
  </form>

  <p class="mt-8 text-center text-sm text-muted">
    Didn't get it? <a href="{{ route('password.request') }}" class="font-semibold text-brand hover:underline">Request a new one</a>
  </p>
@endsection
