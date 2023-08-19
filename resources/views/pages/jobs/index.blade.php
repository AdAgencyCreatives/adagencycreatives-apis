@extends('layouts.app')

@section('title', __('Users'))

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
            alert('Failed to fetch users from the API.');
        },
        complete: function() {
            fetchCategories();
        }
    });
}

function fetchCategories() {

    var requestData = {
        per_page: -1
    };

    $.ajax({
        url: 'api/v1/categories',
        method: 'GET',
        data: requestData,
        dataType: 'json',
        success: function(response) {
            populateFilter(response.data);
        },
        error: function() {
            alert('Failed to fetch categories from the API.');
        }
    });
}

function populateFilter(categories) {
    var selectElement = $('#category');

    $.each(categories, function(index, category) {
        var option = $('<option>', {
            value: category.id,
            text: category.name
        });

        selectElement.append(option);
    });
}

function populateTable(jobs) {
    var tbody = $('#users-table tbody');
    tbody.empty();

    if (jobs.length === 0) {
        displayNoRecordsMessage(7);
    }

    $.each(jobs, function(index, job) {
        var editUrl = "/users/" + job.id + "/details";
        var roleBasedActions = '';

        if (job.role === 'admin') {
            roleBasedActions = 'Admin';
        } else {
            roleBasedActions = '<a href="' + editUrl +
                '">Edit</a> | <a href="#" class="delete-btn" data-id="' +
                job.id + '">Delete</a>';
        }

        var row = '<tr>' +
            '<td>' + (index + 1) + '</td>' +
            '<td>' + job.title + '</td>' +
            '<td>' + job.description.substring(0, 30) + "..." + '</td>' +
            '<td>' + job.category + '</td>' +
            '<td>' + job.employement_type + '</td>' +
            '<td>' + job.industry_experience + '</td>' +
            '<td>' + job.media_experience + '</td>' +
            '<td>' + job.salary_range + '</td>' +
            '<td>' + job.experience + '</td>' +
            '<td>' + displayJobOptionsBadges(job) + '</td>' +
            '<td>' + job.created_at + '</td>' +
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
        deleteConfirmation(resourceId, 'job', 'jobs', csrfToken);
    });


    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        var selectedCategory = $('#category').val();
        var selectedLabels = $('#labels').val();
        var emp_type = $('#employement_type').val();
        var title = $('#title').val();

        filters = {
            category_id: selectedCategory,
            employement_type: emp_type,
            title: title,
        };

        for (var i = 0; i < selectedLabels.length; i++) {
            filters[selectedLabels[i]] = 1;
        }

        currentPage = 1;
        fetchData(currentPage, filters);
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
                                        <th>Description</th>
                                        <th>Category</th>
                                        <th>Employement Type</th>
                                        <th>Salary Range</th>
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
