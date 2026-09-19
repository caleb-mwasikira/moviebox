@extends('layouts.admin')

@section('admin-title', 'Contact Messages')

@section('admin-content')
<x-ui.page-header title="Messages">
  <x-slot:actions></x-slot:actions>
</x-ui.page-header>
<p class="-mt-4 text-sm text-muted">
  {{ $contacts->count() }} {{ Str::plural('message', $contacts->count()) }}
  @if ($unreadCount > 0)&mdash; <span class="font-semibold text-brand">{{ $unreadCount }} unread</span>@endif
</p>

@if ($contacts->isEmpty())
  <x-ui.empty icon="inbox" title="No messages yet" />
@else
  @php
  $inboxData = $contacts->map(fn ($c) => [
      'id'         => $c->id,
      'name'       => $c->name,
      'email'      => $c->email,
      'preview'    => Str::limit($c->message, 80),
      'created_at' => $c->created_at->diffForHumans(),
      'is_unread'  => $c->isUnread(),
  ])->values();
  @endphp
  <script>window._contactMessages = @json($inboxData);</script>

  <div x-data="contactInbox(window._contactMessages)" class="card flex min-h-[32rem] flex-col overflow-hidden md:h-[calc(100vh-15rem)] md:flex-row">
    {{-- Inbox list --}}
    <div class="max-h-[45vh] w-full shrink-0 overflow-y-auto border-b border-line md:max-h-none md:w-80 md:border-b-0 md:border-r" :class="mobileView === 'detail' && 'hidden md:block'">
      <template x-if="messages.length === 0"><div class="p-8 text-center text-sm text-muted">No messages</div></template>
      <template x-for="msg in messages" :key="msg.id">
        <button type="button" @click="select(msg.id)"
          class="block w-full border-b border-l-2 border-line px-4 py-3.5 text-left transition hover:bg-surface-2"
          :class="{ 'border-l-brand bg-brand-soft': activeId === msg.id, 'border-l-transparent': activeId !== msg.id, 'opacity-60': !msg.is_unread && activeId !== msg.id }">
          <div class="flex items-baseline justify-between gap-2">
            <span class="truncate text-sm font-semibold text-fg" x-text="msg.name"></span>
            <span class="shrink-0 text-[11px] text-muted" x-text="msg.created_at"></span>
          </div>
          <div class="mt-0.5 flex items-center gap-2">
            <template x-if="msg.is_unread"><span class="size-2 shrink-0 rounded-full bg-brand"></span></template>
            <span class="truncate text-xs text-muted" x-text="msg.email"></span>
          </div>
          <p class="mt-1 line-clamp-2 text-xs text-fg-soft" x-text="msg.preview"></p>
        </button>
      </template>
    </div>

    {{-- Message detail --}}
    <div class="flex min-h-[24rem] min-w-0 flex-1 flex-col" :class="mobileView === 'list' && 'hidden md:flex'">
      <div class="border-b border-line p-3 md:hidden">
        <button type="button" @click="goBack()" class="btn btn-ghost btn-sm"><x-ui.icon name="arrow-left" class="size-4" /> All messages</button>
      </div>

      <template x-if="!activeId && !loading">
        <div class="flex flex-1 flex-col items-center justify-center gap-2 p-8 text-center text-sm text-muted"><x-ui.icon name="inbox" class="size-8" /> Select a message to read</div>
      </template>
      <template x-if="loading">
        <div class="flex flex-1 items-center justify-center"><svg class="size-6 animate-spin text-brand" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></div>
      </template>

      <template x-if="active && !loading">
        <div class="flex min-h-0 flex-1 flex-col">
          <div class="flex flex-wrap items-start justify-between gap-4 border-b border-line p-5">
            <div class="min-w-0">
              <div class="flex flex-wrap items-baseline gap-x-3">
                <span class="text-lg font-bold text-fg" x-text="active.name"></span>
                <a :href="'mailto:' + active.email" class="text-sm text-brand hover:underline" x-text="active.email"></a>
              </div>
              <p class="mt-0.5 text-xs text-muted" x-text="active.created_at"></p>
            </div>
            <div class="flex gap-2">
              <a :href="'mailto:' + active.email" class="btn btn-primary btn-sm"><x-ui.icon name="mail" class="size-3.5" /> Reply</a>
              <button type="button" @click="remove(active.id)" class="btn btn-danger-soft btn-sm">Delete</button>
            </div>
          </div>
          <div class="flex-1 overflow-y-auto p-5"><p class="whitespace-pre-wrap text-sm leading-relaxed text-fg-soft" x-text="active.message"></p></div>
        </div>
      </template>
    </div>
  </div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', function () {
  var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  Alpine.data('contactInbox', function (initialMessages) {
    return {
      messages:    initialMessages,
      activeId:    null,
      active:      null,
      loading:     false,
      mobileView:  'list',

      goBack: function () {
        this.mobileView = 'list';
        this.activeId   = null;
        this.active     = null;
      },

      select: function (id) {
        if (this.activeId === id) return;
        this.activeId   = id;
        this.active     = null;
        this.loading    = true;
        this.mobileView = 'detail';

        var self = this;
        fetch('/admin/contacts/' + id, {
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          self.active  = data;
          self.loading = false;
          var msg = self.messages.find(function (m) { return m.id === id; });
          if (msg) msg.is_unread = false;
        })
        .catch(function () { self.loading = false; });
      },

      remove: function (id) {
        if (!confirm('Delete this message?')) return;
        var self = this;
        fetch('/admin/contacts/' + id, {
          method:  'DELETE',
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (!data.ok) return;
          self.messages   = self.messages.filter(function (m) { return m.id !== id; });
          self.activeId   = null;
          self.active     = null;
          self.mobileView = 'list';
        })
        .catch(function () {});
      },
    };
  });
});
</script>
@endpush
