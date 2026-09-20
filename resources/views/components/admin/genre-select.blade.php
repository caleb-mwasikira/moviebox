@props(['selected' => null, 'name' => 'genre', 'label' => 'Genre'])

@php
  $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
  $options = \App\Models\Genre::orderBy('name')->pluck('name')->all();
  // Keep a title's existing genre selectable even if it isn't in the list yet.
  if (filled($selected) && ! in_array($selected, $options, true)) {
    $options[] = $selected;
    sort($options, SORT_FLAG_CASE | SORT_STRING);
  }
@endphp

<div x-data="genreSelect(@js(route('admin.genres.store')))">
  <label class="label" for="{{ $name }}">{{ $label }}</label>
  <div class="flex gap-2">
    <select name="{{ $name }}" id="{{ $name }}" x-ref="select" class="select @error($name) input-error @enderror">
      <option value="">— Select genre —</option>
      @foreach ($options as $genre)
        <option value="{{ $genre }}" @selected($selected === $genre)>{{ $genre }}</option>
      @endforeach
    </select>
    <button type="button" @click="toggle()" class="btn btn-secondary btn-icon shrink-0 !size-[2.75rem]" :class="adding && '!border-brand !text-brand'" title="Add a new genre" aria-label="Add a new genre">
      <x-ui.icon name="plus" class="size-4" />
    </button>
  </div>

  <div x-show="adding" x-cloak x-transition class="mt-2 flex gap-2">
    <input type="text" x-ref="newInput" x-model="newName" maxlength="60" placeholder="New genre, e.g. Sci-Fi Thriller" class="input"
      @keydown.enter.prevent.stop="add()" @keydown.escape.stop="adding = false">
    <button type="button" @click="add()" :disabled="busy || !newName.trim()" class="btn btn-primary shrink-0">Add</button>
  </div>
  <p x-show="message" x-cloak x-text="message" class="hint" :class="isError && '!text-red-500 font-medium'"></p>
  @error($name)<p class="field-error">{{ $message }}</p>@enderror
</div>

@once
  @push('scripts')
  <script>
  function genreSelect(storeUrl) {
    return {
      adding: false, newName: '', busy: false, message: '', isError: false,
      toggle() { this.adding = !this.adding; this.message = ''; if (this.adding) this.$nextTick(() => this.$refs.newInput.focus()); },
      async add() {
        const name = this.newName.trim();
        if (!name || this.busy) return;
        this.busy = true; this.message = ''; this.isError = false;
        try {
          const r = await fetch(storeUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ name }),
          });
          const d = await r.json();
          if (!r.ok) { this.isError = true; this.message = (d.errors && d.errors.name && d.errors.name[0]) || d.message || 'Could not add the genre.'; return; }

          // Add the option (unless it already exists) and select it.
          const sel = this.$refs.select;
          let opt = [...sel.options].find(o => o.value.toLowerCase() === d.name.toLowerCase());
          if (!opt) {
            opt = new Option(d.name, d.name);
            const after = [...sel.options].slice(1).find(o => o.value.toLowerCase() > d.name.toLowerCase());
            sel.add(opt, after || null);
          }
          sel.value = opt.value;
          this.message = d.created ? '“' + d.name + '” added and selected.' : '“' + d.name + '” already exists — selected.';
          this.newName = ''; this.adding = false;
        } catch (e) {
          this.isError = true; this.message = 'Could not reach the server.';
        } finally { this.busy = false; }
      },
    };
  }
  </script>
  @endpush
@endonce
