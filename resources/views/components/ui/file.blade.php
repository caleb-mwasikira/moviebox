@props(['name', 'label' => null, 'hint' => null, 'required' => false, 'optional' => false, 'error' => null])
@php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag; $id = $attributes->get('id', $name); $err = $error ?? rtrim($name, '[]'); @endphp
<div>
  @if ($label)
    <label class="label" for="{{ $id }}">{{ $label }}
      @if ($required)<span class="text-red-500">*</span>@endif
      @if ($optional)<span class="font-normal normal-case tracking-normal text-muted/80">(optional)</span>@endif
    </label>
  @endif
  @php $isImage = str_starts_with((string) $attributes->get('accept'), 'image'); $multiple = $attributes->has('multiple'); @endphp
  <label for="{{ $id }}"
    x-data="{
      dragging: false, names: [], previews: [],
      accepts(f) {
        const a = (this.$refs.input.accept || '').split(',').map(x => x.trim().toLowerCase()).filter(Boolean);
        if (!a.length) return true;
        return a.some(x => x.startsWith('.') ? f.name.toLowerCase().endsWith(x) : x.endsWith('/*') ? f.type.startsWith(x.slice(0, -1)) : f.type === x);
      },
      set(files) {
        files = [...files].filter(f => this.accepts(f));
        if (!files.length) return;
        if (!this.$refs.input.multiple) files = files.slice(0, 1);
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        this.$refs.input.files = dt.files;
        this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
      },
      sync() {
        this.previews.forEach(u => URL.revokeObjectURL(u));
        const files = [...this.$refs.input.files];
        this.names = files.map(f => f.name);
        this.previews = files.filter(f => f.type.startsWith('image/')).slice(0, 6).map(f => URL.createObjectURL(f));
      },
      clear() { this.$refs.input.value = ''; this.sync(); }
    }"
    @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="dragging = false; set($event.dataTransfer.files)"
    :class="dragging ? 'border-brand bg-brand-soft' : 'border-line-strong hover:border-brand/60'"
    class="flex cursor-pointer flex-col items-center justify-center gap-1.5 rounded-2xl border-2 border-dashed px-4 py-6 text-center transition">
    <template x-if="previews.length">
      <div class="mb-1 flex flex-wrap justify-center gap-2">
        <template x-for="u in previews" :key="u"><img :src="u" alt="" class="h-16 w-12 rounded-md object-cover ring-1 ring-line"></template>
      </div>
    </template>
    <x-ui.icon name="upload" class="size-6 text-muted" />
    <span class="text-sm font-semibold text-fg">Click to browse<span class="font-normal text-muted"> or drag &amp; drop {{ $multiple ? 'files' : 'a file' }}</span></span>
    <span class="max-w-full truncate text-xs text-muted" x-text="names.length ? (names.length > 1 ? names.length + ' files selected' : names[0]) : '{{ $isImage ? 'Images' : 'Video' }}'">{{ $isImage ? 'Images' : 'Video' }}</span>
    <button type="button" x-show="names.length" x-cloak @click.prevent.stop="clear()" class="text-xs font-semibold text-brand hover:underline">Clear</button>
    <input x-ref="input" type="file" name="{{ $name }}" id="{{ $id }}" {{ $attributes->except(['id', 'class']) }} class="sr-only" @change="sync()">
  </label>
  @if ($hint)<p class="hint">{{ $hint }}</p>@endif
  @error($err)<p class="field-error">{{ $message }}</p>@enderror
  @error($err . '.*')<p class="field-error">{{ $message }}</p>@enderror
</div>
