{{-- Progress of a background upload. Lives inside a component that uses window.uploadMixin. --}}
<div x-show="up()" x-cloak class="space-y-2 rounded-xl border border-line bg-surface p-4">
  <div class="flex items-center justify-between gap-3 text-xs font-semibold uppercase tracking-wider text-muted">
    <span x-text="up() && up().status === 'stalled' ? 'Upload interrupted' : (up() && up().status === 'finalizing' ? 'Saving…' : 'Uploading…')"></span>
    <span x-text="(up() ? up().percent : 0) + '%'"></span>
  </div>
  <div class="progress"><span class="transition-all" :style="'width:' + (up() ? up().percent : 0) + '%'"></span></div>

  <p x-show="up() && up().status === 'uploading'" class="text-xs text-muted">Uploading in the background. You can keep using this page; keep the tab open until it finishes.</p>

  <template x-if="up() && up().status === 'error'">
    <div class="flex flex-wrap items-center gap-3">
      <span class="text-xs text-red-500" x-text="up().error"></span>
      <button type="button" @click="$store.uploads.retry($store.uploads.find(uploadId))" class="btn btn-secondary btn-sm"><x-ui.icon name="refresh" class="size-4" />Retry upload</button>
    </div>
  </template>

  <template x-if="up() && up().status === 'stalled'">
    <div class="space-y-2">
      <p class="text-xs text-muted">The browser stopped sending this file (tab closed or connection lost). Choose the same file to continue from where it stopped<span x-show="cfg.upload"> — <strong x-text="cfg.upload && cfg.upload.name"></strong></span>.</p>
      <label class="btn btn-secondary btn-sm cursor-pointer"><x-ui.icon name="refresh" class="size-4" />Resume upload
        <input type="file" class="sr-only" @change="resumeWith($event)">
      </label>
    </div>
  </template>

  <p x-show="pickError" x-text="pickError" class="field-error"></p>
</div>
