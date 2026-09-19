@extends('layouts.app')

@section('title', $collection->name)

@section('content')
<section class="relative isolate overflow-hidden border-b border-line bg-neutral-950">
  @if ($collection->thumbnail)
    <img src="{{ $collection->thumbnailUrl() }}" alt="" class="absolute inset-0 -z-10 size-full object-cover opacity-40">
  @endif
  <div class="absolute inset-0 -z-10 bg-gradient-to-t from-bg via-neutral-950/60 to-neutral-950/40"></div>
  <div class="container-app py-16 lg:py-24">
    <p class="eyebrow mb-3">Collection</p>
    <h1 class="text-4xl font-extrabold !text-white sm:text-5xl">{{ $collection->name }}</h1>
    @if ($collection->description)<p class="mt-4 max-w-2xl text-base text-white/70">{{ $collection->description }}</p>@endif
    <p class="mt-5"><span class="badge bg-white/10 text-white backdrop-blur">{{ $collection->items->count() }} {{ Str::plural('title', $collection->items->count()) }}</span></p>
  </div>
</section>

<div class="container-app py-12">
  @if ($collection->items->isEmpty())
    <x-ui.empty icon="folder" title="This collection is empty" text="Titles will appear here once they're added." />
  @else
    <div class="poster-grid">
      @foreach ($collection->items as $item)
        @if ($item->collectible)
          @php $type = $item->collectible_type === \App\Models\Movie::class ? 'movie' : 'series'; @endphp
          <x-film-card :film="$item->collectible" :type="$type" />
        @endif
      @endforeach
    </div>
  @endif
</div>
@endsection
