@extends('layouts.admin')

@section('admin-title', 'New Collection')

@section('admin-content')
<x-ui.page-header title="New collection" subtitle="Create a themed playlist to surface on the home page." />

<form method="POST" action="{{ route('admin.collections.store') }}" enctype="multipart/form-data" class="card card-pad max-w-2xl space-y-5">
  @csrf
  <x-ui.field name="name" label="Name" :value="old('name')" placeholder="e.g. Date Night Picks" />
  <x-ui.textarea name="description" label="Description" :value="old('description')" placeholder="Brief description of this collection…" />
  <div class="grid gap-5 sm:grid-cols-2">
    <div>
      <x-ui.file name="thumbnail" id="col-thumbnail" label="Thumbnail" accept="image/*" />
      <div id="col-thumbnail-preview" class="mt-3"></div>
    </div>
    <x-ui.field name="sort_order" type="number" label="Display order" :value="old('sort_order', 0)" min="0" hint="Lower numbers appear first" />
  </div>
  <div>
    <input type="hidden" name="is_active" value="0">
    <x-ui.check name="is_active" label="Active (visible on site)" :checked="old('is_active', true)" />
  </div>
  <div class="flex gap-3">
    <button type="submit" class="btn btn-primary">Create collection</button>
    <a href="{{ route('admin.collections.index') }}" class="btn btn-secondary">Cancel</a>
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
      img.className = 'h-24 w-auto rounded-lg border border-line object-cover';
      preview.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
})();
</script>
@endpush
