@props(['icon' => 'inbox', 'title', 'text' => null])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-2xl border border-dashed border-line-strong px-6 py-16 text-center']) }}>
  <span class="mb-4 grid size-14 place-items-center rounded-2xl bg-surface-2 text-muted"><x-ui.icon :name="$icon" class="size-7" /></span>
  <h3 class="text-lg font-semibold text-fg">{{ $title }}</h3>
  @if ($text)<p class="mt-1.5 max-w-sm text-sm text-muted">{{ $text }}</p>@endif
  @if (!$slot->isEmpty())<div class="mt-5 flex flex-wrap justify-center gap-2">{{ $slot }}</div>@endif
</div>
