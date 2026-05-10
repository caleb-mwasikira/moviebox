@extends('layouts.admin')

@section('admin-title', 'Series')

@section('admin-content')
<div class="flex items-center justify-between mb-10">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Series</h2>
  <a href="{{ route('admin.series.create') }}"
    class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-3 px-6 text-sm text-gray-900 bg-accent hover:bg-accent/90">
    + Add Series
  </a>
</div>

@if ($series->isEmpty())
  <p class="text-gray-500 dark:text-gray">No series yet. Add your first series above.</p>
@else
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Title</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Genre</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Year</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Seasons</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Status</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Featured</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
        @foreach ($series as $show)
          <tr data-series-id="{{ $show->id }}">
            <td class="py-4 pr-4">
              <div class="flex items-center gap-x-3">
                @if ($show->thumbnail)
                  <img src="{{ $show->thumbnailUrl() }}" class="w-10 h-14 object-cover shrink-0" alt="">
                @else
                  <div class="w-10 h-14 bg-gray-100 dark:bg-gray-700 shrink-0"></div>
                @endif
                <span class="font-bold text-gray-900 dark:text-white">{{ $show->title }}</span>
              </div>
            </td>
            <td class="py-4 pr-4 text-gray-500 dark:text-gray">{{ $show->genre ?? '—' }}</td>
            <td class="py-4 pr-4 text-gray-500 dark:text-gray">{{ $show->year ?? '—' }}</td>
            <td class="py-4 pr-4 text-gray-500 dark:text-gray">{{ $show->seasons_count }}</td>
            <td class="py-4 pr-4">
              <form method="POST" action="{{ route('admin.series.toggle-status', $show) }}">
                @csrf @method('PATCH')
                <button type="submit"
                  class="text-xs font-bold uppercase px-2 py-1 {{ $show->isPublished() ? 'bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                  {{ $show->isPublished() ? 'Published' : 'Hidden' }}
                </button>
              </form>
            </td>
            <td class="py-4 pr-4">
              <form method="POST" action="{{ route('admin.series.toggle-featured', $show) }}">
                @csrf @method('PATCH')
                <button type="submit"
                  class="text-xs font-bold uppercase px-2 py-1 cursor-pointer {{ $show->isFeatured() ? 'bg-primary/20 text-primary dark:text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                  {{ $show->isFeatured() ? '★ Featured' : '☆ Feature' }}
                </button>
              </form>
            </td>
            <td class="py-4 text-right">
              <div class="flex items-center justify-end gap-x-4">
                <a href="{{ route('admin.series.edit', $show) }}"
                  class="font-bold text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors text-xs uppercase">Manage</a>
                <form method="POST" action="{{ route('admin.series.destroy', $show) }}"
                  onsubmit="return confirm('Delete {{ addslashes($show->title) }}? All seasons and episodes will be removed.')">
                  @csrf @method('DELETE')
                  <button type="submit"
                    class="font-bold text-gray-500 hover:text-danger transition-colors text-xs uppercase">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @if ($show->unprocessed_count > 0)
            <tr id="series-progress-row-{{ $show->id }}"
                data-unprocessed="{{ $show->unprocessed_count }}">
              <td colspan="7" class="pb-3 pt-0 px-0">
                <div class="px-4 space-y-1.5">
                  <div id="series-progress-bar-wrap-{{ $show->id }}" class="flex-1 bg-gray-200 dark:bg-gray-700 h-1.5 overflow-hidden rounded-full" style="display:none">
                    <div id="series-progress-bar-{{ $show->id }}"
                         class="h-full bg-accent rounded-full transition-[width] duration-300 ease-out"
                         style="width:0%"></div>
                  </div>
                  <div class="flex items-center gap-x-2 text-xs text-gray-500 dark:text-gray-400">
                    <span id="series-encoding-pulse-{{ $show->id }}" style="display:none">
                      <span class="inline-block w-1.5 h-1.5 rounded-full bg-accent animate-pulse mr-1"></span>
                      Encoding in progress ·
                    </span>
                    <span id="series-pending-text-{{ $show->id }}">
                      {{ $show->unprocessed_count }} episode{{ $show->unprocessed_count !== 1 ? 's' : '' }} pending HLS encoding
                    </span>
                  </div>
                </div>
              </td>
            </tr>
          @endif
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-8">{{ $series->links() }}</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // seriesId -> Map(episodeModelId -> percentage)
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
      const form = document.querySelector('form[data-reprocess-series="' + seriesId + '"]');
      if (form) form.remove();
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
    if (d.filmType === 'episodes' && d.seriesId) {
      markEpisodeProgress(d.seriesId, d.modelId, d.percentage);
    }
  });

  document.addEventListener('video-ready', function (e) {
    const d = e.detail;
    if (d.filmType === 'episodes' && d.seriesId) episodeDone(d.seriesId, d.modelId);
  });
});
</script>
@endpush
