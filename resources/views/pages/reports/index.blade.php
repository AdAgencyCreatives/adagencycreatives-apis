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
        url: 'api/v1/reports',
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

function populateTable(orders) {
    var tbody = $('#sales-table tbody');
    tbody.empty();
    console.log(orders);
    console.log(orders.length);
    if (orders.length === 0) {
        displayNoRecordsMessage(7);
    }

    $.each(orders, function(index, order) {
        var editUrl = "/users/" + order.id + "/details";
        var roleBasedActions = '';

            roleBasedActions = '<a href="' + editUrl +
                '">Details</a> | <a href="#" class="delete-user-btn" data-id="' +
                order.id + '">Delete</a>';
    
        var row = '<tr>' +
            '<td>' + order.id + '</td>' +
            '<td>' + order.user + '</td>' +
            '<td>' + getPlanBadge(order.plan) + '</td>' +
            '<td>' + order.amount + '</td>' +          
            '<td>' + order.created_at + '</td>' +
            '</tr>';
        tbody.append(row);
    });
}



$(document).ready(function() {
    fetchData(currentPage);

});
</script>
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
                            <table id="sales-table" class="table table-striped dataTable no-footer dtr-inline"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Plan</th>
                                        <th>Amount</th>
                                        <th>Created At</th>
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
