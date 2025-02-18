@extends('layouts.app')

@section('title', __('Featured Cities'))



@section('scripts')
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
    <!-- Include jQuery UI -->
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

            var selectedTopic = $('#city option:selected').val();

            if (selectedTopic != 'Select Topic') {
                filters = {
                    topic: selectedTopic
                };
            }

            Object.keys(filters).forEach(function(key) {
                if (filters[key] !== '-100') {
                    requestData[`filter[${key}]`] = filters[key];
                }
            });

            $.ajax({
                url: 'api/v1/featured_cities_with_job_count',
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

        function populateTable(categories) {
            var tbody = $('#topics-table tbody');
            tbody.empty();
            console.log(categories.length);
            if (categories.length === 0) {
                displayNoRecordsMessage(7);
            }

            $.each(categories, function(index, topic) {
                var roleBasedActions = '';

                roleBasedActions = '<a href="#" class="delete-category-btn" data-id="' +
                    topic.id + '">Delete</a>';

                var row = '<tr class="sortable-item" id="' + topic.id + '">' +
                    '<td>' + topic.id + '</td>' +
                    '<td><img src="' + topic.preview_link +
                    '" alt="Preview Image" style="max-width: 100px; max-height: 100px;"></td>' +

                    '<td class="editable-field" data-id="' + topic.id + '" data-column="link">' + topic.name +
                    '</td>' +

                    '<td class="editable-field" data-id="' + topic.id + '" data-column="link">' + topic.count +
                    '</td>' +

                    '<td>' + topic.created_at + '</td>' +
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
                console.log(csrfToken);
                deleteConfirmation(resourceId, 'city', 'featured_cities', csrfToken);
            });


            $('#filter-form').on('submit', function(e) {
                e.preventDefault();

                currentPage = 1;
                fetchData(currentPage);
            });

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
                                <table id="topics-table" class="table table-striped dataTable no-footer dtr-inline"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Preview</th>
                                            <th>Name</th>
                                            <th>Jobs count</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody id="sortable-list" data-url="{{ route('update-featured-city-order') }}">
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
