@extends('layouts.app')

@section('title', $comingSoon->title . ' — Coming Soon')

@section('content')
@php $poster = $comingSoon->posters->isNotEmpty() ? $comingSoon->posters->first()->url() : $comingSoon->thumbnailUrl(); @endphp

<section class="relative isolate overflow-hidden border-b border-line">
  @if ($poster)
    <img src="{{ $poster }}" alt="" class="absolute inset-0 -z-10 size-full scale-110 object-cover opacity-30 blur-2xl dark:opacity-25">
  @endif
  <div class="absolute inset-0 -z-10 bg-gradient-to-b from-bg/40 via-bg/70 to-bg"></div>

  <div class="container-app py-8 lg:py-12">
    <a href="{{ route('coming-soon') }}" class="mb-8 inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-brand">
      <x-ui.icon name="arrow-left" class="size-4" /> Coming soon
    </a>

    <div class="grid gap-8 md:grid-cols-12 lg:gap-14" x-data="{ active: 0 }">
      <div class="md:col-span-5 lg:col-span-4">
        <div class="mx-auto max-w-sm md:sticky md:top-24">
          <div class="poster-frame !rounded-2xl shadow-pop">
            @if ($comingSoon->posters->isNotEmpty())
              @foreach ($comingSoon->posters as $idx => $p)
                <img x-show="active === {{ $idx }}" @if($idx > 0) x-cloak @endif src="{{ $p->url() }}" alt="{{ $comingSoon->title }}" class="size-full object-cover">
              @endforeach
            @elseif ($comingSoon->thumbnail)
              <img src="{{ $comingSoon->thumbnailUrl() }}" alt="{{ $comingSoon->title }}" class="size-full object-cover">
            @else
              <div class="grid size-full place-items-center text-muted"><x-ui.icon name="film" class="size-14" /></div>
            @endif
          </div>
          @if ($comingSoon->posters->count() > 1)
            <div class="mt-3 grid grid-cols-5 gap-2">
              @foreach ($comingSoon->posters as $idx => $p)
                <button type="button" @click="active = {{ $idx }}" class="aspect-square overflow-hidden rounded-lg ring-2 transition" :class="active === {{ $idx }} ? 'ring-brand' : 'ring-transparent opacity-60 hover:opacity-100'">
                  <img src="{{ $p->url() }}" alt="" class="size-full object-cover">
                </button>
              @endforeach
            </div>
          @endif
        </div>
      </div>

      <div class="md:col-span-7 lg:col-span-8">
        <div class="flex flex-wrap items-center gap-2">
          <span class="badge bg-amber-400 text-neutral-900">Coming soon</span>
          @if ($comingSoon->genre)<span class="badge badge-brand">{{ $comingSoon->genre }}</span>@endif
        </div>
        <h1 class="mt-4 text-4xl font-extrabold leading-tight text-fg lg:text-5xl">{{ $comingSoon->title }}</h1>
        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-medium text-muted">
          @if ($comingSoon->year)<span class="inline-flex items-center gap-1.5"><x-ui.icon name="calendar" class="size-4" />{{ $comingSoon->year }}</span>@endif
          @if ($comingSoon->duration)<span class="inline-flex items-center gap-1.5"><x-ui.icon name="clock" class="size-4" />{{ $comingSoon->duration }} min</span>@endif
        </div>
        @if ($comingSoon->description)
          <p class="mt-6 max-w-2xl text-base leading-relaxed text-fg-soft">{{ $comingSoon->description }}</p>
        @endif

        <div class="mt-8">
          @if ($comingSoon->trailerIsReady())
            <button type="button" onclick="openTrailerModal('{{ $comingSoon->trailerUrl() }}')" class="btn btn-primary btn-lg"><x-ui.icon name="play" class="size-4" /> Watch trailer</button>
          @else
            <span class="btn btn-secondary btn-lg pointer-events-none opacity-70"><x-ui.icon name="clock" class="size-4" /> Trailer coming soon</span>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
