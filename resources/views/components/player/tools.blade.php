{{-- Picture-in-picture, subtitles drawer and playback speed. Renders inline buttons. --}}
@props(['video', 'subtitles'])

<div class="flex items-center gap-1.5">
  {{-- Picture in picture --}}
  <div x-data="{ supported: !!document.pictureInPictureEnabled, on: false,
        init() { const v = document.getElementById(@js($video));
          v.addEventListener('enterpictureinpicture', () => this.on = true);
          v.addEventListener('leavepictureinpicture', () => this.on = false); },
        toggle() { const v = document.getElementById(@js($video));
          document.pictureInPictureElement ? document.exitPictureInPicture() : v.requestPictureInPicture(); } }"
    x-show="supported" x-cloak>
    <button type="button" @click="toggle()" class="btn btn-secondary btn-icon" :class="on && '!border-brand !text-brand'" title="Picture in picture"><x-ui.icon name="pip" class="size-[18px]" /></button>
  </div>

  {{-- Subtitles --}}
  @if ($subtitles->isNotEmpty())
    @php $defaultSubIdx = $subtitles->search(fn($s) => $s->is_default); @endphp
    <div x-data="subtitlePicker(@js($video), {{ $defaultSubIdx !== false ? $defaultSubIdx : 'null' }})">
      <button type="button" @click="open = true" class="btn btn-secondary btn-icon" :class="active && '!border-brand !text-brand'" title="Subtitles"><x-ui.icon name="captions" class="size-[18px]" /></button>

      <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-[9998]" @keydown.escape.window="open = false">
          <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/70" @click="open = false"></div>
          <aside x-show="open" x-transition:enter="transition duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-80 max-w-[90vw] flex-col border-l border-line bg-surface shadow-pop">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
              <h3 class="text-sm font-semibold text-fg">Subtitles</h3>
              <button type="button" @click="open = false" class="btn btn-ghost btn-icon" aria-label="Close"><x-ui.icon name="x" class="size-4" /></button>
            </div>
            <div class="flex-1 overflow-y-auto p-2">
              <button type="button" @click="off()" class="menu-item justify-between" :class="!active && '!text-brand'">
                <span>Off</span><x-ui.icon name="check" class="size-4" x-show="!active" />
              </button>
              @foreach ($subtitles as $i => $subtitle)
                <button type="button" @click="setTrack({{ $i }})" class="menu-item justify-between" :class="trackIndex === {{ $i }} && '!text-brand'">
                  <span class="flex min-w-0 items-center gap-3">
                    <span class="badge" :class="trackIndex === {{ $i }} ? 'bg-brand text-white' : 'badge-neutral'">{{ strtoupper(substr($subtitle->language_code, 0, 3)) }}</span>
                    <span class="truncate">{{ $subtitle->language }}</span>
                  </span>
                  <x-ui.icon name="check" class="size-4" x-show="trackIndex === {{ $i }}" />
                </button>
              @endforeach
            </div>
          </aside>
        </div>
      </template>
    </div>
  @endif

  {{-- Speed --}}
  <div class="relative" x-data="{ open: false, rate: 1, rates: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2],
      set(r) { document.getElementById(@js($video)).playbackRate = r; this.rate = r; this.open = false; } }" @click.outside="open = false" @keydown.escape="open = false">
    <button type="button" @click="open = !open" class="btn btn-secondary btn-icon w-auto min-w-9 px-2.5 text-xs font-bold tabular-nums" title="Playback speed" x-text="rate === 1 ? '1×' : rate + '×'">1×</button>
    <div x-show="open" x-cloak x-transition class="menu-panel absolute bottom-full right-0 z-20 mb-2 w-28">
      <template x-for="r in rates" :key="r">
        <button type="button" @click="set(r)" class="menu-item text-xs" :class="rate === r && '!text-brand'" x-text="r === 1 ? 'Normal' : r + '×'"></button>
      </template>
    </div>
  </div>
</div>

@once
  @push('scripts')
  <script>
    function subtitlePicker(videoId, defaultIndex) {
      return {
        open: false,
        trackIndex: defaultIndex,
        get active() { return this.trackIndex !== null && this.trackIndex !== false; },
        init() {
          const idx = (this.trackIndex !== null && this.trackIndex !== false) ? this.trackIndex : -1;
          const vid = document.getElementById(videoId);
          const apply = function () {
            const t = vid.textTracks;
            // Set every track to at least 'hidden' so the browser fetches the VTT
            // files. 'disabled' (the browser default) suppresses all network requests.
            for (let i = 0; i < t.length; i++) t[i].mode = i === idx ? 'showing' : 'hidden';
          };
          apply();
          vid.addEventListener('loadedmetadata', apply, { once: true });
        },
        setTrack(index) {
          const t = document.getElementById(videoId).textTracks;
          for (let i = 0; i < t.length; i++) t[i].mode = i === index ? 'showing' : 'hidden';
          this.trackIndex = index;
          this.open = false;
        },
        off() {
          const t = document.getElementById(videoId).textTracks;
          for (let i = 0; i < t.length; i++) t[i].mode = 'hidden';
          this.trackIndex = null;
          this.open = false;
        },
      };
    }
  </script>
  @endpush
@endonce
