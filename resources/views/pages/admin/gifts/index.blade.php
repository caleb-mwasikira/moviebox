@extends('layouts.admin')

@section('admin-title', 'Gift Subscriptions')

@section('admin-content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
  <div>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Gift Subscriptions</h2>
    <p class="mt-1 text-gray-500 dark:text-gray">{{ $gifts->total() }} {{ Str::plural('gift', $gifts->total()) }} total</p>
  </div>
</div>

@if (session('success'))
  <div class="mb-6 px-4 py-3 border border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-950 text-green-700 dark:text-green-300 text-sm font-medium">
    {{ session('success') }}
  </div>
@endif
@if (session('error'))
  <div class="mb-6 px-4 py-3 border border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-300 text-sm font-medium">
    {{ session('error') }}
  </div>
@endif

{{-- Summary cards --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-8">
  <div class="border border-gray-200 dark:border-gray-700 p-4">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Total</p>
    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($summary['total']) }}</p>
  </div>
  <div class="border border-gray-200 dark:border-gray-700 p-4">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Pending Claim</p>
    <p class="text-2xl font-black text-blue-500">{{ number_format($summary['pending']) }}</p>
  </div>
  <div class="border border-gray-200 dark:border-gray-700 p-4">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Claimed</p>
    <p class="text-2xl font-black text-accent">{{ number_format($summary['claimed']) }}</p>
  </div>
  <div class="border border-gray-200 dark:border-gray-700 p-4">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Expired</p>
    <p class="text-2xl font-black text-gray-400">{{ number_format($summary['expired']) }}</p>
  </div>
  <div class="border border-gray-200 dark:border-gray-700 p-4">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Awaiting Payment</p>
    <p class="text-2xl font-black text-orange-400">{{ number_format($summary['payment_pending']) }}</p>
  </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.gifts.index') }}" class="mb-6 flex flex-wrap gap-2">
  <input type="text" name="search" value="{{ request('search') }}"
    placeholder="Search recipient, sender…"
    class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-accent focus:outline-none flex-1 min-w-48">
  <select name="status"
    class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-accent focus:outline-none">
    <option value="">All statuses</option>
    <option value="pending"  @selected(request('status') === 'pending')>Pending Claim</option>
    <option value="claimed"  @selected(request('status') === 'claimed')>Claimed</option>
    <option value="expired"  @selected(request('status') === 'expired')>Expired</option>
  </select>
  <button type="submit"
    class="px-4 py-2 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors shrink-0">
    Filter
  </button>
  @if(request('search') || request('status'))
    <a href="{{ route('admin.gifts.index') }}"
      class="px-4 py-2 text-xs font-bold uppercase border border-gray-300 dark:border-gray-600 text-gray-500 hover:text-accent transition-colors shrink-0">
      Clear
    </a>
  @endif
</form>

@if ($gifts->isEmpty())
  <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
    <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No gifts found</p>
  </div>
@else

  {{-- ── Mobile cards (< lg) ── --}}
  <div class="lg:hidden border border-gray-200 dark:border-gray-600 divide-y divide-gray-200 dark:divide-gray-600">
    @foreach ($gifts as $gift)
      @php
        $isClaimable = $gift->isClaimable();
        $isExpired   = $gift->isExpired() && ! $gift->isClaimed();
        $isClaimed   = $gift->isClaimed();
        $isPending   = $gift->status === 'pending';
      @endphp
      <div class="p-4 {{ $isExpired ? 'opacity-60' : '' }}">
        <div class="flex items-start justify-between gap-x-3 mb-2">
          <div class="min-w-0">
            <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $gift->recipient_email }}</p>
            <p class="text-xs text-gray-500 mt-0.5">
              From <span class="font-medium text-gray-700 dark:text-gray-300">{{ $gift->sender?->name ?? 'Unknown' }}</span>
              · {{ $gift->package?->name ?? '—' }}
            </p>
          </div>
          @if ($isClaimed)
            <span class="shrink-0 inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-accent/10 text-accent border border-accent">Claimed</span>
          @elseif ($isClaimable)
            <span class="shrink-0 inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-900/20 dark:border-blue-700 dark:text-blue-400">Pending</span>
          @elseif ($isExpired)
            <span class="shrink-0 inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-gray-100 text-gray-400 border border-gray-200 dark:bg-gray-800 dark:border-gray-600">Expired</span>
          @else
            <span class="shrink-0 inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-orange-50 text-orange-500 border border-orange-200 dark:bg-orange-900/20 dark:border-orange-700">Awaiting Payment</span>
          @endif
        </div>

        @if ($isClaimed && $gift->claimedBy)
          <p class="text-xs text-gray-400 mb-2">Claimed by {{ $gift->claimedBy->name }} · {{ $gift->claimed_at?->diffForHumans() }}</p>
        @elseif ($gift->expires_at)
          <p class="text-xs text-gray-400 mb-2">
            {{ $isExpired ? 'Expired' : 'Expires' }} {{ $gift->expires_at->format('M j, Y') }}
          </p>
        @endif

        <div class="flex items-center gap-x-4 pt-2 border-t border-gray-100 dark:border-gray-700">
          <span class="text-[10px] text-gray-400">{{ $gift->created_at->format('M j, Y') }}</span>
          @if ($isClaimable)
            <form method="POST" action="{{ route('admin.gifts.resend', $gift) }}" class="ml-auto">
              @csrf
              <button type="submit"
                class="text-xs font-bold uppercase text-accent hover:text-accent/70 transition-colors">
                Resend Email
              </button>
            </form>
          @endif
        </div>
      </div>
    @endforeach
  </div>

  {{-- ── Desktop table (≥ lg) ── --}}
  <div class="hidden lg:block border border-gray-200 dark:border-gray-600 overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Recipient</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Sender</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Package</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Status</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest hidden xl:table-cell">Sent</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest hidden xl:table-cell">Expires / Claimed</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
        @foreach ($gifts as $gift)
          @php
            $isClaimable = $gift->isClaimable();
            $isExpired   = $gift->isExpired() && ! $gift->isClaimed();
            $isClaimed   = $gift->isClaimed();
          @endphp
          <tr class="{{ $isExpired ? 'opacity-60' : '' }}">
            <td class="px-5 py-4">
              <p class="font-bold text-gray-900 dark:text-white">{{ $gift->recipient_email }}</p>
              @if ($isClaimed && $gift->claimedBy)
                <p class="text-xs text-gray-400 mt-0.5">{{ $gift->claimedBy->name }}</p>
              @endif
            </td>
            <td class="px-5 py-4">
              <p class="text-gray-700 dark:text-gray-300">{{ $gift->sender?->name ?? '—' }}</p>
              <p class="text-xs text-gray-400 mt-0.5">{{ $gift->sender?->email ?? '' }}</p>
            </td>
            <td class="px-5 py-4 text-gray-700 dark:text-gray-300">
              {{ $gift->package?->name ?? '—' }}
            </td>
            <td class="px-5 py-4">
              @if ($isClaimed)
                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-accent/10 text-accent border border-accent">Claimed</span>
              @elseif ($isClaimable)
                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-900/20 dark:border-blue-700 dark:text-blue-400">Pending Claim</span>
              @elseif ($isExpired)
                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-gray-100 text-gray-400 border border-gray-200 dark:bg-gray-800 dark:border-gray-600">Expired</span>
              @else
                <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-orange-50 text-orange-500 border border-orange-200 dark:bg-orange-900/20 dark:border-orange-700">Awaiting Payment</span>
              @endif
            </td>
            <td class="px-5 py-4 text-xs text-gray-400 whitespace-nowrap hidden xl:table-cell">
              {{ $gift->created_at->format('M j, Y') }}
            </td>
            <td class="px-5 py-4 hidden xl:table-cell">
              @if ($isClaimed && $gift->claimed_at)
                <p class="text-xs text-gray-500">{{ $gift->claimed_at->format('M j, Y') }}</p>
              @elseif ($gift->expires_at)
                <p class="text-xs {{ $isExpired ? 'text-red-400' : 'text-gray-500' }}">
                  {{ $gift->expires_at->format('M j, Y') }}
                </p>
              @else
                <span class="text-xs text-gray-400">—</span>
              @endif
            </td>
            <td class="px-5 py-4 text-right">
              @if ($isClaimable)
                <form method="POST" action="{{ route('admin.gifts.resend', $gift) }}">
                  @csrf
                  <button type="submit"
                    class="text-xs font-bold uppercase text-accent hover:text-accent/70 transition-colors whitespace-nowrap">
                    Resend Email
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-6">{{ $gifts->links() }}</div>

@endif
@endsection
