@extends('layouts.account')

@section('account-title', 'Invite Member')

@section('account-content')
<x-ui.empty icon="users" title="Invite a member" :text="$memberCount . ' of ' . $maxMembers . ' member slots used. Invitations are sent from your profile page.'">
  <a href="{{ route('account.profile') }}" class="btn btn-primary">Go to my profile</a>
</x-ui.empty>
@endsection
