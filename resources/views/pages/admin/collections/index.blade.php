@extends('layouts.admin')

@section('admin-title', 'Collections')

@section('admin-content')
<div class="flex items-center justify-between mb-8">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Collections</h2>
  <a href="{{ route('admin.collections.create') }}"
    class="inline-flex items-center gap-x-2 font-bold uppercase py-2.5 px-5 text-xs text-gray-900 bg-accent hover:bg-accent/90 transition-colors">
    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
    New Collection
  </a>
</div>

@if ($collections->isEmpty())
  <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
    <svg class="mx-auto mb-3 w-10 h-10 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12z"/><path d="M6 10h12v2H6zm0 4h8v2H6z"/></svg>
    <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No collections yet</p>
    <a href="{{ route('admin.collections.create') }}" class="mt-4 inline-block text-accent text-xs font-bold hover:underline">Create your first collection</a>
  </div>
@else

  {{-- Mobile cards --}}
  <div class="lg:hidden border border-gray-200 dark:border-gray-600 divide-y divide-gray-200 dark:divide-gray-600">
    @foreach ($collections as $collection)
      <div class="p-4">
        <div class="flex gap-x-3">
          @if ($collection->thumbnail)
            <img src="{{ $collection->thumbnailUrl() }}" class="w-12 h-12 object-cover shrink-0" alt="">
          @else
            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 shrink-0 flex items-center justify-center">
              <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z"/></svg>
            </div>
          @endif
          <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $collection->name }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $collection->items_count }} {{ Str::plural('item', $collection->items_count) }}</p>
            <div class="flex flex-wrap items-center gap-2 mt-2">
              <span class="text-[10px] font-bold uppercase px-2 py-0.5 {{ $collection->is_active ? 'bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                {{ $collection->is_active ? 'Active' : 'Hidden' }}
              </span>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-x-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
          <a href="{{ route('admin.collections.edit', $collection) }}"
            class="text-xs font-bold uppercase text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors">Edit</a>
          <a href="{{ route('collections.show', $collection) }}" target="_blank"
            class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">View</a>
          <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}"
            onsubmit="return confirm('Delete &quot;{{ addslashes($collection->name) }}&quot;? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-red-500 transition-colors">Delete</button>
          </form>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Desktop table --}}
  <div class="hidden lg:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Name</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Items</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Order</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-4">Status</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
        @foreach ($collections as $collection)
          <tr class="group">
            <td class="py-3 pr-4">
              <div class="flex items-center gap-x-3">
                @if ($collection->thumbnail)
                  <img src="{{ $collection->thumbnailUrl() }}" class="w-10 h-10 object-cover shrink-0" alt="">
                @else
                  <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 shrink-0 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z"/></svg>
                  </div>
                @endif
                <div>
                  <p class="font-bold text-gray-900 dark:text-white">{{ $collection->name }}</p>
                  @if ($collection->description)
                    <p class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($collection->description, 60) }}</p>
                  @endif
                </div>
              </div>
            </td>
            <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ $collection->items_count }}</td>
            <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ $collection->sort_order }}</td>
            <td class="py-3 pr-4">
              <span class="text-[10px] font-bold uppercase px-2 py-0.5 {{ $collection->is_active ? 'bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
                {{ $collection->is_active ? 'Active' : 'Hidden' }}
              </span>
            </td>
            <td class="py-3">
              <div class="flex items-center gap-x-4">
                <a href="{{ route('admin.collections.edit', $collection) }}"
                  class="text-xs font-bold uppercase text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors">Edit</a>
                <a href="{{ route('collections.show', $collection) }}" target="_blank"
                  class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">View</a>
                <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}"
                  onsubmit="return confirm('Delete &quot;{{ addslashes($collection->name) }}&quot;? This cannot be undone.')">
                  @csrf @method('DELETE')
                  <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-red-500 transition-colors">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endif
@endsection
