{{-- A DELETE form with a confirmation prompt. --}}
@props(['action', 'confirm' => 'Are you sure?', 'label' => 'Delete', 'method' => 'DELETE', 'class' => 'btn btn-danger-soft btn-sm'])
<form method="POST" action="{{ $action }}" class="inline" onsubmit="return confirm({{ Js::from($confirm) }})">
  @csrf @method($method)
  <button type="submit" class="{{ $class }}">{{ $label }}</button>
</form>
