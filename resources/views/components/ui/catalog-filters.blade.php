@props(['route', 'genres'])
<div class="mb-10 flex flex-col gap-4">
  <div class="inline-flex w-fit rounded-xl border border-line bg-surface p-1">
    <a href="{{ route($route) }}" class="rounded-lg px-4 py-1.5 text-sm font-semibold transition {{ !request('free') && !request('genre') ? 'bg-brand text-white shadow' : 'text-fg-soft hover:text-fg' }}">All</a>
    <a href="{{ route($route, ['free' => 1]) }}" class="rounded-lg px-4 py-1.5 text-sm font-semibold transition {{ request('free') ? 'bg-brand text-white shadow' : 'text-fg-soft hover:text-fg' }}">Free</a>
  </div>
  @if ($genres->isNotEmpty())
    <div class="flex flex-wrap gap-2">
      @foreach ($genres as $genre)
        <a href="{{ route($route, ['genre' => $genre]) }}"
          class="badge px-3.5 py-2 text-xs transition {{ request('genre') === $genre ? 'bg-brand text-white' : 'badge-neutral hover:bg-surface-3 hover:text-fg' }}">{{ $genre }}</a>
      @endforeach
    </div>
  @endif
</div>
