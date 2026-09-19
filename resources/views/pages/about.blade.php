@extends('layouts.app')

@section('title', 'About')

@section('content')
<section class="border-b border-line">
  <div class="container-app py-16 text-center lg:py-24">
    <p class="eyebrow mb-3">About us</p>
    <h1 class="mx-auto max-w-3xl text-4xl font-extrabold text-fg sm:text-5xl">Your home for <span class="text-gradient">world-class cinema</span></h1>
    <p class="mx-auto mt-5 max-w-2xl text-lg text-muted">
      Founded in 2019, we curate the finest films from Hollywood and beyond — bringing you timeless classics, award-winning dramas,
      pulse-pounding thrillers, and bold international stories, all in one place.
    </p>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
      <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Start free trial</a>
      <a href="{{ route('pricing') }}" class="btn btn-secondary btn-lg">View plans</a>
    </div>
  </div>
</section>

<div class="container-app grid gap-12 py-14 lg:grid-cols-3 lg:gap-16 lg:py-20">
  <div class="space-y-14 lg:col-span-2">

    {{-- FAQ --}}
    <section x-data="{ open: 1 }">
      <h2 class="section-title mb-6">Frequently asked questions</h2>
      @php
        $faqs = [
          ['How many movies and shows are on ' . config('app.name') . '?', config('app.name') . ' currently offers over 10,000 films and series spanning every genre — from Hollywood blockbusters and indie darlings to international cinema and classic collections. New titles are added every week, and our library grows by hundreds of films each month.'],
          ['Can I download movies to watch offline?', 'Yes! Premium and 4K Ultra subscribers can download up to 25 titles at a time to watch without an internet connection. Downloads are available on our iOS and Android apps and are stored for up to 30 days.'],
          ['What streaming quality does ' . config('app.name') . ' support?', config('app.name') . ' streams in SD, HD, Full HD, and 4K HDR depending on your plan and device. Our 4K Ultra and Premium plans support Dolby Vision and Dolby Atmos where available. We automatically adjust quality based on your connection speed.'],
          ['How do I request a film to be added?', null],
        ];
      @endphp
      <div class="space-y-3">
        @foreach ($faqs as $i => [$q, $a])
          <div class="card overflow-hidden">
            <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
              <span class="font-semibold text-fg">{{ $q }}</span>
              <x-ui.icon name="plus" class="size-5 shrink-0 text-brand transition" ::class="open === {{ $i }} && 'rotate-45'" />
            </button>
            <div x-show="open === {{ $i }}" x-cloak x-transition>
              <p class="px-5 pb-5 text-sm leading-relaxed text-muted">
                @if ($a) {{ $a }} @else
                  Use the <a href="{{ route('contact') }}" class="font-semibold text-brand hover:underline">Contact page</a> to submit a film request. Our curation team reviews all requests weekly. Titles with the most member demand are prioritised for licensing negotiations.
                @endif
              </p>
            </div>
          </div>
        @endforeach
      </div>
    </section>

    {{-- Guidelines --}}
    <section>
      <h2 class="section-title mb-6">Community guidelines</h2>
      <ol class="grid gap-3 sm:grid-cols-2">
        @foreach (['Be respectful in reviews and comments', 'No spoilers in public discussions', 'Do not share account credentials', 'Report inappropriate or illegal content', 'No screen recording or redistribution', 'One account per subscription plan', 'Happy watching!'] as $n => $rule)
          <li class="flex items-center gap-4 rounded-xl border border-line bg-surface px-4 py-3.5">
            <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-brand-soft text-sm font-bold text-brand">{{ $n + 1 }}</span>
            <span class="text-sm font-medium text-fg-soft">{{ $rule }}</span>
          </li>
        @endforeach
      </ol>
    </section>
  </div>

  <aside>
    <div class="card card-pad lg:sticky lg:top-24">
      <h3 class="card-title mb-5">{{ config('app.name') }} at a glance</h3>
      <dl class="space-y-3.5 text-sm">
        @foreach (['Founded' => '2019, Los Angeles', 'Available on' => 'All devices', 'Content rating' => 'All ages', 'Top genre' => 'Sci-Fi & Drama', 'Library' => '10,000+ titles', 'New titles' => 'Added weekly', 'Max quality' => '4K Dolby Vision'] as $k => $v)
          <div class="flex items-center justify-between gap-4 border-b border-line pb-3 last:border-0 last:pb-0">
            <dt class="text-muted">{{ $k }}</dt><dd class="font-semibold text-fg">{{ $v }}</dd>
          </div>
        @endforeach
      </dl>
      <a href="{{ route('register') }}" class="btn btn-primary btn-block mt-6">Start free trial</a>
    </div>
  </aside>
</div>
@endsection
