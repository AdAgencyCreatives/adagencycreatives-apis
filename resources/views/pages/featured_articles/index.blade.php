@extends('layouts.app')

@section('title', __('Featured Articles'))

@section('scripts')
<script src="{{ asset('/assets/js/custom.js') }}"></script>
<!-- This is responsible for sorting UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script>
    var currentPage = 1;
    var totalPages = 1;
    var perPage = 10;
    var filters = {};

    function fetchArticles() {
        $.ajax({
            url: 'api/v1/articles?filter[is_featured]=1&sort=order',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                populateGroupFilter_title(response.data, '#article');
            },
            error: function() {
                // Using a custom modal/message box instead of alert()
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to fetch NEWS Blog from the API.',
                    icon: 'error'
                });
            }
        });
    }

    function populateGroupFilter_title(articles, selectId) {
        var selectElement = $(selectId);
        selectElement.empty().append('<option value="-100">Select NEWS Blog</option>');
        if (Array.isArray(articles)) {
            $.each(articles, function(index, article) {
                var option = $('<option>', {
                    value: article.uuid,
                    text: article.title
                });
                selectElement.append(option);
            });
        } else {
            console.error("The data from the API is not an array:", articles);
        }
    }

    function fetchData_fitler(page, filters = []) {
        var requestData = {
            page: page,
            per_page: perPage
        };
        var selectedArticle = $('#article option:selected').text();
        filters = {};
        if (selectedArticle != 'Select NEWS Blog') {
            filters['title'] = selectedArticle;
        }
        Object.keys(filters).forEach(function(key) {
            if (filters[key] !== '-100') {
                requestData[`filter[${key}]`] = filters[key];
            }
        });
        $.ajax({
            url: 'api/v1/articles?filter[is_featured]=1&sort=order',
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
                // Using a custom modal/message box instead of alert()
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to filter NEWS Blogs from the API.',
                    icon: 'error'
                });
            }
        });
    }


    function fetchData(page, filters = []) {
        var requestData = {
            page: page,
            per_page: perPage
        };

        // Removed invalid filters for the articles model
        filters = {
            is_featured: 1,
        };

        Object.keys(filters).forEach(function(key) {
            if (filters[key] !== '-100') {
                requestData[`filter[${key}]`] = filters[key];
            }
        });

        $.ajax({
            url: 'api/v1/articles?filter[is_featured]=1&sort=order',
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
                alert('Failed to fetch articles from the API.');
            }
        });
    }

    function populateTable(articles) {
        var tbody = $('#topics-table tbody');
        tbody.empty();
        console.log(articles.length);
        if (articles.length === 0) {
            displayNoRecordsMessage(7);
        }

        $.each(articles, function(index, article) {

            var editUrl = "/articles/" + article.uuid + "/details";
            var roleBasedActions = '';

            roleBasedActions = '<a href="' + editUrl +
                '" target="_blank">Details</a>';

            var row = '<tr class="sortable-item" id="' + article.id + '">' +
                '<td>' + article.id + '</td>' +
                '<td class="sort-order" data-id="' + article.id + '">' + article.order + '</td>' +
                '<td>' + article.title + '</td>' +
                '<td>' + article.sub_title + '</td>' +
                '<td class="article-is_featured" >' + (article.is_featured ? 'Yes' : 'No') + '</td>' +
                '<td class="article-description" >' + article.description + '</td>' +
                '<td>' + roleBasedActions + '</td>' +

                '</tr>';
            tbody.append(row);
        });
    }

    $(document).ready(function() {

        fetchData();
        fetchArticles();
        $("#sortable-list").sortable({
            update: function(event, ui) {
                // Get the updated order
                var order = $(this).sortable('toArray');
                console.log(order);
                var url = $("#sortable-list").data('url');
                // Send an AJAX request to update the order in the backend
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
                        console.log(data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });

            }
        });
        $("#sortable-list").disableSelection();

        $('#filter-form').on('submit', function(e) {
            e.preventDefault();

            currentPage = 1;
            fetchData_fitler(currentPage);
        });

        $('table').on('dblclick', '.sort-order', function() {
            var currentText = $(this).text();
            var id = $(this).data('id');
            var inputField = $('<input>', {
                type: 'text',
                value: currentText
            });

            $(this).html(inputField);

            inputField.focus();
            inputField.on('blur', function() {
                var newText = $(this).val();
                $(this).parent().text(
                    newText);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    }
                });
                $.ajax({
                    url: '/update-featured-article-order',
                    method: 'POST',
                    data: {
                        article_id: id,
                        sort_order: newText
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status === 200) {
                            Swal.fire({
                                title: 'Success',
                                text: response.message,
                                icon: 'success'
                            });
                        }
                    },
                    error: function(error) {
                        if (error.responseJSON && error.responseJSON.errors) {
                            var errorMessages = error.responseJSON.errors;

                            // Process and display error messages
                            var errorMessage = '';
                            $.each(errorMessages, function(field, messages) {
                                errorMessage += field + ': ' + messages
                                    .join(', ') + '\n';
                            });

                            Swal.fire({
                                title: 'Validation Error',
                                text: errorMessage,
                                icon: 'error'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: "An error occurred during the update.",
                                icon: 'error'
                            });
                        }
                    }
                });
            });
        });

        @if(Session::has('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ Session::get('success') }}",
            confirmButtonText: 'OK'
        });
        @endif

    });
</script>
@endsection

@section('content')

@include('pages.featured_articles.filters')

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
                            <table id="topics-table" class="table table-striped dataTable no-footer dtr-inline"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sort Position</th>
                                        <th>Title</th>
                                        <th>Sub Title</th>
                                        <th>Is Featured</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="sortable-list" data-url="{{ route('update-featured-articles-order') }}">
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

<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Homepage articles</h5>
            </div>
            <div class="card-body">
                <form id="agency-form2" action="{{ route('settings.articles-count') }}" method="POST">
                    @csrf()

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="agency_count_homepage">Featured articles count</label>
                                <input type="number" class="form-control" name="agency_count_homepage"
                                    value="{{ settings('agency_count_homepage') }}" min="1"
                                    max="{{ \App\Models\Agency::where('is_featured', 1)->count() }}">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection