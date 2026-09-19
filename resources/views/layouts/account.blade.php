@extends('layouts.app')

@section('title')Account — @yield('account-title', 'Dashboard')@endsection

@section('content')
@php
  $u = auth()->user();
  $activeProfile = $u->activeProfile();
  $hasAdmin = $u->isAdmin() || $u->roles->isNotEmpty();
  $items = [
    ['account.dashboard', 'Dashboard', 'dashboard', request()->routeIs('account.dashboard')],
    ['account.profile', 'My profile', 'user', request()->routeIs('account.profile')],
    ['account.watchlist', 'Watch later', 'bookmark', request()->routeIs('account.watchlist')],
    ['account.watching', 'Continue watching', 'play', request()->routeIs('account.watching')],
    ['account.billing', 'Billing', 'credit-card', request()->routeIs('account.billing')],
    ['account.watch-party.index', 'Watch party', 'popcorn', request()->routeIs('account.watch-party.*')],
  ];
@endphp

<div class="container-app py-8 lg:py-12">
  <div class="flex flex-col gap-8 lg:flex-row lg:gap-10">

    {{-- Sidebar --}}
    <aside class="lg:w-64 lg:shrink-0">
      <div class="lg:sticky lg:top-24">
        <div class="card card-pad mb-4 hidden items-center gap-3 lg:flex">
          @if ($u->avatarUrl())
            <img src="{{ $u->avatarUrl() }}" alt="" class="size-11 rounded-full object-cover">
          @else
            <span class="grid size-11 place-items-center rounded-full bg-brand-soft text-sm font-bold text-brand">{{ $u->initials() }}</span>
          @endif
          <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-fg">{{ $u->name }}</p>
            <p class="truncate text-xs text-muted">{{ $activeProfile ? 'Profile: ' . $activeProfile->name : $u->email }}</p>
          </div>
        </div>

        <nav class="-mx-5 flex gap-1 overflow-x-auto px-5 pb-1 lg:mx-0 lg:flex-col lg:overflow-visible lg:px-0 lg:pb-0">
          @foreach ($items as [$route, $label, $icon, $active])
            <a href="{{ route($route) }}" class="side-link shrink-0 whitespace-nowrap {{ $active ? 'is-active' : '' }}"><x-ui.icon :name="$icon" /> {{ $label }}</a>
          @endforeach

          @if ($hasAdmin)
            <a href="{{ route('admin.dashboard') }}" class="side-link shrink-0 whitespace-nowrap lg:mt-3"><x-ui.icon name="shield" /> Admin panel</a>
          @endif
          <a href="{{ route('account.delete-account') }}" class="side-link shrink-0 whitespace-nowrap hover:!bg-red-500/10 hover:!text-red-500 {{ request()->routeIs('account.delete-account') ? '!bg-red-500/10 !text-red-500' : '' }} {{ $hasAdmin ? '' : 'lg:mt-3' }}">
            <x-ui.icon name="trash" /> Delete account
          </a>
          <form method="POST" action="{{ route('logout') }}" class="shrink-0">
            @csrf
            <button type="submit" class="side-link w-full whitespace-nowrap"><x-ui.icon name="log-out" /> Sign out</button>
          </form>
        </nav>
      </div>
    </aside>

    {{-- Content --}}
    <div class="min-w-0 flex-1 space-y-6">
      @if (session('status') === 'profile-updated')
        <div class="alert alert-success"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />Profile updated successfully.</div>
      @elseif (session('status') === 'password-updated')
        <div class="alert alert-success"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />Password updated successfully.</div>
      @endif

      @yield('account-content')
    </div>
  </div>
</div>
@endsection
