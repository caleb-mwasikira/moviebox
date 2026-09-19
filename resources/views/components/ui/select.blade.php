@props(['name', 'label' => null, 'options' => [], 'selected' => null, 'placeholder' => null, 'hint' => null])
@php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag; $id = $attributes->get('id', $name); @endphp
<div>
  @if ($label)<label class="label" for="{{ $id }}">{{ $label }}</label>@endif
  <select name="{{ $name }}" id="{{ $id }}" {{ $attributes->except(['id', 'class']) }} class="select @error($name) input-error @enderror {{ $attributes->get('class') }}">
    @if ($placeholder !== null)<option value="">{{ $placeholder }}</option>@endif
    @foreach ($options as $value => $text)
      @php $value = is_int($value) && array_is_list($options) ? $text : $value; @endphp
      <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $text }}</option>
    @endforeach
    {{ $slot }}
  </select>
  @error($name)<p class="field-error">{{ $message }}</p>@enderror
  @if ($hint)<p class="hint">{{ $hint }}</p>@endif
</div>
