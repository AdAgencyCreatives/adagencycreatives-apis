@extends('layouts.app')

@section('title', __('Test Data'))

@section('scripts')

@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ data }}
                </div>
            </div>
        </div>
    </div>

@endsection
