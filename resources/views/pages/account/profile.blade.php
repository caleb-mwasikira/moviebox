@extends('layouts.account')

@section('account-title', 'My Profile')

@section('account-content')
@php
  $user = auth()->user();
  $activeProfileId = session('active_profile_id');
@endphp

<div x-data="{ loading: false, editId: null, editName: '', editColor: '', editIsKids: false, showInvite: false }"
  @loading.window="loading = true" class="relative space-y-6">

  {{-- Loading overlay --}}
  <div x-show="loading" x-cloak class="fixed inset-0 z-[70] grid place-items-center bg-black/40 backdrop-blur-[2px]">
    <svg class="size-10 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-opacity="0.25"/><path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
  </div>

  {{-- Personal info --}}
  <div class="card">
    <div class="card-header"><h2 class="card-title">My profile</h2></div>
    <div class="flex flex-col gap-8 p-5 sm:flex-row sm:p-6">
      <div class="flex shrink-0 flex-col items-center gap-3">
        @if ($user->avatarUrl())
          <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="size-24 rounded-full object-cover ring-4 ring-brand-soft">
        @else
          <span class="grid size-24 select-none place-items-center rounded-full bg-brand-soft text-3xl font-bold uppercase text-brand">{{ $user->initials() }}</span>
        @endif

        <form method="POST" action="{{ route('account.profile.avatar') }}" enctype="multipart/form-data" x-data="{ file: null }">
          @csrf
          <label class="btn btn-secondary btn-sm cursor-pointer">
            <x-ui.icon name="upload" class="size-3.5" /> Change
            <input type="file" name="avatar" accept="image/*" class="sr-only" @change="file = $event.target.files[0]; $el.form.submit()">
          </label>
        </form>
        @if ($user->avatarUrl())
          <form method="POST" action="{{ route('account.profile.avatar.remove') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs font-semibold text-muted hover:text-red-500">Remove photo</button>
          </form>
        @endif
        @error('avatar')<p class="field-error">{{ $message }}</p>@enderror
      </div>

      <form method="POST" action="{{ route('account.personal-info.update') }}" class="w-full max-w-lg space-y-5">
        @csrf
        @method('PUT')
        <x-ui.field name="name" label="Full name" :value="old('name', $user->name)" required />
        <x-ui.field name="email" type="email" label="Email address" :value="old('email', $user->email)" required />
        <button type="submit" class="btn btn-primary">Save changes</button>
      </form>
    </div>
  </div>

  {{-- Profiles --}}
  <div class="card">
    <div class="card-header">
      <div>
        <h2 class="card-title">Profiles</h2>
        <p class="mt-0.5 text-sm text-muted">Invite family or friends to share your subscription. Each gets their own watch history and watchlist.</p>
      </div>
      <span class="badge badge-neutral">{{ $profiles->where('status', '!=', 'pending')->count() }} / {{ $max }}</span>
    </div>

    <ul class="divide-y divide-line">
      @foreach ($profiles as $profile)
        @php $isOwner = $profile->isOwnerProfile(); @endphp
        <li class="flex flex-wrap items-center gap-4 px-5 py-4 sm:px-6 {{ $profile->isDisabled() ? 'opacity-50' : '' }}">
          <span class="grid size-11 shrink-0 place-items-center rounded-xl font-bold text-white" style="background: {{ $profile->avatar_color }}">{{ $profile->initials() }}</span>

          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
              <span class="truncate text-sm font-semibold text-fg">{{ $profile->displayName() }}</span>
              @if ($isOwner)<span class="badge badge-neutral">You</span>@endif
              @if ($profile->is_kids)<span class="badge badge-warning">Kids</span>@endif
              @if ($profile->isPending())<span class="badge badge-warning">Invite pending</span>
              @elseif ($profile->isDisabled())<span class="badge badge-danger">Disabled</span>@endif
              @if ((int) $activeProfileId === $profile->id)<span class="badge badge-brand">Active</span>@endif
            </div>
            @if ($profile->invite_email && ! $isOwner)<p class="mt-0.5 text-xs text-muted">{{ $profile->invite_email }}</p>@endif
          </div>

          <div class="flex shrink-0 flex-wrap items-center gap-1.5">
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
                    <button type="submit" :disabled="cd > 0" class="btn btn-secondary btn-sm tabular-nums" x-text="cd > 0 ? cd + 's' : 'Resend invite'">Resend invite</button>
                  </form>
                </div>
              @elseif ($profile->isActive())
                @if ((int) $activeProfileId !== $profile->id)
                  <form method="POST" action="{{ route('account.profiles.activate', $profile) }}" @submit="$dispatch('loading')">@csrf @method('PATCH')<button type="submit" class="btn btn-secondary btn-sm">Switch</button></form>
                @endif
                <form method="POST" action="{{ route('account.profiles.disable', $profile) }}" @submit="$dispatch('loading')">@csrf @method('PATCH')<button type="submit" class="btn btn-ghost btn-sm">Disable</button></form>
              @elseif ($profile->isDisabled())
                <form method="POST" action="{{ route('account.profiles.enable', $profile) }}" @submit="$dispatch('loading')">@csrf @method('PATCH')<button type="submit" class="btn btn-secondary btn-sm">Enable</button></form>
              @endif
              <form method="POST" action="{{ route('account.profiles.destroy', $profile) }}"
                @submit="if (!confirm('Remove this profile? Their watch history will be lost.')) { $event.preventDefault(); return; } $dispatch('loading')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger-soft btn-sm">Remove</button>
              </form>
            @else
              @if ((int) $activeProfileId !== $profile->id)
                <form method="POST" action="{{ route('account.profiles.activate', $profile) }}" @submit="$dispatch('loading')">@csrf @method('PATCH')<button type="submit" class="btn btn-secondary btn-sm">Switch</button></form>
              @endif
              <button type="button" class="btn btn-secondary btn-sm"
                @click="editId = {{ $profile->id }}; editName = @js($profile->name); editColor = '{{ $profile->avatar_color }}'; editIsKids = {{ $profile->is_kids ? 'true' : 'false' }}">
                <x-ui.icon name="edit" class="size-3.5" /> Edit
              </button>
            @endif
          </div>
        </li>
      @endforeach
    </ul>

    @if ($sharedProfiles->isNotEmpty())
      <div class="border-t border-line px-5 py-4 sm:px-6">
        <p class="label">Shared with me</p>
        <ul class="divide-y divide-line">
          @foreach ($sharedProfiles as $profile)
            <li class="flex items-center gap-4 py-3">
              <span class="grid size-11 shrink-0 place-items-center rounded-xl font-bold text-white" style="background: {{ $profile->avatar_color }}">{{ $profile->initials() }}</span>
              <div class="min-w-0 flex-1">
                <span class="text-sm font-semibold text-fg">{{ $profile->name }}</span>
                <p class="text-xs text-muted">{{ $profile->user->name }}'s account</p>
              </div>
              @if ((int) $activeProfileId !== $profile->id)
                <form method="POST" action="{{ route('account.profiles.activate', $profile) }}" @submit="$dispatch('loading')">@csrf @method('PATCH')<button type="submit" class="btn btn-secondary btn-sm">Switch</button></form>
              @else
                <span class="badge badge-brand">Active</span>
              @endif
            </li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Invite member --}}
    @php
      $memberSlots = max(0, $max - 1);
      $memberCount = $profiles->filter(fn($p) => ! $p->isOwnerProfile())->count();
    @endphp
    <div class="border-t border-line px-5 py-5 sm:px-6">
      @if ($memberSlots > 0)
        <p class="label">Members ({{ $memberCount }} / {{ $memberSlots }})</p>
        @if ($memberCount < $memberSlots)
          <div x-show="!showInvite"><button type="button" @click="showInvite = true" class="btn btn-outline"><x-ui.icon name="plus" class="size-4" /> Invite member</button></div>
          <div x-show="showInvite" x-cloak class="panel p-5">
            <h3 class="mb-3 text-sm font-semibold text-fg">Send invitation</h3>
            <form method="POST" action="{{ route('account.profiles.store') }}" class="flex flex-col gap-3 sm:flex-row" @submit="$dispatch('loading')">
              @csrf
              <input type="email" name="invite_email" required placeholder="member@example.com" class="input flex-1">
              <div class="flex gap-2">
                <button type="submit" class="btn btn-primary">Send invite</button>
                <button type="button" @click="showInvite = false" class="btn btn-secondary">Cancel</button>
              </div>
            </form>
          </div>
        @else
          <p class="text-sm text-muted">All {{ $memberSlots }} member {{ Str::plural('slot', $memberSlots) }} used. Upgrade to add more.</p>
        @endif
      @else
        <p class="text-sm text-muted">Your current plan supports only 1 profile. Upgrade to invite members.</p>
      @endif
    </div>
  </div>

  {{-- Password --}}
  <div class="card">
    <div class="card-header">
      <div><h2 class="card-title">Change password</h2><p class="mt-0.5 text-sm text-muted">Use a strong password to keep your account secure.</p></div>
    </div>
    <form method="POST" action="{{ route('account.password.update') }}" class="max-w-lg space-y-5 p-5 sm:p-6">
      @csrf @method('PUT')
      <x-ui.field name="current_password" type="password" label="Current password" autocomplete="current-password" />
      <x-ui.field name="password" type="password" label="New password" autocomplete="new-password" />
      <x-ui.field name="password_confirmation" type="password" label="Confirm new password" autocomplete="new-password" />
      <button type="submit" class="btn btn-primary">Update password</button>
    </form>
  </div>

  {{-- Edit profile modal --}}
  <template x-if="editId !== null">
    <div class="fixed inset-0 z-[80] grid place-items-center bg-black/60 p-4 backdrop-blur-sm" @click.self="editId = null" @keydown.escape.window="editId = null">
      <div class="card w-full max-w-md p-6 shadow-pop" @click.stop>
        <h3 class="mb-5 text-lg font-bold text-fg">Edit profile</h3>
        <form method="POST" :action="'/account/profiles/' + editId + '/own'" class="space-y-5" @submit="$dispatch('loading'); editId = null">
          @csrf @method('PATCH')
          <div>
            <label class="label">Name</label>
            <input type="text" name="name" :value="editName" required maxlength="50" class="input">
          </div>
          <div>
            <label class="label">Colour</label>
            <div class="flex flex-wrap gap-2.5">
              @foreach ($colors as $color)
                <label class="cursor-pointer">
                  <input type="radio" name="avatar_color" value="{{ $color }}" class="sr-only" x-bind:checked="editColor === '{{ $color }}'">
                  <span class="block size-9 rounded-xl transition"
                    x-bind:style="editColor === '{{ $color }}' ? 'background:{{ $color }};outline:3px solid var(--c-fg);outline-offset:2px' : 'background:{{ $color }}'"
                    @click="editColor = '{{ $color }}'"></span>
                </label>
              @endforeach
            </div>
          </div>
          <div class="flex gap-2 pt-1">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" @click="editId = null" class="btn btn-secondary">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </template>
</div>
@endsection
