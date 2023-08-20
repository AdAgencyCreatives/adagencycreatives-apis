@extends('layouts.app')

@section('title', 'Job Details')

@section('scripts')

<script>
function populateApplications(applications) {
    console.log(applications);
    var applicationsContainer = $('#applications-container');

    applications.forEach(function(application) {
        var applicationCard = $('<div class="card">');

        var cardHeader = $('<div class="card-header px-4 pt-4">');
        cardHeader.append(
            '<div style="float:right"><a href="' + application.resume_url + '" class="btn btn-primary mt-n1">Download Resume <i class="fas fa-download"></i></a></div>'
        );
        cardHeader.append('<h5 class="card-title mb-0">' + application.user + '</h5>');
        cardHeader.append('<div class="badge bg-warning my-2">' + application.status + '</div>');
        applicationCard.append(cardHeader);

        var cardBody = $('<div class="card-body px-4 pt-2">');
        cardBody.append('<p>' + application.message + '</p>');
        applicationCard.append(cardBody);

        var listGroup = $('<ul class="list-group list-group-flush">');
        var listItem = $('<li class="list-group-item px-4 pb-4">');
        listItem.append('<p class="mb-2 fw-bold">Applied at: <span style="float:right">' + application
            .created_at + '</span></p>');
        listGroup.append(listItem);
        applicationCard.append(listGroup);

        applicationsContainer.append(applicationCard);
    });
}

function fetchApplications() {
    var filters = {
        'job_id': "{{ $job->uuid }}",
    };
    var requestData = {};

    Object.keys(filters).forEach(function(key) {
        if (filters[key] !== '-100') {
            requestData[`filter[${key}]`] = filters[key];
        }
    });

    $.ajax({
        url: '/api/v1/applications',
        method: 'GET',
        data: requestData,
        dataType: 'json',
        success: function(response) {
            populateApplications(response.data);


        },
        error: function() {
            alert('Failed to fetch users from the API.');
        },

    });
}
$(document).ready(function() {

    fetchApplications();


});
</script>
@endsection

@section('content')
<h1 class="h3 mb-3">Job Details</h1>

<div id="error-messages" class="alert alert-danger alert-dismissible" style="display: none;" role="alert">
    <div class="alert-message">
        <strong>Error!</strong> Please fix the following issues:
        <ul></ul>
    </div>
</div>

<div class="row">

    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-body">
                <h1>{{ $job->title }}</h1>
            </div>
        </div>

        <div class="card">
            <grammarly-extension data-grammarly-shadow-root="true"
                style="position: absolute; top: 0px; left: 0px; pointer-events: none;" class="dnXmp">
            </grammarly-extension>
            <grammarly-extension data-grammarly-shadow-root="true"
                style="position: absolute; top: 0px; left: 0px; pointer-events: none;" class="dnXmp">
            </grammarly-extension>
            <div class="card-header">
                <h5 class="card-title mb-0">Description</h5>
            </div>
            <div class="card-body">
                <textarea class="form-control" rows="2" placeholder="Textarea" spellcheck="false"
                    style="height: 225px;">{{ $job->description }}</textarea>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <h1>Applications</h1>
        <div id="applications-container"></div>
    </div>
</div>
@endsection