@extends('layouts.app')

@section('title', 'About')

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        <div class="grid grid-cols-12 gap-x-5 md:gap-x-6 lg:gap-x-7.5 gap-y-16">

          <div class="col-span-full md:col-span-8 md:pr-[38px] lg:pr-[50px] xl:pr-[70px]">

            <div class="mb-12 md:mb-16 lg:mb-20 xl:mb-24">
              <h3
                class="text-gray-900 dark:text-white text-xl sm:text-2xl md:text-3xl lg:text-[32px] font-bold mb-8 md:mb-12 lg:mb-14 xl:mb-16">
                Subscription Plans
              </h3>

              <div class="flex flex-col gap-y-12">
                <div
                  class="grid grid-cols-[70px_1fr] grid-rows-2 lg:grid-rows-1 lg:auto-rows-auto lg:grid-cols-[70px_1fr_180px] gap-x-4 lg:gap-x-6 xl:gap-x-7.5 gap-y-6">
                  <div class="flex items-center justify-center">
                    <svg class="aspect-square w-11 h-12.5 fill-accent" fill="currentColor">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#gamer-mouse"></use>
                    </svg>
                  </div>
                  <div class="lg:pt-1">
                    <h4 class="lg:leading-none text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">
                      <a class="text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors"
                        href="#">Basic — $7.99/mo</a>
                    </h4>
                    <div class="font-bold text-gray-500">HD on 1 device · 10,000+ titles</div>
                  </div>
                  <div class="col-span-full lg:col-auto">
                    <a href="{{ route('register') }}"
                      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-500/40 ring-1 ring-inset ring-gray-500/40 bg-transparent hover:bg-accent hover:text-gray-900 hover:ring-0 w-full">
                      Start Free Trial
                    </a>
                  </div>
                </div>
                <div
                  class="grid grid-cols-[70px_1fr] grid-rows-2 lg:grid-rows-1 lg:auto-rows-auto lg:grid-cols-[70px_1fr_180px] gap-x-4 lg:gap-x-6 xl:gap-x-7.5 gap-y-6">
                  <div class="flex items-center justify-center">
                    <svg class="aspect-square w-11 h-12.5 fill-accent" fill="currentColor">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#headset"></use>
                    </svg>
                  </div>
                  <div class="lg:pt-1">
                    <h4 class="lg:leading-none text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">
                      <a class="text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors"
                        href="#">Standard — $13.99/mo</a>
                    </h4>
                    <div class="font-bold text-gray-500">Full HD · 2 devices · Dolby Audio</div>
                  </div>
                  <div class="col-span-full lg:col-auto">
                    <a href="{{ route('register') }}"
                      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-500/40 ring-1 ring-inset ring-gray-500/40 bg-transparent hover:bg-accent hover:text-gray-900 hover:ring-0 w-full">
                      Start Free Trial
                    </a>
                  </div>
                </div>
                <div
                  class="grid grid-cols-[70px_1fr] grid-rows-2 lg:grid-rows-1 lg:auto-rows-auto lg:grid-cols-[70px_1fr_180px] gap-x-4 lg:gap-x-6 xl:gap-x-7.5 gap-y-6">
                  <div class="flex items-center justify-center">
                    <svg class="aspect-square w-11 h-12.5 fill-accent" fill="currentColor">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#keyboard"></use>
                    </svg>
                  </div>
                  <div class="lg:pt-1">
                    <h4 class="lg:leading-none text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">
                      <a class="text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors"
                        href="#">Premium — $17.99/mo</a>
                    </h4>
                    <div class="font-bold text-gray-500">4K HDR · 4 devices · Offline Downloads</div>
                  </div>
                  <div class="col-span-full lg:col-auto">
                    <a href="{{ route('register') }}"
                      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-500/40 ring-1 ring-inset ring-gray-500/40 bg-transparent hover:bg-accent hover:text-gray-900 hover:ring-0 w-full">
                      Start Free Trial
                    </a>
                  </div>
                </div>
                <div
                  class="grid grid-cols-[70px_1fr] grid-rows-2 lg:grid-rows-1 lg:auto-rows-auto lg:grid-cols-[70px_1fr_180px] gap-x-4 lg:gap-x-6 xl:gap-x-7.5 gap-y-6">
                  <div class="flex items-center justify-center">
                    <svg class="aspect-square w-11 h-12.5 fill-accent" fill="currentColor">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#screen"></use>
                    </svg>
                  </div>
                  <div class="lg:pt-1">
                    <h4 class="lg:leading-none text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">
                      <a class="text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors"
                        href="#">4K Ultra — $22.99/mo</a>
                    </h4>
                    <div class="font-bold text-gray-500">4K + Dolby Atmos · 6 devices · Early Access</div>
                  </div>
                  <div class="col-span-full lg:col-auto">
                    <a href="{{ route('register') }}"
                      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-500/40 ring-1 ring-inset ring-gray-500/40 bg-transparent hover:bg-accent hover:text-gray-900 hover:ring-0 w-full">
                      Start Free Trial
                    </a>
                  </div>
                </div>
                <div
                  class="grid grid-cols-[70px_1fr] grid-rows-2 lg:grid-rows-1 lg:auto-rows-auto lg:grid-cols-[70px_1fr_180px] gap-x-4 lg:gap-x-6 xl:gap-x-7.5 gap-y-6">
                  <div class="flex items-center justify-center">
                    <svg class="aspect-square w-11 h-12.5 fill-accent" fill="currentColor">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#gaming-chair"></use>
                    </svg>
                  </div>
                  <div class="lg:pt-1">
                    <h4 class="lg:leading-none text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">
                      <a class="text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors"
                        href="#">Family — $19.99/mo</a>
                    </h4>
                    <div class="font-bold text-gray-500">Full HD · Up to 6 profiles · Parental controls</div>
                  </div>
                  <div class="col-span-full lg:col-auto">
                    <a href="{{ route('register') }}"
                      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-500/40 ring-1 ring-inset ring-gray-500/40 bg-transparent hover:bg-accent hover:text-gray-900 hover:ring-0 w-full">
                      Start Free Trial
                    </a>
                  </div>
                </div>
                <div
                  class="grid grid-cols-[70px_1fr] grid-rows-2 lg:grid-rows-1 lg:auto-rows-auto lg:grid-cols-[70px_1fr_180px] gap-x-4 lg:gap-x-6 xl:gap-x-7.5 gap-y-6">
                  <div class="flex items-center justify-center">
                    <svg class="aspect-square w-11 h-12.5 fill-accent" fill="currentColor">
                      <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#mousepad-mouse"></use>
                    </svg>
                  </div>
                  <div class="lg:pt-1">
                    <h4 class="lg:leading-none text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">
                      <a class="text-gray-900 dark:text-white hover:text-accent dark:hover:text-accent transition-colors"
                        href="#">Student — $6.99/mo</a>
                    </h4>
                    <div class="font-bold text-gray-500">HD · 1 device · Valid .edu email required</div>
                  </div>
                  <div class="col-span-full lg:col-auto">
                    <a href="{{ route('register') }}"
                      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-500/40 ring-1 ring-inset ring-gray-500/40 bg-transparent hover:bg-accent hover:text-gray-900 hover:ring-0 w-full">
                      Start Free Trial
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <h3
                class="text-gray-900 dark:text-white text-xl sm:text-2xl md:text-3xl lg:text-[32px] font-bold mb-8 md:mb-12 lg:mb-14 xl:mb-16">
                Frequently Asked Questions
              </h3>
              <div class="accordion flex flex-col gap-y-6 lg:gap-y-8 xl:gap-y-11">

                <div class="accordion__item group">
                  <div
                    class="accordion__item-trigger relative flex flex-wrap items-center gap-3 lg:gap-4 text-gray-900 dark:text-white lg:text-2xl font-bold cursor-pointer">
                    <span class="pr-8 md:pr-0">How many movies and shows are on MovieBox?</span>
                    <span
                      class="absolute right-0 top-0 w-5 aspect-square border-2 border-gray-650 group-[.active]:border-accent md:relative before:w-2 before:h-0.5 before:absolute before:left-1/2 before:top-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:bg-gray-650 after:w-0.5 after:h-2 after:visible after:absolute after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:bg-gray-650 group-[.active]:after:invisible group-[.active]:before:bg-gray-900 dark:group-[.active]:before:bg-white"></span>
                  </div>
                  <div class="accordion__item-content hidden group-[.active]:block animate-fade">
                    <div class="pt-6 lg:pt-8 xl:pt-10">
                      MovieBox currently offers over 10,000 films and series spanning every genre — from Hollywood blockbusters and indie darlings to international cinema and classic collections. New titles are added every week, and our library grows by hundreds of films each month.
                    </div>
                  </div>
                </div>
                <div class="accordion__item group active">
                  <div
                    class="accordion__item-trigger relative flex flex-wrap items-center gap-3 lg:gap-4 text-gray-900 dark:text-white lg:text-2xl font-bold cursor-pointer">
                    <span class="pr-8 md:pr-0">Can I download movies to watch offline?</span>
                    <span
                      class="absolute right-0 top-0 w-5 aspect-square border-2 border-gray-650 group-[.active]:border-accent md:relative before:w-2 before:h-0.5 before:absolute before:left-1/2 before:top-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:bg-gray-650 after:w-0.5 after:h-2 after:visible after:absolute after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:bg-gray-650 group-[.active]:after:invisible group-[.active]:before:bg-gray-900 dark:group-[.active]:before:bg-white"></span>
                  </div>
                  <div class="accordion__item-content hidden group-[.active]:block animate-fade">
                    <div class="pt-6 lg:pt-8 xl:pt-10">
                      Yes! Premium and 4K Ultra subscribers can download up to 25 titles at a time to watch without an internet connection. Downloads are available on our iOS and Android apps and are stored for up to 30 days.
                    </div>
                  </div>
                </div>
                <div class="accordion__item group">
                  <div
                    class="accordion__item-trigger relative flex flex-wrap items-center gap-3 lg:gap-4 text-gray-900 dark:text-white lg:text-2xl font-bold cursor-pointer">
                    <span class="pr-8 md:pr-0">What streaming quality does MovieBox support?</span>
                    <span
                      class="absolute right-0 top-0 w-5 aspect-square border-2 border-gray-650 group-[.active]:border-accent md:relative before:w-2 before:h-0.5 before:absolute before:left-1/2 before:top-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:bg-gray-650 after:w-0.5 after:h-2 after:visible after:absolute after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:bg-gray-650 group-[.active]:after:invisible group-[.active]:before:bg-gray-900 dark:group-[.active]:before:bg-white"></span>
                  </div>
                  <div class="accordion__item-content hidden group-[.active]:block animate-fade">
                    <div class="pt-6 lg:pt-8 xl:pt-10">
                      MovieBox streams in SD, HD, Full HD, and 4K HDR depending on your plan and device. Our 4K Ultra and Premium plans support Dolby Vision and Dolby Atmos where available. We automatically adjust quality based on your connection speed.
                    </div>
                  </div>
                </div>
                <div class="accordion__item group">
                  <div
                    class="accordion__item-trigger relative flex flex-wrap items-center gap-3 lg:gap-4 text-gray-900 dark:text-white lg:text-2xl font-bold cursor-pointer">
                    <span class="pr-8 md:pr-0">How do I request a film to be added?</span>
                    <span
                      class="absolute right-0 top-0 w-5 aspect-square border-2 border-gray-650 group-[.active]:border-accent md:relative before:w-2 before:h-0.5 before:absolute before:left-1/2 before:top-1/2 before:-translate-x-1/2 before:-translate-y-1/2 before:bg-gray-650 after:w-0.5 after:h-2 after:visible after:absolute after:left-1/2 after:top-1/2 after:-translate-x-1/2 after:-translate-y-1/2 after:bg-gray-650 group-[.active]:after:invisible group-[.active]:before:bg-gray-900 dark:group-[.active]:before:bg-white"></span>
                  </div>
                  <div class="accordion__item-content hidden group-[.active]:block animate-fade">
                    <div class="pt-6 lg:pt-8 xl:pt-10">
                      Use the <a href="{{ route('contact') }}" class="text-accent hover:underline">Contact page</a> to submit a film request. Our curation team reviews all requests weekly. Titles with the most member demand are prioritised for licensing negotiations.
                    </div>
                  </div>
                </div>

              </div>
            </div>

          </div>

          <div class="col-span-full md:col-span-4">

            <div class="mb-12 md:mb-16 lg:mb-20 xl:mb-24">
              <h3
                class="text-gray-900 dark:text-white text-xl sm:text-2xl md:text-3xl lg:text-[32px] font-bold mb-8 md:mb-12 lg:mb-14 xl:mb-16">
                About MovieBox
              </h3>
              <div class="mb-6 lg:mb-10">
                MovieBox is your home for world-class cinema. Founded in 2019, we curate the finest films from Hollywood and beyond — bringing you timeless classics, award-winning dramas, pulse-pounding thrillers, and bold international stories, all in one place.
              </div>
              <dl class="mb-8 flex flex-col gap-y-3.5 font-medium md:mb-10 lg:mb-12">
                <div class="flex flex-wrap justify-between">
                  <dt class="text-gray-500 opacity-60">Founded</dt>
                  <dd class="[&_a]:text-accent [&_a]:hover:text-gray-900 [&_a]:dark:hover:text-white">2019, Los Angeles</dd>
                </div>
                <div class="flex flex-wrap justify-between">
                  <dt class="text-gray-500 opacity-60">Available On</dt>
                  <dd class="[&_a]:text-accent [&_a]:hover:text-gray-900 [&_a]:dark:hover:text-white">All Devices</dd>
                </div>
                <div class="flex flex-wrap justify-between">
                  <dt class="text-gray-500 opacity-60">Content Rating</dt>
                  <dd class="[&_a]:text-accent [&_a]:hover:text-gray-900 [&_a]:dark:hover:text-white">All Ages</dd>
                </div>
                <div class="flex flex-wrap justify-between">
                  <dt class="text-gray-500 opacity-60">Top Genre</dt>
                  <dd class="[&_a]:text-accent [&_a]:hover:text-gray-900 [&_a]:dark:hover:text-white">Sci-Fi &amp; Drama</dd>
                </div>
                <div class="flex flex-wrap justify-between">
                  <dt class="text-gray-500 opacity-60">Library</dt>
                  <dd class="[&_a]:text-accent [&_a]:hover:text-gray-900 [&_a]:dark:hover:text-white">10,000+ Titles</dd>
                </div>
                <div class="flex flex-wrap justify-between">
                  <dt class="text-gray-500 opacity-60">New Titles</dt>
                  <dd class="[&_a]:text-accent [&_a]:hover:text-gray-900 [&_a]:dark:hover:text-white">Added weekly</dd>
                </div>
                <div class="flex flex-wrap justify-between">
                  <dt class="text-gray-500 opacity-60">Max Quality</dt>
                  <dd class="[&_a]:text-accent [&_a]:hover:text-gray-900 [&_a]:dark:hover:text-white">4K Dolby Vision</dd>
                </div>
              </dl>
              <a href="{{ route('register') }}"
                class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 w-full">
                Start Free Trial
              </a>
            </div>

            <div>
              <h3
                class="text-gray-900 dark:text-white text-xl sm:text-2xl md:text-3xl lg:text-[32px] font-bold mb-8 md:mb-12 lg:mb-14 xl:mb-16">
                Community Guidelines
              </h3>
              <ol class="list-ol">
                <li>Be respectful in reviews and comments</li>
                <li>No spoilers in public discussions</li>
                <li>Do not share account credentials</li>
                <li>Report inappropriate or illegal content</li>
                <li>No screen recording or redistribution</li>
                <li>One account per subscription plan</li>
                <li>Happy watching!</li>
              </ol>
            </div>

          </div>

        </div>

      </div>
    </div>

  </main>
@endsection
