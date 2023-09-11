@extends('layouts.app')

@section('title', 'Add New Job')

@section('scripts')
<script src="{{ asset('/assets/js/custom.js') }}"></script>
<script>
$(document).ready(function() {

    $(".daterange").daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        startDate: moment(),
        locale: {
            format: "Y-MM-DD"
        }
    });
    fetchStates();
    fetchYearsOfExperience();
    fetchIndustries();
    fetchMedias();
    fetchCategories();


    $('#state').on('change', function() {
        var selectedStateId = $(this).val();
        console.log(selectedStateId);
        getCitiesByState(selectedStateId);
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
<h1 class="h3 mb-3">Add New Job</h1>

<div id="error-messages" class="alert alert-danger alert-dismissible" style="display: none;" role="alert">
    <div class="alert-message">
        <strong>Error!</strong> Please fix the following issues:
        <ul></ul>
    </div>
</div>

@if(session('success'))
<x-created-alert type="success"></x-created-alert>
@endif

<div class="row">

    <div class="col-12 col-lg-12">
        <form action="{{route('jobs.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-body">
                    <h1>
                        <input class="form-control" type="text" name="title" placeholder="Enter title of the job" />

                    </h1>
                    <div id=job_options></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Description</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" name="description" rows="10" placeholder="Job description goes here"
                        spellcheck="false"></textarea>
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
                                        placeholder="Agency Name" />
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <div class="mb-3 error-placeholder">
                                    <label class="form-label">Agency Logo (Optional)</label>
                                    <div>
                                        <input type="file" class="validation-file" name="file">
                                    </div>
                                </div>
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
                                        <option value="published">Published
                                        </option>
                                        <option value="approved">Approved
                                        </option>
                                        <option value="rejected">Rejected
                                        </option>
                                        <option value="expired">Expired
                                        </option>
                                        <option value="filled">Filled
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
                                    <label class="form-label" for="labels"> Workplace preference </label>
                                    <select class="form-control select2" id="labels" multiple="multiple"
                                        name="labels[]">
                                        <option value="is_remote">Remote</option>
                                        <option value="is_hybrid">Hybrid</option>
                                        <option value="is_onsite">Onsite</option>
                                        <option value="is_featured">Featured</option>
                                        <option value="is_urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="employement_type"> Employment Type </label>
                                    <select name="employement_type" id="employement_type"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="-100"> Select Type</option>
                                        @foreach(\App\Models\Job::EMPLOYMENT_TYPE as $type)
                                        <option value="{{$type}}">{{$type}}</option>
                                        @endforeach

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
                                    <option value="-100"> Select Industry Experience</option>
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
                                    <option value="-100"> Select Media Experience</option>

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
                                    <option value="Internal">Internal
                                    </option>
                                    <option value="External">External
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="external_link"> Expernal Link </label>
                                <input id="external_link" class="form-control" type="url" name="external_link" />
                            </div>
                        </div>
                    </div>


                    <div class="row">

                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label"> Expired At </label>
                                <input class="form-control daterange" type="text" name="expired_at" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="years_of_experience"> Years of Experience </label>
                                <select name="years_of_experience" id="years_of_experience"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Experience</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">

                            <div class="form-group">
                                <label class="form-label" for="salary_range"> Salary Range </label>
                                <input id="salary_range" class="form-control" type="text" name="salary_range"
                                    placeholder="Enter Salary Range" />
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="state"> State </label>
                                <select name="state" id="state" class="form-control form-select custom-select select2"
                                    data-toggle="select2">
                                    <option value="-100"> Select State</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">

                            <div class="form-group">
                                <label class="form-label" for="city"> City </label>
                                <select name="city" id="city" class="form-control form-select custom-select select2"
                                    data-toggle="select2">
                                    <option value="-100"> Select City</option>
                                </select>
                            </div>

                        </div>

                    </div>

                    <button id="save-job" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection