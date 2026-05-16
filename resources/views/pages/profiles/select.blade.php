@extends('layouts.app')

@section('title', 'Who\'s Watching?')

@section('content')
<div style="min-height:100vh;background:#141414;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 20px;">

  <h1 style="color:#fff;font-size:2rem;font-weight:700;letter-spacing:-0.02em;margin-bottom:3rem;text-align:center;">
    Who's watching?
  </h1>

  @if (session('error'))
    <div style="margin-bottom:1.5rem;background:#7f1d1d;border:1px solid #ef4444;color:#fca5a5;padding:0.75rem 1rem;font-size:0.875rem;text-align:center;max-width:400px;width:100%;">
      {{ session('error') }}
    </div>
  @endif

  @if ($errors->any())
    <div style="margin-bottom:1.5rem;background:#7f1d1d;border:1px solid #ef4444;color:#fca5a5;padding:0.75rem 1rem;font-size:0.875rem;text-align:center;max-width:400px;width:100%;">
      {{ $errors->first() }}
    </div>
  @endif

  {{-- Own profiles --}}
  <div style="display:flex;flex-wrap:wrap;gap:1.5rem;justify-content:center;margin-bottom:2rem;">
    @foreach ($ownProfiles as $profile)
      @include('pages.profiles._card', ['profile' => $profile, 'label' => null])
    @endforeach
  </div>

  {{-- Shared profiles (invited) --}}
  @if ($sharedProfiles->isNotEmpty())
    <p style="color:#666;font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:1rem;">
      Shared accounts
    </p>
    <div style="display:flex;flex-wrap:wrap;gap:1.5rem;justify-content:center;margin-bottom:2.5rem;">
      @foreach ($sharedProfiles as $profile)
        @include('pages.profiles._card', ['profile' => $profile, 'label' => $profile->user->name])
      @endforeach
    </div>
  @endif

  <a href="{{ route('account.profiles') }}"
    style="color:#aaa;font-size:0.8125rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;border:1px solid #555;padding:8px 20px;text-decoration:none;transition:color 0.15s,border-color 0.15s;"
    onmouseover="this.style.color='#fff';this.style.borderColor='#fff';"
    onmouseout="this.style.color='#aaa';this.style.borderColor='#555';">
    Manage Profiles
  </a>

</div>
@endsection
