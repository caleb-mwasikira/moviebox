@extends('layouts.admin')

@section('admin-title', $user->name)

@section('admin-content')
@php
  $statGroups = [['label' => 'Movies', 'type' => 'Movie', 'icon' => 'film'], ['label' => 'Series', 'type' => 'Series', 'icon' => 'tv'], ['label' => 'Episodes', 'type' => 'Episode', 'icon' => 'play']];
  $statActions = ['created' => ['Uploaded', 'badge-success'], 'updated' => ['Edited', 'badge-info'], 'deleted' => ['Deleted', 'badge-warning'], 'force_deleted' => ['Purged', 'badge-danger']];
  $latestSub = $user->userSubscriptions->first();
  $subActive = $latestSub && $latestSub->status === 'active' && ($latestSub->expires_at === null || $latestSub->expires_at->isFuture());
@endphp

<a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-brand"><x-ui.icon name="arrow-left" class="size-4" /> Back to users</a>

{{-- Header --}}
<div class="card card-pad flex flex-wrap items-center gap-5">
  @if ($user->avatarUrl())
    <img src="{{ $user->avatarUrl() }}" alt="" class="size-20 rounded-full object-cover ring-4 ring-brand-soft">
  @else
    <span class="grid size-20 place-items-center rounded-full bg-brand-soft text-2xl font-bold text-brand">{{ $user->initials() }}</span>
  @endif
  <div class="min-w-0">
    <div class="flex flex-wrap items-center gap-2">
      <h2 class="text-2xl font-bold text-fg">{{ $user->name }}</h2>
      @if ($user->isAdmin())<span class="badge badge-brand">Admin</span>
      @elseif ($user->roles->first())<span class="badge badge-info capitalize">{{ str_replace('-', ' ', $user->roles->first()->name) }}</span>
      @else<span class="badge badge-neutral">User</span>@endif
      @if ($user->isBanned())<span class="badge badge-danger">Banned</span>@endif
    </div>
    <p class="text-sm text-muted">{{ $user->email }}</p>
    <p class="mt-1 text-xs text-muted">Joined {{ $user->created_at->format('M j, Y') }} · @if ($user->email_verified_at)<span class="font-semibold text-emerald-500">Verified</span>@else<span class="font-semibold text-amber-500">Unverified</span>@endif</p>
  </div>
</div>

@if (session('success'))<div class="alert alert-success"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />{{ session('success') }}</div>@endif
@if (session('error'))<div class="alert alert-error"><x-ui.icon name="alert" class="mt-0.5 size-5 shrink-0" />{{ session('error') }}</div>@endif

{{-- Activity stats --}}
<div class="grid gap-4 sm:grid-cols-3">
  @foreach ($statGroups as $group)
    @php
      $total = 0; $parts = [];
      foreach ($statActions as $action => [$label, $badge]) {
          $cnt = $activityStats->get("{$action}|{$group['type']}")?->total ?? 0;
          $total += $cnt;
          if ($cnt > 0) $parts[] = [$label, $badge, $cnt];
      }
    @endphp
    <div class="card card-pad">
      <div class="flex items-center gap-3">
        <span class="grid size-10 place-items-center rounded-xl bg-brand-soft text-brand"><x-ui.icon :name="$group['icon']" /></span>
        <span class="text-sm font-semibold text-fg">{{ $group['label'] }}</span>
        <span class="ml-auto text-2xl font-bold text-fg">{{ $total }}</span>
      </div>
      <div class="mt-3 flex flex-wrap gap-1.5">
        @forelse ($parts as [$label, $badge, $cnt])<span class="badge {{ $badge }}">{{ $label }} {{ $cnt }}</span>@empty<span class="text-xs text-muted">no actions</span>@endforelse
      </div>
    </div>
  @endforeach
</div>

