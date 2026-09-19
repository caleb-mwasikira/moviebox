@extends('layouts.admin')

@section('admin-title', 'Movies')

@section('admin-content')
<x-ui.page-header title="Movies" subtitle="Manage your movie catalogue.">
  <x-slot:actions>
    <div class="inline-flex rounded-xl border border-line bg-surface p-1">
      <a href="{{ route('admin.movies.index') }}" class="rounded-lg px-3.5 py-1.5 text-sm font-semibold transition {{ !$trashed ? 'bg-brand text-white' : 'text-fg-soft hover:text-fg' }}">Active</a>
      <a href="{{ route('admin.movies.index', ['trashed' => 1]) }}" class="rounded-lg px-3.5 py-1.5 text-sm font-semibold transition {{ $trashed ? 'bg-brand text-white' : 'text-fg-soft hover:text-fg' }}">Trashed</a>
    </div>
    @if (!$trashed)<a href="{{ route('admin.movies.create') }}" class="btn btn-primary"><x-ui.icon name="upload" class="size-4" /> Upload movie</a>@endif
  </x-slot:actions>
</x-ui.page-header>

@if ($movies->isEmpty())
  <x-ui.empty icon="film" :title="$trashed ? 'No trashed movies' : 'No movies yet'">
    @unless ($trashed)<a href="{{ route('admin.movies.create') }}" class="btn btn-primary">Upload your first movie</a>@endunless
  </x-ui.empty>
@else
  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>Title</th><th class="hidden md:table-cell">Genre</th><th class="hidden md:table-cell">Year</th>
          @if (!$trashed)<th>Status</th><th class="hidden sm:table-cell">Featured</th>@else<th>Deleted</th>@endif
          <th class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($movies as $movie)
          <tr class="{{ $trashed ? 'opacity-70 hover:opacity-100' : '' }}">
            <td>
              <div class="flex items-center gap-3">
                <x-admin.thumb :src="$movie->thumbnail ? $movie->thumbnailUrl() : null" />
                <div class="min-w-0">
                  <p class="truncate font-semibold text-fg">{{ $movie->title }}</p>
                  @if ($movie->is_free)<span class="badge badge-success mt-1">Free</span>@endif
                </div>
              </div>
            </td>
            <td class="hidden md:table-cell">{{ $movie->genre ?? '—' }}</td>
            <td class="hidden md:table-cell">{{ $movie->year ?? '—' }}</td>
            @if (!$trashed)
              <td><x-admin.toggle :action="route('admin.movies.toggle-status', $movie)" :on="$movie->isPublished()" on-label="Published" off-label="Hidden" /></td>
              <td class="hidden sm:table-cell"><x-admin.toggle :action="route('admin.movies.toggle-featured', $movie)" :on="$movie->isFeatured()" on-label="★ Featured" off-label="☆ Feature" /></td>
            @else
              <td class="text-xs text-muted">{{ $movie->deleted_at->diffForHumans() }}</td>
            @endif
            <td>
              <div class="flex items-center justify-end gap-1.5">
                @if ($trashed)
                  <form method="POST" action="{{ route('admin.movies.restore', $movie->id) }}">@csrf @method('PATCH')<button type="submit" class="btn btn-secondary btn-sm">Restore</button></form>
                  @can('force delete movies')
                    <x-admin.delete :action="route('admin.movies.force-delete', $movie->id)" :confirm="'Permanently delete ' . $movie->title . '? This cannot be undone.'" label="Delete forever" />
                  @endcan
                @else
                  <a href="{{ route('admin.movies.edit', $movie) }}" class="btn btn-secondary btn-sm"><x-ui.icon name="edit" class="size-3.5" /> Edit</a>
                  <x-admin.delete :action="route('admin.movies.destroy', $movie)" :confirm="'Delete ' . $movie->title . '?'" />
                @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div>{{ $movies->links() }}</div>
@endif
@endsection
