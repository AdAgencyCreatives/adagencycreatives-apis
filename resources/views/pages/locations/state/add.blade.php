@extends('layouts.app')

@section('title', __('Add New State'))

@section('scripts')

<script>
$(document).ready(function() {

    $('#new_state_form').submit(function(event) {
        event.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        var data = {
            name: $('#new_state').val()
        };

        $.ajax({
            url: '/api/v1/locations',
            method: 'POST',
            data: data,
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    text: "State Created Successfully.",
                    icon: 'success'
                }).then((result) => {
                    fetchData();
                })
            },
            error: function(error) {
                console.error('Error creating state:', error);
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
                <h5 class="card-title">Add New State</h5>

                <form id="new_state_form">
                    @csrf
                    <div class="mb-3">
                        <label for="new_state" class="form-label">State Name</label>
                        <input type="text" class="form-control" id="new_state">
                    </div>

                    <button type="submit" class="btn btn-primary">Add New State</button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection