@extends('layouts.app')

@section('title', 'Home')

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    {{-- Featured movies and series --}}
    @if ($featured->isNotEmpty())
    <div class="pb-20 md:pb-24 lg:pb-[150px]">
      <div class="swiper js-featured-swiper">
        <div class="swiper-wrapper">
          @foreach ($featured as $f)
          @php
            $bgUrl = $f->posters->isNotEmpty()
              ? $f->posters->first()->url()
              : ($f->thumbnail ? $f->thumbnailUrl() : null);
            $fType = $f->content_type; // 'movie' | 'series' | 'coming_soon'
          @endphp
          <div class="swiper-slide">
            <div style="position:relative; min-height:520px; background:#0d0d0d; overflow:hidden;">

              {{-- Background poster --}}
              @if ($bgUrl)
              <div style="position:absolute; inset:0; background-image:url('{{ $bgUrl }}'); background-size:cover; background-position:center top;"></div>
              @endif

              {{-- Gradient overlays --}}
              <div style="position:absolute; inset:0; background:linear-gradient(to right, rgba(0,0,0,0.92) 0%, rgba(0,0,0,0.65) 55%, rgba(0,0,0,0.15) 100%);"></div>
              <div style="position:absolute; bottom:0; left:0; right:0; height:200px; background:linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);"></div>

              {{-- Content --}}
              <div class="container" style="position:relative; z-index:10; padding-top:70px; padding-bottom:70px; max-width:680px;">

                <div style="margin-bottom:12px;">
                  <span style="display:inline-block; background:var(--color-accent, #f5c518); color:#fff; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; padding:3px 10px;">
                    @if ($fType === 'coming_soon') Coming Soon
                    @elseif ($fType === 'series') Series
                    @else Movie
                    @endif
                  </span>
                </div>

                <h2 class="text-white" style="font-size:clamp(1.75rem,4vw,3rem); font-weight:700; line-height:1.1; margin-bottom:14px;">
                  {{ $f->title }}
                </h2>

                <div style="display:flex; flex-wrap:wrap; gap:12px; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:rgba(255,255,255,.55); margin-bottom:16px;">
                  @if ($f->genre)<span style="color:var(--color-accent, #f5c518);">{{ $f->genre }}</span>@endif
                  @if ($f->year)<span>{{ $f->year }}</span>@endif
                  @if ($fType === 'movie' && $f->duration)<span>{{ $f->duration }} min</span>@endif
                  @if ($fType === 'series')<span>{{ $f->seasons->count() }} {{ Str::plural('Season', $f->seasons->count()) }}</span>@endif
                </div>

                @if ($f->description)
                <p style="color:rgba(255,255,255,.7); font-size:15px; line-height:1.6; margin-bottom:28px; max-width:520px;">
                  {{ Str::limit($f->description, 180) }}
                </p>
                @endif

                <div style="display:flex; flex-wrap:wrap; gap:12px;">
                  @if ($f->trailerIsReady())
                  <button type="button"
                    onclick="openTrailerModal('{{ $f->trailerUrl() }}')"
                    style="display:inline-flex; align-items:center; gap:10px; padding:14px 28px; border:2px solid rgba(255,255,255,.4); background:transparent; color:#fff; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; cursor:pointer; transition:border-color .2s;"
                    onmouseover="this.style.borderColor='var(--color-accent,#f5c518)'"
                    onmouseout="this.style.borderColor='rgba(255,255,255,.4)'">
                    <svg style="width:14px; height:14px; fill:currentColor;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    Watch Trailer
                  </button>
                  @endif

                  @if ($fType === 'movie')
                    @if ($f->videoIsReady())
                    <a href="{{ route('movies.watch', $f) }}"
                      onclick="event.preventDefault(); watchOrLogin(this.href)"
                      style="display:inline-flex; align-items:center; gap:10px; padding:14px 28px; background:var(--color-accent, #f5c518); color:#fff; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; text-decoration:none;">
                      <svg style="width:14px; height:14px; fill:currentColor;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                      Watch Now
                    </a>
                    @endif
                  @elseif ($fType === 'series')
                  <a href="{{ route('series.show', $f) }}"
                    style="display:inline-flex; align-items:center; gap:10px; padding:14px 28px; background:var(--color-accent, #f5c518); color:#fff; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; text-decoration:none;">
                    <svg style="width:14px; height:14px; fill:currentColor;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    Watch Series
                  </a>
                  @endif
                </div>

              </div>

              {{-- Slide indicator --}}
              <div style="position:absolute; bottom:24px; right:24px; z-index:10;">
                <span class="js-featured-current" style="font-size:28px; font-weight:700; color:#fff; line-height:1;">01</span>
                <span style="font-size:14px; color:rgba(255,255,255,.4); font-weight:700;"> / {{ str_pad($featured->count(), 2, '0', STR_PAD_LEFT) }}</span>
              </div>

            </div>
          </div>
          @endforeach
        </div>

        {{-- Navigation --}}
        <button class="js-featured-prev" style="position:absolute; left:20px; top:50%; transform:translateY(-50%); z-index:20; width:44px; height:44px; background:rgba(0,0,0,.5); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#fff; transition:background .2s;"
          onmouseover="this.style.background='rgba(0,0,0,.8)'" onmouseout="this.style.background='rgba(0,0,0,.5)'">
          <svg style="width:10px; height:16px; fill:currentColor;" viewBox="0 0 6 10"><path d="M5 1L1 5l4 4"/></svg>
        </button>
        <button class="js-featured-next" style="position:absolute; right:20px; top:50%; transform:translateY(-50%); z-index:20; width:44px; height:44px; background:rgba(0,0,0,.5); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#fff; transition:background .2s;"
          onmouseover="this.style.background='rgba(0,0,0,.8)'" onmouseout="this.style.background='rgba(0,0,0,.5)'">
          <svg style="width:10px; height:16px; fill:currentColor;" viewBox="0 0 6 10"><path d="M1 1l4 4-4 4"/></svg>
        </button>

      </div>
    </div>
    @endif
    {{-- End of featured movies and series --}}

    {{-- Latest Series strip --}}
    @if ($series->isNotEmpty())
    <div class="pb-20 md:pb-24 lg:pb-[120px]">
      <div class="container">

        <div class="mb-10 md:mb-12 lg:mb-16 xl:mb-20">
          <h2
            class="text-2.5xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
            New Series
          </h2>
        </div>

        <div class="swiper js-vv-series-swiper">

          <div class="swiper-wrapper">
            @foreach ($series as $show)
            <div class="swiper-slide">
              <x-film-card :film="$show" type="series" />
            </div>
            @endforeach
          </div>

          <div class="flex justify-center gap-x-3.5 pt-8 sm:pt-10 md:pt-12 lg:pt-16">
            <button
              class="js-vv-series-swiper-btn-prev flex w-8 aspect-square items-center justify-center text-white hover:cursor-pointer hover:text-accent">
              <svg class="h-2.5 w-1.5" fill="currentColor">
                <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#arrow-left-pixel-alt"></use>
              </svg>
            </button>
            <button
              class="js-vv-series-swiper-btn-next flex w-8 aspect-square items-center justify-center text-white hover:cursor-pointer hover:text-accent">
              <svg class="h-2.5 w-1.5" fill="currentColor">
                <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#arrow-right-pixel-alt"></use>
              </svg>
            </button>
          </div>

        </div>
      </div>
    </div>
    @endif

    <section class="pb-20 pt-4 md:pb-24 lg:pb-[130px] lg:pt-6">
      <div class="container">

        <div class="grid grid-cols-12 gap-x-5 gap-y-12 md:gap-x-6 lg:gap-x-7.5">

          <div
            class="col-span-full grid grid-cols-7 gap-x-5 sm:grid-rows-[197px_107px_85px] md:col-span-7 md:grid-rows-[152px_83px_66px] md:gap-x-6 lg:grid-rows-[197px_107px_85px] lg:gap-x-7.5 xl:grid-rows-[246px_134px_106px]">
            <figure class="col-span-full sm:row-start-1 sm:row-end-3">
              <img class="w-full sm:w-auto" src="{{ asset('assets/img/str1/samples/about-1-668x380.jpg') }}" alt="">
            </figure>

            <figure
              class="relative col-span-full sm:col-span-5 sm:col-start-3 sm:row-start-2 sm:row-end-4 sm:pl-3 sm:pr-10">
              <img class="w-full sm:w-auto sm:shadow-[0_0_60px_0_rgba(0,0,0,.4)]"
                src="{{ asset('assets/img/str1/samples/about-2-420x240.jpg') }}" alt="">

              <div
                class="bg-social-twitch absolute -left-8 top-1/2 hidden aspect-square w-20 -translate-y-2/3 items-center justify-center rounded-full text-white shadow-[0_0_60px_0_rgba(0,0,0,.4)] before:absolute before:-left-1 before:bottom-0 before:h-0 before:w-0 before:rotate-[52deg] before:border-l-[12px] before:border-r-[12px] before:border-t-[20px] before:border-l-transparent before:border-r-transparent before:border-t-primary sm:flex">
                <svg class="aspect-square w-10" fill="currentColor">
                  <use xlink:href="{{ asset('assets/img/social-icons.svg') }}#twitch"></use>
                </svg>
              </div>
            </figure>
          </div>

          <div class="col-span-full md:col-span-5">
            <div class="md:-ml-[76px] lg:-ml-[100px] xl:-ml-[130px]">
              <h2
                class="mb-8 text-2xl font-bold tracking-tighter text-gray-900 dark:text-white sm:mb-9 sm:text-3xl md:-mt-[0.6em] md:mb-10 md:text-4xl md:leading-none lg:-mt-[0.6em] lg:mb-12 lg:text-5xl lg:leading-none xl:-mt-[0.7em] xl:mb-14 xl:text-[62px] xl:leading-none">
                <span class="inline-block md:block">Stream the World's Greatest</span>
                <span class="inline-block md:block xl:-mt-2">
                  <span class="text-accent">Cinema</span>
                  &amp;
                  <span class="text-accent">Stories</span></span>
              </h2>
            </div>

            <div class="md:pl-4 md:pr-2 lg:px-7 xl:pr-[70px] xl:pl-10 [&_p]:mb-8">
              <p>MovieBox is your destination for world-class cinema. From timeless classics to the latest blockbusters, our ever-growing library puts thousands of films right at your fingertips — in stunning 4K with Dolby Atmos sound.</p>

              <p>Whether you love gripping dramas, pulse-pounding thrillers, laugh-out-loud comedies or epic sci-fi adventures, MovieBox has something for every taste. Enjoy unlimited streaming on all your devices, with new titles added every week.</p>
            </div>
          </div>

        </div>

      </div>
    </section>

    <div class="pb-20 md:pb-24 lg:pb-[120px]">
      <div class="container">

        <div class="mb-10 md:mb-12 lg:mb-16 xl:mb-20">
          <h2
            class="text-2.5xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
            Top Rated Movies
          </h2>
        </div>

        <div class="swiper js-vv-games-swiper">

          <div class="swiper-wrapper">
            @forelse ($movies as $movie)
            <div class="swiper-slide">
              <x-film-card :film="$movie" type="movie" />
            </div>
            @empty
            <div class="swiper-slide">
              <div class="w-full aspect-[17/22] bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <span class="text-xs text-gray-400 uppercase font-bold">No movies yet</span>
              </div>
            </div>
            @endforelse
          </div>

          <div class="flex justify-center gap-x-3.5 pt-8 sm:pt-10 md:pt-12 lg:pt-16">
            <button
              class="js-vv-games-swiper-btn-prev flex w-8 aspect-square items-center justify-center text-white hover:cursor-pointer hover:text-accent">
              <svg class="h-2.5 w-1.5" fill="currentColor">
                <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#arrow-left-pixel-alt"></use>
              </svg>
            </button>
            <button
              class="js-vv-games-swiper-btn-next flex w-8 aspect-square items-center justify-center text-white hover:cursor-pointer hover:text-accent">
              <svg class="h-2.5 w-1.5" fill="currentColor">
                <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#arrow-right-pixel-alt"></use>
              </svg>
            </button>
          </div>

        </div>
      </div>
    </div>

    {{-- Coming Soon strip --}}
    @if ($comingSoon->isNotEmpty())
    <div class="pb-20 md:pb-24 lg:pb-[120px]">
      <div class="container">

        <div class="mb-10 md:mb-12 lg:mb-16 xl:mb-20 flex items-end justify-between gap-x-6">
          <h2 class="text-2.5xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
            Coming Soon
          </h2>
          <a href="{{ route('coming-soon') }}"
            class="shrink-0 text-xs font-bold uppercase tracking-widest text-gray-500 hover:text-accent dark:hover:text-accent transition-colors mb-2 lg:mb-4">
            View All →
          </a>
        </div>

        <div class="swiper js-coming-soon-swiper">
          <div class="swiper-wrapper">
            @foreach ($comingSoon as $cs)
            <div class="swiper-slide">
              <x-coming-soon-card :film="$cs" />
            </div>
            @endforeach
          </div>

          <div class="flex justify-center gap-x-3.5 pt-8 sm:pt-10 md:pt-12 lg:pt-16">
            <button class="js-coming-soon-prev flex w-8 aspect-square items-center justify-center text-white hover:cursor-pointer hover:text-accent">
              <svg class="h-2.5 w-1.5" fill="currentColor">
                <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#arrow-left-pixel-alt"></use>
              </svg>
            </button>
            <button class="js-coming-soon-next flex w-8 aspect-square items-center justify-center text-white hover:cursor-pointer hover:text-accent">
              <svg class="h-2.5 w-1.5" fill="currentColor">
                <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#arrow-right-pixel-alt"></use>
              </svg>
            </button>
          </div>
        </div>

      </div>
    </div>
    @endif
    {{-- End Coming Soon strip --}}

    <div class="pb-20 md:pb-24 lg:pb-[130px]">
      <div class="container">

        <div class="mb-10 md:mb-12 lg:mb-16 xl:mb-20">
          <h2
            class="text-2.5xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
            Featured Films
          </h2>
        </div>

        @if ($featured->isNotEmpty())
        <div class="grid grid-cols-12 gap-5 md:gap-6 lg:gap-7.5 mb-7.5">

          @foreach ($featured->take(3) as $i => $fm)
            @php
              $thumb   = $fm->posters->first()?->url() ?? $fm->thumbnailUrl();
              $isFirst = $i === 0;
              $fmType  = $fm->content_type; // 'movie' | 'series' | 'coming_soon'
              $aspect  = $isFirst ? 'aspect-[77/46]' : 'aspect-[370/215]';
            @endphp
            <figure class="{{ $isFirst
              ? 'col-span-full md:row-start-1 md:row-end-2 lg:col-span-8 lg:row-start-1 lg:row-end-3'
              : ($i === 1
                  ? 'col-span-full md:row-start-2 md:row-end-2 md:col-span-6 lg:col-span-4 lg:row-start-1 lg:row-end-2'
                  : 'col-span-full md:row-start-2 md:row-end-2 md:col-span-6 lg:col-span-4 lg:row-start-2 lg:row-end-3'
                ) }}">

              @if ($fmType === 'coming_soon')
              {{-- Coming Soon card — no watch link, trailer play if available --}}
              <div class="group relative block h-full overflow-hidden bg-gray-900">
                @if ($thumb)
                  <img class="{{ $aspect }} w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="{{ $thumb }}" alt="{{ $fm->title }}">
                @else
                  <div class="{{ $aspect }} w-full bg-gray-800"></div>
                @endif
                <div class="absolute inset-0 bg-black/50"></div>
                {{-- Coming Soon badge --}}
                <div class="absolute top-3 left-3">
                  <span style="background:var(--color-accent,#facc15); color:#111; font-size:9px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:2px 7px;">Coming Soon</span>
                </div>
                @if ($fm->trailerIsReady())
                <div class="absolute inset-0 flex items-center justify-center cursor-pointer" onclick="openTrailerModal('{{ $fm->trailerUrl() }}')">
                  <span class="w-10 h-10 rounded-full bg-black/40 border border-white/60 flex items-center justify-center backdrop-blur-sm group-hover:bg-accent/80 group-hover:border-accent transition-all duration-200">
                    <svg class="w-4 h-4 fill-white ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                  </span>
                </div>
                @endif
                <div class="absolute flex inset-x-5 xl:inset-x-6 bottom-5 xl:bottom-6 gap-x-3 items-end">
                  <div class="flex-1 leading-tight min-w-0">
                    <h6 class="font-bold text-white truncate w-full">{{ $fm->title }}</h6>
                    <span class="text-gray-400 text-xs">{{ $fm->genre }}{{ $fm->year ? ' · ' . $fm->year : '' }}</span>
                  </div>
                </div>
              </div>

              @elseif ($fmType === 'series')
              <a class="group relative block h-full overflow-hidden bg-gray-900"
                href="{{ route('series.show', $fm) }}">
                @if ($thumb)
                  <img class="{{ $aspect }} w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="{{ $thumb }}" alt="{{ $fm->title }}">
                @else
                  <div class="{{ $aspect }} w-full bg-gray-800"></div>
                @endif
                <div class="absolute inset-0 bg-black/50"></div>
                <span class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                  <svg class="fill-white w-6 ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </span>
                <div class="absolute flex inset-x-5 xl:inset-x-6 bottom-5 xl:bottom-6 gap-x-3 items-end">
                  <div class="flex-1 leading-tight min-w-0">
                    <h6 class="font-bold text-white truncate w-full">{{ $fm->title }}</h6>
                    <span class="text-gray-400 text-xs">{{ $fm->genre }}{{ $fm->year ? ' · ' . $fm->year : '' }}</span>
                  </div>
                </div>
              </a>

              @else
              {{-- Movie --}}
              <a class="group relative block h-full overflow-hidden bg-gray-900"
                href="{{ route('movies.watch', $fm) }}"
                onclick="event.preventDefault(); watchOrLogin(this.href)">
                @if ($thumb)
                  <img class="{{ $aspect }} w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="{{ $thumb }}" alt="{{ $fm->title }}">
                @else
                  <div class="{{ $aspect }} w-full bg-gray-800"></div>
                @endif
                <div class="absolute inset-0 bg-black/50"></div>
                <span class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                  <svg class="fill-white w-6 ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </span>
                <div class="absolute flex inset-x-5 xl:inset-x-6 bottom-5 xl:bottom-6 gap-x-3 items-end">
                  <div class="flex-1 leading-tight min-w-0">
                    <h6 class="font-bold text-white truncate w-full">{{ $fm->title }}</h6>
                    <span class="text-gray-400 text-xs">{{ $fm->genre }}{{ $fm->year ? ' · ' . $fm->year : '' }}</span>
                  </div>
                </div>
              </a>
              @endif

            </figure>
          @endforeach

        </div>
        @endif

        <div class="grid grid-cols-12 gap-5 md:gap-6 lg:gap-7.5">
          <div class="col-span-full md:col-span-6">
            <div
              class="relative isolate w-full aspect-[57/16] flex bg-accent text-gray-900 overflow-hidden justify-between items-center">
              <div class="leading-tight pl-5 lg:pl-8 xl:pl-10 xl:pb-2 lg:pb-3 flex-1">
                <div class="font-bold text-base leading-none sm:text-lg md:text-base md:leading-none lg:text-lg mb-1.5 xl:mb-2.5">
                  Thousands of titles await</div>
                <h4 class="font-bold text-2xl sm:text-3xl md:text-2xl md:leading-none leading-none lg:text-3xl xl:text-[42px]">
                  Browse All Films</h4>
              </div>

              <img class="absolute -z-10 right-0 top-0 bottom-0 object-cover object-left max-w-[39%]"
                src="{{ asset('assets/img/str1/samples/stream-banner-1.png') }}" alt="">

              <a href="{{ route('browse') }}" class="block absolute inset-0"></a>
            </div>
          </div>

          <div class="col-span-full md:col-span-6">
            <div
              class="relative isolate w-full aspect-[57/16] flex bg-primary text-white overflow-hidden justify-between items-center">
              <div class="leading-tight pl-5 lg:pl-8 xl:pl-10 xl:pb-2 lg:pb-3 flex-1">
                <div class="font-bold text-base leading-none sm:text-lg md:text-base md:leading-none lg:text-lg mb-1.5 xl:mb-2.5">
                  Unlimited access from</div>
                <h4 class="font-bold text-2xl sm:text-3xl md:text-2xl md:leading-none leading-none lg:text-3xl xl:text-[42px]">
                  @if ($cheapestPackage)
                    {{ $cheapestPackage->currency ?? '$' }}{{ number_format($cheapestPackage->price, 2) }} / {{ $cheapestPackage->interval ?? 'month' }}
                  @else
                    Subscribe Now
                  @endif
                </h4>
              </div>

              <img class="absolute -z-10 right-0 top-0 bottom-0 object-cover object-left max-w-[27%]"
                src="{{ asset('assets/img/str1/stream-banner-2.png') }}" alt="">

              @auth
                <a href="{{ route('account.dashboard') }}" class="block absolute inset-0"></a>
              @else
                <a href="{{ route('register') }}" class="block absolute inset-0"></a>
              @endauth
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="pt-4 pb-16 lg:pt-5 lg:pb-44">
      <div class="mx-auto max-w-[1320px] bg-social-twitter pt-14 pb-16 md:pt-20 md:pb-28 lg:pt-24 lg:pb-32">
        <div class="container">

          <div>
            <div
              class="mb-10 md:mb-12 lg:mb-16 xl:mb-20 flex flex-col gap-y-6 sm:flex-row sm:justify-between sm:items-center">
              <div class="flex gap-x-6 lg:gap-x-8 items-center justify-between sm:justify-start">
                <h2
                  class="text-2.5xl font-bold tracking-tighter text-white before:text-accent before:content-['.'] sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
                  X Feed
                </h2>

                <div class="flex gap-x-3">
                  <div
                    class="js-vv-twitter-feed-swiper-btn-prev flex w-8 aspect-square items-center justify-center text-white hover:cursor-pointer hover:text-accent">
                    <svg class="h-2.5 w-1.5" fill="currentColor">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#arrow-left-pixel-alt"></use>
                    </svg>
                  </div>
                  <div
                    class="js-vv-twitter-feed-swiper-btn-next flex w-8 aspect-square items-center justify-center text-white hover:cursor-pointer hover:text-accent">
                    <svg class="h-2.5 w-1.5" fill="currentColor">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#arrow-right-pixel-alt"></use>
                    </svg>
                  </div>
                </div>
              </div>

              <a href="https://x.com"
                class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 md:py-[21px] px-7 lg:px-16 text-white ring-1 ring-inset ring-white bg-transparent hover:bg-white hover:text-gray-900 hover:ring-0">
                <svg class="h-[14px] w-[14px]" fill="currentColor">
                  <use xlink:href="{{ asset('assets/img/social-icons.svg') }}#x"></use>
                </svg>
                Follow Us!
              </a>
            </div>

            <div class="swiper js-vv-twitter-feed-swiper pt-1">

              <div class="swiper-wrapper">
                <div class="swiper-slide">
                  <div class="text-white">
                    <div class="flex gap-x-4 items-center mb-5">
                      <figure class="overflow-hidden rounded-full shrink-0">
                        <img src="{{ asset('assets/img/str1/samples/twitter-avatar-60x60.jpg') }}" alt="MovieBox">
                      </figure>
                      <div class="leading-tight font-bold">
                        <div class="text-1.5xl"><a href="#">MovieBox</a></div>
                        <div><a href="#">@MovieBoxApp</a></div>
                      </div>
                    </div>
                    <div
                      class="[&_a]:text-accent [&_a]:font-bold hover:[&_a]:text-white [&_a]:transition-colors leading-[26px] mb-5">
                      This Friday at 8PM ET — Dune: Prophecy Season 2 drops exclusively on MovieBox. Premium subscribers get 48-hour early access. Don't miss it! <a href="#">#DuneProphecy</a> <a href="#">#MovieBox</a>
                    </div>
                    <div class="text-sm">
                      <time datetime="2026-05-09 10:00">26 minutes ago</time>
                    </div>
                  </div>
                </div>
                <div class="swiper-slide">
                  <div class="text-white">
                    <div class="flex gap-x-4 items-center mb-5">
                      <figure class="overflow-hidden rounded-full shrink-0">
                        <img src="{{ asset('assets/img/str1/samples/twitter-avatar-60x60.jpg') }}" alt="MovieBox">
                      </figure>
                      <div class="leading-tight font-bold">
                        <div class="text-1.5xl"><a href="#">MovieBox</a></div>
                        <div><a href="#">@MovieBoxApp</a></div>
                      </div>
                    </div>
                    <div
                      class="[&_a]:text-accent [&_a]:font-bold hover:[&_a]:text-white [&_a]:transition-colors leading-[26px] mb-5">
                      50 new titles added this week including the complete Christopher Nolan collection in 4K Dolby Vision. Start watching now! <a href="#">#NowStreaming</a> <a href="#">#4K</a> <a href="#">#Nolan</a>
                    </div>
                    <div class="text-sm">
                      <time datetime="2026-05-08 14:00">14 hours ago</time>
                    </div>
                  </div>
                </div>
                <div class="swiper-slide">
                  <div class="text-white">
                    <div class="flex gap-x-4 items-center mb-5">
                      <figure class="overflow-hidden rounded-full shrink-0">
                        <img src="{{ asset('assets/img/str1/samples/twitter-avatar-60x60.jpg') }}" alt="MovieBox">
                      </figure>
                      <div class="leading-tight font-bold">
                        <div class="text-1.5xl"><a href="#">MovieBox</a></div>
                        <div><a href="#">@MovieBoxApp</a></div>
                      </div>
                    </div>
                    <div
                      class="[&_a]:text-accent [&_a]:font-bold hover:[&_a]:text-white [&_a]:transition-colors leading-[26px] mb-5">
                      Our Summer Cinema Retrospective charity event raised over $40,000 for <a href="#">@FilmForAll</a>! Thank you to every subscriber who joined. Replay available on-demand now.
                    </div>
                    <div class="text-sm">
                      <time datetime="2026-05-05 09:00">4 days ago</time>
                    </div>
                  </div>
                </div>
              </div>

            </div>

          </div>

        </div>
      </div>
    </section>

  </main>

  {{-- Trailer modal --}}
  <div id="trailer-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.92); align-items:center; justify-content:center;" onclick="if(event.target===this)closeTrailerModal()">
    <button onclick="closeTrailerModal()" style="position:absolute; top:20px; right:24px; background:none; border:none; color:#fff; font-size:28px; line-height:1; cursor:pointer; z-index:1;">&#x2715;</button>
    <video id="trailer-modal-video" style="width:90vw; max-width:1100px; aspect-ratio:16/9; background:#000; display:block;" controls></video>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