<div class="grid gap-6 lg:grid-cols-3">
  <div class="space-y-6 lg:col-span-2">
    {{-- Subscription --}}
    <div class="card card-pad space-y-4">
      <h3 class="card-title">Subscription</h3>
      @if ($latestSub)
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <div class="flex items-center gap-2">
              <span class="badge {{ $subActive ? 'badge-success' : 'badge-neutral' }}"><span class="size-1.5 rounded-full {{ $subActive ? 'bg-emerald-500' : 'bg-muted' }}"></span>{{ $subActive ? 'Active' : 'Expired' }}</span>
              @if ($latestSub->package)<span class="text-sm font-semibold text-fg">{{ $latestSub->package->name }}</span>@endif
            </div>
            <p class="mt-1.5 text-sm text-muted">
              @if ($latestSub->expires_at){{ $subActive ? 'Expires' : 'Expired' }} {{ $latestSub->expires_at->format('M j, Y') }} · {{ $latestSub->expires_at->diffForHumans() }}@else No expiry @endif
            </p>
          </div>
          @if (!$user->isAdmin())
            @can('manage user subscriptions')
              <form method="POST" action="{{ route('admin.users.extend-subscription', $user) }}" class="flex items-end gap-2">
                @csrf @method('PATCH')
                <div><label class="label">Extend by days</label><input type="number" name="days" min="1" max="3650" value="30" class="input !w-28"></div>
                <button type="submit" class="btn btn-primary">Extend</button>
              </form>
            @endcan
          @endif
        </div>
      @else
        <p class="text-sm text-muted">No subscription on record.</p>
      @endif
    </div>

    {{-- Role --}}
    <div class="card card-pad space-y-4">
      <h3 class="card-title">Role</h3>
      @if (!$user->isAdmin())
        @can('assign permissions')
          <div class="flex flex-wrap items-end gap-3">
            <form method="POST" action="{{ route('admin.users.assign-role', $user) }}" id="role-form" class="flex items-end gap-2">
              @csrf @method('PATCH')
              <select name="role" class="select !w-56">
                <option value="">No role</option>
                @foreach ($roles as $r)<option value="{{ $r->name }}" @selected($user->roles->first()?->name === $r->name)>{{ ucwords(str_replace('-', ' ', $r->name)) }}</option>@endforeach
              </select>
              <button type="submit" class="btn btn-primary">Save</button>
            </form>
            <a href="{{ route('admin.users.permissions', $user) }}" class="btn btn-secondary">Manage permissions <x-ui.icon name="arrow-right" class="size-3.5" /></a>
          </div>
        @endcan
      @else
        <p class="text-sm font-semibold text-fg">Super Admin</p>
        <p class="text-sm text-muted">Admins have all permissions and cannot be assigned roles.</p>
      @endif
    </div>

    {{-- Recent activity --}}
    <div class="card">
      <div class="card-header"><h3 class="card-title">Recent activity</h3><a href="{{ route('admin.activity.index', ['user_id' => $user->id]) }}" class="text-xs font-semibold text-brand hover:underline">View all →</a></div>
      @if ($recentActivity->isEmpty())
        <p class="p-6 text-sm text-muted">No activity recorded.</p>
      @else
        <ul class="divide-y divide-line">
          @foreach ($recentActivity as $log)
            @php $badge = match ($log->action) { 'created' => 'badge-success', 'updated' => 'badge-info', 'deleted' => 'badge-warning', 'restored' => 'badge-brand', default => 'badge-danger' }; @endphp
            <li class="flex items-center gap-3 px-5 py-3.5">
              <span class="badge {{ $badge }} shrink-0 uppercase">{{ $log->actionLabel() }}</span>
              <span class="min-w-0 flex-1 truncate text-sm text-fg-soft"><span class="font-semibold text-fg">{{ $log->subject_type }}</span> {{ $log->subject_title }}</span>
              <time class="shrink-0 text-xs text-muted">{{ $log->created_at->diffForHumans() }}</time>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>

  <div class="space-y-6">
    {{-- Account status --}}
    <div class="card card-pad space-y-4">
      <h3 class="card-title">Account status</h3>
      <div class="flex flex-wrap items-center gap-2 text-sm">
        @if ($user->isBanned())
          <span class="size-2.5 rounded-full bg-red-500"></span><span class="font-semibold text-red-500">Banned</span>
          @if ($user->banned_at)<span class="text-xs text-muted">since {{ $user->banned_at->format('M j, Y') }}</span>@endif
        @else
          <span class="size-2.5 rounded-full bg-emerald-500"></span><span class="font-semibold text-emerald-500">Active</span>
        @endif
      </div>
      @if (!$user->is(auth()->user()) && !$user->isAdmin())
        @can('ban users')
          @if ($user->isBanned())
            <form method="POST" action="{{ route('admin.users.unban', $user) }}">@csrf @method('PATCH')<button type="submit" class="btn btn-secondary btn-block">Unban user</button></form>
          @else
            <form method="POST" action="{{ route('admin.users.ban', $user) }}" onsubmit="return confirm('Ban {{ addslashes($user->name) }}?')">@csrf @method('PATCH')<button type="submit" class="btn btn-danger-soft btn-block"><x-ui.icon name="ban" class="size-4" /> Ban user</button></form>
          @endif
        @endcan
      @endif
    </div>

    {{-- Details --}}
    <div class="card card-pad">
      <h3 class="card-title mb-4">Details</h3>
      <dl class="space-y-3 text-sm">
        @foreach (array_merge([['ID', $user->id], ['Email', $user->email], ['Email verified', $user->email_verified_at ? $user->email_verified_at->format('M j, Y') : 'No'], ['Joined', $user->created_at->format('M j, Y')]], $user->social_provider ? [['Auth provider', $user->social_provider]] : []) as [$k, $v])
          <div class="flex items-start justify-between gap-4 border-b border-line pb-3 last:border-0 last:pb-0"><dt class="text-muted">{{ $k }}</dt><dd class="break-all text-right font-medium text-fg">{{ $v }}</dd></div>
        @endforeach
      </dl>
    </div>
  </div>
</div>
@endsection
