@extends('layouts.app')

@section('title', __('Add New Faq'))

@section('scripts')

<script>
$(document).ready(function() {

    $('#new_faq_form').submit(function(event) {
        event.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        var data = {
            title: $('#new_faq_title').val(),
            description: $('#new_faq_description').val(),
            order: $('#new_faq_order').val(),
        };

        $.ajax({
            url: '/api/v1/faqs',
            method: 'POST',
            data: data,
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    text: "Faq Created Successfully.",
                    icon: 'success'
                }).then((result) => {
                    location.reload();
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
                <h5 class="card-title">Add New Faq</h5>

                <form id="new_faq_form">
                    @csrf
                    <div class="mb-3">
                        <label for="new_faq_title" class="form-label">Faq Title</label>
                        <input type="text" class="form-control" id="new_faq_title">
                    </div>
                    <div class="mb-3">
                        <label for="new_faq_description" class="form-label">Faq Description</label>
                        <input type="text" class="form-control" id="new_faq_description">
                    </div>
                    <div class="mb-3">
                        <label for="new_faq_order" class="form-label">Faq Order</label>
                        <input type="number" min="0" class="form-control" id="new_faq_order">
                    </div>

                    <button type="submit" class="btn btn-primary">Add New Faq</button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection