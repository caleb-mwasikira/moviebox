@extends('layouts.admin')

@section('admin-title', 'User Permissions — ' . $user->name)

@section('admin-content')
<x-ui.page-header title="Permissions" :subtitle="$user->name . ' · ' . $user->email">
  <x-slot:actions><a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm"><x-ui.icon name="arrow-left" class="size-3.5" /> Back to users</a></x-slot:actions>
</x-ui.page-header>

@if (session('success'))<div class="alert alert-success"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />{{ session('success') }}</div>@endif

<form method="POST" action="{{ route('admin.users.permissions.sync', $user) }}" class="grid gap-6 lg:grid-cols-3">
  @csrf @method('PUT')

  {{-- Role --}}
  <div class="lg:col-span-1">
    <div class="card card-pad space-y-4 lg:sticky lg:top-24">
      <div><h3 class="card-title">Role</h3><p class="mt-0.5 text-sm text-muted">Roles grant a preset bundle of permissions. You can also assign individual permissions on top of a role.</p></div>

      <div class="space-y-2">
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line p-3.5 transition hover:bg-surface-2 has-[:checked]:border-brand has-[:checked]:bg-brand-soft">
          <input type="radio" name="role" value="" class="mt-1 accent-[var(--c-brand)]" {{ ! $userRole ? 'checked' : '' }}>
          <div><span class="text-sm font-semibold text-fg">No role</span><p class="text-xs text-muted">Only direct permissions apply</p></div>
        </label>
        @foreach ($roles as $role)
          <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line p-3.5 transition hover:bg-surface-2 has-[:checked]:border-brand has-[:checked]:bg-brand-soft">
            <input type="radio" name="role" value="{{ $role->name }}" class="mt-1 accent-[var(--c-brand)]" {{ $userRole === $role->name ? 'checked' : '' }}>
            <div><span class="text-sm font-semibold capitalize text-fg">{{ str_replace('-', ' ', $role->name) }}</span><p class="text-xs text-muted">{{ $role->permissions->count() }} permission{{ $role->permissions->count() !== 1 ? 's' : '' }}</p></div>
          </label>
        @endforeach
      </div>

      <div class="space-y-1">
        @foreach ($roles as $role)
          <details class="rounded-lg text-sm">
            <summary class="cursor-pointer rounded-lg px-2 py-1.5 text-xs font-semibold capitalize text-brand hover:bg-surface-2">{{ str_replace('-', ' ', $role->name) }} includes…</summary>
            <ul class="mt-1 space-y-0.5 px-3 pb-2 text-xs text-muted">@foreach ($role->permissions->sortBy('name') as $rp)<li>· {{ $rp->name }}</li>@endforeach</ul>
          </details>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Direct permissions --}}
  <div class="lg:col-span-2">
    <div class="card card-pad space-y-6">
      <div><h3 class="card-title">Direct permissions</h3><p class="mt-0.5 text-sm text-muted">These are granted directly to the user, in addition to any permissions inherited from their role.</p></div>

      @foreach ($allPermissions as $category => $perms)
        <div>
          <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted">{{ $category }}</p>
          <div class="grid gap-2 sm:grid-cols-2">
            @foreach ($perms as $perm)
              <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line px-3.5 py-2.5 transition hover:bg-surface-2">
                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="check shrink-0" {{ in_array($perm->name, $userPermissions) ? 'checked' : '' }}>
                <span class="text-sm text-fg-soft">{{ $perm->name }}</span>
              </label>
            @endforeach
          </div>
        </div>
        @if (! $loop->last)<hr class="border-line">@endif
      @endforeach

      <div class="flex justify-end gap-2 border-t border-line pt-5">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Save permissions</button>
      </div>
    </div>
  </div>
</form>
@endsection
