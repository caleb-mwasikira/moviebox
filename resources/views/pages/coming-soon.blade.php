@extends('layouts.app')

@section('title', 'Coming Soon')

@section('content')
<x-hero :featured="$featured" />

<div class="container-app py-10 lg:py-14">
  <x-ui.page-header eyebrow="On the horizon" title="Coming soon" subtitle="Get a first look at what's arriving next." />

  @if ($films->isEmpty())
    <x-ui.empty icon="calendar" title="Nothing announced yet" text="New releases will be listed here as soon as they're confirmed." />
  @else
    <div class="poster-grid">
      @foreach ($films as $film)
        <x-coming-soon-card :film="$film" :showDescription="true" />
      @endforeach
    </div>
  @endif
</div>
@endsection
