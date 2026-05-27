@extends('layouts.admin')

@section('admin-title', 'Edit Role — ' . str_replace('-', ' ', $role->name))

@section('admin-content')

<div class="mb-8">
  <a href="{{ route('admin.roles.index') }}"
     class="text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors mb-2 inline-block">
    ← Back to Roles
  </a>
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl capitalize">
    Edit Role — {{ str_replace('-', ' ', $role->name) }}
  </h2>
</div>

@if (session('success'))
  <div class="mb-6 px-4 py-3 border border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-950 text-green-700 dark:text-green-300 text-sm font-medium">
    {{ session('success') }}
  </div>
@endif

@include('pages.admin.roles._form', [
    'action'          => route('admin.roles.update', $role),
    'method'          => 'PUT',
    'role'            => $role,
    'rolePermissions' => old('permissions', $rolePermissions),
])

@endsection
