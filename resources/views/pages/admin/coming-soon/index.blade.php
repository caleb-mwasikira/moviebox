@extends('layouts.admin')

@section('admin-title', 'Coming Soon')

@section('admin-content')
<div class="flex items-center justify-between mb-8">
  <div>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Coming Soon</h2>
    <p class="mt-1 text-sm text-gray-500">Manage upcoming film announcements and trailers.</p>
  </div>
  <a href="{{ route('admin.coming-soon.create') }}"
    class="inline-flex items-center gap-x-2 font-bold uppercase py-2.5 px-5 text-xs text-gray-900 bg-accent hover:bg-accent/90 transition-colors">
    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
    New Film
  </a>
</div>

@if (session('status') === 'coming-soon-created')
  <div class="mb-6 border border-green-500 bg-green-500/10 px-4 py-3 text-sm font-medium text-green-700 dark:text-green-400">
    Film added to Coming Soon.
  </div>
@elseif (session('status') === 'coming-soon-deleted')
  <div class="mb-6 border border-red-500 bg-red-500/10 px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400">
    Film removed.
  </div>
@endif

@if ($films->isEmpty())
  <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
    <svg class="mx-auto mb-3 w-10 h-10 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
    </svg>
    <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No coming soon films</p>
    <a href="{{ route('admin.coming-soon.create') }}" class="mt-4 inline-block text-accent text-xs font-bold hover:underline">
      Add your first announcement
    </a>
  </div>
@else

  {{-- ── Mobile cards (< lg) ── --}}
  <div class="lg:hidden border border-gray-200 dark:border-gray-600 divide-y divide-gray-200 dark:divide-gray-600">
    @foreach ($films as $film)
      <div class="p-4 flex gap-x-3">
        {{-- Thumbnail --}}
        <div class="shrink-0 w-12 h-16 bg-gray-100 dark:bg-gray-800 overflow-hidden">
          @if ($film->thumbnail)
            <img src="{{ $film->thumbnailUrl() }}" alt="" class="w-full h-full object-cover">
          @endif
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $film->title }}</p>
          <p class="text-xs text-gray-500 mt-0.5">
            {{ $film->genre ?? '—' }}
            @if ($film->year) · {{ $film->year }} @endif
          </p>
          <div class="flex items-center gap-x-1.5 mt-1">
            @if ($film->trailerIsReady())
              <span class="text-[10px] font-bold uppercase text-green-500">Trailer Ready</span>
            @elseif ($film->trailer_path)
              <span class="text-[10px] font-bold uppercase text-yellow-500">Processing</span>
            @else
              <span class="text-[10px] font-bold uppercase text-gray-400">No Trailer</span>
            @endif
          </div>
          <div class="flex items-center gap-x-4 mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('admin.coming-soon.edit', $film) }}"
              class="text-xs font-bold uppercase text-accent hover:underline">Edit</a>
            <form action="{{ route('admin.coming-soon.toggle-featured', $film) }}" method="POST" class="inline">
              @csrf @method('PATCH')
              <button type="submit"
                class="text-xs font-bold uppercase transition-colors {{ $film->featured ? 'text-accent hover:text-gray-500' : 'text-gray-500 hover:text-accent' }}">
                {{ $film->featured ? 'Unfeature' : 'Feature' }}
              </button>
            </form>
            <form action="{{ route('admin.coming-soon.destroy', $film) }}" method="POST" class="inline"
              onsubmit="return confirm('Remove {{ addslashes($film->title) }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-red-500 transition-colors">Remove</button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- ── Desktop table (≥ lg) ── --}}
  <div class="hidden lg:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-6">Film</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-6">Genre / Year</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-6">Trailer</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-6">Featured</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
        @foreach ($films as $film)
          <tr>
            <td class="py-4 pr-6">
              <div class="flex items-center gap-x-3">
                <div class="shrink-0 w-10 h-14 bg-gray-100 dark:bg-gray-700 overflow-hidden">
                  @if ($film->thumbnail)
                    <img src="{{ $film->thumbnailUrl() }}" alt="" class="w-full h-full object-cover">
                  @endif
                </div>
                <div>
                  <p class="font-bold text-gray-900 dark:text-white">{{ $film->title }}</p>
                  @if ($film->description)
                    <p class="text-xs text-gray-400 mt-0.5 max-w-xs truncate">{{ $film->description }}</p>
                  @endif
                </div>
              </div>
            </td>
            <td class="py-4 pr-6 text-gray-500">
              {{ $film->genre ?? '—' }}
              @if ($film->year)<span class="text-gray-400 text-xs ml-1">{{ $film->year }}</span>@endif
            </td>
            <td class="py-4 pr-6">
              @if ($film->trailerIsReady())
                <span class="inline-flex items-center gap-x-1 text-xs font-bold uppercase text-green-600 dark:text-green-400">
                  <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>Ready
                </span>
              @elseif ($film->trailer_path)
                <span class="inline-flex items-center gap-x-1 text-xs font-bold uppercase text-yellow-600 dark:text-yellow-400">
                  <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 inline-block"></span>Processing
                </span>
              @else
                <span class="text-xs text-gray-400 uppercase">None</span>
              @endif
            </td>
            <td class="py-4 pr-6">
              <form action="{{ route('admin.coming-soon.toggle-featured', $film) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit"
                  class="inline-flex items-center gap-x-1.5 text-xs font-bold uppercase transition-colors {{ $film->featured ? 'text-accent' : 'text-gray-400 hover:text-accent' }}">
                  <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                  </svg>
                  {{ $film->featured ? 'Featured' : 'Feature' }}
                </button>
              </form>
            </td>
            <td class="py-4 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-x-4">
                <a href="{{ route('admin.coming-soon.edit', $film) }}"
                  class="text-xs font-bold uppercase text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors">Edit</a>
                <form action="{{ route('admin.coming-soon.destroy', $film) }}" method="POST" class="inline"
                  onsubmit="return confirm('Remove {{ addslashes($film->title) }}?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-red-500 transition-colors">Remove</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  @if ($films->hasPages())
    <div class="mt-6">{{ $films->links() }}</div>
  @endif

@endif
@endsection
