@extends('layouts.app')

@section('title', 'Gift Sent!')

@section('content')
<main class="grow">
  <div class="py-16 lg:py-24">
    <div class="container max-w-lg text-center">

      <div class="inline-flex items-center justify-center w-16 h-16 bg-accent/10 mb-6">
        <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4M4 8V6a2 2 0 012-2h3m9 0h3a2 2 0 012 2v2M4 16v2a2 2 0 002 2h3m9 0h3a2 2 0 002-2v-2"/>
        </svg>
      </div>

      <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-3">Gift sent!</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-10 max-w-sm mx-auto leading-relaxed">
        <strong class="text-gray-700 dark:text-gray-200">{{ $gift->recipient_email }}</strong> will receive an email with a link to claim their
        <strong class="text-gray-700 dark:text-gray-200">{{ $gift->package->name }}</strong> subscription. The link expires in 30 days.
      </p>

      @if (session('success'))
        <div class="mb-8 border border-green-300 bg-green-50 dark:bg-green-900/20 dark:border-green-700 px-5 py-3 text-sm text-green-700 dark:text-green-400">
          {{ session('success') }}
        </div>
      @endif

      {{-- Gift details card --}}
      <div class="border border-gray-200 dark:border-gray-600 p-6 text-left mb-8">
        <div class="flex items-start justify-between mb-4">
          <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-0.5">Gift Subscription</p>
            <p class="text-base font-bold text-gray-900 dark:text-white">{{ $gift->package->name }}</p>
            <p class="text-xs text-gray-500 mt-0.5">1 {{ ucfirst($gift->package->interval) }}</p>
          </div>
          <span class="inline-flex px-2 py-0.5 text-[10px] font-bold uppercase bg-accent/10 text-accent border border-accent">Sent</span>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-700 pt-4 space-y-3">
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500">To</span>
            <span class="font-medium text-gray-900 dark:text-white">{{ $gift->recipient_email }}</span>
          </div>
          @if ($gift->expires_at)
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500">Claim by</span>
            <span class="font-medium text-gray-900 dark:text-white">{{ $gift->expires_at->format('F j, Y') }}</span>
          </div>
          @endif
        </div>
      </div>

      {{-- Update email form --}}
      <div class="border border-dashed border-gray-200 dark:border-gray-600 p-6 text-left mb-8"
        x-data="{ open: false }">
        <button @click="open = !open"
          class="flex items-center justify-between w-full text-left text-sm font-bold text-gray-700 dark:text-gray-300">
          Wrong email address?
          <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div x-show="open" x-collapse class="mt-4">
          <p class="text-xs text-gray-400 mb-3">Update the recipient's email and a new claim link will be sent to them.</p>
          <form method="POST" action="{{ route('gift.update-email', $gift) }}" class="flex gap-x-2">
            @csrf
            @method('PATCH')
            <input type="email" name="recipient_email"
              value="{{ $gift->recipient_email }}"
              required
              class="flex-1 border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-3 py-2 text-sm focus:border-accent outline-none transition-colors">
            <button type="submit"
              class="px-4 py-2 text-xs font-bold uppercase bg-gray-900 text-white hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors">
              Update
            </button>
          </form>
          @error('recipient_email')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <a href="{{ route('browse') }}"
        class="inline-flex font-bold uppercase tracking-wide py-6 text-xs bg-gray-900 hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors">
        Continue Watching
      </a>

    </div>
  </div>
</main>
@endsection
