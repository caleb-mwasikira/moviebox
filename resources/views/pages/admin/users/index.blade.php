@extends('layouts.admin')

@section('admin-title', 'Users')

@section('admin-content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
  <div>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Users</h2>
    <p class="mt-1 text-gray-500 dark:text-gray">{{ $users->total() }} {{ Str::plural('user', $users->total()) }} registered</p>
  </div>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex gap-x-3">
  <input type="text" name="search" value="{{ request('search') }}"
    placeholder="Search by name or email…"
    class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-accent focus:outline-none flex-1 max-w-sm">
  <button type="submit"
    class="px-5 py-2 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
    Search
  </button>
  @if(request('search'))
  <a href="{{ route('admin.users.index') }}"
    class="px-5 py-2 text-xs font-bold uppercase border border-gray-300 dark:border-gray-600 text-gray-500 hover:text-accent transition-colors">
    Clear
  </a>
  @endif
</form>

{{-- Table --}}
<div class="border border-gray-200 dark:border-gray-600 overflow-x-auto">
  <table class="w-full text-sm">
    <thead>
      <tr class="border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
        <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Name</th>
        <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Email</th>
        <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Role</th>
        <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Joined</th>
        <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Status</th>
        <th class="px-5 py-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
      @forelse($users as $user)
      <tr class="{{ $user->isBanned() ? 'opacity-60' : '' }}">
        <td class="px-5 py-4 font-bold text-gray-900 dark:text-white whitespace-nowrap">
          {{ $user->name }}
          @if($user->is(auth()->user()))
            <span class="ml-1 text-xs font-bold text-accent">(you)</span>
          @endif
        </td>
        <td class="px-5 py-4 text-gray-500">{{ $user->email }}</td>
        <td class="px-5 py-4">
          <span class="text-xs font-bold uppercase {{ $user->isAdmin() ? 'text-accent' : 'text-gray-400' }}">
            {{ $user->isAdmin() ? 'Admin' : 'User' }}
          </span>
        </td>
        <td class="px-5 py-4 text-gray-500 whitespace-nowrap">{{ $user->created_at->format('M j, Y') }}</td>
        <td class="px-5 py-4">
          @if($user->isBanned())
            <span class="inline-flex items-center gap-x-1.5 text-xs font-bold uppercase text-red-500">
              <span class="h-1.5 w-1.5 rounded-full bg-red-500 inline-block"></span>
              Banned
            </span>
          @else
            <span class="inline-flex items-center gap-x-1.5 text-xs font-bold uppercase text-green-500">
              <span class="h-1.5 w-1.5 rounded-full bg-green-500 inline-block"></span>
              Active
            </span>
          @endif
        </td>
        <td class="px-5 py-4 text-right whitespace-nowrap">
          @if(! $user->is(auth()->user()) && ! $user->isAdmin())
            @if($user->isBanned())
              <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline">
                @csrf @method('PATCH')
                <button type="submit"
                  class="text-xs font-bold uppercase text-accent hover:underline transition-colors">
                  Unban
                </button>
              </form>
            @else
              <form method="POST" action="{{ route('admin.users.ban', $user) }}" class="inline"
                onsubmit="return confirm('Ban {{ addslashes($user->name) }}?')">
                @csrf @method('PATCH')
                <button type="submit"
                  class="text-xs font-bold uppercase text-red-500 hover:underline transition-colors">
                  Ban
                </button>
              </form>
            @endif
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="px-5 py-10 text-center text-gray-500 text-sm font-bold uppercase tracking-widest">
          No users found.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($users->hasPages())
<div class="mt-6">
  {{ $users->links() }}
</div>
@endif
@endsection
