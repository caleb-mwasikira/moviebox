@props(['fields' => ['video'], 'label' => 'Save'])
{{-- Create forms save the record first; the video then uploads in the background (see createWithBackgroundUploads). --}}
<div id="upload-error" class="alert alert-danger" hidden></div>

<div id="upload-started" class="panel space-y-3 p-5" hidden x-data>
  <p class="text-sm font-semibold text-fg"><span data-title></span> was saved.</p>
  <p class="text-sm text-muted">
    <span x-show="$store.uploads.busy" x-cloak>Its files are uploading in the background — you can keep working, but keep this tab open until they finish.</span>
    <span x-show="!$store.uploads.busy" x-cloak>All uploads finished. The video is now being processed for streaming.</span>
  </p>
  <template x-for="item in $store.uploads.items" :key="item.id">
    <div class="space-y-1.5">
      <div class="flex items-center justify-between gap-3 text-xs font-semibold uppercase tracking-wider text-muted">
        <span class="truncate normal-case tracking-normal" x-text="item.label"></span>
        <span x-text="item.status === 'error' ? 'Failed' : (item.status === 'done' ? 'Uploaded' : (item.status === 'finalizing' ? 'Saving…' : item.percent + '%'))"></span>
      </div>
      <div class="progress"><span class="transition-all" :style="'width:' + item.percent + '%'" :class="item.status === 'error' ? '!bg-red-500' : ''"></span></div>
      <div x-show="item.status === 'error'" x-cloak class="flex flex-wrap items-center gap-3">
        <span class="text-xs text-red-500" x-text="item.error"></span>
        <button type="button" @click="$store.uploads.retry(item)" class="btn btn-secondary btn-sm"><x-ui.icon name="refresh" class="size-4" />Retry upload</button>
      </div>
    </div>
  </template>
  <div class="flex flex-wrap gap-2">
    <a data-edit target="_blank" rel="noopener" class="btn btn-secondary btn-sm">Open in a new tab</a>
    <a data-index target="_blank" rel="noopener" class="btn btn-secondary btn-sm">View list</a>
    <a href="{{ url()->current() }}" class="btn btn-primary btn-sm">Add another</a>
  </div>
</div>

<script>
(function () {
  var form = document.getElementById('upload-form');
  var errorBox = document.getElementById('upload-error');
  var started = document.getElementById('upload-started');
  var fields = @json($fields);
  var idle = @json($label);
  if (!form) return;

  form.addEventListener('submit', function (e) {
    var chosen = fields.some(function (f) { var i = form.querySelector('[name="' + f + '"]'); return i && i.files.length; });
    if (!chosen) return; // nothing large to send: normal submit (the server explains what is missing)
    e.preventDefault();

    var submit = document.getElementById('upload-submit');
    errorBox.hidden = true;
    submit.disabled = true;
    submit.textContent = 'Saving…';

    window.createWithBackgroundUploads(form, fields).then(function (data) {
      started.querySelector('[data-title]').textContent = '“' + data.title + '”';
      started.querySelector('[data-edit]').href = data.edit_url;
      started.querySelector('[data-index]').href = data.index_url;
      started.hidden = false;
      submit.closest('div').hidden = true;
      form.querySelectorAll('input, select, textarea').forEach(function (el) { el.disabled = true; });
    }).catch(function (err) {
      errorBox.textContent = err.message;
      errorBox.hidden = false;
      submit.disabled = false;
      submit.textContent = idle;
    });
  });
})();
</script>
