@extends('layouts.admin')

@section('admin-title', 'Subscription Packages')

@section('admin-content')
<x-ui.page-header title="Packages" subtitle="Manage pricing tiers and streaming features.">
  <x-slot:actions><a href="{{ route('admin.packages.create') }}" class="btn btn-primary"><x-ui.icon name="plus" class="size-4" /> New package</a></x-slot:actions>
</x-ui.page-header>

@if ($packages->isEmpty())
  <x-ui.empty icon="package" title="No packages yet"><a href="{{ route('admin.packages.create') }}" class="btn btn-primary">Create your first package</a></x-ui.empty>
@else
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Name</th><th>Price</th><th class="hidden md:table-cell">Features</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
      <tbody>
        @foreach ($packages as $package)
          <tr>
            <td>
              <p class="font-semibold text-fg">{{ $package->name }}</p>
              @if ($package->description)<p class="line-clamp-1 max-w-xs text-xs text-muted">{{ $package->description }}</p>@endif
            </td>
            <td class="whitespace-nowrap font-semibold text-fg">{{ strtoupper($package->currency) }} {{ number_format($package->price, 2) }} <span class="text-xs font-normal text-muted">/ {{ $package->interval }}</span></td>
            <td class="hidden md:table-cell">
              <div class="flex max-w-md flex-wrap gap-1.5">
                <span class="badge badge-neutral">{{ $package->max_resolution }}</span>
                <span class="badge badge-neutral">{{ $package->max_screens }} screen{{ $package->max_screens !== 1 ? 's' : '' }}</span>
                @if (!$package->has_ads)<span class="badge badge-neutral">No ads</span>@endif
                @if ($package->download_enabled)<span class="badge badge-neutral">Downloads</span>@endif
                @if ($package->watch_party_enabled)<span class="badge badge-neutral">Watch party</span>@endif
                @if ($package->is_giftable)<span class="badge badge-neutral">Giftable</span>@endif
                @if ($package->trial_period_days > 0)<span class="badge badge-brand">{{ $package->trial_period_days }}d trial</span>@endif
              </div>
            </td>
            <td><span class="badge {{ $package->is_active ? 'badge-success' : 'badge-neutral' }}">{{ $package->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>
              <div class="flex items-center justify-end gap-1.5">
                <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-secondary btn-sm"><x-ui.icon name="edit" class="size-3.5" /> Edit</a>
                <x-admin.delete :action="route('admin.packages.destroy', $package)" :confirm="'Archive ' . $package->name . '? It will no longer be available to customers.'" label="Archive" />
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endif

@if ($archived->isNotEmpty())
  <div class="space-y-3">
    <h2 class="section-title">Archived packages</h2>
    <ul class="card divide-y divide-line">
      @foreach ($archived as $package)
        <li class="flex items-center justify-between gap-4 px-5 py-4">
          <div>
            <p class="font-semibold text-fg">{{ $package->name }}</p>
            <p class="text-xs text-muted">
              @if ($package->price == 0) Free @else {{ strtoupper($package->currency) }} {{ number_format($package->price, 2) }} / {{ $package->interval }} @endif
              · Archived {{ $package->deleted_at->diffForHumans() }}
            </p>
          </div>
          <form action="{{ route('admin.packages.restore', $package->uuid) }}" method="POST">@csrf<button type="submit" class="btn btn-secondary btn-sm">Restore</button></form>
        </li>
      @endforeach
    </ul>
  </div>
@endif
@endsection
