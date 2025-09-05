@extends('layouts.app')

@section('title', __('Jobs'))

@php
    $url_map = [
        '127.0.0.1:8000' => 'http://localhost:8000/',
        'localhost:8000' => 'http://localhost:8000/',
        'staging-api.adagencycreatives.com' => 'https://staging.adagencycreatives.com/',
        'api.adagencycreatives.com' => 'https://adagencycreatives.com/',
        'adagencycreatives-apis.test' => 'http://localhost:3000/',
    ];
    $site_url = $url_map[$_SERVER['HTTP_HOST']];
@endphp

@section('scripts')
<script src="{{ asset('/assets/js/custom.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script>
    var currentPage = 1;
    var totalPages = 1;
    var perPage = 10;
    var filters = {};

    function fetchData(page) {
        var requestData = {
            page: page,
            per_page: perPage
        };

        var jobTitle = $('#title').val();
        filters = {
            title: jobTitle,
        };

        Object.keys(filters).forEach(function(key) {
            if (filters[key] !== '-100' && filters[key] !== '') {
                requestData[`filter[${key}]`] = filters[key];
            }
        });

        requestData['sort'] = 'sort_order';

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
            error: function(xhr, status, error) {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to fetch jobs from the API.',
                    icon: 'error'
                });
            },
        });
    }

    function populateTable(jobs) {
        var tbody = $('#jobs-table tbody');
        tbody.empty();

        if (jobs.length === 0) {
            displayNoRecordsMessage(7);
        }

        $.each(jobs, function(index, job) {
            console.log("Job: " + job);

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

            var row = '<tr class="sortable-item" id="' + job.id + '">' +
                '<td>' + (index + 1) + '</td>' +
                '<td>' + (job?.sort_order ?? '') + '</td>' +
                '<td>' + (job?.agency_name || job?.agency?.name) + '</td>' +
                '<td><a href="{{ $site_url }}job/' + job.slug + '" target="_blank">' + job.title +
                '</a></td>' +
                '<td style="text-align:center; min-width: 100px;">' + job.apply_type + (job.apply_type == "External" ?
                    '<br><a class="btn btn-dark" href="' + job.external_link +
                    '" target="_blank">Apply Now</a>' : "") + '</td>' +
                '<td>' + job.category + '</td>' +
                '<td>' + job.employment_type + '</td>' +
                '<td>' + statusDropdown + '</td>' +
                '<td>' + roleBasedActions + '</td>' +
                '</tr>';
            tbody.append(row);
        });
    }

    $(document).ready(function() {
        fetchData(currentPage);

        $("#sortable-jobs-list").sortable({
            update: function(event, ui) {
                var order = $(this).sortable('toArray');
                var url = $(this).data('url');
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
                        Swal.fire({
                            title: 'Success',
                            text: 'Job order updated successfully.',
                            icon: 'success'
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to update job order.',
                            icon: 'error'
                        });
                    });
            }
        });
        $("#sortable-jobs-list").disableSelection();

        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            currentPage = 1;
            fetchData(currentPage);
        });

        $(document).on('click', '.delete-btn', function() {
            var resourceId = $(this).data('id');
            var csrfToken = '{{ csrf_token() }}';
            deleteConfirmation(resourceId, 'job', 'jobs', csrfToken);
        });

        $(document).on('change', '.status-dropdown', function() {
            var selectedStatus = $(this).val();
            var jobId = $(this).data('job-id');
            var csrfToken = '{{ csrf_token() }}';
            updateStatus(jobId, 'job', 'jobs', csrfToken, selectedStatus);
        });
    });
</script>
@endsection

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Job Filters</h5>
            </div>
            <div class="card-body">
                <form id="filter-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="title">Job Title</label>
                                <input type="text" class="form-control" id="title" placeholder="Search by title...">
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                            <table id="jobs-table" class="table table-striped dataTable no-footer dtr-inline"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sort Order</th>
                                        <th>Agency</th>
                                        <th>Title</th>
                                        <th>Apply Type</th>
                                        <th>Category</th>
                                        <th>Employment Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-jobs-list" data-url="{{ route('update-featured-jobs-order') }}">
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

{{-- This is the new section for the featured jobs count form --}}
<div class="row">
    <div class="col-md-12 col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Homepage Featured Jobs Count</h5>
            </div>
            <div class="card-body">
                <form id="jobs-count-form" action="{{ route('settings.jobs-count') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="jobs_count_homepage">Featured jobs count</label>
                                <input type="number" class="form-control" name="jobs_count_homepage"
                                    value="{{ settings('jobs_count_homepage') }}" min="1"
                                    max="{{ \App\Models\Job::where('is_featured', 1)->count() }}">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- End of new section --}}

@endsection
