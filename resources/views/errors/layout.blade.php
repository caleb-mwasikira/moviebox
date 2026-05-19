@extends('layouts.app')

@section('title', $code . ' — ' . $title)

@push('head')
<style>
  .err-wrap {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100svh - 64px);
    padding: 2rem 1rem;
    overflow: hidden;
  }
  @media (min-width: 1024px) {
    .err-wrap { min-height: calc(100vh - 90px); }
  }
  .err-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center top;
    filter: blur(3px) brightness(0.28) saturate(1.2);
    transform: scale(1.06);
    transition: opacity .4s;
  }
  .err-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(160deg, rgba(0,0,0,.25) 0%, rgba(0,0,0,.65) 60%, rgba(0,0,0,.88) 100%);
  }
  .err-card {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 440px;
    width: 100%;
  }
  .err-code {
    font-size: clamp(5rem, 18vw, 9rem);
    font-weight: 900;
    line-height: 1;
    letter-spacing: -.03em;
    color: rgba(255,255,255,.06);
    user-select: none;
    margin-bottom: 0;
  }
  .err-label {
    font-size: .55rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .22em;
    color: var(--color-accent, #facc15);
    margin-top: -.35rem;
    margin-bottom: .6rem;
  }
  .err-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #fff;
    line-height: 1.2;
    margin-bottom: .75rem;
  }
  .err-message {
    font-size: .825rem;
    color: rgba(255,255,255,.45);
    line-height: 1.7;
    margin-bottom: 2rem;
  }
  .err-btn {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-size: .65rem;
    padding: .85rem 2rem;
    background: #fde047;
    color: #111827;
    text-decoration: none;
    transition: background .15s, transform .1s;
  }
  .err-btn:active { transform: scale(.98); }
</style>
@endpush

@section('content')
@php
  use App\Models\Movie;
  use App\Models\Series;
  use App\Models\Poster;

  $bgUrl = Poster::inRandomOrder()->first()?->url()
    ?? Movie::whereNotNull('thumbnail')->where('status', 'published')->inRandomOrder()->first()?->thumbnailUrl()
    ?? Series::whereNotNull('thumbnail')->where('status', 'published')->inRandomOrder()->first()?->thumbnailUrl();
@endphp
<main class="grow">
  <div class="err-wrap">

    <div class="err-bg" @if($bgUrl) style="background-image:url('{{ $bgUrl }}')" @else style="background:#0c1220" @endif></div>
    <div class="err-overlay"></div>

    <div class="err-card">
      <p class="err-code">{{ $code }}</p>
      <p class="err-label">{{ $title }}</p>
      <h1 class="err-title">{{ $title }}</h1>
      <p class="err-message">{{ $message }}</p>

      <div style="display:flex;align-items:center;justify-content:center;gap:.75rem;flex-wrap:wrap;">
        <a href="{{ route('home') }}" class="err-btn">
          <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
          </svg>
          Go to Homepage
        </a>
        <a href="javascript:history.back()"
          style="display:inline-flex;align-items:center;gap:.4rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;font-size:.65rem;padding:.85rem 1.5rem;color:rgba(255,255,255,.5);border:1px solid rgba(255,255,255,.15);text-decoration:none;transition:color .15s,border-color .15s;"
          onmouseover="this.style.color='#fff';this.style.borderColor='rgba(255,255,255,.4)'"
          onmouseout="this.style.color='rgba(255,255,255,.5)';this.style.borderColor='rgba(255,255,255,.15)'">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
          </svg>
          Go Back
        </a>
      </div>
    </div>

  </div>
</main>
@endsection
