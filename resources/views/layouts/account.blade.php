@extends('layouts.app')

@section('title')Account - @yield('account-title', 'Dashboard')@endsection

@section('content')
<div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
  <div class="container">

    <div class="flex flex-col lg:flex-row gap-y-8 lg:gap-x-12 xl:gap-x-16">

      {{-- Sidebar --}}
      <aside class="lg:w-52 xl:w-60 shrink-0">
        <nav class="lg:sticky lg:top-8">
          <ul class="flex flex-row flex-wrap gap-x-6 gap-y-2 lg:flex-col lg:gap-x-0 lg:gap-y-1 font-bold">
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('account') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('account') }}">
                Dashboard
              </a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('account.personal-info') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('account.personal-info') }}">
                Personal Info
              </a>
            </li>
            <li>
              <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('account.billing') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                href="{{ route('account.billing') }}">
                Billing Details
              </a>
            </li>
            <li class="lg:mt-4 lg:pt-4 lg:border-t lg:border-gray-200 lg:dark:border-gray-600">
              <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                  class="block py-1.5 transition-all text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent font-bold">
                  Logout
                </button>
              </form>
            </li>
          </ul>
        </nav>
      </aside>

      {{-- Main content --}}
      <div class="flex-1 min-w-0">
        @if (session('status') === 'profile-updated')
          <div class="mb-6 border border-accent bg-accent/10 px-4 py-3 text-sm text-accent font-medium">
            Profile updated successfully.
          </div>
        @elseif (session('status') === 'password-updated')
          <div class="mb-6 border border-accent bg-accent/10 px-4 py-3 text-sm text-accent font-medium">
            Password updated successfully.
          </div>
        @endif

        @yield('account-content')
      </div>

    </div>
  </div>
</div>
@endsection
