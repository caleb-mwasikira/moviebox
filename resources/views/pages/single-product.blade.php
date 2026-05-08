@extends('layouts.app')

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        <div class="grid grid-cols-12 gap-x-5 md:gap-x-6 lg:gap-x-7.5 gap-y-16">

          <div class="col-span-full md:col-span-6 md:pr-7 lg:pr-[70px]">
            <div class="sticky top-8">
              <div class="swiper js-vv-product-swiper mb-9 aspect-[50/63] w-full">
                <div class="swiper-wrapper">
                  <div class="swiper-slide">
                    <img class="h-full w-full object-cover" src="{{ asset('assets/img/str1/samples/product-1-500x630-1.jpg') }}" alt="Dune: Part One Movie Poster">
                  </div>
                  <div class="swiper-slide">
                    <img class="h-full w-full object-cover" src="{{ asset('assets/img/str1/samples/product-1-500x630-2.jpg') }}" alt="Dune: Part One Movie Poster">
                  </div>
                  <div class="swiper-slide">
                    <img class="h-full w-full object-cover" src="{{ asset('assets/img/str1/samples/product-1-500x630-3.jpg') }}" alt="Dune: Part One Movie Poster">
                  </div>
                  <div class="swiper-slide">
                    <img class="h-full w-full object-cover" src="{{ asset('assets/img/str1/samples/product-1-500x630-4.jpg') }}" alt="Dune: Part One Movie Poster">
                  </div>
                </div>
              </div>

              <div class="swiper js-vv-product-swiper-nav w-full">
                <div class="swiper-wrapper">
                  <div class="swiper-slide">
                    <img class="aspect-square w-full cursor-pointer object-cover opacity-100 transition-opacity duration-300 hover:opacity-20" src="{{ asset('assets/img/str1/samples/product-1-98x98-1.jpg') }}" alt="">
                  </div>
                  <div class="swiper-slide">
                    <img class="aspect-square w-full cursor-pointer object-cover opacity-100 transition-opacity duration-300 hover:opacity-20" src="{{ asset('assets/img/str1/samples/product-1-98x98-2.jpg') }}" alt="">
                  </div>
                  <div class="swiper-slide">
                    <img class="aspect-square w-full cursor-pointer object-cover opacity-100 transition-opacity duration-300 hover:opacity-20" src="{{ asset('assets/img/str1/samples/product-1-98x98-3.jpg') }}" alt="">
                  </div>
                  <div class="swiper-slide">
                    <img class="aspect-square w-full cursor-pointer object-cover opacity-100 transition-opacity duration-300 hover:opacity-20" src="{{ asset('assets/img/str1/samples/product-1-98x98-4.jpg') }}" alt="">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full md:col-span-6 lg:-ml-7.5">

            <div class="mb-16 md:mb-20 lg:mb-24">
              <div class="flex justify-between items-baseline mb-5 lg:mb-8">
                <h2 class="text-2xl font-bold text-gray-900 md:text-2xl lg:text-4xl dark:text-white lg:leading-none">
                  Dune: Part One Movie Poster
                </h2>
                <div class="text-2xl font-bold text-accent md:text-2xl lg:text-[32px] lg:leading-none">
                  $36.00
                </div>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none mb-6 md:mb-12">
                <div class="relative inline-flex leading-none gap-[5px]">
                  <svg role="img" class="h-3.5 w-3.5 fill-gray-200 dark:fill-white/20">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                  </svg>
                  <svg role="img" class="h-3.5 w-3.5 fill-gray-200 dark:fill-white/20">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                  </svg>
                  <svg role="img" class="h-3.5 w-3.5 fill-gray-200 dark:fill-white/20">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                  </svg>
                  <svg role="img" class="h-3.5 w-3.5 fill-gray-200 dark:fill-white/20">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                  </svg>
                  <svg role="img" class="h-3.5 w-3.5 fill-gray-200 dark:fill-white/20">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                  </svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-[5px]">
                  <svg role="img" class="h-3.5 w-3.5 fill-accent">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                  </svg>
                  <svg role="img" class="h-3.5 w-3.5 fill-accent">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                  </svg>
                  <svg role="img" class="h-3.5 w-3.5 fill-accent">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                  </svg>
                  <svg role="img" class="h-3.5 w-3.5 fill-accent">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                  </svg>
                  <svg role="img" class="h-3.5 w-3.5 fill-accent">
                    <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                  </svg>
                </div>
                <div class="text-gray-500/60 uppercase font-bold text-sm leading-none mt-[1px]">
                  5.0 out of 4 reviews
                </div>
              </div>
              <div class="mb-6 lg:mb-10">
                The official theatrical poster for Denis Villeneuve's epic adaptation of Frank Herbert's Dune — museum-quality giclée print on heavyweight archival paper. A statement piece for any cinema lover's wall, capturing the sweeping grandeur of Arrakis.
              </div>
              <div class="grid grid-cols-2 gap-7.5 mb-9 lg:mb-14 xl:pr-12.5">
                <div class="basis-1/2">
                  <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="product-size">Print Size
                  </label>
                  <select class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900" name="product-size" id="product-size">
                    <option value="sm">18x26 Inches</option>
                    <option value="md">24x32 Inches</option>
                    <option value="lg">36x52 Inches</option>
                  </select>
                </div>
                <div class="basis-1/2">
                  <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="product-finish">Paper Finish
                  </label>
                  <select class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900" name="product-finish" id="product-finish">
                    <option value="matte">Matte</option>
                    <option value="gloss">Gloss</option>
                    <option value="satin">Satin</option>
                  </select>
                </div>
                <div class="basis-1/2">
                  <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="product-quantity">Quantity
                  </label>
                  <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-900 w-full" type="number" name="product-quantity" id="product-quantity" value="1">
                </div>
              </div>
              <div class="flex mb-10 lg:mb-20">
                <button type="submit" class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 basis-full md:basis-1/2 lg:basis-1/3">
                  + Add to the bag
                </button>
              </div>
              <div class="flex gap-4 text-sm leading-none">
                <span class="uppercase font-bold text-gray-500">Share:</span>
                <a href="#" class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent">
                  <svg class="w-3 h-3" fill="currentColor">
                    <use xlink:href="{{ asset('assets/img/social-icons.svg') }}#facebook"></use>
                  </svg>
                </a>
                <a href="#" class="text-gray-900 dark:text-white transition-colors hover:text-accent dark:hover:text-accent">
                  <svg class="w-3 h-3" fill="currentColor">
                    <use xlink:href="{{ asset('assets/img/social-icons.svg') }}#twitter"></use>
                  </svg>
                </a>
              </div>
            </div>

            <div class="tabs">

              <nav class="tabs__header pb-4 font-bold leading-tight tracking-tight text-xl lg:text-2xl xl:text-[32px]">
                <ul class="flex flex-col flex-wrap justify-between gap-y-3 sm:flex-row">
                  <li class="tabs__header-item is-active group">
                    <a href="#" class="text-gray-500/40 transition-colors hover:text-gray-900 group-[.is-active]:text-gray-900 dark:group-[.is-active]:text-white dark:hover:text-white before:content-['.'] before:mr-[0.15em] group-[.is-active]:before:text-accent">Description</a>
                  </li>
                  <li class="tabs__header-item group">
                    <a href="#" class="text-gray-500/40 transition-colors hover:text-gray-900 group-[.is-active]:text-gray-900 dark:group-[.is-active]:text-white dark:hover:text-white before:content-['.'] before:mr-[0.15em] group-[.is-active]:before:text-accent">Specifications</a>
                  </li>
                  <li class="tabs__header-item group">
                    <a href="#" class="text-gray-500/40 transition-colors hover:text-gray-900 group-[.is-active]:text-gray-900 dark:group-[.is-active]:text-white dark:hover:text-white before:content-['.'] before:mr-[0.15em] group-[.is-active]:before:text-accent">Reviews (4)</a>
                  </li>
                </ul>
              </nav>

              <div class="tabs__content vv-prose pt-6 pb-0 md:pt-9 lg:pt-10 xl:pt-12">

                <div class="tabs__content-item">
                  <h3 class="mt-0 lg:mb-16">Museum-Quality Prints</h3>
                  <p class="lead">Each poster is produced on heavyweight 200gsm archival paper using professional giclée printing — vivid colours, deep blacks, and precise detail that do justice to the original theatrical artwork.</p>
                  <p>Printed to order and shipped in a sturdy protective tube, every poster arrives ready to frame. We work directly with official studios and distributors to ensure all artwork is fully licensed. Whether you're decorating a home cinema or gifting a film lover, these prints are built to last.</p>
                  <figure class="xl:mb-16">
                    <img class="h-auto w-full" src="{{ asset('assets/img/str1/samples/product-content-img-1.jpg') }}" alt="Poster detail shot">
                  </figure>
                  <p>All posters ship within 3–5 business days. Free shipping on orders over $50. Returns accepted within 30 days if the print arrives damaged.</p>
                </div>

                <div class="tabs__content-item">
                  <dl class="grid grid-cols-8 gap-x-5 leading-normal md:gap-x-6 lg:gap-x-7.5">
                    <dt class="col-span-full text-gray-500 dark:text-gray sm:col-span-3">
                      Print Size
                    </dt>
                    <dd class="col-span-full mb-4 font-bold text-gray-900 dark:text-white sm:col-span-5 md:mb-7">
                      18×26 Inches (Standard)
                    </dd>
                    <dt class="col-span-full text-gray-500 dark:text-gray sm:col-span-3">
                      Paper Weight
                    </dt>
                    <dd class="col-span-full mb-4 font-bold text-gray-900 dark:text-white sm:col-span-5 md:mb-7">
                      200gsm Archival Art Paper
                    </dd>
                    <dt class="col-span-full text-gray-500 dark:text-gray sm:col-span-3">
                      Print Finish
                    </dt>
                    <dd class="col-span-full mb-4 font-bold text-gray-900 dark:text-white sm:col-span-5 md:mb-7">
                      Matte
                    </dd>
                    <dt class="col-span-full text-gray-500 dark:text-gray sm:col-span-3">
                      Printing Method
                    </dt>
                    <dd class="col-span-full mb-4 font-bold text-gray-900 dark:text-white sm:col-span-5 md:mb-7">
                      Giclée (archival pigment inks)
                    </dd>
                    <dt class="col-span-full text-gray-500 dark:text-gray sm:col-span-3">
                      SKU
                    </dt>
                    <dd class="col-span-full mb-4 font-bold text-gray-900 dark:text-white sm:col-span-5 md:mb-7">
                      MB-DUNE1-P-18
                    </dd>
                  </dl>
                </div>

                <div class="tabs__content-item">

                  <ol class="flex flex-col gap-y-12 lg:gap-y-12 pb-5 my-0">
                    <li class="block">
                      <div class="mb-4 lg:mb-8">
                        <div class="flex items-center gap-x-4 relative text-sm leading-none mb-4">
                          <div class="relative inline-flex leading-none gap-1">
                            <svg role="img" class="h-[13px] w-[13px] fill-gray-200 dark:fill-white/20">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-gray-200 dark:fill-white/20">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-gray-200 dark:fill-white/20">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-gray-200 dark:fill-white/20">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-gray-200 dark:fill-white/20">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                          </div>
                          <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                            <svg role="img" class="h-[13px] w-[13px] fill-accent">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-accent">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-accent">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-accent">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-accent">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                          </div>
                        </div>
                        <h5 class="text-gray-900 dark:text-white text-xl lg:text-2xl">
                          Stunning — the colours are incredible.
                        </h5>
                        <div class="text-gray-500 text-sm uppercase font-bold pt-0.5">by Sarah Okonkwo</div>
                      </div>
                      <div class="mb-3 lg:mb-5">
                        Hung this in my home cinema room and it's an absolute showstopper. The matte finish really brings out the desert tones and the sand detail is razor sharp. Arrived well-packaged with no creases — will definitely order more.
                      </div>
                      <div class="text-sm font-bold uppercase text-gray-900 dark:text-white">
                        <time datetime="2026-03-14">March 14th, 2026</time>
                      </div>
                    </li>
                    <li class="block">
                      <div class="mb-4 lg:mb-8">
                        <div class="flex items-center gap-x-4 relative text-sm leading-none mb-4">
                          <div class="relative inline-flex leading-none gap-1">
                            <svg role="img" class="h-[13px] w-[13px] fill-gray-200 dark:fill-white/20">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-gray-200 dark:fill-white/20">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-gray-200 dark:fill-white/20">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-gray-200 dark:fill-white/20">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-gray-200 dark:fill-white/20">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                          </div>
                          <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                            <svg role="img" class="h-[13px] w-[13px] fill-accent">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-accent">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-accent">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-accent">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                            <svg role="img" class="h-[13px] w-[13px] fill-accent">
                              <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use>
                            </svg>
                          </div>
                        </div>
                        <h5 class="text-gray-900 dark:text-white text-xl lg:text-2xl">
                          Perfect gift for a Dune superfan.
                        </h5>
                        <div class="text-gray-500 text-sm uppercase font-bold pt-0.5">by James Hartley</div>
                      </div>
                      <div class="mb-3 lg:mb-5">
                        Bought the 36×52 for my brother's birthday. He was completely speechless. The archival paper feels premium and the print is just gorgeous. MovieBox Shop is my go-to for cinema prints from now on.
                      </div>
                      <div class="text-sm font-bold uppercase text-gray-900 dark:text-white">
                        <time datetime="2026-02-28">February 28th, 2026</time>
                      </div>
                    </li>
                  </ol>

                  <div class="">
                    <div class="mb-10 md:mb-12 lg:mb-16 xl:mb-[72px]">
                      <h3 class="text-xl font-bold tracking-tight text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-1.5xl md:text-2xl lg:leading-none lg:text-[32px]">
                        Add Review
                      </h3>
                    </div>

                    <form class="grid grid-cols-1 md:grid-cols-2 gap-7" action="#">
                      <div class="col-span-full lg:col-span-1">
                        <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="review-rating">Review Stars
                        </label>
                        <select class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900" name="review-rating" id="review-rating">
                          <option value="-">Select Rating</option>
                          <option value="1">Really Bad</option>
                          <option value="2">Bad</option>
                          <option value="3">Not Bad</option>
                          <option value="4">Good</option>
                          <option value="5">Excellent</option>
                        </select>
                      </div>
                      <div class="col-span-full lg:col-span-1">
                        <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="review-title">Review Title
                        </label>
                        <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-900 w-full" type="text" name="review-title" id="review-title" value="">
                      </div>
                      <div class="col-span-full lg:col-span-1">
                        <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="review-name">Your Name
                        </label>
                        <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-900 w-full" type="text" name="review-name" id="review-name" value="">
                      </div>
                      <div class="col-span-full lg:col-span-1">
                        <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="review-email">Your Email
                        </label>
                        <input class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-900 w-full" type="email" name="review-email" id="review-email" value="">
                      </div>
                      <div class="col-span-full">
                        <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="review-comment">Your Review
                        </label>
                        <textarea class="block border-base bg-white/40 w-full px-4 py-2 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-900 h-32" name="review-comment" id="review-comment" placeholder="Tell us what you thought of this print..."></textarea>
                      </div>
                      <div class="col-span-full md:col-start-2 md:col-span-1 xl:pl-[29%]">
                        <button type="submit" class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 w-full">
                          Post Review
                        </button>
                      </div>
                    </form>
                  </div>

                </div>

              </div>

            </div>

          </div>

        </div>

      </div>
    </div>

  </main>
@endsection
