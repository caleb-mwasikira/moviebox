@extends('layouts.admin')

@section('admin-title', 'Dashboard')

@section('admin-content')
<div class="mb-10">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">
    Dashboard
  </h2>
  <p class="mt-2 text-gray-500 dark:text-gray">Welcome back, {{ auth()->user()->name }}.</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-12">
  <div class="border border-gray-200 dark:border-gray-600 p-6">
    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $movieCount }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Movies</div>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-6">
    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $seriesCount }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Series</div>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-6">
    <div class="text-3xl font-bold text-accent mb-1">{{ $published }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Published</div>
  </div>
  <div class="border border-gray-200 dark:border-gray-600 p-6">
    <div class="text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $hidden }}</div>
    <div class="text-xs font-bold uppercase text-gray-500 tracking-widest">Hidden</div>
  </div>
</div>

<div class="flex flex-wrap gap-4">
  <a href="{{ route('admin.movies.create') }}"
    class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
    + Upload Movie
  </a>
  <a href="{{ route('admin.series.create') }}"
    class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 border border-accent hover:bg-accent/10">
    + Add Series
  </a>
</div>
@endsection
