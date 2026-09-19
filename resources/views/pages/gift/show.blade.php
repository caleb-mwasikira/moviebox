@extends('layouts.app')

@section('title', 'Send a Gift — ' . $package->name)

@section('content')
@php $isFree = $package->price == 0; @endphp

<div class="container-app max-w-xl py-12 lg:py-16" x-data="giftPage()" x-init="init()">
  <a href="{{ route('pricing') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-brand">
    <x-ui.icon name="arrow-left" class="size-4" /> Back to plans
  </a>

  {{-- Plan summary --}}
  <div class="card card-pad mb-6 flex items-center justify-between gap-4">
    <div class="flex min-w-0 items-center gap-4">
      <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-pink-500/12 text-pink-500"><x-ui.icon name="gift" class="size-6" /></span>
      <div class="min-w-0">
        <p class="text-[11px] font-semibold uppercase tracking-widest text-muted">Gift — selected plan</p>
        <p class="text-lg font-bold text-fg">{{ $package->name }}</p>
        @if ($package->description)<p class="text-xs text-muted">{{ $package->description }}</p>@endif
      </div>
    </div>
    <div class="shrink-0 text-right">
      @if ($isFree)<p class="text-3xl font-extrabold text-brand">Free</p>
      @else<p class="text-3xl font-extrabold text-fg">{{ $package->currency }}&nbsp;{{ number_format($package->price, 0) }}</p>@endif
      <p class="text-xs text-muted">per {{ $package->interval }}</p>
    </div>
  </div>

  {{-- Steps --}}
  <div x-show="!['waiting','success','failed'].includes(step)" class="mb-8 flex items-center">
    <div class="flex items-center gap-2">
      <span class="grid size-7 place-items-center rounded-full text-xs font-bold transition" :class="step === 'recipient' ? 'bg-brand text-white' : 'bg-brand-soft text-brand'">1</span>
      <span class="text-xs font-semibold uppercase tracking-wider" :class="step === 'recipient' ? 'text-fg' : 'text-muted'">Recipient</span>
    </div>
    <div class="mx-4 h-px flex-1 bg-line"></div>
    <div class="flex items-center gap-2">
      <span class="grid size-7 place-items-center rounded-full text-xs font-bold transition" :class="step === 'payment' ? 'bg-brand text-white' : 'bg-surface-3 text-muted'">2</span>
      <span class="text-xs font-semibold uppercase tracking-wider" :class="step === 'payment' ? 'text-fg' : 'text-muted'">Payment</span>
    </div>
  </div>

  {{-- Step 1: recipient --}}
  <div x-show="step === 'recipient'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" class="card card-pad">
    @if ($errors->any())
      <div class="alert alert-error mb-6 !block"><ul class="space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="mb-6">
      <label for="recipient_email" class="label">Recipient's email address</label>
      <input type="email" id="recipient_email" x-model="recipientEmail" @input="recipientError = ''" @keydown.enter.prevent="nextStep()"
        value="{{ old('recipient_email') }}" placeholder="friend@example.com" autocomplete="off"
        class="input" :class="recipientError ? 'input-error' : ''">
      <p class="mt-1.5 min-h-4 text-xs" :class="recipientError ? 'font-medium text-red-500' : 'text-muted'"
        x-text="recipientError || 'They\'ll receive an email with a link to claim the subscription.'"></p>
    </div>

    <div class="mb-6">
      <label for="message" class="label">Personal message <span class="font-normal normal-case tracking-normal">(optional)</span></label>
      <textarea id="message" x-model="message" rows="3" maxlength="500" placeholder="Add a personal note to make it special…" class="textarea resize-none">{{ old('message') }}</textarea>
      <p class="hint">Up to 500 characters. Will appear in the gift email.</p>
    </div>

    <button type="button" @click="nextStep()" class="btn btn-primary btn-lg btn-block">Continue <x-ui.icon name="arrow-right" class="size-4" /></button>
  </div>

  {{-- Step 2: payment --}}
  <div x-show="step === 'payment'" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0">
    <div class="panel mb-6 flex items-center justify-between px-4 py-3">
      <div class="min-w-0">
        <p class="text-[11px] font-semibold uppercase tracking-widest text-muted">Sending to</p>
        <p class="truncate text-sm font-semibold text-fg" x-text="recipientEmail"></p>
      </div>
      <button type="button" @click="step = 'recipient'" class="ml-4 shrink-0 text-xs font-semibold text-brand hover:underline">Edit</button>
    </div>

    @if ($isFree)
      <form method="POST" action="{{ route('gift.store', $package->uuid) }}" x-data="{ loading: false }" @submit="loading = true" id="gift-form-free">
        @csrf
        <input type="hidden" name="recipient_email" :value="recipientEmail">
        <input type="hidden" name="message" :value="message">
        <div class="card card-pad mb-6 py-10 text-center">
          <span class="mx-auto mb-4 grid size-14 place-items-center rounded-2xl bg-brand-soft text-brand"><x-ui.icon name="gift" class="size-7" /></span>
          <h2 class="text-lg font-bold text-fg">Ready to send</h2>
          <p class="mt-1 text-sm text-muted">No payment required — click below to send the gift instantly.</p>
        </div>
        <x-ui.submit loading="Sending…">Send gift</x-ui.submit>
      </form>
    @else
      <p class="label mb-3">Payment method</p>
      <div class="mb-6 grid grid-cols-2 gap-3">
        <div class="cursor-not-allowed select-none rounded-2xl border border-line p-4 opacity-50">
          <div class="mb-3 flex items-center gap-3"><x-ui.icon name="credit-card" class="size-6 text-muted" /><span class="text-sm font-semibold text-fg">Credit card</span></div>
          <span class="badge badge-neutral">Coming soon</span>
        </div>
        <button type="button" @click="method = 'mpesa'" :class="method === 'mpesa' ? 'border-brand bg-brand-soft' : 'border-line hover:border-line-strong'" class="rounded-2xl border p-4 text-left transition">
          <div class="mb-3 flex items-center justify-between">
            <div class="flex items-center gap-3"><span class="text-xl leading-none">🇰🇪</span><span class="text-sm font-semibold text-fg">M-Pesa</span></div>
            <span :class="method === 'mpesa' ? 'border-brand bg-brand' : 'border-line-strong'" class="grid size-4 place-items-center rounded-full border-2"><span x-show="method === 'mpesa'" class="size-1.5 rounded-full bg-white"></span></span>
          </div>
          <span class="text-xs text-muted">Safaricom STK Push</span>
        </button>
      </div>

      <div x-show="method === 'mpesa'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="card card-pad mb-6">
        <label class="label">Your M-Pesa phone number</label>
        <div class="flex">
          <span class="inline-flex select-none items-center whitespace-nowrap rounded-l-xl border border-r-0 border-line-strong bg-surface-2 px-3.5 text-sm font-semibold text-fg">🇰🇪 +254</span>
          <input type="tel" x-model="phone" @input="phoneError = ''" @keydown.enter.prevent="pay()" x-ref="phoneInput" placeholder="712 345 678" maxlength="10" autocomplete="tel" class="input !rounded-l-none" :class="phoneError ? 'input-error' : ''">
        </div>
        <p class="mt-1.5 min-h-4 text-xs" :class="phoneError ? 'font-medium text-red-500' : 'text-muted'" x-text="phoneError || 'You pay — your friend receives the subscription by email.'"></p>
      </div>

      <button type="button" @click="pay()" :disabled="!method || loading" class="btn btn-primary btn-lg btn-block">
        <svg x-show="loading" x-cloak class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
        <span x-text="loading ? 'Processing…' : 'Pay with M-Pesa — {{ $package->currency }} {{ number_format($package->price, 0) }}'"></span>
      </button>
    @endif
  </div>

  @if (!$isFree)
    <div x-show="step === 'waiting'" x-cloak x-transition class="card card-pad py-10 text-center">
      <span class="mx-auto mb-5 grid size-14 animate-pulse place-items-center rounded-2xl bg-emerald-500/12 text-emerald-500"><x-ui.icon name="phone" class="size-7" /></span>
      <h2 class="text-xl font-bold text-fg">Check your phone</h2>
      <p class="mt-2 text-sm text-muted">Prompt sent to <span class="font-semibold text-fg" x-text="displayPhone"></span></p>
      <p class="mb-6 text-sm text-muted">Enter your M-Pesa PIN to complete payment.</p>
      <div class="mb-8 flex items-center justify-center gap-1.5">
        <span class="size-2 animate-bounce rounded-full bg-brand [animation-delay:-0.3s]"></span>
        <span class="size-2 animate-bounce rounded-full bg-brand [animation-delay:-0.15s]"></span>
        <span class="size-2 animate-bounce rounded-full bg-brand"></span>
      </div>
      <div class="flex items-center justify-between border-t border-line pt-4 text-xs text-muted">
        <span>Expires in <span class="font-bold text-fg" x-text="countdown + 's'"></span></span>
        <button type="button" @click="cancelPayment()" class="font-semibold hover:text-red-500">Cancel</button>
      </div>
    </div>

    <div x-show="step === 'failed'" x-cloak x-transition class="card card-pad py-10 text-center">
      <span class="mx-auto mb-5 grid size-14 place-items-center rounded-2xl bg-red-500/12 text-red-500"><x-ui.icon name="alert" class="size-7" /></span>
      <h2 class="text-xl font-bold text-fg">Payment not completed</h2>
      <p class="mt-2 text-sm text-muted" x-text="errorMsg || 'Cancelled or timed out. Please try again.'"></p>
      <button type="button" @click="step = 'payment'; errorMsg = ''" class="btn btn-primary mt-8">Try again</button>
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
function giftPage() {
  return {
    step: 'recipient',
    recipientEmail: '{{ old('recipient_email') }}',
    message: '',
    recipientError: '',

    method: 'mpesa',
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
      document.getElementById('recipient_email')?.focus();
    },

    nextStep() {
      const email = this.recipientEmail.trim();
      if (!email) {
        this.recipientError = 'Recipient email is required.';
        return;
      }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        this.recipientError = 'Enter a valid email address.';
        return;
      }
      this.recipientError = '';
      this.step = 'payment';
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

    async pay() {
      if (this.method !== 'mpesa') return;
      if (!this.validatePhone()) return;

      this.loading = true;
      this.phoneError = '';
      this.displayPhone = this.formatDisplay(this.phone);

      try {
        // Step A: create the pending gift record
        const giftRes = await fetch('{{ route('gift.store', $package->uuid) }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            recipient_email: this.recipientEmail.trim(),
            message: this.message.trim(),
          }),
        });

        const giftData = await giftRes.json();

        if (!giftRes.ok) {
          this.phoneError = giftData.message || 'Something went wrong. Please try again.';
          this.loading = false;
          return;
        }

        this.giftId    = giftData.gift_id;
        this.giftToken = giftData.token;

        // Step B: initiate M-Pesa STK push
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
          this.step    = 'waiting';
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
          const res  = await fetch(statusBase + this.checkoutRequestId, {
            headers: { 'Accept': 'application/json' },
          });
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

    cancelPayment() {
      clearInterval(this._pollTimer);
      clearInterval(this._countdownTimer);
      this.step = 'payment';
    },
  };
}
</script>
@endpush
