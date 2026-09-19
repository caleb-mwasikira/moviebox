{{--
  Admin video preview. Plays the video in a modal; if it fails to load while the streaming
  service is reachable, the server flags it as corrupted (see VideoHealthController).
--}}
@props(['title', 'src' => null, 'hls' => false, 'reportUrl', 'clearUrl', 'corrupted' => false, 'token', 'label' => 'Preview', 'compact' => false])

<div x-data="videoPreview({ src: @js($src), hls: @js((bool) $hls), reportUrl: @js($reportUrl), clearUrl: @js($clearUrl), token: @js($token), corrupted: @js((bool) $corrupted) })"
  {{ $attributes->merge(['class' => 'inline-flex flex-wrap items-center gap-2']) }}>
  <span x-show="corrupted && !{{ $compact ? 'true' : 'false' }}" x-cloak class="badge badge-danger"><x-ui.icon name="alert" class="size-3" /> Corrupted</span>
  <button type="button" @click="open()" :disabled="!src" class="btn btn-secondary btn-sm" :title="src ? 'Watch this video' : 'No video uploaded'">
    <x-ui.icon name="play" class="size-3.5" /> {{ $label }}
  </button>
  <button type="button" x-show="corrupted && !{{ $compact ? 'true' : 'false' }}" x-cloak @click="clearFlag()" class="btn btn-ghost btn-sm">Clear flag</button>

  <template x-teleport="body">
    <div x-show="show" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/85 p-4 backdrop-blur-sm" @keydown.escape.window="close()" @click.self="close()">
      <div class="w-full max-w-4xl overflow-hidden rounded-2xl border border-line bg-surface shadow-pop">
        <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-3">
          <p class="truncate text-sm font-semibold text-fg">{{ $title }}</p>
          <button type="button" @click="close()" class="btn btn-ghost btn-icon" aria-label="Close"><x-ui.icon name="x" class="size-4" /></button>
        </div>
        <div class="relative aspect-video bg-black">
          <video x-ref="video" controls playsinline class="size-full"></video>
          <div x-show="state === 'loading'" class="pointer-events-none absolute inset-0 grid place-items-center">
            <svg class="size-9 animate-spin text-white/80" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          </div>
          <div x-show="state === 'failed'" x-cloak class="absolute inset-0 grid place-items-center bg-black/80 p-6 text-center">
            <div class="max-w-sm space-y-3">
              <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-red-500/15 text-red-400"><x-ui.icon name="alert" class="size-6" /></span>
              <p class="font-semibold text-white">Video failed to load</p>
              <p class="text-sm text-white/70" x-text="msg"></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
</div>

@once
  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
  <script>
  function videoPreview(o) {
    const csrf = () => document.querySelector('meta[name="csrf-token"]').content;
    return {
      src: o.src, corrupted: o.corrupted,
      show: false, state: 'idle', msg: '', hls: null, timer: null, reported: false,

      open() {
        if (!this.src) return;
        this.show = true; this.state = 'loading'; this.msg = ''; this.reported = false;
        this.$nextTick(() => this.load());
      },
      close() { this.show = false; this.teardown(); },
      teardown() {
        clearTimeout(this.timer);
        if (this.hls) { this.hls.destroy(); this.hls = null; }
        const v = this.$refs.video;
        if (v) { v.pause(); v.removeAttribute('src'); v.load(); }
      },
      load() {
        const v = this.$refs.video;
        v.onloadeddata = () => { clearTimeout(this.timer); this.state = 'playing'; };
        v.onerror = () => this.fail();

        if (o.hls && window.Hls && Hls.isSupported()) {
          this.hls = new Hls({ xhrSetup: (xhr) => xhr.setRequestHeader('Authorization', 'Bearer ' + o.token) });
          this.hls.loadSource(this.src);
          this.hls.attachMedia(v);
          this.hls.on(Hls.Events.ERROR, (_, d) => { if (d.fatal) this.fail(); });
        } else {
          v.src = this.src;
        }
        // A video that never produces a frame counts as failed too.
        this.timer = setTimeout(() => { if (this.state === 'loading') this.fail(); }, 20000);
        v.play().catch(() => {});
      },
      async fail() {
        if (this.reported || !this.show) return;
        this.reported = true; clearTimeout(this.timer);
        this.state = 'failed'; this.msg = 'Checking the streaming service…';
        try {
          const r = await fetch(o.reportUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' } });
          const d = await r.json();
          if (d.marked) { this.corrupted = true; this.msg = 'The streaming service is up, so this video has been marked as corrupted. Re-upload or re-encode it to fix.'; }
          else { this.msg = d.reason || 'The video was not flagged.'; }
        } catch (e) { this.msg = 'Could not reach the server to report the problem.'; }
      },
      async clearFlag() {
        try {
          await fetch(o.clearUrl, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' } });
          this.corrupted = false;
        } catch (e) {}
      },
    };
  }
  </script>
  @endpush
@endonce
