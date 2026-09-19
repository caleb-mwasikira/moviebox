@props(['type', 'id', 'saved' => false])

@auth
  <div x-data="watchlistToggle(@js($saved), '{{ route('watchlist.toggle', [$type, $id]) }}')">
    <button type="button" @click="toggle()" :disabled="busy" class="btn btn-secondary">
      <svg class="size-4" viewBox="0 0 24 24" :fill="watchlisted ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16Z"/></svg>
      <span x-text="watchlisted ? 'Saved' : 'Watch later'">Watch later</span>
    </button>
  </div>
@else
  <button type="button" onclick="watchOrLogin('{{ route('login') }}')" class="btn btn-secondary"><x-ui.icon name="bookmark" class="size-4" /> Watch later</button>
@endauth
