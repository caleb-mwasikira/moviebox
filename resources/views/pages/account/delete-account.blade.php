@extends('layouts.account')

@section('account-title', 'Delete Account')

@section('account-content')
<div class="card">
  <div class="card-header"><div><h2 class="card-title">Delete account</h2><p class="mt-0.5 text-sm text-muted">Permanently delete your account and all associated data. This action cannot be undone.</p></div></div>

  <div class="p-5 sm:p-6">
    @if (auth()->user()->isAdmin())
      <div class="alert alert-warning"><x-ui.icon name="alert" class="mt-0.5 size-5 shrink-0" />Admin accounts cannot be self-deleted. Contact another administrator to remove this account.</div>
    @else
      <div class="alert alert-error mb-6 !block">
        <p class="mb-1 font-semibold">What will be deleted</p>
        <ul class="list-inside list-disc space-y-0.5 font-normal">
          <li>Your account and login credentials</li>
          <li>All profiles on this account</li>
          <li>Watch history and watchlist</li>
          <li>Active subscription (no refund)</li>
        </ul>
      </div>

      @if (session('error'))
        <div class="alert alert-error mb-6"><x-ui.icon name="alert" class="mt-0.5 size-5 shrink-0" />{{ session('error') }}</div>
      @endif

      <form method="POST" action="{{ route('account.delete-account.destroy') }}" class="max-w-lg space-y-5"
        onsubmit="return confirm('Are you absolutely sure? This will permanently delete your account.')">
        @csrf
        @method('DELETE')
        <x-ui.field name="password" type="password" label="Confirm your password" placeholder="Enter your password to confirm" autocomplete="current-password" />
        <button type="submit" class="btn btn-danger"><x-ui.icon name="trash" class="size-4" /> Permanently delete my account</button>
      </form>
    @endif
  </div>
</div>
@endsection
