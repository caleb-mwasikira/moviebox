@extends('layouts.admin')

@section('admin-title', $user->name)

@section('admin-content')

{{-- Header --}}
<div class="mb-8">
  <a href="{{ route('admin.users.index') }}"
     class="text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors mb-3 inline-block">
    ← Back to Users
  </a>
  <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
    {{-- Avatar --}}
    @if ($user->avatarUrl())
      <img src="{{ $user->avatarUrl() }}" alt=""
        class="w-14 h-14 rounded-full object-cover ring-2 ring-accent/30 shrink-0">
    @else
      <div class="w-14 h-14 rounded-full bg-accent/15 flex items-center justify-center shrink-0">
        <span class="text-lg font-black text-accent">{{ $user->initials() }}</span>
      </div>
    @endif
    <div>
      <div class="flex items-center gap-x-2 flex-wrap">
        <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white">{{ $user->name }}</h2>
        @if ($user->isAdmin())
          <span class="text-xs font-bold uppercase px-2 py-0.5 bg-accent/15 text-accent">Admin</span>
        @elseif ($user->roles->first())
          <span class="text-xs font-bold uppercase px-2 py-0.5 bg-accent/10 text-accent/80 capitalize">{{ str_replace('-', ' ', $user->roles->first()->name) }}</span>
        @else
          <span class="text-xs font-bold uppercase px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-400">User</span>
        @endif
        @if ($user->isBanned())
          <span class="text-xs font-bold uppercase px-2 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-500">Banned</span>
        @endif
      </div>
      <p class="text-sm text-gray-500 mt-0.5">{{ $user->email }}</p>
      <p class="text-xs text-gray-400 mt-0.5">
        Joined {{ $user->created_at->format('M j, Y') }}
        @if ($user->email_verified_at)
          · <span class="text-green-500">Verified</span>
        @else
          · <span class="text-orange-400">Unverified</span>
        @endif
      </p>
    </div>
  </div>
</div>

{{-- Flash messages --}}
@if (session('success'))
  <div class="mb-6 px-4 py-3 border border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-950 text-green-700 dark:text-green-300 text-sm font-medium">
    {{ session('success') }}
  </div>
@endif
@if (session('error'))
  <div class="mb-6 px-4 py-3 border border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-950 text-red-700 dark:text-red-300 text-sm font-medium">
    {{ session('error') }}
  </div>
@endif

{{-- ── Activity stats ── --}}
@php
  $statGroups = [
      ['label' => 'Movies',   'type' => 'Movie'],
      ['label' => 'Series',   'type' => 'Series'],
      ['label' => 'Episodes', 'type' => 'Episode'],
  ];
  $statActions = [
      'created'      => ['label' => 'Uploaded',  'color' => 'text-green-500'],
      'updated'      => ['label' => 'Edited',    'color' => 'text-blue-500'],
      'deleted'      => ['label' => 'Deleted',   'color' => 'text-orange-500'],
      'force_deleted'=> ['label' => 'Purged',    'color' => 'text-red-500'],
  ];
