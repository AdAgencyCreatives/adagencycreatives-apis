@extends('layouts.app')

@section('title')
    {{ ucfirst($user->role) }}
@endsection


@section('styles')
@endsection

@section('scripts')
    <script src="{{ asset('/assets/js/custom.js') }}"></script>
    @include('pages.users.creative.scripts')

    <script src="https://cdn.tiny.cloud/1/0de1wvfzr5x0z7za5hi7txxvlhepurk5812ub5p0fu5tnywh/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>

    <script>
        var expired_at = "{{ $user->latest_subscription?->ends_at }}";

        if (expired_at) {
            $(".daterange").daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate: expired_at,
                locale: {
                    format: "Y-MM-DD"
                }
            });
        } else {

            $(".daterange").daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: "Y-MM-DD"
                }
            });
        }

        $("#subscriptionDuration").on("change", function() {

            var datepicker = $(".daterange").daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false, // Disable auto-update to allow manual updates
                locale: {
                    format: "Y-MM-DD"
                }
            });

            var selectedOption = $(this).find(":selected");
            var days = selectedOption.data("days");
            var quota = selectedOption.data("quota");

            $("input[name='quota_left']").val(quota);

            // Use a library like moment.js to manipulate dates
            var newDate = moment().add(days, 'days').format("YYYY-MM-DD");

            // Update the datepicker
            datepicker.data('daterangepicker').setStartDate(newDate);
            datepicker.val(newDate);
        });


        var employmentTypeString = "{{ $user->creative?->employment_type ?? '' }}";
        var userEmploymentTypes = employmentTypeString ? employmentTypeString.split(',') : [];

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

        tinymce.init({
            selector: 'textarea',
            menubar: false,
            plugins: 'anchor autolink codesample emoticons link lists visualblocks',
            toolbar: 'bold italic underline strikethrough | blocks fontfamily fontsize  | link media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',

        });
    </script>
@endsection

@section('content')
    <h1 class="h3 mb-3">Profile</h1>

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
        <div class="col-md-3 col-xl-2">

            <div class="card">

                <div class="list-group list-group-flush" role="tablist">

                    <a class="list-group-item list-group-item-action" data-toggle="list" href="#personal_info"
                        role="tab" aria-selected="true">
                        Personal Info
                    </a>

                    @if (in_array($user->role, ['agency', 'advisor', 'recruiter']))
                        <a class="list-group-item list-group-item-action active" data-toggle="list" href="#agency_info"
                            role="tab" aria-selected="true">
                            Agency Info
                        </a>
                    @elseif($user->role == 'creative')
                        <a class="list-group-item list-group-item-action active" data-toggle="list" href="#creative_info"
                            role="tab" aria-selected="true">
                            Creative Info
                        </a>
                    @endif

                    @if ($user->role == 'creative')
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#qualifications"
                            role="tab" aria-selected="true">
                            Qualifications
                        </a>
                        </a> <a class="list-group-item list-group-item-action" data-toggle="list" href="#portfolio"
                            role="tab" aria-selected="true">
                            Portfolio
                        </a>

                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#experiences"
                            role="tab" aria-selected="true">
                            Experience
                        </a>

                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#educations"
                            role="tab" aria-selected="true">
                            Education
                        </a>

                        </a> <a class="list-group-item list-group-item-action" data-toggle="list" href="#spotlight"
                            role="tab" aria-selected="true">
                            Spotlight
                        </a>
                    @endif



                    @if (in_array($user->role, ['agency', 'advisor', 'recruiter']))
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#package" role="tab"
                            aria-selected="false" tabindex="-1">
                            Package
                        </a>
                    @endif


                    @if ($user->role != 'admin')
                        @if ($user->role != 'recruiter')
                            <a class="list-group-item list-group-item-action" data-toggle="list" href="#seo"
                                role="tab" aria-selected="false" tabindex="-1">
                                SEO
                            </a>
                        @endif
                    @endif
                    <a class="list-group-item list-group-item-action" data-toggle="list" href="#password" role="tab"
                        aria-selected="false" tabindex="-1">
                        Password
                    </a>

                    @if ($user->role != 'admin')
                        @php
                            $frontend_url = $user->role == 'creative' ? env('FRONTEND_URL') . '/creative/' . ($user->creative ? $user->creative->slug : '') : env('FRONTEND_URL') . '/agency/' . ($user->agency ? $user->agency->slug : '');
                        @endphp

                        <a class="list-group-item list-group-item-action" href="{{ $frontend_url }}" target="_blank">
                            Frontend URL
                        </a>

                        <a class="list-group-item list-group-item-action" href="{{ route('impersonate', $user->id) }}">
                            Impersonate
                        </a>

                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#permanently_delete"
                            role="tab" aria-selected="false" tabindex="-1">
                            Permanently Delete
                        </a>
                    @endif

                </div>
            </div>
        </div>

        <div class="col-md-9 col-xl-10">
            <div class="tab-content">
                @if (in_array($user->role, ['agency', 'advisor', 'recruiter']))
                    <div class="tab-pane fade show active" id="agency_info" role="tabpanel">
                        @include('pages.users.agency.agency')
                    </div>

                    <div class="tab-pane fade" id="seo" role="tabpanel">
                        @include('pages.users.agency.seo')
                    </div>

                    <div class="tab-pane fade" id="package" role="tabpanel">
                        @include('pages.users.agency.package')
                    </div>
                @elseif($user->role == 'creative')
                    <div class="tab-pane fade show active" id="creative_info" role="tabpanel">
                        @include('pages.users.creative.creative')
                    </div>
                @endif
                <div class="tab-pane fade @if ($user->role == 'admin') show active @endif " id="personal_info"
                    role="tabpanel">
                    @include('pages.users._inc.personal_info')
                </div>
                <div class="tab-pane fade" id="password" role="tabpanel">
                    @include('pages.users._inc.password')
                </div>

                <div class="tab-pane fade" id="permanently_delete" role="tabpanel">
                    @include('pages.users._inc.permanently_delete')
                </div>
                @if ($user->role == 'creative')
                    <div class="tab-pane fade" id="experiences" role="tabpanel">
                        @include('pages.users.creative.experience')
                    </div>

                    <div class="tab-pane fade" id="educations" role="tabpanel">
                        @include('pages.users.creative.education')
                    </div>
                    <div class="tab-pane fade" id="qualifications" role="tabpanel">
                        @include('pages.users.creative.qualification')
                    </div>
                    <div class="tab-pane fade" id="spotlight" role="tabpanel">
                        @include('pages.users.creative.spotlight')
                    </div>
                    <div class="tab-pane fade" id="portfolio" role="tabpanel">
                        @include('pages.users.creative.portfolio')
                    </div>
                    <div class="tab-pane fade" id="seo" role="tabpanel">
                        @include('pages.users.creative.seo')
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
