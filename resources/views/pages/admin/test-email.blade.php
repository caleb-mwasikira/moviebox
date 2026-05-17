@extends('layouts.admin')

@section('admin-title', 'Test Emails')

@section('admin-content')
<div class="mb-8">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Test Emails</h2>
  <p class="mt-1 text-gray-500 dark:text-gray-400 text-sm">Send a test email to verify templates are rendering correctly. Fake data is used where real records are needed.</p>
</div>

<div class="max-w-lg">
  <form method="POST" action="{{ route('admin.test-email.send') }}" class="border border-gray-200 dark:border-gray-600 p-6">
    @csrf

    <div class="mb-5">
      <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
        Recipient Email
      </label>
      <input
        type="email"
        name="email"
        value="{{ old('email', auth()->user()->email) }}"
        required
        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-accent focus:outline-none">
      @error('email')
        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
      @enderror
    </div>

    <div class="mb-8">
      <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3">
        Template
      </label>

      @php
        $descriptions = [
          'welcome'               => 'Sent after email verification. Shows account email and join date.',
          'payment_confirmed'     => 'Sent on successful M-Pesa payment. Shows receipt, amount, plan, phone.',
          'subscription_renewed'  => 'Sent when subscription is renewed. Shows plan and expiry date.',
          'subscription_extended' => 'Sent when admin extends a subscription. Shows +30 days added.',
          'subscription_expiring' => 'Sent 3 days before expiry. Shows plan and days remaining.',
          'profile_invite'        => 'Sent when a user invites a family member to their profile.',
        ];
        $groups = [
          'Account'      => ['welcome', 'profile_invite'],
          'Payments'     => ['payment_confirmed'],
          'Subscription' => ['subscription_renewed', 'subscription_extended', 'subscription_expiring'],
        ];
      @endphp

      <div class="flex flex-col gap-y-6">
        @foreach ($groups as $group => $keys)
          <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">{{ $group }}</p>
            <div class="flex flex-col gap-y-2">
              @foreach ($keys as $key)
                <label class="flex items-start gap-x-3 cursor-pointer border border-gray-200 dark:border-gray-600 p-3 hover:border-accent transition-colors has-[:checked]:border-accent has-[:checked]:bg-accent/5">
                  <input
                    type="radio"
                    name="template"
                    value="{{ $key }}"
                    {{ old('template', 'welcome') === $key ? 'checked' : '' }}
                    class="mt-0.5 w-4 h-4 shrink-0 border-gray-300 text-accent focus:ring-accent">
                  <div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $templates[$key] }}</span>
                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">{{ $descriptions[$key] }}</p>
                  </div>
                </label>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>

      @error('template')
        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
      @enderror
    </div>

    <button type="submit"
      class="w-full flex items-center justify-center gap-x-2 font-bold uppercase tracking-wide py-3 text-sm bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
      </svg>
      Send Test Email
    </button>
  </form>
</div>
@endsection
