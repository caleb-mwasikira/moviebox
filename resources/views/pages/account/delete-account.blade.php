@extends('layouts.account')

@section('account-title', 'Delete Account')

@section('account-content')
<div class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700">

  <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Delete Account</h2>
  <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
    Permanently delete your account and all associated data. This action cannot be undone.
  </p>

  @if (auth()->user()->isAdmin())
    <div class="border border-orange-300 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/20 px-5 py-4 text-sm text-orange-700 dark:text-orange-300">
      Admin accounts cannot be self-deleted. Contact another administrator to remove this account.
    </div>
  @else

    <div class="border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 px-5 py-4 mb-8">
      <p class="text-sm font-bold text-red-700 dark:text-red-300 mb-1">What will be deleted</p>
      <ul class="text-sm text-red-600 dark:text-red-400 space-y-0.5 list-disc list-inside">
        <li>Your account and login credentials</li>
        <li>All profiles on this account</li>
        <li>Watch history and watchlist</li>
        <li>Active subscription (no refund)</li>
      </ul>
    </div>

    @if (session('error'))
      <div class="mb-6 border border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20 px-5 py-4 text-sm text-red-600 dark:text-red-400">
        {{ session('error') }}
      </div>
    @endif

    <form method="POST" action="{{ route('account.delete-account.destroy') }}"
      x-data="{ confirm: false }"
      onsubmit="return confirm('Are you absolutely sure? This will permanently delete your account.')">
      @csrf
      @method('DELETE')

      <div class="mb-6">
        <label for="password" class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">
          Confirm Your Password
        </label>
        <input type="password" id="password" name="password"
          autocomplete="current-password"
          placeholder="Enter your password to confirm"
          class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-red-400 outline-none transition-colors {{ $errors->has('password') ? '!border-red-400' : '' }}">
        @error('password')
          <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
        @enderror
      </div>

      <button type="submit"
        class="px-6 py-3 text-sm font-bold uppercase border border-red-400 text-red-600 dark:text-red-400 hover:bg-red-500 hover:text-white dark:hover:bg-red-500 dark:hover:text-white transition-colors">
        Permanently Delete My Account
      </button>
    </form>

  @endif

</div>
@endsection
