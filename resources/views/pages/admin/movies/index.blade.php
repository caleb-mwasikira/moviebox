@extends('layouts.admin')

@section('admin-title', 'Movies')

@section('admin-content')
<div class="flex items-center justify-between mb-8">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Movies</h2>
  <a href="{{ route('admin.movies.create') }}"
    class="inline-flex items-center gap-x-2 font-bold uppercase py-2.5 px-5 text-xs text-gray-900 bg-accent hover:bg-accent/90 transition-colors">
    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
    Upload Movie
  </a>
</div>

@if ($movies->isEmpty())
  <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
    <svg class="mx-auto mb-3 w-10 h-10 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>
    <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No movies yet</p>
    <a href="{{ route('admin.movies.create') }}" class="mt-4 inline-block text-accent text-xs font-bold hover:underline">Upload your first movie</a>
  </div>
@else

  {{-- ── Mobile cards (< lg) ── --}}
  <div class="lg:hidden border border-gray-200 dark:border-gray-600 divide-y divide-gray-200 dark:divide-gray-600">
    @foreach ($movies as $movie)
      <div class="p-4">
        <div class="flex gap-x-3">
          {{-- Thumbnail --}}
          @if ($movie->thumbnail)
            <img src="{{ $movie->thumbnailUrl() }}" class="w-12 h-16 object-cover shrink-0" alt="">
          @else
            <div class="w-12 h-16 bg-gray-100 dark:bg-gray-700 shrink-0 flex items-center justify-center">
              <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>
            </div>
          @endif
          {{-- Info --}}
          <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $movie->title }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $movie->genre ?? '—' }} · {{ $movie->year ?? '—' }}</p>
            <div class="flex flex-wrap items-center gap-2 mt-2">
              @if ($movie->is_free)
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Free</span>
              @endif
              <form method="POST" action="{{ route('admin.movies.toggle-status', $movie) }}">
                @csrf @method('PATCH')
                <button type="submit"
                  class="text-[10px] font-bold uppercase px-2 py-0.5 {{ $movie->isPublished() ? 'bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                  {{ $movie->isPublished() ? 'Published' : 'Hidden' }}
                </button>
              </form>
              <form method="POST" action="{{ route('admin.movies.toggle-featured', $movie) }}">
                @csrf @method('PATCH')
                <button type="submit"
                  class="text-[10px] font-bold uppercase px-2 py-0.5 {{ $movie->isFeatured() ? 'bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                  {{ $movie->isFeatured() ? '★ Featured' : '☆ Feature' }}
                </button>
              </form>
            </div>
          </div>
        </div>
        {{-- Actions --}}
        <div class="flex items-center gap-x-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
          <a href="{{ route('admin.movies.edit', $movie) }}"
            class="text-xs font-bold uppercase text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors">Edit</a>
          <form method="POST" action="{{ route('admin.movies.destroy', $movie) }}"
            onsubmit="return confirm('Delete {{ addslashes($movie->title) }}? This cannot be undone.')">
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
                <div>
                  <span class="font-bold text-gray-900 dark:text-white">{{ $movie->title }}</span>
                  @if ($movie->is_free)
                    <span class="ml-2 text-[10px] font-bold uppercase px-1.5 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Free</span>
                  @endif
                </div>
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
                  class="text-xs font-bold uppercase px-2 py-1 cursor-pointer {{ $movie->isFeatured() ? 'bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                  {{ $movie->isFeatured() ? '★ Featured' : '☆ Feature' }}
                </button>
              </form>
            </td>
            <td class="py-4 text-right">
              <div class="flex items-center justify-end gap-x-4">
                <a href="{{ route('admin.movies.edit', $movie) }}"
                  class="font-bold text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors text-xs uppercase">Edit</a>
                <form method="POST" action="{{ route('admin.movies.destroy', $movie) }}"
                  onsubmit="return confirm('Delete {{ addslashes($movie->title) }}? This cannot be undone.')">
                  @csrf @method('DELETE')
                  <button type="submit"
                    class="font-bold text-gray-500 hover:text-red-500 transition-colors text-xs uppercase">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-6">{{ $movies->links() }}</div>
@endif
@endsection

