@extends('layouts.account')

@section('account-title', 'Dashboard')

@section('account-content')
  <div
    class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700 [&_a]:text-accent hover:[&_a]:text-accent/80 [&_a]:transition-colors">

    <p class="mb-6 text-gray-900 dark:text-white">
      Hello <strong>{{ auth()->user()->name }}</strong>
    </p>

    <p class="text-gray-500 dark:text-gray leading-relaxed">
      From your account dashboard you can manage your
      <a href="{{ route('account.billing') }}">billing details</a>
      and
      <a href="{{ route('account.personal-info') }}">edit your password and account details</a>.
    </p>

    {{-- Profiles --}}
    @php
      $profiles = auth()->user()->profiles()->visible()->orderBy('created_at')->get();
      $activeProfileId = session('active_profile_id');
    @endphp
    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-600">
      <div class="flex items-center justify-between mb-4">
        <p class="text-xs font-bold uppercase text-gray-500 tracking-widest">Profiles</p>
        <a href="{{ route('account.profiles') }}"
          class="text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors tracking-widest">
          Manage
        </a>
      </div>

      <div class="flex flex-wrap gap-3 mb-4">
        @foreach ($profiles as $profile)
          <form method="POST" action="{{ route('account.profiles.activate', $profile) }}">
            @csrf
            @method('PATCH')
            <button type="submit" title="Switch to {{ $profile->name }}"
              class="flex flex-col items-center gap-y-1.5 group">
              <div class="w-12 h-12 rounded flex items-center justify-center text-white font-bold text-base transition-all {{ (int) $activeProfileId === $profile->id ? 'ring-2 ring-offset-2 ring-accent' : 'opacity-70 group-hover:opacity-100' }}"
                style="background: {{ $profile->avatar_color }}">
                {{ $profile->initials() }}
              </div>
              <span class="text-[10px] font-bold uppercase tracking-wider {{ (int) $activeProfileId === $profile->id ? 'text-accent' : 'text-gray-400' }}">
                {{ Str::limit($profile->name, 10) }}
              </span>
            </button>
          </form>
        @endforeach

        {{-- Add profile shortcut --}}
        @if ($profiles->count() < auth()->user()->maxProfiles())
          <a href="{{ route('account.profiles.create-invite') }}"
            class="flex flex-col items-center gap-y-1.5 group">
            <div class="w-12 h-12 rounded flex items-center justify-center text-gray-400 border-2 border-dashed border-gray-300 dark:border-gray-600 group-hover:border-accent group-hover:text-accent transition-colors">
              <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 group-hover:text-accent transition-colors">
              Add
            </span>
          </a>
        @endif
      </div>
    </div>

    @if (auth()->user()->isAdmin())
      <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-600">
        <p class="text-xs font-bold uppercase text-gray-500 tracking-widest mb-4">Admin</p>
        <a href="{{ route('admin.dashboard') }}" class="bg-accent px-6 py-3 text-sm font-bold uppercase text-gray-900 hover:bg-accent/90" style="color: black;">
          Go to Admin Panel
        </a>
      </div>
    @endif

  </div>
@endsection