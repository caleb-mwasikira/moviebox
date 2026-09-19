{{-- A PATCH form rendered as a status pill. --}}
@props(['action', 'on' => false, 'onLabel', 'offLabel', 'onClass' => 'badge-brand', 'offClass' => 'badge-neutral'])
<form method="POST" action="{{ $action }}" class="inline">
  @csrf @method('PATCH')
  <button type="submit" class="badge whitespace-nowrap px-3 py-1.5 transition hover:opacity-80 {{ $on ? $onClass : $offClass }}">{{ $on ? $onLabel : $offLabel }}</button>
</form>
