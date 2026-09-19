@extends('layouts.admin')

@section('admin-title', isset($package) ? 'Edit Package' : 'Create Package')

@section('admin-content')
@php
  $p = $package ?? null;
  $isEdit = isset($package);
  $isFree = (int) old('price', $p?->price ?? 1) === 0;
  $intervals = ['week' => 'Weekly', 'month' => 'Monthly', 'year' => 'Yearly'];
  $features = [
    ['download_enabled', 'Downloads', 'Subscribers can download content for offline viewing.', 'download', (bool) old('download_enabled', $p?->download_enabled ?? true)],
    ['watch_party_enabled', 'Watch party', 'Subscribers can create and join synchronised watch parties.', 'popcorn', (bool) old('watch_party_enabled', $p?->watch_party_enabled ?? false)],
    ['is_giftable', 'Giftable', 'Allow subscribers to gift this plan to others.', 'gift', (bool) old('is_giftable', $p?->is_giftable ?? true)],
    ['has_ads', 'Show ads', 'Display advertisements during playback on this plan.', 'tv', (bool) old('has_ads', $p?->has_ads ?? false)],
    ['is_active', 'Published', 'Visible on the pricing page and available for sign-up.', 'globe', (bool) old('is_active', $p?->is_active ?? true)],
  ];
@endphp

<x-ui.page-header :title="($isEdit ? 'Edit' : 'Create') . ' package'" subtitle="Configure pricing and feature access for this subscription tier." />

<form action="{{ $isEdit ? route('admin.packages.update', $p) : route('admin.packages.store') }}" method="POST" class="max-w-3xl space-y-6">
  @csrf
  @if ($isEdit) @method('PUT') @endif

  @if ($errors->any())
    <div class="alert alert-error !block"><ul class="list-inside list-disc space-y-0.5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
  @endif

  {{-- General --}}
  <div class="card card-pad space-y-5">
    <h2 class="card-title">General</h2>
    <div class="grid gap-5 sm:grid-cols-2">
      <div><label class="label">Package name</label><input type="text" name="name" value="{{ old('name', $p?->name) }}" required placeholder="e.g. Standard" class="input"></div>
      <div><label class="label">Display order</label><input type="number" name="sort_order" value="{{ old('sort_order', $p?->sort_order ?? 0) }}" min="0" class="input"><p class="hint">Lower numbers appear first on the pricing page.</p></div>
      <div class="sm:col-span-2"><label class="label">Description</label><textarea name="description" rows="3" placeholder="Briefly describe what's included in this package…" class="textarea">{{ old('description', $p?->description) }}</textarea></div>
    </div>
  </div>

  {{-- Pricing --}}
  <div class="card card-pad space-y-5" x-data="{ free: {{ $isFree ? 'true' : 'false' }} }">
    <div class="flex items-center justify-between">
      <h2 class="card-title">Pricing</h2>
      <button type="button" role="switch" :aria-checked="free" @click="free = !free" class="flex items-center gap-3 text-sm font-medium text-fg">
        <span class="relative inline-flex h-6 w-11 items-center rounded-full transition" :class="free ? 'bg-brand' : 'bg-surface-3'"><span class="inline-block size-5 rounded-full bg-white shadow transition" :class="free ? 'translate-x-[1.375rem]' : 'translate-x-0.5'"></span></span>
        Free package
      </button>
    </div>

    <template x-if="!free">
      <div class="grid gap-5 sm:grid-cols-3">
        <div><label class="label">Currency</label><input type="text" name="currency" value="{{ old('currency', $p?->currency ?? 'KES') }}" maxlength="3" placeholder="KES" class="input uppercase"></div>
        <div><label class="label">Price</label><input type="number" name="price" value="{{ old('price', $p?->price) }}" min="1" placeholder="0" class="input"></div>
        <div>
          <label class="label">Billing interval</label>
          <select name="interval" class="select">@foreach ($intervals as $val => $label)<option value="{{ $val }}" @selected(old('interval', $p?->interval) === $val)>{{ $label }}</option>@endforeach</select>
        </div>
      </div>
    </template>

    <template x-if="free">
      <div class="max-w-xs">
        <input type="hidden" name="price" value="0">
        <label class="label">Access duration</label>
        <select name="interval" class="select">@foreach ($intervals as $val => $label)<option value="{{ $val }}" @selected(old('interval', $p?->interval) === $val)>{{ $label }}</option>@endforeach</select>
        <p class="hint">How long access lasts before expiring.</p>
      </div>
    </template>
  </div>

  {{-- Limits --}}
  <div class="card card-pad space-y-5">
    <h2 class="card-title">Limits</h2>
    <div class="grid gap-5 sm:grid-cols-3">
      <div>
        <label class="label">Max resolution</label>
        <select name="max_resolution" class="select">@foreach (['720p' => '720p — HD', '1080p' => '1080p — Full HD', '4K' => '4K — Ultra HD'] as $val => $label)<option value="{{ $val }}" @selected(old('max_resolution', $p?->max_resolution) === $val)>{{ $label }}</option>@endforeach</select>
      </div>
      <div><label class="label">Simultaneous screens</label><input type="number" name="max_screens" value="{{ old('max_screens', $p?->max_screens ?? 1) }}" required min="1" class="input"><p class="hint">Profiles that can stream at the same time.</p></div>
      <div><label class="label">Max shared links</label><input type="number" name="max_shared_links" value="{{ old('max_shared_links', $p?->max_shared_links ?? 5) }}" required min="0" class="input"><p class="hint">Shared links a subscriber can create. 0 for unlimited.</p></div>
    </div>
  </div>

  {{-- Features --}}
  <div class="card card-pad space-y-4">
    <div><h2 class="card-title">Features</h2><p class="mt-0.5 text-sm text-muted">Click a card to toggle the feature on or off.</p></div>
    <div class="grid gap-3 sm:grid-cols-2">
      @foreach ($features as [$name, $title, $desc, $icon, $checked])
        <label x-data="{ on: {{ $checked ? 'true' : 'false' }} }" :class="on ? 'border-brand bg-brand-soft' : 'border-line hover:border-line-strong'" class="relative flex cursor-pointer items-start gap-3.5 rounded-2xl border p-4 transition">
          <input type="checkbox" name="{{ $name }}" value="1" x-model="on" class="sr-only">
          <span class="grid size-10 shrink-0 place-items-center rounded-xl transition" :class="on ? 'bg-brand text-white' : 'bg-surface-3 text-muted'"><x-ui.icon :name="$icon" class="size-5" /></span>
          <span class="min-w-0 flex-1">
            <span class="block text-sm font-semibold" :class="on ? 'text-fg' : 'text-fg-soft'">{{ $title }}</span>
            <span class="text-xs text-muted">{{ $desc }}</span>
          </span>
          <span class="grid size-5 shrink-0 place-items-center rounded-full border-2 transition" :class="on ? 'border-brand bg-brand text-white' : 'border-line-strong'"><x-ui.icon name="check" class="size-3" x-show="on" /></span>
        </label>
      @endforeach
    </div>
  </div>

  <div class="flex gap-3">
    <button type="submit" class="btn btn-primary btn-lg">{{ $isEdit ? 'Save changes' : 'Create package' }}</button>
    <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
  </div>
</form>
@endsection
