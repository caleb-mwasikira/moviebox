@extends('layouts.app')

@section('title', 'Home')

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    <section class="py-12 lg:py-16 lg:pb-[70px]">
      <div class="mx-auto max-w-[1360px] px-5">
        <div class="grid grid-cols-12 gap-x-5 gap-y-6 md:gap-x-6 lg:gap-x-7.5">
          <div class="col-span-6 sm:col-span-4 lg:col-span-2 xl:col-span-2">
            <a href="#" class="group">
              <img class="mx-auto opacity-20 transition-opacity group-hover:opacity-100"
                src="{{ asset('assets/img/str1/sponsor-1-md.png') }}" alt="">
            </a>
          </div>
          <div class="col-span-6 sm:col-span-4 lg:col-span-2 xl:col-span-2">
            <a href="#" class="group">
              <img class="mx-auto opacity-20 transition-opacity group-hover:opacity-100"
                src="{{ asset('assets/img/str1/sponsor-2-md.png') }}" alt="">
            </a>
          </div>
          <div class="col-span-6 sm:col-span-4 lg:col-span-2 xl:col-span-2">
            <a href="#" class="group">
              <img class="mx-auto opacity-20 transition-opacity group-hover:opacity-100"
                src="{{ asset('assets/img/str1/sponsor-3-md.png') }}" alt="">
            </a>
          </div>
          <div class="col-span-6 sm:col-span-4 lg:col-span-2 xl:col-span-2">
            <a href="#" class="group">
              <img class="mx-auto opacity-20 transition-opacity group-hover:opacity-100"
                src="{{ asset('assets/img/str1/sponsor-4-md.png') }}" alt="">
            </a>
          </div>
          <div class="col-span-6 sm:col-span-4 lg:col-span-2 xl:col-span-2">
            <a href="#" class="group">
              <img class="mx-auto opacity-20 transition-opacity group-hover:opacity-100"
                src="{{ asset('assets/img/str1/sponsor-5-md.png') }}" alt="">
            </a>
          </div>
          <div class="col-span-6 sm:col-span-4 lg:col-span-2 xl:col-span-2">
            <a href="#" class="group">
              <img class="mx-auto opacity-20 transition-opacity group-hover:opacity-100"
                src="{{ asset('assets/img/str1/sponsor-6-md.png') }}" alt="">
            </a>
          </div>
        </div>
      </div>
    </section>

    <div class="mx-auto max-w-[1360px] px-5 pb-20 md:pb-24 lg:pb-[150px]">

      <div class="relative min-h-[220px] bg-[url('../../../assets/img/str1/countdown-bg.jpg')] bg-cover bg-no-repeat">

        <div class="container relative z-10 text-white">
          <div class="grid grid-cols-12 gap-x-5 gap-y-8 py-7 px-2 md:gap-x-6 lg:gap-y-12 lg:gap-x-7.5 lg:py-11 lg:px-0">

            <div class="col-span-full md:col-span-5 md:col-start-3 xl:-ml-7.5">
              <div class="text-lg font-bold">New Arrival</div>
              <h3 class="mb-3 text-2xl font-bold leading-none md:text-2.5xl lg:mb-4 lg:text-4xl xl:text-[42px]">Dune: Prophecy — Season 2</h3>
              <div class="text-base font-medium leading-normal">
                The highly anticipated second season of Dune: Prophecy arrives exclusively on MovieBox. Stream it before anywhere else — premium subscribers get early access.
              </div>
            </div>

            <div
              class="col-span-full flex flex-wrap items-center justify-center gap-y-4 self-center md:col-span-4 md:col-start-9 lg:gap-y-0">

              <div
                class="js-vv-countdown-container mb-4 grid w-full basis-full grid-cols-4 font-bold lg:mb-5 xl:pt-2.5 [&>div]:before:absolute [&>div]:before:-left-0.5 [&>div]:before:top-0 [&>div]:before:text-xl [&>div]:before:content-[':'] [&>div]:md:before:text-3xl [&>div]:lg:before:-left-1 [&>div]:xl:before:text-[42px] [&>div]:xl:before:leading-none [&>div:first-child]:before:hidden">

                <div class="relative flex flex-col items-center">
                  <div class="js-vv-days text-xl md:text-3xl xl:mb-1 xl:text-[42px] xl:leading-none">00</div>
                  <div class="days-label leading-none">Days</div>
                </div>

                <div class="relative flex flex-col items-center">
                  <div class="js-vv-hours text-xl md:text-3xl xl:mb-1 xl:text-[42px] xl:leading-none">00</div>
                  <div class="hours-label leading-none">Hours</div>
                </div>

                <div class="relative flex flex-col items-center">
                  <div class="js-vv-minutes text-xl md:text-3xl xl:mb-1 xl:text-[42px] xl:leading-none">00</div>
                  <div class="minutes-label leading-none">Mins</div>
                </div>

                <div class="relative flex flex-col items-center">
                  <div class="js-vv-seconds text-xl md:text-3xl xl:mb-1 xl:text-[42px] xl:leading-none">00</div>
                  <div class="seconds-label leading-none">Secs</div>
                </div>

              </div>

              <div class="js-vv-countdown-progress h-2 basis-full overflow-hidden rounded-full bg-gray-900">
                <div class="js-vv-countdown-progress-bar h-full rounded-full bg-accent">test</div>
              </div>

            </div>

          </div>
        </div>

        <div class="absolute inset-0 z-[1] bg-black mix-blend-color"></div>
        <div class="absolute inset-0 z-[2] bg-primary mix-blend-multiply"></div>
        <div class="absolute inset-0 z-[3] bg-gray-900 mix-blend-screen"></div>

        <img class="pointer-events-none absolute -left-8 bottom-0 z-[4] hidden md:block"
          src="{{ asset('assets/img/str1/countdown-hero.png') }}" alt="">
      </div>

    </div>

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
            <div class="swiper-slide">
              <a class="flex flex-col gap-y-5 group" href="{{ route('movies') }}">
                <figure class="relative overflow-hidden">
                  <img class="w-full object-cover aspect-[17/22] transition-transform duration-300 group-hover:scale-110"
                    src="{{ asset('assets/img/str1/samples/game-1-170x220.jpg') }}" alt="Oppenheimer">
                  <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-accent/80 duration-300">
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-7.5 aspect-square bg-gray-900 grid place-items-center text-white scale-90 group-hover:scale-100 transition-all duration-300">
                      <svg class="h-2.5 aspect-square" fill="currentColor">
                        <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#plus-thinest"></use>
                      </svg>
                    </div>
                  </div>
                </figure>
                <div class="leading-tight">
                  <h5 class="font-bold text-gray-900 dark:text-white mb-1.5 group-hover:text-accent">Oppenheimer</h5>
                  <div class="text-gray-500">Biography / Drama</div>
                </div>
              </a>
            </div>
            <div class="swiper-slide">
              <a class="flex flex-col gap-y-5 group" href="{{ route('movies') }}">
                <figure class="relative overflow-hidden">
                  <img class="w-full object-cover aspect-[17/22] transition-transform duration-300 group-hover:scale-110"
                    src="{{ asset('assets/img/str1/samples/game-2-170x220.jpg') }}" alt="Dune: Part Two">
                  <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-accent/80 duration-300">
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-7.5 aspect-square bg-gray-900 grid place-items-center text-white scale-90 group-hover:scale-100 transition-all duration-300">
                      <svg class="h-2.5 aspect-square" fill="currentColor">
                        <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#plus-thinest"></use>
                      </svg>
                    </div>
                  </div>
                </figure>
                <div class="leading-tight">
                  <h5 class="font-bold text-gray-900 dark:text-white mb-1.5 group-hover:text-accent">Dune: Part Two</h5>
                  <div class="text-gray-500">Sci-Fi / Epic</div>
                </div>
              </a>
            </div>
            <div class="swiper-slide">
              <a class="flex flex-col gap-y-5 group" href="{{ route('movies') }}">
                <figure class="relative overflow-hidden">
                  <img class="w-full object-cover aspect-[17/22] transition-transform duration-300 group-hover:scale-110"
                    src="{{ asset('assets/img/str1/samples/game-3-170x220.jpg') }}" alt="Interstellar">
                  <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-accent/80 duration-300">
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-7.5 aspect-square bg-gray-900 grid place-items-center text-white scale-90 group-hover:scale-100 transition-all duration-300">
                      <svg class="h-2.5 aspect-square" fill="currentColor">
                        <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#plus-thinest"></use>
                      </svg>
                    </div>
                  </div>
                </figure>
                <div class="leading-tight">
                  <h5 class="font-bold text-gray-900 dark:text-white mb-1.5 group-hover:text-accent">Interstellar</h5>
                  <div class="text-gray-500">Sci-Fi / Adventure</div>
                </div>
              </a>
            </div>
            <div class="swiper-slide">
              <a class="flex flex-col gap-y-5 group" href="{{ route('movies') }}">
                <figure class="relative overflow-hidden">
                  <img class="w-full object-cover aspect-[17/22] transition-transform duration-300 group-hover:scale-110"
                    src="{{ asset('assets/img/str1/samples/game-4-170x220.jpg') }}" alt="The Godfather">
                  <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-accent/80 duration-300">
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-7.5 aspect-square bg-gray-900 grid place-items-center text-white scale-90 group-hover:scale-100 transition-all duration-300">
                      <svg class="h-2.5 aspect-square" fill="currentColor">
                        <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#plus-thinest"></use>
                      </svg>
                    </div>
                  </div>
                </figure>
                <div class="leading-tight">
                  <h5 class="font-bold text-gray-900 dark:text-white mb-1.5 group-hover:text-accent">The Godfather</h5>
                  <div class="text-gray-500">Crime / Drama</div>
                </div>
              </a>
            </div>
            <div class="swiper-slide">
              <a class="flex flex-col gap-y-5 group" href="{{ route('movies') }}">
                <figure class="relative overflow-hidden">
                  <img class="w-full object-cover aspect-[17/22] transition-transform duration-300 group-hover:scale-110"
                    src="{{ asset('assets/img/str1/samples/game-5-170x220.jpg') }}" alt="Parasite">
                  <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-accent/80 duration-300">
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-7.5 aspect-square bg-gray-900 grid place-items-center text-white scale-90 group-hover:scale-100 transition-all duration-300">
                      <svg class="h-2.5 aspect-square" fill="currentColor">
                        <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#plus-thinest"></use>
                      </svg>
                    </div>
                  </div>
                </figure>
                <div class="leading-tight">
                  <h5 class="font-bold text-gray-900 dark:text-white mb-1.5 group-hover:text-accent">Parasite</h5>
                  <div class="text-gray-500">Thriller / Drama</div>
                </div>
              </a>
            </div>
            <div class="swiper-slide">
              <a class="flex flex-col gap-y-5 group" href="{{ route('movies') }}">
                <figure class="relative overflow-hidden">
                  <img class="w-full object-cover aspect-[17/22] transition-transform duration-300 group-hover:scale-110"
                    src="{{ asset('assets/img/str1/samples/game-6-170x220.jpg') }}" alt="1917">
                  <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-accent/80 duration-300">
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-7.5 aspect-square bg-gray-900 grid place-items-center text-white scale-90 group-hover:scale-100 transition-all duration-300">
                      <svg class="h-2.5 aspect-square" fill="currentColor">
                        <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#plus-thinest"></use>
                      </svg>
                    </div>
                  </div>
                </figure>
                <div class="leading-tight">
                  <h5 class="font-bold text-gray-900 dark:text-white mb-1.5 group-hover:text-accent">1917</h5>
                  <div class="text-gray-500">War / Drama</div>
                </div>
              </a>
            </div>
            <div class="swiper-slide">
              <a class="flex flex-col gap-y-5 group" href="{{ route('movies') }}">
                <figure class="relative overflow-hidden">
                  <img class="w-full object-cover aspect-[17/22] transition-transform duration-300 group-hover:scale-110"
                    src="{{ asset('assets/img/str1/samples/game-7-170x220.jpg') }}" alt="Arrival">
                  <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-all bg-accent/80 duration-300">
                    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-7.5 aspect-square bg-gray-900 grid place-items-center text-white scale-90 group-hover:scale-100 transition-all duration-300">
                      <svg class="h-2.5 aspect-square" fill="currentColor">
                        <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#plus-thinest"></use>
                      </svg>
                    </div>
                  </div>
                </figure>
                <div class="leading-tight">
                  <h5 class="font-bold text-gray-900 dark:text-white mb-1.5 group-hover:text-accent">Arrival</h5>
                  <div class="text-gray-500">Sci-Fi / Mystery</div>
                </div>
              </a>
            </div>
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

    <div class="pb-20 md:pb-24 lg:pb-[130px]">
      <div class="container">

        <div class="mb-10 md:mb-12 lg:mb-16 xl:mb-20">
          <h2
            class="text-2.5xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
            Featured Films
          </h2>
        </div>

        <div class="grid grid-cols-12 gap-5 md:gap-6 lg:gap-7.5 mb-7.5">

          <figure class="col-span-full md:row-start-1 md:row-end-2 lg:col-span-8 lg:row-start-1 lg:row-end-3">
            <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
              href="{{ route('watch') }}">
              <img
                class="aspect-[77/46] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                src="{{ asset('assets/img/str1/samples/video-2-770x460.jpg') }}" alt="Oppenheimer — Official Trailer">

              <div class="absolute inset-0 bg-black/60"></div>

              <span
                class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                <svg role="img" class="fill-white aspect-[18/22] ml-[2px] w-6">
                  <use xlink:href="{{ asset('assets/img/yt1/sprite.svg') }}#play"></use>
                </svg>
              </span>

              <div class="absolute flex inset-x-5 xl:inset-x-6 top-5 xl:top-6 gap-x-3">
                <figure class="shrink-0">
                  <img class="w-10 object-cover aspect-square" src="{{ asset('assets/img/str1/samples/stream-avatar-40x40.jpg') }}" alt="">
                </figure>
                <div class="flex-1 leading-tight min-w-0">
                  <h6 class="font-bold text-white truncate w-full">Oppenheimer — Now Streaming in 4K</h6>
                  <time class="text-gray text-sm" datetime="2025-03-01 21:30">Added March 1st, 2026</time>
                </div>
              </div>
            </a>
          </figure>

          <figure
            class="col-span-full md:row-start-2 md:row-end-2 md:col-span-6 lg:col-span-4 lg:row-start-1 lg:row-end-2">
            <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
              href="{{ route('watch') }}">
              <img
                class="aspect-[370/215] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                src="{{ asset('assets/img/str1/samples/video-4-370x215.jpg') }}" alt="Dune: Part Two">

              <div class="absolute inset-0 bg-black/60"></div>

              <span
                class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                <svg role="img" class="fill-white aspect-[18/22] ml-[2px] w-6">
                  <use xlink:href="{{ asset('assets/img/yt1/sprite.svg') }}#play"></use>
                </svg>
              </span>

              <div class="absolute flex inset-x-5 xl:inset-x-6 top-5 xl:top-6 gap-x-3">
                <figure class="shrink-0">
                  <img class="w-10 object-cover aspect-square" src="{{ asset('assets/img/str1/samples/stream-avatar-40x40.jpg') }}" alt="">
                </figure>
                <div class="flex-1 leading-tight min-w-0">
                  <h6 class="font-bold text-white truncate w-full">Dune: Part Two</h6>
                  <time class="text-gray text-sm" datetime="2025-12-17 21:30">Added December 17th, 2025</time>
                </div>
              </div>
            </a>
          </figure>

          <figure
            class="col-span-full md:row-start-2 md:row-end-2 md:col-span-6 lg:col-span-4 lg:row-start-2 lg:row-end-3">
            <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
              href="{{ route('watch') }}">
              <img
                class="aspect-[370/215] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                src="{{ asset('assets/img/str1/samples/video-8-370x215.jpg') }}" alt="Interstellar — Director's Commentary">

              <div class="absolute inset-0 bg-black/60"></div>

              <span
                class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                <svg role="img" class="fill-white aspect-[18/22] ml-[2px] w-6">
                  <use xlink:href="{{ asset('assets/img/yt1/sprite.svg') }}#play"></use>
                </svg>
              </span>

              <div class="absolute flex inset-x-5 xl:inset-x-6 top-5 xl:top-6 gap-x-3">
                <figure class="shrink-0">
                  <img class="w-10 object-cover aspect-square" src="{{ asset('assets/img/str1/samples/stream-avatar-40x40.jpg') }}" alt="">
                </figure>
                <div class="flex-1 leading-tight min-w-0">
                  <h6 class="font-bold text-white truncate w-full">Interstellar — Director's Commentary</h6>
                  <time class="text-gray text-sm" datetime="2025-10-22 21:30">Added October 22nd, 2025</time>
                </div>
              </div>
            </a>
          </figure>

        </div>

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

              <a href="{{ route('movies') }}" class="block absolute inset-0"></a>
            </div>
          </div>

          <div class="col-span-full md:col-span-6">
            <div
              class="relative isolate w-full aspect-[57/16] flex bg-primary text-white overflow-hidden justify-between items-center">
              <div class="leading-tight pl-5 lg:pl-8 xl:pl-10 xl:pb-2 lg:pb-3 flex-1">
                <div class="font-bold text-base leading-none sm:text-lg md:text-base md:leading-none lg:text-lg mb-1.5 xl:mb-2.5">
                  Unlimited access from</div>
                <h4 class="font-bold text-2xl sm:text-3xl md:text-2xl md:leading-none leading-none lg:text-3xl xl:text-[42px]">
                  $7.99 / month</h4>
              </div>

              <img class="absolute -z-10 right-0 top-0 bottom-0 object-cover object-left max-w-[27%]"
                src="{{ asset('assets/img/str1/stream-banner-2.png') }}" alt="">

              @auth
                <a href="{{ route('account') }}" class="block absolute inset-0"></a>
              @else
                <a href="{{ route('register') }}" class="block absolute inset-0"></a>
              @endauth
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="pb-20 md:pb-24 lg:pb-[120px]">
      <div class="container">
        <div class="mb-10 md:mb-12 lg:mb-16 xl:mb-20">
          <h2
            class="text-2.5xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
            Upcoming Premieres
          </h2>
        </div>

        <div class="divide-y divide-gray-500/20">
          <div class="grid grid-cols-12 gap-x-4 md:gap-x-6 lg:gap-x-7.5 py-11 first:pt-0 last:pb-0">
            <div class="col-span-2 md:col-span-1 lg:col-span-1">
              <time class="flex flex-col text-center text-xs font-bold md:text-base text-white bg-primary"
                datetime="2026-04-08 20:00">
                <span class="text-2xl lg:text-[32px] md:leading-none h-11 lg:h-[62px] flex items-center justify-center">08</span>
                <span class="text-white leading-[28px] bg-primary-500">Apr</span>
              </time>
            </div>
            <div class="col-span-10 md:col-span-6 lg:col-span-6">
              <h4 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-2 lg:mb-2.5">
                <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                  href="{{ route('events') }}">Opening Night: The Last Horizon</a>
              </h4>
              <div class="lg:leading-[26px]">Join us for the exclusive opening night screening of this year's most anticipated thriller. Followed by a live Q&amp;A with the director.</div>
            </div>
            <div class="col-span-full md:col-span-5 md:col-start-8 lg:col-start-8 lg:col-span-5">
              <ul class="flex flex-wrap gap-y-5 pt-7 md:pt-3 lg:pt-3">
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">NY, USA</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#pin"></use>
                    </svg>
                  </div>
                </li>
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">8PM EST</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#clock"></use>
                    </svg>
                  </div>
                </li>
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">$35</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#credit-card"></use>
                    </svg>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="grid grid-cols-12 gap-x-4 md:gap-x-6 lg:gap-x-7.5 py-11 first:pt-0 last:pb-0">
            <div class="col-span-2 md:col-span-1 lg:col-span-1">
              <time class="flex flex-col text-center text-xs font-bold md:text-base text-white bg-primary"
                datetime="2026-04-24 19:00">
                <span class="text-2xl lg:text-[32px] md:leading-none h-11 lg:h-[62px] flex items-center justify-center">24</span>
                <span class="text-white leading-[28px] bg-primary-500">Apr</span>
              </time>
            </div>
            <div class="col-span-10 md:col-span-6 lg:col-span-6">
              <h4 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-2 lg:mb-2.5">
                <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                  href="{{ route('events') }}">Live Director's Commentary: Dune: Part Two</a>
              </h4>
              <div class="lg:leading-[26px]">Denis Villeneuve returns for an exclusive live commentary screening streamed directly on MovieBox. Premium subscribers only.</div>
            </div>
            <div class="col-span-full md:col-span-5 md:col-start-8 lg:col-start-8 lg:col-span-5">
              <ul class="flex flex-wrap gap-y-5 pt-7 md:pt-3 lg:pt-3">
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">Online</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#pin"></use>
                    </svg>
                  </div>
                </li>
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">7PM EST</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#clock"></use>
                    </svg>
                  </div>
                </li>
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">Premium</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#credit-card"></use>
                    </svg>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="grid grid-cols-12 gap-x-4 md:gap-x-6 lg:gap-x-7.5 py-11 first:pt-0 last:pb-0">
            <div class="col-span-2 md:col-span-1 lg:col-span-1">
              <time class="flex flex-col text-center text-xs font-bold md:text-base text-white bg-primary"
                datetime="2026-05-19 18:00">
                <span class="text-2xl lg:text-[32px] md:leading-none h-11 lg:h-[62px] flex items-center justify-center">19</span>
                <span class="text-white leading-[28px] bg-primary-500">May</span>
              </time>
            </div>
            <div class="col-span-10 md:col-span-6 lg:col-span-6">
              <h4 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-2 lg:mb-2.5">
                <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                  href="{{ route('events') }}">Summer Cinema Retrospective</a>
              </h4>
              <div class="lg:leading-[26px]">A curated evening celebrating fifty years of landmark films. Six screenings across two stages, with panel discussions and live scores.</div>
            </div>
            <div class="col-span-full md:col-span-5 md:col-start-8 lg:col-start-8 lg:col-span-5">
              <ul class="flex flex-wrap gap-y-5 pt-7 md:pt-3 lg:pt-3">
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">LA, USA</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#pin"></use>
                    </svg>
                  </div>
                </li>
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">6PM PST</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#clock"></use>
                    </svg>
                  </div>
                </li>
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">$22</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#credit-card"></use>
                    </svg>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="grid grid-cols-12 gap-x-4 md:gap-x-6 lg:gap-x-7.5 py-11 first:pt-0 last:pb-0">
            <div class="col-span-2 md:col-span-1 lg:col-span-1">
              <time class="flex flex-col text-center text-xs font-bold md:text-base text-white bg-primary"
                datetime="2026-07-10 21:00">
                <span class="text-2xl lg:text-[32px] md:leading-none h-11 lg:h-[62px] flex items-center justify-center">10</span>
                <span class="text-white leading-[28px] bg-primary-500">Jul</span>
              </time>
            </div>
            <div class="col-span-10 md:col-span-6 lg:col-span-6">
              <h4 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-2 lg:mb-2.5">
                <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                  href="{{ route('events') }}">Classic Horror Movie Night</a>
              </h4>
              <div class="lg:leading-[26px]">Back-to-back iconic horror films streaming live on MovieBox. Chat with fellow fans in real time and vote for the next title.</div>
            </div>
            <div class="col-span-full md:col-span-5 md:col-start-8 lg:col-start-8 lg:col-span-5">
              <ul class="flex flex-wrap gap-y-5 pt-7 md:pt-3 lg:pt-3">
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">Online</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#pin"></use>
                    </svg>
                  </div>
                </li>
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">9PM EST</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#clock"></use>
                    </svg>
                  </div>
                </li>
                <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                  <div>
                    <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">Free</h5>
                    <span class="text-xs lg:text-sm"></span>
                  </div>
                  <div class="lg:pt-1">
                    <svg role="img" class="h-5 w-5 fill-accent">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#credit-card"></use>
                    </svg>
                  </div>
                </li>
              </ul>
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
@endsection
