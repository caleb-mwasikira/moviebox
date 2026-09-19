@extends('layouts.app')

@section('title', 'Who\'s watching?')

@section('no-footer', '1')

@section('content')
<div class="flex min-h-[calc(100vh-4rem)] flex-col items-center justify-center px-5 py-12">
  <h1 class="mb-10 text-center text-3xl font-extrabold text-fg sm:text-4xl">Who's watching?</h1>

  @if (session('error'))
    <div class="alert alert-error mb-6 w-full max-w-md"><x-ui.icon name="alert" class="mt-0.5 size-5 shrink-0" />{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-error mb-6 w-full max-w-md"><x-ui.icon name="alert" class="mt-0.5 size-5 shrink-0" />{{ $errors->first() }}</div>
  @endif

  <div class="mb-10 flex max-w-3xl flex-wrap justify-center gap-6 sm:gap-8">
    @foreach ($ownProfiles as $profile)
      @include('pages.profiles._card', ['profile' => $profile, 'label' => null])
    @endforeach
  </div>

  @if ($sharedProfiles->isNotEmpty())
    <p class="divider mb-6 w-full max-w-md">Shared accounts</p>
    <div class="mb-10 flex max-w-3xl flex-wrap justify-center gap-6 sm:gap-8">
      @foreach ($sharedProfiles as $profile)
        @include('pages.profiles._card', ['profile' => $profile, 'label' => $profile->user->name])
      @endforeach
    </div>
  @endif

  <a href="{{ route('account.profiles') }}" class="btn btn-outline"><x-ui.icon name="settings" class="size-4" /> Manage profiles</a>
</div>
@endsection
