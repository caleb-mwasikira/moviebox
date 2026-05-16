@extends('layouts.admin')

@section('admin-title', 'Series')

@section('admin-content')
<div class="flex items-center justify-between mb-8">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Series</h2>
  <a href="{{ route('admin.series.create') }}"
    class="inline-flex items-center gap-x-2 font-bold uppercase py-2.5 px-5 text-xs text-gray-900 bg-accent hover:bg-accent/90 transition-colors">
    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
    Add Series
  </a>
</div>

@if ($series->isEmpty())
  <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
    <svg class="mx-auto mb-3 w-10 h-10 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/></svg>
    <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No series yet</p>
    <a href="{{ route('admin.series.create') }}" class="mt-4 inline-block text-accent text-xs font-bold hover:underline">Add your first series</a>
  </div>
@else

  {{-- ── Mobile cards (< lg) ── --}}
  <div class="lg:hidden border border-gray-200 dark:border-gray-600 divide-y divide-gray-200 dark:divide-gray-600">
    @foreach ($series as $show)
      <div class="p-4">
        <div class="flex gap-x-3">
          {{-- Thumbnail --}}
          @if ($show->thumbnail)
            <img src="{{ $show->thumbnailUrl() }}" class="w-12 h-16 object-cover shrink-0" alt="">
          @else
            <div class="w-12 h-16 bg-gray-100 dark:bg-gray-700 shrink-0"></div>
          @endif
          {{-- Info --}}
          <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $show->title }}</p>
            <p class="text-xs text-gray-500 mt-0.5">
              {{ $show->genre ?? '—' }} · {{ $show->year ?? '—' }} · {{ $show->seasons_count }} season{{ $show->seasons_count !== 1 ? 's' : '' }}
            </p>
            <div class="flex flex-wrap items-center gap-2 mt-2">
              @if ($show->free_episodes_count > 0)
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">{{ $show->free_episodes_count }} free ep{{ $show->free_episodes_count !== 1 ? 's' : '' }}</span>
              @endif
              <form method="POST" action="{{ route('admin.series.toggle-status', $show) }}">
                @csrf @method('PATCH')
                <button type="submit"
                  class="text-[10px] font-bold uppercase px-2 py-0.5 {{ $show->isPublished() ? 'bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                  {{ $show->isPublished() ? 'Published' : 'Hidden' }}
                </button>
              </form>
              <form method="POST" action="{{ route('admin.series.toggle-featured', $show) }}">
                @csrf @method('PATCH')
                <button type="submit"
                  class="text-[10px] font-bold uppercase px-2 py-0.5 {{ $show->isFeatured() ? 'bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                  {{ $show->isFeatured() ? '★ Featured' : '☆ Feature' }}
                </button>
              </form>
            </div>
          </div>
        </div>
        @if ($show->unprocessed_count > 0)
          <p class="mt-2 text-xs text-gray-500">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-accent animate-pulse mr-1"></span>
            {{ $show->unprocessed_count }} episode{{ $show->unprocessed_count !== 1 ? 's' : '' }} pending encoding
          </p>
        @endif
        {{-- Actions --}}
        <div class="flex items-center gap-x-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
          <a href="{{ route('admin.series.edit', $show) }}"
            class="text-xs font-bold uppercase text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors">Manage</a>
          <form method="POST" action="{{ route('admin.series.destroy', $show) }}"
            onsubmit="return confirm('Delete {{ addslashes($show->title) }}? All seasons and episodes will be removed.')">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-red-500 transition-colors">Delete</button>
          </form>
        </div>
      </div>
    @endforeach
  </div>

  {{-- ── Desktop table (≥ lg) ── --}}
  <div class="hidden lg:block overflow-x-auto">
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
                <div>
                  <span class="font-bold text-gray-900 dark:text-white">{{ $show->title }}</span>
                  @if ($show->free_episodes_count > 0)
                    <span class="ml-2 text-[10px] font-bold uppercase px-1.5 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">{{ $show->free_episodes_count }} free ep{{ $show->free_episodes_count !== 1 ? 's' : '' }}</span>
                  @endif
                </div>
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
                  class="text-xs font-bold uppercase px-2 py-1 cursor-pointer {{ $show->isFeatured() ? 'bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
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
                    class="font-bold text-gray-500 hover:text-red-500 transition-colors text-xs uppercase">Delete</button>
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

  <div class="mt-6">{{ $series->links() }}</div>
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
