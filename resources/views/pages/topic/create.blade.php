@extends('layouts.app')

@section('title', __('Add New Topic'))

@section('scripts')

    <script>
        $(document).ready(function() {

            $('#new_topic_form').submit(function(event) {
                event.preventDefault();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    }
                });

                var data = {
                    title: $('#title').val(),
                    slug: $('#slug').val(),
                    description: $('#description').val()
                };

                $.ajax({
                    url: '/api/v1/topics',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        Swal.fire({
                            title: 'Success',
                            text: "Topic Created Successfully.",
                            icon: 'success'
                        }).then((result) => {
                            fetchData();
                        })
                    },
                    error: function(error) {
                        if (error.responseJSON && error.responseJSON.errors) {
                            var errorMessages = error.responseJSON.errors;

                            // Process and display error messages
                            var errorMessage = '';
                            $.each(errorMessages, function(field, messages) {
                                errorMessage += field + ': ' + messages.join(', ') +
                                    '\n';
                            });

                            Swal.fire({
                                title: 'Validation Error',
                                text: errorMessage,
                                icon: 'error'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: error.message,
                                icon: 'error'
                            });
                        }
                    }
                });
            });

        });
    </script>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Add New Topic</h5>

                    <form id="new_topic_form">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Topic Name</label>
                            <input type="text" class="form-control" id="title">
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug (optional)</label>
                            <input type="text" class="form-control" id="slug" placeholder="Example abc-def">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description">
                        </div>

                        <button type="submit" class="btn btn-primary">Add New Topic</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
