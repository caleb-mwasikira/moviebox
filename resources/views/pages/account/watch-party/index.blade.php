@extends('layouts.account')

@section('account-title', 'Watch Party')

@section('account-content')
@php $user = auth()->user(); $canCreate = $user->canUseWatchParty(); @endphp

<div class="card card-pad">
  <div class="flex flex-wrap items-start justify-between gap-4">
    <div class="flex items-start gap-4">
      <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-brand-soft text-brand"><x-ui.icon name="popcorn" class="size-6" /></span>
      <div><h2 class="card-title text-xl">Watch party</h2><p class="mt-0.5 text-sm text-muted">Watch movies and series in sync with friends.</p></div>
    </div>
    @if ($canCreate)
      <a href="{{ route('account.watch-party.create') }}" class="btn btn-primary"><x-ui.icon name="plus" class="size-4" /> New party</a>
    @else
      <a href="{{ route('pricing') }}" class="btn btn-outline">Upgrade to create parties</a>
    @endif
  </div>
  @if (! $canCreate)
    <div class="alert alert-info mt-6"><x-ui.icon name="info" class="mt-0.5 size-5 shrink-0" />
      <p>Watch parties are available on plans with the feature enabled. <a href="{{ route('pricing') }}" class="font-semibold text-brand hover:underline">View plans →</a></p>
    </div>
  @endif
</div>

<div class="card">
  <div class="card-header"><div><h2 class="card-title">Your parties</h2><p class="mt-0.5 text-sm text-muted">Parties you're hosting.</p></div></div>
  @if ($parties->isEmpty())
    <div class="p-6">
      <x-ui.empty icon="popcorn" title="No active parties">
        @if ($canCreate)<a href="{{ route('account.watch-party.create') }}" class="btn btn-primary">Start one now</a>@endif
      </x-ui.empty>
    </div>
  @else
    <ul class="divide-y divide-line">
      @foreach ($parties as $party)
        <li class="flex items-center gap-4 px-5 py-4 sm:px-6">
          <div class="min-w-0 flex-1">
            <p class="truncate font-semibold text-fg">{{ $party->title() }}</p>
            <p class="mt-0.5 flex items-center gap-2 text-xs text-muted">
              Started {{ $party->created_at->diffForHumans() }}
              @if ($party->status === 'live')<span class="badge badge-success">Live</span>@else<span class="badge badge-neutral">Waiting</span>@endif
            </p>
          </div>
          <div class="flex shrink-0 items-center gap-2">
            <a href="{{ route('watch-party.show', $party->token) }}" class="btn btn-secondary btn-sm">Open</a>
            <form method="POST" action="{{ route('watch-party.end', $party->token) }}" onsubmit="return confirm('End this watch party?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger-soft btn-sm">End</button>
            </form>
          </div>
        </li>
      @endforeach
    </ul>
  @endif
</div>

@if ($joined->isNotEmpty())
  <div class="card">
    <div class="card-header"><div><h2 class="card-title">Parties you've joined</h2><p class="mt-0.5 text-sm text-muted">Active parties from others.</p></div></div>
    <ul class="divide-y divide-line">
      @foreach ($joined as $membership)
        @php $p = $membership->party; @endphp
        <li class="flex items-center gap-4 px-5 py-4 sm:px-6">
          <div class="min-w-0 flex-1">
            <p class="truncate font-semibold text-fg">{{ $p->title() }}</p>
            <p class="mt-0.5 flex items-center gap-2 text-xs text-muted">
              Hosted by {{ $p->host->name }}
              @if ($p->status === 'live')<span class="badge badge-success">Live</span>@else<span class="badge badge-neutral">Waiting</span>@endif
            </p>
          </div>
          <a href="{{ route('watch-party.show', $p->token) }}" class="btn btn-secondary btn-sm shrink-0">Rejoin</a>
        </li>
      @endforeach
    </ul>
  </div>
@endif
@endsection
