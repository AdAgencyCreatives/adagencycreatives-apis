@extends('layouts.app')

@section('title', __('Jobs'))

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

    console.log(requestData);


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

        },
        error: function() {
            alert('Failed to fetch jobs from the API.');
        },

    });
}

function fetchCategories() {

    $.ajax({
        url: 'api/v1/get_categories',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log(response.data);
            populateFilter(response.data, '#category');
        },
        error: function() {
            alert('Failed to fetch categories from the API.');
        }
    });
}

function fetchIndustries() {

    var requestData = {
        per_page: -1
    };

    $.ajax({
        url: 'api/v1/industries',
        method: 'GET',
        data: requestData,
        dataType: 'json',
        success: function(response) {
            populateFilter(response.data, '#media');
            populateFilter(response.data, '#industry');
        },
        error: function() {
            alert('Failed to fetch industries from the API.');
        }
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

        var statusDropdown = '<select class="status-dropdown form-control form-select select2" data-job-id="' +
            job.id + '">' +
            '<option value="published" ' + (job.status === 'published' ? 'selected' : '') +
            '>Published</option>' +
            '<option value="pending" ' + (job.status === 'pending' ? 'selected' : '') + '>Pending</option>' +
            '<option value="approved" ' + (job.status === 'approved' ? 'selected' : '') + '>Approved</option>' +
            '<option value="rejected" ' + (job.status === 'rejected' ? 'selected' : '') + '>Rejected</option>' +
            '<option value="expired" ' + (job.status === 'expired' ? 'selected' : '') + '>Expired</option>' +
            '<option value="filled" ' + (job.status === 'filled' ? 'selected' : '') + '>Filled</option>' +
            '>Inactive</option>' +
            '</select>';

        var row = '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td>' + job.title + '</td>' +
            // '<td>' + job.description.substring(0, 30) + "..." + '</td>' +
            '<td>' + job.category + '</td>' +
            '<td>' + job.employement_type + '</td>' +
            '<td>' + displayJobOptionsBadges(job) + '</td>' +
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

    $(document).on('click', '.delete-btn', function() {
        var resourceId = $(this).data('id');
        var csrfToken = '{{ csrf_token() }}';
        deleteConfirmation(resourceId, 'job', 'jobs', csrfToken);
    });


    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        var selectedCategory = $('#category').val();
        var selectedLabels = $('#labels').val();
        var emp_type = $('#employement_type').val();
        var selectedStatus = $('#status').val();
        var title = $('#title').val();

        var selectedIndustry = $('#industry').val();
        var selectedMedia = $('#media').val();

        filters = {
            category_id: selectedCategory,
            employement_type: emp_type,
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
                        <div class="col-sm-12">
                            <table id="users-table" class="table table-striped dataTable no-footer dtr-inline"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <!-- <th>Description</th> -->
                                        <th>Category</th>
                                        <th>Employement Type</th>
                                        <th>Labels</th>
                                        <th>Industry</th>
                                        <th>Media</th>
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
                            <div class="dataTables_info" id="table_entries_info" role="status" aria-live="polite"></div>
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