(function () {
  // ── Featured hero carousel ────────────────────────────────────
  var featuredEl = document.querySelector('.js-featured-swiper');
  if (featuredEl) {
    var featuredSwiper = new Swiper(featuredEl, {
      loop: true,
      speed: 700,
      autoplay: { delay: 6000, disableOnInteraction: false },
      navigation: { prevEl: '.js-featured-prev', nextEl: '.js-featured-next' },
      on: {
        slideChange: function () {
          var el = document.querySelector('.js-featured-current');
          if (el) el.textContent = String(this.realIndex + 1).padStart(2, '0');
        },
      },
    });
  }

  // ── Trailer modal ─────────────────────────────────────────────
  @php
    $uid = Auth::id() ?? 0;
    $exp = time() + 7200;
    $sig = hash_hmac('sha256', $uid . ':' . $exp, env('APP_SECRET', ''));
    $hlsToken = base64_encode($uid . ':' . $exp . ':' . $sig);
  @endphp
  var _hlsToken = @json($hlsToken);
  var _hlsInst  = null;

  window.openTrailerModal = function (url) {
    var modal = document.getElementById('trailer-modal');
    var video = document.getElementById('trailer-modal-video');

    if (typeof Hls !== 'undefined' && Hls.isSupported() && url.indexOf('.m3u8') !== -1) {
      if (_hlsInst) { _hlsInst.destroy(); }
      _hlsInst = new Hls({ xhrSetup: function (xhr) { xhr.setRequestHeader('Authorization', 'Bearer ' + _hlsToken); } });
      _hlsInst.loadSource(url);
      _hlsInst.attachMedia(video);
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

  // ── Coming Soon swiper ────────────────────────────────────────
  var csEl = document.querySelector('.js-coming-soon-swiper');
  if (csEl) {
    new Swiper(csEl, {
      slidesPerView: 2,
      spaceBetween: 20,
      navigation: {
        prevEl: '.js-coming-soon-prev',
        nextEl: '.js-coming-soon-next',
      },
      breakpoints: {
        640:  { slidesPerView: 3, spaceBetween: 20 },
        768:  { slidesPerView: 4, spaceBetween: 24 },
        1024: { slidesPerView: 5, spaceBetween: 30 },
      },
    });
  }

  // ── New Series swiper ─────────────────────────────────────────
  var seriesEl = document.querySelector('.js-vv-series-swiper');
  if (seriesEl) {
    new Swiper(seriesEl, {
      slidesPerView: 2,
      spaceBetween: 20,
      navigation: {
        prevEl: '.js-vv-series-swiper-btn-prev',
        nextEl: '.js-vv-series-swiper-btn-next',
      },
      breakpoints: {
        640:  { slidesPerView: 3, spaceBetween: 20 },
        768:  { slidesPerView: 4, spaceBetween: 24 },
        1024: { slidesPerView: 5, spaceBetween: 30 },
      },
    });
  }
})();
</script>
@endpush
