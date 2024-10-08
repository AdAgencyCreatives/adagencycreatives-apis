@extends('layouts.app')

@section('title', __('Categories'))

@section('scripts')
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
    <script>
        var currentPage = 1;
        var totalPages = 1;
        var perPage = 10;
        var filters = {};

        function fetchCategories() {

            $.ajax({
                url: 'api/v1/get_categories',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateGroupFilter(response.data, '#category');
                    populateGroupFilter(response.data, '#group');
                },
                error: function() {
                    alert('Failed to fetch categories from the API.');
                }
            });
        }

        function fetchData(page, filters = []) {
            var requestData = {
                page: page,
                per_page: perPage
            };

            var selectedCategory = $('#category option:selected').text();
            var selectedGroup = $('#group option:selected').text();

            filters = {};
            if (selectedCategory != 'Select Category') {
                filters['name'] = selectedCategory;
            }
            if (selectedGroup != 'Select Group') {
                filters['group_name'] = selectedGroup;
            }

            Object.keys(filters).forEach(function(key) {
                if (filters[key] !== '-100') {
                    requestData[`filter[${key}]`] = filters[key];
                }
            });

            $.ajax({
                url: 'api/v1/categories',
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
                    alert('Failed to filter categories from the API.');
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
                    '<td class="group-name" data-id="' + category.id + '">' + (category?.group_name || "") +
                    '</td>' +
                    '<td>' + category.created_at + '</td>' +
                    '<td>' + roleBasedActions + '</td>' +
                    '</tr>';

                tbody.append(row);
            });
        }

        $(document).ready(function() {

            fetchData();

            fetchCategories();
            $(document).on('click', '.delete-category-btn', function() {
                var resourceId = $(this).data('id');
                var csrfToken = '{{ csrf_token() }}';
                console.log(csrfToken);
                deleteConfirmation(resourceId, 'category', 'categories', csrfToken);
            });


            $('#filter-form').on('submit', function(e) {
                e.preventDefault();

                currentPage = 1;
                fetchData(currentPage);
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
                        url: '/api/v1/categories/' + id,
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

            $('table').on('dblclick', '.group-name', function() {
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
                        url: '/api/v1/categories/' + id,
                        method: 'PUT',
                        data: {
                            group_name: newText
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

        });
    </script>
@endsection

@section('content')


    @include('pages.categories.filters')

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
                                            <th>Group Name</th>
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
