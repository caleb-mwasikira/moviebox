@props(['type', 'id', 'message'])

@auth
  <div x-data="shareBtn('{{ route('share.store') }}', '{{ $type }}', {{ $id }})" data-wa-msg="{{ $message }}" x-init="waMsg = $el.dataset.waMsg" class="relative flex items-center gap-2">
    <button type="button" @click="share()" :disabled="busy" class="btn btn-secondary">
      <x-ui.icon name="share" class="size-4" />
      <span x-text="copied ? 'Copied!' : (link ? 'Copy link' : (busy ? '…' : 'Share'))">Share</span>
    </button>
    <button type="button" @click="shareWhatsApp()" :disabled="busy" class="btn bg-[#25D366] text-white hover:bg-[#1ebe5d]">
      <svg class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.1-.2.3-.8.9-.9 1.1-.2.2-.3.2-.6.1-1.700-.8-2.800-1.500-3.900-3.400-.3-.5.3-.5.900-1.600.1-.2.1-.4 0-.5-.1-.1-.7-1.600-.9-2.200-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.500s1.100 2.900 1.200 3.100c.1.200 2.100 3.200 5.100 4.500 1.900.8 2.600.9 3.500.7.600-.1 1.700-.7 1.900-1.400.2-.7.2-1.300.2-1.400-.1-.2-.3-.3-.6-.4ZM12 2a10 10 0 0 0-8.500 15.200L2 22l4.900-1.500A10 10 0 1 0 12 2Z"/></svg>
      WhatsApp
    </button>
    <div x-show="link" x-cloak @click.outside="link = null; copied = false" class="menu-panel absolute left-0 top-full z-20 mt-2 w-80 p-3">
      <p class="label">Shareable link</p>
      <div class="flex items-stretch gap-1.5">
        <input type="text" :value="link" readonly @click="$el.select()" class="input !py-2 text-xs">
        <button type="button" @click="copyLink()" class="btn btn-primary btn-sm" x-text="copied ? '✓' : 'Copy'">Copy</button>
      </div>
    </div>
    <p x-show="errorMsg" x-cloak x-text="errorMsg" class="absolute left-0 top-full mt-2 w-72 text-xs font-medium text-red-500"></p>
  </div>
@else
  <div class="flex items-center gap-2">
    <button type="button" onclick="watchOrLogin('{{ route('login') }}')" class="btn btn-secondary"><x-ui.icon name="share" class="size-4" /> Share</button>
  </div>
@endauth

@once
  @push('scripts')
  <script>
  function shareBtn(storeUrl, shareableType, shareableId) {
    function request() {
      return fetch(storeUrl, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ shareable_type: shareableType, shareable_id: shareableId }),
      }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); });
    }
    return {
      busy: false, link: null, copied: false, errorMsg: null, waMsg: '',
      withLink: function (onSuccess) {
        var self = this;
        if (this.link) { onSuccess(this.link); return; }
        this.busy = true; this.errorMsg = null;
        request().then(function (res) {
          self.busy = false;
          if (!res.ok) { self.errorMsg = res.data.error || 'Could not create share link.'; return; }
          self.link = res.data.url;
          onSuccess(self.link);
        }).catch(function () { self.busy = false; self.errorMsg = 'Could not create share link.'; });
      },
      share: function () { var self = this; this.withLink(function () { self.copyLink(); }); },
      shareWhatsApp: function () {
        var self = this;
        var waUrl = function (url) { return 'https://wa.me/?text=' + encodeURIComponent(self.waMsg + '\n' + url); };
        if (self.link) { window.open(waUrl(self.link), '_blank'); return; }
        var win = window.open('', '_blank');
        self.busy = true; self.errorMsg = null;
        request().then(function (res) {
          self.busy = false;
          if (!res.ok) { if (win) win.close(); self.errorMsg = res.data.error || 'Could not create share link.'; return; }
          self.link = res.data.url;
          if (win) win.location.href = waUrl(self.link);
        }).catch(function () { if (win) win.close(); self.busy = false; self.errorMsg = 'Could not create share link.'; });
      },
      copyLink: function () {
        if (!this.link) return;
        var self = this;
        navigator.clipboard.writeText(this.link).then(function () {
          self.copied = true;
          setTimeout(function () { self.copied = false; }, 2000);
        }).catch(function () {});
      },
    };
  }
  </script>
  @endpush
@endonce
