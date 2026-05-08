@extends('layouts.app')

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        <div class="flex gap-x-8 justify-between items-center mb-12 md:mb-16 lg:mb-20 xl:mb-24">

          <nav>
            <ul class="flex flex-wrap gap-x-4 lg:gap-x-9 text-base lg:text-lg font-bold">
              <li>
                <a class="text-gray-900 text-accent dark:text-accent transition-colors" href="#">All Merchandise</a>
              </li>
              <li>
                <a class="text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent transition-colors"
                  href="#">Movie Posters</a>
              </li>
              <li>
                <a class="text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent transition-colors"
                  href="#">Apparel</a>
              </li>
              <li>
                <a class="text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent transition-colors"
                  href="#">Collectibles</a>
              </li>
              <li>
                <a class="text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent transition-colors"
                  href="#">Accessories</a>
              </li>
            </ul>
          </nav>

          <div class="shrink-0 flex gap-4 sm:gap-8 items-center relative">

            <button class="js-vv-streams-filter-toggle group peer">
              <svg role="img"
                class="w-5 sm:w-7.5 aspect-square fill-gray-500 group-hover:fill-gray-900 transition-colors dark:fill-gray-650 dark:group-hover:fill-white">
                <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#filter"></use>
              </svg>
            </button>

            <div
              class="absolute w-72 sm:w-80 opacity-0 peer-[.active]:opacity-100 transition-all invisible peer-[.active]:visible -translate-y-3 peer-[.active]:translate-y-0 flex flex-col gap-8 bg-white dark:bg-gray-700 p-6 sm:p-7.5 z-10 right-0 top-14 shadow-3xl">
              <div>
                <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5"
                  for="filter-by">Filter By
                </label>
                <select
                  class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
                  name="filter-by" id="filter-by">
                  <option value="newest-items">Newest Items</option>
                  <option value="older-items">Older Items</option>
                  <option value="popular">Popular</option>
                  <option value="most-liked">Most Liked</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="order-by">Order
                  By
                </label>
                <select
                  class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
                  name="order-by" id="order-by">
                  <option value="descending">Descending</option>
                  <option value="ascending">Ascending</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="rating">Rating
                </label>
                <select
                  class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
                  name="rating" id="rating">
                  <option value="5">5 Stars</option>
                  <option value="4">4+ Stars</option>
                  <option value="3">3+ Stars</option>
                  <option value="2">2+ Stars</option>
                  <option value="1">1+ Star</option>
                </select>
              </div>
              <div class="pt-1">
                <button type="submit"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 w-full">
                  Apply Filters
                </button>
              </div>
            </div>

          </div>

        </div>

        <div class="grid grid-cols-12 gap-x-6 gap-y-9 md:gap-x-6 lg:gap-x-7.5">

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-1-270x340.jpg') }}" alt="Dune: Part Two Official Poster">
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">Dune: Part Two Official Poster</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$36.00</ins>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-2-270x340.jpg') }}" alt="Oppenheimer Art Print">
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">Oppenheimer Art Print</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$32.00</ins>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-3-270x340.jpg') }}" alt="Interstellar Collector's Poster">
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">Interstellar Collector's Poster</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$36.00</ins>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-4-270x340.jpg') }}" alt="The Godfather Vintage Print">
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">The Godfather Vintage Print</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$34.00</ins>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-5-270x340.jpg') }}" alt="Parasite Limited Edition Print">
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">Parasite Limited Edition Print</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$28.00</ins>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-6-270x340.jpg') }}" alt="1917 Panoramic Poster">
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">1917 Panoramic Poster</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$28.00</ins>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-7-270x340.jpg') }}" alt="MovieBox Logo Mug">
              <div class="vv-sale-label absolute left-0 top-0 bg-accent font-bold uppercase text-base text-gray-900 w-24 h-[72px] pl-3 pt-2 leading-normal">Sale!</div>
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">MovieBox Logo Mug</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$18.00</ins>
                <del class="text-sm text-gray-500">$24.00</del>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-8-270x340.jpg') }}" alt="Cinema Classics Black Mug">
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">Cinema Classics Black Mug</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$24.00</ins>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-9-270x340.jpg') }}" alt="MovieBox Black T-Shirt">
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">MovieBox Black T-Shirt</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$26.00</ins>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-10-270x340.jpg') }}" alt="MovieBox Midnight Purple T-Shirt">
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">MovieBox Midnight Purple T-Shirt</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$28.00</ins>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-11-270x340.jpg') }}" alt="MovieBox Enamel Pin">
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">MovieBox Enamel Pin</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$8.00</ins>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-full sm:col-span-6 md:col-span-4 lg:col-span-3">
            <figure class="group relative mb-5.5 overflow-hidden bg-gray-100">
              <img class="aspect-[27/34] w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="{{ asset('assets/img/str1/samples/product-12-270x340.jpg') }}" alt="Clapperboard Keychain">
              <div class="absolute inset-0 bg-white dark:bg-gray-900 opacity-0 group-hover:opacity-90 transition-opacity"></div>
              <div class="flex absolute justify-center left-0 right-0 top-1/2 -translate-y-1/2 scale-90 [@media(hover:hover)]:opacity-0 [@media(hover:hover)]:transition-all [@media(hover:hover)]:duration-300 [@media(hover:hover)]:group-hover:scale-100 [@media(hover:hover)]:group-hover:opacity-100">
                <a href="{{ route('single-product') }}"
                  class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
                  + Add To Cart
                </a>
              </div>
            </figure>
            <div>
              <h3 class="mb-1 font-bold leading-tight text-lg text-gray-900 dark:text-white">
                <a class="text-gray-900 transition-colors hover:text-accent dark:text-white dark:hover:text-accent"
                  href="{{ route('single-product') }}">Clapperboard Keychain</a>
              </h3>
              <div class="flex mb-2 font-medium gap-2 text-gray leading-snug dark:text-gray">
                <ins class="no-underline">$6.00</ins>
              </div>
              <div class="flex items-center gap-x-4 relative text-sm leading-none">
                <div class="relative inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-gray-200 dark:fill-white/20"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
                <div class="absolute left-0 top-0 inline-flex leading-none gap-1">
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                  <svg role="img" class="h-2.5 w-2.5 fill-accent"><use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#star"></use></svg>
                </div>
              </div>
            </div>
          </div>

        </div>

        <nav class="pt-10 md:pt-14 lg:pt-24">
          <ul class="flex flex-wrap items-center justify-center gap-x-1.5 md:gap-x-2 lg:gap-x-4">
            <li class="flex items-center">
              <a class="inline-flex justify-center items-center border border-gray-200 dark:border-gray-600 w-8 lg:w-12.5 aspect-square text-gray-900 dark:text-white hover:bg-accent hover:border-accent dark:hover:border-accent transition-colors hover:text-gray-900 dark:hover:text-gray-900 pointer-events-none"
                href="#">
                <svg class="w-1.5 h-2.5 rotate-180 opacity-40" fill="currentColor">
                  <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#arrow-right-pixel-alt"></use>
                </svg>
              </a>
            </li>
            <li>
              <a class="inline-flex justify-center items-center w-8 lg:w-12.5 aspect-square border font-bold transition-colors text-gray-900 bg-accent border-accent dark:border-accent"
                href="#">1</a>
            </li>
            <li>
              <a class="inline-flex justify-center items-center w-8 lg:w-12.5 aspect-square border font-bold transition-colors border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white hover:text-gray-900 hover:bg-accent dark:hover:text-gray-900 hover:border-accent dark:hover:border-accent"
                href="#">2</a>
            </li>
            <li>
              <a class="inline-flex justify-center items-center w-8 lg:w-12.5 aspect-square border font-bold transition-colors border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white hover:text-gray-900 hover:bg-accent dark:hover:text-gray-900 hover:border-accent dark:hover:border-accent"
                href="#">3</a>
            </li>
            <li class="flex items-center">
              <a class="inline-flex justify-center items-center border border-gray-200 dark:border-gray-600 w-8 lg:w-12.5 aspect-square text-gray-900 dark:text-white hover:bg-accent hover:border-accent dark:hover:border-accent transition-colors hover:text-gray-900 dark:hover:text-gray-900"
                href="#">
                <svg class="w-1.5 h-2.5" fill="currentColor">
                  <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#arrow-right-pixel-alt"></use>
                </svg>
              </a>
            </li>
          </ul>
        </nav>

      </div>
    </div>

  </main>
@endsection
