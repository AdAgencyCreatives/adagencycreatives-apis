@extends('layouts.app')

@section('title', __('Film Festivals'))

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
                url: 'api/v1/festivals',
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
                    alert('Failed to fetch attachments from the API.');
                },

            });
        }

        function populateTable(attachments) {
            // var img_div = $('#image_div');
            // img_div.empty(); //Un-Comment this line to create Show More effect

            var tbody = $('#attachments-table tbody');
            tbody.empty();

            if (attachments.length === 0) {
                displayNoRecordsMessage(11);
            }

            $.each(attachments, function(index, attachment) {

                var fileUrl = attachment.path;
                var roleBasedActions = '';

                roleBasedActions = '<a href="#" class="delete-btn" data-id="' +
                    attachment.id + '">Delete</a>';

                var imageExtensions = ['jpg', 'jpeg', 'png'];
                var videoExtensions = ['mp4'];

                var extension = fileUrl.split('.').pop().toLowerCase();

                var displayContent = ''; // Assuming you have a variable named displayContent

                if (imageExtensions.includes(extension)) {
                    // Display images in anchor tags
                    displayContent = '<a href="' + fileUrl + '" class="image-container" target="_blank">' +
                        '<img src="' + fileUrl + '" alt="' + attachment.name + '">' +
                        '</a>';
                } else if (videoExtensions.includes(extension)) {
                    // Display video using anchor tags
                    displayContent = '<a href="' + fileUrl + '" class="video-container" target="_blank">' +
                        '<video width="150" height="150" controls >' +
                        '<source src="' + fileUrl + '" type="video/mp4">' +
                        'Your browser does not support the video tag.' +
                        '</video>' +
                        '</a>';
                }

                var row = '<tr>' +
                    '<td>' + displayContent + '</td>' +
                    '<td>' + attachment.first_name + ' ' + attachment.last_name + '</td>' +
                    '<td>' + attachment.email + '</td>' +
                    '<td>' + attachment.title + '</td>' +
                    '<td>' + attachment.category + '</td>' +
                    '<td><span class="badge bg-primary me-2">' + attachment.created_at +
                    '<td>' + roleBasedActions + '</td>' +
                    '</tr>';
                tbody.append(row);
            });
        }

        function fetchUsers() {

            $.ajax({
                url: 'api/v1/get_users/festivals',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateUserFilter(response, '#all_users', 'attachments_count');
                },
                error: function() {
                    alert('Failed to fetch users from the API.');
                }
            });
        }

        function populateUserFilter(users, div_id) {
            var selectElement = $(div_id);

            $.each(users, function(index, user) {
                var userInfo = user.first_name + ' ' + user.last_name + ' (' + user.email + ')';

                var option = $('<option>', {
                    value: user.email,
                    text: userInfo
                });

                selectElement.append(option);
            });
        }

        $(document).ready(function() {
            fetchData(currentPage);
            fetchUsers();

            $(document).on('click', '.delete-btn', function() {
                var resourceId = $(this).data('id');
                var csrfToken = '{{ csrf_token() }}';
                deleteConfirmation(resourceId, 'entry', 'festivals', csrfToken);
            });


            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                var selectedUser = $('#all_users').val();
                var selectedCategory = $('#category').val();

                filters = {
                    email: selectedUser,
                    category: selectedCategory
                };

                currentPage = 1;
                fetchData(currentPage, filters);
            });


            $('#download-festival-btn').on('click', function() {

                var selectedUser = $('#all_users').val();
                var selectedCategory = $('#category').val();

                var filters = {
                    email: selectedUser,
                    category: selectedCategory
                };

                var requestData = {};

                Object.keys(filters).forEach(function(key) {
                    if (filters[key] !== '-100') {
                        requestData[`filter[${key}]`] = filters[key];
                    }
                });

                requestData['sort'] = '-created_at';
                console.log(requestData);

                var url = '/festivals/download?filter[email]=' + selectedUser + '&filter[category]=' +
                    selectedCategory;

                // trigger the click funtionality with target _blank, so that the download will start
                $('<a />', {
                        "download": "festivals.xlsx",
                        "href": url,
                        "target": "_blank"
                    }).appendTo("body")
                    .on("click", function() {
                        $(this).remove()
                    })[0].click()

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


    @include('pages.festivals.filters')

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

                                <table id="attachments-table" class="table table-striped dataTable no-footer dtr-inline"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>File</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Title</th>
                                            <th>Category</th>
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
