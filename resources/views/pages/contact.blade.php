@extends('layouts.app')

@section('title', 'Contact')

@section('content')
<div class="container-app py-12 lg:py-16">
  <x-ui.page-header eyebrow="Get in touch" title="Contact us"
    subtitle="Have a question about your subscription, a film you'd like to see added, or a partnership opportunity? We'd love to hear from you." />

  <div class="grid gap-10 lg:grid-cols-3 lg:gap-14">
    {{-- Info --}}
    <div class="space-y-4">
      @foreach ([
        ['mail', 'Member support', 'support@moviebox.com', 'Billing, accounts & technical help'],
        ['film', 'Film & content licensing', 'licensing@moviebox.com', 'Submit your film or inquire about licensing'],
        ['message', 'Press & partnerships', 'press@moviebox.com', 'Media enquiries and brand collaborations'],
      ] as [$icon, $title, $email, $desc])
        <div class="card card-pad flex gap-4">
          <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-soft text-brand"><x-ui.icon :name="$icon" /></span>
          <div class="min-w-0">
            <h6 class="text-xs font-semibold uppercase tracking-wider text-muted">{{ $title }}</h6>
            <a class="mt-1 block truncate font-semibold text-fg hover:text-brand" href="mailto:{{ $email }}">{{ $email }}</a>
            <p class="mt-1 text-xs text-muted">{{ $desc }}</p>
          </div>
        </div>
      @endforeach

      <div class="card card-pad">
        <h6 class="mb-3 text-xs font-semibold uppercase tracking-wider text-muted">Follow {{ config('app.name') }}</h6>
        <ul class="flex gap-2">
          @foreach (['x' => ['X / Twitter', 'https://x.com'], 'instagram' => ['Instagram', 'https://instagram.com'], 'facebook' => ['Facebook', 'https://facebook.com'], 'discord' => ['Discord', 'https://discord.com']] as $icon => [$name, $url])
            <li>
              <a href="{{ $url }}" title="{{ $name }}" class="grid size-10 place-items-center rounded-xl bg-surface-2 text-fg-soft transition hover:bg-brand hover:text-white">
                <svg class="size-4" fill="currentColor"><use xlink:href="{{ asset('assets/img/social-icons.svg') }}#{{ $icon }}"></use></svg>
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </div>

    {{-- Form --}}
    <div class="lg:col-span-2">
      <div class="card card-pad sm:p-8">
        @if (session('status') === 'message-sent')
          <div class="alert alert-success mb-6"><x-ui.icon name="check-circle" class="mt-0.5 size-5 shrink-0" />Thanks for reaching out! We'll get back to you within 1–2 business days.</div>
        @endif

        <form method="POST" action="{{ route('contact.store') }}" class="space-y-5" x-data="{ loading: false }" @submit="loading = true">
          @csrf
          <div class="grid gap-5 sm:grid-cols-2">
            <x-ui.field name="name" label="Your name" :value="old('name')" autocomplete="name" />
            <x-ui.field name="email" type="email" label="Your email" :value="old('email')" autocomplete="email" />
          </div>
          <div>
            <label class="label" for="message">Your message</label>
            <textarea name="message" id="message" rows="7" class="textarea @error('message') input-error @enderror"
              placeholder="Tell us about your subscription issue, a film request, or anything else on your mind.">{{ old('message') }}</textarea>
            @error('message')<p class="field-error">{{ $message }}</p>@enderror
          </div>
          <x-ui.submit class="btn btn-primary btn-lg" loading="Sending…">Send message</x-ui.submit>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
