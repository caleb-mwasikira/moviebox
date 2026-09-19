@extends('layouts.admin')

@section('admin-title', 'Payments')

@section('admin-content')
<x-ui.page-header title="Payments" :subtitle="$payments->total() . ' ' . Str::plural('transaction', $payments->total())" />

<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
  <x-ui.stat label="Total revenue" :value="'KES ' . number_format($summary['total_paid'], 0)" icon="wallet" tone="success" class="col-span-2 lg:col-span-1" />
  <x-ui.stat label="Successful" :value="number_format($summary['count_paid'])" icon="check-circle" tone="success" />
  <x-ui.stat label="Pending" :value="number_format($summary['count_pending'])" icon="clock" tone="warning" />
  <x-ui.stat label="Failed" :value="number_format($summary['count_failed'])" icon="ban" tone="danger" />
</div>

<form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-col gap-3 sm:flex-row">
  <div class="relative flex-1"><x-ui.icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-muted" /><input type="text" name="search" value="{{ request('search') }}" placeholder="Search user, phone, receipt…" class="input !pl-10"></div>
  <select name="status" class="select sm:!w-48">
    <option value="">All statuses</option>
    @foreach (['paid' => 'Paid', 'pending' => 'Pending', 'failed' => 'Failed', 'cancelled' => 'Cancelled'] as $v => $l)<option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>@endforeach
  </select>
  <button type="submit" class="btn btn-primary">Filter</button>
  @if (request('search') || request('status'))<a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Clear</a>@endif
</form>

@if ($payments->isEmpty())
  <x-ui.empty icon="wallet" title="No payments found" />
@else
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>User</th><th class="hidden sm:table-cell">Plan</th><th>Amount</th><th class="hidden lg:table-cell">Phone</th><th class="hidden lg:table-cell">Receipt</th><th>Status</th><th class="hidden md:table-cell">Date</th></tr></thead>
      <tbody>
        @foreach ($payments as $payment)
          @php
            $badge = match ($payment->status) { 'paid' => 'badge-success', 'pending' => 'badge-warning', 'failed' => 'badge-danger', default => 'badge-neutral' };
            $dot   = match ($payment->status) { 'paid' => 'bg-emerald-500', 'pending' => 'bg-amber-400', 'failed' => 'bg-red-500', default => 'bg-muted' };
          @endphp
          <tr>
            <td><p class="font-semibold text-fg">{{ $payment->user?->name ?? '—' }}</p><p class="text-xs text-muted">{{ $payment->user?->email }}</p></td>
            <td class="hidden sm:table-cell">{{ $payment->package?->name ?? '—' }}</td>
            <td class="whitespace-nowrap font-semibold text-fg">KES {{ number_format($payment->amount) }}</td>
            <td class="hidden lg:table-cell">{{ $payment->phone }}</td>
            <td class="hidden font-mono text-xs lg:table-cell">{{ $payment->mpesa_receipt_number ?? '—' }}</td>
            <td>
              <span class="badge {{ $badge }} capitalize"><span class="size-1.5 rounded-full {{ $dot }}"></span>{{ $payment->status }}</span>
              @if ($payment->result_desc && $payment->status !== 'paid')<p class="mt-1 max-w-[12rem] truncate text-[11px] text-muted" title="{{ $payment->result_desc }}">{{ $payment->result_desc }}</p>@endif
            </td>
            <td class="hidden whitespace-nowrap md:table-cell">{{ $payment->created_at->format('M j, Y') }}<br><span class="text-xs text-muted">{{ $payment->created_at->format('H:i') }}</span></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endif
@if ($payments->hasPages())<div>{{ $payments->links() }}</div>@endif
@endsection
