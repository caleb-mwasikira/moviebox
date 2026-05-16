@extends('layouts.admin')

@section('admin-title', 'Contact Messages')

@section('admin-content')
<div class="mb-8 flex flex-wrap items-center justify-between gap-4">
  <div>
    <h2 class="text-2xl font-bold tracking-tighter text-gray-900 dark:text-white lg:text-4xl">Messages</h2>
    <p class="mt-1 text-gray-500 dark:text-gray">
      {{ $contacts->count() }} {{ Str::plural('message', $contacts->count()) }}
      @if ($unreadCount > 0)
        &mdash; <span class="text-accent font-bold">{{ $unreadCount }} unread</span>
      @endif
    </p>
  </div>
</div>

@if ($contacts->isEmpty())
  <div class="py-16 text-center border border-dashed border-gray-300 dark:border-gray-600">
    <p class="text-sm font-bold uppercase text-gray-400 tracking-widest">No messages yet</p>
  </div>
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
<style>
  .inbox-wrap    { display: flex; }
  .inbox-sidebar { width: 300px; flex-shrink: 0; overflow-y: auto; border-right: 1px solid; }
  .inbox-detail  { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
  .inbox-back    { display: none; }

  @media (max-width: 767px) {
    .inbox-wrap            { flex-direction: column; height: auto !important; }
    .inbox-sidebar         { width: 100%; border-right: none; border-bottom: 1px solid; max-height: 45vh; }
    .inbox-detail          { min-height: 55vh; }
    .inbox-mobile-hidden   { display: none !important; }
    .inbox-back            { display: flex; }
  }
</style>

<script>window._contactMessages = @json($inboxData);</script>

<div
  x-data="contactInbox(window._contactMessages)"
  class="inbox-wrap border border-gray-200 dark:border-gray-600 overflow-hidden"
  style="height: 70vh;"
>

  {{-- ── Inbox list ── --}}
  <div class="inbox-sidebar border-gray-200 dark:border-gray-600"
    :class="{ 'inbox-mobile-hidden': mobileView === 'detail' }">

    <template x-if="messages.length === 0">
      <div class="py-12 text-center text-sm text-gray-400 font-bold uppercase tracking-widest">
        No messages
      </div>
    </template>

    <template x-for="msg in messages" :key="msg.id">
      <button
        type="button"
        @click="select(msg.id)"
        :class="{
          'bg-accent/10 border-l-2 border-accent': activeId === msg.id,
          'border-l-2 border-transparent': activeId !== msg.id,
          'opacity-60': !msg.is_unread && activeId !== msg.id,
        }"
        class="w-full text-left px-4 py-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors focus:outline-none"
      >
        <div class="flex items-center justify-between gap-x-2 mb-1">
          <span class="font-bold text-sm text-gray-900 dark:text-white truncate" x-text="msg.name"></span>
          <span class="shrink-0 text-[10px] text-gray-400 font-medium" x-text="msg.created_at"></span>
        </div>
        <div class="flex items-center gap-x-1.5">
          <template x-if="msg.is_unread">
            <span class="w-1.5 h-1.5 rounded-full bg-accent shrink-0 mt-px"></span>
          </template>
          <span class="text-xs text-gray-500 truncate" x-text="msg.email"></span>
        </div>
        <p class="mt-1 text-xs text-gray-400 leading-snug line-clamp-2" x-text="msg.preview"></p>
      </button>
    </template>

  </div>

  {{-- ── Message detail ── --}}
  <div class="inbox-detail"
    :class="{ 'inbox-mobile-hidden': mobileView === 'list' }">

    {{-- Mobile back button --}}
    <div class="inbox-back items-center gap-x-2 px-4 py-2 border-b border-gray-200 dark:border-gray-600">
      <button type="button" @click="goBack()"
        class="inline-flex items-center gap-x-1.5 text-xs font-bold uppercase text-gray-500 hover:text-accent transition-colors">
        <svg class="h-3 w-3 fill-current rotate-180" viewBox="0 0 6 10"><path d="M1 1l4 4-4 4"/></svg>
        All Messages
      </button>
    </div>

    {{-- Empty state --}}
    <template x-if="!activeId && !loading">
      <div class="flex items-center justify-center text-sm font-bold uppercase text-gray-300 dark:text-gray-600 tracking-widest select-none" style="height:100%;">
        Select a message to read
      </div>
    </template>

    {{-- Loading --}}
    <template x-if="loading">
      <div class="flex items-center justify-center" style="height:100%;">
        <svg class="h-6 w-6 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
        </svg>
      </div>
    </template>

    {{-- Message body --}}
    <template x-if="active && !loading">
      <div class="flex flex-col overflow-hidden" style="height:100%;">

        {{-- Header --}}
        <div class="shrink-0 flex items-center justify-between gap-x-3 px-5 py-3 border-b border-gray-200 dark:border-gray-600">
          <div class="min-w-0">
            <div class="flex items-baseline gap-x-2 flex-wrap">
              <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="active.name"></span>
              <a :href="'mailto:' + active.email" class="text-xs text-accent hover:underline" x-text="active.email"></a>
            </div>
            <p class="text-[11px] text-gray-400 mt-0.5" x-text="active.created_at"></p>
          </div>

          {{-- Actions --}}
          <div class="flex items-center gap-x-1.5 shrink-0">
            <a :href="'mailto:' + active.email"
              class="inline-flex items-center gap-x-1 text-[10px] font-bold uppercase px-2 py-1 bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
              <svg class="h-2.5 w-2.5 fill-current" viewBox="0 0 24 24"><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
              Reply
            </a>
            <button type="button" @click="remove(active.id)"
              class="inline-flex items-center gap-x-1 text-[10px] font-bold uppercase px-2 py-1 border border-gray-300 dark:border-gray-600 text-gray-500 hover:border-red-500 hover:text-red-500 transition-colors">
              <svg class="h-2.5 w-2.5 fill-current" viewBox="0 0 24 24"><path d="M9 3h6l1 1h4v2H4V4h4L9 3zM5 7h14l-1 14H6L5 7zm4 2v9h2V9H9zm4 0v9h2V9h-2z"/></svg>
              Delete
            </button>
          </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-4">
          <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap" x-text="active.message"></p>
        </div>

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
