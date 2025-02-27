@extends('layouts.app')

@section('title', __('Featured Cities'))

@section('scripts')
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
    <!-- This is responsible for sorting UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

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

            var firstname = $('#first_name').val();
            filters = {

                name: firstname,
                status: 1,
                is_visible: 1,
                is_featured: 1,
            };

            Object.keys(filters).forEach(function(key) {
                if (filters[key] !== '-100') {
                    requestData[`filter[${key}]`] = filters[key];
                }
            });

            $.ajax({
                url: 'api/v1/agencies?filter[is_featured]=1&filter[status]=1&filter[is_visible]=1&sort=sort_order',
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
                    alert('Failed to fetch agencies from the API.');
                }
            });
        }

        function populateTable(agencies) {
            var tbody = $('#topics-table tbody');
            tbody.empty();
            console.log(agencies.length);
            if (agencies.length === 0) {
                displayNoRecordsMessage(7);
            }

            $.each(agencies, function(index, agency) {

                var editUrl = "/users/" + agency.user_id2 + "/details";
                var roleBasedActions = '';

                roleBasedActions = '<a href="' + editUrl +
                    '" target="_blank">Details</a>';

                var row = '<tr class="sortable-item" id="' + agency.id2 + '">' +
                    '<td>' + agency.user_id2 + '</td>' +
                    '<td><img src="' + (agency?.user_thumbnail || agency?.profile_image) +
                    '" style="max-width: 100px; max-height: 100px;"></td>' +

                    '<td class="editable-field" data-id="' + agency.id2 + '" data-column="link">' + agency
                    .name +
                    '</td>' +
                    '<td class="sort-order" data-id="' + agency.id2 + '">' + agency.sort_order + '</td>' +
                    '<td>' + roleBasedActions + '</td>' +

                    '</tr>';
                tbody.append(row);
            });
        }

        $(document).ready(function() {

            fetchData();

            $("#sortable-list").sortable({
                update: function(event, ui) {
                    // Get the updated order
                    var order = $(this).sortable('toArray');
                    console.log(order);
                    var url = $("#sortable-list").data('url');
                    // Send an AJAX request to update the order in the backend
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                order: order
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });

                }
            });
            $("#sortable-list").disableSelection();

            $('#filter-form').on('submit', function(e) {
                e.preventDefault();

                currentPage = 1;
                fetchData(currentPage);
            });

            $('table').on('dblclick', '.sort-order', function() {
                var currentText = $(this).text();
                var id = $(this).data('id');
                var inputField = $('<input>', {
                    type: 'text',
                    value: currentText
                });

                $(this).html(inputField);

                inputField.focus();
                inputField.on('blur', function() {
                    var newText = $(this).val();
                    $(this).parent().text(
                        newText);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        }
                    });
                    $.ajax({
                        url: '/update-featured-agency-order',
                        method: 'POST',
                        data: {
                            agency_id: id,
                            sort_order: newText
                        },
                        success: function(response) {
                            console.log(response);
                            if (response.status === 200) {
                                Swal.fire({
                                    title: 'Success',
                                    text: response.message,
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
                                    errorMessage += field + ': ' + messages
                                        .join(', ') + '\n';
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



            @if (Session::has('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "{{ Session::get('success') }}",
                    confirmButtonText: 'OK'
                });
            @endif

        });
    </script>
@endsection

@section('content')

    @include('pages.featured_agencies.filters')

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
                                <table id="topics-table" class="table table-striped dataTable no-footer dtr-inline"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Profile</th>
                                            <th>Name</th>
                                            <th>Sort Position</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody id="sortable-list" data-url="{{ route('update-featured-agencies-order') }}">
                                    </tbody>

                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info" id="table_entries_info" role="status" aria-live="polite">
                                </div>
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

    <div class="row">

        <div class="col-md-12 col-xl-12">
            <div class="card">

                <div class="card-header">
                    <h5 class="card-title mb-0">Homepage agencies</h5>
                </div>
                <div class="card-body">
                    <form id="agency-form2" action="{{ route('settings.agencies-count') }}" method="POST">
                        @csrf()

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="agency_count_homepage">Featured agencies count</label>
                                    <input type="number" class="form-control" name="agency_count_homepage"
                                        value="{{ settings('agency_count_homepage') }}" min="1"
                                        max="{{ \App\Models\Agency::where('is_featured', 1)->count() }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection
