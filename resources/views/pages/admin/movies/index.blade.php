@extends('layouts.admin')

@section('admin-title', 'Movies')

@section('admin-content')
<div class="flex items-center justify-between mb-10">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Movies</h2>
  <a href="{{ route('admin.movies.create') }}"
    class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-3 px-6 text-sm text-gray-900 bg-accent hover:bg-accent/90">
    + Upload Movie
  </a>
</div>

@if ($movies->isEmpty())
  <p class="text-gray-500 dark:text-gray">No movies yet. Upload your first movie above.</p>
@else
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Title</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Genre</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Year</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Status</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Featured</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
        @foreach ($movies as $movie)
          <tr>
            <td class="py-4 pr-4">
              <div class="flex items-center gap-x-3">
                @if ($movie->thumbnail)
                  <img src="{{ $movie->thumbnailUrl() }}" class="w-10 h-14 object-cover shrink-0" alt="">
                @else
                  <div class="w-10 h-14 bg-gray-100 dark:bg-gray-700 shrink-0"></div>
                @endif
                <span class="font-bold text-gray-900 dark:text-white">{{ $movie->title }}</span>
              </div>
            </td>
            <td class="py-4 pr-4 text-gray-500 dark:text-gray">{{ $movie->genre ?? '—' }}</td>
            <td class="py-4 pr-4 text-gray-500 dark:text-gray">{{ $movie->year ?? '—' }}</td>
            <td class="py-4 pr-4">
              <form method="POST" action="{{ route('admin.movies.toggle-status', $movie) }}">
                @csrf @method('PATCH')
                <button type="submit"
                  class="text-xs font-bold uppercase px-2 py-1 {{ $movie->isPublished() ? 'bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                  {{ $movie->isPublished() ? 'Published' : 'Hidden' }}
                </button>
              </form>
            </td>
            <td class="py-4 pr-4">
              <form method="POST" action="{{ route('admin.movies.toggle-featured', $movie) }}">
                @csrf @method('PATCH')
                <button type="submit"
                  class="text-xs font-bold uppercase px-2 py-1 cursor-pointer {{ $movie->isFeatured() ? 'bg-primary/20 text-primary dark:text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                  {{ $movie->isFeatured() ? '★ Featured' : '☆ Feature' }}
                </button>
              </form>
            </td>
            <td class="py-4 text-right">
              <div class="flex items-center justify-end gap-x-4">
                @unless ($movie->streaming_path)
                  <form data-reprocess-movie="{{ $movie->id }}"
                    method="POST" action="{{ route('admin.movies.reprocess', $movie) }}"
                    onsubmit="return confirm('Re-encode {{ addslashes($movie->title) }} for HLS streaming? This is CPU-intensive and may take several minutes.')">
                    @csrf
                    <button type="submit"
                      class="font-bold text-accent hover:text-accent/70 transition-colors text-xs uppercase cursor-pointer">
                      ↺ Stream
                    </button>
                  </form>
                @endunless
                <a href="{{ route('admin.movies.edit', $movie) }}"
                  class="font-bold text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors text-xs uppercase">Edit</a>
                <form method="POST" action="{{ route('admin.movies.destroy', $movie) }}"
                  onsubmit="return confirm('Delete {{ addslashes($movie->title) }}? This cannot be undone.')">
                  @csrf @method('DELETE')
                  <button type="submit"
                    class="font-bold text-gray-500 hover:text-danger transition-colors text-xs uppercase">Delete</button>
                </form>
              </div>
            </td>
          </tr>
          @unless ($movie->streaming_path)
            <tr id="progress-row-{{ $movie->id }}" style="display:none">
              <td colspan="6" class="pb-3 pt-0 px-0">
                <div class="flex items-center gap-x-3 px-4">
                  <div class="flex-1 bg-gray-200 dark:bg-gray-700 h-1.5 overflow-hidden rounded-full">
                    <div id="progress-bar-{{ $movie->id }}"
                         class="h-full bg-accent rounded-full transition-[width] duration-300 ease-out"
                         style="width:0%"></div>
                  </div>
                  <span id="progress-pct-{{ $movie->id }}"
                        class="text-xs text-gray-500 dark:text-gray-400 tabular-nums w-10 text-right shrink-0">
                    0%
                  </span>
                </div>
              </td>
            </tr>
          @endunless
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-8">{{ $movies->links() }}</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  function showProgress(movieId, pct) {
    const row = document.getElementById('progress-row-' + movieId);
    if (!row) return;
    row.style.display = '';
    const bar = document.getElementById('progress-bar-' + movieId);
    const txt = document.getElementById('progress-pct-' + movieId);
    if (bar) bar.style.width = pct + '%';
    if (txt) txt.textContent = pct + '%';
  }

  function markDone(movieId) {
    const row = document.getElementById('progress-row-' + movieId);
    if (row) row.style.display = 'none';
    const form = document.querySelector('form[data-reprocess-movie="' + movieId + '"]');
    if (form) form.remove();
  }

  fetch('{{ route("admin.encoding-status") }}', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      (data.jobs || []).forEach(job => {
        if (job.film_type === 'movies') showProgress(job.model_id, job.percentage);
      });
    })
    .catch(() => {});

  document.addEventListener('encoding-progress', function (e) {
    const d = e.detail;
    if (d.filmType === 'movies') showProgress(d.modelId, d.percentage);
  });

  document.addEventListener('streaming-ready', function (e) {
    const d = e.detail;
    if (d.filmType === 'movies') markDone(d.modelId);
  });
});
</script>
@endpush
