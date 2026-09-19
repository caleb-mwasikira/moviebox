@extends('layouts.admin')

@section('admin-title', 'Series')

@section('admin-content')
<x-ui.page-header title="Series" subtitle="Manage series, seasons and episodes.">
  <x-slot:actions>
    <div class="inline-flex rounded-xl border border-line bg-surface p-1">
      <a href="{{ route('admin.series.index') }}" class="rounded-lg px-3.5 py-1.5 text-sm font-semibold transition {{ !$trashed ? 'bg-brand text-white' : 'text-fg-soft hover:text-fg' }}">Active</a>
      <a href="{{ route('admin.series.index', ['trashed' => 1]) }}" class="rounded-lg px-3.5 py-1.5 text-sm font-semibold transition {{ $trashed ? 'bg-brand text-white' : 'text-fg-soft hover:text-fg' }}">Trashed</a>
    </div>
    @if (!$trashed)<a href="{{ route('admin.series.create') }}" class="btn btn-primary"><x-ui.icon name="plus" class="size-4" /> Add series</a>@endif
  </x-slot:actions>
</x-ui.page-header>

@if ($series->isEmpty())
  <x-ui.empty icon="tv" :title="$trashed ? 'No trashed series' : 'No series yet'">
    @unless ($trashed)<a href="{{ route('admin.series.create') }}" class="btn btn-primary">Add your first series</a>@endunless
  </x-ui.empty>
