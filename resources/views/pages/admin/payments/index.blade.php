@extends('layouts.admin')

@section('admin-title', 'Payments')

@section('admin-content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
  <div>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Payments</h2>
    <p class="mt-1 text-gray-500 dark:text-gray">{{ $payments->total() }} {{ Str::plural('transaction', $payments->total()) }}</p>
  </div>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
  <div class="border border-gray-200 dark:border-gray-600 p-4">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Total Revenue</p>
    <p class="text-xl font-black text-gray-900 dark:text-white">KES {{ number_format($summary['total_paid'], 0) }}</p>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-4">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Successful</p>
    <p class="text-xl font-black text-green-600">{{ number_format($summary['count_paid']) }}</p>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-4">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Pending</p>
    <p class="text-xl font-black text-yellow-500">{{ number_format($summary['count_pending']) }}</p>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-4">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Failed</p>
    <p class="text-xl font-black text-red-500">{{ number_format($summary['count_failed']) }}</p>
  </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.payments.index') }}" class="mb-6 flex flex-wrap gap-2">
  <input type="text" name="search" value="{{ request('search') }}"
    placeholder="Search user, phone, receipt…"
    class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-accent focus:outline-none flex-1 min-w-48">
  <select name="status"
    class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-accent focus:outline-none">
    <option value="">All statuses</option>
    <option value="paid"      {{ request('status') === 'paid'      ? 'selected' : '' }}>Paid</option>
    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
    <option value="failed"    {{ request('status') === 'failed'    ? 'selected' : '' }}>Failed</option>
    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
  </select>
  <button type="submit"
    class="px-4 py-2 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors shrink-0">
    Filter
  </button>
  @if(request('search') || request('status'))
    <a href="{{ route('admin.payments.index') }}"
      class="px-4 py-2 text-xs font-bold uppercase border border-gray-300 dark:border-gray-600 text-gray-500 hover:text-accent transition-colors shrink-0">
      Clear
    </a>
  @endif
</form>

@if ($payments->isEmpty())
  <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
    <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No payments found</p>
  </div>
@else

  {{-- Mobile cards --}}
  <div class="lg:hidden border border-gray-200 dark:border-gray-600 divide-y divide-gray-200 dark:divide-gray-600">
    @foreach ($payments as $payment)
      @php
        $statusColor = match($payment->status) {
          'paid'      => 'text-green-600',
          'pending'   => 'text-yellow-500',
          'failed'    => 'text-red-500',
          default     => 'text-gray-400',
        };
        $dotColor = match($payment->status) {
          'paid'      => 'bg-green-500',
          'pending'   => 'bg-yellow-400',
          'failed'    => 'bg-red-500',
          default     => 'bg-gray-400',
        };
      @endphp
      <div class="p-4">
        <div class="flex items-start justify-between gap-x-3 mb-2">
          <div class="min-w-0">
            <p class="font-bold text-sm text-gray-900 dark:text-white">{{ $payment->user?->name ?? '—' }}</p>
            <p class="text-xs text-gray-500">{{ $payment->user?->email }}</p>
          </div>
          <span class="inline-flex items-center gap-x-1.5 text-[10px] font-bold uppercase {{ $statusColor }} shrink-0">
            <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} inline-block"></span>
            {{ $payment->status }}
          </span>
        </div>
        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
          <span>{{ $payment->package?->name ?? '—' }}</span>
          <span class="font-bold text-gray-900 dark:text-white">KES {{ number_format($payment->amount) }}</span>
          <span>{{ $payment->phone }}</span>
          @if ($payment->mpesa_receipt_number)
            <span class="font-mono text-accent">{{ $payment->mpesa_receipt_number }}</span>
          @endif
          <span>{{ $payment->created_at->format('M j, Y H:i') }}</span>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Desktop table --}}
  <div class="hidden lg:block border border-gray-200 dark:border-gray-600 overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">User</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Plan</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Amount</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Phone</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Receipt</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Status</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Date</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
        @foreach ($payments as $payment)
          @php
            $statusColor = match($payment->status) {
              'paid'      => 'text-green-600',
              'pending'   => 'text-yellow-500',
              'failed'    => 'text-red-500',
              default     => 'text-gray-400',
            };
            $dotColor = match($payment->status) {
              'paid'      => 'bg-green-500',
              'pending'   => 'bg-yellow-400',
              'failed'    => 'bg-red-500',
              default     => 'bg-gray-400',
            };
          @endphp
          <tr>
            <td class="px-5 py-4">
              <p class="font-bold text-gray-900 dark:text-white whitespace-nowrap">{{ $payment->user?->name ?? '—' }}</p>
              <p class="text-xs text-gray-500 mt-0.5">{{ $payment->user?->email }}</p>
            </td>
            <td class="px-5 py-4 text-gray-500 whitespace-nowrap">{{ $payment->package?->name ?? '—' }}</td>
            <td class="px-5 py-4 font-bold text-gray-900 dark:text-white whitespace-nowrap">
              KES {{ number_format($payment->amount) }}
            </td>
            <td class="px-5 py-4 text-gray-500 font-mono text-xs">{{ $payment->phone }}</td>
            <td class="px-5 py-4 font-mono text-xs text-accent">{{ $payment->mpesa_receipt_number ?? '—' }}</td>
            <td class="px-5 py-4">
              <span class="inline-flex items-center gap-x-1.5 text-xs font-bold uppercase {{ $statusColor }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} inline-block"></span>
                {{ $payment->status }}
              </span>
              @if ($payment->result_desc && $payment->status !== 'paid')
                <p class="text-[11px] text-gray-400 mt-0.5 max-w-[180px] truncate" title="{{ $payment->result_desc }}">
                  {{ $payment->result_desc }}
                </p>
              @endif
            </td>
            <td class="px-5 py-4 text-gray-500 whitespace-nowrap text-xs">
              {{ $payment->created_at->format('M j, Y') }}<br>
              <span class="text-gray-400">{{ $payment->created_at->format('H:i') }}</span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

@endif

@if ($payments->hasPages())
  <div class="mt-6">{{ $payments->links() }}</div>
@endif
@endsection
