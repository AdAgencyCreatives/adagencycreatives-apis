@extends('layouts.app')

@section('title', __('Add New Strength'))

@section('scripts')

<script>
$(document).ready(function() {

    $('#new_category_form').submit(function(event) {
        event.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        var data = {
            name: $('#new_category').val()
        };

        $.ajax({
            url: '/api/v1/strengths',
            method: 'POST',
            data: data,
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    text: "Strength Created Successfully.",
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
                <h5 class="card-title">Add New Strength</h5>

                <form id="new_category_form">
                    @csrf
                    <div class="mb-3">
                        <label for="new_category" class="form-label">Strength Name</label>
                        <input type="text" class="form-control" id="new_category">
                    </div>

                    <button type="submit" class="btn btn-primary">Add New Strength</button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection