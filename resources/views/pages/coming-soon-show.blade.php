@extends('layouts.app')

@section('title', $comingSoon->title . ' — Coming Soon')

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        <div class="mb-8 md:mb-10">
          <a href="{{ route('coming-soon') }}"
            class="inline-flex items-center gap-x-2 text-sm font-bold uppercase text-gray-500 hover:text-accent transition-colors">
            <svg class="h-2 w-1.5 rotate-180 fill-current" viewBox="0 0 6 10"><path d="M1 1l4 4-4 4"/></svg>
            Coming Soon
          </a>
        </div>

        <div class="grid grid-cols-12 gap-x-5 md:gap-x-6 lg:gap-x-7.5 gap-y-12">

          {{-- Left: poster gallery --}}
          <div class="col-span-full md:col-span-5 lg:col-span-4">
            <div class="sticky top-8">
              @if ($comingSoon->posters->isNotEmpty())
                <div class="swiper js-vv-product-swiper mb-6 aspect-[2/3] w-full overflow-hidden">
                  <div class="swiper-wrapper">
                    @foreach ($comingSoon->posters as $poster)
                    <div class="swiper-slide">
                      <img class="h-full w-full object-cover" src="{{ $poster->url() }}" alt="{{ $comingSoon->title }}">
                    </div>
                    @endforeach
                  </div>
                </div>

                @if ($comingSoon->posters->count() > 1)
                <div class="swiper js-vv-product-swiper-nav w-full">
                  <div class="swiper-wrapper">
                    @foreach ($comingSoon->posters as $poster)
                    <div class="swiper-slide">
                      <img class="aspect-square w-full cursor-pointer object-cover opacity-100 transition-opacity duration-300 hover:opacity-50"
                        src="{{ $poster->url() }}" alt="">
                    </div>
                    @endforeach
                  </div>
                </div>
                @endif
              @elseif ($comingSoon->thumbnail)
                <img class="w-full aspect-[2/3] object-cover" src="{{ $comingSoon->thumbnailUrl() }}" alt="{{ $comingSoon->title }}">
              @else
                <div class="w-full aspect-[2/3] bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                  <svg class="h-16 w-16 fill-gray-400" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
              @endif
            </div>
          </div>

          {{-- Right: metadata + trailer --}}
          <div class="col-span-full md:col-span-7 lg:col-span-8">

            <div class="mb-10 md:mb-14">

              {{-- Coming Soon badge --}}
              <div class="mb-5">
                <span style="background:var(--color-accent,#facc15); color:#111; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; padding:3px 10px;">
                  Coming Soon
                </span>
              </div>

              <h1 class="text-2xl font-bold text-gray-900 dark:text-white md:text-3xl lg:text-4xl lg:leading-none mb-4">
                {{ $comingSoon->title }}
              </h1>

              <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-bold uppercase text-gray-500 mb-6">
                @if ($comingSoon->genre)
                  <span class="text-accent">{{ $comingSoon->genre }}</span>
                @endif
                @if ($comingSoon->year)
                  <span>{{ $comingSoon->year }}</span>
                @endif
                @if ($comingSoon->duration)
                  <span>{{ $comingSoon->duration }} min</span>
                @endif
              </div>

              @if ($comingSoon->description)
              <div class="text-gray-500 dark:text-gray leading-relaxed mb-8 max-w-2xl">
                {{ $comingSoon->description }}
              </div>
              @endif

              @if ($comingSoon->trailerIsReady())
              <button type="button"
                onclick="openTrailerModal('{{ $comingSoon->trailerUrl() }}')"
                class="inline-flex items-center gap-x-3 font-bold leading-none uppercase py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 transition-colors">
                <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                Watch Trailer
              </button>
              @else
              <div class="inline-flex items-center gap-x-3 py-3 px-5 bg-gray-100 dark:bg-gray-700">
                <svg class="h-4 w-4 fill-gray-400" viewBox="0 0 24 24"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
                <span class="text-xs font-bold uppercase text-gray-400 tracking-widest">Trailer coming soon</span>
              </div>
              @endif

            </div>

          </div>

        </div>

      </div>
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
@php
    $uid      = auth()->id() ?? 0;
    $exp      = time() + 7200;
    $sig      = hash_hmac('sha256', $uid . ':' . $exp, env('APP_SECRET', ''));
    $hlsToken = base64_encode($uid . ':' . $exp . ':' . $sig);
@endphp
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
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
