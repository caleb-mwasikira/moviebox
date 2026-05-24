@extends('layouts.admin')

@section('admin-title', 'New Collection')

@section('admin-content')
<div class="mb-10">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">New Collection</h2>
  <p class="mt-1 text-gray-500 dark:text-gray text-sm">Create a themed playlist to surface on the home page.</p>
</div>

<form method="POST" action="{{ route('admin.collections.store') }}" enctype="multipart/form-data"
  class="flex flex-col gap-y-6 max-w-2xl">
  @csrf

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="name">Name</label>
    <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('name') border-danger @enderror"
      type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Date Night Picks">
    @error('name')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="description">Description</label>
    <textarea class="block px-4 py-2 border-base bg-white/40 font-medium tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full h-28"
      name="description" id="description" placeholder="Brief description of this collection…">{{ old('description') }}</textarea>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Thumbnail</label>
      <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
        type="file" name="thumbnail" id="col-thumbnail" accept="image/*">
      <div id="col-thumbnail-preview" class="mt-3"></div>
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="sort_order">Display Order</label>
      <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
        type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0">
      <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
    </div>
  </div>

  <div class="flex items-center gap-x-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
      class="w-4 h-4 accent-accent cursor-pointer">
    <label for="is_active" class="text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Active (visible on site)</label>
  </div>

  <div>
    <button type="submit"
      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
      Create Collection
    </button>
    <a href="{{ route('admin.collections.index') }}"
      class="ml-6 text-sm font-bold text-gray-500 hover:text-accent transition-colors uppercase">Cancel</a>
  </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
  var input = document.getElementById('col-thumbnail');
  var preview = document.getElementById('col-thumbnail-preview');
  if (!input || !preview) return;
  input.addEventListener('change', function () {
    preview.innerHTML = '';
    var file = input.files[0];
    if (!file || !file.type.startsWith('image/')) return;
    var reader = new FileReader();
    reader.onload = function (e) {
      var img = document.createElement('img');
      img.src = e.target.result;
      img.className = 'h-24 w-auto object-cover border border-gray-200 dark:border-gray-600';
      preview.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
})();
</script>
@endpush
