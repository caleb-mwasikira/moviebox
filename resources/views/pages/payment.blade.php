@extends('layouts.app')

@section('title', 'Payment — ' . $package->name)

@section('content')
@php $isFree = $package->price == 0; @endphp

<div class="container-app max-w-xl py-12 lg:py-16" @if(!$isFree) x-data="paymentPage()" x-init="init()" @endif>
  <a href="{{ route('pricing') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-brand">
    <x-ui.icon name="arrow-left" class="size-4" /> Back to plans
  </a>

  {{-- Plan summary --}}
  <div class="card card-pad mb-6 flex items-center justify-between gap-4">
    <div class="min-w-0">
      <p class="text-[11px] font-semibold uppercase tracking-widest text-muted">Selected plan</p>
      <p class="mt-1 text-lg font-bold text-fg">{{ $package->name }}</p>
      @if ($package->description)<p class="mt-0.5 text-xs text-muted">{{ $package->description }}</p>@endif
    </div>
    <div class="shrink-0 text-right">
      @if ($isFree)
        <p class="text-3xl font-extrabold text-brand">Free</p>
      @else
        <p class="text-3xl font-extrabold text-fg">{{ $package->currency }}&nbsp;{{ number_format($package->price, 0) }}</p>
      @endif
      <p class="text-xs text-muted">per {{ $package->interval }}</p>
    </div>
  </div>

  @if ($isFree)
    @if ($alreadyUsedFreeTrial)
      <div class="card card-pad py-10 text-center">
        <span class="mx-auto mb-5 grid size-14 place-items-center rounded-2xl bg-surface-2 text-muted"><x-ui.icon name="lock" class="size-7" /></span>
        <h2 class="text-xl font-bold text-fg">Free trial already used</h2>
        <p class="mx-auto mt-2 max-w-sm text-sm text-muted">You've already activated a free plan on this account. Each account is eligible for one free trial only.</p>
        <a href="{{ route('pricing') }}" class="btn btn-primary mt-8">View paid plans</a>
      </div>
    @else
      <div class="card card-pad py-10 text-center">
        <span class="mx-auto mb-5 grid size-14 place-items-center rounded-2xl bg-brand-soft text-brand"><x-ui.icon name="gift" class="size-7" /></span>
        <h2 class="text-xl font-bold text-fg">Activate free access</h2>
        <p class="mx-auto mt-2 max-w-sm text-sm text-muted">No payment required. Click below to activate your <span class="font-semibold text-fg">{{ $package->name }}</span> plan instantly.</p>
        <form method="POST" action="{{ route('payment.activate-free', $package->uuid) }}" class="mt-8" x-data="{ loading: false }" @submit="loading = true">
          @csrf
          <x-ui.submit class="btn btn-primary btn-lg" loading="Activating…">Activate now — it's free</x-ui.submit>
        </form>
      </div>
    @endif
  @else
    {{-- Payment form --}}
    <div x-show="step === 'form'">
      <p class="label mb-3">Payment method</p>
      <div class="mb-6 grid grid-cols-2 gap-3">
        <div class="relative cursor-not-allowed select-none rounded-2xl border border-line p-4 opacity-50">
          <div class="mb-3 flex items-center gap-3"><x-ui.icon name="credit-card" class="size-6 text-muted" /><span class="text-sm font-semibold text-fg">Credit card</span></div>
          <span class="badge badge-neutral">Coming soon</span>
        </div>
        <button type="button" @click="method = 'mpesa'"
          :class="method === 'mpesa' ? 'border-brand bg-brand-soft' : 'border-line hover:border-line-strong'"
          class="rounded-2xl border p-4 text-left transition">
          <div class="mb-3 flex items-center justify-between">
            <div class="flex items-center gap-3"><span class="text-xl leading-none">🇰🇪</span><span class="text-sm font-semibold text-fg">M-Pesa</span></div>
            <span :class="method === 'mpesa' ? 'border-brand bg-brand' : 'border-line-strong'" class="grid size-4 place-items-center rounded-full border-2">
              <span x-show="method === 'mpesa'" class="size-1.5 rounded-full bg-white"></span>
            </span>
          </div>
          <span class="text-xs text-muted">Safaricom STK Push</span>
        </button>
      </div>

      <div class="card card-pad">
        <label class="label">M-Pesa phone number</label>
        <div class="flex">
          <span class="inline-flex select-none items-center rounded-l-xl border border-r-0 border-line-strong bg-surface-2 px-3.5 text-sm font-semibold text-fg">🇰🇪 +254</span>
          <input type="tel" x-model="phone" x-ref="phoneInput" @input="phoneError = ''" @keydown.enter="pay()"
            placeholder="712 345 678" maxlength="10" autocomplete="tel"
            class="input !rounded-l-none" :class="phoneError ? 'input-error' : ''">
        </div>
        <p class="mt-1.5 min-h-4 text-xs" :class="phoneError ? 'font-medium text-red-500' : 'text-muted'"
          x-text="phoneError || 'Enter the number registered with M-Pesa, e.g. 0712 345 678.'"></p>

        <button type="button" @click="pay()" :disabled="loading" class="btn btn-primary btn-lg btn-block mt-6">
          <svg x-show="loading" x-cloak class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <span x-text="loading ? 'Sending request…' : (method === 'mpesa' ? 'Pay with M-Pesa' : 'Continue')"></span>
        </button>
      </div>
    </div>

    {{-- Waiting --}}
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

    {{-- Success --}}
    <div x-show="step === 'success'" x-cloak x-transition class="card card-pad py-10 text-center">
      <span class="mx-auto mb-5 grid size-14 place-items-center rounded-2xl bg-emerald-500/12 text-emerald-500"><x-ui.icon name="check-circle" class="size-7" /></span>
      <h2 class="text-xl font-bold text-fg">Payment confirmed!</h2>
      <p class="mt-2 text-sm text-muted">Your subscription is now active. Enjoy streaming!</p>
      <a href="{{ route('account.billing') }}" class="btn btn-primary mt-8">View my plan</a>
    </div>

    {{-- Failed --}}
    <div x-show="step === 'failed'" x-cloak x-transition class="card card-pad py-10 text-center">
      <span class="mx-auto mb-5 grid size-14 place-items-center rounded-2xl bg-red-500/12 text-red-500"><x-ui.icon name="alert" class="size-7" /></span>
      <h2 class="text-xl font-bold text-fg">Payment not completed</h2>
      <p class="mt-2 text-sm text-muted" x-text="errorMsg || 'Cancelled or timed out. Please try again.'"></p>
      <button type="button" @click="step = 'form'; errorMsg = ''" class="btn btn-primary mt-8">Try again</button>
    </div>
  @endif
