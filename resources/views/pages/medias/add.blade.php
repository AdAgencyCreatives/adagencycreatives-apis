@extends('layouts.app')

@section('title', __('Add New Media'))

@section('scripts')

<script>
$(document).ready(function() {

    $('#new_media_form').submit(function(event) {
        event.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        var data = {
            name: $('#new_media').val()
        };

        $.ajax({
            url: '/api/v1/media-experiences',
            method: 'POST',
            data: data,
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    text: "Media Experience created successfully.",
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
                        errorMessage += field + ': ' + messages.join(', ') + '\n';
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
                <h5 class="card-title">Add New Media Experience</h5>

                <form id="new_media_form">
                    @csrf
                    <div class="mb-3">
                        <label for="new_media" class="form-label">Media Experience Name</label>
                        <input type="text" class="form-control" id="new_media">
                    </div>

                    <button type="submit" class="btn btn-primary">Add New Media Experience</button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection