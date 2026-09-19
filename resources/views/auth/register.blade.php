@extends('layouts.auth')

@section('title', 'Create account')

@section('content')
  <h1 class="text-3xl font-bold text-fg">Create your account</h1>
  <p class="mt-2 text-sm text-muted">Free to join. Cancel anytime.</p>

  <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5" x-data="{ loading: false }" @submit="loading = true">
    @csrf
    <x-ui.field name="name" label="Name" :value="old('name')" autocomplete="name" required autofocus />
    <x-ui.field name="email" type="email" label="Email" :value="old('email')" autocomplete="email" required />
    <div class="grid gap-5 sm:grid-cols-2">
      <x-ui.field name="password" type="password" label="Password" autocomplete="new-password" required />
      <x-ui.field name="password_confirmation" type="password" label="Confirm password" autocomplete="new-password" required />
    </div>
    <x-ui.submit loading="Creating account…">Create account</x-ui.submit>
  </form>

  <x-ui.social-login />

  <p class="mt-8 text-center text-sm text-muted">
    Already have an account? <a href="{{ route('login') }}" class="font-semibold text-brand hover:underline">Sign in</a>
  </p>
@endsection
