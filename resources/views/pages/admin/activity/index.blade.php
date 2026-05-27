@extends('layouts.admin')

@section('admin-title', 'Activity Log')

@section('admin-content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
  <div>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Activity Log</h2>
    <p class="mt-1 text-gray-500 dark:text-gray">Staff content actions — uploads, edits, deletions</p>
  </div>
</div>

{{-- ── Summary cards ── --}}
@if (count($summary))
@php
  $summaryGroups = [
      ['label' => 'Movies',   'type' => 'Movie',   'icon' => 'M15 10l4.553-2.07A1 1 0 0121 8.868V15a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z'],
      ['label' => 'Series',   'type' => 'Series',  'icon' => 'M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z'],
      ['label' => 'Episodes', 'type' => 'Episode', 'icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
  ];
  $summaryActions = [
      'created'       => ['label' => 'Uploaded', 'color' => 'text-green-500',  'bg' => 'bg-green-100 dark:bg-green-900/30'],
      'updated'       => ['label' => 'Edited',   'color' => 'text-blue-500',   'bg' => 'bg-blue-100 dark:bg-blue-900/30'],
      'deleted'       => ['label' => 'Deleted',  'color' => 'text-orange-500', 'bg' => 'bg-orange-100 dark:bg-orange-900/30'],
      'force_deleted' => ['label' => 'Purged',   'color' => 'text-red-500',    'bg' => 'bg-red-100 dark:bg-red-900/30'],
      'restored'      => ['label' => 'Restored', 'color' => 'text-teal-500',   'bg' => 'bg-teal-100 dark:bg-teal-900/30'],
  ];

  // Aggregate counts across all staff members
  $allCounts = [];
  foreach ($summary as $row) {
      foreach ($row['counts'] as $key => $cnt) {
          $allCounts[$key] = ($allCounts[$key] ?? 0) + $cnt;
      }
  }
@endphp

{{-- ── Stats cards (aggregate or user-specific) ── --}}
@php
  $statCounts = $filterUser ? ($summary[(int)$filterUser]['counts'] ?? []) : $allCounts;
  $statLabel  = $filterUser ? ($summary[(int)$filterUser]['name'] ?? null) : null;
@endphp
@if ($statLabel)
  <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">{{ $statLabel }}</p>
@endif
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
  @foreach ($summaryGroups as $g)
    @php
      $typeTotal = 0;
      foreach (array_keys($summaryActions) as $a) {
          $typeTotal += $statCounts["{$a}|{$g['type']}"] ?? 0;
      }
    @endphp
    <div class="border border-gray-200 dark:border-gray-700 p-4">
      <div class="flex items-center gap-x-2 mb-2">
        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="{{ $g['icon'] }}"/>
        </svg>
        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ $g['label'] }}</span>
      </div>
      <p class="text-3xl font-black text-gray-900 dark:text-white mb-3">{{ $typeTotal }}</p>
      <div class="flex flex-col gap-y-1">
        @foreach ($summaryActions as $action => $meta)
          @php $cnt = $statCounts["{$action}|{$g['type']}"] ?? 0; @endphp
          @if ($cnt > 0)
            <div class="flex items-center justify-between text-xs">
              <span class="text-gray-400">{{ $meta['label'] }}</span>
              <span class="font-bold {{ $meta['color'] }}">{{ $cnt }}</span>
            </div>
          @endif
        @endforeach
        @if ($typeTotal === 0)
          <p class="text-xs text-gray-400">No actions yet</p>
        @endif
      </div>
    </div>
  @endforeach
</div>

