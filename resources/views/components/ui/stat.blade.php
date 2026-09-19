@props(['label', 'value', 'icon' => null, 'hint' => null, 'tone' => 'brand'])
@php
  $tones = ['brand' => 'bg-brand-soft text-brand', 'success' => 'bg-emerald-500/12 text-emerald-500', 'warning' => 'bg-amber-500/14 text-amber-500', 'danger' => 'bg-red-500/12 text-red-500', 'info' => 'bg-sky-500/12 text-sky-500'];
@endphp
<div {{ $attributes->merge(['class' => 'card card-pad flex items-start gap-4']) }}>
  @if ($icon)
    <span class="grid size-11 shrink-0 place-items-center rounded-xl {{ $tones[$tone] ?? $tones['brand'] }}"><x-ui.icon :name="$icon" class="size-5" /></span>
  @endif
  <div class="min-w-0">
    <p class="text-xs font-semibold uppercase tracking-wider text-muted">{{ $label }}</p>
    <p class="mt-1 truncate text-2xl font-bold text-fg">{{ $value }}</p>
    @if ($hint)<p class="mt-0.5 text-xs text-muted">{{ $hint }}</p>@endif
  </div>
</div>
