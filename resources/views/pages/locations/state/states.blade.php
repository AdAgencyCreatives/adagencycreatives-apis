@extends('layouts.app')

@section('title', __('States'))

@section('scripts')
<script src="{{ asset('/assets/js/custom.js') }}"></script>
<script>
var currentPage = 1;
var totalPages = 1;
var perPage = 10;
var filters = {};

function fetchStates() {

    var requestData = {
        per_page: -1
    };

    $.ajax({
        url: 'api/v1/locations',
        method: 'GET',
        dataType: 'json',
        data: requestData,
        success: function(response) {
            populateGroupFilter(response.data, '#state');
        },
        error: function() {
            alert('Failed to fetch states from the API.');
        }
    });
}

function fetchData(page, filters = []) {
    var requestData = {
        page: page,
        per_page: perPage
    };

    var selectedState = $('#state option:selected').text();

    if (selectedState != 'Select State') {
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
        url: 'api/v1/locations',
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
            alert('Failed to fetch states from the API.');
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
        var editUrl = "/locations/" + state.id + "/cities";
        var roleBasedActions = '';


        roleBasedActions = '<a href="' + editUrl +
            '">Cities</a> | <a href="#" class="delete-state-btn" data-id="' +
            state.uuid + '">Delete</a>';

        var row = '<tr>' +
            '<td>' + state.id + '</td>' +
            '<td class="state-name" data-id="' + state.uuid + '">' + state.name + '</td>' +
            '<td>' + state.created_at + '</td>' +
            '<td>' + roleBasedActions + '</td>' +

            '</tr>';
        tbody.append(row);
    });
}



$(document).ready(function() {

    fetchData();

    fetchStates();
    $(document).on('click', '.delete-state-btn', function() {
        var resourceId = $(this).data('id');
        var csrfToken = '{{ csrf_token() }}';
        console.log(csrfToken);
        deleteConfirmation(resourceId, 'location', 'locations', csrfToken);
    });


    $('#filter-form').on('submit', function(e) {
        e.preventDefault();

        currentPage = 1;
        fetchData(currentPage);
    });

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


@include('pages.locations.state.filters')

<div class="row">
    <div class="col-12">
        <div class="card">
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
                                        <th>State</th>
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

@include('pages.locations.state.add-state')
@endsection