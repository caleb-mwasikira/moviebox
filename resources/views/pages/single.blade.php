@extends('layouts.app')

@section('content')
  <main id="main-content" class="grow lg:pt-0">


    <div class="pb-16 lg:pb-44">
      <div class="container">

        <div class="grid grid-cols-12 gap-x-5 md:gap-x-6 lg:gap-x-7.5 grid-rows-[180px_1fr] lg:grid-rows-[304px_1fr]">

          <figure class="col-span-full row-start-1 row-span-1 xl:-mx-[75px]">
            <img src="assets/img/str1/samples/post-img-2-1320x660.jpg" alt="">
          </figure>

          <div class="col-span-full md:col-start-2 md:col-span-10 row-start-2 row-span-1 bg-white dark:bg-gray-900 grid grid-cols-10 gap-x-5 md:gap-x-6 lg:gap-x-7.5">

            <div class="col-span-full lg:col-start-2 lg:col-span-8">
              <article class="pt-10 sm:pt-12 md:pt-16 lg:pt-[90px]">

                <header class="text-center mb-8 sm:mb-10 md:mb-12 lg:mb-16 xl:mb-20">

                  <time class="block text-accent font-bold text-base lg:text-lg leading-tight mb-1 md:mb-2 xl:mb-4 pt-1" datetime="2025-03-06 20:00">March 6th, 2025</time>

                  <h1 class="text-gray-900 dark:text-white text-2xl sm:text-3xl sm:leading-none md:text-4xl md:leading-none lg:text-5xl lg:leading-none leading-none xl:text-[62px] xl:leading-none font-bold xl:px-16 mb-5 md:mb-6 lg:mb-10 xl:mb-12">
                    Witch Hunters Deluxe Pack Unboxing
                  </h1>

                  <ul class="flex justify-center gap-x-4">
                    <li>
                      <a href="#" class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-white bg-social-facebook hover:bg-social-facebook/90">
                        <svg class="h-[14px] w-[14px]" fill="currentColor">
                          <use xlink:href="assets/img/social-icons.svg#facebook"></use>
                        </svg>
                        Share!

                      </a>
                    </li>
                    <li>
                      <a href="#" class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-white bg-social-twitter hover:bg-social-twitter/90">
                        <svg class="h-[14px] w-[14px]" fill="currentColor">
                          <use xlink:href="assets/img/social-icons.svg#twitter"></use>
                        </svg>
                        Tweet!

                      </a>
                    </li>
                  </ul>
                </header>

                <div class="vv-prose">
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

                  <figure>
                    <a class="group relative block h-full overflow-hidden bg-gray-900" href="#">
                      <img class="my-0 aspect-[770/460] w-full object-cover transition-transform duration-300 group-hover:scale-110 lg:my-0" src="assets/img/str1/samples/single-post-img-2-770x460.jpg" alt="">
                      <div class="absolute inset-0 bg-black opacity-40 dark:opacity-60"></div>
                      <span class="absolute top-1/2 left-1/2 flex aspect-square h-20 w-20 -translate-x-2/4 -translate-y-2/4 items-center justify-center">
                        <svg role="img" class="mr-[-3px] h-[27px] w-[22px] fill-white">
                          <use xlink:href="assets/img/yt1/sprite.svg#play"></use>
                        </svg>
                      </span>
                    </a>
                  </figure>

                  <h3>Why the Deluxe Pack is Worth It</h3>

                  <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>

                  <h3>Final Thougths</h3>
                  <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia.</p>

                  <div class="grid gap-x-[30px] md:auto-cols-fr md:grid-flow-col">
                    <div>
                      <h4>Pros</h4>
                      <ul class="list-none p-0">
                        <li>
                          <span class="-mt-1 flex h-[22px] w-[22px] flex-shrink-0 items-center justify-center rounded-full border-2 lg:mt-1 border-accent text-accent">
                            <svg role="img" class="h-2 w-2" fill="currentColor">
                              <use xlink:href="assets/img/str1/sprite.svg#plus-thin"></use>
                            </svg>
                          </span>
                          Crimson Witch PVC Figurine
                        </li>
                        <li>
                          <span class="-mt-1 flex h-[22px] w-[22px] flex-shrink-0 items-center justify-center rounded-full border-2 lg:mt-1 border-accent text-accent">
                            <svg role="img" class="h-2 w-2" fill="currentColor">
                              <use xlink:href="assets/img/str1/sprite.svg#plus-thin"></use>
                            </svg>
                          </span>
                          6+ Hours of Bonus Content
                        </li>
                        <li>
                          <span class="-mt-1 flex h-[22px] w-[22px] flex-shrink-0 items-center justify-center rounded-full border-2 lg:mt-1 border-accent text-accent">
                            <svg role="img" class="h-2 w-2" fill="currentColor">
                              <use xlink:href="assets/img/str1/sprite.svg#plus-thin"></use>
                            </svg>
                          </span>
                          Hand Crafted Boxset
                        </li>
                      </ul>
                    </div>
                    <div>
                      <h4>Cons</h4>
                      <ul class="list-none p-0">
                        <li>
                          <span class="-mt-1 flex h-[22px] w-[22px] flex-shrink-0 items-center justify-center rounded-full border-2 lg:mt-1 border-primary text-primary">
                            <svg role="img" class="h-2 w-2" fill="currentColor">
                              <use xlink:href="assets/img/str1/sprite.svg#plus-thin"></use>
                            </svg>
                          </span>
                          Hand Crafted Boxset
                        </li>
                        <li>
                          <span class="-mt-1 flex h-[22px] w-[22px] flex-shrink-0 items-center justify-center rounded-full border-2 lg:mt-1 border-primary text-primary">
                            <svg role="img" class="h-2 w-2" fill="currentColor">
                              <use xlink:href="assets/img/str1/sprite.svg#plus-thin"></use>
                            </svg>
                          </span>
                          Only a Few New Characters
                        </li>
                      </ul>
                    </div>
                  </div>


                </div>

                <footer class="border-y border-gray/20 py-6 lg:py-10">
                  <ul class="flex gap-x-4">
                    <li class="flex-1">
                      <a href="#" class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-white bg-social-facebook hover:bg-social-facebook/90 w-full">
                        <svg class="h-[14px] w-[14px]" fill="currentColor">
                          <use xlink:href="assets/img/social-icons.svg#facebook"></use>
                        </svg>
                        Share!

                      </a>
                    </li>
                    <li class="flex-1">
                      <a href="#" class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-white bg-social-twitter hover:bg-social-twitter/90 w-full">
                        <svg class="h-[14px] w-[14px]" fill="currentColor">
                          <use xlink:href="assets/img/social-icons.svg#twitter"></use>
                        </svg>
                        Tweet!

                      </a>
                    </li>
                  </ul>
                </footer>
              </article>

              <div class="mt-16 md:mt-20 lg:mt-28 xl:mt-32">
                <h2 class="mb-8 text-xl font-bold text-gray-900 dark:text-white md:mb-16 md:text-2xl lg:mb-20 lg:text-3xl xl:mb-24 xl:text-4xl">
                  4 Comments
                </h2>
                <ol>
                  <li class="mb-12 lg:mb-20">
                    <div>
                      <div class="flex gap-x-5 md:gap-x-7 mb-7">
                        <figure class="shrink-0">
                          <img src="assets/img/str1/samples/user-2-60x60.jpg" alt="Comment Author Avatar">
                        </figure>
                        <div class="flex-grow">
                          <div>
                            <h5 class="mb-1 text-lg md:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">Mark Stark</h5>
                            <div class="text-xs md:text-sm uppercase font-bold text-gray-500">5 minutes ago</div>
                          </div>
                        </div>
                      </div>

                      <div class="mb-7 leading-relaxed md:text-lg md:leading-9">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua en derum enim.
                      </div>
                      <div class="flex gap-8">
                        <a class="text-sm font-bold uppercase leading-none text-gray-900 dark:text-white dark:hover:text-accent transition-colors hover:text-accent" href="#">Like ( <span class="text-accent">2</span> )</a>
                        <a class="text-sm font-bold uppercase leading-none text-gray-900 dark:text-white dark:hover:text-accent transition-colors hover:text-accent" href="#">Reply</a>
                      </div>
                    </div>

                  </li>
                  <li class="mb-12 lg:mb-20">
                    <div>
                      <div class="flex gap-x-5 md:gap-x-7 mb-7">
                        <figure class="shrink-0">
                          <img src="assets/img/str1/samples/user-3-60x60.jpg" alt="Comment Author Avatar">
                        </figure>
                        <div class="flex-grow">
                          <div>
                            <h5 class="mb-1 text-lg md:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">Marina Valentine</h5>
                            <div class="text-xs md:text-sm uppercase font-bold text-gray-500">23 minutes ago</div>
                          </div>
                        </div>
                      </div>

                      <div class="mb-7 leading-relaxed md:text-lg md:leading-9">
                        Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam
                      </div>
                      <div class="flex gap-8">
                        <a class="text-sm font-bold uppercase leading-none text-gray-900 dark:text-white dark:hover:text-accent transition-colors hover:text-accent" href="#">Like ( <span class="text-accent">5</span> )</a>
                        <a class="text-sm font-bold uppercase leading-none text-gray-900 dark:text-white dark:hover:text-accent transition-colors hover:text-accent" href="#">Reply</a>
                      </div>
                    </div>

                    <ul class="mt-12 md:pl-10 lg:mt-20">
                      <li class="mb-12 lg:mb-20">
                        <div>
                          <div class="flex gap-x-5 md:gap-x-7 mb-7">
                            <figure class="shrink-0">
                              <img src="assets/img/str1/samples/user-4-60x60.jpg" alt="Comment Author Avatar">
                            </figure>
                            <div class="flex-grow">
                              <div>
                                <h5 class="mb-1 text-lg md:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">Nicholas Grissom</h5>
                                <div class="text-xs md:text-sm uppercase font-bold text-gray-500">11 minutes ago</div>
                              </div>
                            </div>
                          </div>

                          <div class="mb-7 leading-relaxed md:text-lg md:leading-9">
                            At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias.
                          </div>
                          <div class="flex gap-8">
                            <a class="text-sm font-bold uppercase leading-none text-gray-900 dark:text-white dark:hover:text-accent transition-colors hover:text-accent" href="#">Like ( <span class="text-accent">0</span> )</a>
                            <a class="text-sm font-bold uppercase leading-none text-gray-900 dark:text-white dark:hover:text-accent transition-colors hover:text-accent" href="#">Reply</a>
                          </div>
                        </div>

                      </li>
                    </ul>
                  </li>
                  <li class="mb-12 lg:mb-20">
                    <div>
                      <div class="flex gap-x-5 md:gap-x-7 mb-7">
                        <figure class="shrink-0">
                          <img src="assets/img/str1/samples/user-5-60x60.jpg" alt="Comment Author Avatar">
                        </figure>
                        <div class="flex-grow">
                          <div>
                            <h5 class="mb-1 text-lg md:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white">Peter Morales</h5>
                            <div class="text-xs md:text-sm uppercase font-bold text-gray-500">5 minutes ago</div>
                          </div>
                        </div>
                      </div>

                      <div class="mb-7 leading-relaxed md:text-lg md:leading-9">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua en derum enim.
                      </div>
                      <div class="flex gap-8">
                        <a class="text-sm font-bold uppercase leading-none text-gray-900 dark:text-white dark:hover:text-accent transition-colors hover:text-accent" href="#">Like ( <span class="text-accent">1</span> )</a>
                        <a class="text-sm font-bold uppercase leading-none text-gray-900 dark:text-white dark:hover:text-accent transition-colors hover:text-accent" href="#">Reply</a>
                      </div>
                    </div>

                  </li>
                </ol>
              </div>

              <div class="mt-16 md:mt-20 lg:mt-28 xl:mt-32">
                <h2 class="mb-8 text-xl font-bold text-gray-900 dark:text-white md:mb-12 md:text-2xl lg:mb-16 lg:text-3xl xl:mb-20 xl:text-4xl">
                  Leave a Comment
                </h2>
                <form class="grid grid-cols-1 md:grid-cols-2 gap-7.5" action="#">
                  <div class="col-span-full lg:col-span-1">
                    <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="post-name">Your Name
                    </label>
                    <input class="group-[.is-success]:bg-input-success-dark group-[.is-error]:border-danger group-[.is-error]:bg-input-invalid group-[.is-error]:text-danger group-[.is-success]:pr-16 group-[.is-invalid]:pr-16 px-4 py-2 border-base bg-white/40 bg-[length:18px_18px,_38px_38px] bg-[position:right_16px_center,_right_6px_center] bg-no-repeat font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:group-[.is-success]:bg-input-success dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 w-full" type="text" name="post-name" id="post-name" value="" placeholder="">
                    <p class="group-[.is-error]:block mt-2 hidden text-xs text-danger">

                    </p>
                  </div>
                  <div class="col-span-full lg:col-span-1">
                    <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="post-email">Your Email
                    </label>
                    <input class="group-[.is-success]:bg-input-success-dark group-[.is-error]:border-danger group-[.is-error]:bg-input-invalid group-[.is-error]:text-danger group-[.is-success]:pr-16 group-[.is-invalid]:pr-16 px-4 py-2 border-base bg-white/40 bg-[length:18px_18px,_38px_38px] bg-[position:right_16px_center,_right_6px_center] bg-no-repeat font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 placeholder:font-normal placeholder:text-gray-500/60 focus:border-accent focus:bg-white focus:text-gray-900 focus:outline-0 focus:ring-0 dark:border-gray-650 dark:group-[.is-success]:bg-input-success dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 w-full" type="email" name="post-email" id="post-email" value="" placeholder="">
                    <p class="group-[.is-error]:block mt-2 hidden text-xs text-danger">

                    </p>
                  </div>
                  <div class="col-span-full">
                    <label class="block text-sm font-bold uppercase text-gray-500 [&:not(:empty)]:mb-2.5" for="post-comment">Your Comment
                    </label>
                    <textarea class="block border-base bg-white/40 w-full px-4 py-2 tracking-tight text-gray-900 transition-all duration-150 placeholder:text-gray-500/60 focus:border-accent focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:placeholder:text-gray-500/80 dark:focus:border-accent dark:focus:bg-gray-900 h-40 md:h-48" name="post-comment" id="post-comment" placeholder=""></textarea>
                  </div>
                  <div class="col-span-full lg:flex lg:justify-end">
                    <button type="submit" class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-4 md:py-[18px] lg:px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 w-full lg:w-auto lg:basis-1/3">
                      Post Comment

                    </button>
                  </div>
                </form>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

  </main>
@endsection