@else
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>Title</th><th class="hidden md:table-cell">Genre</th><th class="hidden md:table-cell">Year</th>
          @if (!$trashed)<th class="hidden sm:table-cell">Seasons</th><th>Status</th><th class="hidden sm:table-cell">Featured</th>@else<th>Deleted</th>@endif
          <th class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($series as $show)
          <tr data-series-id="{{ $show->id }}" class="{{ $trashed ? 'opacity-70 hover:opacity-100' : '' }}">
            <td>
              <div class="flex items-center gap-3">
                <x-admin.thumb :src="$show->thumbnail ? $show->thumbnailUrl() : null" />
                <div class="min-w-0">
                  <p class="truncate font-semibold text-fg">{{ $show->title }}</p>
                  @if (!$trashed && $show->free_episodes_count > 0)
                    <span class="badge badge-success mt-1">{{ $show->free_episodes_count }} free ep{{ $show->free_episodes_count !== 1 ? 's' : '' }}</span>
                  @endif
                </div>
              </div>
            </td>
            <td class="hidden md:table-cell">{{ $show->genre ?? '—' }}</td>
            <td class="hidden md:table-cell">{{ $show->year ?? '—' }}</td>
            @if (!$trashed)
              <td class="hidden sm:table-cell">{{ $show->seasons_count }}</td>
              <td><x-admin.toggle :action="route('admin.series.toggle-status', $show)" :on="$show->isPublished()" on-label="Published" off-label="Hidden" /></td>
              <td class="hidden sm:table-cell"><x-admin.toggle :action="route('admin.series.toggle-featured', $show)" :on="$show->isFeatured()" on-label="★ Featured" off-label="☆ Feature" /></td>
            @else
              <td class="text-xs text-muted">{{ $show->deleted_at->diffForHumans() }}</td>
            @endif
            <td>
              <div class="flex items-center justify-end gap-1.5">
                @if ($trashed)
                  <form method="POST" action="{{ route('admin.series.restore', $show->id) }}">@csrf @method('PATCH')<button type="submit" class="btn btn-secondary btn-sm">Restore</button></form>
                  @can('force delete series')
                    <x-admin.delete :action="route('admin.series.force-delete', $show->id)" :confirm="'Permanently delete ' . $show->title . '? All seasons and episodes will be removed.'" label="Delete forever" />
                  @endcan
                @else
                  <a href="{{ route('admin.series.edit', $show) }}" class="btn btn-secondary btn-sm"><x-ui.icon name="edit" class="size-3.5" /> Manage</a>
                  <x-admin.delete :action="route('admin.series.destroy', $show)" :confirm="'Delete ' . $show->title . '? All seasons and episodes will be removed.'" />
                @endif
              </div>
            </td>
          </tr>
          @if (!$trashed && $show->unprocessed_count > 0)
            <tr id="series-progress-row-{{ $show->id }}" data-unprocessed="{{ $show->unprocessed_count }}" class="!bg-surface-2/40">
              <td colspan="7" class="!py-2.5">
                <div class="space-y-1.5">
                  <div id="series-progress-bar-wrap-{{ $show->id }}" class="progress" style="display:none">
                    <span id="series-progress-bar-{{ $show->id }}" class="transition-all" style="width:0%"></span>
                  </div>
                  <div class="flex items-center gap-2 text-xs text-muted">
                    <span id="series-encoding-pulse-{{ $show->id }}" class="inline-flex items-center gap-1.5 font-semibold text-amber-500" style="display:none">
                      <span class="size-1.5 animate-pulse rounded-full bg-amber-500"></span> Encoding in progress ·
                    </span>
                    <span id="series-pending-text-{{ $show->id }}">{{ $show->unprocessed_count }} episode{{ $show->unprocessed_count !== 1 ? 's' : '' }} pending HLS encoding</span>
                  </div>
                </div>
              </td>
            </tr>
          @endif
        @endforeach
      </tbody>
    </table>
  </div>
  <div>{{ $series->links() }}</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const episodePcts = {};

  function setEncodingPulse(seriesId, active) {
    const pulse = document.getElementById('series-encoding-pulse-' + seriesId);
    if (pulse) pulse.style.display = active ? '' : 'none';
  }

  function updateBar(seriesId) {
    const map = episodePcts[seriesId];
    const wrap = document.getElementById('series-progress-bar-wrap-' + seriesId);
    const bar  = document.getElementById('series-progress-bar-' + seriesId);
    if (!wrap || !bar || !map || map.size === 0) return;
    wrap.style.display = '';
    let total = 0;
    map.forEach(function (pct) { total += pct; });
    bar.style.width = Math.round(total / map.size) + '%';
  }

  function markEpisodeProgress(seriesId, modelId, pct) {
    if (!episodePcts[seriesId]) episodePcts[seriesId] = new Map();
    episodePcts[seriesId].set(modelId, pct);
    setEncodingPulse(seriesId, true);
    updateBar(seriesId);
  }

  function episodeDone(seriesId, modelId) {
    if (episodePcts[seriesId]) episodePcts[seriesId].delete(modelId);
    const active = episodePcts[seriesId] && episodePcts[seriesId].size > 0;
    setEncodingPulse(seriesId, active);

    const row = document.getElementById('series-progress-row-' + seriesId);
    if (!row) return;
    let pending = parseInt(row.dataset.unprocessed || '0', 10);
    if (pending > 0) pending--;
    row.dataset.unprocessed = pending;

    if (pending === 0) {
      row.style.display = 'none';
    } else {
      const txt = document.getElementById('series-pending-text-' + seriesId);
      if (txt) txt.textContent = pending + ' episode' + (pending !== 1 ? 's' : '') + ' pending HLS encoding';
      updateBar(seriesId);
    }
  }

  fetch('{{ route("admin.encoding-status") }}', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      (data.jobs || []).forEach(function (job) {
        if (job.film_type === 'episodes' && job.series_id) {
          markEpisodeProgress(job.series_id, job.model_id, job.percentage);
        }
      });
    })
    .catch(function () {});

  document.addEventListener('encoding-progress', function (e) {
    const d = e.detail;
    if (d.filmType === 'episodes' && d.seriesId) markEpisodeProgress(d.seriesId, d.modelId, d.percentage);
  });

  document.addEventListener('video-ready', function (e) {
    const d = e.detail;
    if (d.filmType === 'episodes' && d.seriesId) episodeDone(d.seriesId, d.modelId);
  });
});
</script>
@endpush
