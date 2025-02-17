@extends('layouts.app')

@section('title', __('Cities'))

@section('scripts')
<script src="{{ asset('/assets/js/custom.js') }}"></script>
<script>
var currentPage = 1;
var totalPages = 1;
var perPage = 10;
var filters = {};

function fetchData(page, filters = []) {
    var requestData = {
        page: page,
        per_page: perPage
    };

    var state_id = "{{ $location->uuid }}";
    var selectedState = $('#state option:selected').text();


    filters = {
        state_id: state_id
    };

    if (selectedState != 'Select City') {
        filters = {
            name: selectedState
        };
    }

    Object.keys(filters).forEach(function(key) {
        if (filters[key] !== '-100') {
            requestData[`filter[${key}]`] = filters[key];
        }
    });
    console.log(requestData);
    $.ajax({
        url: '/api/v1/locations',
        method: 'GET',
        data: requestData,
        dataType: 'json',
        success: function(response) {
            totalPages = response.meta.last_page;
            populateTable(response.data);
            updatePaginationButtons(response.links, response.meta.links);
            updateTableInfo(response.meta);

        },
        error: function() {
            alert('Failed to fetch cities from the API.');
        }
    });
}

function populateTable(states) {
    var tbody = $('#locations-table tbody');
    tbody.empty();
    console.log(states.length);
    if (states.length === 0) {
        displayNoRecordsMessage(7);
    }

    $.each(states, function(index, state) {
    var editUrl = "/locations/" + state.id + "/edit"; 
    var roleBasedActions = '';

    roleBasedActions = 
        '<a href="' + editUrl + '">Edit</a> | ' +
        '<a href="#" class="delete-state-btn" data-id="' + state.uuid + '">Delete</a>';

    var row = '<tr>' +
        '<td>' + state.id + '</td>' +
        '<td class="state-name" data-id="' + state.uuid + '">' + state.name + '</td>' +
        '<td>' + state.created_at + '</td>' +
        '<td>' + roleBasedActions + '</td>' +
        '</tr>';
    tbody.append(row);
});
}

function deleteCityConfirmation(resource_id, resource, url, csrfToken) {
    Swal.fire({
        title: 'Confirm Delete',
        text: 'Are you sure you want to delete this ' + resource + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteCityResource(resource_id, resource, url, csrfToken);
        }
    });
}

function deleteCityResource(userId, resource, url, csrfToken) {

    var msg = resource.charAt(0).toUpperCase() + resource.slice(1) + ' has been deleted.';
    var deleteURL = '/api/v1/' + url + '/' + userId;
    console.log(deleteURL);
    $.ajax({
        url: deleteURL,

        method: 'DELETE',
        data: {
            _token: csrfToken
        },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            Swal.fire({
                title: 'Success',
                text: msg,
                icon: 'success'
            }).then((result) => {
                fetchData(currentPage);
            });
        },
        error: function() {
            alert('Failed to delete the user.');
        }
    });
}

function fetchCities() {

    var requestData = {
        per_page: -1
    };

    // var selectedState = $('#state option:selected').text();

    // filters = {
    //     state_id: state_id
    // };    // var selectedState = $('#state option:selected').text();

    // filters = {
    //     state_id: state_id
    // };

    var state_id = "{{ $location->uuid }}";
    filters = {
        state_id: state_id
    };


    Object.keys(filters).forEach(function(key) {
        if (filters[key] !== '-100') {
            requestData[`filter[${key}]`] = filters[key];
        }
    });
    $.ajax({
        url: '/api/v1/locations',
        method: 'GET',
        dataType: 'json',
        data: requestData,
        success: function(response) {
            populateGroupFilter(response.data, '#state');
        },
        error: function() {
            alert('Failed to fetch cities from the API.');
        }
    });
}

$(document).ready(function() {

    fetchData();
    fetchCities();

    $(document).on('click', '.delete-state-btn', function() {
        var resourceId = $(this).data('id');
        var csrfToken = '{{ csrf_token() }}';
        console.log(csrfToken);
        deleteCityConfirmation(resourceId, 'city', 'locations', csrfToken);
    });


    $('#filter-form').on('submit', function(e) {
        e.preventDefault();

        currentPage = 1;
        fetchData(currentPage);
    });

    $('#new_city_form').submit(function(event) {
        event.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });

        var data = {
            name: $('#new_city').val(),
            parent_id: "{{ $location->id }}",
        };

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
                    }).then((result) => {
                        fetchData();
                    })
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

    $('table').on('dblclick', '.state-name', function() {
        var currentText = $(this).text();
        var id = $(this).data('id');
        var inputField = $('<input>', {
            type: 'text',
            value: currentText
        });

        $(this).html(inputField);

        // Focus on the input field
        inputField.focus();

        // Handle input field blur event
        inputField.on('blur', function() {
            var newText = $(this).val();
            $(this).parent().text(
                newText); // Replace the input field with the updated state name

            console.log(id);
            console.log(newText);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                }
            });
            // Send AJAX request
            $.ajax({
                url: '/api/v1/locations/' + id,
                method: 'PUT',
                data: {
                    name: newText
                },
                success: function(response) {
                    if (response.data) {
                        Swal.fire({
                            title: 'Success',
                            text: "Succesfully updated",
                            icon: 'success'
                        });
                    }


                },
                error: function(error) {
                    console.error('Error updating state:', error);
                }
            });
        });
    });




});
</script>
@endsection

@section('content')

@include('pages.locations.city.filters')


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h1 id="headdd">Cities in {{ $location->name }} </h1>
            </div>
            <div class="card-body">
                <div id="datatables-reponsive_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="table_length" id="table_length"><label>Show <select
                                        name="datatables-reponsive_length" id="per-page-select"
                                        class="form-select form-select-sm">

                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select> entries</label></div>
                        </div>

                    </div>
                    <div class="row dt-row">
                        <div class="col-sm-12">
                            <table id="locations-table" class="table table-striped dataTable no-footer dtr-inline"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>City</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info" id="table_entries_info" role="status" aria-live="polite"></div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers" id="datatables-reponsive_paginate">
                                <ul class="pagination"></ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection