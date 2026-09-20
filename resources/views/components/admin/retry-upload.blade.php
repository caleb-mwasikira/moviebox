{{-- Shown after a failed upload; resubmitting resumes from the bytes the server already holds. --}}
<button type="button" x-show="failed" x-cloak @click="$el.closest('form').requestSubmit()" class="btn btn-secondary btn-sm">
  <x-ui.icon name="refresh" class="size-4" />Retry upload
</button>
