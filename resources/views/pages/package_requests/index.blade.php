@extends('layouts.app')

@section('title', 'Job Requests')

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
                url: 'api/v1/package-requests',
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
                    alert('Failed to fetch jobs requests from the API.');
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

                var editUrl = "/job-requests/" + job.id + "/details";
                var roleBasedActions = '';

                roleBasedActions = '<a href="' + editUrl +
                    '">Details</a> | <a href="#" class="delete-btn" data-id="' +
                    job.id + '">Delete</a>';


                var statusDropdown =
                    '<select class="status-dropdown form-control form-select select2" data-job-id="' +
                    job.id + '">' +
                    '<option value="pending" ' + (job.status === 'pending' ? 'selected' : '') +
                    '>Pending</option>' +
                    '<option value="approved" ' + (job.status === 'approved' ? 'selected' : '') +
                    '>Approved</option>' +
                    '<option value="rejected" ' + (job.status === 'rejected' ? 'selected' : '') +
                    '>Rejected</option>';


                var row = '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + job.category + '</td>' +
                    '<td>' + job.agency_name + '</td>' +
                    '<td>' + statusDropdown + '</td>' +
                    '<td><span class="badge bg-primary me-2">' + job.created_at +
                    '<td>' + roleBasedActions + '</td>' +
                    '</tr>';
                tbody.append(row);
            });
        }

        $(document).ready(function() {
            fetchData(currentPage);

            $(document).on('click', '.delete-btn', function() {
                var resourceId = $(this).data('id');
                var csrfToken = '{{ csrf_token() }}';
                deleteConfirmation(resourceId, 'job request', 'package-requests', csrfToken);
            });


            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                var selectedStatus = $('#status').val();

                filters = {
                    status: selectedStatus,
                };

                currentPage = 1;
                fetchData(currentPage, filters);
            });


            $(document).on('change', '.status-dropdown', function() {
                var selectedStatus = $(this).val();
                var jobId = $(this).data('job-id');
                var csrfToken = '{{ csrf_token() }}';
                updateStatus(jobId, 'job request', 'package-requests', csrfToken, selectedStatus);
            });
        });
    </script>
@endsection

@section('content')


    @include('pages.package_requests._inc.filters')

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
                                <table id="users-table" class="table table-striped dataTable no-footer dtr-inline"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Agency</th>
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
