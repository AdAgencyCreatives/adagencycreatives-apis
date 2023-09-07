@extends('layouts.app')

@section('title', __('Cities'))

@section('scripts')
<script src="{{ asset('/assets/js/custom.js') }}"></script>
<script>
function fetchStates() {

    var requestData = {
        per_page: -1
    };

    $.ajax({
        url: '/api/v1/locations',
        method: 'GET',
        dataType: 'json',
        data: requestData,
        success: function(response) {
            populateStateFilter(response.data, '#state');
        },
        error: function() {
            alert('Failed to fetch states from the API.');
        }
    });
}

function populateStateFilter(states, div_id) {
    var selectElement = $(div_id);

    $.each(states, function(index, state) {
        var option = $('<option>', {
            value: state.id,
            text: state.name
        });

        selectElement.append(option);
    });
}

$(document).ready(function() {

    fetchStates();

    $('#new_city_form').submit(function(event) {
        event.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        if ($('#state').val() == "-100") {
            Swal.fire({
                title: 'State Missing',
                text: "Please select state first.",
                icon: 'error'
            });
            return;
        }
        var data = {
            name: $('#new_city').val(),
            parent_id: $('#state').val(),
        };


        console.log(data);
        $.ajax({
            url: '/api/v1/locations',
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.data) {
                    Swal.fire({
                        title: 'Success',
                        text: "City Created Successfully.",
                        icon: 'success'
                    });
                }

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
                <form id="filter-form">
                    <input type="hidden" class="d-none" name="filter" value="true" hidden>
                    <div class="row">
                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="state"> Select State </label>
                                <select name="state" id="state" class="form-control form-select custom-select select2"
                                    data-toggle="select2">
                                    <option value="-100">Select State</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <form id="new_city_form">
                    @csrf
                    <div class="mb-3">
                        <label for="new_city" class="form-label">City Name</label>
                        <input type="text" class="form-control" id="new_city">
                    </div>

                    <button type="submit" class="btn btn-primary">Add New City</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection