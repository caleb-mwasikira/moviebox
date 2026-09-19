@php
  $flashes = collect([
    ['error', session('error')],
    ['info', session('info')],
    ['success', session('success')],
  ])->filter(fn ($f) => filled($f[1]));
@endphp

@if ($flashes->isNotEmpty())
  <div class="pointer-events-none fixed inset-x-0 top-20 z-[60] flex flex-col items-center gap-2 px-4">
    @foreach ($flashes as [$type, $message])
      <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 7000)" x-show="show" x-transition.opacity
        class="alert {{ $type === 'error' ? 'alert-error' : ($type === 'info' ? 'alert-info' : 'alert-success') }} pointer-events-auto w-full max-w-md bg-surface shadow-pop" role="status">
        <x-ui.icon :name="$type === 'error' ? 'alert' : ($type === 'info' ? 'info' : 'check-circle')" class="mt-0.5 size-5 shrink-0" />
        <p class="flex-1 leading-snug">{{ $message }}</p>
        <button type="button" @click="show = false" class="opacity-60 hover:opacity-100" aria-label="Dismiss"><x-ui.icon name="x" class="size-4" /></button>
      </div>
    @endforeach
  </div>
@endif
