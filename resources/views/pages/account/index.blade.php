@extends('layouts.account')

@section('account-title', 'Dashboard')

@section('account-content')
@php
  $profiles = auth()->user()->profiles()->visible()->orderBy('created_at')->get();
  $activeProfileId = session('active_profile_id');
@endphp

<x-ui.page-header class="!mb-0" title="Hello, {{ Str::before(auth()->user()->name, ' ') }} 👋" subtitle="Manage your subscription, profiles and account details from here." />

<div class="grid gap-4 sm:grid-cols-2">
  <a href="{{ route('account.billing') }}" class="card card-pad group flex items-center gap-4 transition hover:border-brand/50">
    <span class="grid size-11 place-items-center rounded-xl bg-brand-soft text-brand"><x-ui.icon name="credit-card" /></span>
    <span class="flex-1"><span class="block font-semibold text-fg">Billing details</span><span class="text-sm text-muted">Plan, invoices &amp; payments</span></span>
    <x-ui.icon name="chevron-right" class="size-5 text-muted transition group-hover:translate-x-0.5 group-hover:text-brand" />
  </a>
  <a href="{{ route('account.personal-info') }}" class="card card-pad group flex items-center gap-4 transition hover:border-brand/50">
    <span class="grid size-11 place-items-center rounded-xl bg-brand-soft text-brand"><x-ui.icon name="lock" /></span>
    <span class="flex-1"><span class="block font-semibold text-fg">Account &amp; password</span><span class="text-sm text-muted">Edit your details</span></span>
    <x-ui.icon name="chevron-right" class="size-5 text-muted transition group-hover:translate-x-0.5 group-hover:text-brand" />
  </a>
  <a href="{{ route('account.watching') }}" class="card card-pad group flex items-center gap-4 transition hover:border-brand/50">
    <span class="grid size-11 place-items-center rounded-xl bg-brand-soft text-brand"><x-ui.icon name="play" /></span>
    <span class="flex-1"><span class="block font-semibold text-fg">Continue watching</span><span class="text-sm text-muted">Pick up where you left off</span></span>
    <x-ui.icon name="chevron-right" class="size-5 text-muted transition group-hover:translate-x-0.5 group-hover:text-brand" />
  </a>
  <a href="{{ route('account.watchlist') }}" class="card card-pad group flex items-center gap-4 transition hover:border-brand/50">
    <span class="grid size-11 place-items-center rounded-xl bg-brand-soft text-brand"><x-ui.icon name="bookmark" /></span>
    <span class="flex-1"><span class="block font-semibold text-fg">Watch later</span><span class="text-sm text-muted">Titles you've saved</span></span>
    <x-ui.icon name="chevron-right" class="size-5 text-muted transition group-hover:translate-x-0.5 group-hover:text-brand" />
  </a>
</div>

{{-- Profiles --}}
<div class="card">
  <div class="card-header">
    <h2 class="card-title">Profiles</h2>
    <a href="{{ route('account.profiles') }}" class="btn btn-ghost btn-sm">Manage</a>
  </div>
  <div class="flex flex-wrap gap-5 p-5 sm:p-6">
    @foreach ($profiles as $profile)
      @php $isActive = (int) $activeProfileId === $profile->id; @endphp
      <form method="POST" action="{{ route('account.profiles.activate', $profile) }}">
        @csrf
        @method('PATCH')
        <button type="submit" title="Switch to {{ $profile->name }}" class="group flex flex-col items-center gap-2">
          <span class="grid size-14 place-items-center rounded-2xl text-lg font-bold text-white transition group-hover:scale-105 {{ $isActive ? 'ring-2 ring-brand ring-offset-2 ring-offset-surface' : '' }}" style="background: {{ $profile->avatar_color }}">{{ $profile->initials() }}</span>
          <span class="text-xs font-semibold {{ $isActive ? 'text-brand' : 'text-muted' }}">{{ Str::limit($profile->name, 10) }}</span>
        </button>
      </form>
    @endforeach

    @if ($profiles->count() < auth()->user()->maxProfiles())
      <a href="{{ route('account.profile') }}" class="group flex flex-col items-center gap-2">
        <span class="grid size-14 place-items-center rounded-2xl border-2 border-dashed border-line-strong text-muted transition group-hover:border-brand group-hover:text-brand"><x-ui.icon name="plus" /></span>
        <span class="text-xs font-semibold text-muted group-hover:text-brand">Add</span>
      </a>
    @endif
  </div>
</div>

@if (auth()->user()->isAdmin())
  <div class="card card-pad flex flex-wrap items-center justify-between gap-4">
    <div><h2 class="card-title">Administration</h2><p class="text-sm text-muted">Manage content, users and revenue.</p></div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary"><x-ui.icon name="shield" class="size-4" /> Go to admin panel</a>
  </div>
@endif
@endsection
