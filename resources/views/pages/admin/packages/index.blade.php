@extends('layouts.admin')

@section('admin-title', 'Subscription Packages')

@section('admin-content')
<div class="flex items-center justify-between mb-8">
  <div>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Packages</h2>
    <p class="mt-1 text-sm text-gray-500">Manage pricing tiers and streaming features.</p>
  </div>
  <a href="{{ route('admin.packages.create') }}"
    class="inline-flex items-center gap-x-2 font-bold uppercase py-2.5 px-5 text-xs text-gray-900 bg-accent hover:bg-accent/90 transition-colors">
    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
    New Package
  </a>
</div>

@if ($packages->isEmpty())
  <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
    <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No packages yet</p>
    <a href="{{ route('admin.packages.create') }}" class="mt-4 inline-block text-accent text-xs font-bold hover:underline">Create your first package</a>
  </div>
@else

  {{-- ── Mobile cards (< lg) ── --}}
  <div class="lg:hidden border border-gray-200 dark:border-gray-600 divide-y divide-gray-200 dark:divide-gray-600">
    @foreach ($packages as $package)
      <div class="p-4">
        <div class="flex items-start justify-between gap-x-3">
          <div>
            <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $package->name }}</p>
            <p class="text-xs text-gray-500 mt-0.5">
              {{ strtoupper($package->currency) }} {{ number_format($package->price, 2) }} / {{ $package->interval }}
            </p>
          </div>
          <span class="shrink-0 text-[10px] font-bold uppercase px-2 py-0.5 {{ $package->is_active ? 'bg-accent/20 text-accent' : 'bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400' }}">
            {{ $package->is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>
        <p class="text-xs text-gray-500 mt-2">
          {{ $package->max_resolution }} · {{ $package->max_screens }} screen{{ $package->max_screens !== 1 ? 's' : '' }}
          @if (!$package->has_ads) · No ads @endif
          @if ($package->download_enabled) · Downloads @endif
        </p>
        @if ($package->description)
          <p class="text-xs text-gray-400 mt-1">{{ $package->description }}</p>
        @endif
        <div class="flex items-center gap-x-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
          <a href="{{ route('admin.packages.edit', $package) }}"
            class="text-xs font-bold uppercase text-accent hover:underline transition-colors">Edit</a>
          <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="inline"
            onsubmit="return confirm('Delete {{ addslashes($package->name) }}?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs font-bold uppercase text-gray-500 hover:text-red-500 transition-colors">Delete</button>
          </form>
        </div>
      </div>
    @endforeach
  </div>

  {{-- ── Desktop table (≥ lg) ── --}}
  <div class="hidden lg:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-6">Name</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-6">Price</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-6">Features</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest pr-6">Status</th>
          <th class="pb-3 font-bold uppercase text-xs text-gray-500 tracking-widest"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
        @foreach ($packages as $package)
          <tr>
            <td class="py-4 pr-6">
              <p class="font-bold text-gray-900 dark:text-white">{{ $package->name }}</p>
              @if ($package->description)
                <p class="text-xs text-gray-400 mt-0.5 max-w-xs">{{ $package->description }}</p>
              @endif
            </td>
            <td class="py-4 pr-6 text-gray-500 whitespace-nowrap">
              {{ strtoupper($package->currency) }} {{ number_format($package->price, 2) }}
              <span class="text-xs text-gray-400">/ {{ $package->interval }}</span>
            </td>
            <td class="py-4 pr-6 text-xs text-gray-500">
              <div class="flex flex-wrap gap-x-3 gap-y-1">
                <span>{{ $package->max_resolution }}</span>
                <span>{{ $package->max_screens }} screen{{ $package->max_screens !== 1 ? 's' : '' }}</span>
                @if (!$package->has_ads)<span class="text-accent">No ads</span>@endif
                @if ($package->download_enabled)<span>Downloads</span>@endif
                @if ($package->trial_period_days > 0)<span>{{ $package->trial_period_days }}d trial</span>@endif
              </div>
            </td>
            <td class="py-4 pr-6">
              <span class="text-xs font-bold uppercase px-2 py-1 {{ $package->is_active ? 'bg-accent/20 text-accent' : 'bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400' }}">
                {{ $package->is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="py-4 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-x-4">
                <a href="{{ route('admin.packages.edit', $package) }}"
                  class="text-xs font-bold uppercase text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors">Edit</a>
                <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="inline"
                  onsubmit="return confirm('Delete {{ addslashes($package->name) }}?')">
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
