@extends('layouts.app')

@section('title', 'Gift Sent!')

@section('content')
<div class="container-app max-w-lg py-14 text-center lg:py-20">
  <span class="mx-auto mb-6 grid size-16 place-items-center rounded-2xl bg-brand-soft text-brand"><x-ui.icon name="gift" class="size-8" /></span>
  <h1 class="text-3xl font-extrabold text-fg">Gift sent!</h1>
  <p class="mx-auto mb-8 mt-3 max-w-sm text-sm leading-relaxed text-muted">
    <strong class="text-fg">{{ $gift->recipient_email }}</strong> will receive an email with a link to claim their
    <strong class="text-fg">{{ $gift->package->name }}</strong> subscription. The link expires in 30 days.
  </p>

  @if (session('success'))
    <div class="alert alert-success mb-6 text-left"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />{{ session('success') }}</div>
  @endif

  <div class="card card-pad mb-6 text-left">
    <div class="mb-4 flex items-start justify-between">
      <div>
        <p class="text-[11px] font-semibold uppercase tracking-widest text-muted">Gift subscription</p>
        <p class="text-lg font-bold text-fg">{{ $gift->package->name }}</p>
        <p class="text-xs text-muted">1 {{ ucfirst($gift->package->interval) }}</p>
      </div>
      <span class="badge badge-success">Sent</span>
    </div>
    <dl class="space-y-3 border-t border-line pt-4 text-sm">
      <div class="flex items-center justify-between"><dt class="text-muted">To</dt><dd class="font-medium text-fg">{{ $gift->recipient_email }}</dd></div>
      @if ($gift->expires_at)
        <div class="flex items-center justify-between"><dt class="text-muted">Claim by</dt><dd class="font-medium text-fg">{{ $gift->expires_at->format('F j, Y') }}</dd></div>
      @endif
    </dl>
  </div>

  <div class="mb-8 rounded-2xl border border-dashed border-line-strong p-5 text-left" x-data="{ open: {{ $errors->has('recipient_email') ? 'true' : 'false' }} }">
    <button type="button" @click="open = !open" class="flex w-full items-center justify-between text-left text-sm font-semibold text-fg">
      Wrong email address?
      <x-ui.icon name="chevron-down" class="size-4 text-muted transition" ::class="open && 'rotate-180'" />
    </button>
    <div x-show="open" x-cloak x-transition class="mt-4">
      <p class="mb-3 text-xs text-muted">Update the recipient's email and a new claim link will be sent to them.</p>
      <form method="POST" action="{{ route('gift.update-email', $gift) }}" class="flex gap-2">
        @csrf
        @method('PATCH')
        <input type="email" name="recipient_email" value="{{ $gift->recipient_email }}" required class="input flex-1">
        <button type="submit" class="btn btn-primary">Update</button>
      </form>
      @error('recipient_email')<p class="field-error">{{ $message }}</p>@enderror
    </div>
  </div>

  <a href="{{ route('movies') }}" class="btn btn-primary btn-lg">Continue watching</a>
</div>
@endsection
