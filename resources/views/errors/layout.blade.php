@extends('layouts.app')

@section('title', $code . ' — ' . $title)

@section('no-footer', '1')

@section('content')
@php
  use App\Models\Movie;
  use App\Models\Series;
  use App\Models\Poster;
  $bgUrl = Poster::inRandomOrder()->first()?->url()
    ?? Movie::whereNotNull('thumbnail')->where('status', 'published')->inRandomOrder()->first()?->thumbnailUrl()
    ?? Series::whereNotNull('thumbnail')->where('status', 'published')->inRandomOrder()->first()?->thumbnailUrl();
@endphp

<div class="relative isolate flex min-h-[calc(100vh-4rem)] items-center justify-center overflow-hidden px-5 py-16">
  @if ($bgUrl)
    <img src="{{ $bgUrl }}" alt="" class="absolute inset-0 -z-20 size-full scale-110 object-cover opacity-30 blur-md">
  @endif
  <div class="absolute inset-0 -z-10 bg-gradient-to-b from-bg/60 via-bg/80 to-bg"></div>

  <div class="w-full max-w-md animate-fade-up text-center">
    <p class="select-none text-[8rem] font-extrabold leading-none tracking-tighter text-gradient sm:text-[10rem]">{{ $code }}</p>
    <h1 class="-mt-2 text-2xl font-bold text-fg sm:text-3xl">{{ $title }}</h1>
    <p class="mx-auto mt-3 max-w-sm text-sm leading-relaxed text-muted">{{ $message }}</p>
    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
      <a href="{{ route('home') }}" class="btn btn-primary btn-lg"><x-ui.icon name="home" class="size-4" /> Go to homepage</a>
      <a href="javascript:history.back()" class="btn btn-secondary btn-lg"><x-ui.icon name="arrow-left" class="size-4" /> Go back</a>
    </div>
  </div>
</div>
@endsection
