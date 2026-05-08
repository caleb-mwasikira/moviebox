@extends('layouts.account')

@section('account-title', 'Personal Info')

@section('account-content')
<div class="flex flex-col gap-y-8">

  {{-- Profile Information --}}
  <div class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Profile Information</h2>
    <p class="text-sm text-gray-500 dark:text-gray mb-8">Update your name and email address.</p>

    <form method="POST" action="{{ route('account.personal-info.update') }}" class="flex flex-col gap-y-6 max-w-lg">
      @csrf
      @method('PUT')

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="name">Full Name</label>
        <input
          class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('name') border-danger @enderror"
          type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" autocomplete="name" required>
        @error('name')
          <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="email">Email Address</label>
        <input
          class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('email') border-danger @enderror"
          type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" autocomplete="email" required>
        @error('email')
          <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
        @enderror

        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
          <p class="mt-2 text-xs text-gray-500 dark:text-gray">
            Your email address is unverified.
            <form method="POST" action="{{ route('verification.send') }}" class="inline">
              @csrf
              <button type="submit" class="text-accent hover:underline text-xs">Resend verification email.</button>
            </form>
          </p>
        @endif
      </div>

      <div>
        <button type="submit"
          class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
          Save Changes
        </button>
      </div>
    </form>
  </div>

  {{-- Update Password --}}
  <div class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Update Password</h2>
    <p class="text-sm text-gray-500 dark:text-gray mb-8">Use a strong password to keep your account secure.</p>

    <form method="POST" action="{{ route('account.password.update') }}" class="flex flex-col gap-y-6 max-w-lg">
      @csrf
      @method('PUT')

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="current_password">
          Current Password
        </label>
        <input
          class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('current_password') border-danger @enderror"
          type="password" name="current_password" id="current_password" autocomplete="current-password">
        @error('current_password')
          <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="password">New Password</label>
        <input
          class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('password') border-danger @enderror"
          type="password" name="password" id="password" autocomplete="new-password">
        @error('password')
          <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="password_confirmation">
          Confirm New Password
        </label>
        <input
          class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
          type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password">
      </div>

      <div>
        <button type="submit"
          class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-white bg-primary hover:bg-primary/90">
          Update Password
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
