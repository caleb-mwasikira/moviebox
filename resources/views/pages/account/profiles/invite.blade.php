@extends('layouts.account')

@section('account-title', 'Invite Member')

@section('account-content')
<div class="bg-white shadow-3xl p-4 sm:p-8 md:p-10 lg:p-14 dark:bg-gray-700">

  <div class="flex items-center justify-between mb-1">
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white">Invite a Member</h2>
    <a href="{{ route('account.profiles') }}"
      class="text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors tracking-widest">
      &larr; Back
    </a>
  </div>
  <p class="text-sm text-gray-500 dark:text-gray mb-8">
    Enter the email address of the person you want to invite. They'll receive an email to set up their profile.
  </p>

  @if (session('success'))
    <div class="mb-6 border border-green-400 bg-green-50 dark:bg-green-900/20 px-4 py-3 text-sm text-green-700 dark:text-green-400">
      {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div class="mb-6 border border-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-600 dark:text-red-400">
      {{ session('error') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-6 border border-red-400 bg-red-50 dark:bg-red-900/20 px-4 py-3 text-sm text-red-600 dark:text-red-400">
      {{ $errors->first() }}
    </div>
  @endif

  @if ($memberCount >= $maxMembers)
    <p class="text-sm text-gray-500">
      All {{ $maxMembers }} member {{ Str::plural('slot', $maxMembers) }} are used.
      <a href="{{ route('account.billing') }}" class="text-accent hover:underline">Upgrade your plan</a> to add more.
    </p>
  @else
    <form method="POST" action="{{ route('account.profiles.store') }}" class="max-w-sm flex flex-col gap-y-4"
      x-data="{ loading: false }" @submit="loading = true">
      @csrf
      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 dark:text-gray-400 tracking-widest mb-1.5">
          Email Address
        </label>
        <input type="email" name="invite_email" required placeholder="member@example.com"
          value="{{ old('invite_email') }}"
          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2.5 text-sm focus:outline-none focus:border-accent">
      </div>
      <div class="flex gap-x-3">
        <button type="submit" :disabled="loading"
          class="inline-flex items-center gap-x-1.5 font-bold leading-none uppercase py-2.5 px-6 text-xs text-gray-900 bg-accent hover:bg-accent transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
          <svg x-show="loading" class="animate-spin h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <span x-text="loading ? 'Sending…' : 'Send Invite'"></span>
        </button>
        <a href="{{ route('account.profiles') }}"
          class="font-bold leading-none uppercase py-2.5 px-6 text-xs text-gray-500 hover:text-gray-900 dark:hover:text-white ring-2 ring-inset ring-gray-200 dark:ring-gray-600 transition-colors">
          Cancel
        </a>
      </div>
    </form>
  @endif

</div>
@endsection
