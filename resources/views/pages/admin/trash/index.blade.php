@extends('layouts.admin')

@section('admin-title', 'Trash')

@section('admin-content')
@php
  $msg = match (session('status')) {
    'movie-restored'       => 'Movie restored successfully.',
    'movie-force-deleted'  => 'Movie permanently deleted.',
    'series-restored'      => 'Series restored successfully.',
    'series-force-deleted' => 'Series permanently deleted.',
    default                => null,
  };
@endphp

<x-ui.page-header title="Trash" subtitle="Soft-deleted content — restore or permanently delete." />

@if ($msg)<div class="alert alert-success"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />{{ $msg }}</div>@endif

@foreach ([['Movies', $movies, 'movie', 'admin.movies.restore', 'admin.movies.force-delete', 'No deleted movies', 'film'], ['Series', $series, 'series', 'admin.series.restore', 'admin.series.force-delete', 'No deleted series', 'tv']] as [$title, $items, $key, $restoreRoute, $forceRoute, $emptyText, $icon])
  <section class="space-y-3">
    <h2 class="section-title">{{ $title }} <span class="text-base font-normal text-muted">({{ $items->count() }})</span></h2>
    @if ($items->isEmpty())
      <x-ui.empty :icon="$icon" :title="$emptyText" class="!py-10" />
    @else
      <ul class="card divide-y divide-line">
        @foreach ($items as $item)
          <li class="flex flex-wrap items-center gap-4 px-5 py-4">
            <x-admin.thumb :src="$item->thumbnail ? Storage::url($item->thumbnail) : null" />
            <div class="min-w-0 flex-1">
              <p class="truncate font-semibold text-fg">{{ $item->title }}</p>
              <p class="text-xs text-muted">Deleted {{ $item->deleted_at->diffForHumans() }}@if ($item->genre) &middot; {{ $item->genre }}@endif</p>
            </div>
            <div class="flex items-center gap-1.5">
              <form method="POST" action="{{ route($restoreRoute, $item->id) }}">@csrf @method('PATCH')<button type="submit" class="btn btn-secondary btn-sm">Restore</button></form>
              <x-admin.delete :action="route($forceRoute, $item->id)" :confirm="'Permanently delete “' . $item->title . '”? This cannot be undone.'" label="Delete forever" />
            </div>
          </li>
        @endforeach
      </ul>
    @endif
  </section>
@endforeach
@endsection
