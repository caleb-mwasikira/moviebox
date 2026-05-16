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

<script>
function comingSoonAdmin() {
  return {
    featured: @json($films->getCollection()->mapWithKeys(fn($f) => [$f->uuid => (bool) $f->featured])),
    toggleFeatured(uuid, url) {
      fetch(url, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
      }).then(r => r.json()).then(d => this.featured[uuid] = d.featured);
    }
  };
}
</script>

<div x-data="comingSoonAdmin()">

  {{-- ── Mobile cards (< lg) ── --}}
  <div class="lg:hidden border border-gray-200 dark:border-gray-600 divide-y divide-gray-200 dark:divide-gray-600">
    @foreach ($films as $film)
      <div class="p-4 flex gap-x-3">
        <div class="shrink-0 w-12 h-16 bg-gray-100 dark:bg-gray-800 overflow-hidden">
          @if ($film->thumbnail)
            <img src="{{ $film->thumbnailUrl() }}" alt="" class="w-full h-full object-cover">
          @endif
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $film->title }}</p>
          <p class="text-xs text-gray-500 mt-0.5">
            {{ $film->genre ?? '—' }}@if ($film->year) · {{ $film->year }}@endif
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
          <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('admin.coming-soon.edit', $film) }}"
              class="text-xs font-bold uppercase text-accent hover:underline">Edit</a>

            <button type="button"
              x-on:click="toggleFeatured('{{ $film->uuid }}', '{{ route('admin.coming-soon.toggle-featured', $film) }}')"
              :class="featured['{{ $film->uuid }}'] ? 'text-accent' : 'text-gray-400 hover:text-accent'"
              class="inline-flex items-center gap-x-1 text-xs font-bold uppercase transition-colors">
              <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
              <span x-text="featured['{{ $film->uuid }}'] ? 'Featured' : 'Feature'"></span>
            </button>

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

  {{-- ── Desktop list (≥ lg) ── --}}
  <div class="hidden lg:block">
    <div class="flex items-center gap-x-4 pb-3 border-b border-gray-200 dark:border-gray-600 text-xs font-bold uppercase text-gray-500 tracking-widest">
      <div class="w-10 shrink-0"></div>
      <div class="flex-1 min-w-0">Film</div>
      <div class="w-36 shrink-0">Genre / Year</div>
      <div class="w-28 shrink-0">Trailer</div>
      <div class="w-24 shrink-0">Featured</div>
      <div class="w-40 shrink-0">Actions</div>
    </div>

    <div class="divide-y divide-gray-200 dark:divide-gray-600">
      @foreach ($films as $film)
        <div class="flex items-center gap-x-4 py-4">

          <div class="w-10 h-14 shrink-0 bg-gray-100 dark:bg-gray-700 overflow-hidden">
            @if ($film->thumbnail)
              <img src="{{ $film->thumbnailUrl() }}" alt="" class="w-full h-full object-cover">
            @endif
          </div>

          <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $film->title }}</p>
            @if ($film->description)
              <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $film->description }}</p>
            @endif
          </div>

          <div class="w-36 shrink-0 text-sm text-gray-500">
            {{ $film->genre ?? '—' }}
            @if ($film->year)<span class="text-gray-400 text-xs ml-1">{{ $film->year }}</span>@endif
          </div>

          <div class="w-28 shrink-0">
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
          </div>

          <div class="w-24 shrink-0">
            <button type="button"
              x-on:click="toggleFeatured('{{ $film->uuid }}', '{{ route('admin.coming-soon.toggle-featured', $film) }}')"
              :class="featured['{{ $film->uuid }}'] ? 'text-accent' : 'text-gray-400 hover:text-accent'"
              class="inline-flex items-center gap-x-1.5 text-xs font-bold uppercase transition-colors cursor-pointer"
              :title="featured['{{ $film->uuid }}'] ? 'Unfeature' : 'Feature'">
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
              <span x-text="featured['{{ $film->uuid }}'] ? 'Featured' : 'Feature'"></span>
            </button>
          </div>

          <div class="w-40 shrink-0 flex items-center gap-x-3">
            <a href="{{ route('admin.coming-soon.edit', $film) }}"
              class="inline-flex items-center gap-x-1 text-xs font-bold uppercase py-1.5 px-3 bg-accent text-gray-900 hover:bg-accent/80 transition-colors">
              <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
              Edit
            </a>

            <form action="{{ route('admin.coming-soon.destroy', $film) }}" method="POST" class="inline"
              onsubmit="return confirm('Remove {{ addslashes($film->title) }}?')">
              @csrf @method('DELETE')
              <button type="submit"
                class="inline-flex items-center gap-x-1 text-xs font-bold uppercase py-1.5 px-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-red-500 hover:text-red-500 transition-colors">
                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                Remove
              </button>
            </form>
          </div>

        </div>
      @endforeach
    </div>
  </div>

  @if ($films->hasPages())
    <div class="mt-6">{{ $films->links() }}</div>
  @endif

</div>{{-- /x-data --}}
@endif
@endsection
