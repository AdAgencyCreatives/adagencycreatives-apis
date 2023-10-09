@extends('layouts.app')

@section('title', Str::ucfirst($page))

@section('scripts')
    @include('pages.ckEditor')
    <script>
        $(document).ready(function() {});
    </script>
@endsection

@section('styles')
    <style>
        .ck-editor__editable[role="textbox"] {
            /* editing area */
            min-height: 200px;
        }

        .ck-content .image {
            /* block images */
            max-width: 80%;
            margin: 20px auto;
        }
    </style>
@endsection
@section('content')

    @if (session('success'))
        <x-alert type="success"></x-alert>
    @endif

    <h1 class="h3 mb-3">{{ Str::ucfirst($page) }} page</h1>
    <div class="row">
        <div class="col-md-12 col-xl-12">
            <div class="card">

                <div class="card-body">
                    <form action="{{ route('pages.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="page" value="{{ $page }}">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <textarea class="form-control text-area-description" id="editor" name="user_note"
                                        placeholder="Put your card info and also paste image here">
                                    </textarea>
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary" id="submitButton">Save</button>
                    </form>
                </div>

            </div>
        </div>
    </div>


@endsection
