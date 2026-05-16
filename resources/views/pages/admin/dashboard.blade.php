@extends('layouts.admin')

@section('admin-title', 'Dashboard')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  new Chart(document.getElementById('signupsChart'), {
    type: 'bar',
    data: {
      labels: @json($signupLabels),
      datasets: [{
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
      plugins: { legend: { display: false }, tooltip: {
        callbacks: { label: (i) => ` ${i.raw} signup${i.raw !== 1 ? 's' : ''}` }
      }},
      scales: {
        x: { grid: { color: 'rgba(156,163,175,0.1)' }, ticks: { color: '#9ca3af', font: { size: 10 }, maxRotation: 45, maxTicksLimit: 10 } },
        y: { beginAtZero: true, grid: { color: 'rgba(156,163,175,0.1)' }, ticks: { color: '#9ca3af', font: { size: 10 }, precision: 0 } }
      }
    }
  });
});
</script>
@endpush

@section('admin-content')

<div class="mb-8">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Dashboard</h2>
  <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm">Welcome back, {{ auth()->user()->name }}.</p>
</div>

{{-- ── Content & user stats ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
  <div class="border border-gray-200 dark:border-gray-600 p-5">
    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $movieCount }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Movies</div>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-5">
    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $seriesCount }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Series</div>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-5">
    <div class="text-3xl font-bold text-accent mb-1">{{ $published }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Published</div>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-5">
    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $hidden }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Hidden</div>
  </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
  <a href="{{ route('admin.users.index') }}" class="border border-gray-200 dark:border-gray-600 p-5 hover:border-accent transition-colors">
    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ number_format($userCount) }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Total Users</div>
  </a>
  <div class="border border-gray-200 dark:border-gray-600 p-5">
    <div class="text-3xl font-bold text-accent mb-1">{{ number_format($activeSubscribers) }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Active Subscribers</div>
  </div>
  <a href="{{ route('admin.users.index') }}" class="border border-gray-200 dark:border-gray-600 p-5 hover:border-accent transition-colors col-span-2 lg:col-span-1">
    <div class="text-3xl font-bold text-red-500 mb-1">{{ number_format($bannedCount) }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Banned</div>
  </a>
</div>

{{-- ── Signups chart + Most watched ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">

  {{-- Signups over time --}}
  <div class="border border-gray-200 dark:border-gray-600 p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-xs font-bold uppercase text-gray-500 tracking-widest">Signups — Last 30 Days</h3>
      <span class="text-xs font-bold text-gray-900 dark:text-white tabular-nums">{{ number_format(array_sum($signupData)) }} total</span>
    </div>
    <div style="height:200px;">
      <canvas id="signupsChart"></canvas>
    </div>
  </div>

  {{-- Most watched --}}
  <div class="border border-gray-200 dark:border-gray-600 p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-xs font-bold uppercase text-gray-500 tracking-widest">Most Watched</h3>
      <a href="{{ route('admin.analytics') }}" class="text-xs font-bold text-accent hover:underline">Full report</a>
    </div>
    @if ($mostWatched->isEmpty())
      <p class="text-sm text-gray-400">No watch data yet.</p>
    @else
      @php $maxViews = $mostWatched->first()['views']; @endphp
      <div class="flex flex-col gap-y-3">
        @foreach ($mostWatched as $i => $item)
          <div class="flex items-center gap-x-3">
            <span class="text-xs font-bold text-gray-400 w-4 shrink-0 text-right">{{ $i + 1 }}</span>
            <div class="flex-1 min-w-0">
              <div class="flex items-baseline justify-between gap-x-2 mb-0.5">
                <span class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $item['title'] }}</span>
                <span class="text-xs text-gray-400 shrink-0 tabular-nums">{{ number_format($item['views']) }}</span>
              </div>
              <div class="h-1 bg-gray-100 dark:bg-gray-600 rounded-full overflow-hidden">
                <div class="h-full rounded-full {{ $item['type'] === 'Movie' ? 'bg-accent' : 'bg-blue-500' }}"
                  style="width:{{ $maxViews > 0 ? round(($item['views'] / $maxViews) * 100) : 0 }}%"></div>
              </div>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wide shrink-0 {{ $item['type'] === 'Movie' ? 'text-accent' : 'text-blue-400' }}">
              {{ $item['type'] }}
            </span>
          </div>
        @endforeach
      </div>
    @endif
  </div>

</div>

{{-- ── Quick actions ── --}}
<div class="flex flex-wrap gap-4">
  <a href="{{ route('admin.movies.create') }}"
    class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
    + Upload Movie
  </a>
  <a href="{{ route('admin.series.create') }}"
    class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 border border-accent hover:bg-accent/10">
    + Add Series
  </a>
  <a href="{{ route('admin.analytics') }}"
    class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600 hover:border-accent hover:text-accent">
    View Full Analytics
  </a>
</div>

@endsection
