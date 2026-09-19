@props(['src' => null, 'class' => 'h-14 w-10'])
<span class="block shrink-0 overflow-hidden rounded-md bg-surface-3 {{ $class }}">
  @if ($src)<img src="{{ $src }}" alt="" class="size-full object-cover" loading="lazy">@else<span class="grid size-full place-items-center text-muted"><x-ui.icon name="image" class="size-4" /></span>@endif
</span>
