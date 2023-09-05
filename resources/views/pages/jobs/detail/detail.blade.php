@extends('layouts.app')

@section('title', 'Job Details')

@section('scripts')
<script src="{{ asset('/assets/js/custom.js') }}"></script>
<script>
function populateApplications(applications) {

    var applicationsContainer = $('#applications-container');

    if (applications.length === 0) {
        // No applications found, display a beautiful message
        var noApplicationsMessage = $(
            '<div class="alert alert-secondary" style="padding: 16px;">No applications found.</div>');
        applicationsContainer.append(noApplicationsMessage);

    } else {
        applications.forEach(function(application) {
            var applicationCard = $('<div class="card">');

            var cardHeader = $('<div class="card-header px-4 pt-4">');
            cardHeader.append(
                '<div style="float:right"><a href="' + application.resume_url +
                '" class="btn btn-primary mt-n1">Download Resume <i class="fas fa-download"></i></a></div>'
            );
            var userNameLink = $('<a target="_blank" href="' + '/users/' + application.user_profile_id +
                    '/details' + '">')
                .text(application.user);
            cardHeader.append($('<h5 class="card-title mb-0">').append(userNameLink));
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

function fetchCategories() {

    $.ajax({
        url: '/api/v1/get_categories',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            populateFilter(response.data, '#category');
            var job_category = "{{ $job->category->uuid }}";
            $('#category').val(job_category);
            $('#category').trigger('change');
        },
        error: function() {
            alert('Failed to fetch categories from the API.');
        }
    });
}

function fetchJobObject() {

    $.ajax({
        url: '/api/v1/jobs/' + "{{ $job->uuid }}",
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            $("#job_options").html(displayJobOptionsBadges(response.data));
        },
        error: function() {
            alert('Failed to fetch job from the API.');
        }
    });
}

function fetchIndustries() {

    var requestData = {
        per_page: -1
    };

    $.ajax({
        url: '/api/v1/industries',
        method: 'GET',
        data: requestData,
        dataType: 'json',
        success: function(response) {
            populateFilter(response.data, '#media');
            var media_experience = "{{ $job->media_experience }}";
            var mediaArray = media_experience.split(',');
            mediaArray.forEach(function(uuid) {
                $('#media option[value="' + uuid + '"]').prop('selected', true);
            });
            $('#media').trigger('change');

            populateFilter(response.data, '#industry');
            var industry_experience = "{{ $job->industry_experience }}";
            var industryArray = industry_experience.split(',');
            industryArray.forEach(function(uuid) {
                $('#industry option[value="' + uuid + '"]').prop('selected', true);
            });
            $('#industry').trigger('change');

        },
        error: function() {
            alert('Failed to fetch industries from the API.');
        }
    });
}

$(document).ready(function() {
    fetchJobObject();
    fetchIndustries();
    fetchCategories();
    fetchApplications();

    $('#save-job').click(function(event) {
        event.preventDefault();

        var formData = $('form').serialize();

        $.ajax({
            type: 'PUT',
            url: '/api/v1/jobs/' + "{{ $job->uuid }}" + '/admin',
            data: formData,
            success: function(response) {
                console.log(response.data);
                if (response.data) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Job has been updated.',
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
                        errorMessage += field + ': ' + messages.join(', ') + '\n';
                    });

                    Swal.fire({
                        title: 'Validation Error',
                        text: errorMessage,
                        icon: 'error'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: error.message,
                        icon: 'error'
                    });
                }
            }
        });
    });

    $(".daterange").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: "Y-MM-DD"
        }
    });


});
</script>
@endsection

@section('styles')
<style>
#job_options .badge {
    margin-right: 5px;
}
</style>
@endsection
@section('content')
<h1 class="h3 mb-3">Job Details</h1>

<div id="error-messages" class="alert alert-danger alert-dismissible" style="display: none;" role="alert">
    <div class="alert-message">
        <strong>Error!</strong> Please fix the following issues:
        <ul></ul>
    </div>
</div>

@if(session('success'))
<x-alert type="success"></x-alert>
@endif

