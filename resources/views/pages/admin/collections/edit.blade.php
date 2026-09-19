@extends('layouts.admin')

@section('admin-title', $collection->name)

@section('admin-content')
<x-ui.page-header :title="$collection->name">
  <x-slot:actions>
    <a href="{{ route('collections.show', $collection) }}" target="_blank" class="btn btn-secondary btn-sm"><x-ui.icon name="external" class="size-3.5" /> View on site</a>
    <a href="{{ route('admin.collections.index') }}" class="btn btn-secondary btn-sm"><x-ui.icon name="arrow-left" class="size-3.5" /> Back</a>
  </x-slot:actions>
</x-ui.page-header>

<form method="POST" action="{{ route('admin.collections.update', $collection) }}" enctype="multipart/form-data" class="card">
  @csrf @method('PUT')
  <div class="card-header"><h2 class="card-title">Details</h2></div>
  <div class="max-w-2xl space-y-5 p-5 sm:p-6">
    <x-ui.field name="name" label="Name" :value="old('name', $collection->name)" />
    <x-ui.textarea name="description" label="Description" :value="old('description', $collection->description)" />
    <div class="grid gap-5 sm:grid-cols-2">
      <div>
        <label class="label">Thumbnail</label>
        @if ($collection->thumbnail)<img src="{{ $collection->thumbnailUrl() }}" alt="" class="mb-3 h-24 w-auto rounded-lg border border-line object-cover">@endif
        <x-ui.file name="thumbnail" id="col-thumbnail" accept="image/*" />
        <div id="col-thumbnail-preview" class="mt-3"></div>
      </div>
      <x-ui.field name="sort_order" type="number" label="Display order" :value="old('sort_order', $collection->sort_order)" min="0" hint="Lower numbers appear first" />
    </div>
    <div>
      <input type="hidden" name="is_active" value="0">
      <x-ui.check name="is_active" label="Active (visible on site)" :checked="old('is_active', $collection->is_active)" />
    </div>
    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Save changes</button>
      <a href="{{ route('admin.collections.index') }}" class="btn btn-secondary">Back</a>
    </div>
  </div>
</form>

{{-- Items --}}
<div class="card">
  <div class="card-header"><h2 class="card-title">Items in this collection</h2><span class="badge badge-neutral">{{ $collection->items->count() }}</span></div>
  <div class="space-y-6 p-5 sm:p-6">
    @if ($collection->items->isEmpty())
      <p class="text-sm text-muted">No items yet. Add movies or series below.</p>
    @else
      <ul class="divide-y divide-line rounded-xl border border-line">
        @foreach ($collection->items as $item)
          @php $content = $item->collectible; @endphp
          <li class="flex items-center gap-4 px-4 py-3">
            <x-admin.thumb :src="$content && $content->thumbnail ? $content->thumbnailUrl() : null" />
            <div class="min-w-0 flex-1">
              <p class="truncate text-sm font-semibold text-fg">{{ $content?->title ?? '(deleted)' }}</p>
              <p class="text-xs text-muted">{{ $item->collectible_type === \App\Models\Movie::class ? 'Movie' : 'Series' }}@if ($content?->year) · {{ $content->year }}@endif</p>
            </div>
            <x-admin.delete :action="route('admin.collections.remove-item', [$collection, $item])" confirm="Remove this item from the collection?" label="Remove" />
          </li>
        @endforeach
      </ul>
    @endif

    <div>
      <h3 class="mb-3 text-sm font-semibold text-fg">Add content</h3>
      <form method="POST" action="{{ route('admin.collections.add-item', $collection) }}" class="flex flex-col gap-3 sm:flex-row">
        @csrf
        <select name="type" id="col-type" class="select sm:!w-40"><option value="movie">Movie</option><option value="series">Series</option></select>
        <select name="id" id="col-content-id" class="select flex-1">
          <option value="">— Select movie —</option>
          @foreach ($movies as $movie)
            <option value="{{ $movie->id }}" data-type="movie">{{ $movie->title }}{{ $movie->year ? ' (' . $movie->year . ')' : '' }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Add</button>
      </form>
    </div>
  </div>
</div>

{{-- Danger zone --}}
<div class="card card-pad border-red-500/25">
  <h3 class="text-sm font-semibold text-red-500">Danger zone</h3>
  <p class="mb-4 mt-1 text-sm text-muted">Deleting this collection also removes all of its items. This cannot be undone.</p>
  <x-admin.delete :action="route('admin.collections.destroy', $collection)" :confirm="'Delete “' . $collection->name . '” and all its items? This cannot be undone.'" label="Delete collection" class="btn btn-danger" />
</div>
@endsection

@push('scripts')
<script>
(function () {
  // Thumbnail preview
  var thumbInput = document.getElementById('col-thumbnail');
  var thumbPreview = document.getElementById('col-thumbnail-preview');
  if (thumbInput && thumbPreview) {
    thumbInput.addEventListener('change', function () {
      thumbPreview.innerHTML = '';
      var file = thumbInput.files[0];
      if (!file || !file.type.startsWith('image/')) return;
      var reader = new FileReader();
      reader.onload = function (e) {
        var img = document.createElement('img');
        img.src = e.target.result;
        img.className = 'h-24 w-auto rounded-lg border border-line object-cover';
        thumbPreview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  }

  // Swap content dropdown when type changes
  var typeSelect    = document.getElementById('col-type');
  var contentSelect = document.getElementById('col-content-id');
  var movies = @json($movies->map(fn($m) => ['id' => $m->id, 'label' => $m->title . ($m->year ? ' (' . $m->year . ')' : '')]));
  var series = @json($series->map(fn($s) => ['id' => $s->id, 'label' => $s->title . ($s->year ? ' (' . $s->year . ')' : '')]));

  function populateContent(type) {
    var items = type === 'movie' ? movies : series;
    var placeholder = type === 'movie' ? '— Select movie —' : '— Select series —';
    contentSelect.innerHTML = '<option value="">' + placeholder + '</option>';
    items.forEach(function (item) {
      var opt = document.createElement('option');
      opt.value = item.id;
      opt.textContent = item.label;
      contentSelect.appendChild(opt);
    });
  }

  if (typeSelect) {
    typeSelect.addEventListener('change', function () {
      populateContent(this.value);
    });
  }
})();
</script>
@endpush
