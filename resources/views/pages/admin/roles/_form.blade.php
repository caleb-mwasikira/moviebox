{{--
  Shared role form.
  Variables expected:
    $action       — form action URL
    $method       — 'POST' | 'PUT'
    $allPermissions — grouped permissions collection
    $role         — Role model (for edit) or null (for create)
    $rolePermissions — array of checked permission names (edit) or [] (create)
--}}
<form method="POST" action="{{ $action }}" class="grid gap-6 lg:grid-cols-3">
  @csrf
  @if ($method === 'PUT') @method('PUT') @endif

  {{-- Role name --}}
  <div class="lg:col-span-1">
    <div class="card card-pad space-y-3 lg:sticky lg:top-24">
      <h3 class="card-title">Role name</h3>
      <p class="text-sm text-muted">Use a short, descriptive slug. Hyphens are allowed (e.g. <code class="rounded bg-surface-3 px-1.5 py-0.5 text-xs">content-manager</code>).</p>
      <input type="text" name="name" value="{{ old('name', $role?->name) }}" placeholder="e.g. content-manager" class="input @error('name') input-error @enderror">
      @error('name')<p class="field-error">{{ $message }}</p>@enderror

      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary flex-1">{{ $role ? 'Save changes' : 'Create role' }}</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </div>
  </div>

  {{-- Permissions --}}
  <div class="lg:col-span-2">
    <div class="card card-pad space-y-6">
      <div><h3 class="card-title">Permissions</h3><p class="mt-0.5 text-sm text-muted">All users with this role will inherit these permissions.</p></div>

      @foreach ($allPermissions as $category => $perms)
        <div>
          <div class="mb-3 flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted">{{ $category }}</p>
            <label class="flex cursor-pointer items-center gap-2 text-xs font-semibold text-fg-soft">
              <input type="checkbox" data-group="{{ Str::slug($category) }}" class="check js-check-all"
                {{ count(array_intersect($perms->pluck('name')->toArray(), $rolePermissions)) === $perms->count() ? 'checked' : '' }}>
              All
            </label>
          </div>
          <div class="grid gap-2 sm:grid-cols-2">
            @foreach ($perms as $perm)
              <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line px-3.5 py-2.5 transition hover:bg-surface-2">
                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" data-group-item="{{ Str::slug($category) }}" class="check shrink-0 js-perm-check"
                  {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}>
                <span class="text-sm text-fg-soft">{{ $perm->name }}</span>
              </label>
            @endforeach
          </div>
        </div>
        @if (! $loop->last)<hr class="border-line">@endif
      @endforeach
    </div>
  </div>
</form>

<script>
document.querySelectorAll('.js-check-all').forEach(function (master) {
  var group = master.dataset.group;
  master.addEventListener('change', function () {
    document.querySelectorAll('[data-group-item="' + group + '"]').forEach(function (cb) {
      cb.checked = master.checked;
    });
  });
});
document.querySelectorAll('.js-perm-check').forEach(function (cb) {
  cb.addEventListener('change', function () {
    var group = cb.dataset.groupItem;
    var all   = document.querySelectorAll('[data-group-item="' + group + '"]');
    var checked = document.querySelectorAll('[data-group-item="' + group + '"]:checked');
    var master  = document.querySelector('[data-group="' + group + '"]');
    if (master) master.checked = all.length === checked.length;
  });
});
</script>
