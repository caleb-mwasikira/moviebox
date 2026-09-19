<div
  x-data="{
    open: false, query: '', results: [], loading: false, activeIdx: -1, _timer: null,
    _suggestUrl: '{{ route('search.suggestions') }}',
    _browseUrl: '{{ route('browse') }}',
    show() { this.open = true; this.$nextTick(() => this.$refs.searchInput && this.$refs.searchInput.focus()); },
    hide() { this.open = false; this.query = ''; this.results = []; this.activeIdx = -1; clearTimeout(this._timer); },
    onInput() {
      clearTimeout(this._timer);
      this.activeIdx = -1;
      if (this.query.length < 2) { this.results = []; this.loading = false; return; }
      this.loading = true;
      this._timer = setTimeout(() => {
        fetch(this._suggestUrl + '?q=' + encodeURIComponent(this.query), { headers: { 'Accept': 'application/json' } })
          .then(r => r.json())
          .then(data => { this.results = data; this.loading = false; })
          .catch(() => { this.loading = false; });
      }, 280);
    },
    moveDown() { if (this.results.length) this.activeIdx = Math.min(this.activeIdx + 1, this.results.length - 1); },
    moveUp() { this.activeIdx = Math.max(this.activeIdx - 1, -1); },
    go() { var r = this.results[this.activeIdx]; if (r && r.url) { window.location.href = r.url; } else { this.submit(); } },
    submit() { if (this.query.trim()) window.location.href = this._browseUrl + '?q=' + encodeURIComponent(this.query.trim()); }
  }"
  x-on:open-search.window="show()"
  x-on:keydown.escape.window="if (open) hide()"
  x-on:keydown.window="
    if (($event.key === 'k' && ($event.metaKey || $event.ctrlKey)) ||
        ($event.key === '/' && !['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName) && !document.activeElement.isContentEditable)) {
      $event.preventDefault(); show();
    }">
  <div x-show="open" x-cloak x-transition.opacity.duration.150ms
    class="fixed inset-0 z-[100] flex items-start justify-center bg-black/70 px-4 pt-[10vh] backdrop-blur-sm" @click.self="hide()">
    <div x-show="open" x-transition class="menu-panel w-full max-w-2xl overflow-hidden !p-0" role="dialog" aria-label="Search">
      <div class="flex h-16 items-center gap-3 border-b border-line px-5">
        <x-ui.icon name="search" class="size-5 shrink-0 text-muted" />
        <input x-ref="searchInput" x-model="query" @input="onInput()"
          @keydown.arrow-down.prevent="moveDown()" @keydown.arrow-up.prevent="moveUp()"
          @keydown.enter.prevent="results.length ? go() : submit()"
          type="text" placeholder="Search movies, series, coming soon…" autocomplete="off" spellcheck="false"
          class="flex-1 bg-transparent text-base text-fg placeholder:text-muted focus:outline-none">
        <button type="button" @click="hide()" class="text-muted hover:text-fg" aria-label="Close"><x-ui.icon name="x" class="size-5" /></button>
      </div>

      <div x-show="loading" class="px-5 py-6 text-sm text-muted">Searching…</div>

      <div x-show="results.length > 0 && !loading" class="max-h-[50vh] overflow-y-auto p-2">
        <template x-for="(r, i) in results" :key="i">
          <a :href="r.url ?? (_browseUrl + '?q=' + encodeURIComponent(query))" @mouseenter="activeIdx = i"
            :class="activeIdx === i ? 'bg-surface-2' : ''" class="flex items-center gap-4 rounded-xl px-3 py-2.5 transition-colors">
            <span class="block h-14 w-10 shrink-0 overflow-hidden rounded-md bg-surface-3">
              <img x-show="r.thumb" :src="r.thumb" class="size-full object-cover" loading="lazy" alt="">
            </span>
            <span class="min-w-0 flex-1">
              <span class="block truncate text-sm font-semibold text-fg" x-text="r.title"></span>
              <span x-show="r.genre" class="block truncate text-xs text-muted" x-text="r.genre"></span>
            </span>
            <span class="badge shrink-0"
              :class="{ 'badge-brand': r.type === 'movie', 'badge-info': r.type === 'series', 'badge-warning': r.type === 'coming_soon' }"
              x-text="r.label"></span>
          </a>
        </template>
        <a :href="_browseUrl + '?q=' + encodeURIComponent(query)" class="mt-1 block rounded-xl px-3 py-2.5 text-sm font-semibold text-brand hover:bg-surface-2">
          View all results for “<span x-text="query"></span>” →
        </a>
      </div>

      <div x-show="!loading && query.length >= 2 && results.length === 0" class="px-5 py-12 text-center">
        <p class="font-semibold text-fg">No results found</p>
        <p class="mt-1 text-sm text-muted">Try a different search term</p>
      </div>

      <div x-show="query.length < 2 && !loading" class="px-5 py-5">
        <p class="mb-3 text-[11px] font-semibold uppercase tracking-widest text-muted">Try searching for</p>
        <div class="flex flex-wrap gap-2">
          <template x-for="genre in ['Action', 'Drama', 'Comedy', 'Thriller', 'Horror', 'Sci-Fi']" :key="genre">
            <button type="button" @click="query = genre; onInput()" class="badge badge-neutral px-3 py-1.5 text-xs hover:bg-brand hover:text-white" x-text="genre"></button>
          </template>
        </div>
      </div>

      <div class="hidden items-center gap-5 border-t border-line px-5 py-2.5 text-[11px] text-muted sm:flex">
        <span><kbd class="rounded bg-surface-2 px-1.5 py-0.5 font-mono">↑↓</kbd> navigate</span>
        <span><kbd class="rounded bg-surface-2 px-1.5 py-0.5 font-mono">↵</kbd> select</span>
        <span><kbd class="rounded bg-surface-2 px-1.5 py-0.5 font-mono">Esc</kbd> close</span>
      </div>
    </div>
  </div>
</div>
