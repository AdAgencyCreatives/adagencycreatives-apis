@extends('layouts.app')

@section('title', __('Add New Industry'))

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
            error: function(error) {
                console.error('Error creating year of experience:', error);
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