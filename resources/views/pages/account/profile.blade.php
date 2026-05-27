@extends('layouts.account')

@section('account-title', 'My Profile')

@section('account-content')
@php
  $user          = auth()->user();
  $activeProfileId = session('active_profile_id');
@endphp

<div x-data="{
       loading: false,
       editId: null, editName: '', editColor: '', editIsKids: false,
       showInvite: false
     }"
     @loading.window="loading = true"
     class="flex flex-col gap-y-8">

  {{-- ── Personal info + avatar ── --}}
  <div class="relative bg-white shadow-3xl p-8 md:p-10 dark:bg-gray-700">

    {{-- Loading overlay --}}
    <div x-show="loading"
         style="position:absolute;inset:0;z-index:20;background:rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;">
      <svg class="animate-spin" style="width:2.25rem;height:2.25rem;" viewBox="0 0 24 24" fill="none">
        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="3" stroke-opacity="0.25"/>
        <path d="M22 12a10 10 0 0 0-10-10" stroke="white" stroke-width="3" stroke-linecap="round"/>
      </svg>
    </div>

    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-8">My Profile</h2>

    <div class="flex flex-col sm:flex-row items-start gap-8 max-w-2xl">

      {{-- Avatar --}}
      <div class="shrink-0 flex flex-col items-center gap-3">
        @if ($user->avatarUrl())
          <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
            class="w-20 h-20 rounded-full object-cover">
        @else
          <div class="w-20 h-20 rounded-full bg-accent flex items-center justify-center text-2xl font-bold text-gray-900 uppercase select-none">
            {{ $user->initials() }}
          </div>
        @endif

        <form method="POST" action="{{ route('account.profile.avatar') }}" enctype="multipart/form-data"
          x-data="{ file: null }">
          @csrf
          <label class="inline-flex items-center gap-x-1.5 cursor-pointer text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12V4m0 0L8 8m4-4l4 4"/>
            </svg>
            Change
            <input type="file" name="avatar" accept="image/*" class="sr-only"
              @change="file = $event.target.files[0]; $el.form.submit()">
          </label>
        </form>

        @if ($user->avatarUrl())
          <form method="POST" action="{{ route('account.profile.avatar.remove') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs font-bold uppercase text-gray-300 hover:text-red-400 transition-colors">
              Remove
            </button>
          </form>
        @endif

        @error('avatar')
          <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
      </div>

      {{-- Name + email --}}
      <form method="POST" action="{{ route('account.personal-info.update') }}" class="flex-1 flex flex-col gap-y-5">
        @csrf
        @method('PUT')

        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="name">Full Name</label>
          <input
            class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('name') border-danger @enderror"
            type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
          @error('name')
            <p class="mt-1 text-xs text-danger">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="email">Email Address</label>
          <input
            class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('email') border-danger @enderror"
            type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
          @error('email')
            <p class="mt-1 text-xs text-danger">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <button type="submit"
            class="inline-flex font-bold leading-none uppercase py-3 px-7 text-xs text-gray-900 bg-accent hover:bg-accent/90 transition-colors">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- ── Profiles management ── --}}
  <div class="relative bg-white shadow-3xl p-8 md:p-10 dark:bg-gray-700">

    <div class="flex items-center justify-between mb-1">
      <h2 class="text-xl font-bold text-gray-900 dark:text-white">Profiles</h2>
      <span class="text-xs font-bold uppercase text-gray-400 tracking-widest">
        {{ $profiles->where('status', '!=', 'pending')->count() }} / {{ $max }}
      </span>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
      Invite family or friends to share your subscription. Each gets their own watch history and watchlist.
    </p>

    {{-- Own profiles --}}
    <div class="flex flex-col divide-y divide-gray-100 dark:divide-gray-700 mb-6">
      @foreach ($profiles as $profile)
        @php $isOwner = $profile->isOwnerProfile(); @endphp
        <div class="py-4 flex items-center gap-x-3 sm:gap-x-4 {{ $profile->isDisabled() ? 'opacity-40' : '' }}">

          <div class="shrink-0 w-11 h-11 rounded flex items-center justify-center text-white font-bold text-base"
            style="background: {{ $profile->avatar_color }}">
            {{ $profile->initials() }}
          </div>

          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
              <span class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $profile->displayName() }}</span>
              @if ($isOwner)
                <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">You</span>
              @endif
              @if ($profile->is_kids)
                <span class="bg-amber-400 text-gray-900 text-[10px] font-bold uppercase px-1.5 py-0.5 tracking-wider">Kids</span>
              @endif
              @if ($profile->isPending())
                <span class="text-[10px] font-bold uppercase text-amber-500 tracking-wider">Invite pending</span>
              @elseif ($profile->isDisabled())
                <span class="text-[10px] font-bold uppercase text-red-400 tracking-wider">Disabled</span>
              @endif
              @if ((int) $activeProfileId === $profile->id)
                <span class="text-[10px] font-bold uppercase text-accent tracking-wider">Active</span>
              @endif
            </div>
            @if ($profile->invite_email && ! $isOwner)
              <p class="text-xs text-gray-400 mt-0.5">{{ $profile->invite_email }}</p>
            @endif
          </div>

          <div class="shrink-0 flex items-center gap-x-2 sm:gap-x-3">
            @if (! $isOwner)
              @if ($profile->isPending())
                <div x-data="{
                       cd: 0, _t: null, _key: 'rsnd_{{ $profile->id }}',
                       init() {
                         const end = parseInt(localStorage.getItem(this._key) || 0);
                         const left = Math.ceil((end - Date.now()) / 1000);
                         if (left > 0) { this.cd = left; this._tick(); }
                       },
                       _tick() {
                         this._t = setInterval(() => {
                           this.cd = Math.max(0, this.cd - 1);
                           if (this.cd === 0) clearInterval(this._t);
                         }, 1000);
                       },
                       submit(e) {
                         if (this.cd > 0) { e.preventDefault(); return; }
                         localStorage.setItem(this._key, Date.now() + 60000);
                         this.cd = 60; this._tick(); this.$dispatch('loading');
                       }
                     }">
                  <form method="POST" action="{{ route('account.profiles.resend-invite', $profile) }}" @submit="submit($event)">
                    @csrf
                    <button type="submit" :disabled="cd > 0"
                      :class="cd > 0 ? 'text-xs font-bold uppercase text-gray-300 dark:text-gray-600 cursor-not-allowed tabular-nums' : 'text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors'"
                      x-text="cd > 0 ? cd + 's' : 'Resend'"></button>
                  </form>
                </div>
              @elseif ($profile->isActive())
                @if ((int) $activeProfileId !== $profile->id)
                  <form method="POST" action="{{ route('account.profiles.activate', $profile) }}" @submit="$dispatch('loading')">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">Switch</button>
                  </form>
                @endif
                <form method="POST" action="{{ route('account.profiles.disable', $profile) }}" @submit="$dispatch('loading')">
                  @csrf @method('PATCH')
                  <button type="submit" class="text-xs font-bold uppercase text-gray-400 hover:text-amber-500 transition-colors">Disable</button>
                </form>
              @elseif ($profile->isDisabled())
                <form method="POST" action="{{ route('account.profiles.enable', $profile) }}" @submit="$dispatch('loading')">
                  @csrf @method('PATCH')
                  <button type="submit" class="text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors">Enable</button>
                </form>
              @endif
              <form method="POST" action="{{ route('account.profiles.destroy', $profile) }}"
                @submit="if (!confirm('Remove this profile? Their watch history will be lost.')) { $event.preventDefault(); return; } $dispatch('loading')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs font-bold uppercase text-gray-400 hover:text-red-500 transition-colors">Remove</button>
              </form>
            @else
              @if ((int) $activeProfileId !== $profile->id)
                <form method="POST" action="{{ route('account.profiles.activate', $profile) }}" @submit="$dispatch('loading')">
                  @csrf @method('PATCH')
                  <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">Switch</button>
                </form>
              @endif
              <button type="button"
                @click="editId = {{ $profile->id }}; editName = '{{ addslashes($profile->name) }}'; editColor = '{{ $profile->avatar_color }}'; editIsKids = {{ $profile->is_kids ? 'true' : 'false' }}"
                class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">
                Edit
              </button>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    {{-- Shared profiles --}}
    @if ($sharedProfiles->isNotEmpty())
      <div class="mb-6 pt-4 border-t border-gray-200 dark:border-gray-600">
        <p class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-4">Shared with me</p>
        <div class="flex flex-col divide-y divide-gray-100 dark:divide-gray-700">
          @foreach ($sharedProfiles as $profile)
            <div class="py-4 flex items-center gap-x-3 sm:gap-x-4">
              <div class="shrink-0 w-11 h-11 rounded flex items-center justify-center text-white font-bold text-base"
                style="background: {{ $profile->avatar_color }}">{{ $profile->initials() }}</div>
              <div class="flex-1 min-w-0">
                <span class="font-bold text-sm text-gray-900 dark:text-white">{{ $profile->name }}</span>
                <p class="text-xs text-gray-400 mt-0.5">{{ $profile->user->name }}'s account</p>
              </div>
              <div class="shrink-0 flex items-center gap-x-2 sm:gap-x-3">
                @if ((int) $activeProfileId !== $profile->id)
                  <form method="POST" action="{{ route('account.profiles.activate', $profile) }}" @submit="$dispatch('loading')">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">Switch</button>
                  </form>
                @else
                  <span class="text-[10px] font-bold uppercase text-accent tracking-wider">Active</span>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    {{-- Invite member --}}
    @php
      $memberSlots = max(0, $max - 1);
      $memberCount = $profiles->filter(fn($p) => ! $p->isOwnerProfile())->count();
    @endphp

    <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
      @if ($memberSlots > 0)
        <p class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-4">
          Members ({{ $memberCount }} / {{ $memberSlots }})
        </p>
        @if ($memberCount < $memberSlots)
          <div x-show="!showInvite">
            <button type="button" @click="showInvite = true"
              class="inline-flex items-center gap-x-2 font-bold leading-none uppercase py-2.5 px-5 text-xs text-gray-900 dark:text-white ring-2 ring-inset ring-gray-300 dark:ring-gray-600 hover:ring-accent hover:text-accent transition-colors bg-transparent">
              <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
              Invite Member
            </button>
          </div>
          <div x-show="showInvite" class="border border-gray-200 dark:border-gray-600 p-5">
            <h3 class="text-xs font-bold uppercase text-gray-500 tracking-widest mb-4">Send Invitation</h3>
            <form method="POST" action="{{ route('account.profiles.store') }}"
              class="flex flex-col sm:flex-row gap-3" @submit="$dispatch('loading')">
              @csrf
              <input type="email" name="invite_email" required placeholder="member@example.com"
                class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:border-accent">
              <div class="flex gap-x-2">
                <button type="submit"
                  class="font-bold leading-none uppercase py-2.5 px-5 text-xs text-gray-900 bg-accent hover:bg-accent/90 transition-colors">
                  Send Invite
                </button>
                <button type="button" @click="showInvite = false"
                  class="font-bold leading-none uppercase py-2.5 px-5 text-xs text-gray-500 hover:text-gray-900 dark:hover:text-white ring-2 ring-inset ring-gray-200 dark:ring-gray-600 transition-colors">
                  Cancel
                </button>
              </div>
            </form>
          </div>
        @else
          <p class="text-sm text-gray-400">All {{ $memberSlots }} member {{ Str::plural('slot', $memberSlots) }} used. Upgrade to add more.</p>
        @endif
      @else
        <p class="text-sm text-gray-400">Your current plan supports only 1 profile. Upgrade to invite members.</p>
      @endif
    </div>
  </div>

  {{-- ── Change password ── --}}
  <div class="bg-white shadow-3xl p-8 md:p-10 dark:bg-gray-700">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Change Password</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Use a strong password to keep your account secure.</p>

    <form method="POST" action="{{ route('account.password.update') }}" class="flex flex-col gap-y-5 max-w-lg">
      @csrf @method('PUT')

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="current_password">Current Password</label>
        <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('current_password') border-danger @enderror"
          type="password" name="current_password" id="current_password" autocomplete="current-password">
        @error('current_password')
          <p class="mt-1 text-xs text-danger">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="password">New Password</label>
        <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('password') border-danger @enderror"
          type="password" name="password" id="password" autocomplete="new-password">
        @error('password')
          <p class="mt-1 text-xs text-danger">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="password_confirmation">Confirm New Password</label>
        <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
          type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password">
      </div>

      <div>
        <button type="submit"
          class="inline-flex font-bold leading-none uppercase py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 transition-colors">
          Update Password
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ── Edit profile modal ── --}}
<template x-if="editId !== null">
  <div style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;padding:1rem"
    @click.self="editId = null">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md p-6" @click.stop>
      <h3 class="text-xs font-bold uppercase text-gray-500 tracking-widest mb-5">Edit Profile</h3>
      <form method="POST" :action="'/account/profiles/' + editId + '/own'"
        class="flex flex-col gap-y-4" @submit="$dispatch('loading'); editId = null">
        @csrf @method('PATCH')
        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 tracking-widest mb-1.5">Name</label>
          <input type="text" name="name" :value="editName" required maxlength="50"
            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:border-accent">
        </div>
        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 tracking-widest mb-1.5">Colour</label>
          <div class="flex flex-wrap gap-2">
            @foreach ($colors as $color)
              <label class="cursor-pointer">
                <input type="radio" name="avatar_color" value="{{ $color }}" class="sr-only"
                  x-bind:checked="editColor === '{{ $color }}'">
                <div class="w-8 h-8 rounded transition-all"
                  x-bind:style="editColor === '{{ $color }}' ? 'background:{{ $color }};outline:3px solid white;outline-offset:2px;box-shadow:0 0 0 4px {{ $color }}' : 'background:{{ $color }}'"
                  @click="editColor = '{{ $color }}'"></div>
              </label>
            @endforeach
          </div>
        </div>
        <div class="flex gap-x-3 pt-2">
          <button type="submit" class="font-bold leading-none uppercase py-2.5 px-6 text-xs text-gray-900 bg-accent hover:bg-accent/90 transition-colors">Save</button>
          <button type="button" @click="editId = null" class="font-bold leading-none uppercase py-2.5 px-6 text-xs text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</template>

@endsection
