@extends('layouts.account')

@section('account-title', 'Watch Party')

@section('account-content')
@php $user = auth()->user(); $canCreate = $user->canUseWatchParty(); @endphp

<div class="flex flex-col gap-y-8">

  {{-- Header + create CTA --}}
  <div class="bg-white shadow-3xl p-8 md:p-10 dark:bg-gray-700">
    <div class="flex items-start justify-between gap-4 flex-wrap">
      <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Watch Party</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Watch movies and series in sync with friends.</p>
      </div>
      @if ($canCreate)
        <a href="{{ route('account.watch-party.create') }}"
          class="inline-flex items-center gap-x-2 font-bold uppercase py-2.5 px-5 text-xs bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
          <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
          New Party
        </a>
      @else
        <a href="{{ route('pricing') }}"
          class="inline-flex items-center gap-x-2 font-bold uppercase py-2.5 px-5 text-xs border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-accent hover:text-accent transition-colors">
          Upgrade to Create Parties
        </a>
      @endif
    </div>

    @if (! $canCreate)
      <div class="mt-6 border border-dashed border-gray-200 dark:border-gray-600 p-5 flex items-start gap-x-3">
        <svg class="w-5 h-5 text-accent shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Watch Parties are available on plans with the feature enabled. <a href="{{ route('pricing') }}" class="text-accent hover:underline font-bold">View plans →</a>
        </p>
      </div>
    @endif
  </div>

  {{-- Your parties --}}
  <div class="bg-white shadow-3xl p-8 md:p-10 dark:bg-gray-700">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Your Parties</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Parties you're hosting.</p>

    @if ($parties->isEmpty())
      <div class="py-10 text-center border border-dashed border-gray-200 dark:border-gray-600">
        <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No active parties</p>
        @if ($canCreate)
          <a href="{{ route('account.watch-party.create') }}"
            class="inline-block mt-4 text-xs font-bold uppercase text-accent hover:underline">
            Start one now
          </a>
        @endif
      </div>
    @else
      <div class="divide-y divide-gray-100 dark:divide-gray-700">
        @foreach ($parties as $party)
          <div class="py-4 flex items-center gap-x-4">
            <div class="flex-1 min-w-0">
              <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $party->title() }}</p>
              <p class="text-xs text-gray-400 mt-0.5">
                Started {{ $party->created_at->diffForHumans() }}
                &middot;
                @if ($party->status === 'live') <span class="text-green-500">Live</span>
                @else <span class="text-gray-400">Waiting</span>
                @endif
              </p>
            </div>
            <div class="shrink-0 flex items-center gap-x-3">
              <a href="{{ route('watch-party.show', $party->token) }}"
                class="text-xs font-bold uppercase text-accent hover:underline">
                Open
              </a>
              <form method="POST" action="{{ route('watch-party.end', $party->token) }}"
                onsubmit="return confirm('End this watch party?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs font-bold uppercase text-gray-400 hover:text-red-500 transition-colors">
                  End
                </button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Joined parties --}}
  @if ($joined->isNotEmpty())
  <div class="bg-white shadow-3xl p-8 md:p-10 dark:bg-gray-700">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Parties You've Joined</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Active parties from others.</p>

    <div class="divide-y divide-gray-100 dark:divide-gray-700">
      @foreach ($joined as $membership)
        @php $p = $membership->party; @endphp
        <div class="py-4 flex items-center gap-x-4">
          <div class="flex-1 min-w-0">
            <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $p->title() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">
              Hosted by {{ $p->host->name }}
              &middot;
              @if ($p->status === 'live') <span class="text-green-500">Live</span>
              @else <span class="text-gray-400">Waiting</span>
              @endif
            </p>
          </div>
          <a href="{{ route('watch-party.show', $p->token) }}"
            class="shrink-0 text-xs font-bold uppercase text-accent hover:underline">
            Rejoin
          </a>
        </div>
      @endforeach
    </div>
  </div>
  @endif

</div>
@endsection
