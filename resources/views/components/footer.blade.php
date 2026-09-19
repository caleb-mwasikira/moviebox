<footer class="mt-24 border-t border-line bg-surface">
  <div class="container-app">
    <div class="grid gap-10 py-14 md:grid-cols-12">
      <div class="md:col-span-4">
        <a href="{{ route('home') }}"><x-ui.logo /></a>
        <p class="mt-4 max-w-xs text-sm leading-relaxed text-muted">
          Stream the world's greatest cinema. Films and series from every era and genre — available anywhere, anytime.
        </p>
      </div>

      <div class="md:col-span-2">
        <h6 class="mb-4 text-xs font-semibold uppercase tracking-widest text-muted">Explore</h6>
        <ul class="space-y-2.5 text-sm">
          <li><a class="text-fg-soft hover:text-brand" href="{{ route('home') }}">Home</a></li>
          <li><a class="text-fg-soft hover:text-brand" href="{{ route('movies') }}">Movies</a></li>
          <li><a class="text-fg-soft hover:text-brand" href="{{ route('series') }}">Series</a></li>
          <li><a class="text-fg-soft hover:text-brand" href="{{ route('coming-soon') }}">Coming soon</a></li>
        </ul>
      </div>

      <div class="md:col-span-2">
        <h6 class="mb-4 text-xs font-semibold uppercase tracking-widest text-muted">Genres</h6>
        <ul class="space-y-2.5 text-sm">
          @foreach (['Action' => 'Action & Adventure', 'Drama' => 'Drama', 'Science Fiction' => 'Science Fiction', 'Horror' => 'Horror & Thriller', 'International Cinema' => 'International', 'Documentary' => 'Documentaries'] as $g => $l)
            <li><a class="text-fg-soft hover:text-brand" href="{{ route('browse', ['genre' => $g]) }}">{{ $l }}</a></li>
          @endforeach
        </ul>
      </div>

      <div class="md:col-span-4">
        <h6 class="mb-4 text-xs font-semibold uppercase tracking-widest text-muted">Latest releases</h6>
        @if ($latestReleases->isEmpty())
          <p class="text-sm text-muted">No releases yet.</p>
        @else
          <ul class="space-y-3">
            @foreach ($latestReleases as $entry)
              @php $item = $entry['item']; $isMovie = $entry['type'] === 'movie'; @endphp
              <li>
                <a class="group flex items-center gap-3" href="{{ $isMovie ? route('movies.show', $item) : route('series.show', $item) }}">
                  <span class="block h-14 w-10 shrink-0 overflow-hidden rounded-md bg-surface-3">
                    @if ($item->thumbnail)
                      <img class="size-full object-cover transition duration-300 group-hover:scale-110" src="{{ $item->thumbnailUrl() }}" alt="{{ $item->title }}" loading="lazy">
                    @endif
                  </span>
                  <span class="min-w-0">
                    <span class="block truncate text-sm font-semibold text-fg group-hover:text-brand">{{ $item->title }}</span>
                    @php $meta = array_filter([$item->genre, $item->year]); @endphp
                    <span class="text-xs text-muted">{{ implode(' · ', $meta) ?: ($isMovie ? 'Movie' : 'Series') }}</span>
                  </span>
                </a>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>

    <div class="flex flex-col items-center justify-between gap-3 border-t border-line py-6 text-xs text-muted sm:flex-row">
      <span>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
      <ul class="flex gap-6">
        <li><a class="hover:text-brand" href="{{ route('about') }}">About</a></li>
        <li><a class="hover:text-brand" href="{{ route('contact') }}">Contact</a></li>
        <li><a class="hover:text-brand" href="{{ route('pricing') }}">Pricing</a></li>
      </ul>
    </div>
  </div>
</footer>
