@extends('layouts.admin')

@section('admin-title', 'Users')

@section('admin-content')
<x-ui.page-header title="Users" :subtitle="$users->total() . ' ' . Str::plural('user', $users->total()) . ' registered'" />

@if (session('success'))<div class="alert alert-success"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />{{ session('success') }}</div>@endif
@if (session('error'))<div class="alert alert-error"><x-ui.icon name="alert" class="mt-0.5 size-5 shrink-0" />{{ session('error') }}</div>@endif

@php $filtersActive = request()->hasAny(['search', 'role', 'subscription', 'status']) && collect(request()->only(['search', 'role', 'subscription', 'status']))->filter()->isNotEmpty(); @endphp
<form method="GET" action="{{ route('admin.users.index') }}" class="space-y-3">
  <div class="flex flex-col gap-3 sm:flex-row">
    <div class="relative flex-1"><x-ui.icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-muted" /><input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email…" class="input !pl-10"></div>
    <button type="submit" class="btn btn-primary">Search</button>
  </div>

  <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-[repeat(3,minmax(0,14rem))_auto]">
    <select name="role" class="select" aria-label="Role" onchange="this.form.submit()">
      <option value="">All roles</option>
      <option value="admin" @selected(request('role') === 'admin')>Admin</option>
      <option value="user" @selected(request('role') === 'user')>Regular user (no role)</option>
      @foreach ($roleOptions as $r)
        <option value="r:{{ $r }}" @selected(request('role') === 'r:' . $r)>{{ ucwords(str_replace('-', ' ', $r)) }}</option>
      @endforeach
    </select>
    <select name="subscription" class="select" aria-label="Subscription status" onchange="this.form.submit()">
      <option value="">Any subscription</option>
      <option value="active" @selected(request('subscription') === 'active')>Active</option>
      <option value="expired" @selected(request('subscription') === 'expired')>Expired</option>
      <option value="none" @selected(request('subscription') === 'none')>No subscription</option>
    </select>
    <select name="status" class="select" aria-label="Account status" onchange="this.form.submit()">
      <option value="">Any account status</option>
      <option value="active" @selected(request('status') === 'active')>Active</option>
      <option value="banned" @selected(request('status') === 'banned')>Banned</option>
      <option value="unverified" @selected(request('status') === 'unverified')>Email not verified</option>
    </select>
    @if ($filtersActive)<a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Clear filters</a>@endif
  </div>
</form>

@if ($users->isEmpty())
  <x-ui.empty icon="users" title="No users found" />
@else
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Name</th><th class="hidden sm:table-cell">Role</th><th class="hidden md:table-cell">Subscription</th><th>Status</th><th class="text-right"></th></tr></thead>
      <tbody>
        @foreach ($users as $user)
          @php
            $latestSub   = $user->userSubscriptions->first();
            $subActive   = $latestSub && $latestSub->status === 'active' && ($latestSub->expires_at === null || $latestSub->expires_at->isFuture());
            $pendingGift = $pendingGifts[$user->email] ?? null;
          @endphp
          <tr>
            <td>
              <div class="flex items-center gap-3">
                @if ($user->avatarUrl())<img src="{{ $user->avatarUrl() }}" alt="" class="size-9 rounded-full object-cover">@else<span class="grid size-9 shrink-0 place-items-center rounded-full bg-brand-soft text-xs font-bold text-brand">{{ $user->initials() }}</span>@endif
                <div class="min-w-0">
                  <p class="truncate font-semibold text-fg">{{ $user->name }} @if ($user->is(auth()->user()))<span class="text-xs font-normal text-muted">(you)</span>@endif</p>
                  <p class="truncate text-xs text-muted">{{ $user->email }}</p>
                </div>
              </div>
            </td>
            <td class="hidden sm:table-cell">
              @if ($user->isAdmin())<span class="badge badge-brand">Admin</span>
              @elseif ($user->roles->first())<span class="badge badge-info capitalize">{{ str_replace('-', ' ', $user->roles->first()->name) }}</span>
              @else<span class="badge badge-neutral">User</span>@endif
            </td>
            <td class="hidden md:table-cell">
              @if ($latestSub)
                <span class="badge {{ $subActive ? 'badge-success' : 'badge-neutral' }}">{{ $subActive ? 'Active' : 'Expired' }}</span>
                @if ($latestSub->package)<span class="text-xs text-muted">· {{ $latestSub->package->name }}</span>@endif
                @if ($latestSub->expires_at)<p class="mt-0.5 text-xs text-muted">{{ $subActive ? 'Expires' : 'Expired' }} {{ $latestSub->expires_at->format('M j, Y') }}</p>@endif
              @elseif ($pendingGift)
                <span class="badge badge-warning">Gift pending</span>
                @if ($pendingGift->package)<span class="text-xs text-muted">· {{ $pendingGift->package->name }}</span>@endif
                <p class="mt-0.5 text-xs text-muted">Awaiting claim</p>
              @else
                <span class="text-muted">None</span>
              @endif
            </td>
            <td>
              @if ($user->isBanned())<span class="badge badge-danger"><span class="size-1.5 rounded-full bg-red-500"></span>Banned</span>
              @else<span class="badge badge-success"><span class="size-1.5 rounded-full bg-emerald-500"></span>Active</span>@endif
            </td>
            <td class="text-right"><a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary btn-sm">Manage <x-ui.icon name="arrow-right" class="size-3.5" /></a></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endif
@if ($users->hasPages())<div>{{ $users->links() }}</div>@endif
@endsection
