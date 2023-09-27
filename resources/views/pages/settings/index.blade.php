@extends('layouts.app')

@section('title', __('SEO'))


@section('scripts')
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
@endsection

@if (session('success'))
    <x-alert type="success"></x-alert>
@endif

@section('content')
    @include('pages.settings.creatives')
    {{-- @include('pages.settings.job') --}}

    <div class="row">
        <div class="col-md-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <br><span class="font-13 text-muted">%site_name%</span>
                    <br><span class="font-13 text-muted">%site_description%</span>
                    <br><span class="font-13 text-muted">%separator%</span>
                </div>

            </div>
        </div>
    </div>

@endsection
