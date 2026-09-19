@extends('layouts.admin')

@section('admin-title', 'Activity Log')

@section('admin-content')
<x-ui.page-header title="Activity log" subtitle="Staff content actions — uploads, edits, deletions." />

@if (count($summary))
  @php
    $summaryGroups = [
        ['label' => 'Movies',   'type' => 'Movie',   'icon' => 'film'],
        ['label' => 'Series',   'type' => 'Series',  'icon' => 'tv'],
        ['label' => 'Episodes', 'type' => 'Episode', 'icon' => 'play'],
    ];
    $summaryActions = [
        'created'       => ['label' => 'Uploaded', 'badge' => 'badge-success'],
        'updated'       => ['label' => 'Edited',   'badge' => 'badge-info'],
        'deleted'       => ['label' => 'Deleted',  'badge' => 'badge-warning'],
        'force_deleted' => ['label' => 'Purged',   'badge' => 'badge-danger'],
        'restored'      => ['label' => 'Restored', 'badge' => 'badge-brand'],
    ];
    // Aggregate counts across all staff members
    $allCounts = [];
    foreach ($summary as $row) {
        foreach ($row['counts'] as $key => $cnt) {
            $allCounts[$key] = ($allCounts[$key] ?? 0) + $cnt;
        }
    }
    $statCounts = $filterUser ? ($summary[(int)$filterUser]['counts'] ?? []) : $allCounts;
    $statLabel  = $filterUser ? ($summary[(int)$filterUser]['name'] ?? null) : null;
  @endphp

  @if ($statLabel)<p class="text-sm font-semibold text-fg">Showing <span class="text-brand">{{ $statLabel }}</span></p>@endif

  <div class="grid gap-4 sm:grid-cols-3">
    @foreach ($summaryGroups as $g)
      @php
        $typeTotal = 0;
        foreach (array_keys($summaryActions) as $a) { $typeTotal += $statCounts["{$a}|{$g['type']}"] ?? 0; }
      @endphp
      <div class="card card-pad">
        <div class="flex items-center gap-3">
          <span class="grid size-10 place-items-center rounded-xl bg-brand-soft text-brand"><x-ui.icon :name="$g['icon']" /></span>
          <span class="text-sm font-semibold text-fg">{{ $g['label'] }}</span>
          <span class="ml-auto text-3xl font-bold text-fg">{{ $typeTotal }}</span>
        </div>
        <div class="mt-4 flex flex-wrap gap-1.5">
          @foreach ($summaryActions as $action => $meta)
            @php $cnt = $statCounts["{$action}|{$g['type']}"] ?? 0; @endphp
            @if ($cnt > 0)<span class="badge {{ $meta['badge'] }}">{{ $meta['label'] }} <strong>{{ $cnt }}</strong></span>@endif
          @endforeach
          @if ($typeTotal === 0)<p class="text-xs text-muted">No actions yet</p>@endif
        </div>
      </div>
    @endforeach
  </div>

  @if ($filterUser)
    <section class="space-y-3">
      <h2 class="section-title">Summary by staff member</h2>
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($summary as $uid => $row)
          @php $memberTotal = array_sum($row['counts']); $isNumericUid = is_numeric($uid); @endphp
          <div class="card card-pad">
            <div class="mb-4 flex items-start justify-between gap-3">
              <div class="min-w-0">
                @if ($isNumericUid)
                  <a href="{{ route('admin.activity.index', ['user_id' => $uid]) }}" class="block truncate font-semibold text-fg hover:text-brand">{{ $row['name'] }}</a>
                @else
                  <span class="block truncate font-semibold text-fg">{{ $row['name'] }}</span>
                @endif
                <span class="text-xs text-muted">{{ $memberTotal }} total action{{ $memberTotal !== 1 ? 's' : '' }}</span>
              </div>
              @if ($isNumericUid)<a href="{{ route('admin.activity.index', ['user_id' => $uid]) }}" class="text-xs font-semibold text-brand hover:underline">View →</a>@endif
            </div>
            <div class="space-y-3">
              @foreach ($summaryGroups as $g)
                @php
                  $typeTotal = 0;
                  foreach (array_keys($summaryActions) as $a) { $typeTotal += $row['counts']["{$a}|{$g['type']}"] ?? 0; }
                @endphp
                @if ($typeTotal > 0)
                  <div>
                    <div class="mb-1.5 flex justify-between text-xs"><span class="font-semibold text-fg-soft">{{ $g['label'] }}</span><span class="text-muted">{{ $typeTotal }}</span></div>
                    <div class="flex flex-wrap gap-1.5">
                      @foreach ($summaryActions as $action => $meta)
                        @php $cnt = $row['counts']["{$action}|{$g['type']}"] ?? 0; @endphp
                        @if ($cnt > 0)<span class="badge {{ $meta['badge'] }}">{{ $meta['label'] }} {{ $cnt }}</span>@endif
                      @endforeach
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
            @if ($memberTotal === 0)<p class="text-xs text-muted">No actions recorded.</p>@endif
          </div>
        @endforeach
      </div>
    </section>
  @endif
@endif

{{-- Recent activity --}}
<section class="space-y-4">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <h2 class="section-title">Recent activity</h2>
    <form method="GET" action="{{ route('admin.activity.index') }}" class="flex flex-wrap items-center gap-2">
      <select name="user_id" class="select !w-auto min-w-44">
        <option value="">All staff</option>
        @foreach ($staffUsers as $u)<option value="{{ $u->id }}" @selected($filterUser == $u->id)>{{ $u->name }}</option>@endforeach
      </select>
      <button type="submit" class="btn btn-primary">Filter</button>
      @if ($filterUser)<a href="{{ route('admin.activity.index') }}" class="btn btn-secondary">Clear</a>@endif
    </form>
  </div>

  @if ($recent->isEmpty())
    <x-ui.empty icon="activity" title="No activity recorded yet" />
  @else
    <ul class="card divide-y divide-line">
      @foreach ($recent as $log)
        @php
          $badge = match ($log->action) { 'created' => 'badge-success', 'updated' => 'badge-info', 'deleted' => 'badge-warning', 'restored' => 'badge-brand', default => 'badge-danger' };
        @endphp
        <li class="flex items-start gap-4 px-5 py-4">
          <span class="badge {{ $badge }} mt-0.5 shrink-0 uppercase">{{ $log->actionLabel() }}</span>
          <div class="min-w-0 flex-1">
            <p class="text-sm text-fg"><span class="font-semibold">{{ $log->subject_type }}</span> &mdash; {{ $log->subject_title }}</p>
            <p class="text-xs text-muted">by <span class="font-medium text-fg-soft">{{ $log->user_name }}</span></p>
          </div>
          <time class="shrink-0 text-xs text-muted" title="{{ $log->created_at->toDateTimeString() }}">{{ $log->created_at->diffForHumans() }}</time>
        </li>
      @endforeach
    </ul>
    <div>{{ $recent->links() }}</div>
  @endif
</section>
@endsection
