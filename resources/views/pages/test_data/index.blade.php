@extends('layouts.app')

@section('title', __('Test Data View'))

@section('scripts')

@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ dd($data) }}
                </div>
            </div>
        </div>
    </div>

@endsection
