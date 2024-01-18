@extends('layouts.app')

@section('title', 'Log Detail')

@section('styles')
    <style>
        .json-container {
            border: 1px solid #ccc;
            padding: 10px;
        }
    </style>
@endsection
@section('content')
    <h1 class="h3 mb-3">Log Detail</h1>

    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="json-container">
                <pre><?php echo htmlspecialchars($activityDetail, ENT_QUOTES, 'UTF-8'); ?></pre>
            </div>
        </div>

    </div>

@endsection
