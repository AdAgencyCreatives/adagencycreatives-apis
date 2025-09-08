@extends('layouts.app')

@section('title', __('Add NEWS Blog'))

@section('styles')
@include('components.tip-tap-editor')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {

        $('#new_article_form').submit(function(event) {
            event.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                }
            });

            var data = {
                title: $('#new_article_title').val(),
                sub_title: $('#new_article_sub_title').val(),
                article_date: $('#new_article_date').val(),
                description: $('#new_article_description').val(),
                // Correctly send '1' or '0' to the backend
                is_featured: $('#new_article_is_featured').val() === 'yes' ? 1 : 0,
            };

            $.ajax({
                url: '/api/v1/articles',
                method: 'POST',
                data: data,
                success: function(response) {
                    Swal.fire({
                        title: 'Success',
                        text: "Article Created Successfully.",
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
                <h5 class="card-title">Add NEWS Blog</h5>

                <form id="new_article_form">
                    @csrf
                    <div class="mb-3">
                        <label for="new_article_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="new_article_title">
                    </div>
                    <div class="mb-3">
                        <label for="new_article_sub_title" class="form-label"> Sub-Title</label>
                        <input type="text" class="form-control" id="new_article_sub_title">
                    </div>
                    <div class="mb-3">
                        <label for="new_article_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="new_article_date">
                    </div>
                    <div class="mb-3">
                        <label for="new_article_is_featured" class="form-label">Featured</label>
                        <select class="form-control" id="new_article_is_featured">
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="new_article_description" class="form-label">Description</label>
                        <textarea class="form-control tip-tap-editor w-100" id="new_article_description"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add New Article</button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection