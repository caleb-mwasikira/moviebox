@props(['name', 'label' => null, 'hint' => null, 'required' => false, 'optional' => false, 'error' => null])
@php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag; $id = $attributes->get('id', $name); $err = $error ?? rtrim($name, '[]'); @endphp
<div>
  @if ($label)
    <label class="label" for="{{ $id }}">{{ $label }}
      @if ($required)<span class="text-red-500">*</span>@endif
      @if ($optional)<span class="font-normal normal-case tracking-normal text-muted/80">(optional)</span>@endif
    </label>
  @endif
  <input type="file" name="{{ $name }}" id="{{ $id }}" {{ $attributes->except(['id', 'class']) }} class="input {{ $attributes->get('class') }}">
  @if ($hint)<p class="hint">{{ $hint }}</p>@endif
  @error($err)<p class="field-error">{{ $message }}</p>@enderror
  @error($err . '.*')<p class="field-error">{{ $message }}</p>@enderror
</div>
