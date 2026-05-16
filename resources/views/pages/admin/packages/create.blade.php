@extends('layouts.admin')

@section('admin-content')
@php
  $p = $package ?? null;
  $isEdit = isset($package);
@endphp

<div class="max-w-3xl">

  <div class="mb-8">
    <h2 class="text-3xl font-black uppercase tracking-tighter text-gray-900 dark:text-white">
      {{ $isEdit ? 'Edit' : 'Create' }} <span class="text-accent">Package</span>
    </h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
      Configure pricing and feature access for this subscription tier.
    </p>
  </div>

  <form action="{{ $isEdit ? route('admin.packages.update', $p) : route('admin.packages.store') }}" method="POST">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    @if ($errors->any())
      <div class="mb-6 border border-red-400 bg-red-50 dark:bg-red-900/20 p-4">
        <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="flex flex-col gap-y-6">

      {{-- ── Identity ── --}}
      <div class="bg-white dark:bg-gray-700 p-6 md:p-8 border-l-4 border-accent">
        <h3 class="text-[10px] font-bold uppercase tracking-widest text-accent mb-6">General</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold uppercase text-gray-400 mb-1.5">Package Name</label>
            <input type="text" name="name" value="{{ old('name', $p?->name) }}" required
              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-accent outline-none transition-colors @error('name') border-red-400 @enderror"
              placeholder="e.g. Standard">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase text-gray-400 mb-1.5">Display Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $p?->sort_order ?? 0) }}" min="0"
              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-accent outline-none transition-colors">
            <p class="mt-1 text-[11px] text-gray-400">Lower numbers appear first on the pricing page.</p>
          </div>
        </div>
      </div>

      {{-- ── Pricing ── --}}
      <div class="bg-white dark:bg-gray-700 p-6 md:p-8">
        <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-6">Pricing</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
          <div>
            <label class="block text-xs font-bold uppercase text-gray-400 mb-1.5">Currency</label>
            <input type="text" name="currency" value="{{ old('currency', $p?->currency ?? 'KSH') }}" required maxlength="3"
              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-accent outline-none transition-colors @error('currency') border-red-400 @enderror"
              placeholder="KSH">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase text-gray-400 mb-1.5">Price</label>
            <input type="number" name="price" value="{{ old('price', $p?->price) }}" required min="0"
              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-accent outline-none transition-colors @error('price') border-red-400 @enderror"
              placeholder="0">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase text-gray-400 mb-1.5">Billing Interval</label>
            <select name="interval"
              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-accent outline-none transition-colors">
              @foreach (['week' => 'Weekly', 'month' => 'Monthly', 'year' => 'Yearly'] as $val => $label)
                <option value="{{ $val }}" {{ old('interval', $p?->interval) === $val ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      {{-- ── Limits ── --}}
      <div class="bg-white dark:bg-gray-700 p-6 md:p-8">
        <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-6">Limits</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-bold uppercase text-gray-400 mb-1.5">Max Resolution</label>
            <select name="max_resolution"
              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-accent outline-none transition-colors">
              @foreach (['720p' => '720p — HD', '1080p' => '1080p — Full HD', '4K' => '4K — Ultra HD'] as $val => $label)
                <option value="{{ $val }}" {{ old('max_resolution', $p?->max_resolution) === $val ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase text-gray-400 mb-1.5">Simultaneous Screens</label>
            <input type="number" name="max_screens" value="{{ old('max_screens', $p?->max_screens ?? 1) }}" required min="1"
              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-accent outline-none transition-colors @error('max_screens') border-red-400 @enderror">
            <p class="mt-1 text-[11px] text-gray-400">Profiles that can stream at the same time.</p>
          </div>
        </div>
      </div>

      {{-- ── Features ── --}}
      <div class="bg-white dark:bg-gray-700 p-6 md:p-8">
        <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Features</h3>
        <p class="text-xs text-gray-400 mb-6">Click a card to toggle the feature on or off.</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

          {{-- Downloads --}}
          @php $dlChecked = (bool) old('download_enabled', $p?->download_enabled ?? true); @endphp
          <label x-data="{ on: {{ $dlChecked ? 'true' : 'false' }} }"
            :class="on ? 'border-accent bg-accent/5 dark:bg-accent/10' : 'border-gray-200 dark:border-gray-600'"
            class="relative flex flex-col gap-y-4 p-5 border-2 cursor-pointer select-none transition-colors">
            <input type="checkbox" name="download_enabled" value="1" x-model="on" class="sr-only">
            <div class="flex items-start justify-between gap-x-3">
              <div :class="on ? 'bg-accent/10 dark:bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-600 text-gray-400'"
                class="p-2 transition-colors">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M5 20h14v-2H5v2zM19 9h-4V3H9v6H5l7 7 7-7z"/></svg>
              </div>
              <div :class="on ? 'bg-accent border-accent' : 'border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-800'"
                class="w-5 h-5 border-2 flex items-center justify-center shrink-0 transition-colors mt-0.5">
                <svg x-show="on" class="w-3 h-3 fill-gray-900" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              </div>
            </div>
            <div>
              <p :class="on ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                class="font-bold text-sm transition-colors">Downloads</p>
              <p class="text-xs text-gray-400 mt-0.5 leading-snug">Subscribers can download content for offline viewing.</p>
            </div>
          </label>

          {{-- Ads --}}
          @php $adsChecked = (bool) old('has_ads', $p?->has_ads ?? false); @endphp
          <label x-data="{ on: {{ $adsChecked ? 'true' : 'false' }} }"
            :class="on ? 'border-accent bg-accent/5 dark:bg-accent/10' : 'border-gray-200 dark:border-gray-600'"
            class="relative flex flex-col gap-y-4 p-5 border-2 cursor-pointer select-none transition-colors">
            <input type="checkbox" name="has_ads" value="1" x-model="on" class="sr-only">
            <div class="flex items-start justify-between gap-x-3">
              <div :class="on ? 'bg-accent/10 dark:bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-600 text-gray-400'"
                class="p-2 transition-colors">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M20 3H4v10c0 2.21 1.79 4 4 4h6c2.21 0 4-1.79 4-4v-3h2c1.11 0 2-.89 2-2V5c0-1.11-.89-2-2-2zm0 5h-2V5h2v3zM4 19h16v2H4z"/></svg>
              </div>
              <div :class="on ? 'bg-accent border-accent' : 'border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-800'"
                class="w-5 h-5 border-2 flex items-center justify-center shrink-0 transition-colors mt-0.5">
                <svg x-show="on" class="w-3 h-3 fill-gray-900" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              </div>
            </div>
            <div>
              <p :class="on ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                class="font-bold text-sm transition-colors">Show Ads</p>
              <p class="text-xs text-gray-400 mt-0.5 leading-snug">Display advertisements during playback on this plan.</p>
            </div>
          </label>

          {{-- Published --}}
          @php $activeChecked = (bool) old('is_active', $p?->is_active ?? true); @endphp
          <label x-data="{ on: {{ $activeChecked ? 'true' : 'false' }} }"
            :class="on ? 'border-accent bg-accent/5 dark:bg-accent/10' : 'border-gray-200 dark:border-gray-600'"
            class="relative flex flex-col gap-y-4 p-5 border-2 cursor-pointer select-none transition-colors">
            <input type="checkbox" name="is_active" value="1" x-model="on" class="sr-only">
            <div class="flex items-start justify-between gap-x-3">
              <div :class="on ? 'bg-accent/10 dark:bg-accent/20 text-accent' : 'bg-gray-100 dark:bg-gray-600 text-gray-400'"
                class="p-2 transition-colors">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
              </div>
              <div :class="on ? 'bg-accent border-accent' : 'border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-800'"
                class="w-5 h-5 border-2 flex items-center justify-center shrink-0 transition-colors mt-0.5">
                <svg x-show="on" class="w-3 h-3 fill-gray-900" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              </div>
            </div>
            <div>
              <p :class="on ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'"
                class="font-bold text-sm transition-colors">Published</p>
              <p class="text-xs text-gray-400 mt-0.5 leading-snug">Visible on the pricing page and available for sign-up.</p>
            </div>
          </label>

        </div>
      </div>

    </div>

    <div class="mt-8 flex items-center gap-x-4">
      <button type="submit"
        class="font-bold uppercase tracking-tight py-4 px-10 text-sm text-gray-900 bg-accent hover:bg-accent/90 transition-colors">
        {{ $isEdit ? 'Save Changes' : 'Create Package' }}
      </button>
      <a href="{{ route('admin.packages.index') }}"
        class="text-xs font-bold uppercase text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
        Cancel
      </a>
    </div>

  </form>
</div>
@endsection
