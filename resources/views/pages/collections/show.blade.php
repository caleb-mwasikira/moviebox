@extends('layouts.app')

@section('title', $collection->name)

@section('content')
<main id="main-content" class="grow">

  {{-- Header --}}
  <div class="py-16 md:py-20 lg:py-24"
    @if ($collection->thumbnail)
      style="background:linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.85) 100%), url('{{ $collection->thumbnailUrl() }}') center/cover no-repeat;"
    @else
      style="background: #111;"
    @endif
  >
    <div class="container">
      <p class="text-xs font-bold uppercase tracking-widest text-accent mb-3">Collection</p>
      <h1 class="text-3xl md:text-5xl font-bold tracking-tighter text-white mb-4">{{ $collection->name }}</h1>
      @if ($collection->description)
        <p class="text-gray-300 text-base max-w-2xl">{{ $collection->description }}</p>
      @endif
      <p class="mt-4 text-xs text-gray-500 font-bold uppercase tracking-widest">
        {{ $collection->items->count() }} {{ Str::plural('title', $collection->items->count()) }}
      </p>
    </div>
  </div>

  {{-- Items grid --}}
  <div class="py-16 md:py-20 lg:py-24">
    <div class="container">
      @if ($collection->items->isEmpty())
        <div class="py-20 text-center">
          <p class="text-gray-500 text-sm font-bold uppercase tracking-widest">No titles in this collection yet.</p>
        </div>
      @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5 md:gap-6 lg:gap-7.5">
          @foreach ($collection->items as $item)
            @if ($item->collectible)
              @php
                $type = $item->collectible_type === \App\Models\Movie::class ? 'movie' : 'series';
              @endphp
              <x-film-card :film="$item->collectible" :type="$type" />
            @endif
          @endforeach
        </div>
      @endif
    </div>
  </div>

</main>
@endsection
