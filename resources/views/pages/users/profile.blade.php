@extends('layouts.app')

@section('title')
@if ($user->role == 'agency')
Agency
@elseif ($user->role == 'creative')
Creatives
@elseif ($user->role == 'advisor')
Advisors
@else
Profile
@endif
@endsection

@section('styles')

@endsection

@section('scripts')
<script src="{{ asset('/assets/js/custom.js') }}"></script>
@include('pages.users.creative.scripts')

@endsection

@section('content')
<h1 class="h3 mb-3">Profile</h1>

<div id="error-messages" class="alert alert-danger alert-dismissible" style="display: none;" role="alert">
    <div class="alert-message">
        <strong>Error!</strong> Please fix the following issues:
        <ul></ul>
    </div>
</div>

@if(session('success'))
<x-alert type="success"></x-alert>
@endif


@if(in_array($user->role, ['agency', 'advisor']))
@include('pages.users.agency.agency')
@elseif($user->role == 'creative')

@include('pages.users.creative.creative')
@include('pages.users.creative.qualification')
@include('pages.users.creative.experience')


@endif

@include('pages.users._inc.personal_info')
@include('pages.users._inc.password')

@endsection