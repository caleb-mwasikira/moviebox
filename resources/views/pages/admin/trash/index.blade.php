@extends('layouts.admin')

@section('admin-title', 'Trash')

@section('admin-content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
  <div>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Trash</h2>
    <p class="mt-1 text-gray-500 dark:text-gray">Soft-deleted content — restore or permanently delete</p>
  </div>
</div>

@if (session('status'))
  @php
    $msg = match(session('status')) {
      'movie-restored'       => 'Movie restored successfully.',
      'movie-force-deleted'  => 'Movie permanently deleted.',
      'series-restored'      => 'Series restored successfully.',
      'series-force-deleted' => 'Series permanently deleted.',
      default                => null,
    };
  @endphp
  @if ($msg)
    <div class="mb-6 px-4 py-3 border border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-950 text-green-700 dark:text-green-300 text-sm font-medium">
      {{ $msg }}
    </div>
  @endif
@endif

{{-- ── Movies ── --}}
<div class="mb-10">
  <h3 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">
    Movies <span class="text-gray-300 dark:text-gray-600">({{ $movies->count() }})</span>
  </h3>

  @if ($movies->isEmpty())
    <div class="py-10 text-center border border-dashed border-gray-200 dark:border-gray-700">
      <p class="text-sm text-gray-400">No deleted movies</p>
    </div>
  @else
    <div class="border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-800">
      @foreach ($movies as $movie)
        <div class="flex items-center gap-x-4 px-4 py-3">
          {{-- Thumbnail --}}
          <div class="w-10 h-14 shrink-0 bg-gray-100 dark:bg-gray-800 overflow-hidden">
            @if ($movie->thumbnail)
              <img src="{{ Storage::url($movie->thumbnail) }}" class="w-full h-full object-cover" loading="lazy">
            @endif
          </div>

          {{-- Info --}}
          <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $movie->title }}</p>
            <p class="text-xs text-gray-500 mt-0.5">
              Deleted {{ $movie->deleted_at->diffForHumans() }}
              @if ($movie->genre) &middot; {{ $movie->genre }}@endif
            </p>
          </div>

          {{-- Actions --}}
          <div class="flex items-center gap-x-2 shrink-0">
            <form method="POST" action="{{ route('admin.movies.restore', $movie->id) }}">
              @csrf @method('PATCH')
              <button type="submit"
                class="text-xs font-bold uppercase px-3 py-1.5 border border-green-400 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors">
                Restore
              </button>
            </form>
            <form method="POST" action="{{ route('admin.movies.force-delete', $movie->id) }}"
              onsubmit="return confirm('Permanently delete "{{ addslashes($movie->title) }}"? This cannot be undone.')">
              @csrf @method('DELETE')
              <button type="submit"
                class="text-xs font-bold uppercase px-3 py-1.5 border border-red-400 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                Delete Forever
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

{{-- ── Series ── --}}
<div>
  <h3 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">
    Series <span class="text-gray-300 dark:text-gray-600">({{ $series->count() }})</span>
  </h3>

  @if ($series->isEmpty())
    <div class="py-10 text-center border border-dashed border-gray-200 dark:border-gray-700">
      <p class="text-sm text-gray-400">No deleted series</p>
    </div>
  @else
    <div class="border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-800">
      @foreach ($series as $s)
        <div class="flex items-center gap-x-4 px-4 py-3">
          <div class="w-10 h-14 shrink-0 bg-gray-100 dark:bg-gray-800 overflow-hidden">
            @if ($s->thumbnail)
              <img src="{{ Storage::url($s->thumbnail) }}" class="w-full h-full object-cover" loading="lazy">
            @endif
          </div>

          <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $s->title }}</p>
            <p class="text-xs text-gray-500 mt-0.5">
              Deleted {{ $s->deleted_at->diffForHumans() }}
              @if ($s->genre) &middot; {{ $s->genre }}@endif
            </p>
          </div>

          <div class="flex items-center gap-x-2 shrink-0">
            <form method="POST" action="{{ route('admin.series.restore', $s->id) }}">
              @csrf @method('PATCH')
              <button type="submit"
                class="text-xs font-bold uppercase px-3 py-1.5 border border-green-400 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors">
                Restore
              </button>
            </form>
            <form method="POST" action="{{ route('admin.series.force-delete', $s->id) }}"
              onsubmit="return confirm('Permanently delete "{{ addslashes($s->title) }}"? This cannot be undone.')">
              @csrf @method('DELETE')
              <button type="submit"
                class="text-xs font-bold uppercase px-3 py-1.5 border border-red-400 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                Delete Forever
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
