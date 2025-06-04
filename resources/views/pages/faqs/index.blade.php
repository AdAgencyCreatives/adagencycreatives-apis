@extends('layouts.app')

@section('title', __('Faqs'))

@section('scripts')
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
    <script>
        var currentPage = 1;
        var totalPages = 1;
        var perPage = 10;
        var filters = {};

        function fetchFaqs() {

            $.ajax({
                url: 'api/v1/get_faqs',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateGroupFilter(response.data, '#faq');
                },
                error: function() {
                    alert('Failed to fetch faqs from the API.');
                }
            });
        }

        function fetchData(page, filters = []) {
            var requestData = {
                page: page,
                per_page: perPage
            };

            var selectedFaq = $('#faq option:selected').text();

            filters = {};
            if (selectedFaq != 'Select Faq') {
                filters['title'] = selectedFaq;
            }

            Object.keys(filters).forEach(function(key) {
                if (filters[key] !== '-100') {
                    requestData[`filter[${key}]`] = filters[key];
                }
            });

            $.ajax({
                url: 'api/v1/faqs',
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
                    alert('Failed to filter faqs from the API.');
                }
            });
        }

        function populateTable(faqs) {
            var tbody = $('#faqs-table tbody');
            tbody.empty();
            console.log(faqs.length);
            if (faqs.length === 0) {
                displayNoRecordsMessage(7);
            }

            $.each(faqs, function(index, faq) {
                var roleBasedActions = '';

                roleBasedActions = '<a href="#" class="delete-faq-btn" data-id="' +
                    faq.id + '">Delete</a>';

                var row = '<tr>' +
                    '<td>' + faq.id + '</td>' +
                    '<td class="faq-title" data-id="' + faq.id + '" data-col="title">' + faq.title + '</td>' +
                    '<td class="faq-description" data-id="' + faq.id + '" data-col="description">' + faq
                    .description + '</td>' +
                    '<td class="faq-order" data-id="' + faq.id + '" data-col="order">' + faq.order + '</td>' +
                    '<td>' + roleBasedActions + '</td>' +
                    '</tr>';

                tbody.append(row);
            });
        }

        $(document).ready(function() {

            fetchData();

            fetchFaqs();
            $(document).on('click', '.delete-faq-btn', function() {
                var resourceId = $(this).data('id');
                var csrfToken = '{{ csrf_token() }}';
                console.log(csrfToken);
                deleteConfirmation(resourceId, 'faq', 'faqs', csrfToken);
            });


            $('#filter-form').on('submit', function(e) {
                e.preventDefault();

                currentPage = 1;
                fetchData(currentPage);
            });

            function handleEdit() {
                var currentData = $(this).text();
                var id = $(this).data('id');
                var col = $(this).data('col');

                var inputField = $('<input>', {
                    type: 'text',
                    value: currentData
                });

                if (col == 'order') {
                    inputField = $('<input>', {
                        type: 'number',
                        value: currentData
                    });
                } else if (col == 'description') {
                    inputField = $('<textarea>', {
                        rows: 3,
                    });
                }

                $(this).val(inputField);

                inputField.focus();
                inputField.on('blur', function() {

                    var newData = $(this).val();
                    $(this).parent().text(newData);
                    // console.log(id);
                    // console.log(newData);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        }
                    });
                    $.ajax({
                        url: '/api/v1/faqs/' + id,
                        method: 'PUT',
                        data: {
                            [col]: newData
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
            }

            $('table').on('dblclick', '.faq-title', handleEdit);
            $('table').on('dblclick', '.faq-description', handleEdit);
            $('table').on('dblclick', '.faq-order', handleEdit);

        });
    </script>
@endsection

@section('content')


    @include('pages.faqs.filters')

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
                                <table id="faqs-table" class="table table-striped dataTable no-footer dtr-inline"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Order</th>
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
