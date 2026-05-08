@extends('layouts.account')

@section('account-title', 'Billing Details')

@section('account-content')
<div class="flex flex-col gap-y-8">

  {{-- Subscription Status --}}
  <div class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Subscription</h2>
    <p class="text-sm text-gray-500 dark:text-gray mb-8">Your current plan and billing cycle.</p>

    <div class="flex items-center justify-between flex-wrap gap-4 border border-gray-200 dark:border-gray-600 p-6">
      <div>
        <div class="text-xs font-bold uppercase text-gray-500 mb-1">Current Plan</div>
        <div class="text-lg font-bold text-gray-900 dark:text-white">Premium</div>
        <div class="text-sm text-gray-500 dark:text-gray mt-1">Renews on July 1, 2026</div>
      </div>
      <span
        class="inline-flex items-center px-3 py-1 text-xs font-bold uppercase bg-accent/10 text-accent border border-accent">
        Active
      </span>
    </div>
  </div>

  {{-- Payment Method --}}
  <div class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Payment Method</h2>
    <p class="text-sm text-gray-500 dark:text-gray mb-8">Update your card details.</p>

    <form method="POST" action="#" class="flex flex-col gap-y-6 max-w-lg">
      @csrf

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="card_name">
          Name on Card
        </label>
        <input
          class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
          type="text" name="card_name" id="card_name" placeholder="e.g. John Smith" autocomplete="cc-name">
      </div>

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="card_number">
          Card Number
        </label>
        <input
          class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
          type="text" name="card_number" id="card_number" placeholder="•••• •••• •••• ••••" maxlength="19" autocomplete="cc-number">
      </div>

      <div class="grid grid-cols-2 gap-x-5">
        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="card_expiry">
            Expiry Date
          </label>
          <input
            class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
            type="text" name="card_expiry" id="card_expiry" placeholder="MM / YY" maxlength="7" autocomplete="cc-exp">
        </div>
        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="card_cvc">
            CVC
          </label>
          <input
            class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
            type="text" name="card_cvc" id="card_cvc" placeholder="•••" maxlength="4" autocomplete="cc-csc">
        </div>
      </div>

      <div>
        <button type="submit"
          class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
          Update Payment Method
        </button>
      </div>
    </form>
  </div>

  {{-- Billing Address --}}
  <div class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Billing Address</h2>
    <p class="text-sm text-gray-500 dark:text-gray mb-8">Address used for invoices and receipts.</p>

    <form method="POST" action="#" class="flex flex-col gap-y-6 max-w-lg">
      @csrf

      <div class="grid grid-cols-2 gap-x-5 gap-y-6">
        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="first_name">First Name</label>
          <input
            class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
            type="text" name="first_name" id="first_name" autocomplete="given-name">
        </div>
        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="last_name">Last Name</label>
          <input
            class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
            type="text" name="last_name" id="last_name" autocomplete="family-name">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="address">Street Address</label>
        <input
          class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
          type="text" name="address" id="address" autocomplete="street-address">
      </div>

      <div class="grid grid-cols-2 gap-x-5 gap-y-6">
        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="city">City</label>
          <input
            class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
            type="text" name="city" id="city" autocomplete="address-level2">
        </div>
        <div>
          <label class="block text-xs font-bold uppercase text-gray-500 mb-2" for="postcode">Postcode</label>
          <input
            class="px-4 py-2 border-base bg-white/40 font-medium leading-8 tracking-tight text-gray-900 transition-all duration-150 focus:border-accent focus:bg-white focus:outline-0 focus:ring-0 dark:border-gray-650 dark:bg-gray-900/40 dark:text-white dark:focus:border-accent dark:focus:bg-gray-800 w-full"
            type="text" name="postcode" id="postcode" autocomplete="postal-code">
        </div>
      </div>

      <div>
        <button type="submit"
          class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90">
          Save Address
        </button>
      </div>
    </form>
  </div>

</div>
@endsection
