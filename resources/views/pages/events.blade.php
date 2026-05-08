@extends('layouts.app')

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        <section>
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
                  datetime="2026-06-14 20:00">
                  <span
                    class="text-2xl lg:text-[32px] md:leading-none h-11 lg:h-[62px] flex items-center justify-center">14</span>
                  <span class="text-white leading-[28px] bg-primary-500">Jun</span>
                </time>
              </div>

              <div class="col-span-10 md:col-span-6 lg:col-span-6">
                <h4 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-2 lg:mb-2.5">
                  <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                    href="#">Opening Night: Dune — Messiah Premiere</a>
                </h4>
                <div class="lg:leading-[26px]">Join us for the world premiere screening of Dune: Messiah, the most anticipated sequel of the decade. Red carpet arrivals, exclusive interviews, and an extended Q&amp;A with the cast and director Denis Villeneuve.</div>
              </div>

              <div class="col-span-full md:col-span-5 md:col-start-8 lg:col-start-8 lg:col-span-5">
                <ul class="flex flex-wrap gap-y-5 pt-7 md:pt-3 lg:pt-3">
                  <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                    <div>
                      <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">Los Angeles</h5>
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
                      <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">8PM PST</h5>
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
                      <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">$45</h5>
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
                  datetime="2026-06-28 19:00">
                  <span
                    class="text-2xl lg:text-[32px] md:leading-none h-11 lg:h-[62px] flex items-center justify-center">28</span>
                  <span class="text-white leading-[28px] bg-primary-500">Jun</span>
                </time>
              </div>

              <div class="col-span-10 md:col-span-6 lg:col-span-6">
                <h4 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-2 lg:mb-2.5">
                  <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                    href="#">Director's Livestream: Behind the Lens with Christopher Nolan</a>
                </h4>
                <div class="lg:leading-[26px]">An exclusive live conversation with Christopher Nolan exploring his creative process, his influences, and the making of his latest project. Members can submit questions in advance — tune in from anywhere in the world.</div>
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
            <div class="grid grid-cols-12 gap-x-4 md:gap-x-6 lg:gap-x-7.5 py-11 first:pt-0 last:pb-0">
              <div class="col-span-2 md:col-span-1 lg:col-span-1">
                <time class="flex flex-col text-center text-xs font-bold md:text-base text-white bg-primary"
                  datetime="2026-07-19 18:00">
                  <span
                    class="text-2xl lg:text-[32px] md:leading-none h-11 lg:h-[62px] flex items-center justify-center">19</span>
                  <span class="text-white leading-[28px] bg-primary-500">Jul</span>
                </time>
              </div>

              <div class="col-span-10 md:col-span-6 lg:col-span-6">
                <h4 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-2 lg:mb-2.5">
                  <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                    href="#">Summer Cinema Retrospective: Sci-Fi Masterworks</a>
                </h4>
                <div class="lg:leading-[26px]">A curated weekend series celebrating the golden age of science fiction cinema. Featuring restored 4K screenings of 2001: A Space Odyssey, Blade Runner, and Alien — introduced by noted film scholars and critics.</div>
              </div>

              <div class="col-span-full md:col-span-5 md:col-start-8 lg:col-start-8 lg:col-span-5">
                <ul class="flex flex-wrap gap-y-5 pt-7 md:pt-3 lg:pt-3">
                  <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                    <div>
                      <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">New York</h5>
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
                      <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">6PM EST</h5>
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
                  datetime="2026-10-31 20:30">
                  <span
                    class="text-2xl lg:text-[32px] md:leading-none h-11 lg:h-[62px] flex items-center justify-center">31</span>
                  <span class="text-white leading-[28px] bg-primary-500">Oct</span>
                </time>
              </div>

              <div class="col-span-10 md:col-span-6 lg:col-span-6">
                <h4 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-2 lg:mb-2.5">
                  <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                    href="#">Classic Horror Night: Halloween Marathon</a>
                </h4>
                <div class="lg:leading-[26px]">Celebrate Halloween with an all-night horror marathon streaming live. From Hitchcock's Psycho to modern masterpieces like Hereditary and The Witch — curated for the brave-hearted. Chat live with fellow fans as you watch.</div>
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
                      <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">8:30PM EST</h5>
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
        </section>

        <div class="h-20 md:h-24 lg:h-28 xl:h-32"></div>

        <section>
          <div class="mb-10 md:mb-12 lg:mb-16 xl:mb-20">
            <h2
              class="text-2.5xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
              Past Screenings
            </h2>
          </div>
          <div class="divide-y divide-gray-500/20">
            <div class="grid grid-cols-12 gap-x-4 md:gap-x-6 lg:gap-x-7.5 py-11 first:pt-0 last:pb-0">
              <div class="col-span-2 md:col-span-1 lg:col-span-1">
                <time class="flex flex-col text-center text-xs font-bold md:text-base text-white bg-gray"
                  datetime="2026-03-15 19:00">
                  <span
                    class="text-2xl lg:text-[32px] md:leading-none h-11 lg:h-[62px] flex items-center justify-center">15</span>
                  <span class="text-white leading-[28px] bg-gray-500">Mar</span>
                </time>
              </div>

              <div class="col-span-10 md:col-span-6 lg:col-span-6">
                <h4 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-2 lg:mb-2.5">
                  <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                    href="#">International Women's Day: Films by Female Directors</a>
                </h4>
                <div class="lg:leading-[26px]">A special curated lineup celebrating cinema from the world's greatest female filmmakers — including Sofia Coppola, Chloé Zhao, and Kathryn Bigelow. Streamed globally with a live panel discussion following each film.</div>
              </div>

              <div class="col-span-full md:col-span-5 md:col-start-8 lg:col-start-8 lg:col-span-5">
                <ul class="flex flex-wrap gap-y-5 pt-7 md:pt-3 lg:pt-3">
                  <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                    <div>
                      <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">Online</h5>
                      <span class="text-xs lg:text-sm"></span>
                    </div>
                    <div class="lg:pt-1">
                      <svg role="img" class="h-5 w-5 fill-gray dark:fill-gray-500">
                        <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#pin"></use>
                      </svg>
                    </div>
                  </li>
                  <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                    <div>
                      <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">7PM GMT</h5>
                      <span class="text-xs lg:text-sm"></span>
                    </div>
                    <div class="lg:pt-1">
                      <svg role="img" class="h-5 w-5 fill-gray dark:fill-gray-500">
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
                      <svg role="img" class="h-5 w-5 fill-gray dark:fill-gray-500">
                        <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#credit-card"></use>
                      </svg>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <div class="grid grid-cols-12 gap-x-4 md:gap-x-6 lg:gap-x-7.5 py-11 first:pt-0 last:pb-0">
              <div class="col-span-2 md:col-span-1 lg:col-span-1">
                <time class="flex flex-col text-center text-xs font-bold md:text-base text-white bg-gray"
                  datetime="2026-01-20 20:00">
                  <span
                    class="text-2xl lg:text-[32px] md:leading-none h-11 lg:h-[62px] flex items-center justify-center">20</span>
                  <span class="text-white leading-[28px] bg-gray-500">Jan</span>
                </time>
              </div>

              <div class="col-span-10 md:col-span-6 lg:col-span-6">
                <h4 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-2 lg:mb-2.5">
                  <a class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent"
                    href="#">Awards Season Preview: Best Picture Contenders</a>
                </h4>
                <div class="lg:leading-[26px]">An advance look at the films generating Oscar buzz — featuring exclusive first-look clips, critic roundtables, and early access to three contenders before their wide release on MovieBox.</div>
              </div>

              <div class="col-span-full md:col-span-5 md:col-start-8 lg:col-start-8 lg:col-span-5">
                <ul class="flex flex-wrap gap-y-5 pt-7 md:pt-3 lg:pt-3">
                  <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                    <div>
                      <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">Online</h5>
                      <span class="text-xs lg:text-sm"></span>
                    </div>
                    <div class="lg:pt-1">
                      <svg role="img" class="h-5 w-5 fill-gray dark:fill-gray-500">
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
                      <svg role="img" class="h-5 w-5 fill-gray dark:fill-gray-500">
                        <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#clock"></use>
                      </svg>
                    </div>
                  </li>
                  <li class="flex flex-1 flex-col items-center gap-y-3.5 text-center">
                    <div>
                      <h5 class="font-bold tracking-tight text-gray-900 dark:text-white lg:text-lg lg:leading-tight">$12</h5>
                      <span class="text-xs lg:text-sm"></span>
                    </div>
                    <div class="lg:pt-1">
                      <svg role="img" class="h-5 w-5 fill-gray dark:fill-gray-500">
                        <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#credit-card"></use>
                      </svg>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </section>

      </div>
    </div>

  </main>
@endsection
