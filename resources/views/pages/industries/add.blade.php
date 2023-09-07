@extends('layouts.app')

@section('title', __('Add New Industry'))

@section('scripts')

<script>
$(document).ready(function() {

    $('#new_industry_form').submit(function(event) {
        event.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        var data = {
            name: $('#new_industry').val()
        };

        $.ajax({
            url: '/api/v1/industry-experiences',
            method: 'POST',
            data: data,
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    text: "Industry Experience created successfully.",
                    icon: 'success'
                }).then((result) => {
                    fetchData();
                })
            },
            error: function(error) {
                console.error('Error creating industry experience:', error);
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
                <h5 class="card-title">Add New Industry Experience</h5>

                <form id="new_industry_form">
                    @csrf
                    <div class="mb-3">
                        <label for="new_industry" class="form-label">Industry Experience Name</label>
                        <input type="text" class="form-control" id="new_industry">
                    </div>

                    <button type="submit" class="btn btn-primary">Add New Industry Experience</button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection