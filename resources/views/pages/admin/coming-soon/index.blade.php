@extends('layouts.admin')

@section('admin-title', 'Coming Soon')

@section('admin-content')
<x-ui.page-header title="Coming soon" subtitle="Manage upcoming film announcements and trailers.">
  <x-slot:actions>
    <a href="{{ route('admin.coming-soon.create') }}" class="btn btn-primary"><x-ui.icon name="plus" class="size-4" /> New film</a>
  </x-slot:actions>
</x-ui.page-header>

@if ($films->isEmpty())
  <x-ui.empty icon="calendar" title="No coming soon films">
    <a href="{{ route('admin.coming-soon.create') }}" class="btn btn-primary">Add your first announcement</a>
  </x-ui.empty>
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

  <div x-data="comingSoonAdmin()" class="space-y-6">
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Film</th><th class="hidden md:table-cell">Genre / year</th><th>Trailer</th><th class="hidden sm:table-cell">Featured</th><th class="text-right">Actions</th></tr></thead>
        <tbody>
          @foreach ($films as $film)
            <tr>
              <td>
                <div class="flex items-center gap-3">
                  <x-admin.thumb :src="$film->thumbnail ? $film->thumbnailUrl() : null" />
                  <div class="min-w-0">
                    <p class="truncate font-semibold text-fg">{{ $film->title }}</p>
                    @if ($film->description)<p class="line-clamp-1 max-w-xs text-xs text-muted">{{ $film->description }}</p>@endif
                  </div>
                </div>
              </td>
              <td class="hidden md:table-cell">{{ $film->genre ?? '—' }}@if ($film->year) <span class="text-muted">· {{ $film->year }}</span>@endif</td>
              <td>
                @if ($film->trailerIsReady())<span class="badge badge-success"><span class="size-1.5 rounded-full bg-emerald-500"></span>Ready</span>
                @elseif ($film->trailer_path)<span class="badge badge-warning"><span class="size-1.5 animate-pulse rounded-full bg-amber-500"></span>Processing</span>
                @else<span class="badge badge-neutral">None</span>@endif
              </td>
              <td class="hidden sm:table-cell">
                <button type="button" x-on:click="toggleFeatured('{{ $film->uuid }}', '{{ route('admin.coming-soon.toggle-featured', $film) }}')"
                  class="badge px-3 py-1.5 transition hover:opacity-80" :class="featured['{{ $film->uuid }}'] ? 'badge-brand' : 'badge-neutral'"
                  :title="featured['{{ $film->uuid }}'] ? 'Unfeature' : 'Feature'">
                  <span x-text="featured['{{ $film->uuid }}'] ? '★ Featured' : '☆ Feature'"></span>
                </button>
              </td>
              <td>
                <div class="flex items-center justify-end gap-1.5">
                  <a href="{{ route('admin.coming-soon.edit', $film) }}" class="btn btn-secondary btn-sm"><x-ui.icon name="edit" class="size-3.5" /> Edit</a>
                  <x-admin.delete :action="route('admin.coming-soon.destroy', $film)" :confirm="'Remove ' . $film->title . '?'" label="Remove" />
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if ($films->hasPages())<div>{{ $films->links() }}</div>@endif
  </div>
@endif
@endsection
