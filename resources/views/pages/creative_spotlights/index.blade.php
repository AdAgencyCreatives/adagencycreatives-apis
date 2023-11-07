@extends('layouts.app')

@section('title', __('Media'))

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
                url: 'api/v1/creative-spotlights',
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
                    alert('Failed to fetch spotlights from the API.');
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

                var fileUrl = attachment.url;

                roleBasedActions = '<a href="/creative_spotlights/' + attachment.id +
                    '/edit" class="detail-btn">Edit</a> | <a href="#" class="delete-btn" data-id="' +
                    attachment.id + '">Delete</a>';

                // Display video using anchor tags
                displayContent = '<a href="' + fileUrl + '" class="video-container" target="_blank">' +
                    '<video width="150" height="150" controls poster="abc">' +
                    '<source src="' + fileUrl + '" type="video/mp4">' +
                    'Your browser does not support the video tag.' +
                    '</video>' +
                    '</a>';


                var statusDropdown =
                    '<select class="status-dropdown form-control form-select select2" data-post-id="' +
                    attachment.id + '">' +
                    '<option value="pending" ' + (attachment.status === 'pending' ? 'selected' : '') +
                    '>Pending</option>' +
                    '<option value="approved" ' + (attachment.status === 'approved' ? 'selected' : '') +
                    '>Approved</option>' +
                    '<option value="rejected" ' + (attachment.status === 'rejected' ? 'selected' : '') +
                    '>Rejected</option>' +
                    '</select>';

                var row = '<tr>' +
                    '<td>' + displayContent + '</td>' +
                    '<td>' + attachment.title + '</td>' +
                    '<td>' + attachment.slug + '</td>' +
                    '<td>' + statusDropdown + '</td>' +
                    '<td><span class="badge bg-primary me-2">' + attachment.created_at +
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
                deleteConfirmation(resourceId, 'Spotlight', 'creative-spotlights', csrfToken);
            });


            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                var selectedUser = $('#all_users').val();
                var selectedResourceType = $('#resource_type').val();
                var selectedStatus = $('#status').val();

                filters = {
                    user_id: selectedUser,
                    resource_type: selectedResourceType,
                    status: selectedStatus

                };

                currentPage = 1;
                fetchData(currentPage, filters);
            });


            $(document).on('change', '.status-dropdown', function() {
                var selectedStatus = $(this).val();
                var postId = $(this).data('post-id');
                var csrfToken = '{{ csrf_token() }}';

                updateStatus(postId, 'Spotlight', 'creative-spotlights', csrfToken, selectedStatus);
            });
        });

        function updateStatus(userId, resource, url, csrfToken, selectedStatus) {
            Swal.fire({
                title: 'Confirm Update',
                text: 'Are you sure you want to update this ' + resource + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateResourceStatus(userId, selectedStatus, url, csrfToken);
                }
            });
        }
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
                                            <th>Title</th>
                                            <th>Slug</th>
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
