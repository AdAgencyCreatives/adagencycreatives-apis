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
                    url: 'api/v1/home/creatives?filter[is_featured]=1&filter[status]=1&filter[is_visible]=1&sort=sort_order',
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

            function populateTable(creatives) {
                var tbody = $('#topics-table tbody');
                tbody.empty();
                console.log(creatives.length);
                if (creatives.length === 0) {
                    displayNoRecordsMessage(7);
                }

                $.each(creatives, function(index, creative) { 

                    var editUrl = "/users/" + creative.user_id2 + "/details";
                    var roleBasedActions = '';

                    roleBasedActions = '<a href="' + editUrl +
                            '" target="_blank">Details</a>';

                    var row = '<tr class="sortable-item" id="' + creative.id2 + '">' +
                        '<td>' + creative.user_id2 + '</td>' +
                        '<td><img src="' + creative.profile_image +
                        '" alt="Profile Image" style="max-width: 100px; max-height: 100px;"></td>' +

                        '<td class="editable-field" data-id="' + creative.id2 + '" data-column="link">' + creative.name +
                        '</td>' +
                        '<td>' + creative.sort_order + '</td>' +
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
                                            <th>Profile</th>
                                            <th>Name</th>
                                            <th>Sort Position</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody id="sortable-list" data-url="{{ route('update-featured-creatives-order') }}">
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
                    <h5 class="card-title mb-0">Homepage Creatives</h5>
                </div>
                <div class="card-body">
                    <form id="creative-form2" action="{{ route('settings.creatives-count') }}" method="POST">
                        @csrf()

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="creative_count_homepage">Featured creatives count</label>
                                    <input type="number" class="form-control" name="creative_count_homepage" 
                                        value="{{ settings('creative_count_homepage') }}" min="1" max="{{ \App\Models\Creative::where('is_featured', 1)->count() }}">
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
