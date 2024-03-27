@extends('layouts.app')

@section('title', __('Jobs'))

@php

    $url_map = [
        '127.0.0.1:8000' => 'http://localhost:3000/',
        'staging-api.adagencycreatives.com' => 'https://staging.adagencycreatives.com/',
        'api.adagencycreatives.com' => 'https://adagencycreatives.com/',
    ];

    $site_url = $url_map[$_SERVER['HTTP_HOST']];

@endphp

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
                url: 'api/v1/jobs',
                method: 'GET',
                data: requestData,
                dataType: 'json',
                success: function(response) {
                    totalPages = response.meta.last_page;
                    populateTable(response.data);
                    updatePaginationButtons(response.links, response.meta.links);
                    updateTableInfo(response.meta);
                    $('.double-scroll').doubleScroll();
                },
                error: function() {
                    alert('Failed to fetch jobs from the API.');
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

                var editUrl = "/jobs/" + job.id + "/details";
                var roleBasedActions = '';

                if (job.role === 'admin') {
                    roleBasedActions = 'Admin';
                } else {
                    roleBasedActions = '<a href="' + editUrl +
                        '">Details</a> | <a href="#" class="delete-btn" data-id="' +
                        job.id + '">Delete</a>';
                }

                var statusDropdown =
                    '<select class="status-dropdown form-control form-select select2" data-job-id="' +
                    job.id + '">' +
                    '<option value="draft" ' + (job.status === 'draft' ? 'selected' : '') +
                    '>Draft</option>' +
                    '<option value="pending" ' + (job.status === 'pending' ? 'selected' : '') +
                    '>Pending</option>' +
                    '<option value="approved" ' + (job.status === 'approved' ? 'selected' : '') +
                    '>Approved</option>' +
                    '<option value="rejected" ' + (job.status === 'rejected' ? 'selected' : '') +
                    '>Rejected</option>' +
                    '<option value="expired" ' + (job.status === 'expired' ? 'selected' : '') +
                    '>Expired</option>' +
                    '<option value="filled" ' + (job.status === 'filled' ? 'selected' : '') +
                    '>Filled</option>' +
                    '</select>';

                var row = '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>\
                            <div class="user-details">\
                                <div><a href="{{ $site_url }}agency/' + job.agency.slug + '" target="_blank">' + job
                    .agency.name + '</a></div>\
                            </div>\
                        </td>' +
                    '<td><a href="{{ $site_url }}job/' + job.slug + '" target="_blank">' + job.title +
                    '</a></td>' +
                    '<td style="text-align:center; min-width: 100px;">' + job.apply_type + (job.apply_type == "External" ?
                        '<br><a class="btn btn-dark" href="' + job.external_link + '" target="_blank">Apply Now</a>' : "") + '</td>' +
                    // '<td>' + job.description.substring(0, 30) + "..." + '</td>' +
                    '<td>' + job.category + '</td>' +
                    '<td>' + job.employment_type + '</td>' +
                    '<td>' + displayJobOptionsBadges(job.workplace_preference) + '</td>' +
                    '<td>' + job.industry_experience + '</td>' +
                    '<td>' + job.media_experience + '</td>' +
                    '<td>' + statusDropdown + '</td>' +
                    // '<td>' + job.experience + '</br> ' + job.salary_range + '</td>' +


                    '<td><span class="badge bg-primary me-2">' + job.created_at +
                    '</span><span class="badge bg-danger me-2">' + job.expired_at + '</span></td>' +
                    '<td>' + roleBasedActions + '</td>' +
                    '</tr>';
                tbody.append(row);
            });
        }



        $(document).ready(function() {
            fetchData(currentPage);
            fetchCategories();
            fetchIndustries();
            fetchMedias();

            $(document).on('click', '.delete-btn', function() {
                var resourceId = $(this).data('id');
                var csrfToken = '{{ csrf_token() }}';
                deleteConfirmation(resourceId, 'job', 'jobs', csrfToken);
            });


            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                var selectedCategory = $('#category').val();
                var selectedLabels = $('#labels').val();
                var apply_type = $('#apply_type').val();
                var emp_type = $('#employment_type').val();
                var selectedStatus = $('#status').val();
                var title = $('#title').val();

                var selectedIndustry = $('#industry').val();
                var selectedMedia = $('#media').val();

                filters = {
                    category_id: selectedCategory,
                    apply_type: apply_type,
                    employment_type: emp_type,
                    title: title,
                    status: selectedStatus,
                };

                if (selectedIndustry && selectedIndustry.length > 0) {
                    filters.industry_experience = selectedIndustry.join(',');
                }

                if (selectedMedia && selectedMedia.length > 0) {
                    filters.media_experience = selectedMedia.join(',');
                }

                for (var i = 0; i < selectedLabels.length; i++) {
                    filters[selectedLabels[i]] = 1;
                }

                currentPage = 1;
                fetchData(currentPage, filters);
            });


            $(document).on('change', '.status-dropdown', function() {
                var selectedStatus = $(this).val();
                var jobId = $(this).data('job-id');
                var csrfToken = '{{ csrf_token() }}';
                updateStatus(jobId, 'job', 'jobs', csrfToken, selectedStatus);
            });

            $.ajax({
                url: '/api/v1/employment_types',
                type: "GET",
                success: function(data) {
                    // Clear existing options
                    $("#employment_type").empty();

                    // Add the default option
                    $("#employment_type").append('<option value="-100"> Select Type</option>');

                    // Populate the dropdown with options from the API
                    $.each(data, function(index, type) {
                        $("#employment_type").append('<option value="' + type + '" ' + '>' +
                            type + '</option>');
                    });

                    // Refresh the Select2 plugin
                    $("#employment_type").select2("destroy").select2();
                },
                error: function(error) {
                    console.error("Error fetching employment types:", error);
                }
            });
            $('.double-scroll').doubleScroll();
        });
    </script>
@endsection

@section('content')


    @include('pages.jobs._inc.filters')

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
                            <div class="col-sm-12 double-scroll">
                                <table id="users-table" class="table table-striped dataTable no-footer dtr-inline"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Agency</th>
                                            <th>Title</th>
                                            <th>Apply Type</th>
                                            <!-- <th>Job Post</th> -->
                                            <th>Category</th>
                                            <th>Employment Type</th>
                                            <th>Workplace Preference</th>
                                            <th>Industry Experience</th>
                                            <th>Media Experience</th>
                                            <th>Status</th>
                                            <th>Created At / Expired At</th>
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
