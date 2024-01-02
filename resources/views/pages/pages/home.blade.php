@extends('layouts.app')

@section('title', Str::ucfirst($page))

@section('scripts')
    <script src="https://cdn.tiny.cloud/1/0de1wvfzr5x0z7za5hi7txxvlhepurk5812ub5p0fu5tnywh/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>

    <script>
        $(document).ready(function() {
            tinymce.init({
                selector: 'textarea',
                menubar: false,
                plugins: 'anchor autolink codesample emoticons link lists visualblocks image preview',
                toolbar: 'bold italic underline strikethrough | blocks fontfamily fontsize  | link media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            });

        });
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
                    <form action="{{ route('pages.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="page" value="{{ $page }}">
                        <div class="row">

                            <div class="col-md-12">

                                @foreach ($data as $item)
                                    <div class="mb-3">
                                        <label for="name" class="form-label">{{ Str::ucfirst($item->key) }}</label>
                                        <textarea class="form-control editor-textarea" name="{{ $item->key }}"
                                            placeholder="Enter {{ Str::ucfirst($item->key) }}">{{ $item->value }}</textarea>
                                    </div>
                                @endforeach

                                {{-- @foreach ($data as $item)
                                        <div class="mb-3">
                                            <label for="name" class="form-label"> {{ Str::ucfirst($item->key) }}</label>
                                            <input type="text" class="form-control" name="{{ $item->key }}"
                                                value="{{ $item->value }}">
                                        </div>
                                    @endforeach --}}


                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary" id="submitButton">Save</button>
                    </form>
                </div>

            </div>
        </div>
    </div>


@endsection
