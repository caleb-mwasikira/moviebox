@extends('layouts.app')

@section('title', 'Payment — ' . $package->name)

@section('content')
@php $isFree = $package->price == 0; @endphp

{{-- Free packages need no Alpine payment component --}}
<main class="grow" @if(!$isFree) x-data="paymentPage()" x-init="init()" @endif>
  <div class="py-16 lg:py-24">
    <div class="container max-w-xl">

      {{-- Back link --}}
      <a href="{{ route('pricing') }}" class="inline-flex items-center gap-x-1.5 text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors mb-10">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Plans
      </a>

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

      @if ($isFree)

        @if ($alreadyUsedFreeTrial)
        {{-- ── Already used free trial ── --}}
        <div class="border border-gray-200 dark:border-gray-600 p-8 text-center">
          <div class="inline-flex items-center justify-center w-14 h-14 bg-gray-100 dark:bg-gray-700 mb-5">
            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
          </div>
          <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Free trial already used</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
            You've already activated a free plan on this account. Each account is eligible for one free trial only.
          </p>
          <a href="{{ route('pricing') }}"
            class="inline-flex font-bold uppercase tracking-wide py-3 px-8 text-xs bg-gray-900 text-white hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors">
            View Paid Plans
          </a>
        </div>

        @else
        {{-- ── Free package activation ── --}}
        <div class="border border-gray-200 dark:border-gray-600 p-8 text-center">
          <div class="inline-flex items-center justify-center w-14 h-14 bg-accent/10 mb-5">
            <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Activate free access</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">
            No payment required. Click below to activate your
            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $package->name }}</span>
            plan instantly.
          </p>
          <form method="POST" action="{{ route('payment.activate-free', $package->uuid) }}">
            @csrf
            <button type="submit"
              class="inline-flex items-center justify-center gap-x-2 font-bold uppercase tracking-wide py-3.5 px-10 text-sm bg-gray-900 hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors">
              Activate Now — It's Free
            </button>
          </form>
        </div>
        @endif

      @else

        {{-- ── Payment method selector ── --}}
        <div x-show="step === 'form'">
          <p class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4">Payment Method</p>

          <div class="grid grid-cols-2 gap-4 mb-8">

            {{-- Credit Card (disabled) --}}
            <div class="relative border border-gray-200 dark:border-gray-600 p-5 opacity-50 cursor-not-allowed select-none">
              <div class="flex items-center gap-x-3 mb-3">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Credit Card</span>
              </div>
              <span class="inline-block text-[10px] font-bold uppercase tracking-widest border border-gray-300 dark:border-gray-600 text-gray-400 px-2 py-0.5">Coming Soon</span>
            </div>

            {{-- M-Pesa --}}
            <button type="button"
              @click="method = 'mpesa'"
              :class="method === 'mpesa'
                ? 'border-accent bg-accent/5 dark:bg-accent/10'
                : 'border-gray-200 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-400'"
              class="relative border p-5 text-left transition-colors focus:outline-none">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-x-3">
                  <span class="text-xl leading-none">🇰🇪</span>
                  <span class="text-sm font-bold text-gray-700 dark:text-gray-300">M-Pesa</span>
                </div>
                <div :class="method === 'mpesa' ? 'border-accent bg-accent' : 'border-gray-300 dark:border-gray-600'"
                  class="w-4 h-4 border-2 rounded-full flex items-center justify-center transition-colors shrink-0">
                  <div x-show="method === 'mpesa'" class="w-1.5 h-1.5 bg-white rounded-full"></div>
                </div>
              </div>
              <span class="text-xs text-gray-500 dark:text-gray-400">Safaricom M-Pesa STK Push</span>
            </button>

          </div>

          {{-- Phone input --}}
          <div x-show="method === 'mpesa'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <label class="block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">
              M-Pesa Phone Number
            </label>
            <div class="flex mb-1.5">
              <span class="inline-flex items-center px-3.5 border border-r-0 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm font-bold text-gray-700 dark:text-gray-300 select-none whitespace-nowrap">
                🇰🇪 +254
              </span>
              <input
                type="tel"
                x-model="phone"
                x-ref="phoneInput"
                @input="phoneError = ''"
                @keydown.enter="pay()"
                placeholder="712 345 678"
                maxlength="10"
                autocomplete="tel"
                class="flex-1 min-w-0 border border-gray-200 dark:border-gray-600 dark:bg-gray-900/40 dark:text-white px-4 py-3 text-sm text-gray-900 focus:border-accent outline-none transition-colors"
                :class="phoneError ? '!border-red-400' : ''">
            </div>
            <p class="text-[11px] min-h-4"
              :class="phoneError ? 'text-red-500' : 'text-gray-400'"
              x-text="phoneError || 'Enter the number registered with M-Pesa, e.g. 0712 345 678.'">
            </p>
          </div>

          {{-- Pay button --}}
          <div class="mt-8">
            <button
              @click="pay()"
              :disabled="!method || loading"
              class="w-full flex items-center py-6 justify-center gap-x-2 font-bold uppercase tracking-wide py-3.5 text-sm bg-gray-900 hover:bg-accent hover:text-gray-900 dark:bg-white dark:text-gray-900 dark:hover:bg-accent transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
              <svg x-show="loading" class="h-4 w-4 animate-spin fill-current shrink-0" viewBox="0 0 24 24">
                <path d="M12 2v4a8 8 0 0 1 8 8h4A12 12 0 0 0 12 2z"/>
              </svg>
              <span x-text="loading ? 'Sending request…' : (method === 'mpesa' ? 'Pay with M-Pesa' : 'Continue')"></span>
            </button>
          </div>
        </div>

        {{-- ── Waiting for PIN ── --}}
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
            <button @click="cancelPayment()" class="font-bold uppercase hover:text-red-500 transition-colors">Cancel</button>
          </div>
        </div>

        {{-- ── Success ── --}}
        <div x-show="step === 'success'" x-transition class="py-8 text-center border border-gray-200 dark:border-gray-600 px-8">
          <div class="inline-flex items-center justify-center w-14 h-14 bg-accent/10 mb-5">
            <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Payment confirmed!</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Your subscription is now active. Enjoy streaming!</p>
          <a href="{{ route('account.billing') }}"
            class="inline-flex font-bold uppercase tracking-wide py-3 px-8 text-xs bg-accent text-gray-900 hover:bg-accent/90 transition-colors">
            View My Plan
          </a>
        </div>

        {{-- ── Failed ── --}}
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
