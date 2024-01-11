@extends('layouts.app')

@section('title', __('Topics'))

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

        function fetchTopicsForFilter() {

            $.ajax({
                url: 'api/v1/topics',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateTopicFilter(response.data, '#topic');
                },
                error: function() {
                    alert('Failed to fetch topics from the API.');
                }
            });
        }

        function populateTopicFilter(topics, div_id) {
            var selectElement = $(div_id);

            $.each(topics, function(index, topic) {
                console.log('d');
                var option = $('<option>', {
                    value: topic.id,
                    text: topic.slug
                });

                selectElement.append(option);
            });
        }

        function fetchData(page, filters = []) {
            var requestData = {
                page: page,
                per_page: perPage
            };

            var selectedTopic = $('#topic option:selected').text();

            if (selectedTopic != 'Select Topic') {
                filters = {
                    slug: selectedTopic
                };
            }

            Object.keys(filters).forEach(function(key) {
                if (filters[key] !== '-100') {
                    requestData[`filter[${key}]`] = filters[key];
                }
            });

            $.ajax({
                url: 'api/v1/topics',
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
                    alert('Failed to fetch categories from the API.');
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
                    '<td class="editable-field" data-id="' + topic.id + '" data-column="title">' + topic.title +
                    '</td>' +
                    '<td class="editable-field" data-id="' + topic.id + '" data-column="slug">' + topic.slug +
                    '</td>' +
                    '<td class="editable-field" data-id="' + topic.id + '" data-column="description">' + topic
                    .description + '</td>' +
                    '<td>' + topic.created_at + '</td>' +
                    '<td>' + roleBasedActions + '</td>' +

                    '</tr>';
                tbody.append(row);
            });
        }

        $(document).ready(function() {

            fetchData();
            fetchTopicsForFilter();

            $(document).on('click', '.delete-category-btn', function() {
                var resourceId = $(this).data('id');
                var csrfToken = '{{ csrf_token() }}';
                console.log(csrfToken);
                deleteConfirmation(resourceId, 'topic', 'topics', csrfToken);
            });


            $('#filter-form').on('submit', function(e) {
                e.preventDefault();

                currentPage = 1;
                fetchData(currentPage);
            });

            $('table').on('dblclick', '.editable-field', function() {
                var currentText = $(this).html();
                var id = $(this).data('id');
                var column = $(this).data('column');
                console.log(currentText);
                var inputField = $('<input>', {
                    type: 'text',
                    value: currentText,
                    css: {
                        width: $(this).width() +
                            'px' // Set the width of the input field to match the cell
                    }
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
                        url: '/api/v1/topics/' + id,
                        method: 'PUT',
                        data: {
                            column: column,
                            value: newText
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
                            console.error('Error updating category:', error);
                        }
                    });
                });
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


    @include('pages.topic.filters')

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
                                            <th>Title</th>
                                            <th>Slug</th>
                                            <th>Description</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody id="sortable-list" data-url="{{ route('update-topic-order') }}">
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
