@extends('layouts.admin')

@section('admin-title', 'Gift Subscriptions')

@section('admin-content')
<x-ui.page-header title="Gift subscriptions" :subtitle="$gifts->total() . ' ' . Str::plural('gift', $gifts->total()) . ' total'" />

@if (session('success'))<div class="alert alert-success"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />{{ session('success') }}</div>@endif
@if (session('error'))<div class="alert alert-error"><x-ui.icon name="alert" class="mt-0.5 size-5 shrink-0" />{{ session('error') }}</div>@endif

<div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
  <x-ui.stat label="Total" :value="number_format($summary['total'])" icon="gift" />
  <x-ui.stat label="Pending claim" :value="number_format($summary['pending'])" icon="clock" tone="info" />
  <x-ui.stat label="Claimed" :value="number_format($summary['claimed'])" icon="check-circle" tone="success" />
  <x-ui.stat label="Expired" :value="number_format($summary['expired'])" icon="ban" tone="danger" />
  <x-ui.stat label="Awaiting payment" :value="number_format($summary['payment_pending'])" icon="wallet" tone="warning" class="col-span-2 lg:col-span-1" />
</div>

<form method="GET" action="{{ route('admin.gifts.index') }}" class="flex flex-col gap-3 sm:flex-row">
  <div class="relative flex-1"><x-ui.icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-muted" /><input type="text" name="search" value="{{ request('search') }}" placeholder="Search recipient, sender…" class="input !pl-10"></div>
  <select name="status" class="select sm:!w-48">
    <option value="">All statuses</option>
    <option value="pending" @selected(request('status') === 'pending')>Pending claim</option>
    <option value="claimed" @selected(request('status') === 'claimed')>Claimed</option>
    <option value="expired" @selected(request('status') === 'expired')>Expired</option>
  </select>
  <button type="submit" class="btn btn-primary">Filter</button>
  @if (request('search') || request('status'))<a href="{{ route('admin.gifts.index') }}" class="btn btn-secondary">Clear</a>@endif
</form>

@if ($gifts->isEmpty())
  <x-ui.empty icon="gift" title="No gifts found" />
@else
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Recipient</th><th class="hidden md:table-cell">Sender</th><th class="hidden sm:table-cell">Package</th><th>Status</th><th class="hidden lg:table-cell">Sent</th><th class="hidden lg:table-cell">Expires / claimed</th><th></th></tr></thead>
      <tbody>
        @foreach ($gifts as $gift)
          @php
            $isClaimable = $gift->isClaimable();
            $isExpired   = $gift->isExpired() && ! $gift->isClaimed();
            $isClaimed   = $gift->isClaimed();
          @endphp
          <tr>
            <td>
              <p class="font-semibold text-fg">{{ $gift->recipient_email }}</p>
              @if ($isClaimed && $gift->claimedBy)<p class="text-xs text-muted">{{ $gift->claimedBy->name }}</p>@endif
            </td>
            <td class="hidden md:table-cell"><p class="text-fg-soft">{{ $gift->sender?->name ?? '—' }}</p><p class="text-xs text-muted">{{ $gift->sender?->email ?? '' }}</p></td>
            <td class="hidden sm:table-cell">{{ $gift->package?->name ?? '—' }}</td>
            <td>
              @if ($isClaimed)<span class="badge badge-success">Claimed</span>
              @elseif ($isClaimable)<span class="badge badge-info">Pending claim</span>
              @elseif ($isExpired)<span class="badge badge-neutral">Expired</span>
              @else<span class="badge badge-warning">Awaiting payment</span>@endif
            </td>
            <td class="hidden whitespace-nowrap lg:table-cell">{{ $gift->created_at->format('M j, Y') }}</td>
            <td class="hidden whitespace-nowrap lg:table-cell">
              @if ($isClaimed && $gift->claimed_at){{ $gift->claimed_at->format('M j, Y') }}
              @elseif ($gift->expires_at){{ $gift->expires_at->format('M j, Y') }}
              @else<span class="text-muted">—</span>@endif
            </td>
            <td class="text-right">
              @if ($isClaimable)
                <form method="POST" action="{{ route('admin.gifts.resend', $gift) }}">@csrf<button type="submit" class="btn btn-secondary btn-sm"><x-ui.icon name="mail" class="size-3.5" /> Resend</button></form>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div>{{ $gifts->links() }}</div>
@endif
@endsection
