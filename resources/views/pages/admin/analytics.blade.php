@extends('layouts.admin')

@section('admin-title', 'Analytics')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const css = getComputedStyle(document.documentElement);
  const brand = css.getPropertyValue('--c-brand').trim() || '#7c63ff';
  const muted = css.getPropertyValue('--c-muted').trim() || '#8b8ba3';
  const gridColor = css.getPropertyValue('--c-line').trim() || '#262636';
  const tickFont  = { size: 11 };

  // ── Signups chart ──
  new Chart(document.getElementById('signupsChart'), {
    type: 'bar',
    data: {
      labels: @json($signupLabels),
      datasets: [{ label: 'Signups', data: @json($signupData), backgroundColor: brand, borderRadius: 4, maxBarThickness: 18 }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { color: muted, font: tickFont, maxRotation: 45 } },
        y: { beginAtZero: true, grid: { color: gridColor }, border: { display: false }, ticks: { color: muted, font: tickFont, precision: 0 } }
      }
    }
  });

  // ── Revenue chart ──
  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
      labels: @json($revenueLabels),
      datasets: [{
        label: '{{ $currency }}',
        data: @json($revenueData),
        borderColor: brand,
        backgroundColor: brand + '22',
        borderWidth: 2,
        pointRadius: 3,
        pointBackgroundColor: brand,
        fill: true,
        tension: 0.35,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { color: muted, font: tickFont, maxRotation: 45 } },
        y: {
          beginAtZero: true,
          grid: { color: gridColor },
          border: { display: false },
          ticks: { color: muted, font: tickFont, callback: (v) => '{{ $currency }} ' + v.toLocaleString() }
        }
      }
    }
  });
});
</script>
@endpush

@section('admin-content')
<div>
  <h2 class="page-title">Analytics</h2>
  <p class="mt-1 text-sm text-muted">Platform overview, revenue and content performance.</p>
</div>

@php $signupSum = array_sum($signupData); @endphp
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
  <x-ui.stat label="Total users" :value="number_format($totalUsers)" icon="users" />
  <x-ui.stat label="New this month" :value="'+' . number_format($newThisMonth)" icon="user" tone="success" />
  <x-ui.stat label="Active subscribers" :value="number_format($activeTotal)" icon="crown" tone="info" />
  <x-ui.stat label="Signups (30 days)" :value="number_format($signupSum)" icon="activity" tone="warning" />
</div>

<div class="grid gap-4 sm:grid-cols-2">
  <x-ui.stat label="Total revenue" :value="$currency . ' ' . number_format($revenueAllTime)" icon="wallet" hint="All time · paid accounts only" />
  <x-ui.stat label="This month" :value="$currency . ' ' . number_format($revenueThisMonth)" icon="chart" tone="success" :hint="now()->format('F Y')" />
</div>

<div class="card card-pad">
  <h3 class="card-title mb-5">Revenue — last 12 months <span class="font-normal text-muted">({{ $currency }})</span></h3>
  <div class="h-64"><canvas id="revenueChart"></canvas></div>
</div>

<div class="grid gap-6 lg:grid-cols-2">
  <div class="card card-pad">
    <h3 class="card-title mb-5">Revenue by plan (all time)</h3>
    @if ($revenueByPlan->isEmpty())
      <x-ui.empty icon="wallet" title="No sales yet" class="!py-10" />
    @else
      <div class="space-y-5">
        @foreach ($revenueByPlan as $row)
          @php $pct = $revenueAllTime > 0 ? round(($row->revenue / $revenueAllTime) * 100) : 0; @endphp
          <div>
            <div class="mb-1.5 flex items-baseline justify-between gap-2 text-sm">
              <span class="truncate font-semibold text-fg">{{ $row->name }}</span>
              <span class="shrink-0 tabular-nums text-muted">{{ $currency }} {{ number_format($row->revenue) }} <span class="text-xs">· {{ number_format($row->sales) }} {{ Str::plural('sale', $row->sales) }}</span></span>
            </div>
            <div class="progress"><span style="width:{{ $pct }}%"></span></div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <div class="card card-pad">
    <h3 class="card-title mb-5">Signups — last 30 days</h3>
    <div class="h-56"><canvas id="signupsChart"></canvas></div>
  </div>
</div>

<div class="grid gap-6 lg:grid-cols-2">
  <div class="card card-pad">
    <h3 class="card-title mb-5">Active subscribers by plan</h3>
    @if ($byPackage->isEmpty())
      <x-ui.empty icon="users" title="No active subscriptions" class="!py-10" />
    @else
      <div class="space-y-4">
        @foreach ($byPackage as $row)
          @php $pct = $activeTotal > 0 ? round(($row->count / $activeTotal) * 100) : 0; @endphp
          <div>
            <div class="mb-1.5 flex justify-between text-sm">
              <span class="font-semibold text-fg">{{ $row->package?->name ?? 'Unknown plan' }}</span>
              <span class="text-muted">{{ number_format($row->count) }} <span class="text-xs">({{ $pct }}%)</span></span>
            </div>
            <div class="progress"><span style="width:{{ $pct }}%"></span></div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <div class="card card-pad">
    <h3 class="card-title mb-5">Most watched content</h3>
    @if ($mostWatched->isEmpty())
      <x-ui.empty icon="chart" title="No watch data yet" class="!py-10" />
    @else
      @php $maxViews = $mostWatched->first()['views']; @endphp
      <ol class="space-y-4">
        @foreach ($mostWatched as $i => $item)
          <li class="flex items-center gap-3">
            <span class="w-5 shrink-0 text-right text-xs font-bold text-muted">{{ $i + 1 }}</span>
            <div class="min-w-0 flex-1">
              <div class="mb-1 flex items-baseline justify-between gap-2">
                <span class="truncate text-sm font-semibold text-fg">{{ $item['title'] }}</span>
                <span class="shrink-0 text-xs tabular-nums text-muted">{{ number_format($item['views']) }}</span>
              </div>
              <div class="progress"><span class="{{ $item['type'] === 'Movie' ? '' : '!bg-sky-500' }}" style="width:{{ $maxViews > 0 ? round(($item['views'] / $maxViews) * 100) : 0 }}%"></span></div>
            </div>
            <span class="badge {{ $item['type'] === 'Movie' ? 'badge-brand' : 'badge-info' }}">{{ $item['type'] }}</span>
          </li>
        @endforeach
      </ol>
    @endif
  </div>
</div>
@endsection
