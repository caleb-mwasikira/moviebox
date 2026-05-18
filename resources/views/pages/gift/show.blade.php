@extends('layouts.app')

@section('title', 'Send a Gift — ' . $package->name)

@section('content')
@php $isFree = $package->price == 0; @endphp

<main class="grow" x-data="giftPage()" x-init="init()">
  <div class="py-16 lg:py-24">
    <div class="container max-w-xl">

      {{-- Back link --}}
      <a href="{{ route('pricing') }}" class="inline-flex items-center gap-x-1.5 text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-10">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Plans
      </a>

      {{-- Gift header --}}
      <div class="mb-8">
        <p class="text-xs font-bold uppercase tracking-widest text-accent mb-2">Gift Subscription</p>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Send the gift of streaming</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Treat someone to movies and series — no account required for them to receive it.</p>
      </div>

      {{-- Package summary --}}
      <div class="border border-gray-200 dark:border-gray-600 px-6 py-5 mb-8 flex items-center justify-between">
        <div>
          <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-0.5">Selected Plan</p>
          <p class="text-base font-bold text-gray-900 dark:text-white">{{ $package->name }}</p>
          @if ($package->description)
            <p class="text-xs text-gray-500 mt-0.5">{{ $package->description }}</p>
          @endif
        </div>
        <div class="text-right">
          @if ($isFree)
            <p class="text-2xl font-black text-accent">Free</p>
            <p class="text-xs text-gray-500">per {{ $package->interval }}</p>
          @else
            <p class="text-2xl font-black text-gray-900 dark:text-white">
              {{ $package->currency }}&nbsp;{{ number_format($package->price, 0) }}
            </p>
            <p class="text-xs text-gray-500">per {{ $package->interval }}</p>
          @endif
        </div>
      </div>

      {{-- Form step --}}
      <div x-show="step === 'form'">

        <form method="POST" action="{{ route('gift.store', $package->uuid) }}"
          @if($isFree) x-data="{ loading: false }" @submit="loading = true" @endif
          @if(!$isFree) @submit.prevent="sendGift()" @endif
          id="gift-form">
          @csrf

          @if ($errors->any())
            <div class="mb-6 border border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700 px-5 py-4">
              <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                  <li class="text-sm text-red-600 dark:text-red-400">{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          {{-- Recipient email --}}
          <div class="mb-6">
            <label for="recipient_email" class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">
              Recipient's Email Address
            </label>
            <input type="email" id="recipient_email" name="recipient_email"
              value="{{ old('recipient_email') }}"
              placeholder="friend@example.com"
              required autocomplete="off"
              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-accent outline-none transition-colors {{ $errors->has('recipient_email') ? '!border-red-400' : '' }}">
            <p class="mt-1.5 text-[11px] text-gray-400">They'll receive an email with a link to claim the subscription.</p>
          </div>

          {{-- Optional message --}}
          <div class="mb-6">
            <label for="message" class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">
              Personal Message <span class="font-normal normal-case tracking-normal">(optional)</span>
            </label>
            <textarea id="message" name="message" rows="3" maxlength="500"
              placeholder="Add a personal note to make it special…"
              class="w-full border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-accent outline-none transition-colors resize-none">{{ old('message') }}</textarea>
            <p class="mt-1.5 text-[11px] text-gray-400">Up to 500 characters. Will appear in the gift email.</p>
          </div>

          @if ($isFree)
            {{-- Free gift: simple submit --}}
            <button type="submit" :disabled="loading"
              class="w-full flex items-center justify-center gap-x-2 font-bold uppercase tracking-wide py-4 text-sm bg-gray-900 hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
              <svg x-show="loading" class="animate-spin h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              <span x-text="loading ? 'Sending…' : '🎁 Send Gift'"></span>
            </button>
          @else
            {{-- Paid gift: M-Pesa phone input + pay button --}}
            <div class="border-t border-gray-100 dark:border-gray-700 pt-6 mt-2">
              <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4">Payment Method</p>

              <div class="flex items-center gap-x-3 border border-accent bg-accent/5 dark:bg-accent/10 p-4 mb-6">
                <span class="text-xl leading-none">🇰🇪</span>
                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">M-Pesa STK Push</span>
              </div>

              <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">
                Your M-Pesa Phone Number
              </label>
              <div class="flex mb-1.5">
                <span class="inline-flex items-center px-3.5 border border-r-0 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm font-bold text-gray-700 dark:text-gray-300 select-none whitespace-nowrap">
                  🇰🇪 +254
                </span>
                <input type="tel" x-model="phone" @input="phoneError = ''" @keydown.enter.prevent="sendGift()"
                  x-ref="phoneInput"
                  placeholder="712 345 678" maxlength="10" autocomplete="tel"
                  class="flex-1 min-w-0 border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-accent outline-none transition-colors"
                  :class="phoneError ? '!border-red-400' : ''">
              </div>
              <p class="text-[11px] min-h-4 mb-6"
                :class="phoneError ? 'text-red-500' : 'text-gray-400'"
                x-text="phoneError || 'You pay — your friend receives the subscription by email.'">
              </p>

              <button type="button" @click="sendGift()" :disabled="loading"
                class="w-full flex items-center justify-center gap-x-2 font-bold uppercase tracking-wide py-4 text-sm bg-gray-900 hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                <svg x-show="loading" class="h-4 w-4 animate-spin fill-current shrink-0" viewBox="0 0 24 24">
                  <path d="M12 2v4a8 8 0 0 1 8 8h4A12 12 0 0 0 12 2z"/>
                </svg>
                <span x-text="loading ? 'Processing…' : '🎁 Pay & Send Gift — {{ $package->currency }} {{ number_format($package->price, 0) }}'"></span>
              </button>
            </div>
          @endif
        </form>

      </div>

      @if (!$isFree)
      {{-- Waiting for PIN --}}
      <div x-show="step === 'waiting'" x-transition class="py-8 text-center border border-gray-200 dark:border-gray-600 px-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-green-100 dark:bg-green-900/30 mb-5">
          <svg class="w-7 h-7 text-green-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Check your phone</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
          Prompt sent to <span class="font-semibold text-gray-700 dark:text-gray-200" x-text="displayPhone"></span>
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Enter your M-Pesa PIN to complete payment.</p>
        <div class="flex items-center justify-center gap-x-1.5 mb-8">
          <span class="w-2 h-2 bg-accent rounded-full animate-bounce [animation-delay:-0.3s]"></span>
          <span class="w-2 h-2 bg-accent rounded-full animate-bounce [animation-delay:-0.15s]"></span>
          <span class="w-2 h-2 bg-accent rounded-full animate-bounce"></span>
        </div>
        <div class="flex items-center justify-between border-t border-gray-100 dark:border-gray-700 pt-4 text-xs text-gray-400">
          <span>Expires in <span class="font-bold text-gray-600 dark:text-gray-300" x-text="countdown + 's'"></span></span>
          <button @click="step = 'form'; loading = false" class="font-bold uppercase hover:text-red-500 transition-colors">Cancel</button>
        </div>
      </div>

      {{-- Failed --}}
      <div x-show="step === 'failed'" x-transition class="py-8 text-center border border-gray-200 dark:border-gray-600 px-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-red-100 dark:bg-red-900/30 mb-5">
          <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Payment not completed</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8" x-text="errorMsg || 'Cancelled or timed out. Please try again.'"></p>
        <button @click="step = 'form'; errorMsg = ''"
          class="inline-flex font-bold uppercase tracking-wide py-3 px-8 text-xs bg-gray-900 text-white hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors">
          Try Again
        </button>
      </div>
      @endif

    </div>
  </div>
</main>
@endsection

@if (!$isFree)
@push('scripts')
<script>
function giftPage() {
  return {
    step: 'form',
    phone: '',
    displayPhone: '',
    phoneError: '',
    loading: false,
    errorMsg: '',
    checkoutRequestId: '',
    giftId: null,
    giftToken: null,
    countdown: 60,
    _pollTimer: null,
    _countdownTimer: null,

    init() {
      this.$nextTick(() => this.$refs.phoneInput?.focus());
    },

    validatePhone() {
      const raw = this.phone.replace(/\s+/g, '');
      if (!raw) { this.phoneError = 'Phone number is required.'; return false; }
      if (!/^(0[71]\d{8}|[71]\d{8})$/.test(raw)) {
        this.phoneError = 'Enter a valid Kenyan number (e.g. 0712 345 678).';
        return false;
      }
      return true;
    },

    formatDisplay(raw) {
      const stripped = raw.replace(/\s+/g, '').replace(/^0/, '');
      return '+254 ' + stripped.replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3');
    },

    getFormData() {
      const form = document.getElementById('gift-form');
      return {
        recipient_email: form.querySelector('[name=recipient_email]').value.trim(),
        message: form.querySelector('[name=message]').value.trim(),
      };
    },

    async sendGift() {
      if (!this.validatePhone()) return;
      const formData = this.getFormData();
      if (!formData.recipient_email) { return; }

      this.loading = true;
      this.phoneError = '';
      this.displayPhone = this.formatDisplay(this.phone);

      try {
        // Step 1: create the pending gift record
        const giftRes = await fetch('{{ route('gift.store', $package->uuid) }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
          body: JSON.stringify(formData),
        });

        const giftData = await giftRes.json();

        if (!giftRes.ok) {
          this.phoneError = giftData.message || 'Something went wrong. Please try again.';
          this.loading = false;
          return;
        }

        this.giftId    = giftData.gift_id;
        this.giftToken = giftData.token;

        // Step 2: initiate M-Pesa with the gift id linked
        const payRes = await fetch('{{ route('mpesa.initiate') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            phone: this.phone,
            package_uuid: '{{ $package->uuid }}',
            gift_id: this.giftId,
          }),
        });

        const payData = await payRes.json();

        if (payData.success) {
          this.checkoutRequestId = payData.checkout_request_id;
          this.step = 'waiting';
          this.loading = false;
          this.startPolling();
        } else {
          this.phoneError = payData.errors?.phone?.[0] || payData.message || 'Payment initiation failed.';
          this.loading = false;
        }
      } catch (e) {
        this.phoneError = 'Network error. Please try again.';
        this.loading = false;
      }
    },

    startPolling() {
      this.countdown = 60;
      this._countdownTimer = setInterval(() => {
        this.countdown--;
        if (this.countdown <= 0) {
          clearInterval(this._countdownTimer);
          clearInterval(this._pollTimer);
          if (this.step === 'waiting') {
            this.errorMsg = 'Payment timed out. Please try again.';
            this.step = 'failed';
          }
        }
      }, 1000);

      const statusBase = '{{ route('mpesa.status', ['checkoutRequestId' => '__ID__']) }}'.replace('__ID__', '');
      this._pollTimer = setInterval(async () => {
        try {
          const res  = await fetch(statusBase + this.checkoutRequestId, { headers: { 'Accept': 'application/json' } });
          const data = await res.json();
          if (data.status === 'paid') {
            clearInterval(this._pollTimer);
            clearInterval(this._countdownTimer);
            const sentBase = '{{ route('gift.sent', '__TOKEN__') }}';
            window.location.href = this.giftToken
              ? sentBase.replace('__TOKEN__', this.giftToken)
              : '{{ route('account.billing') }}';
          } else if (data.status === 'failed' || data.status === 'cancelled') {
            clearInterval(this._pollTimer);
            clearInterval(this._countdownTimer);
            this.errorMsg = data.message || '';
            this.step = 'failed';
          }
        } catch (e) { /* keep polling */ }
      }, 4000);
    },
  };
}
</script>
@endpush
@endif
