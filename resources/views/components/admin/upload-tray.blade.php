{{-- Floating list of uploads running from this tab; keeps working while the admin uses the page. --}}
<div x-data x-show="$store.uploads.items.length" x-cloak class="fixed bottom-4 right-4 z-50 w-[min(24rem,calc(100vw-2rem))] space-y-2">
  <template x-for="item in $store.uploads.items" :key="item.id">
    <div class="panel space-y-2 p-3.5 shadow-lg">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <p class="truncate text-sm font-semibold text-fg" x-text="item.label"></p>
          <p class="text-xs text-muted">
            <span x-show="item.status === 'queued'">Waiting…</span>
            <span x-show="item.status === 'uploading'" x-text="'Uploading · ' + item.percent + '%'"></span>
            <span x-show="item.status === 'finalizing'">Saving…</span>
            <span x-show="item.status === 'done'" class="text-emerald-600 dark:text-emerald-400">Uploaded — now processing</span>
            <span x-show="item.status === 'error'" x-text="item.error" class="text-red-500"></span>
          </p>
        </div>
        <button type="button" x-show="item.status === 'done'" @click="$store.uploads.dismiss(item)" class="btn btn-ghost btn-icon btn-sm" aria-label="Dismiss">✕</button>
        <button type="button" x-show="['queued', 'uploading', 'error'].includes(item.status)" @click="confirm('Cancel this upload?') && $store.uploads.cancel(item)" class="text-xs font-semibold text-muted hover:text-fg">Cancel</button>
      </div>
      <div class="progress" x-show="item.status !== 'error'"><span class="transition-all" :style="'width:' + item.percent + '%'"></span></div>
      <button type="button" x-show="item.status === 'error'" @click="$store.uploads.retry(item)" class="btn btn-secondary btn-sm"><x-ui.icon name="refresh" class="size-4" />Retry upload</button>
    </div>
  </template>
</div>