@if ($filterUser)
<div class="mb-12">
  <h3 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">Summary by Staff Member</h3>
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach ($summary as $uid => $row)
      @php
        $memberTotal = array_sum($row['counts']);
        $isNumericUid = is_numeric($uid);
      @endphp
      <div class="border border-gray-200 dark:border-gray-700 p-5">
        {{-- Card header --}}
        <div class="flex items-start justify-between gap-x-3 mb-4">
          <div class="min-w-0">
            @if ($isNumericUid)
              <a href="{{ route('admin.activity.index', ['user_id' => $uid]) }}"
                class="font-bold text-gray-900 dark:text-white hover:text-accent transition-colors truncate block">
                {{ $row['name'] }}
              </a>
            @else
              <span class="font-bold text-gray-500 truncate block">{{ $row['name'] }}</span>
            @endif
            <span class="text-xs text-gray-400">{{ $memberTotal }} total action{{ $memberTotal !== 1 ? 's' : '' }}</span>
          </div>
          @if ($isNumericUid)
            <a href="{{ route('admin.activity.index', ['user_id' => $uid]) }}"
              class="shrink-0 text-[10px] font-bold uppercase text-accent hover:text-accent/70 transition-colors whitespace-nowrap">
              View →
            </a>
          @endif
        </div>

        {{-- Per-type breakdown --}}
        <div class="flex flex-col gap-y-3">
          @foreach ($summaryGroups as $g)
            @php
              $typeTotal = 0;
              foreach (array_keys($summaryActions) as $a) {
                  $typeTotal += $row['counts']["{$a}|{$g['type']}"] ?? 0;
              }
            @endphp
            @if ($typeTotal > 0)
              <div>
                <div class="flex items-center gap-x-1.5 mb-1.5">
                  <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $g['icon'] }}"/>
                  </svg>
                  <span class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ $g['label'] }}</span>
                  <span class="ml-auto text-xs font-bold text-gray-900 dark:text-white">{{ $typeTotal }}</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                  @foreach ($summaryActions as $action => $meta)
                    @php $cnt = $row['counts']["{$action}|{$g['type']}"] ?? 0; @endphp
                    @if ($cnt > 0)
                      <span class="inline-flex items-center gap-x-1 text-[10px] font-bold uppercase px-2 py-0.5 {{ $meta['bg'] }} {{ $meta['color'] }}">
                        {{ $meta['label'] }} <span class="opacity-70">{{ $cnt }}</span>
                      </span>
                    @endif
                  @endforeach
                </div>
              </div>
            @endif
          @endforeach
        </div>

        @if ($memberTotal === 0)
          <p class="text-xs text-gray-400">No actions recorded.</p>
        @endif
      </div>
    @endforeach
  </div>
</div>
@endif {{-- !$filterUser --}}
@endif {{-- count($summary) --}}

{{-- ── Recent activity feed ── --}}
<div>
  <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
    <h3 class="text-sm font-bold uppercase tracking-widest text-gray-400">Recent Activity</h3>

    {{-- Filter by user --}}
    <form method="GET" action="{{ route('admin.activity.index') }}" class="flex gap-x-2">
      <select name="user_id"
        class="text-sm px-3 py-1.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:border-accent focus:outline-none">
        <option value="">All staff</option>
        @foreach ($staffUsers as $u)
          <option value="{{ $u->id }}" @selected($filterUser == $u->id)>{{ $u->name }}</option>
        @endforeach
      </select>
      <button type="submit"
        class="px-3 py-1.5 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors">Filter</button>
      @if ($filterUser)
        <a href="{{ route('admin.activity.index') }}"
          class="px-3 py-1.5 text-xs font-bold uppercase border border-gray-300 dark:border-gray-600 text-gray-500 hover:text-accent transition-colors">Clear</a>
      @endif
    </form>
  </div>

  @if ($recent->isEmpty())
    <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
      <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No activity recorded yet</p>
    </div>
  @else
    <div class="border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-800">
      @foreach ($recent as $log)
        <div class="flex items-start gap-x-4 px-4 py-3">
          {{-- Action badge --}}
          <span class="shrink-0 mt-0.5 text-[10px] font-bold uppercase tracking-widest px-2 py-0.5
            @if($log->action === 'created') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
            @elseif($log->action === 'updated') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
            @elseif($log->action === 'deleted') bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400
            @elseif($log->action === 'restored') bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400
            @else bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
            @endif">
            {{ $log->actionLabel() }}
          </span>

          {{-- Subject --}}
          <div class="flex-1 min-w-0">
            <p class="text-sm text-gray-900 dark:text-white font-medium truncate">
              <span class="text-gray-400 font-normal">{{ $log->subject_type }}</span>
              &mdash;
              {{ $log->subject_title }}
            </p>
            <p class="text-xs text-gray-500 mt-0.5">
              by <span class="font-bold text-gray-700 dark:text-gray-300">{{ $log->user_name }}</span>
            </p>
          </div>

          {{-- Time --}}
          <time class="shrink-0 text-xs text-gray-400 whitespace-nowrap" title="{{ $log->created_at->toDateTimeString() }}">
            {{ $log->created_at->diffForHumans() }}
          </time>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $recent->links() }}
    </div>
  @endif
</div>
@endsection
