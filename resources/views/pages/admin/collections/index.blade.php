@extends('layouts.admin')

@section('admin-title', 'Collections')

@section('admin-content')
<x-ui.page-header title="Collections" subtitle="Themed playlists surfaced on the home page.">
  <x-slot:actions><a href="{{ route('admin.collections.create') }}" class="btn btn-primary"><x-ui.icon name="plus" class="size-4" /> New collection</a></x-slot:actions>
</x-ui.page-header>

@if ($collections->isEmpty())
  <x-ui.empty icon="folder" title="No collections yet"><a href="{{ route('admin.collections.create') }}" class="btn btn-primary">Create your first collection</a></x-ui.empty>
@else
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Name</th><th>Items</th><th class="hidden sm:table-cell">Order</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
      <tbody>
        @foreach ($collections as $collection)
          <tr>
            <td>
              <div class="flex items-center gap-3">
                <x-admin.thumb :src="$collection->thumbnail ? $collection->thumbnailUrl() : null" class="size-12" />
                <div class="min-w-0">
                  <p class="truncate font-semibold text-fg">{{ $collection->name }}</p>
                  @if ($collection->description)<p class="line-clamp-1 max-w-xs text-xs text-muted">{{ Str::limit($collection->description, 60) }}</p>@endif
                </div>
              </div>
            </td>
            <td>{{ $collection->items_count }}</td>
            <td class="hidden sm:table-cell">{{ $collection->sort_order }}</td>
            <td><span class="badge {{ $collection->is_active ? 'badge-success' : 'badge-neutral' }}">{{ $collection->is_active ? 'Active' : 'Hidden' }}</span></td>
            <td>
              <div class="flex items-center justify-end gap-1.5">
                <a href="{{ route('admin.collections.edit', $collection) }}" class="btn btn-secondary btn-sm">Edit</a>
                <a href="{{ route('collections.show', $collection) }}" target="_blank" class="btn btn-ghost btn-sm">View</a>
                <x-admin.delete :action="route('admin.collections.destroy', $collection)" :confirm="'Delete “' . $collection->name . '”? This cannot be undone.'" />
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endif
@endsection
