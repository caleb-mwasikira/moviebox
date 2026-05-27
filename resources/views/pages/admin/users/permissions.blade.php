@extends('layouts.admin')

@section('admin-title', 'User Permissions — ' . $user->name)

@section('admin-content')

{{-- Header --}}
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
  <div>
    <a href="{{ route('admin.users.index') }}"
       class="text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors mb-2 inline-block">
      ← Back to Users
    </a>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">
      Permissions
    </h2>
    <p class="mt-1 text-gray-500 dark:text-gray-400">
      {{ $user->name }}
      <span class="text-gray-400 mx-1">·</span>
      <span class="text-gray-500">{{ $user->email }}</span>
    </p>
  </div>
</div>

@if (session('success'))
  <div class="mb-6 px-4 py-3 border border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-950 text-green-700 dark:text-green-300 text-sm font-medium">
    {{ session('success') }}
  </div>
@endif

<form method="POST" action="{{ route('admin.users.permissions.sync', $user) }}">
  @csrf @method('PUT')

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left column: Role selector --}}
    <div class="lg:col-span-1">
      <div class="border border-gray-200 dark:border-gray-600 p-6">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4">Role</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 leading-relaxed">
          Roles grant a preset bundle of permissions. You can also assign individual permissions below on top of a role.
        </p>

        <div class="space-y-2">
          <label class="flex items-center gap-x-3 cursor-pointer group">
            <input type="radio" name="role" value=""
              {{ ! $userRole ? 'checked' : '' }}
              class="accent-accent w-4 h-4 shrink-0">
            <div>
              <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">No role</span>
              <p class="text-xs text-gray-400 mt-0.5">Only direct permissions apply</p>
            </div>
          </label>

          @foreach ($roles as $role)
            <label class="flex items-center gap-x-3 cursor-pointer group">
              <input type="radio" name="role" value="{{ $role->name }}"
                {{ $userRole === $role->name ? 'checked' : '' }}
                class="accent-accent w-4 h-4 shrink-0">
              <div>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 capitalize">
                  {{ str_replace('-', ' ', $role->name) }}
                </span>
                <p class="text-xs text-gray-400 mt-0.5">
                  {{ $role->permissions->count() }} permission{{ $role->permissions->count() !== 1 ? 's' : '' }}
                </p>
              </div>
            </label>
          @endforeach
        </div>

        {{-- Role permission tooltips --}}
        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 space-y-3">
          @foreach ($roles as $role)
            <details class="group">
              <summary class="text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors cursor-pointer capitalize">
                {{ str_replace('-', ' ', $role->name) }} includes…
              </summary>
              <ul class="mt-2 ml-2 space-y-1">
                @foreach ($role->permissions->sortBy('name') as $rp)
                  <li class="text-xs text-gray-500 dark:text-gray-400">· {{ $rp->name }}</li>
                @endforeach
              </ul>
            </details>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Right column: Individual permissions grouped by category --}}
    <div class="lg:col-span-2 space-y-6">
      <div class="border border-gray-200 dark:border-gray-600 p-6">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1">
          Direct Permissions
        </h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-5 leading-relaxed">
          These are granted directly to the user, in addition to any permissions inherited from their role.
        </p>

        @foreach ($allPermissions as $category => $perms)
          <div class="mb-6 last:mb-0">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-3">
              {{ $category }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-6">
              @foreach ($perms as $perm)
                <label class="flex items-center gap-x-3 cursor-pointer">
                  <input type="checkbox"
                    name="permissions[]"
                    value="{{ $perm->name }}"
                    {{ in_array($perm->name, $userPermissions) ? 'checked' : '' }}
                    class="accent-accent w-4 h-4 shrink-0">
                  <span class="text-sm text-gray-700 dark:text-gray-300">{{ $perm->name }}</span>
                </label>
              @endforeach
            </div>
          </div>

          @if (! $loop->last)
            <hr class="border-gray-100 dark:border-gray-700 mb-6">
          @endif
        @endforeach
      </div>

      {{-- Submit --}}
      <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}"
           class="text-sm text-gray-500 hover:text-accent transition-colors">
          Cancel
        </a>
        <button type="submit"
          class="px-6 py-2.5 text-sm font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
          Save Permissions
        </button>
      </div>
    </div>

  </div>
</form>

@endsection
