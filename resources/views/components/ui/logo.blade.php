@props(['class' => ''])
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 font-extrabold tracking-tight text-fg ' . $class]) }}>
  <span class="grid size-8 place-items-center rounded-lg bg-brand text-white shadow-[0_6px_16px_-6px_var(--c-brand)]">
    <svg class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.5-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5Z"/></svg>
  </span>
  <span class="text-lg">{{ config('app.name') }}</span>
</span>
