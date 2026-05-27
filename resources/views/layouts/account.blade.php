@extends('layouts.app')

@section('title')Account - @yield('account-title', 'Dashboard')@endsection

@section('content')
  <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
    <div class="container">

      <div class="flex flex-col lg:flex-row gap-y-8 lg:gap-x-12 xl:gap-x-16">

        {{-- Sidebar --}}
        <aside class="hidden lg:block lg:w-52 xl:w-60 shrink-0">
          <nav class="lg:sticky lg:top-8">
            <ul class="flex flex-row flex-wrap gap-x-6 gap-y-2 lg:flex-col lg:gap-x-0 lg:gap-y-1 font-bold">
              <li>
                <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('account') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                  href="{{ route('account.dashboard') }}">
                  Dashboard
                </a>
              </li>
              <li>
                @php $activeProfile = auth()->user()->activeProfile(); @endphp
                <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('account.profile') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                  href="{{ route('account.profile') }}">
                  My Profile
                  @if ($activeProfile)
                    <span class="text-xs font-normal text-gray-400 ml-1">({{ $activeProfile->name }})</span>
                  @endif
                </a>
              </li>
              <li>
                <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('account.watching') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                  href="{{ route('account.watching') }}">
                  Continue Watching
                </a>
              </li>
              <li>
                <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('account.watchlist') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                  href="{{ route('account.watchlist') }}">
                  My Watchlist
                </a>
              </li>
              <li>
                <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('account.billing') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                  href="{{ route('account.billing') }}">
                  Billing Details
                </a>
              </li>
              <li>
                <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('account.watch-party.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                  href="{{ route('account.watch-party.index') }}">
                  Watch Party
                </a>
              </li>
              @if (auth()->user()->isAdmin() || auth()->user()->roles->isNotEmpty())
              <li class="lg:mt-4 lg:pt-4 lg:border-t lg:border-gray-200 lg:dark:border-gray-600">
                <a class="block py-1.5 transition-all hover:text-accent dark:hover:text-accent {{ request()->routeIs('admin.*') ? 'text-accent' : 'text-gray-900 dark:text-white' }}"
                  href="{{ route('admin.dashboard') }}">
                  Admin Panel
                </a>
              </li>
              @endif
              <li>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                  @csrf
                  <button type="submit"
                    class="block py-1.5 transition-all text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent font-bold">
                    Logout
                  </button>
                </form>
              </li>
            </ul>

            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
              <label for="theme-toggle"
                class="flex items-center gap-x-2.5 cursor-pointer text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors font-bold text-sm py-1.5">
                <svg class="w-4 h-4 shrink-0 dark:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                </svg>
                <svg class="w-4 h-4 shrink-0 hidden dark:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
                <span class="dark:hidden">Light Mode</span>
                <span class="hidden dark:block">Dark Mode</span>
              </label>
            </div>
          </nav>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 min-w-0">
            @if (session('success'))
              <div class="mb-6 flex items-center justify-between bg-accent/10 border border-accent p-4 dark:bg-accent/5">
                  <div class="flex items-center gap-x-3">
                      <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                      </svg>
                      <span class="text-sm font-bold uppercase tracking-tight text-gray-900 dark:text-white">
                          {{ session('success') }}
                      </span>
                  </div>
                  <button type="button" onclick="this.parentElement.remove()" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path></svg>
                  </button>
              </div>
            @endif

          @if (session('error'))
            <div class="mb-6 flex items-center justify-between bg-red-500/10 border border-red-500 p-4 dark:bg-red-500/5">
                <div class="flex items-center gap-x-3">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="text-sm font-bold uppercase tracking-tight text-gray-900 dark:text-white">
                        {{ session('error') }}
                    </span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path></svg>
                </button>
            </div>
          @endif

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
