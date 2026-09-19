@extends('layouts.admin')

@section('admin-title', 'Movies')

@section('admin-content')
@php
  $hlsToken = base64_encode((auth()->id() ?? 0) . ':' . ($exp = time() + 7200) . ':' . hash_hmac('sha256', (auth()->id() ?? 0) . ':' . $exp, config('services.streaming.secret')));
@endphp
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
          <th>Title</th>
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
                  @if ($movie->genre || $movie->year)<p class="truncate text-xs text-muted">{{ collect([$movie->genre, $movie->year])->filter()->implode(' · ') }}</p>@endif
                  <div class="mt-1 flex flex-wrap gap-1">
                    @if ($movie->is_free)<span class="badge badge-success">Free</span>@endif
                    @if ($movie->is_corrupted)<span class="badge badge-danger"><x-ui.icon name="alert" class="size-3" /> Corrupted</span>@endif
                  </div>
                </div>
              </div>
            </td>
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
                  <x-admin.video-preview :title="$movie->title" label="Watch" :src="$movie->streaming_path ? $movie->streamingUrl() : ($movie->video_path ? $movie->videoUrl() : null)" :hls="(bool) $movie->streaming_path"
                    :report-url="route('admin.movies.corrupted', $movie)" :clear-url="route('admin.movies.corrupted.clear', $movie)" :corrupted="$movie->is_corrupted" :token="$hlsToken" compact />
                  <a href="{{ route('admin.movies.edit', $movie) }}" class="btn btn-secondary btn-sm" title="Edit"><x-ui.icon name="edit" class="size-3.5" /> <span class="hidden 2xl:inline">Edit</span></a>
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
