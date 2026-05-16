@extends('layouts.admin')

@section('admin-title', 'Analytics')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const gridColor = 'rgba(156,163,175,0.1)';
  const tickColor = '#9ca3af';
  const tickFont  = { size: 11 };

  // ── Signups chart ─────────────────────────────────────────────
  new Chart(document.getElementById('signupsChart'), {
    type: 'bar',
    data: {
      labels: @json($signupLabels),
      datasets: [{
        label: 'Signups',
        data: @json($signupData),
        backgroundColor: 'rgba(101,76,255,0.7)',
        borderColor: 'rgba(101,76,255,1)',
        borderWidth: 1,
        borderRadius: 2,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { color: gridColor }, ticks: { color: tickColor, font: tickFont, maxRotation: 45 } },
        y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: tickColor, font: tickFont, precision: 0 } }
      }
    }
  });

  // ── Revenue chart ─────────────────────────────────────────────
  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
      labels: @json($revenueLabels),
      datasets: [{
        label: '{{ $currency }}',
        data: @json($revenueData),
        borderColor: 'rgba(101,76,255,1)',
        backgroundColor: 'rgba(101,76,255,0.08)',
        borderWidth: 2,
        pointRadius: 3,
        pointBackgroundColor: 'rgba(101,76,255,1)',
        fill: true,
        tension: 0.3,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { color: gridColor }, ticks: { color: tickColor, font: tickFont, maxRotation: 45 } },
        y: {
          beginAtZero: true,
          grid: { color: gridColor },
          ticks: {
            color: tickColor,
            font: tickFont,
            callback: (v) => '{{ $currency }} ' + v.toLocaleString(),
          }
        }
      }
    }
  });
});
</script>
@endpush

@section('admin-content')

<div class="mb-8">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Analytics</h2>
  <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm">Platform overview, revenue and content performance.</p>
</div>

{{-- ── Quick stats ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
  <div class="border border-gray-200 dark:border-gray-600 p-5">
    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($totalUsers) }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Total Users</div>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-5">
    <div class="text-3xl font-bold text-accent mb-1">+{{ number_format($newThisMonth) }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">New This Month</div>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-5">
    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($activeTotal) }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Active Subscribers</div>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-5">
    @php $signupSum = array_sum($signupData); @endphp
    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($signupSum) }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Signups (30 days)</div>
  </div>
</div>

