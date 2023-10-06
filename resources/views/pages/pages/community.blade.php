@extends('layouts.app')

@section('title', Str::ucfirst($page))
@section('scripts')
    <script>
        $(document).ready(function() {});
    </script>
@endsection
@section('content')

    @if (session('success'))
        <x-alert type="success"></x-alert>
    @endif


    <div id="error-messages" class="alert alert-danger alert-dismissible" style="display: none;" role="alert">
        <div class="alert-message">
            <strong>Error!</strong> Please fix the following issues:
            <ul></ul>
        </div>
    </div>

    <h1 class="h3 mb-3">{{ Str::ucfirst($page) }} page</h1>
    <div class="row">
        <div class="col-md-12 col-xl-12">
            <div class="card">

                <div class="card-body">
                    <form action="{{ route('pages.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="page" value="{{ $page }}">
                        <div class="row">
                            <div class="col-md-8">
                                @foreach ($data as $item)
                                    <div class="mb-3">
                                        <label for="name" class="form-label"> {{ Str::ucfirst($item->key) }}</label>
                                        <input type="text" class="form-control" name="{{ $item->key }}"
                                            value="{{ $item->value }}">
                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary" id="submitButton">Save</button>
                    </form>
                </div>

            </div>
        </div>
    </div>


@endsection
