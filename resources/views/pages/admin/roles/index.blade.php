@extends('layouts.admin')

@section('admin-title', 'Roles')

@section('admin-content')
<x-ui.page-header title="Roles" :subtitle="$roles->count() . ' ' . Str::plural('role', $roles->count()) . ' defined'">
  <x-slot:actions><a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><x-ui.icon name="plus" class="size-4" /> New role</a></x-slot:actions>
</x-ui.page-header>

@if (session('success'))<div class="alert alert-success"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />{{ session('success') }}</div>@endif
@if (session('error'))<div class="alert alert-error"><x-ui.icon name="alert" class="mt-0.5 size-5 shrink-0" />{{ session('error') }}</div>@endif

@if ($roles->isEmpty())
  <x-ui.empty icon="shield" title="No roles defined yet"><a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Create your first role</a></x-ui.empty>
@else
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Role</th><th>Permissions</th><th>Users</th><th class="text-right">Actions</th></tr></thead>
      <tbody>
        @foreach ($roles as $role)
          <tr>
            <td><p class="font-semibold capitalize text-fg">{{ str_replace('-', ' ', $role->name) }}</p><p class="font-mono text-xs text-muted">{{ $role->name }}</p></td>
            <td>{{ $role->permissions_count }} {{ Str::plural('permission', $role->permissions_count) }}</td>
            <td>{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}</td>
            <td>
              <div class="flex items-center justify-end gap-1.5">
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-secondary btn-sm"><x-ui.icon name="edit" class="size-3.5" /> Edit</a>
                @if ($role->users_count === 0)
                  <x-admin.delete :action="route('admin.roles.destroy', $role)" :confirm="'Delete role ‘' . str_replace('-', ' ', $role->name) . '’?'" />
                @else
                  <span class="btn btn-sm cursor-not-allowed opacity-40" title="{{ $role->users_count }} user(s) assigned — unassign first">Delete</span>
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
