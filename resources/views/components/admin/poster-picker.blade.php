@props(['posters' => collect()])
@php
  $existing = $posters->sortByDesc('is_main')->values()->map(fn ($p) => ['id' => $p->id, 'url' => $p->url(), 'main' => (bool) $p->is_main])->all();
@endphp
{{-- Artwork: posters[] takes several images and one of them is marked as the main poster. --}}
<div x-data='posterPicker(@json($existing))' class="space-y-3">
  <label class="label">Posters <span class="font-normal normal-case tracking-normal text-muted/80">— choose which one is the main poster</span></label>

  <label @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="dragging = false; add($event.dataTransfer.files)"
    :class="dragging ? 'border-brand bg-brand-soft' : 'border-line-strong hover:border-brand/60'"
    class="flex cursor-pointer flex-col items-center justify-center gap-1.5 rounded-2xl border-2 border-dashed px-4 py-6 text-center transition">
    <x-ui.icon name="upload" class="size-6 text-muted" />
    <span class="text-sm font-semibold text-fg">Click to browse<span class="font-normal text-muted"> or drag &amp; drop images</span></span>
    <span class="text-xs text-muted">Add as many as you like</span>
    <input x-ref="input" type="file" name="posters[]" accept="image/*" multiple class="sr-only" @change="pick($event)">
  </label>

  <input type="hidden" name="main_poster" :value="mainKey">
  <template x-for="id in removed" :key="id"><input type="hidden" name="remove_posters[]" :value="id"></template>

  <div x-show="items.length" x-cloak class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
    <template x-for="item in items" :key="item.url">
      <div class="group relative overflow-hidden rounded-xl border-2 bg-surface-2 transition" :class="item === mainItem ? 'border-brand' : 'border-line'">
        <img :src="item.url" alt="" class="aspect-[2/3] w-full object-cover">
        <button type="button" @click="remove(item)" class="absolute right-1.5 top-1.5 grid size-6 place-items-center rounded-full bg-black/70 text-xs text-white transition hover:bg-red-500" aria-label="Remove poster">✕</button>
        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-2 pt-6">
          <span x-show="item === mainItem" class="badge badge-brand w-full justify-center">★ Main poster</span>
          <button type="button" x-show="item !== mainItem" @click="mainItem = item" class="w-full rounded-lg bg-white/15 px-2 py-1 text-xs font-semibold text-white backdrop-blur transition hover:bg-white/30">Set as main</button>
        </div>
      </div>
    </template>
  </div>
  <p x-show="!items.length" class="text-xs text-muted">No posters yet — the first image you add becomes the main poster.</p>
</div>
