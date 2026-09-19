@extends('layouts.admin')

@section('admin-title', 'Create Role')

@section('admin-content')
<x-ui.page-header title="Create role">
  <x-slot:actions><a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm"><x-ui.icon name="arrow-left" class="size-3.5" /> Back to roles</a></x-slot:actions>
</x-ui.page-header>

@include('pages.admin.roles._form', [
    'action'          => route('admin.roles.store'),
    'method'          => 'POST',
    'role'            => null,
    'rolePermissions' => old('permissions', []),
])
@endsection
