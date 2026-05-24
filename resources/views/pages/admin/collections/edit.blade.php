@extends('layouts.admin')

@section('admin-title', $collection->name)

@section('admin-content')
<div class="flex items-center justify-between mb-8">
  <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">{{ $collection->name }}</h2>
  <a href="{{ route('collections.show', $collection) }}" target="_blank"
    class="text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">View on site →</a>
</div>

{{-- ── Details form ── --}}
<form method="POST" action="{{ route('admin.collections.update', $collection) }}" enctype="multipart/form-data"
  class="flex flex-col gap-y-6 max-w-2xl mb-16">
  @csrf @method('PUT')

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="name">Name</label>
    <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('name') border-danger @enderror"
      type="text" name="name" id="name" value="{{ old('name', $collection->name) }}">
    @error('name')<p class="mt-1.5 text-xs text-danger">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="description">Description</label>
    <textarea class="block px-4 py-2 border-base bg-white/40 font-medium tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full h-28"
      name="description" id="description">{{ old('description', $collection->description) }}</textarea>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Thumbnail</label>
      @if ($collection->thumbnail)
        <img src="{{ $collection->thumbnailUrl() }}" class="h-24 w-auto object-cover border border-gray-200 dark:border-gray-600 mb-3" alt="">
      @endif
      <input class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:font-bold file:uppercase file:text-xs file:bg-accent file:text-gray-900 file:border-0 hover:file:bg-accent/90 file:cursor-pointer"
        type="file" name="thumbnail" id="col-thumbnail" accept="image/*">
      <div id="col-thumbnail-preview" class="mt-3"></div>
    </div>

    <div>
      <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="sort_order">Display Order</label>
      <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
        type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $collection->sort_order) }}" min="0">
      <p class="mt-1 text-xs text-gray-500">Lower numbers appear first</p>
    </div>
  </div>

  <div class="flex items-center gap-x-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $collection->is_active) ? 'checked' : '' }}
      class="w-4 h-4 accent-accent cursor-pointer">
    <label for="is_active" class="text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Active (visible on site)</label>
  </div>

  <div>
    <button type="submit"
      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
      Save Changes
    </button>
    <a href="{{ route('admin.collections.index') }}"
      class="ml-6 text-sm font-bold text-gray-500 hover:text-accent transition-colors uppercase">Back</a>
  </div>
</form>

{{-- ── Items management ── --}}
<div class="max-w-2xl">
  <h3 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white mb-6">Items in this collection</h3>

  {{-- Current items --}}
  @if ($collection->items->isEmpty())
    <p class="text-sm text-gray-500 mb-8">No items yet. Add movies or series below.</p>
  @else
    <div class="border border-gray-200 dark:border-gray-600 divide-y divide-gray-200 dark:divide-gray-600 mb-8">
      @foreach ($collection->items as $item)
        @php $content = $item->collectible; @endphp
        <div class="flex items-center gap-x-3 p-3">
          @if ($content && $content->thumbnail)
            <img src="{{ $content->thumbnailUrl() }}" class="w-10 h-12 object-cover shrink-0" alt="">
          @else
            <div class="w-10 h-12 bg-gray-100 dark:bg-gray-700 shrink-0 flex items-center justify-center">
              <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>
            </div>
          @endif
          <div class="flex-1 min-w-0">
            <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $content?->title ?? '(deleted)' }}</p>
            <p class="text-xs text-gray-500 uppercase tracking-wide">
              {{ $item->collectible_type === \App\Models\Movie::class ? 'Movie' : 'Series' }}
              @if ($content?->year) · {{ $content->year }}@endif
            </p>
          </div>
          <form method="POST" action="{{ route('admin.collections.remove-item', [$collection, $item]) }}"
            onsubmit="return confirm('Remove this item from the collection?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs font-bold uppercase text-gray-400 hover:text-red-500 transition-colors">Remove</button>
          </form>
        </div>
      @endforeach
    </div>
  @endif

  {{-- Add item form --}}
  <h4 class="text-sm font-bold uppercase text-gray-500 tracking-widest mb-4">Add content</h4>
  <form method="POST" action="{{ route('admin.collections.add-item', $collection) }}"
    class="flex flex-col sm:flex-row gap-3">
    @csrf

    <select name="type" id="col-type"
      class="border-base bg-white/40 px-4 h-12 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:bg-caret-white">
      <option value="movie">Movie</option>
      <option value="series">Series</option>
    </select>

    <select name="id" id="col-content-id"
      class="flex-1 border-base bg-white/40 px-4 h-12 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:bg-caret-white">
      <option value="">— Select movie —</option>
      @foreach ($movies as $movie)
        <option value="{{ $movie->id }}" data-type="movie">{{ $movie->title }}{{ $movie->year ? ' (' . $movie->year . ')' : '' }}</option>
      @endforeach
    </select>

    <button type="submit"
      class="px-6 h-12 font-bold uppercase text-xs text-gray-900 bg-accent hover:bg-accent/90 transition-colors shrink-0">
      Add
    </button>
  </form>
</div>

{{-- Danger zone --}}
<div class="max-w-2xl mt-16 pt-8 border-t border-gray-200 dark:border-gray-600">
  <h3 class="text-sm font-bold uppercase text-gray-500 tracking-widest mb-4">Danger Zone</h3>
  <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}"
    onsubmit="return confirm('Delete &quot;{{ addslashes($collection->name) }}&quot; and all its items? This cannot be undone.')">
    @csrf @method('DELETE')
    <button type="submit"
      class="px-6 py-3 font-bold uppercase text-xs text-white bg-red-600 hover:bg-red-700 transition-colors">
      Delete Collection
    </button>
  </form>
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
        img.className = 'h-24 w-auto object-cover border border-gray-200 dark:border-gray-600';
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
