@extends('layouts.account')

@section('account-title', 'Dashboard')

@section('account-content')
<div
  class="bg-white shadow-3xl p-8 md:p-10 lg:p-14 xl:p-[60px] dark:bg-gray-700 [&_a]:text-accent hover:[&_a]:text-accent/80 [&_a]:transition-colors">

  <p class="mb-6 text-gray-900 dark:text-white">
    Hello <strong>{{ auth()->user()->name }}</strong>
  </p>

  <p class="text-gray-500 dark:text-gray leading-relaxed">
    From your account dashboard you can manage your
    <a href="{{ route('account.billing') }}">billing details</a>
    and
    <a href="{{ route('account.personal-info') }}">edit your password and account details</a>.
  </p>

  @if (auth()->user()->isAdmin())
    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-600">
      <p class="text-xs font-bold uppercase text-gray-500 tracking-widest mb-4">Admin</p>
      <a href="{{ route('admin.dashboard') }}"
        class="inline-flex text-center font-bold leading-none transition-colors uppercase justify-center gap-x-3 py-4 px-8 text-sm text-gray-900 bg-accent hover:bg-accent/90 !text-gray-900">
        Go to Admin Panel
      </a>
    </div>
  @endif

</div>
@endsection
