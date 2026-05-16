@extends('layouts.app')

@section('title', 'Set Up Your Profile')

@section('content')
<div style="min-height:100vh;background:#141414;display:flex;align-items:center;justify-content:center;padding:3rem 1.25rem;">
  <div style="width:100%;max-width:420px;">

    {{-- Inviter context --}}
    <div style="text-align:center;margin-bottom:2.5rem;">
      <p style="color:#888;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:0.75rem;">
        {{ $profile->user->name }} invited you
      </p>
      <h1 style="color:#fff;font-size:1.875rem;font-weight:700;letter-spacing:-0.02em;line-height:1.2;">
        Set up your profile
      </h1>
      <p style="color:#666;font-size:0.875rem;margin-top:0.5rem;">
        @if ($authenticated)
          Choose how you'll appear on {{ config('app.name') }}.
        @elseif ($hasExistingAccount)
          You already have an account. Enter your password to join {{ $profile->user->name }}.
        @else
          Create your account and join {{ $profile->user->name }} on {{ config('app.name') }}.
        @endif
      </p>
    </div>

    {{-- Error / session flash --}}
    @if (session('error') || $errors->any())
      <div style="margin-bottom:1.5rem;background:rgba(127,29,29,0.6);border:1px solid #ef4444;color:#fca5a5;padding:0.75rem 1rem;font-size:0.875rem;">
        {{ session('error') ?? $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('profiles.invite.accept', $token) }}"
      x-data="{
        color: '{{ $colors[0] }}',
        name: '{{ old('name') }}',
        get initials() {
          var words = this.name.trim().split(/\s+/).filter(Boolean);
          if (!words.length) return '?';
          return (words[0].charAt(0) + (words[1] ? words[1].charAt(0) : '')).toUpperCase();
        }
      }"
      style="display:flex;flex-direction:column;gap:1.75rem;">
      @csrf

      {{-- Avatar preview + Name --}}
      <div style="display:flex;align-items:center;gap:1.25rem;">
        <div :style="'flex-shrink:0;width:80px;height:80px;border-radius:6px;background:' + color + ';display:flex;align-items:center;justify-content:center;font-size:1.875rem;font-weight:700;color:#fff;letter-spacing:0.02em;transition:background 0.25s'"
          x-text="initials"></div>

        <div style="flex:1;min-width:0;">
          <label style="display:block;font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#888;margin-bottom:0.5rem;">
            Your Name
          </label>
          <input type="text" name="name" x-model="name" required maxlength="50"
            placeholder="e.g. Sarah"
            style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #333;color:#fff;padding:10px 12px;font-size:0.9375rem;outline:none;border-radius:2px;transition:border-color 0.15s;"
            onfocus="this.style.borderColor='#aaa'"
            onblur="this.style.borderColor='#333'">
        </div>
      </div>

      {{-- Colour picker --}}
      <div>
        <label style="display:block;font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#888;margin-bottom:0.875rem;">
          Profile Colour
        </label>
        <input type="hidden" name="avatar_color" :value="color">
        <div style="display:flex;flex-wrap:wrap;gap:0.625rem;">
          @foreach ($colors as $c)
            <button type="button"
              :style="color === '{{ $c }}'
                ? 'width:2.625rem;height:2.625rem;border-radius:50%;background:{{ $c }};border:none;cursor:pointer;box-shadow:0 0 0 2px #141414,0 0 0 4px {{ $c }};transform:scale(1.1);transition:all 0.2s'
                : 'width:2.625rem;height:2.625rem;border-radius:50%;background:{{ $c }};border:none;cursor:pointer;box-shadow:none;transform:scale(1);transition:all 0.2s'"
              @click="color = '{{ $c }}'"
              aria-label="Select colour {{ $c }}">
            </button>
          @endforeach
        </div>
      </div>

      @unless ($authenticated)
        {{-- Divider --}}
        <div style="display:flex;align-items:center;gap:0.75rem;">
          <div style="flex:1;height:1px;background:#2a2a2a;"></div>
          <span style="font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#444;">
            {{ $hasExistingAccount ? 'Your Account' : 'Create Account' }}
          </span>
          <div style="flex:1;height:1px;background:#2a2a2a;"></div>
        </div>

        @if ($hasExistingAccount)
          {{-- Existing account — password only --}}
          <div>
            <p style="font-size:0.8125rem;color:#888;margin-bottom:1rem;">
              We found an account for <strong style="color:#ccc;">{{ $profile->invite_email }}</strong>.
              Enter your password to link this profile.
            </p>
            <label style="display:block;font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#888;margin-bottom:0.5rem;">
              Password
            </label>
            <input type="password" name="password" required autocomplete="current-password"
              style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid {{ $errors->has('password') ? '#ef4444' : '#333' }};color:#fff;padding:10px 12px;font-size:0.9375rem;outline:none;border-radius:2px;transition:border-color 0.15s;"
              onfocus="this.style.borderColor='#aaa'"
              onblur="this.style.borderColor='{{ $errors->has('password') ? '#ef4444' : '#333' }}'">
            @error('password')
              <p style="margin-top:0.375rem;font-size:0.75rem;color:#f87171;">{{ $message }}</p>
            @enderror
          </div>
        @else
          {{-- New account — password + confirm --}}
          <div style="display:flex;flex-direction:column;gap:1rem;">
            <p style="font-size:0.8125rem;color:#888;margin:0;">
              A new account will be created for <strong style="color:#ccc;">{{ $profile->invite_email }}</strong>.
            </p>
            <div>
              <label style="display:block;font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#888;margin-bottom:0.5rem;">
                Password
              </label>
              <input type="password" name="password" required autocomplete="new-password" minlength="8"
                style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid {{ $errors->has('password') ? '#ef4444' : '#333' }};color:#fff;padding:10px 12px;font-size:0.9375rem;outline:none;border-radius:2px;transition:border-color 0.15s;"
                onfocus="this.style.borderColor='#aaa'"
                onblur="this.style.borderColor='{{ $errors->has('password') ? '#ef4444' : '#333' }}'">
              @error('password')
                <p style="margin-top:0.375rem;font-size:0.75rem;color:#f87171;">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label style="display:block;font-size:0.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#888;margin-bottom:0.5rem;">
                Confirm Password
              </label>
              <input type="password" name="password_confirmation" required autocomplete="new-password"
                style="width:100%;box-sizing:border-box;background:#1e1e1e;border:1px solid #333;color:#fff;padding:10px 12px;font-size:0.9375rem;outline:none;border-radius:2px;transition:border-color 0.15s;"
                onfocus="this.style.borderColor='#aaa'"
                onblur="this.style.borderColor='#333'">
            </div>
          </div>
        @endif
      @endunless

      {{-- Submit --}}
      <div style="padding-top:0.25rem;">
        <button type="submit"
          class="bg-accent"
          style="width:100%;color:#111;font-size:0.8125rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;padding:15px;border:none;cursor:pointer;transition:opacity 0.15s;"
          onmouseover="this.style.opacity='0.88'"
          onmouseout="this.style.opacity='1'">
          @if ($authenticated)
            Save &amp; Join
          @elseif ($hasExistingAccount)
            Log In &amp; Join
          @else
            Create Account &amp; Join
          @endif
        </button>
      </div>

    </form>

  </div>
</div>
@endsection
