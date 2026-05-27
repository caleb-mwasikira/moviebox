@if ($paginator->hasPages())
  <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

    {{-- Result count --}}
    <p class="text-xs text-gray-500 dark:text-gray-400">
      @if ($paginator->firstItem())
        Showing <span class="font-bold text-gray-700 dark:text-gray-200">{{ $paginator->firstItem() }}</span>
        –
        <span class="font-bold text-gray-700 dark:text-gray-200">{{ $paginator->lastItem() }}</span>
        of
        <span class="font-bold text-gray-700 dark:text-gray-200">{{ $paginator->total() }}</span>
      @else
        {{ $paginator->count() }} results
      @endif
    </p>

    {{-- Page links --}}
    <div class="flex items-center gap-px">

      {{-- Previous --}}
      @if ($paginator->onFirstPage())
        <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-300 dark:text-gray-600 border border-gray-200 dark:border-gray-700 cursor-not-allowed" aria-disabled="true">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
          class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:border-accent hover:text-accent transition-colors"
          aria-label="{{ __('pagination.previous') }}">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        </a>
      @endif

      {{-- Page numbers --}}
      @foreach ($elements as $element)
        @if (is_string($element))
          <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-400 dark:text-gray-500 border border-gray-200 dark:border-gray-700 cursor-default">
            {{ $element }}
          </span>
        @endif

        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span aria-current="page"
                class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold bg-accent text-gray-900 border border-accent cursor-default">
                {{ $page }}
              </span>
            @else
              <a href="{{ $url }}"
                class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:border-accent hover:text-accent transition-colors"
                aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                {{ $page }}
              </a>
            @endif
          @endforeach
        @endif
      @endforeach

      {{-- Next --}}
      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
          class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:border-accent hover:text-accent transition-colors"
          aria-label="{{ __('pagination.next') }}">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
        </a>
      @else
        <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-gray-300 dark:text-gray-600 border border-gray-200 dark:border-gray-700 cursor-not-allowed" aria-disabled="true">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
        </span>
      @endif

    </div>
  </nav>
@endif
