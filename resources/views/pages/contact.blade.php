@extends('layouts.app')

@section('content')
<div class="pt-10 pb-16 lg:pt-20 lg:pb-44">
  <div class="container">

    <div class="mb-10 md:mb-12 lg:mb-16">
      <h2 class="text-2.5xl font-bold tracking-tighter text-gray-900 before:text-accent before:content-['.'] dark:text-white sm:text-3.5xl md:text-4xl lg:text-[62px] lg:leading-none">
        Contact Us
      </h2>
      <p class="mt-4 text-gray-500 dark:text-gray max-w-lg">
        Have a question about your subscription, a film you'd like to see added, or a partnership opportunity? We'd love to hear from you.
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 lg:gap-16">

      {{-- Left: Info --}}
      <div class="flex flex-col gap-y-10">

        <div class="flex gap-x-4">
          <svg class="aspect-square h-10 shrink-0 fill-accent" fill="currentColor">
            <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#mailbox"></use>
          </svg>
          <div>
            <h6 class="uppercase font-bold text-gray-500 text-xs mb-1">Member Support</h6>
            <a class="text-gray-900 dark:text-white font-bold hover:text-accent dark:hover:text-accent transition-colors"
              href="mailto:support@moviebox.com">support@moviebox.com</a>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray">Billing, accounts &amp; technical help</p>
          </div>
        </div>

        <div class="flex gap-x-4">
          <svg class="aspect-square h-10 shrink-0 fill-accent" fill="currentColor">
            <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#award"></use>
          </svg>
          <div>
            <h6 class="uppercase font-bold text-gray-500 text-xs mb-1">Film &amp; Content Licensing</h6>
            <a class="text-gray-900 dark:text-white font-bold hover:text-accent dark:hover:text-accent transition-colors"
              href="mailto:licensing@moviebox.com">licensing@moviebox.com</a>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray">Submit your film or inquire about licensing</p>
          </div>
        </div>

        <div class="flex gap-x-4">
          <svg class="aspect-square h-10 shrink-0 fill-accent" fill="currentColor">
            <use xlink:href="{{ asset('assets/img/str1/sprite.svg') }}#joystick"></use>
          </svg>
          <div>
            <h6 class="uppercase font-bold text-gray-500 text-xs mb-1">Press &amp; Partnerships</h6>
            <a class="text-gray-900 dark:text-white font-bold hover:text-accent dark:hover:text-accent transition-colors"
              href="mailto:press@moviebox.com">press@moviebox.com</a>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray">Media enquiries and brand collaborations</p>
          </div>
        </div>

        <div>
          <h6 class="uppercase font-bold text-gray-500 text-xs mb-4">Follow MovieBox</h6>
          <ul class="flex gap-x-4">
            <li>
              <a class="flex h-9 w-9 items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-accent hover:text-white dark:hover:bg-accent transition-colors text-gray-900 dark:text-white"
                href="https://x.com" title="X / Twitter">
                <svg class="h-4 w-4" fill="currentColor">
                  <use xlink:href="{{ asset('assets/img/social-icons.svg') }}#x"></use>
                </svg>
              </a>
            </li>
            <li>
              <a class="flex h-9 w-9 items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-accent hover:text-white dark:hover:bg-accent transition-colors text-gray-900 dark:text-white"
                href="https://instagram.com" title="Instagram">
                <svg class="h-4 w-4" fill="currentColor">
                  <use xlink:href="{{ asset('assets/img/social-icons.svg') }}#instagram"></use>
                </svg>
              </a>
            </li>
            <li>
              <a class="flex h-9 w-9 items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-accent hover:text-white dark:hover:bg-accent transition-colors text-gray-900 dark:text-white"
                href="https://facebook.com" title="Facebook">
                <svg class="h-4 w-4" fill="currentColor">
                  <use xlink:href="{{ asset('assets/img/social-icons.svg') }}#facebook"></use>
                </svg>
              </a>
            </li>
            <li>
              <a class="flex h-9 w-9 items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-accent hover:text-white dark:hover:bg-accent transition-colors text-gray-900 dark:text-white"
                href="https://discord.com" title="Discord">
                <svg class="h-4 w-4" fill="currentColor">
                  <use xlink:href="{{ asset('assets/img/social-icons.svg') }}#discord"></use>
                </svg>
              </a>
            </li>
          </ul>
        </div>

      </div>

      {{-- Right: Form --}}
      <div class="lg:col-span-2">

        @if (session('status') === 'message-sent')
          <div class="mb-6 border border-accent bg-accent/10 px-4 py-3 text-sm text-accent font-medium">
            Thanks for reaching out! We'll get back to you within 1–2 business days.
          </div>
        @endif

        <form method="POST" action="{{ route('contact.store') }}" class="flex flex-col gap-y-6"
          x-data="{ loading: false }" @submit="loading = true">
          @csrf

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="name">Your Name</label>
              <input
                class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('name') border-danger @enderror"
                type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name">
              @error('name')
                <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="email">Your Email</label>
              <input
                class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full @error('email') border-danger @enderror"
                type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email">
              @error('email')
                <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="message">Your Message</label>
            <textarea
              class="block px-4 py-2 border-base bg-white/40 font-medium tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full h-44 md:h-56 @error('message') border-danger @enderror"
              name="message" id="message" placeholder="Tell us about your subscription issue, a film request, or anything else on your mind.">{{ old('message') }}</textarea>
            @error('message')
              <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <button type="submit" :disabled="loading"
              class="inline-flex items-center text-center font-bold leading-none transition-colors uppercase justify-center gap-x-2 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 disabled:opacity-60 disabled:cursor-not-allowed">
              <svg x-show="loading" class="animate-spin h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <span x-text="loading ? 'Sending…' : 'Send Message'"></span>
            </button>
          </div>
        </form>

      </div>

    </div>
  </div>
</div>
@endsection
