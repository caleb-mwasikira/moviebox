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

</div>
@endsection
