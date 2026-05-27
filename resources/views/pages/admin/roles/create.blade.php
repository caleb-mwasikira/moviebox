@extends('layouts.admin')

@section('admin-title', 'Create Role')

@section('admin-content')

<div class="mb-8">
  <a href="{{ route('admin.roles.index') }}"
     class="text-xs font-bold uppercase text-gray-400 hover:text-accent transition-colors mb-2 inline-block">
    ← Back to Roles
  </a>
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Create Role</h2>
</div>

@include('pages.admin.roles._form', [
    'action'          => route('admin.roles.store'),
    'method'          => 'POST',
    'role'            => null,
    'rolePermissions' => old('permissions', []),
])

@endsection
