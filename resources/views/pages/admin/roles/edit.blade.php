@extends('layouts.admin')

@section('admin-title', 'Edit Role — ' . str_replace('-', ' ', $role->name))

@section('admin-content')
<x-ui.page-header :title="'Edit role — ' . str_replace('-', ' ', $role->name)">
  <x-slot:actions><a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm"><x-ui.icon name="arrow-left" class="size-3.5" /> Back to roles</a></x-slot:actions>
</x-ui.page-header>

@if (session('success'))<div class="alert alert-success"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />{{ session('success') }}</div>@endif

@include('pages.admin.roles._form', [
    'action'          => route('admin.roles.update', $role),
    'method'          => 'PUT',
    'role'            => $role,
    'rolePermissions' => old('permissions', $rolePermissions),
])
@endsection
