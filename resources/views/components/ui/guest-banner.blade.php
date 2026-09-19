@if (session('shared_guest'))
  @php $guestExpiry = \Carbon\Carbon::createFromTimestamp(session('shared_guest_expires'))->format('g:i A \o\n M j'); @endphp
  <div class="border-b border-brand/20 bg-brand-soft">
    <div class="container-app flex items-center gap-2 py-2.5 text-xs font-medium text-fg">
      <x-ui.icon name="info" class="size-4 shrink-0 text-brand" />
      You're watching as a guest on {{ session('shared_guest_name') }}'s account — access expires at {{ $guestExpiry }}.
    </div>
  </div>
@endif
