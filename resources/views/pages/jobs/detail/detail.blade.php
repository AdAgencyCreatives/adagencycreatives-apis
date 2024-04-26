@extends('layouts.app')

@section('title', 'Job Details')

@section('scripts')
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
    <script src="https://cdn.tiny.cloud/1/0de1wvfzr5x0z7za5hi7txxvlhepurk5812ub5p0fu5tnywh/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
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

                    if (application.status === 'pending') {
                        cardHeader.append(
                            '<div style="float:right"><a href="javascript:void(0);" class="btn btn-default mt-n1 mark-application" data-status="accepted" data-id="' +
                            application.id + '" title="Interested"><i class="fas fa-check"></i></a></div>');
                        cardHeader.append(
                            '<div style="float:right"><a href="javascript:void(0);" class="btn btn-default mt-n1 mark-application" data-status="rejected" data-id="' +
                            application.id + '" title="Not Aligned"><i class="fas fa-times"></i></a></div>');
                    } else {
                        cardHeader.append(
                            '<div style="float:right"><a href="javascript:void(0);" class="btn btn-default mt-n1 mark-application" data-status="pending" data-id="' +
                            application.id + '" title="Undo"><i class="fas fa-undo"></i></a></div>');
                    }

                    cardHeader.append('<div style="float:right"><a href="' + application.resume_url +
                        '" class="btn btn-primary mt-n1 mr-2">Download Resume <i class="fas fa-download"></i></a></div>'
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
                    let applications = response.data;
                    // const rejected = applications.filter((app) => {
                    //     return app.status === 'rejected';
                    // });
                    // let others = applications.filter((app) => {
                    //     return app.status !== 'rejected';
                    // });

                    // applications = others.concat(rejected);

                    populateApplications(applications);
                },
                error: function() {
                    alert('Failed to fetch applications from the API.');
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
                    var job_category = "{{ $job->category?->uuid }}";
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
                    job = response.data;
                },
                error: function() {
                    alert('Failed to fetch job from the API.');
                }
            });
        }

        function fetchIndustries() {

            $.ajax({
                url: '/api/v1/get_industry-experiences',
                method: 'GET',
                dataType: 'json',
                success: function(response) {

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

        function fetchMedias() {
            $.ajax({
                url: '/api/v1/get_media-experiences',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateFilter(response.data, '#media');
                    var media_experience = "{{ $job->media_experience }}";
                    var mediaArray = media_experience.split(',');
                    mediaArray.forEach(function(uuid) {
                        $('#media option[value="' + uuid + '"]').prop('selected', true);
                    });
                    $('#media').trigger('change');

                },
                error: function() {
                    alert('Failed to fetch medias from the API.');
                }
            });
        }

        function fetchStrengthsForJobs() {
            $.ajax({
                url: '/api/v1/get_strengths',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateFilter(response.data, '#strengths');
                    var strengths = "{{ $job->strengths }}";
                    var strengthArray = strengths.split(',');
                    strengthArray.forEach(function(uuid) {
                        $('#strengths option[value="' + uuid + '"]').prop('selected', true);
                    });
                    $('#strengths').trigger('change');

                },
                error: function() {
                    alert('Failed to fetch strength from the API.');
                }
            });
        }

        function fetchYearsOfExperience(user_experience) {
            $.ajax({
                url: '/api/v1/years-of-experience',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    var selectElement = $('#years_of_experience');
                    $.each(response.data, function(index, experience) {
                        var option = $('<option>', {
                            value: experience.name,
                            text: experience.name
                        });

                        selectElement.append(option);
                    });

                    $('#years_of_experience').val(user_experience);
                    $('#years_of_experience').trigger('change');

                },
                error: function() {
                    alert('Failed to fetch years-of-experiences from the API.');
                }
            });
        }

        var employmentTypeString = "{{ $job?->employment_type ?? '' }}";
        var userEmploymentTypes = employmentTypeString ? @json(explode(',', $job?->employment_type)) : [];

        $.ajax({
            url: '/api/v1/employment_types',
            type: "GET",
            success: function(data) {
                // Clear existing options
                $("#employment_type").empty();

                // Add the default option
                $("#employment_type").append('<option value="-100"> Select Type</option>');

                // Populate the dropdown with options from the API
                $.each(data, function(index, type) {
                    var isSelected = userEmploymentTypes.includes(type);
                    $("#employment_type").append('<option value="' + type + '" ' + (isSelected ?
                        'selected' : '') + '>' + type + '</option>');
                });

                // Refresh the Select2 plugin
                $("#employment_type").select2("destroy").select2();
            },
            error: function(error) {
                console.error("Error fetching employment types:", error);
            }
        });

        $(document).ready(function() {


            fetchJobObject();
            fetchApplications();
            fetchCategories();
            fetchIndustries();
            fetchMedias();
            fetchStrengthsForJobs();


            var job_state = "{{ $job->state?->uuid }}";
            fetchStates(job_state);

            var years_of_experience = "{{ $job->years_of_experience }}";
            fetchYearsOfExperience(years_of_experience);

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
                                errorMessage += field + ': ' + messages.join(', ') +
                                    '\n';
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


            $('#state').on('change', function() {
                var selectedStateId = $(this).val();
                var city_id = "{{ $job->city?->uuid }}";
                getCitiesByState(selectedStateId, city_id);
            });

            const application_container = $('#applications-container');
            application_container.on('click', '.mark-application', async function() {
                const e = $(this);
                const id = $(this).data('id');
                const status = $(this).data('status');
                const data = {
                    status: status
                };
                $(this).html(
                    '<div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div>'
                    );
                let icon = '';
                if (status === 'accepted') {
                    icon = '<i class="fas fa-check"></i>';
                } else if (status === 'rejected') {
                    icon = '<i class="fas fa-times"></i>';
                } else {
                    icon = '<i class="fas fa-undo"></i>';
                }
                updateApplication(id, data, icon, e);
            });

            tinymce.init({
                selector: 'textarea',
                menubar: false,
                plugins: 'anchor autolink codesample emoticons link lists visualblocks',
                toolbar: 'bold italic underline strikethrough | blocks fontfamily fontsize  | link media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            });

        });

        async function updateApplication(id, data, icon, e) {
            $.ajax({
                url: '/api/v1/applications/' + id,
                method: 'PATCH',
                data: JSON.stringify(data),
                dataType: 'json',
                contentType: 'application/json',
                success: function(response) {
                    $('#applications-container').html('');
                    fetchApplications();
                },
                error: function() {
                    e.html(icon);
                    alert('Something went wrong!');
                },

            });
        }
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

    @if (session('success'))
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
                        <textarea class="form-control" name="description" rows="10" placeholder="Textarea" spellcheck="false">{{ $job->description }}</textarea>
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
                                    @if (isset($agency_logo))
                                        <img class="rounded-circle img-responsive mt-2 lazy"
                                            src="{{ getAttachmentBasePath() . $agency_logo->path }}"
                                            alt="{{ $agency_logo->resource_type }}" width="100" height="100"
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
                                            <option value="draft" @if ($job->status == 'draft') selected @endif>
                                                Draft
                                            </option>
                                            <option value="pending" @if ($job->status == 'pending') selected @endif>
                                                Pending
                                            </option>
                                            <option value="approved" @if ($job->status == 'approved') selected @endif>
                                                Approved
                                            </option>
                                            <option value="rejected" @if ($job->status == 'rejected') selected @endif>
                                                Rejected
                                            </option>
                                            <option value="expired" @if ($job->status == 'expired') selected @endif>
                                                Expired
                                            </option>
                                            <option value="filled" @if ($job->status == 'filled') selected @endif>Filled
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
                                <div class="form-group">
                                    <label class="form-label" for="workplace_experience">Workplace Preference</label>
                                    <select class="form-control select2" multiple="multiple" name="workplace_experience[]">
                                        <option value="is_remote" @if ($job->is_remote) selected @endif>Remote
                                        </option>
                                        <option value="is_hybrid" @if ($job->is_hybrid) selected @endif>Hybrid
                                        </option>
                                        <option value="is_onsite" @if ($job->is_onsite) selected @endif>Onsite
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="employment_type"> Employment Type </label>
                                        <select name="employment_type" id="employment_type"
                                            class="form-control form-select custom-select select2" data-toggle="select2">
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
                                    <label class="form-label" for="media"> Media Experience </label>
                                    <select name="media_experience[]" id="media" required
                                        class="form-control form-select custom-select select2" multiple="multiple"
                                        data-toggle="select2">
                                        <option value="-100"> Select Media</option>

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <div class="form-group">
                                    <label class="form-label" for="strengths">Character Strengths (Select up to 5)</label>
                                    <select name="strengths[]" id="strengths"
                                        class="form-control form-select custom-select select2" multiple="multiple"
                                        data-toggle="select2">
                                        <option value="-100"> Select Strengths </option>
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
                                        <option value="Internal" @if ($job->apply_type == 'Internal') selected @endif>
                                            Internal
                                        </option>
                                        <option value="External" @if ($job->apply_type == 'External') selected @endif>
                                            External
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="external_link"> External Link </label>
                                    <a>
                                        <input id="external_link" class="form-control" type="text"
                                            name="external_link" value="{{ $job->external_link }}" />
                                    </a>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label class="form-label"> Created At </label>
                                    <input class="form-control daterange" type="text" name="created_at"
                                        value="{{ $job->created_at }}" />
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
                                    <label class="form-label" for="years_of_experience"> Years of experience </label>
                                    <select name="years_of_experience" id="years_of_experience"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="-100"> Select Experience</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="salary_range"> Salary Range </label>
                                        <input id="salary_range" class="form-control" type="text" name="salary_range"
                                            placeholder="Enter Salary Range" value="{{ $job->salary_range }}" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="state"> State </label>
                                    <select name="state" id="state"
                                        class="form-control form-select custom-select select2" data-toggle="select2"
                                        required>
                                        <option value="-100"> Select State</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">

                                <div class="form-group">
                                    <label class="form-label" for="city"> City </label>
                                    <select name="city" id="city"
                                        class="form-control form-select custom-select select2" data-toggle="select2"
                                        required>
                                        <option value="-100"> Select City</option>
                                    </select>
                                </div>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="is_featured"> Featured? </label>
                                    <select name="is_featured" id="is_featured"
                                        class="form-control form-select custom-select select2" data-toggle="select2"
                                        @if (auth()->user()->role == 'advisor') disabled @endif>
                                        <option value="1" @if ($job->is_featured == 1) selected @endif> Yes
                                        </option>
                                        <option value="0" @if ($job->is_featured == 0) selected @endif> No
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="is_urgent"> Urgent?</label>
                                    <select name="is_urgent" id="is_urgent"
                                        class="form-control form-select custom-select select2" data-toggle="select2"
                                        @if (auth()->user()->role == 'advisor') disabled @endif>
                                        <option value="1" @if ($job->is_urgent == 1) selected @endif> Yes
                                        </option>
                                        <option value="0" @if ($job->is_urgent == 0) selected @endif> No
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="is_opentoremote"> Open to Remote? </label>
                                    <select name="is_opentoremote" id="is_opentoremote"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="1" @if ($job->is_opentoremote == 1) selected @endif> Yes
                                        </option>
                                        <option value="0" @if ($job->is_opentoremote == 0) selected @endif> No
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="is_opentorelocation"> Open to Relocation? </label>
                                    <select name="is_opentorelocation" id="is_opentorelocation"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="1" @if ($job->is_opentorelocation == 1) selected @endif>
                                            Yes
                                        </option>
                                        <option value="0" @if ($job->is_opentorelocation == 0) selected @endif> No
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
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

    @include('pages.jobs._inc.seo')
@endsection
