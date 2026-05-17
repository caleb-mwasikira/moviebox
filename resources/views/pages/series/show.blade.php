@extends('layouts.app')

@section('title', $series->title)

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    @if (session('shared_guest'))
    @php $guestExpiry = \Carbon\Carbon::createFromTimestamp(session('shared_guest_expires'))->format('g:i A \o\n M j'); @endphp
    <div class="bg-accent/10 border-b border-accent/20">
      <div class="container py-2.5 flex items-center gap-x-2 text-xs font-bold text-gray-700 dark:text-gray-300">
        <svg class="h-3.5 w-3.5 fill-accent shrink-0" viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/></svg>
        You're watching as a guest on {{ session('shared_guest_name') }}'s account — access expires at {{ $guestExpiry }}.
      </div>
    </div>
    @if (session('info'))
    <div x-data="{ show: true }" x-show="show"
      x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
      class="bg-gray-800 dark:bg-gray-700 border-b border-gray-700 dark:border-gray-600">
      <div class="container py-2.5 flex items-center gap-x-2 text-xs font-medium text-gray-200">
        <svg class="h-3.5 w-3.5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        {{ session('info') }}
        <button @click="show = false" class="ml-auto shrink-0 text-white/60 hover:text-white">
          <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
      </div>
    </div>
    @endif
    @endif

    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        <div class="mb-8 md:mb-10">
          <a href="{{ route('series') }}"
            class="inline-flex items-center gap-x-2 text-sm font-bold uppercase text-gray-500 hover:text-accent transition-colors">
            <svg class="h-2 w-1.5 rotate-180 fill-current" viewBox="0 0 6 10"><path d="M1 1l4 4-4 4"/></svg>
            All Series
          </a>
        </div>

        {{-- Header: thumbnail + metadata --}}
        <div class="grid grid-cols-12 gap-x-5 md:gap-x-6 lg:gap-x-7.5 gap-y-8 mb-14 md:mb-20">

          <div class="col-span-full sm:col-span-4 md:col-span-3">
            @if ($series->posters->isNotEmpty())
              <img class="w-full aspect-[2/3] object-cover"
                src="{{ $series->posters->first()->url() }}" alt="{{ $series->title }}">
            @elseif ($series->thumbnail)
              <img class="w-full aspect-[2/3] object-cover"
                src="{{ $series->thumbnailUrl() }}" alt="{{ $series->title }}">
            @else
              <div class="w-full aspect-[2/3] bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <svg class="h-14 w-14 fill-gray-400" viewBox="0 0 24 24"><path d="M4 2h16a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/></svg>
              </div>
            @endif
          </div>

          <div class="col-span-full sm:col-span-8 md:col-span-9">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white md:text-3xl lg:text-4xl lg:leading-none mb-4">
              {{ $series->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-bold uppercase text-gray-500 mb-6">
              @if ($series->genre)
                <span class="text-accent">{{ $series->genre }}</span>
              @endif
              @if ($series->year)
                <span>{{ $series->year }}</span>
              @endif
              <span>{{ $series->seasons->count() }} {{ Str::plural('Season', $series->seasons->count()) }}</span>
            </div>

            @if ($series->description)
            <div class="text-gray-500 dark:text-gray leading-relaxed mb-6">
              {{ $series->description }}
            </div>
            @endif

            <div class="flex flex-wrap gap-3">
              @if ($series->trailerIsReady())
              <button type="button"
                onclick="openTrailerModal('{{ $series->trailerUrl() }}')"
                class="inline-flex items-center gap-x-3 font-bold leading-none uppercase py-4 px-8 text-sm text-gray-900 dark:text-white ring-2 ring-inset ring-gray-300 dark:ring-gray-600 hover:ring-accent hover:text-accent transition-colors bg-transparent">
                <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                Watch Trailer
              </button>
              @endif

              {{-- Watchlist button --}}
              @auth
                <div x-data="watchlistToggle(@js($isWatchlisted), '{{ route('watchlist.toggle', ['series', $series->id]) }}')">
                  <button type="button" @click="toggle()"
                    :disabled="busy"
                    class="inline-flex items-center gap-x-2 font-bold leading-none uppercase py-3 px-5 text-sm ring-2 ring-inset ring-gray-300 dark:ring-gray-600 hover:ring-accent hover:text-accent transition-colors bg-transparent text-gray-900 dark:text-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" :fill="watchlisted ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    <span x-text="watchlisted ? 'Watchlisted' : 'Watchlist'"></span>
                  </button>
                </div>
              @else
                <button type="button" onclick="watchOrLogin('{{ route('login') }}')"
                  class="inline-flex items-center gap-x-2 font-bold leading-none uppercase py-3 px-5 text-sm ring-2 ring-inset ring-gray-300 dark:ring-gray-600 hover:ring-accent hover:text-accent transition-colors bg-transparent text-gray-900 dark:text-white">
                  <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                  </svg>
                  Watchlist
                </button>
              @endauth

              {{-- Share --}}
              @auth
                <div x-data="shareBtn('{{ route('share.store') }}', 'series', {{ $series->id }})"
                  data-wa-msg="Hey! I've been watching *{{ $series->title }}* on MovieBox and it's seriously good. Check it out:"
                  x-init="waMsg = $el.dataset.waMsg"
                  class="relative">
                  <div class="flex items-center gap-x-2">
                    <button type="button" @click="share()" :disabled="busy"
                      class="inline-flex items-center gap-x-2 font-bold leading-none uppercase py-3 px-5 text-sm ring-2 ring-inset ring-gray-300 dark:ring-gray-600 hover:ring-accent hover:text-accent transition-colors bg-transparent text-gray-900 dark:text-white">
                      <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/></svg>
                      <span x-text="copied ? 'Copied!' : (link ? 'Copy Link' : (busy ? '…' : 'Share'))">Share</span>
                    </button>
                    <button type="button" @click="shareWhatsApp()" :disabled="busy"
                      class="inline-flex items-center gap-x-2 font-bold leading-none uppercase py-3 px-5 text-sm bg-[#25D366] hover:bg-[#1ebe5d] text-white transition-colors">
                      <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                      WhatsApp
                    </button>
                  </div>
                  <div x-show="link" style="display:none" @click.outside="link = null; copied = false"
                    class="absolute top-full left-0 mt-2 z-20 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 p-3 shadow-lg">
                    <p class="text-[10px] font-bold uppercase text-gray-500 tracking-widest mb-2">Shareable Link</p>
                    <div class="flex items-stretch gap-x-1.5">
                      <input type="text" :value="link" readonly @click="$el.select()"
                        class="flex-1 min-w-0 px-2 py-1.5 text-xs border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:outline-none cursor-pointer">
                      <button @click="copyLink()" x-text="copied ? '✓' : 'Copy'"
                        class="shrink-0 px-3 py-1.5 text-xs font-bold uppercase bg-accent text-gray-900 hover:bg-accent/90 transition-colors">Copy</button>
                    </div>
                  </div>
                  <p x-show="errorMsg" x-text="errorMsg" style="display:none"
                    class="absolute top-full left-0 mt-2 text-xs font-bold text-red-500 w-72"></p>
                </div>
              @else
                <button type="button" onclick="watchOrLogin('{{ route('login') }}')"
                  class="inline-flex items-center gap-x-2 font-bold leading-none uppercase py-3 px-5 text-sm ring-2 ring-inset ring-gray-300 dark:ring-gray-600 hover:ring-accent hover:text-accent transition-colors bg-transparent text-gray-900 dark:text-white">
                  <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/></svg>
                  Share
                </button>
                <button type="button" onclick="watchOrLogin('{{ route('login') }}')"
                  class="inline-flex items-center gap-x-2 font-bold leading-none uppercase py-3 px-5 text-sm bg-[#25D366] hover:bg-[#1ebe5d] text-white transition-colors">
                  <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                  WhatsApp
                </button>
              @endauth
            </div>
          </div>

        </div>

        {{-- Seasons / Episodes --}}
        @php $canDownload = auth()->check() && auth()->user()->canDownload(); @endphp
        @if ($series->seasons->isEmpty())
          <div class="py-16 text-center text-gray-500 font-bold uppercase text-sm tracking-widest border border-gray-200 dark:border-gray-600">
            No seasons added yet.
          </div>
        @else
          <div class="flex flex-col gap-y-2">
            @foreach ($series->seasons as $season)
            <details class="group border border-gray-200 dark:border-gray-600" @if($loop->first) open @endif>
              <summary class="flex items-center justify-between gap-x-4 px-5 py-4 cursor-pointer font-bold text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors list-none">
                <span class="text-base lg:text-lg">
                  {{ $season->label() }}
                  @if ($season->title) — {{ $season->title }} @endif
                </span>
                <span class="shrink-0 text-xs text-gray-500 font-bold uppercase">
                  {{ $season->episodes->count() }} {{ Str::plural('Episode', $season->episodes->count()) }}
                </span>
              </summary>

              <div class="border-t border-gray-200 dark:border-gray-600">
                @if ($season->episodes->isEmpty())
                  <div class="px-5 py-6 text-sm text-gray-500">No episodes yet.</div>
                @else
                  <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($season->episodes as $episode)
                    <div class="flex w-full items-start justify-between gap-x-4 px-5 py-4 {{ $episode->is_free ? 'border-l-2 border-green-500' : '' }}">
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-x-2 mb-0.5">
                          <span class="font-bold text-gray-900 dark:text-white text-sm leading-tight">{{ $episode->label() }}</span>
                        </div>
                        @if ($episode->description)
                        <div class="text-xs text-gray-500 leading-snug line-clamp-2">{{ $episode->description }}</div>
                        @endif
                      </div>
                      <div class="shrink-0 flex items-center gap-x-3">
                        @if ($episode->duration)
                          <span class="text-xs text-gray-500 font-bold">{{ $episode->duration }} min</span>
                        @endif
                        @if ($canDownload && $episode->video_path)
                          <a href="{{ route('episodes.download', $episode) }}"
                            class="inline-flex items-center justify-center w-8 h-8 border border-gray-200 dark:border-gray-600 text-gray-400 hover:text-accent hover:border-accent transition-colors"
                            title="Download for offline viewing">
                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M5 20h14v-2H5v2zM19 9h-4V3H9v6H5l7 7 7-7z"/></svg>
                          </a>
                        @endif
                        @if ($episode->videoIsReady())
                          @if ($episode->is_free)
                            <a href="{{ route('series.watch', [$series, $episode]) }}"
                              class="inline-flex items-center gap-x-2 font-bold leading-none uppercase py-2 px-4 text-xs text-white bg-green-500 hover:bg-green-600 transition-colors">
                              <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                              Watch Free
                            </a>
                          @else
                            <a href="{{ route('series.watch', [$series, $episode]) }}"
                              onclick="event.preventDefault(); watchOrLogin(this.href)"
                              class="inline-flex items-center justify-center w-8 h-8 bg-accent text-gray-900 hover:bg-accent/90 transition-colors"
                              title="Watch">
                              <svg class="h-3 w-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </a>
                          @endif
                        @else
                          <span class="text-xs text-gray-400 font-bold uppercase">Processing</span>
                        @endif
                      </div>
                    </div>
                    @endforeach
                  </div>
                @endif
              </div>
            </details>
            @endforeach
          </div>
        @endif

      </div>
    </div>

    @if ($related->isNotEmpty())
    <div class="pb-16 lg:pb-20 border-t border-gray-100 dark:border-gray-800">
      <div class="container pt-10 lg:pt-14">
        <div class="flex items-center justify-between gap-x-4 mb-6 lg:mb-8">
          <h2 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">More Like This</h2>
          <a href="{{ route('series', ['genre' => $series->genre]) }}"
            class="shrink-0 text-xs font-bold uppercase tracking-widest text-gray-500 hover:text-accent dark:hover:text-accent transition-colors">
            View All →
          </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-5 md:gap-6">
          @foreach ($related as $film)
            <x-film-card :film="$film" type="series" />
          @endforeach
        </div>
      </div>
    </div>
    @endif

  </main>

  {{-- Trailer modal --}}
  <div id="trailer-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.92); align-items:center; justify-content:center;" onclick="if(event.target===this)closeTrailerModal()">
    <button onclick="closeTrailerModal()" style="position:absolute; top:20px; right:24px; background:none; border:none; color:#fff; font-size:28px; line-height:1; cursor:pointer; z-index:1;">&#x2715;</button>
    <video id="trailer-modal-video" style="width:90vw; max-width:1100px; aspect-ratio:16/9; background:#000; display:block;" controls></video>
  </div>
@endsection

@push('scripts')
@php
    $uid      = auth()->id() ?? 0;
    $exp      = time() + 7200;
    $sig      = hash_hmac('sha256', $uid . ':' . $exp, env('APP_SECRET', ''));
    $hlsToken = base64_encode($uid . ':' . $exp . ':' . $sig);
@endphp
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
function shareBtn(storeUrl, shareableType, shareableId) {
  function fetchLink(self, onSuccess) {
    self.busy = true;
    self.errorMsg = null;
    fetch(storeUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ shareable_type: shareableType, shareable_id: shareableId }),
    })
    .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
    .then(function (res) {
      self.busy = false;
      if (!res.ok) { self.errorMsg = res.data.error || 'Could not create share link.'; return; }
      self.link = res.data.url;
      onSuccess(self.link);
    })
    .catch(function () { self.busy = false; self.errorMsg = 'Could not create share link.'; });
  }
  return {
    busy: false,
    link: null,
    copied: false,
    errorMsg: null,
    waMsg: '',
    withLink: function (onSuccess) {
      if (this.link) { onSuccess(this.link); return; }
      fetchLink(this, onSuccess);
    },
    share: function () {
      var self = this;
      this.withLink(function () { self.copyLink(); });
    },
    shareWhatsApp: function () {
      var self = this;
      var waUrl = function (url) {
        return 'https://wa.me/?text=' + encodeURIComponent(self.waMsg + '\n' + url);
      };
      if (self.link) { window.open(waUrl(self.link), '_blank'); return; }
      var win = window.open('', '_blank');
      self.busy = true;
      self.errorMsg = null;
      fetch(storeUrl, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ shareable_type: shareableType, shareable_id: shareableId }),
      })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
      .then(function (res) {
        self.busy = false;
        if (!res.ok) { if (win) win.close(); self.errorMsg = res.data.error || 'Could not create share link.'; return; }
        self.link = res.data.url;
        if (win) win.location.href = waUrl(self.link);
      })
      .catch(function () { if (win) win.close(); self.busy = false; self.errorMsg = 'Could not create share link.'; });
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

(function () {
  var hlsToken   = @json($hlsToken);
  var trailerHls = null;

  window.openTrailerModal = function (url) {
    var modal = document.getElementById('trailer-modal');
    var video = document.getElementById('trailer-modal-video');
    modal.style.display = 'flex';

    if (Hls.isSupported()) {
      if (trailerHls) trailerHls.destroy();
      trailerHls = new Hls({
        xhrSetup: function (xhr) {
          xhr.setRequestHeader('Authorization', 'Bearer ' + hlsToken);
        }
      });
      trailerHls.loadSource(url);
      trailerHls.attachMedia(video);
      trailerHls.on(Hls.Events.MANIFEST_PARSED, function () {
        video.play().catch(function () {});
      });
    } else {
      // Safari native HLS
      video.src = url;
      video.play().catch(function () {});
    }
  };

  window.closeTrailerModal = function () {
    var modal = document.getElementById('trailer-modal');
    var video = document.getElementById('trailer-modal-video');
    video.pause();
    if (trailerHls) { trailerHls.destroy(); trailerHls = null; }
    video.src = '';
    modal.style.display = 'none';
  };

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') window.closeTrailerModal();
  });
})();
</script>
@endpush

