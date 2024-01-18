@extends('layouts.app')

@section('title', 'Job Request Details')

@section('scripts')
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
    <script>
        function fetchIndustries() {

            $.ajax({
                url: '/api/v1/industry-experiences',
                method: 'GET',
                dataType: 'json',
                success: function(response) {

                    populateFilter(response.data, '#industry');
                    var industry_experience = "{{ $package_request->industry_experience }}";
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
                url: '/api/v1/media-experiences',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateFilter(response.data, '#media');
                    var media_experience = "{{ $package_request->media_experience }}";
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
        function fetchCategories() {

            $.ajax({
                url: '/api/v1/get_categories',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateFilter(response.data, '#category');
                    var job_category = "{{ $package_request->category->uuid }}";
                    $('#category').val(job_category);
                    $('#category').trigger('change');
                },
                error: function() {
                    alert('Failed to fetch categories from the API.');
                }
            });
        }
        $(document).ready(function() {
            var job_state = "{{ $package_request->state->uuid }}";
            fetchStates(job_state);

            fetchCategories();
            fetchIndustries();
            fetchMedias();

            $('#save-job').click(function(event) {
                event.preventDefault();

                var formData = $('form').serialize();

                $.ajax({
                    type: 'PUT',
                    url: '/api/v1/package_request/' + "{{ $package_request->uuid }}" + '/admin',
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

            $('#state').on('change', function() {
                var selectedStateId = $(this).val();
                var city_id = "{{ $package_request->city?->uuid }}";
                getCitiesByState(selectedStateId, city_id);
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
    <h1 class="h3 mb-3">Job Request Details</h1>

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
        <div class="col-12 col-lg-12">
            <form action="{{ route('job-requests.update', $package_request->uuid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="agency_name"> Title </label>
                                        <input class="form-control" type="text" name="title"
                                            placeholder="Enter title of the job"
                                            value="{{ $package_request->category?->name }}" disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="agency_name"> Agency Name</label>
                                        <input id="agency_name" class="form-control" type="text" name="agency_name"
                                            placeholder="Agency Name" value="{{ $package_request->user->agency?->name }}"
                                            disabled />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="agency_name"> Contact Name </label>
                                        <input class="form-control" type="text" name="contact_name"
                                            placeholder="Enter Contact Name"
                                            value="{{ $package_request->user?->first_name . ' ' . $package_request->user?->last_name }}"
                                            disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="agency_name"> Email </label>
                                        <input id="agency_name" class="form-control" type="text" name="email"
                                            placeholder="Email" value="{{ $package_request->user?->email }}" disabled />
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="agency_name"> Phone Number </label>
                                        <input class="form-control" type="text" name="phone_number"
                                            placeholder="Enter Phone Number" value="{{ $package_request->user?->phones()->where('label', 'business')->first()?->phone_number, }}"
                                            disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="agency_name"> Start Date </label>
                                        <input id="agency_name" class="form-control" type="text" name="start_date"
                                            placeholder="Agency Name" value="{{ $package_request->start_date }}"
                                            disabled />
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
                                        disabled>
                                        <option value="-100"> Select State</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">

                                <div class="form-group">
                                    <label class="form-label" for="city"> City </label>
                                    <select name="city" id="city"
                                        class="form-control form-select custom-select select2" data-toggle="select2"
                                        disabled>
                                        <option value="-100"> Select City</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="category"> Comment </label>
                                        <input id="comment" class="form-control" type="text" name="comment"
                                            placeholder="Comment" value="{{ $package_request->comment }}"
                                            disabled />
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label class="form-label"> Created At </label>
                                    <input class="form-control" type="text"
                                        value="{{ $package_request->created_at }}" disabled />
                                </div>
                            </div>
                        </div>

                         <div class="row">
                            <div class="col-6 col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="industry"> Industry Experience </label>
                                    <select name="industry_experience[]" id="industry" required
                                        class="form-control form-select custom-select select2" multiple="multiple"
                                        data-toggle="select2" disabled>
                                        <option value="-100"> Select Industry</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="media"> Media Experience </label>
                                    <select name="media_experience[]" id="media" required
                                        class="form-control form-select custom-select select2" multiple="multiple"
                                        data-toggle="select2" disabled>
                                        <option value="-100"> Select Media</option>
                                    </select>
                                </div>
                            </div>

                        </div>


                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="status"> Status </label>
                                        <select name="status" id="status"
                                            class="form-control form-select custom-select select2" data-toggle="select2">
                                            <option value="pending" @if ($package_request->status == 'pending') selected @endif>
                                                Pending
                                            </option>
                                            <option value="approved" @if ($package_request->status == 'approved') selected @endif>
                                                Approved
                                            </option>
                                            <option value="rejected" @if ($package_request->status == 'rejected') selected @endif>
                                                Rejected
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label class="form-label"> Package/Plan </label>
                                    <select name="plan_id" id="plan_id"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="-1">Select Plan</option>
                                        @foreach (\App\Models\Plan::all() as $plan)
                                            <option value="{{ $plan->id }}"
                                                @if ($plan->id == $package_request->plan_id) selected @endif>
                                                {{ $plan->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}



                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="assigned_to"> Assign To </label>
                                        <select name="assigned_to" id="assigned_to"
                                            class="form-control form-select custom-select select2" data-toggle="select2">
                                            <option value="-1">Select Advisor</option>
                                            @foreach (\App\Models\User::where('role', 2)->get() as $user)
                                                <option value="{{ $user->id }}"
                                                    @if ($user->id == $package_request->assigned_to) selected @endif>
                                                    {{ $user->first_name . ' ' . $user->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label class="form-label"> Attached User </label>
                                    <a href="{{ url('/users/' . $package_request->user_id . '/details') }}"
                                        target="_blank">
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

    </div>


@endsection
