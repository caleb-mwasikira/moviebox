@extends('layouts.account')

@section('account-title', 'Personal Info')

@section('account-content')
<div class="card">
  <div class="card-header"><div><h2 class="card-title">Profile information</h2><p class="mt-0.5 text-sm text-muted">Update your name and email address.</p></div></div>
  <form method="POST" action="{{ route('account.personal-info.update') }}" class="max-w-lg space-y-5 p-5 sm:p-6">
    @csrf
    @method('PUT')
    <x-ui.field name="name" label="Full name" :value="old('name', auth()->user()->name)" autocomplete="name" required />
    <div>
      <x-ui.field name="email" type="email" label="Email address" :value="old('email', auth()->user()->email)" autocomplete="email" required />
      @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
        <p class="mt-2 text-xs text-muted">
          Your email address is unverified.
          <form method="POST" action="{{ route('verification.send') }}" class="inline">@csrf<button type="submit" class="font-semibold text-brand hover:underline">Resend verification email.</button></form>
        </p>
      @endif
    </div>
    <button type="submit" class="btn btn-primary">Save changes</button>
  </form>
</div>

<div class="card">
  <div class="card-header"><div><h2 class="card-title">Update password</h2><p class="mt-0.5 text-sm text-muted">Use a strong password to keep your account secure.</p></div></div>
  <form method="POST" action="{{ route('account.password.update') }}" class="max-w-lg space-y-5 p-5 sm:p-6">
    @csrf
    @method('PUT')
    <x-ui.field name="current_password" type="password" label="Current password" autocomplete="current-password" />
    <x-ui.field name="password" type="password" label="New password" autocomplete="new-password" />
    <x-ui.field name="password_confirmation" type="password" label="Confirm new password" autocomplete="new-password" />
    <button type="submit" class="btn btn-primary">Update password</button>
  </form>
</div>
@endsection
