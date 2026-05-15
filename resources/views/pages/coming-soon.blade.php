@extends('layouts.app')

@section('title', 'Coming Soon')

@section('content')
  <main id="main-content" class="grow">

    <div class="container py-16 lg:py-24">

      <div class="mb-12 lg:mb-20">
        <h1 class="text-2.5xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
          Coming Soon
        </h1>
        <p class="mt-3 text-gray-500 text-sm lg:text-base max-w-xl">
          Upcoming titles making their way to MovieBox. Watch trailers and be the first to know when they drop.
        </p>
      </div>

      @if ($films->isEmpty())
        <div class="py-24 text-center border border-dashed border-gray-300 dark:border-gray-600">
          <svg class="mx-auto mb-4 w-12 h-12 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
          </svg>
          <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">Nothing announced yet</p>
          <p class="text-xs text-gray-400 mt-2">Check back soon for upcoming releases.</p>
        </div>
      @else
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($films as $film)
            @php
              $thumb = $film->posters->first()?->url() ?? $film->thumbnailUrl();
            @endphp
            <div class="flex flex-col">

              {{-- Poster / Thumbnail --}}
              <div class="relative overflow-hidden bg-gray-900 aspect-[2/3] mb-5">
                @if ($thumb)
                  <img src="{{ $thumb }}" alt="{{ $film->title }}"
                    class="w-full h-full object-cover opacity-90">
                @else
                  <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                    </svg>
                  </div>
                @endif

                {{-- Coming Soon badge --}}
                <div class="absolute top-3 left-3">
                  <span style="background:var(--color-accent,#facc15); color:#111; font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:3px 8px;">
                    Coming Soon
                  </span>
                </div>

                {{-- Trailer play button --}}
                @if ($film->trailerIsReady())
                  <button type="button"
                    onclick="openTrailerModal('{{ $film->trailerUrl() }}')"
                    class="absolute inset-0 flex items-center justify-center group hover:bg-black/30 transition-colors">
                    <span class="w-14 h-14 rounded-full bg-black/40 border border-white/60 flex items-center justify-center backdrop-blur-sm group-hover:bg-accent/80 group-hover:border-accent transition-all duration-200">
                      <svg class="w-5 h-5 fill-white ml-1" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                  </button>
                @endif
              </div>

              {{-- Info --}}
              <div class="flex-1">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-1">
                  {{ $film->title }}
                </h2>

                <div class="flex flex-wrap gap-x-3 text-xs font-bold uppercase tracking-wide text-gray-500 mb-3">
                  @if ($film->genre)
                    <span style="color:var(--color-accent,#facc15);">{{ $film->genre }}</span>
                  @endif
                  @if ($film->year)
                    <span>{{ $film->year }}</span>
                  @endif
                </div>

                @if ($film->description)
                  <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-3">
                    {{ $film->description }}
                  </p>
                @endif

                @if ($film->trailerIsReady())
                  <button type="button"
                    onclick="openTrailerModal('{{ $film->trailerUrl() }}')"
                    class="mt-4 inline-flex items-center gap-x-2 text-xs font-bold uppercase tracking-wide py-2.5 px-5 border border-white/30 text-white hover:border-accent hover:text-accent transition-colors bg-transparent"
                    style="border-color: rgba(255,255,255,0.3);">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    Watch Trailer
                  </button>
                @endif
              </div>

            </div>
          @endforeach
        </div>
      @endif

    </div>

  </main>

  {{-- Trailer modal --}}
  <div id="trailer-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.92); align-items:center; justify-content:center;"
    onclick="if(event.target===this)closeTrailerModal()">
    <button onclick="closeTrailerModal()" style="position:absolute; top:20px; right:24px; background:none; border:none; color:#fff; font-size:28px; line-height:1; cursor:pointer; z-index:1;">&#x2715;</button>
    <video id="trailer-modal-video" style="width:90vw; max-width:1100px; aspect-ratio:16/9; background:#000; display:block;" controls></video>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
(function () {
  @php
    $uid = Auth::id() ?? 0;
    $exp = time() + 7200;
    $sig = hash_hmac('sha256', $uid . ':' . $exp, env('APP_SECRET', ''));
    $hlsToken = base64_encode($uid . ':' . $exp . ':' . $sig);
  @endphp
  var hlsToken = @json($hlsToken);

  window.openTrailerModal = function (url) {
    var modal = document.getElementById('trailer-modal');
    var video = document.getElementById('trailer-modal-video');

    if (Hls.isSupported() && url.includes('.m3u8')) {
      var hls = new Hls({ xhrSetup: function (xhr) { xhr.setRequestHeader('Authorization', 'Bearer ' + hlsToken); } });
      hls.loadSource(url);
      hls.attachMedia(video);
    } else {
      video.src = url;
    }

    modal.style.display = 'flex';
    video.play().catch(function () {});
  };

  window.closeTrailerModal = function () {
    var modal = document.getElementById('trailer-modal');
    var video = document.getElementById('trailer-modal-video');
    video.pause();
    video.src = '';
    modal.style.display = 'none';
  };

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') window.closeTrailerModal();
  });
})();
</script>
@endpush
