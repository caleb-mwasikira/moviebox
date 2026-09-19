@extends('layouts.account')

@section('account-title', 'Profiles')

@section('account-content')
<x-ui.empty icon="users" title="Manage profiles" text="Profiles are managed from your profile page.">
  <a href="{{ route('account.profile') }}" class="btn btn-primary">Go to my profile</a>
</x-ui.empty>
@endsection
