@extends('layouts.app')

@section('title', __('Posts'))

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
                url: 'api/v1/posts',
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
                    alert('Failed to fetch posts from the API.');
                },

            });
        }

        function populateTable(posts) {
            var tbody = $('#users-table tbody');
            tbody.empty();

            if (posts.length === 0) {
                displayNoRecordsMessage(11);
            }

            $.each(posts, function(index, post) {
                var editUrl = "/posts/" + post.id + "/details";
                var roleBasedActions = '';

                if (post.role === 'admin') {
                    roleBasedActions = 'Admin';
                } else {
                    roleBasedActions = '<a href="' + editUrl +
                        '">Details</a> | <a href="#" class="delete-btn" data-id="' +
                        post.id + '">Delete</a>';
                }

                var statusDropdown =
                    '<select class="status-dropdown form-control form-select select2" data-post-id="' +
                    post.id + '">' +
                    '<option value="draft" ' + (post.status === 'draft' ? 'selected' : '') + '>Draft</option>' +
                    '<option value="published" ' + (post.status === 'published' ? 'selected' : '') +
                    '>Published</option>' +
                    '<option value="archived" ' + (post.status === 'archived' ? 'selected' : '') +
                    '>Archived</option>' +
                    '</select>';

                var row = '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + post.author + '</td>' +
                    '<td>' + post.content.substring(0, 60) + '...</td>' +
                    '<td>' + statusDropdown + '</td>' +
                    '<td><span class="badge bg-primary me-2">' + post.created_at +
                    '<td>' + roleBasedActions + '</td>' +
                    '</tr>';
                tbody.append(row);
            });
        }

        function fetchUsers() {

            $.ajax({
                url: 'api/v1/get_users/posts',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateUserFilter(response, '#all_users', 'posts_count');
                },
                error: function() {
                    alert('Failed to fetch users from the API.');
                }
            });
        }

        function fetchGroups() {

            $.ajax({
                url: 'api/v1/get_groups',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateGroupFilter(response, '#group');
                },
                error: function() {
                    alert('Failed to fetch groups from the API.');
                }
            });
        }



        $(document).ready(function() {
            fetchData(currentPage);
            fetchUsers();
            fetchGroups();

            $(document).on('click', '.delete-btn', function() {
                var resourceId = $(this).data('id');
                var csrfToken = '{{ csrf_token() }}';
                deleteConfirmation(resourceId, 'post', 'posts', csrfToken);
            });


            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                var selectedUser = $('#all_users').val();
                var selectedGroup = $('#group').val();
                var selectedStatus = $('#status').val();

                filters = {
                    user_id: selectedUser,
                    group_id: selectedGroup,
                    status: selectedStatus,
                };

                currentPage = 1;
                fetchData(currentPage, filters);
            });


            $(document).on('change', '.status-dropdown', function() {
                var selectedStatus = $(this).val();
                var postId = $(this).data('post-id');
                var csrfToken = '{{ csrf_token() }}';
                updateStatus(postId, 'post', 'posts', csrfToken, selectedStatus);
            });
        });
    </script>
@endsection

@section('content')


    @include('pages.posts._inc.filters')

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
                                            <th>Author</th>
                                            <th>Content</th>
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
