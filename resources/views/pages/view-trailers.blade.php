@extends('layouts.app')

@section('content')
  <main id="main-content" class="grow lg:pt-0">

    <div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
      <div class="container">

        <section class="mb-20 lg:mb-32">

          <div class="mb-10 md:mb-12 lg:mb-16 xl:mb-20">
            <h2
              class="text-2.5xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
              LiveStream Box
            </h2>

          </div>

          <div class="grid grid-cols-12 gap-x-5 md:gap-x-6 lg:gap-x-7.5">

            <div class="col-span-full lg:col-span-9">
              <iframe class="w-full aspect-[870/480]"
                src="https://player.twitch.tv/?channel=danfisher_dev&parent=valkivid.dan-fisher.dev" allowfullscreen>
              </iframe>
            </div>

            <div class="col-span-full lg:col-span-3 bg-white">
              <iframe class="aspect-[300/480] w-full lg:-ml-7.5 lg:w-[calc(100%+30px)] overflow-auto" id="chat_embed"
                src="https://www.twitch.tv/embed/danfisher_dev/chat?parent=valkivid.dan-fisher.dev">
              </iframe>
            </div>
          </div>

        </section>


        <section class="mb-20 lg:mb-32">

          <div class="flex justify-between items-baseline mb-10 md:mb-12 lg:mb-16 xl:mb-[60px]">

            <h2
              class="text-2.5xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
              Stream Videos
            </h2>

            <div class="flex gap-4 sm:gap-8 items-baseline">
              <ul class="flex gap-4 sm:gap-8">
                <li>
                  <a class="group" href="_str1-streams-4.html">
                    <svg role="img" class="w-5 sm:w-7.5 aspect-square fill-accent dark:fill-accent">
                      <use xlink:href="assets/img/str1/sprite.svg#grid-4"></use>
                    </svg>
                  </a>
                </li>
                <li>
                  <a class="group" href="_str1-streams-3.html">
                    <svg role="img"
                      class="w-5 sm:w-7.5 aspect-square fill-gray-500 group-hover:fill-gray-900 transition-colors dark:fill-gray-650 dark:group-hover:fill-white">
                      <use xlink:href="assets/img/str1/sprite.svg#grid-3"></use>
                    </svg>
                  </a>
                </li>
              </ul>

              <div class="relative">
                <button class="js-vv-streams-filter-toggle group peer">
                  <svg role="img"
                    class="w-5 sm:w-7.5 aspect-square fill-gray-500 group-hover:fill-gray-900 transition-colors dark:fill-gray-650 dark:group-hover:fill-white">
                    <use xlink:href="assets/img/str1/sprite.svg#filter"></use>
                  </svg>
                </button>

                <div
                  class="absolute w-72 sm:w-80 opacity-0 peer-[.active]:opacity-100 transition-all invisible peer-[.active]:visible -translate-y-3 peer-[.active]:translate-y-0 flex flex-col gap-8 bg-white dark:bg-gray-700 p-6 sm:p-7.5 z-10 right-0 top-14 shadow-3xl">
                  <div>
                    <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5"
                      for="category">Category
                    </label>
                    <select
                      class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
                      name="category" id="category">
                      <option value="-">All Categories</option>
                      <option value="gaming">Gaming</option>
                      <option value="twitch-streams">Twitch Streams</option>
                      <option value="Unboxing">Unboxing</option>
                      <option value="Devices">Devices</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5"
                      for="filter-by">Filter By
                    </label>
                    <select
                      class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
                      name="filter-by" id="filter-by">
                      <option value="date-uploaded">Date Uploaded</option>
                      <option value="popular">Popular</option>
                      <option value="most-liked">Most Liked</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5"
                      for="order-by">Order By
                    </label>
                    <select
                      class="w-full border-base bg-white/40 px-4 h-12.5 font-medium text-gray-900 transition-all duration-150 bg-caret bg-[length:10px_6px] bg-[position:right_18px_center] focus:border-accent focus:outline-0 focus:ring-0 focus:text-gray-900 hover:cursor-pointer dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:text-white dark:bg-caret-white dark:focus:bg-gray-900"
                      name="order-by" id="order-by">
                      <option value="descending">Descending</option>
                      <option value="ascending">Ascending</option>
                    </select>
                  </div>
                  <div class="pt-1">
                    <button type="submit"
                      class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-white bg-primary hover:bg-primary/90 w-full">
                      Apply Filters

                    </button>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <nav>
            <ul class="flex flex-wrap gap-x-9 gap-y-2 text-lg font-bold mb-10 md:mb-16 lg:mb-[90px]">
              <li>
                <a class="text-accent dark:text-accent transition-colors" href="#">All Videos</a>
              </li>
              <li>
                <a class="text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent transition-colors"
                  href="#">a-Rex Legends</a>
              </li>
              <li>
                <a class="text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent transition-colors"
                  href="#">Xenowatch</a>
              </li>
              <li>
                <a class="text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent transition-colors"
                  href="#">Witch Hunters</a>
              </li>
              <li>
                <a class="text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent transition-colors"
                  href="#">Super Robo Bross II</a>
              </li>
              <li>
                <a class="text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent transition-colors"
                  href="#">Mochi’s Island</a>
              </li>
              <li>
                <a class="text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent transition-colors"
                  href="#">Rony da Hedgehog</a>
              </li>
              <li>
                <a class="text-gray-900 hover:text-accent dark:text-white dark:hover:text-accent transition-colors"
                  href="#">Zodiac Goddess</a>
              </li>
            </ul>
          </nav>

          <div class="grid gap-y-7.5 grid-cols-12 sm:gap-x-7.5">


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-1-270x160.jpg" alt="[XW] Shadow DLC Gameplay">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    32:25</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-1-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    [XW] Shadow DLC Gameplay
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      2 hours ago
                    </li>
                    <li class="text-accent">
                      Xenowatch
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-2-270x160.jpg"
                    alt="I Play the Witch Hunters Story in this weekend">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    28:17</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-2-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    I Play the Witch Hunters Story in this weekend
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      February 3rd, 2025
                    </li>
                    <li class="text-accent">
                      Witch Hunters
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-3-270x160.jpg" alt="Ram Goddess Gameplay">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    44:39</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-3-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    Ram Goddess Gameplay
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      January 26th, 2025
                    </li>
                    <li class="text-accent">
                      Zodiac Goddess
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-4-270x160.jpg" alt="First Play: Zodiac Goddess">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    01:16:28</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-3-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    First Play: Zodiac Goddess
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      December 17th, 2022
                    </li>
                    <li class="text-accent">
                      Zodiac Goddess
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-5-270x160.jpg" alt="Rony’s EggRing Quest">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    44:26</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-4-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    Rony’s EggRing Quest
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      December 14th, 2022
                    </li>
                    <li class="text-accent">
                      Rony da Hedgehog
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-6-270x160.jpg" alt="Robo Bross Level Maker">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    25:28</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-5-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    Robo Bross Level Maker
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      December 9th, 2022
                    </li>
                    <li class="text-accent">
                      Super Robo Bross II
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-7-270x160.jpg" alt="Athena Goddess Gameplay">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    09:19</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-3-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    Athena Goddess Gameplay
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      October 28th, 2022
                    </li>
                    <li class="text-accent">
                      Zodiac Goddess
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-8-270x160.jpg" alt="2 New Levels on Mochi’s Island Extension">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    02:36:19</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-6-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    2 New Levels on Mochi’s Island Extension
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      October 22nd, 2022
                    </li>
                    <li class="text-accent">
                      Mochi’s Island
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-9-270x160.jpg" alt="Hare Goddess Gameplay">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    58:32</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-3-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    Hare Goddess Gameplay
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      October 20th, 2022
                    </li>
                    <li class="text-accent">
                      Zodiac Goddess
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-10-270x160.jpg" alt="1 Hour of Capture the Flag ">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    01:04:33</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-7-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    1 Hour of Capture the Flag
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      October 16th, 2022
                    </li>
                    <li class="text-accent">
                      a-Rex Legends
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-11-270x160.jpg" alt="Witch Paragliding Minigame">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    08:12</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-2-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    Witch Paragliding Minigame
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      August 30th, 2022
                    </li>
                    <li class="text-accent">
                      Witch Hunters
                    </li>
                  </ul>
                </div>
              </div>
            </div>


            <div class="col-span-full sm:col-span-6 lg:col-span-4 xl:col-span-3">
              <figure class="mb-5">
                <a class="group relative block h-full overflow-hidden bg-gray-900 glightbox"
                  href="https://player.twitch.tv/?video&#x3D;1703307874&amp;parent&#x3D;valkivid.dan-fisher.dev">
                  <img
                    class="aspect-[270/160] w-full object-cover transition-all duration-300 group-hover:scale-110 group-hover:opacity-75"
                    src="assets/img/str1/samples/video-12-270x160.jpg" alt="Exploring The Orc Witch Cave">

                  <div class="absolute inset-0 bg-black/60"></div>

                  <div
                    class="absolute bg-primary uppercase text-white text-xs leading-none font-bold left-3.5 top-3 px-1.5 py-1">
                    17:22</div>

                  <span
                    class="absolute top-1/2 left-1/2 flex aspect-square -translate-x-2/4 -translate-y-2/4 items-center justify-center w-10">
                    <svg role="img" class="fill-white ml-[2px] aspect-[18/22] w-7">
                      <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                    </svg>
                  </span>
                </a>
              </figure>

              <div class="flex gap-x-3">

                <div class="shrink-0">
                  <img src="assets/img/str1/samples/game-2-40x60.jpg" alt="">
                </div>

                <div class="min-w-0">
                  <h3 class="mb-1 font-bold text-base leading-tight text-gray-900 dark:text-white truncate w-full">
                    Exploring The Orc Witch Cave
                  </h3>
                  <ul class="flex flex-col gap-y-1.5 text-sm leading-none font-medium">
                    <li>
                      August 27th, 2022
                    </li>
                    <li class="text-accent">
                      Witch Hunters
                    </li>
                  </ul>
                </div>
              </div>
            </div>


          </div>

          <div class="flex items-center justify-center mt-10 md:mt-20 lg:mt-28">
            <div class="vv-preloader-spikes-roll relative h-10 w-16 animate-spike-roll bg-spike-roll bg-no-repeat"></div>
          </div>

        </section>

        <div
          class="bg-[#232738] bg-[url('../../../assets/img/str1/samples/banner-bg.jpg')] bg-cover bg-no-repeat lg:bg-auto">
          <div class="relative h-32 overflow-hidden lg:h-40">

            <div
              class="absolute left-[45%] top-3.5 hidden aspect-square w-[110px] rounded-full bg-gray-900 font-bold uppercase text-white lg:flex lg:flex-col lg:items-center lg:justify-center lg:leading-none">
              <span class="text-xs leading-none">The Best</span>
              <span class="-mb-1 text-2xl leading-none text-accent">Retro</span>
              <span class="text-[22px] leading-none">Games</span>
              <span class="text-xs leading-none">Reviews</span>
            </div>

            <div
              class="vv-channel-info-banner absolute inset-y-0 right-0 left-1/4 flex items-center bg-primary pl-10 text-white sm:left-1/2 md:left-[60%] lg:pl-20 xl:pl-24">
              <div class="flex gap-x-4">
                <svg class="aspect-square w-8 lg:w-14" fill="currentColor">
                  <use xlink:href="assets/img/social-icons.svg#twitch"></use>
                </svg>
                <div>
                  <div class="text-2xl font-bold leading-none lg:text-[42px]">NekoMaGiX</div>
                  <div class="text-sm font-bold leading-none lg:text-lg">Subscribe to my
                    channel!</div>
                </div>
              </div>
            </div>

            <a class="after:absolute after:inset-0 after:block" href="#"></a>

          </div>
        </div>


      </div>
    </div>

  </main>
@endsection