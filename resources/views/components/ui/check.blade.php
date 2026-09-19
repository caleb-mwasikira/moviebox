@props(['name', 'label', 'hint' => null, 'checked' => false, 'value' => '1'])
@php $id = $attributes->get('id', $name); @endphp
<label for="{{ $id }}" class="flex cursor-pointer items-start gap-3">
  <input type="checkbox" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}" @checked($checked) {{ $attributes->except(['id']) }} class="check mt-0.5">
  <span class="text-sm font-medium text-fg">{{ $label }} @if ($hint)<span class="font-normal text-muted">{{ $hint }}</span>@endif</span>
</label>
