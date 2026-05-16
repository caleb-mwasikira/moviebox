<div style="display:flex;flex-direction:column;align-items:center;gap:0.75rem;width:120px;">

  <form method="POST" action="{{ route('profiles.select.post') }}"
    style="display:flex;flex-direction:column;align-items:center;gap:0.75rem;">
    @csrf
    <input type="hidden" name="profile_id" value="{{ $profile->id }}">
    <button type="submit" style="background:none;border:none;cursor:pointer;padding:0;display:flex;flex-direction:column;align-items:center;gap:0.75rem;">
      <div style="width:96px;height:96px;border-radius:4px;background:{{ $profile->avatar_color }};display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:#fff;letter-spacing:0.05em;transition:opacity 0.15s;"
        onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
        {{ $profile->initials() }}
      </div>
      <span style="color:#ccc;font-size:0.875rem;font-weight:600;text-align:center;">{{ $profile->name }}</span>
      @if ($label) <span style="color:#888;font-size:0.75rem;">{{ $label }}</span> @endif
    </button>
  </form>

  @if ($profile->is_kids)
    <span style="background:#f59e0b;color:#000;font-size:0.625rem;font-weight:700;text-transform:uppercase;padding:1px 6px;border-radius:2px;letter-spacing:0.05em;margin-top:-0.25rem;">
      Kids
    </span>
  @endif

</div>