<div class="row">
    <div class="col-12 col-lg-6">
        <form action="{{ route('jobs.update', $job->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    <h1>
                        <input class="form-control" type="text" name="title" placeholder="Enter title of the job"
                            value="{{ $job->title }}" />

                    </h1>
                    <div id=job_options></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Description</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" name="description" rows="10" placeholder="Textarea"
                        spellcheck="false">{{ $job->description }}</textarea>
                </div>
            </div>

            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="agency_name"> Agency Name (Optional) </label>
                                    <input id="agency_name" class="form-control" type="text" name="agency_name"
                                        placeholder="Agency Name" value="{{ $job->agency_name }}" />
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <div class="mb-3 error-placeholder">
                                    <label class="form-label">Agency Logo</label>
                                    <div>
                                        <input type="file" class="validation-file" name="file">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="text-center">
                                <h4>Agency Logo</h4>
                                @if(isset($job->attachment))
                                <img class="rounded-circle img-responsive mt-2 lazy"
                                    src="{{ isset($job->attachment) ? asset('storage/' . $job->attachment->path) : asset('images/default.png') }}"
                                    alt="{{ $job->attachment->resource_type }}" width="100" height="100"
                                    style="border-radius: 50%;" />
                                @else
                                <p>No logo uploaded yet</p>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="status"> Status </label>
                                    <select name="status" id="status"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="published" @if($job->status == 'published') selected
                                            @endif>Published
                                        </option>
                                        <option value="approved" @if($job->status == 'approved') selected
                                            @endif>Approved
                                        </option>
                                        <option value="rejected" @if($job->status == 'rejected') selected
                                            @endif>Rejected
                                        </option>
                                        <option value="expired" @if($job->status == 'expired') selected @endif>Expired
                                        </option>
                                        <option value="filled" @if($job->status == 'filled') selected @endif>Filled
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="category"> Category </label>
                                    <select name="category_id" id="category"
                                        class="form-control form-select custom-select select2" data-toggle="select2">

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="salary_range"> Salary Range </label>
                                    <input id="salary_range" class="form-control" type="text" name="salary_range"
                                        placeholder="Enter Salary Range" value="{{ $job->salary_range }}" />
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="employement_type"> Employement Type </label>
                                    <select name="employement_type" id="employement_type"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="-100"> Select Type</option>
                                        <option value="Freelance" @if($job->employement_type == 'Freelance')
                                            selected @endif>Freelance</option>
                                        <option value="Contract" @if($job->employement_type == 'Contract')
                                            selected @endif>Contract</option>
                                        <option value="Part-time" @if($job->employement_type == 'Part-time')
                                            selected @endif>Part-time</option>
                                        <option value="Full-time" @if($job->employement_type == 'Full-time')
                                            selected @endif>Full-time</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label class="form-label" for="industry"> Industry Experience </label>
                                <select name="industry_experience[]" id="industry" required
                                    class="form-control form-select custom-select select2" multiple="multiple"
                                    data-toggle="select2">
                                    <option value="-100"> Select Industry</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label class="form-label" for="media"> Media </label>
                                <select name="media_experience[]" id="media" required
                                    class="form-control form-select custom-select select2" multiple="multiple"
                                    data-toggle="select2">
                                    <option value="-100"> Select Media</option>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="apply_type"> Apply Type </label>
                                <select name="apply_type" id="apply_type"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="Internal" @if($job->apply_type == 'Internal') selected
                                        @endif>Internal
                                    </option>
                                    <option value="External" @if($job->apply_type == 'External') selected
                                        @endif>External
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="external_link"> Expernal Link </label>
                                <a>
                                    <input id="external_link" class="form-control" type="url" name="external_link"
                                        value="{{ $job->external_link }}" />
                                </a>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label"> Created At </label>
                                <input class="form-control" type="text" value="{{ $job->created_at }}" disabled />
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label"> Expired At </label>
                                <input class="form-control daterange" name="expired_at" type="text"
                                    value="{{ $job->expired_at }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="experience"> Experience </label>
                                <select name="experience" id="experience"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Experience</option>
                                    <option value="Junior 0-2 years" @if($job->experience == 'Junior 0-2 years')
                                        selected @endif>Junior 0-2 years</option>
                                    <option value="Mid-level 2-5 years" @if($job->experience == 'Mid-level 2-5 years')
                                        selected @endif>Mid-level 2-5 years</option>
                                    <option value="Senior 5-10 years" @if($job->experience == 'Senior 5-10 years')
                                        selected @endif>Senior 5-10 years</option>
                                    <option value="Director 10+ years" @if($job->experience == 'Director 10+ years')
                                        selected @endif>Director 10+ years</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label"> Attached User </label>
                                <a href="{{ url('/users/' . $job->user_id . '/details') }}" target="_blank">
                                    <input class="form-control" value="View User Profile" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-12 col-lg-6">
        <h1>Applications</h1>
        <div id="applications-container"></div>
    </div>
</div>
@endsection