@extends('layouts.admin')

@section('admin-title', 'Test Emails')

@section('admin-content')
@php
  $descriptions = [
    'welcome'               => 'Sent after email verification. Shows account email and join date.',
    'payment_confirmed'     => 'Sent on successful M-Pesa payment. Shows receipt, amount, plan, phone.',
    'subscription_renewed'  => 'Sent when subscription is renewed. Shows plan and expiry date.',
    'subscription_extended' => 'Sent when admin extends a subscription. Shows +30 days added.',
    'subscription_expiring' => 'Sent 3 days before expiry. Shows plan and days remaining.',
    'profile_invite'        => 'Sent when a user invites a family member to their profile.',
    'gift_received'         => 'Sent to the recipient when someone purchases a gift subscription for them. Includes gift banner image, sender\'s message, plan details, and claim link.'
  ];
  $groups = [
    'Account'      => ['welcome', 'profile_invite'],
    'Payments'     => ['payment_confirmed'],
    'Subscription' => ['subscription_renewed', 'subscription_extended', 'subscription_expiring'],
    'Gifts'        => ['gift_received'],
  ];
@endphp

<x-ui.page-header title="Test emails" subtitle="Send a test email to verify templates are rendering correctly. Fake data is used where real records are needed." />

<form method="POST" action="{{ route('admin.test-email.send') }}" class="card card-pad max-w-3xl space-y-6">
  @csrf
  <x-ui.field name="email" type="email" label="Recipient email" :value="old('email', auth()->user()->email)" required />

  <div>
    <p class="label">Template</p>
    <div class="space-y-5">
      @foreach ($groups as $group => $keys)
        <div>
          <p class="mb-2 text-[11px] font-semibold uppercase tracking-widest text-muted">{{ $group }}</p>
          <div class="space-y-2">
            @foreach ($keys as $key)
              <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line p-3.5 transition hover:bg-surface-2 has-[:checked]:border-brand has-[:checked]:bg-brand-soft">
                <input type="radio" name="template" value="{{ $key }}" class="mt-1 accent-[var(--c-brand)]" {{ old('template', 'welcome') === $key ? 'checked' : '' }}>
                <div>
                  <span class="text-sm font-semibold text-fg">{{ $templates[$key] }}</span>
                  <p class="text-xs text-muted">{{ $descriptions[$key] }}</p>
                </div>
              </label>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
    @error('template')<p class="field-error">{{ $message }}</p>@enderror
  </div>

  <button type="submit" class="btn btn-primary"><x-ui.icon name="send" class="size-4" /> Send test email</button>
</form>
@endsection
