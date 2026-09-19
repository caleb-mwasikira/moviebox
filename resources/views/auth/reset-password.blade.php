@extends('layouts.auth')

@section('title', 'New password')

@section('content')
  <h1 class="text-3xl font-bold text-fg">Set a new password</h1>
  <p class="mt-2 text-sm text-muted">Your identity has been verified. Choose a new password below.</p>

  <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5" x-data="{ loading: false }" @submit="loading = true">
    @csrf
    <x-ui.field name="password" type="password" label="New password" autocomplete="new-password" required autofocus />
    <x-ui.field name="password_confirmation" type="password" label="Confirm new password" autocomplete="new-password" required />
    <x-ui.submit loading="Saving…">Reset password</x-ui.submit>
  </form>
@endsection
