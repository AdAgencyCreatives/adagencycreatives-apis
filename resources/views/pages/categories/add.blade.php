@extends('layouts.app')

@section('title', __('Add New Category'))

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
            url: '/api/v1/categories',
            method: 'POST',
            data: data,
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    text: "Category Created Successfully.",
                    icon: 'success'
                }).then((result) => {
                    fetchData();
                })
            },
            error: function(error) {
                console.error('Error creating category:', error);
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
                <h5 class="card-title">Add New Category</h5>

                <form id="new_category_form">
                    @csrf
                    <div class="mb-3">
                        <label for="new_category" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="new_category">
                    </div>

                    <button type="submit" class="btn btn-primary">Add New Category</button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection