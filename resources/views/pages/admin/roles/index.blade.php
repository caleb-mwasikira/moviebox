@extends('layouts.admin')

@section('admin-title', 'Roles')

@section('admin-content')

<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
  <div>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Roles</h2>
    <p class="mt-1 text-gray-500 dark:text-gray-400">{{ $roles->count() }} {{ Str::plural('role', $roles->count()) }} defined</p>
  </div>
  <a href="{{ route('admin.roles.create') }}"
     class="px-5 py-2.5 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
    + New Role
  </a>
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

@if ($roles->isEmpty())
  <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
    <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No roles defined yet</p>
    <a href="{{ route('admin.roles.create') }}" class="mt-4 inline-block text-xs font-bold uppercase text-accent hover:underline">
      Create your first role →
    </a>
  </div>
@else

  <div class="border border-gray-200 dark:border-gray-600 overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Role</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Permissions</th>
          <th class="text-left px-5 py-3 font-bold uppercase text-xs text-gray-500 tracking-widest">Users</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
        @foreach ($roles as $role)
          <tr>
            <td class="px-5 py-4 font-bold text-gray-900 dark:text-white capitalize">
              {{ str_replace('-', ' ', $role->name) }}
              <p class="text-xs font-normal text-gray-400 font-mono mt-0.5">{{ $role->name }}</p>
            </td>
            <td class="px-5 py-4 text-gray-500">
              {{ $role->permissions_count }} {{ Str::plural('permission', $role->permissions_count) }}
            </td>
            <td class="px-5 py-4 text-gray-500">
              {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
            </td>
            <td class="px-5 py-4 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-x-4">
                <a href="{{ route('admin.roles.edit', $role) }}"
                   class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">
                  Edit
                </a>
                @if ($role->users_count === 0)
                  <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline"
                    onsubmit="return confirm('Delete role \'{{ addslashes(str_replace('-', ' ', $role->name)) }}\'?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-bold uppercase text-red-500 hover:underline transition-colors">
                      Delete
                    </button>
                  </form>
                @else
                  <span class="text-xs font-bold uppercase text-gray-300 dark:text-gray-600 cursor-not-allowed"
                    title="{{ $role->users_count }} user(s) assigned — unassign first">
                    Delete
                  </span>
                @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

@endif

@endsection
