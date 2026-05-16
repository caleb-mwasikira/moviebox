<div>
  <label class="block text-xs font-bold uppercase text-gray-500 tracking-widest mb-1.5">Name</label>
  <input type="text" name="name" value="{{ old('name') }}" required maxlength="50"
    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:border-accent">
</div>

<div>
  <label class="block text-xs font-bold uppercase text-gray-500 tracking-widest mb-1.5">Colour</label>
  <div class="flex flex-wrap gap-2" x-data="{ color: '{{ Profile::COLORS[0] }}' }">
    @foreach ($colors as $i => $color)
      <label class="cursor-pointer">
        <input type="radio" name="avatar_color" value="{{ $color }}" class="sr-only"
          x-bind:checked="color === '{{ $color }}'" {{ $i === 0 ? 'checked' : '' }}>
        <div class="w-8 h-8 rounded transition-all"
          style="background: {{ $color }};"
          x-bind:style="color === '{{ $color }}' ? 'background: {{ $color }}; outline: 3px solid white; outline-offset: 2px; box-shadow: 0 0 0 4px {{ $color }}' : 'background: {{ $color }}'"
          @click="color = '{{ $color }}'">
        </div>
      </label>
    @endforeach
  </div>
</div>

<label class="flex items-center gap-x-2 cursor-pointer">
  <input type="checkbox" name="is_kids" value="1" class="w-4 h-4 accent-yellow-400">
  <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Kids profile</span>
</label>

<div>
  <label class="block text-xs font-bold uppercase text-gray-500 tracking-widest mb-1.5">PIN (optional, 4 digits)</label>
  <input type="text" name="pin" maxlength="4" inputmode="numeric" pattern="\d{4}" placeholder="Leave blank for no PIN"
    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:border-accent">
</div>
