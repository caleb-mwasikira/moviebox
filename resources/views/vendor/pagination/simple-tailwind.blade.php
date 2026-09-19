@if ($paginator->hasPages())
  <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between gap-3">
    @if ($paginator->onFirstPage())
      <span class="btn btn-secondary btn-sm opacity-50">Previous</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-secondary btn-sm">Previous</a>
    @endif
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-secondary btn-sm">Next</a>
    @else
      <span class="btn btn-secondary btn-sm opacity-50">Next</span>
    @endif
  </nav>
@endif
