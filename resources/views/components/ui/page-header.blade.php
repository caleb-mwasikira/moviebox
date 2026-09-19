@props(['title', 'subtitle' => null, 'eyebrow' => null])
<div {{ $attributes->merge(['class' => 'mb-8 flex flex-wrap items-end justify-between gap-4']) }}>
  <div class="min-w-0">
    @if ($eyebrow)<p class="eyebrow mb-2">{{ $eyebrow }}</p>@endif
    <h1 class="page-title">{{ $title }}</h1>
    @if ($subtitle)<p class="mt-1.5 max-w-2xl text-sm text-muted">{{ $subtitle }}</p>@endif
  </div>
  @if (isset($actions) && !$actions->isEmpty())
    <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
  @endif
</div>
