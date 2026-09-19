@extends('layouts.auth')

@section('title', 'Reset password')

@section('content')
  <h1 class="text-3xl font-bold text-fg">Reset your password</h1>
  <p class="mt-2 text-sm text-muted">Enter your email and we'll send you a 6-digit code.</p>

  @if (session('status'))
    <div class="alert alert-success mt-6"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5" x-data="{ loading: false }" @submit="loading = true">
    @csrf
    <x-ui.field name="email" type="email" label="Email address" :value="old('email')" autocomplete="email" required autofocus />
    <x-ui.submit loading="Sending…">Send verification code</x-ui.submit>
  </form>

  <p class="mt-8 text-center text-sm text-muted">
    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 font-semibold text-brand hover:underline"><x-ui.icon name="arrow-left" class="size-4" /> Back to sign in</a>
  </p>
@endsection
