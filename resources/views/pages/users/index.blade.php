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

            var currentUrl = window.location.href;
            if (currentUrl.includes('role=2')) {
                $('#role').val('2');
                $('#role').trigger('change');
                $('#role').prop('disabled', true);
            }
            if (currentUrl.includes('role=3')) {
                $('#role').val('3');
                $('#role').trigger('change');
                $('#role').prop('disabled', true);

                //Enable agency slug filter
                $('#agency_slug_filter').removeClass('d-none');
            }
            if (currentUrl.includes('role=4')) {
                $('#role').val('4');
                $('#role').trigger('change');
                $('#role').prop('disabled', true);
            }

            var selectedRole = $('#role').val();
            var selectedStatus = $('#status').val();
            var firstname = $('#first_name').val();
            var lastname = $('#last_name').val();
            var username = $('#username').val();
            var email = $('#email').val();
            var company_slug = $('#agency_slug').val();

            filters = {
                role: selectedRole,
                status: selectedStatus,
                username: username,
                email: email,
                first_name: firstname,
                last_name: lastname,
                company_slug: company_slug
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
                    alert('Nothing found');
                }
            });
        }

        function populateTable(users) {
            var tbody = $('#users-table tbody');
            tbody.empty();
            console.log(users);
            console.log(users.length);
            if (users.length === 0) {
                displayNoRecordsMessage(7);
            }

            var current_logged_in_userid = "{{ auth()->id() }}";

            $.each(users, function(index, user) {
                var editUrl = "/users/" + user.id + "/details";
                var roleBasedActions = '';

                if (current_logged_in_userid == user.id) {
                    roleBasedActions = '<a href="' + editUrl +
                        '" target="_blank">Details</a>';
                } else {
                    roleBasedActions = '<a href="' + editUrl +
                        '">Details</a> | <a href="#" class="delete-user-btn" data-id="' +
                        user.uuid + '">Delete</a>';
                }

                var statusDropdown =
                    '<select class="status-dropdown form-control form-select select2" data-user-id="' +
                    user.uuid + '">' +
                    '<option value="pending" ' + (user.status === 'pending' ? 'selected' : '') +
                    '>Pending</option>' +
                    '<option value="active" ' + (user.status === 'active' ? 'selected' : '') + '>Active</option>' +
                    '<option value="inactive" ' + (user.status === 'inactive' ? 'selected' : '') +
                    '>Inactive</option>' +
                    '</select>';

                var row = '<tr>' +
                    '<td>' + user.id + '</td>' +
                    '<td>' +
                    '<a href="' + user.image + '" class="image-container" target="_blank">' +
                    '<img src="' + user.image + '">' +
                    '</a>' +
                    '</td>' +
                    '<td>' + user.first_name + ' ' + user.last_name + '</td>' +
                    '<td>' + user.username + '</br>' + user.email + '</td>' +
                    // Concatenate email and username in the same column
                    '<td>' + getRoleBadge(user.role) + '</td>' +
                    '<td>' + statusDropdown + '</td>' +
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

                currentPage = 1;
                fetchData(currentPage);
            });

            $(document).on('change', '.status-dropdown', function() {
                var selectedStatus = $(this).val();
                var userId = $(this).data('user-id');
                var csrfToken = '{{ csrf_token() }}';
                updateStatus(userId, 'user', 'users', csrfToken, selectedStatus);
            });

        });
    </script>
@endsection

@section('styles')
    <style>
        .image-container {
            width: auto;
            /* Fixed width for each image container */
            height: 150px;
            /* Fixed height for each image container */
            overflow: hidden;
            display: inline-block;
            /* Display images in a row */
            margin: 10px;
            /* Add some margin between image containers */
        }

        .image-container img {
            max-width: 100%;
            /* Make the image responsive */
            max-height: 100%;
            /* Make the image responsive */
            object-fit: contain;
            /* Maintain aspect ratio and fill the container */
        }
    </style>
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
                                            <th>Profile</th>
                                            <th>Name</th>
                                            <th>Username/Email</th>
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
