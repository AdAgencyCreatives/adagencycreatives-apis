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
                url: 'api/v1/attachments',
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
            var img_div = $('#image_div');
            img_div.empty(); //Un-Comment this line to create Show More effect

            if (attachments.length === 0) {
                displayNoRecordsMessage(11);
            }

            $.each(attachments, function(index, attachment) {

                var extension = attachment.extension;
                var fileUrl = attachment.url;

                if (extension === 'jpg' || extension === 'png') {
                    // Display images in img tags
                    var imageContainer = '<div class="image-container">' +
                        '<img src="' + fileUrl + '" alt="' + attachment.name + '">' +
                        '</div>';
                    img_div.append(imageContainer);
                } else if (extension === 'spotlight') {
                    // Display video using video tag
                    var videoContainer = '<div class="video-container">' +
                        '<video controls>' +
                        '<source src="' + fileUrl + '" type="video/mp4">' +
                        'Your browser does not support the video tag.' +
                        '</video>' +
                        '</div>';
                    img_div.append(videoContainer);
                } else if (extension === 'pdf') {
                    // Display a button to download PDF
                    var downloadButton = '<div class="pdf-container">' +
                        '<a href="' + fileUrl + '" download="file.pdf">Download PDF</a>' +
                        '</div>';
                    img_div.append(downloadButton);
                } else if (extension === 'doc') {
                    // Display a button to download PDF
                    var downloadButton = '<div class="pdf-container">' +
                        '<a href="' + fileUrl + '" download="file.pdf">Download Document</a>' +
                        '</div>';
                    img_div.append(downloadButton);
                }
            });
        }

        function fetchUsers() {

            $.ajax({
                url: 'api/v1/get_users/attachments',
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
            // fetchGroups();

            $(document).on('click', '.delete-btn', function() {
                var resourceId = $(this).data('id');
                var csrfToken = '{{ csrf_token() }}';
                deleteConfirmation(resourceId, 'post', 'posts', csrfToken);
            });


            $('#filter-form').on('submit', function(e) {
                e.preventDefault();
                var selectedUser = $('#all_users').val();
                var selectedResourceType = $('#resource_type').val();
                var selectedStatus = $('#status').val();

                filters = {
                    user_id: selectedUser,
                    resource_type: selectedResourceType,

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


    @include('pages.attachments._inc.filters')

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
                            <div class="col-sm-12" id="image_div">

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
