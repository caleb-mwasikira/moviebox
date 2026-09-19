@if ($paginator->hasPages())
  <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col items-center justify-between gap-4 sm:flex-row">
    <p class="text-sm text-muted">
      Showing <span class="font-semibold text-fg">{{ $paginator->firstItem() }}</span>–<span class="font-semibold text-fg">{{ $paginator->lastItem() }}</span>
      of <span class="font-semibold text-fg">{{ $paginator->total() }}</span>
    </p>

    <ul class="flex flex-wrap items-center gap-1">
      @if ($paginator->onFirstPage())
        <li><span class="grid size-9 place-items-center rounded-lg text-muted/50" aria-disabled="true"><x-ui.icon name="chevron-left" class="size-4" /></span></li>
      @else
        <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="grid size-9 place-items-center rounded-lg text-fg-soft hover:bg-surface-2" aria-label="Previous"><x-ui.icon name="chevron-left" class="size-4" /></a></li>
      @endif

      @foreach ($elements as $element)
        @if (is_string($element))
          <li><span class="grid size-9 place-items-center text-muted">{{ $element }}</span></li>
        @endif
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <li><span aria-current="page" class="grid size-9 place-items-center rounded-lg bg-brand text-sm font-semibold text-white">{{ $page }}</span></li>
            @else
              <li><a href="{{ $url }}" class="grid size-9 place-items-center rounded-lg text-sm font-medium text-fg-soft hover:bg-surface-2" aria-label="Go to page {{ $page }}">{{ $page }}</a></li>
            @endif
          @endforeach
        @endif
      @endforeach

      @if ($paginator->hasMorePages())
        <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" class="grid size-9 place-items-center rounded-lg text-fg-soft hover:bg-surface-2" aria-label="Next"><x-ui.icon name="chevron-right" class="size-4" /></a></li>
      @else
        <li><span class="grid size-9 place-items-center rounded-lg text-muted/50" aria-disabled="true"><x-ui.icon name="chevron-right" class="size-4" /></span></li>
      @endif
    </ul>
  </nav>
@endif
