@extends('layouts.app')

@section('title', __('Add New Years Of Experience'))

@section('scripts')

    <script>
        $(document).ready(function() {

            $('#new_experience_form').submit(function(event) {
                event.preventDefault();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    }
                });

                var data = {
                    name: $('#new_experience').val()
                };

                $.ajax({
                    url: '/api/v1/years-of-experience',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        Swal.fire({
                            title: 'Success',
                            text: "Year of Experience created successfully.",
                            icon: 'success'
                        }).then((result) => {
                            fetchData();
                        })
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        if (xhr.status === 422) {
                            // Validation error occurred
                            var response = JSON.parse(xhr.responseText);
                            var errorMessage = "Validation Error:\n";

                            // Loop through validation errors and append to the error message
                            for (var field in response.errors) {
                                errorMessage += response.errors[field][0] + "\n";
                            }

                            Swal.fire({
                                title: 'Error',
                                text: errorMessage,
                                icon: 'error'
                            });
                        } else {
                            // Handle other types of errors
                            Swal.fire({
                                title: 'Error',
                                text: "An unexpected error occurred",
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
                    <h5 class="card-title">Add New Year of Experience</h5>

                    <form id="new_experience_form">
                        @csrf
                        <div class="mb-3">
                            <label for="new_experience" class="form-label">Year of Experience</label>
                            <input type="text" class="form-control" id="new_experience">
                        </div>

                        <button type="submit" class="btn btn-primary">Add New Year of Experience</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
