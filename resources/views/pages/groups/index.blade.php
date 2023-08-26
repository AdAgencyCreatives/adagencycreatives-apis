@extends('layouts.app')

@section('title', __('Groups'))

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
        url: 'api/v1/groups',
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
        }
    });
}

function populateTable(groups) {
    var tbody = $('#users-table tbody');
    tbody.empty();
    console.log(groups);
    console.log(groups.length);
    if (groups.length === 0) {
        displayNoRecordsMessage(7);
    }

    $.each(groups, function(index, group) {
        var editUrl = "/groups/" + group.id + "/details";
        var roleBasedActions = '';


        roleBasedActions = '<a href="' + editUrl +
            '">Details</a> | <a href="#" class="delete-user-btn" data-id="' +
            group.uuid + '">Delete</a>';

        var statusDropdown =
            '<select class="status-dropdown form-control form-select select2" data-group-id="' +
            group.uuid + '">' +
            '<option value="public" ' + (group.status === 'public' ? 'selected' : '') + '>Public</option>' +
            '<option value="private" ' + (group.status === 'private' ? 'selected' : '') + '>Private</option>' +
            '<option value="hidden" ' + (group.status === 'hidden' ? 'selected' : '') +
            '>Hidden</option>' +
            '</select>';

        var row = '<tr>' +
            '<td>' + group.id + '</td>' +
            '<td>' + group.name + '</td>' +
            '<td>' + group.description + '</td>' +
            '<td>' + statusDropdown + '</td>' +
            '<td>' + group.created_at + '</td>' +
            '<td>' + roleBasedActions + '</td>' +
            '</tr>';
        tbody.append(row);
    });
}



$(document).ready(function() {
    fetchData(currentPage);

    $(document).on('click', '.delete-user-btn', function() {
        var resourceId = $(this).data('id');
        var csrfToken = '{{ csrf_token() }}';
        console.log(csrfToken);
        deleteConfirmation(resourceId, 'group', 'groups', csrfToken);
    });


    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        var selectedRole = $('#role').val();
        var selectedStatus = $('#status').val();
        var username = $('#username').val();
        var email = $('#email').val();

        filters = {
            role: selectedRole,
            status: selectedStatus,
            username: username,
            email: email,
        };
        currentPage = 1;
        fetchData(currentPage, filters);
    });

    $(document).on('change', '.status-dropdown', function() {
        var selectedStatus = $(this).val();
        var userId = $(this).data('group-id');
        var csrfToken = '{{ csrf_token() }}';
        updateStatus(userId, 'group', 'groups', csrfToken, selectedStatus);
    });

});
</script>
@endsection

@section('content')


@include('pages.groups._inc.filters')

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
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
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