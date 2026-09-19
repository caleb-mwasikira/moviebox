@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
  <h1 class="text-3xl font-bold text-fg">Welcome back</h1>
  <p class="mt-2 text-sm text-muted">Sign in to continue watching.</p>

  @if (session('status'))
    <div class="alert alert-info mt-6"><x-ui.icon name="info" class="mt-0.5 size-5 shrink-0" />{{ session('status') }}</div>
  @endif

  @if (session('banned'))
    <div class="alert alert-error mt-6"><x-ui.icon name="ban" class="mt-0.5 size-5 shrink-0" />{{ session('banned') }}</div>
  @endif

  <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" x-data="{ loading: false }" @submit="loading = true">
    @csrf
    <x-ui.field name="email" type="email" label="Email" :value="old('email')" autocomplete="email" required autofocus />
    <x-ui.field name="password" type="password" label="Password" autocomplete="current-password" required />

    <div class="flex flex-wrap items-center justify-between gap-2">
      <label class="flex cursor-pointer items-center gap-2 text-sm text-fg-soft"><input type="checkbox" name="remember" class="check"> Remember me</label>
      <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand hover:underline">Forgot password?</a>
    </div>

    <x-ui.submit loading="Signing in…">Sign in</x-ui.submit>
  </form>

  <x-ui.social-login />

  <p class="mt-8 text-center text-sm text-muted">
    Don't have an account? <a href="{{ route('register') }}" class="font-semibold text-brand hover:underline">Create one</a>
  </p>
@endsection
