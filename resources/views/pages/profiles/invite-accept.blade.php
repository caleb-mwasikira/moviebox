@extends('layouts.app')

@section('title', 'Set Up Your Profile')

@section('no-footer', '1')

@section('content')
<div class="flex min-h-[calc(100vh-4rem)] items-center justify-center px-5 py-12">
  <div class="w-full max-w-md">
    <div class="mb-8 text-center">
      <p class="eyebrow mb-3">{{ $profile->user->name }} invited you</p>
      <h1 class="text-3xl font-extrabold text-fg">Set up your profile</h1>
      <p class="mt-2 text-sm text-muted">
        @if ($authenticated)
          Choose how you'll appear on {{ config('app.name') }}.
        @elseif ($hasExistingAccount)
          You already have an account. Enter your password to join {{ $profile->user->name }}.
        @else
          Create your account and join {{ $profile->user->name }} on {{ config('app.name') }}.
        @endif
      </p>
    </div>

    @if (session('error') || $errors->any())
      <div class="alert alert-error mb-6"><x-ui.icon name="alert" class="mt-0.5 size-5 shrink-0" />{{ session('error') ?? $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('profiles.invite.accept', $token) }}" class="card card-pad space-y-6"
      x-data="{
        color: '{{ $colors[0] }}',
        name: @js(old('name', '')),
        get initials() {
          var words = this.name.trim().split(/\s+/).filter(Boolean);
          if (!words.length) return '?';
          return (words[0].charAt(0) + (words[1] ? words[1].charAt(0) : '')).toUpperCase();
        }
      }">
      @csrf

      <div class="flex items-center gap-5">
        <span class="grid size-20 shrink-0 place-items-center rounded-2xl text-3xl font-bold text-white transition" :style="'background:' + color" x-text="initials"></span>
        <div class="min-w-0 flex-1">
          <label class="label">Your name</label>
          <input type="text" name="name" x-model="name" required maxlength="50" placeholder="e.g. Sarah" class="input">
        </div>
      </div>

      <div>
        <label class="label">Profile colour</label>
        <input type="hidden" name="avatar_color" :value="color">
        <div class="flex flex-wrap gap-2.5">
          @foreach ($colors as $c)
            <button type="button" @click="color = '{{ $c }}'" aria-label="Select colour {{ $c }}"
              class="size-10 rounded-full transition"
              :class="color === '{{ $c }}' ? 'scale-110 ring-2 ring-offset-2 ring-offset-surface' : ''"
              style="background: {{ $c }}; --tw-ring-color: {{ $c }}"></button>
          @endforeach
        </div>
      </div>

      @unless ($authenticated)
        <p class="divider">{{ $hasExistingAccount ? 'Your account' : 'Create account' }}</p>

        @if ($hasExistingAccount)
          <div>
            <p class="mb-4 text-sm text-muted">We found an account for <strong class="text-fg">{{ $profile->invite_email }}</strong>. Enter your password to link this profile.</p>
            <x-ui.field name="password" type="password" label="Password" autocomplete="current-password" required />
          </div>
        @else
          <div class="space-y-4">
            <p class="text-sm text-muted">A new account will be created for <strong class="text-fg">{{ $profile->invite_email }}</strong>.</p>
            <x-ui.field name="password" type="password" label="Password" autocomplete="new-password" minlength="8" required />
            <x-ui.field name="password_confirmation" type="password" label="Confirm password" autocomplete="new-password" required />
          </div>
        @endif
      @endunless

      <button type="submit" class="btn btn-primary btn-lg btn-block">
        @if ($authenticated) Save &amp; join @elseif ($hasExistingAccount) Log in &amp; join @else Create account &amp; join @endif
      </button>
    </form>
  </div>
</div>
@endsection
