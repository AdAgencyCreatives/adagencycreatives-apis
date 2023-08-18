@extends('layouts.app')

@section('title', __('Users'))

@section('scripts')
<script src="{{ asset('/assets/js/custom.js') }}"></script>
<script>
var currentPage = 1;
var totalPages = 1;
var perPage = 10;

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
        url: 'api/v1/users',
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

function populateTable(users) {
    var tbody = $('#users-table tbody');
    tbody.empty();


    $.each(users, function(index, user) {

        var editUrl = "/users/" + user.id + "/details";
        var roleBasedActions = '';

        if (user.role === 'admin') {
            roleBasedActions = 'Admin';
        } else {
            roleBasedActions = '<a href="' + editUrl +
                '">Edit</a> | <a href="#" class="delete-user-btn" data-id="' +
                user.uuid + '">Delete</a>';
        }

        var row = '<tr>' +
            '<td>' + user.id + '</td>' +
            '<td>' + user.first_name + ' ' + user.last_name + '</td>' +
            '<td>' + user.email + '</td>' +
            '<td>' + user.role + '</td>' +
            '<td>' + user.status + '</td>' +
            '<td>' + user.created_at + '</td>' +
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
        deleteConfirmation(resourceId, 'user', 'users', csrfToken);
    });


    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        var selectedRole = $('#role').val();
        var selectedStatus = $('#status').val();
        var username = $('#username').val();
        var email = $('#email').val();

        var filters = {
            role: selectedRole,
            status: selectedStatus,
            username: username,
            email: email,

        };

        console.log(filters);
        fetchData(currentPage, filters);
    });
});
</script>
@endsection

@section('content')


@include('pages.users._inc.filters')

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
                                        <option value="3">3</option>
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
                                        <th>Email</th>
                                        <th>Role</th>
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
