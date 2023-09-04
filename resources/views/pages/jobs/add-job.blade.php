@extends('layouts.app')

@section('title', 'Add New Job')

@section('scripts')
<script src="{{ asset('/assets/js/custom.js') }}"></script>
<script>
function fetchCategories() {
    var requestData = {
        per_page: -1
    };

    $.ajax({
        url: '/api/v1/categories',
        method: 'GET',
        data: requestData,
        dataType: 'json',
        success: function(response) {
            populateFilter(response.data, '#category');

        },
        error: function() {
            alert('Failed to fetch categories from the API.');
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
            populateFilter(response.data, '#industry');

        },
        error: function() {
            alert('Failed to fetch industries from the API.');
        }
    });
}

$(document).ready(function() {

    fetchIndustries();
    // fetchCategories();
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

<div class="row">

    <div class="col-12 col-lg-12">
        <form>
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
                                    <label class="form-label" for="salary_range"> Salary Range </label>
                                    <input id="salary_range" class="form-control" type="text" name="salary_range"
                                        placeholder="Enter Salary Range" />
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
                                        <option value="Freelance">Freelance</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Part-time">Part-time</option>
                                        <option value="Full-time">Full-time</option>
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
                                <input id="apply_type" class="form-control" type="text" name="apply_type" />
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
                                <label class="form-label"> Created At </label>
                                <input class="form-control" type="text" />
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label"> Expired At </label>
                                <input class="form-control" type="text" />
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
                                    <option value="Junior 0-2 years">Junior 0-2 years</option>
                                    <option value="Mid-level 2-5 years">Mid-level 2-5 years</option>
                                    <option value="Senior 5-10 years">Senior 5-10 years</option>
                                    <option value="Director 10+ years">Director 10+ years</option>
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