@endphp
<div class="flex flex-wrap gap-3 mb-6">
  @foreach ($statGroups as $group)
    @php
      $total = 0;
      $parts = [];
      foreach ($statActions as $action => $meta) {
          $cnt = $activityStats->get("{$action}|{$group['type']}")?->total ?? 0;
          $total += $cnt;
          if ($cnt > 0) $parts[] = ['label' => $meta['label'], 'color' => $meta['color'], 'count' => $cnt];
      }
    @endphp
    <div class="border border-gray-200 dark:border-gray-700 px-3 py-2 flex items-center gap-x-3 min-w-0">
      <div class="flex items-center gap-x-1.5 shrink-0">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">{{ $group['label'] }}</span>
        <span class="text-sm font-black text-gray-900 dark:text-white">{{ $total }}</span>
      </div>
      @if (count($parts))
        <div class="flex items-center gap-x-2 flex-wrap">
          @foreach ($parts as $p)
            <span class="text-[10px] whitespace-nowrap"><span class="text-gray-400">{{ $p['label'] }}</span> <span class="font-bold {{ $p['color'] }}">{{ $p['count'] }}</span></span>
          @endforeach
        </div>
      @else
        <span class="text-[10px] text-gray-400">no actions</span>
      @endif
    </div>
  @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- ── Left column ── --}}
  <div class="lg:col-span-2 flex flex-col gap-y-6">

    {{-- Subscription --}}
    @php
      $latestSub = $user->userSubscriptions->first();
      $subActive = $latestSub && $latestSub->status === 'active'
          && ($latestSub->expires_at === null || $latestSub->expires_at->isFuture());
    @endphp
    <div class="border border-gray-200 dark:border-gray-700 p-6">
      <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4">Subscription</h3>

      @if ($latestSub)
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <div class="flex items-center gap-x-2">
              <span class="inline-flex items-center gap-x-1.5 text-sm font-bold uppercase {{ $subActive ? 'text-green-500' : 'text-red-500' }}">
                <span class="h-2 w-2 rounded-full {{ $subActive ? 'bg-green-500' : 'bg-red-500' }} inline-block"></span>
                {{ $subActive ? 'Active' : 'Expired' }}
              </span>
              @if ($latestSub->package)
                <span class="text-sm text-gray-500">· {{ $latestSub->package->name }}</span>
              @endif
            </div>
            @if ($latestSub->expires_at)
              <p class="text-xs text-gray-400 mt-1">
                {{ $subActive ? 'Expires' : 'Expired' }} {{ $latestSub->expires_at->format('M j, Y') }}
                <span class="text-gray-300 dark:text-gray-600 mx-1">·</span>
                {{ $latestSub->expires_at->diffForHumans() }}
              </p>
            @else
              <p class="text-xs text-gray-400 mt-1">No expiry</p>
            @endif
          </div>

          @if (!$user->isAdmin())
            @can('manage user subscriptions')
            <div>
              <p class="text-xs font-bold uppercase text-gray-500 mb-2">Extend by days</p>
              <form method="POST" action="{{ route('admin.users.extend-subscription', $user) }}" class="flex gap-x-2">
                @csrf @method('PATCH')
                <input type="number" name="days" min="1" max="3650" value="30"
                  class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-accent focus:outline-none">
                <button type="submit"
                  class="px-4 py-2 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors shrink-0">
                  Extend
                </button>
              </form>
            </div>
            @endcan
          @endif
        </div>
      @else
        <p class="text-sm text-gray-400">No subscription on record.</p>
      @endif
    </div>

    {{-- Role --}}
    @if (!$user->isAdmin())
      @can('assign permissions')
      <div class="border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4">Role</h3>
        <div class="flex flex-wrap items-end gap-4">
          <div class="flex-1 min-w-[160px]">
            <form method="POST" action="{{ route('admin.users.assign-role', $user) }}" class="flex gap-x-2" id="role-form">
              @csrf @method('PATCH')
              <select name="role"
                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-accent focus:outline-none">
                <option value="">No role</option>
                @foreach ($roles as $r)
                  <option value="{{ $r->name }}" {{ $user->roles->first()?->name === $r->name ? 'selected' : '' }}>
                    {{ ucwords(str_replace('-', ' ', $r->name)) }}
                  </option>
                @endforeach
              </select>
              <button type="submit"
                class="px-4 py-2 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors shrink-0">
                Save
              </button>
            </form>
          </div>
          <a href="{{ route('admin.users.permissions', $user) }}"
             class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors border border-gray-300 dark:border-gray-600 px-4 py-2 shrink-0">
            Manage Permissions →
          </a>
        </div>
      </div>
      @endcan
    @else
      <div class="border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Role</h3>
        <p class="text-sm text-gray-900 dark:text-white font-bold">Super Admin</p>
        <p class="text-xs text-gray-400 mt-0.5">Admins have all permissions and cannot be assigned roles.</p>
      </div>
    @endif

    {{-- Recent Activity --}}
    <div class="border border-gray-200 dark:border-gray-700 p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Recent Activity</h3>
        <a href="{{ route('admin.activity.index', ['user_id' => $user->id]) }}"
           class="text-xs font-bold uppercase text-accent hover:text-accent/70 transition-colors">View all →</a>
      </div>

      @if ($recentActivity->isEmpty())
        <p class="text-sm text-gray-400">No activity recorded.</p>
      @else
        <div class="divide-y divide-gray-100 dark:divide-gray-800 -mx-6">
          @foreach ($recentActivity as $log)
            <div class="flex items-center gap-x-3 px-6 py-2.5">
              <span class="shrink-0 text-[10px] font-bold uppercase tracking-widest px-2 py-0.5
                @if($log->action === 'created') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                @elseif($log->action === 'updated') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                @elseif($log->action === 'deleted') bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400
                @elseif($log->action === 'restored') bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400
                @else bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                @endif">
                {{ $log->actionLabel() }}
              </span>
              <span class="flex-1 text-sm text-gray-700 dark:text-gray-300 truncate">
                <span class="text-gray-400 text-xs">{{ $log->subject_type }}</span> {{ $log->subject_title }}
              </span>
              <time class="shrink-0 text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</time>
            </div>
          @endforeach
        </div>
      @endif
    </div>

  </div>

  {{-- ── Right column ── --}}
  <div class="flex flex-col gap-y-6">

    {{-- Account status --}}
    <div class="border border-gray-200 dark:border-gray-700 p-6">
      <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4">Account Status</h3>

      <div class="flex items-center gap-x-2 mb-4">
        @if ($user->isBanned())
          <span class="h-2.5 w-2.5 rounded-full bg-red-500 shrink-0"></span>
          <span class="text-sm font-bold text-red-500">Banned</span>
          @if ($user->banned_at)
            <span class="text-xs text-gray-400">since {{ $user->banned_at->format('M j, Y') }}</span>
          @endif
        @else
          <span class="h-2.5 w-2.5 rounded-full bg-green-500 shrink-0"></span>
          <span class="text-sm font-bold text-green-500">Active</span>
        @endif
      </div>

      @if (!$user->is(auth()->user()) && !$user->isAdmin())
        @can('ban users')
          @if ($user->isBanned())
            <form method="POST" action="{{ route('admin.users.unban', $user) }}">
              @csrf @method('PATCH')
              <button type="submit"
                class="w-full px-4 py-2 text-xs font-bold uppercase border border-green-400 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors">
                Unban User
              </button>
            </form>
          @else
            <form method="POST" action="{{ route('admin.users.ban', $user) }}"
              onsubmit="return confirm('Ban {{ addslashes($user->name) }}?')">
              @csrf @method('PATCH')
              <button type="submit"
                class="w-full px-4 py-2 text-xs font-bold uppercase border border-red-400 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                Ban User
              </button>
            </form>
          @endif
        @endcan
      @endif
    </div>

    {{-- Account info --}}
    <div class="border border-gray-200 dark:border-gray-700 p-6">
      <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4">Details</h3>
      <dl class="flex flex-col gap-y-3 text-sm">
        <div>
          <dt class="text-xs text-gray-400 uppercase">ID</dt>
          <dd class="font-mono text-gray-700 dark:text-gray-300">{{ $user->id }}</dd>
        </div>
        <div>
          <dt class="text-xs text-gray-400 uppercase">Email</dt>
          <dd class="text-gray-700 dark:text-gray-300 break-all">{{ $user->email }}</dd>
        </div>
        <div>
          <dt class="text-xs text-gray-400 uppercase">Email Verified</dt>
          <dd class="{{ $user->email_verified_at ? 'text-green-500' : 'text-orange-400' }} font-bold">
            {{ $user->email_verified_at ? $user->email_verified_at->format('M j, Y') : 'No' }}
          </dd>
        </div>
        <div>
          <dt class="text-xs text-gray-400 uppercase">Joined</dt>
          <dd class="text-gray-700 dark:text-gray-300">{{ $user->created_at->format('M j, Y') }}</dd>
        </div>
        @if ($user->social_provider)
        <div>
          <dt class="text-xs text-gray-400 uppercase">Auth Provider</dt>
          <dd class="text-gray-700 dark:text-gray-300 capitalize">{{ $user->social_provider }}</dd>
        </div>
        @endif
      </dl>
    </div>

  </div>

</div>
@endsection
