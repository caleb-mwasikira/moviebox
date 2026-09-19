@props(['name', 'label' => null, 'value' => null, 'rows' => 4, 'hint' => null])
@php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag; $id = $attributes->get('id', $name); @endphp
<div>
  @if ($label)<label class="label" for="{{ $id }}">{{ $label }}</label>@endif
  <textarea name="{{ $name }}" id="{{ $id }}" rows="{{ $rows }}" {{ $attributes->except(['id', 'class']) }}
    class="textarea @error($name) input-error @enderror {{ $attributes->get('class') }}">{{ $value }}</textarea>
  @error($name)<p class="field-error">{{ $message }}</p>@enderror
  @if ($hint)<p class="hint">{{ $hint }}</p>@endif
</div>
