@extends('layouts.admin')

@section('admin-title', 'Users')

@section('admin-content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
  <div>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Users</h2>
    <p class="mt-1 text-gray-500 dark:text-gray">{{ $users->total() }} {{ Str::plural('user', $users->total()) }} registered</p>
  </div>
</div>

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

{{-- Search --}}
<form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex gap-x-2">
  <input type="text" name="search" value="{{ request('search') }}"
    placeholder="Search by name or email…"
    class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-accent focus:outline-none flex-1 min-w-0">
  <button type="submit"
    class="px-4 py-2 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors shrink-0">
    Search
  </button>
  @if(request('search'))
    <a href="{{ route('admin.users.index') }}"
      class="px-4 py-2 text-xs font-bold uppercase border border-gray-300 dark:border-gray-600 text-gray-500 hover:text-accent transition-colors shrink-0">
      Clear
    </a>
  @endif
</form>

@if ($users->isEmpty())
  <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
    <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No users found</p>
  </div>
@else

  {{-- ── Mobile cards (< lg) ── --}}
  <div class="lg:hidden border border-gray-200 dark:border-gray-600 divide-y divide-gray-200 dark:divide-gray-600">
    @foreach ($users as $user)
      @php
        $latestSub = $user->userSubscriptions->first();
        $subActive = $latestSub && $latestSub->status === 'active'
            && ($latestSub->expires_at === null || $latestSub->expires_at->isFuture());
      @endphp
      <div class="p-4 {{ $user->isBanned() ? 'opacity-60' : '' }}">
        {{-- Name + badges --}}
        <div class="flex items-start justify-between gap-x-3">
          <div class="min-w-0">
            <div class="flex items-center gap-x-2 flex-wrap">
              <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $user->name }}</span>
              @if($user->is(auth()->user()))
                <span class="text-[10px] font-bold text-accent">(you)</span>
              @endif
              @if ($user->isAdmin())
                <span class="text-[10px] font-bold uppercase text-accent">Admin</span>
              @elseif ($user->roles->first())
                <span class="text-[10px] font-bold uppercase text-accent/70 capitalize">{{ str_replace('-', ' ', $user->roles->first()->name) }}</span>
              @else
                <span class="text-[10px] font-bold uppercase text-gray-400">User</span>
              @endif
            </div>
            <p class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</p>
          </div>
          <div class="shrink-0">
            @if($user->isBanned())
              <span class="inline-flex items-center gap-x-1 text-[10px] font-bold uppercase text-red-500">
                <span class="h-1.5 w-1.5 rounded-full bg-red-500 inline-block"></span>Banned
              </span>
            @else
              <span class="inline-flex items-center gap-x-1 text-[10px] font-bold uppercase text-green-500">
                <span class="h-1.5 w-1.5 rounded-full bg-green-500 inline-block"></span>Active
              </span>
            @endif
          </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
          @if ($latestSub && !$user->isAdmin())
            <details class="relative" style="list-style:none">
              <summary class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors cursor-pointer" style="list-style:none">
                Extend
              </summary>
              <div class="absolute left-0 top-6 z-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-lg p-4 w-52">
                <p class="text-xs font-bold uppercase text-gray-500 mb-2">Extend by days</p>
                <form method="POST" action="{{ route('admin.users.extend-subscription', $user) }}" class="flex gap-x-2">
                  @csrf @method('PATCH')
                  <input type="number" name="days" min="1" max="3650" value="30"
                    class="w-full px-2 py-1.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:border-accent focus:outline-none">
                  <button type="submit"
                    class="px-3 py-1.5 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors shrink-0">OK</button>
                </form>
              </div>
            </details>
          @endif
          @if(! $user->isAdmin())
            @can('assign permissions')
              <details class="relative" style="list-style:none">
                <summary class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors cursor-pointer" style="list-style:none">
                  Role
                </summary>
                <div class="absolute left-0 top-6 z-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-lg p-4 w-52">
                  <p class="text-xs font-bold uppercase text-gray-500 mb-2">Assign Role</p>
                  <form method="POST" action="{{ route('admin.users.assign-role', $user) }}" class="flex gap-x-2">
                    @csrf @method('PATCH')
                    <select name="role"
                      class="w-full px-2 py-1.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:border-accent focus:outline-none">
                      <option value="">No role</option>
                      @foreach ($roles as $r)
                        <option value="{{ $r->name }}" {{ $user->roles->first()?->name === $r->name ? 'selected' : '' }}>
                          {{ str_replace('-', ' ', $r->name) }}
                        </option>
                      @endforeach
                    </select>
                    <button type="submit"
                      class="px-3 py-1.5 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors shrink-0">OK</button>
                  </form>
                </div>
              </details>
              <a href="{{ route('admin.users.permissions', $user) }}"
                 class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">
                Permissions
              </a>
            @endcan
          @endif
          @if(! $user->is(auth()->user()) && ! $user->isAdmin())
            @if($user->isBanned())
              <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline">
                @csrf @method('PATCH')
                <button type="submit" class="text-xs font-bold uppercase text-accent hover:underline transition-colors">Unban</button>
              </form>
            @else
              <form method="POST" action="{{ route('admin.users.ban', $user) }}" class="inline"
                onsubmit="return confirm('Ban {{ addslashes($user->name) }}?')">
                @csrf @method('PATCH')
                <button type="submit" class="text-xs font-bold uppercase text-red-500 hover:underline transition-colors">Ban</button>
              </form>
            @endif
          @endif
        </div>
      </div>
    @endforeach
  </div>

  {{-- ── Desktop table (≥ lg) ── --}}
  <div class="hidden lg:block border border-gray-200 dark:border-gray-600 overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Name</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Role</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Subscription</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Status</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
        @foreach ($users as $user)
          @php
            $latestSub = $user->userSubscriptions->first();
            $subActive = $latestSub && $latestSub->status === 'active'
                && ($latestSub->expires_at === null || $latestSub->expires_at->isFuture());
          @endphp
          <tr class="{{ $user->isBanned() ? 'opacity-60' : '' }}">
            <td class="px-5 py-4 font-bold text-gray-900 dark:text-white whitespace-nowrap">
              {{ $user->name }}
              @if($user->is(auth()->user()))
                <span class="ml-1 text-xs font-bold text-accent">(you)</span>
              @endif
              <p class="text-xs font-normal text-gray-500 mt-0.5">{{ $user->email }}</p>
            </td>
            <td class="px-5 py-4">
              @if ($user->isAdmin())
                <span class="text-xs font-bold uppercase text-accent">Admin</span>
              @elseif ($user->roles->first())
                <span class="text-xs font-bold uppercase text-accent/70 capitalize">{{ str_replace('-', ' ', $user->roles->first()->name) }}</span>
              @else
                <span class="text-xs font-bold uppercase text-gray-400">User</span>
              @endif
            </td>
            <td class="px-5 py-4">
              @if ($latestSub)
                <div>
                  <span class="text-xs font-bold {{ $subActive ? 'text-accent' : 'text-red-500' }} uppercase">
                    {{ $subActive ? 'Active' : 'Expired' }}
                  </span>
                  @if ($latestSub->package)
                    <span class="text-xs text-gray-500 ml-1">· {{ $latestSub->package->name }}</span>
                  @endif
                  @if ($latestSub->expires_at)
                    <p class="text-xs text-gray-400 mt-0.5">
                      {{ $subActive ? 'Expires' : 'Expired' }} {{ $latestSub->expires_at->format('M j, Y') }}
                    </p>
                  @endif
                </div>
              @else
                <span class="text-xs text-gray-400 uppercase">None</span>
              @endif
            </td>
            <td class="px-5 py-4">
              @if($user->isBanned())
                <span class="inline-flex items-center gap-x-1.5 text-xs font-bold uppercase text-red-500">
                  <span class="h-1.5 w-1.5 rounded-full bg-red-500 inline-block"></span>Banned
                </span>
              @else
                <span class="inline-flex items-center gap-x-1.5 text-xs font-bold uppercase text-green-500">
                  <span class="h-1.5 w-1.5 rounded-full bg-green-500 inline-block"></span>Active
                </span>
              @endif
            </td>
            <td class="px-5 py-4 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-x-4">
                @if ($latestSub && !$user->isAdmin())
                  <details class="relative" style="list-style:none">
                    <summary class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors cursor-pointer" style="list-style:none">
                      Extend
                    </summary>
                    <div class="absolute right-0 top-6 z-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-lg p-4 w-52">
                      <p class="text-xs font-bold uppercase text-gray-500 mb-2">Extend by days</p>
                      <form method="POST" action="{{ route('admin.users.extend-subscription', $user) }}" class="flex gap-x-2">
                        @csrf @method('PATCH')
                        <input type="number" name="days" min="1" max="3650" value="30"
                          class="w-full px-2 py-1.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:border-accent focus:outline-none">
                        <button type="submit"
                          class="px-3 py-1.5 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors shrink-0">OK</button>
                      </form>
                    </div>
                  </details>
                @endif
                @if(! $user->isAdmin())
                  @can('assign permissions')
                    <details class="relative" style="list-style:none">
                      <summary class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors cursor-pointer" style="list-style:none">
                        Role
                      </summary>
                      <div class="absolute right-0 top-6 z-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-lg p-4 w-52">
                        <p class="text-xs font-bold uppercase text-gray-500 mb-2">Assign Role</p>
                        <form method="POST" action="{{ route('admin.users.assign-role', $user) }}" class="flex gap-x-2">
                          @csrf @method('PATCH')
                          <select name="role"
                            class="w-full px-2 py-1.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:border-accent focus:outline-none">
                            <option value="">No role</option>
                            @foreach ($roles as $r)
                              <option value="{{ $r->name }}" {{ $user->roles->first()?->name === $r->name ? 'selected' : '' }}>
                                {{ str_replace('-', ' ', $r->name) }}
                              </option>
                            @endforeach
                          </select>
                          <button type="submit"
                            class="px-3 py-1.5 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors shrink-0">OK</button>
                        </form>
                      </div>
                    </details>
                    <a href="{{ route('admin.users.permissions', $user) }}"
                       class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">
                      Permissions
                    </a>
                  @endcan
                @endif
                @if(! $user->is(auth()->user()) && ! $user->isAdmin())
                  @if($user->isBanned())
                    <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline">
                      @csrf @method('PATCH')
                      <button type="submit" class="text-xs font-bold uppercase text-accent hover:underline transition-colors">Unban</button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('admin.users.ban', $user) }}" class="inline"
                      onsubmit="return confirm('Ban {{ addslashes($user->name) }}?')">
                      @csrf @method('PATCH')
                      <button type="submit" class="text-xs font-bold uppercase text-red-500 hover:underline transition-colors">Ban</button>
                    </form>
                  @endif
                @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

@endif

@if($users->hasPages())
  <div class="mt-6">{{ $users->links() }}</div>
@endif
@endsection
