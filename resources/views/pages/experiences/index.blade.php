@extends('layouts.app')

@section('title', __('Years of Experience'))

@section('scripts')
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
    <script>
        var currentPage = 1;
        var totalPages = 1;
        var perPage = 10;
        var filters = {};

        function fetchData(page, filters = []) {

            $.ajax({
                url: 'api/v1/years-of-experience',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    totalPages = response.meta.last_page;
                    populateTable(response.data);
                    updatePaginationButtons(response.links, response.meta.links);
                    updateTableInfo(response.meta);

                },
                error: function() {
                    alert('Failed to fetch years-of-experience from the API.');
                }
            });
        }

        function populateTable(categories) {
            var tbody = $('#categories-table tbody');
            tbody.empty();
            console.log(categories.length);
            if (categories.length === 0) {
                displayNoRecordsMessage(7);
            }

            $.each(categories, function(index, category) {
                var roleBasedActions = '';

                roleBasedActions = '<a href="#" class="delete-category-btn" data-id="' +
                    category.id + '">Delete</a>';

                var row = '<tr>' +
                    '<td>' + category.id + '</td>' +
                    '<td class="category-name" data-id="' + category.id + '">' + category.name + '</td>' +
                    '<td>' + category.created_at + '</td>' +
                    '<td>' + roleBasedActions + '</td>' +

                    '</tr>';
                tbody.append(row);
            });
        }

        $(document).ready(function() {

            fetchData();

            $(document).on('click', '.delete-category-btn', function() {
                var resourceId = $(this).data('id');
                var csrfToken = '{{ csrf_token() }}';
                deleteConfirmation(resourceId, 'year of experience', 'years-of-experience', csrfToken);
            });

            $('table').on('dblclick', '.category-name', function() {
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

                    console.log(id);
                    console.log(newText);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        }
                    });
                    $.ajax({
                        url: '/api/v1/years-of-experience/' + id,
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
                        error: function(xhr, textStatus, errorThrown) {
                            if (xhr.status === 422) {
                                // Validation error occurred
                                var response = JSON.parse(xhr.responseText);
                                var errorMessage = "Validation Error:\n";

                                // Loop through validation errors and append to the error message
                                for (var field in response.errors) {
                                    errorMessage += response.errors[field][0] + "\n";
                                }

                                Swal.fire({
                                    title: 'Error',
                                    text: errorMessage,
                                    icon: 'error'
                                });
                            } else {
                                // Handle other types of errors
                                Swal.fire({
                                    title: 'Error',
                                    text: "An unexpected error occurred",
                                    icon: 'error'
                                });
                            }
                        }
                    });
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
                                <table id="categories-table" class="table table-striped dataTable no-footer dtr-inline"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
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
@endsection
