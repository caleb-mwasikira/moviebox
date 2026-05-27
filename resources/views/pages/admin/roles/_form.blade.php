{{--
  Shared role form.
  Variables expected:
    $action       — form action URL
    $method       — 'POST' | 'PUT'
    $allPermissions — grouped permissions collection
    $role         — Role model (for edit) or null (for create)
    $rolePermissions — array of checked permission names (edit) or [] (create)
--}}

<form method="POST" action="{{ $action }}">
  @csrf
  @if ($method === 'PUT') @method('PUT') @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Role name --}}
    <div class="lg:col-span-1">
      <div class="border border-gray-200 dark:border-gray-600 p-6">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4">Role Name</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 leading-relaxed">
          Use a short, descriptive slug. Hyphens are allowed (e.g. <code class="font-mono">content-manager</code>).
        </p>
        <input type="text" name="name"
          value="{{ old('name', $role?->name) }}"
          placeholder="e.g. content-manager"
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-accent focus:outline-none @error('name') border-red-500 @enderror">
        @error('name')
          <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
      </div>
    </div>

    {{-- Right: Permissions --}}
    <div class="lg:col-span-2">
      <div class="border border-gray-200 dark:border-gray-600 p-6">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-1">Permissions</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-5 leading-relaxed">
          All users with this role will inherit these permissions.
        </p>

        @foreach ($allPermissions as $category => $perms)
          <div class="mb-6 last:mb-0">
            <div class="flex items-center justify-between mb-3">
              <p class="text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">{{ $category }}</p>
              <label class="flex items-center gap-x-1.5 cursor-pointer text-[10px] font-bold uppercase text-gray-400 hover:text-accent transition-colors select-none">
                <input type="checkbox"
                  class="accent-accent w-3.5 h-3.5 js-check-all"
                  data-group="{{ Str::slug($category) }}"
                  {{ count(array_intersect($perms->pluck('name')->toArray(), $rolePermissions)) === $perms->count() ? 'checked' : '' }}>
                All
              </label>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-6">
              @foreach ($perms as $perm)
                <label class="flex items-center gap-x-3 cursor-pointer">
                  <input type="checkbox"
                    name="permissions[]"
                    value="{{ $perm->name }}"
                    data-group-item="{{ Str::slug($category) }}"
                    {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}
                    class="accent-accent w-4 h-4 shrink-0 js-perm-check">
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

      {{-- Actions --}}
      <div class="flex items-center justify-between mt-6">
        <a href="{{ route('admin.roles.index') }}"
           class="text-sm text-gray-500 hover:text-accent transition-colors">
          Cancel
        </a>
        <button type="submit"
          class="px-6 py-2.5 text-sm font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
          {{ $role ? 'Save Changes' : 'Create Role' }}
        </button>
      </div>
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
