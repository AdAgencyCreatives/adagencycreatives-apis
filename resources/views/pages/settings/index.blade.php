@extends('layouts.app')

@section('title', __('SEO'))


@section('scripts')
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
@endsection

@if (session('success'))
    <x-alert type="success"></x-alert>
@endif

@section('content')
    @include('pages.settings.job')
@endsection
