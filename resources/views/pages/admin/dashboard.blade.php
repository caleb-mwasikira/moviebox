@extends('layouts.admin')

@section('admin-title', 'Dashboard')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const css = getComputedStyle(document.documentElement);
  const brand = css.getPropertyValue('--c-brand').trim() || '#7c63ff';
  const muted = css.getPropertyValue('--c-muted').trim() || '#8b8ba3';
  const grid  = css.getPropertyValue('--c-line').trim() || '#262636';

  new Chart(document.getElementById('signupsChart'), {
    type: 'bar',
    data: {
      labels: @json($signupLabels),
      datasets: [{ data: @json($signupData), backgroundColor: brand, borderRadius: 4, maxBarThickness: 18 }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { callbacks: { label: (i) => ` ${i.raw} signup${i.raw !== 1 ? 's' : ''}` } } },
      scales: {
        x: { grid: { display: false }, ticks: { color: muted, font: { size: 10 }, maxRotation: 45, maxTicksLimit: 10 } },
        y: { beginAtZero: true, grid: { color: grid }, border: { display: false }, ticks: { color: muted, font: { size: 10 }, precision: 0 } }
      }
    }
  });
});
</script>
@endpush

@section('admin-content')
<div>
  <h2 class="page-title">Welcome back, {{ Str::before(auth()->user()->name, ' ') }}</h2>
  <p class="mt-1 text-sm text-muted">Here's what's happening on {{ config('app.name') }} today.</p>
</div>

<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
  @can('view movies')
    <a href="{{ route('admin.movies.index') }}" class="block transition hover:-translate-y-0.5"><x-ui.stat label="Movies" :value="$movieCount" icon="film" /></a>
  @else
    <x-ui.stat label="Movies" :value="$movieCount" icon="film" />
  @endcan
  @can('view series')
    <a href="{{ route('admin.series.index') }}" class="block transition hover:-translate-y-0.5"><x-ui.stat label="Series" :value="$seriesCount" icon="tv" tone="info" /></a>
  @else
    <x-ui.stat label="Series" :value="$seriesCount" icon="tv" tone="info" />
  @endcan
  <x-ui.stat label="Published" :value="$published" icon="check-circle" tone="success" />
  <x-ui.stat label="Hidden" :value="$hidden" icon="eye-off" tone="warning" />
</div>

<div class="grid gap-4 sm:grid-cols-3">
  <a href="{{ route('admin.users.index') }}" class="block transition hover:-translate-y-0.5"><x-ui.stat label="Total users" :value="number_format($userCount)" icon="users" /></a>
  <x-ui.stat label="Active subscribers" :value="number_format($activeSubscribers)" icon="crown" tone="success" />
  <a href="{{ route('admin.users.index') }}" class="block transition hover:-translate-y-0.5"><x-ui.stat label="Banned" :value="number_format($bannedCount)" icon="ban" tone="danger" /></a>
</div>

<div class="grid gap-6 lg:grid-cols-2">
  <div class="card card-pad min-w-0">
    <div class="mb-5 flex items-center justify-between">
      <h3 class="card-title">Signups — last 30 days</h3>
      <span class="badge badge-brand tabular-nums">{{ number_format(array_sum($signupData)) }} total</span>
    </div>
    <div class="relative h-56"><canvas id="signupsChart"></canvas></div>
  </div>

  <div class="card card-pad">
    <div class="mb-5 flex items-center justify-between">
      <h3 class="card-title">Most watched</h3>
      <a href="{{ route('admin.analytics') }}" class="text-xs font-semibold text-brand hover:underline">Full report →</a>
    </div>
    @if ($mostWatched->isEmpty())
      <x-ui.empty icon="chart" title="No watch data yet" class="!py-10" />
    @else
      @php $maxViews = $mostWatched->first()['views']; @endphp
      <ol class="space-y-4">
        @foreach ($mostWatched as $i => $item)
          <li class="flex items-center gap-3">
            <span class="w-4 shrink-0 text-right text-xs font-bold text-muted">{{ $i + 1 }}</span>
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

<div class="flex flex-wrap gap-3">
  <a href="{{ route('admin.movies.create') }}" class="btn btn-primary"><x-ui.icon name="plus" class="size-4" /> Upload movie</a>
  <a href="{{ route('admin.series.create') }}" class="btn btn-outline"><x-ui.icon name="plus" class="size-4" /> Add series</a>
  <a href="{{ route('admin.analytics') }}" class="btn btn-secondary"><x-ui.icon name="chart" class="size-4" /> View full analytics</a>
</div>
@endsection
