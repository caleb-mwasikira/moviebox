@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'bag' => 'default', 'hint' => null, 'icon' => null])

@php
  // Error pages render without the shared $errors bag; fall back to an empty one.
  $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
  $id = $attributes->get('id', $name);
  $isPassword = $type === 'password';
@endphp
<div {{ $attributes->only('data-wrap') }} @if($isPassword) x-data="{ show: false }" @endif>
  @if ($label)
    <label class="label" for="{{ $id }}">{{ $label }}</label>
  @endif
  <div class="relative">
    <input
      @if($isPassword) :type="show ? 'text' : 'password'" @else type="{{ $type }}" @endif
      name="{{ $name }}" id="{{ $id }}"
      @if ($type !== 'password' && $type !== 'file' && $value !== null) value="{{ $value }}" @endif
      {{ $attributes->except(['id', 'data-wrap', 'class'])->merge([]) }}
      class="input @error($name, $bag) input-error @enderror {{ $isPassword ? 'pr-11' : '' }} {{ $attributes->get('class') }}">
    @if ($isPassword)
      <button type="button" @click="show = !show" tabindex="-1" aria-label="Toggle password visibility"
        class="absolute inset-y-0 right-0 grid w-11 place-items-center text-muted hover:text-fg">
        <x-ui.icon name="eye" class="size-[18px]" x-show="!show" />
        <x-ui.icon name="eye-off" class="size-[18px]" x-show="show" x-cloak />
      </button>
    @endif
  </div>
  @error($name, $bag)
    <p class="field-error">{{ $message }}</p>
  @enderror
  @if ($hint)
    <p class="hint">{{ $hint }}</p>
  @endif
</div>