{{-- ── Revenue stats ── --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
  <div class="border border-gray-200 dark:border-gray-600 p-5">
    <div class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-2">Total Revenue</div>
    <div class="text-2xl font-bold text-gray-900 dark:text-white">
      {{ $currency }} {{ number_format($revenueAllTime) }}
    </div>
    <div class="text-xs text-gray-400 mt-1">all time &middot; paid accounts only</div>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-5">
    <div class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-2">This Month</div>
    <div class="text-2xl font-bold text-accent">
      {{ $currency }} {{ number_format($revenueThisMonth) }}
    </div>
    <div class="text-xs text-gray-400 mt-1">{{ now()->format('F Y') }}</div>
  </div>
</div>

{{-- ── Revenue chart ── --}}
<div class="border border-gray-200 dark:border-gray-600 p-6 mb-8">
  <h3 class="text-xs font-bold uppercase text-gray-500 tracking-widest mb-5">
    Revenue — Last 12 Months
    <span class="normal-case font-normal text-gray-400">({{ $currency }})</span>
  </h3>
  <div style="height:220px;">
    <canvas id="revenueChart"></canvas>
  </div>
</div>

{{-- ── Revenue by plan + signups chart ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

  {{-- Revenue by plan --}}
  <div class="border border-gray-200 dark:border-gray-600 p-6">
    <h3 class="text-xs font-bold uppercase text-gray-500 tracking-widest mb-5">Revenue by Plan (All Time)</h3>
    @if ($revenueByPlan->isEmpty())
      <p class="text-sm text-gray-400">No sales yet.</p>
    @else
      <div class="flex flex-col gap-y-4">
        @foreach ($revenueByPlan as $row)
          @php $pct = $revenueAllTime > 0 ? round(($row->revenue / $revenueAllTime) * 100) : 0; @endphp
          <div>
            <div class="flex justify-between items-baseline text-sm mb-1 gap-x-2">
              <span class="font-bold text-gray-900 dark:text-white truncate">{{ $row->name }}</span>
              <span class="shrink-0 text-gray-500 tabular-nums">
                {{ $currency }} {{ number_format($row->revenue) }}
                <span class="text-xs text-gray-400">&middot; {{ number_format($row->sales) }} {{ Str::plural('sale', $row->sales) }}</span>
              </span>
            </div>
            <div class="h-1.5 bg-gray-100 dark:bg-gray-600 rounded-full overflow-hidden">
              <div class="h-full bg-accent rounded-full" style="width:{{ $pct }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Signups chart --}}
  <div class="border border-gray-200 dark:border-gray-600 p-6">
    <h3 class="text-xs font-bold uppercase text-gray-500 tracking-widest mb-5">Signups — Last 30 Days</h3>
    <div style="height:220px;">
      <canvas id="signupsChart"></canvas>
    </div>
  </div>

</div>

{{-- ── Active subscribers by package + most watched ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

  {{-- Subscribers by package --}}
  <div class="border border-gray-200 dark:border-gray-600 p-6">
    <h3 class="text-xs font-bold uppercase text-gray-500 tracking-widest mb-5">Active Subscribers by Plan</h3>
    @if ($byPackage->isEmpty())
      <p class="text-sm text-gray-400">No active subscriptions.</p>
    @else
      <div class="flex flex-col gap-y-3">
        @foreach ($byPackage as $row)
          @php $pct = $activeTotal > 0 ? round(($row->count / $activeTotal) * 100) : 0; @endphp
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span class="font-bold text-gray-900 dark:text-white">{{ $row->package?->name ?? 'Unknown Plan' }}</span>
              <span class="text-gray-500">{{ number_format($row->count) }}
                <span class="text-xs text-gray-400">({{ $pct }}%)</span>
              </span>
            </div>
            <div class="h-1.5 bg-gray-100 dark:bg-gray-600 rounded-full overflow-hidden">
              <div class="h-full bg-accent rounded-full" style="width:{{ $pct }}%"></div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Most watched --}}
  <div class="border border-gray-200 dark:border-gray-600 p-6">
    <h3 class="text-xs font-bold uppercase text-gray-500 tracking-widest mb-5">Most Watched Content</h3>
    @if ($mostWatched->isEmpty())
      <p class="text-sm text-gray-400">No watch data yet.</p>
    @else
      @php $maxViews = $mostWatched->first()['views']; @endphp
      <div class="flex flex-col gap-y-3">
        @foreach ($mostWatched as $i => $item)
          <div class="flex items-center gap-x-3">
            <span class="text-xs font-bold text-gray-400 w-5 shrink-0 text-right">{{ $i + 1 }}</span>
            <div class="flex-1 min-w-0">
              <div class="flex items-baseline justify-between gap-x-2 mb-1">
                <span class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $item['title'] }}</span>
                <span class="text-xs text-gray-400 shrink-0">{{ number_format($item['views']) }}</span>
              </div>
              <div class="h-1 bg-gray-100 dark:bg-gray-600 rounded-full overflow-hidden">
                <div class="h-full rounded-full {{ $item['type'] === 'Movie' ? 'bg-accent' : 'bg-blue-500' }}"
                  style="width:{{ $maxViews > 0 ? round(($item['views'] / $maxViews) * 100) : 0 }}%">
                </div>
              </div>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wide shrink-0
              {{ $item['type'] === 'Movie' ? 'text-accent' : 'text-blue-400' }}">
              {{ $item['type'] }}
            </span>
          </div>
        @endforeach
      </div>
    @endif
  </div>

</div>

@endsection
