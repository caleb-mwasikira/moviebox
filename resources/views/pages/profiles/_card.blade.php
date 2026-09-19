<div class="flex w-28 flex-col items-center gap-2 sm:w-32">
  <form method="POST" action="{{ route('profiles.select.post') }}">
    @csrf
    <input type="hidden" name="profile_id" value="{{ $profile->id }}">
    <button type="submit" class="group flex flex-col items-center gap-3">
      <span class="grid size-24 place-items-center rounded-2xl text-3xl font-bold text-white shadow-card ring-2 ring-transparent transition duration-200 group-hover:-translate-y-1 group-hover:ring-brand sm:size-28"
        style="background: {{ $profile->avatar_color }}">{{ $profile->initials() }}</span>
      <span class="text-sm font-semibold text-fg transition-colors group-hover:text-brand">{{ $profile->name }}</span>
      @if ($label)<span class="-mt-2 text-xs text-muted">{{ $label }}</span>@endif
    </button>
  </form>
  @if ($profile->is_kids)<span class="badge badge-warning">Kids</span>@endif
</div>
