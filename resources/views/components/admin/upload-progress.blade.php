<div id="upload-progress-wrap" class="panel hidden space-y-2 p-4">
  <div class="flex justify-between text-xs font-semibold uppercase tracking-wider text-muted"><span>Uploading to server…</span><span id="upload-progress-label">0%</span></div>
  <div class="progress"><span id="upload-progress-bar" class="transition-all" style="width:0%"></span></div>
  <button type="button" id="upload-retry" hidden onclick="this.form ? this.form.requestSubmit() : document.getElementById('upload-form').requestSubmit()" class="btn btn-secondary btn-sm"><x-ui.icon name="refresh" class="size-4" />Retry upload</button>
  <p class="text-xs text-muted">Large files may take a few minutes. If the connection drops, press Retry to continue from where it stopped.</p>
</div>
