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
          <tr>
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
                  class="text-xs font-bold uppercase px-2 py-1 {{ $show->isFeatured() ? 'bg-primary/20 text-primary dark:text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
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
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-8">{{ $series->links() }}</div>
@endif
@endsection