</div>
@endsection

@if (!$isFree)
@push('scripts')
<script>
function paymentPage() {
  return {
    step: 'form',
    method: 'mpesa',
    phone: '',
    displayPhone: '',
    phoneError: '',
    loading: false,
    errorMsg: '',
    checkoutRequestId: '',
    countdown: 60,
    _pollTimer: null,
    _countdownTimer: null,

    init() {
      this.$nextTick(() => this.$refs.phoneInput?.focus());
    },

    validatePhone() {
      const raw = this.phone.replace(/\s+/g, '');
      if (!raw) {
        this.phoneError = 'Phone number is required.';
        return false;
      }
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

      this.loading      = true;
      this.phoneError   = '';
      this.displayPhone = this.formatDisplay(this.phone);

      try {
        const res = await fetch('{{ route('mpesa.initiate') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            phone: this.phone,
            package_uuid: '{{ $package->uuid }}',
          }),
        });

        const data = await res.json();

        if (data.success) {
          this.checkoutRequestId = data.checkout_request_id;
          this.step    = 'waiting';
          this.loading = false;
          this.startPolling();
        } else {
          this.phoneError = data.errors?.phone?.[0] || data.message || 'Payment initiation failed.';
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
            this.step = 'success';
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
      this.step = 'form';
    },
  };
}
</script>
@endpush
@endif
