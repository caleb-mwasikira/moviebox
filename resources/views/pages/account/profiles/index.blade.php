@extends('layouts.account')

@section('account-title', 'Profiles')

@section('account-content')
@php $activeProfileId = session('active_profile_id'); @endphp

<div x-data="{
       showInvite: false,
       loading: false,
       editId: null, editName: '', editColor: '', editIsKids: false
     }"
     @loading.window="loading = true"
     class="relative bg-white shadow-3xl p-4 sm:p-8 md:p-10 lg:p-14 dark:bg-gray-700">

  {{-- Loading overlay --}}
  <div x-show="loading"
       style="position:absolute;inset:0;z-index:20;background:rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;">
    <svg class="animate-spin" style="width:2.25rem;height:2.25rem;" viewBox="0 0 24 24" fill="none">
      <circle cx="12" cy="12" r="10" stroke="white" stroke-width="3" stroke-opacity="0.25"/>
      <path d="M22 12a10 10 0 0 0-10-10" stroke="white" stroke-width="3" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="flex items-center justify-between mb-1">
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white">Profiles</h2>
    <span class="text-xs font-bold uppercase text-gray-400 tracking-widest">
      {{ $profiles->where('status', '!=', 'pending')->count() }} / {{ $max }}
    </span>
  </div>
  <p class="text-sm text-gray-500 dark:text-gray mb-8">
    Invite family or friends to share your subscription. Each gets their own watch history and watchlist.
  </p>

  {{-- ── Own profiles (main user + invited members) ── --}}
  <div class="flex flex-col divide-y divide-gray-100 dark:divide-gray-700 mb-8">
    @foreach ($profiles as $profile)
      @php $isOwner = $profile->isOwnerProfile(); @endphp
      <div class="py-4 flex items-center gap-x-3 sm:gap-x-4 {{ $profile->isDisabled() ? 'opacity-40' : '' }}">

        {{-- Avatar --}}
        <div class="shrink-0 w-11 h-11 rounded flex items-center justify-center text-white font-bold text-base"
          style="background: {{ $profile->avatar_color }}">
          {{ $profile->initials() }}
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
          <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5">
            <span class="font-bold text-sm text-gray-900 dark:text-white truncate">
              {{ $profile->displayName() }}
            </span>

            @if ($isOwner)
              <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">You</span>
            @endif
            @if ($profile->is_kids)
              <span class="bg-amber-400 text-gray-900 text-[10px] font-bold uppercase px-1.5 py-0.5 tracking-wider">Kids</span>
            @endif
            @if ($profile->hasPin())
              <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">PIN</span>
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

        {{-- Actions --}}
        <div class="shrink-0 flex items-center gap-x-2 sm:gap-x-3">
          @if (! $isOwner)

            @if ($profile->isPending())
              {{-- Resend with cooldown timer --}}
              <div x-data="{
                     cd: 0,
                     _t: null,
                     _key: 'rsnd_{{ $profile->id }}',
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
                       this.cd = 60;
                       this._tick();
                       this.$dispatch('loading');
                     }
                   }">
                <form method="POST" action="{{ route('account.profiles.resend-invite', $profile) }}"
                  @submit="submit($event)">
                  @csrf
                  <button type="submit" :disabled="cd > 0"
                    :class="cd > 0
                      ? 'text-xs font-bold uppercase text-gray-300 dark:text-gray-600 cursor-not-allowed tabular-nums'
                      : 'text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors'"
                    x-text="cd > 0 ? cd + 's' : 'Resend'">
                  </button>
                </form>
              </div>

            @elseif ($profile->isActive())
              @if ((int) $activeProfileId !== $profile->id)
                <form method="POST" action="{{ route('account.profiles.activate', $profile) }}"
                  @submit="$dispatch('loading')">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">
                    Switch
                  </button>
                </form>
              @endif
              <form method="POST" action="{{ route('account.profiles.disable', $profile) }}"
                @submit="$dispatch('loading')">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-xs font-bold uppercase text-gray-400 hover:text-amber-500 transition-colors">
                  Disable
                </button>
              </form>

            @elseif ($profile->isDisabled())
              <form method="POST" action="{{ route('account.profiles.enable', $profile) }}"
                @submit="$dispatch('loading')">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors">
                  Enable
                </button>
              </form>
            @endif

            <form method="POST" action="{{ route('account.profiles.destroy', $profile) }}"
              @submit="if (!confirm('Remove this profile? Their watch history will be lost.')) { $event.preventDefault(); return; } $dispatch('loading')">
              @csrf
              @method('DELETE')
              <button type="submit" class="text-xs font-bold uppercase text-gray-400 hover:text-red-500 transition-colors">
                Remove
              </button>
            </form>

          @else
            {{-- Owner profile: switch + edit --}}
            @if ((int) $activeProfileId !== $profile->id)
              <form method="POST" action="{{ route('account.profiles.activate', $profile) }}"
                @submit="$dispatch('loading')">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">
                  Switch
                </button>
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

  {{-- ── Shared profiles (invited into other accounts) ── --}}
  @if ($sharedProfiles->isNotEmpty())
    <div class="mb-8 pt-4 border-t border-gray-200 dark:border-gray-600">
      <p class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-4">Shared with me</p>
      <div class="flex flex-col divide-y divide-gray-100 dark:divide-gray-700">
        @foreach ($sharedProfiles as $profile)
          <div class="py-4 flex items-center gap-x-3 sm:gap-x-4">
            <div class="shrink-0 w-11 h-11 rounded flex items-center justify-center text-white font-bold text-base"
              style="background: {{ $profile->avatar_color }}">
              {{ $profile->initials() }}
            </div>
            <div class="flex-1 min-w-0">
              <span class="font-bold text-sm text-gray-900 dark:text-white">{{ $profile->name }}</span>
              <p class="text-xs text-gray-400 mt-0.5">{{ $profile->user->name }}'s account</p>
            </div>
            <div class="shrink-0 flex items-center gap-x-2 sm:gap-x-3">
              @if ((int) $activeProfileId !== $profile->id)
                <form method="POST" action="{{ route('account.profiles.activate', $profile) }}"
                  @submit="$dispatch('loading')">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">
                    Switch
                  </button>
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

  {{-- ── Invite new member ── --}}
  @php
    $memberSlots = max(0, $max - 1);
    $memberCount = $profiles->filter(fn($p) => ! $p->isOwnerProfile())->count();
  @endphp

  @if ($memberSlots > 0)
    <div id="invite" class="pt-4 border-t border-gray-200 dark:border-gray-600">
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
            class="flex flex-col sm:flex-row gap-3"
            @submit="$dispatch('loading')">
            @csrf
            <input type="email" name="invite_email" required placeholder="member@example.com"
              class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:border-accent">
            <div class="flex gap-x-2">
              <button type="submit"
                class="font-bold leading-none uppercase py-2.5 px-5 text-xs text-gray-900 bg-accent hover:bg-accent transition-colors">
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
        <p class="text-sm text-gray-400">
          All {{ $memberSlots }} member {{ Str::plural('slot', $memberSlots) }} used.
          Upgrade your plan to add more.
        </p>
      @endif
    </div>
  @else
    <p class="text-sm text-gray-400 pt-4 border-t border-gray-200 dark:border-gray-600">
      Your current plan supports only 1 profile. Upgrade to invite members.
    </p>
  @endif

</div>

{{-- ── Edit profile modal ── --}}
<template x-if="editId !== null">
  <div style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;padding:1rem"
    @click.self="editId = null">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md p-6" @click.stop>
      <h3 class="text-xs font-bold uppercase text-gray-500 tracking-widest mb-5">Edit Profile</h3>

      <form method="POST" :action="'/account/profiles/' + editId + '/own'"
        class="flex flex-col gap-y-4"
        @submit="$dispatch('loading'); editId = null">
        @csrf
        @method('PATCH')

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
          <button type="submit"
            class="font-bold leading-none uppercase py-2.5 px-6 text-xs text-gray-900 bg-accent hover:bg-accent transition-colors">
            Save
          </button>
          <button type="button" @click="editId = null"
            class="font-bold leading-none uppercase py-2.5 px-6 text-xs text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

@endsection
