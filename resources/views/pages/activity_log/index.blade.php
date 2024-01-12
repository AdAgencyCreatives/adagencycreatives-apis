@extends('layouts.app')

@section('title', __('Logs'))

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

            Object.keys(filters).forEach(function(key) {
                if (filters[key] !== '-100') {
                    requestData[`filter[${key}]`] = filters[key];
                }
            });

            requestData['sort'] = '-created_at';

            $.ajax({
                url: '/api/v1/activities',
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
                    alert('Failed to fetch logs from the API.');
                },

            });
        }

        function populateTable(jobs) {
            var tbody = $('#users-table tbody');
            tbody.empty();

            if (jobs.length === 0) {
                displayNoRecordsMessage(11);
            }

            $.each(jobs, function(index, job) {

                var editUrl = "/activity/log/" + job.id + "/details";
                var roleBasedActions = '';

                if (job.role === 'admin') {
                    roleBasedActions = 'Admin';
                } else {
                    roleBasedActions = '<a href="' + editUrl +
                        '">Details</a>';
                }


                var row = '<tr>' +
                    '<td>' + job.id + '</td>' +
                    '<td>' + job.user.first_name + ' ' + job.user.last_name + '</td>' +
                    '<td>' + job.type + '</td>' +
                    '<td>' + job.message + '</td>' +
                    '<td><span class="badge bg-primary me-2">' + job.human_readable_date +
                    '<td>' + roleBasedActions + '</td>' +
                    '</tr>';
                tbody.append(row);
            });
        }

        function fetchUsers() {

            $.ajax({
                url: '/api/v1/get_users',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateUserFilter(response, '#all_users');
                },
                error: function() {
                    alert('Failed to fetch users from the API.');
                }
            });
        }

        function populateUserFilter(users, div_id) {

            var selectElement = $(div_id);
            $.each(users, function(index, user) {
                var option = $('<option>', {
                    value: user.id,
                    text: user.first_name + ' ' + user.last_name + ' - ' + user.email
                });

                selectElement.append(option);
            });
        }



        $(document).ready(function() {
            fetchData(currentPage);
            fetchUsers();

            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                var selectedUser = $('#all_users').val();
                var selectedType = $('#type').val();

                console.log(selectedUser);
                console.log(selectedType);
                filters = {
                    user_id: selectedUser,
                    type: selectedType,
                };
                currentPage = 1;
                fetchData(currentPage, filters);
            });

        });
    </script>
@endsection

@section('content')


    @include('pages.activity_log._inc.filters')

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
                                        </select> entries</label></div>
                            </div>

                        </div>
                        <div class="row dt-row">
                            <div class="col-sm-12">
                                <table id="users-table" class="table table-striped dataTable no-footer dtr-inline"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Type</th>
                                            <th>Message</th>
                                            <th>Created At</th>
                                            <th>Details</th>
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
