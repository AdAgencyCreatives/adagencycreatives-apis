@extends('layouts.app')

@section('title', 'Group Details')

@section('scripts')

<script>
function fetchApplications() {
    var filters = {
        'job_id': "{{ $group->uuid }}",
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

function createAttachment() {
    const formData = new FormData();
    const imageInput = document.querySelector('input[type="file"]');

    var form = new FormData();
    formData.append('resource_type', 'cover_image');
    formData.append('user_id', 'fc29773c-d0e6-3493-a4ca-4d2e2b2c3df1');
    formData.append('file', imageInput.files[0]);

    var settings = {
        "url": "/api/v1/attachments",
        "method": "POST",
        "timeout": 0,
        "headers": {
            "X-CSRF-Token": "{{ csrf_token() }}",
            "Accept": "application/json",
            "Authorization": "Bearer 1|z8axd4FBsikytrLF0Zedsb3sKEM9buGXm7GISQcr",
        },
        "processData": false,
        "mimeType": "multipart/form-data",
        "contentType": false,
        "data": formData
    };

    $.ajax(settings).done(function(response) {
        console.log(response);
    });
}
$(document).ready(function() {





});
</script>
@endsection

@section('content')
<h1 class="h3 mb-3">Group Details</h1>

<div id="error-messages" class="alert alert-danger alert-dismissible" style="display: none;" role="alert">
    <div class="alert-message">
        <strong>Error!</strong> Please fix the following issues:
        <ul></ul>
    </div>
</div>

<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-body">
                <form id="group-detail-form" action="{{ route('groups.update', $group->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-8">

                            <div class="mb-3">
                                <label for="name" class="form-label"> Name</label>
                                <input type="text" class="form-control" value="{{ $group->name }}" name="name"
                                    placeholder="Group Name">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" spellcheck="true"
                                    style="height: 120px;">{{ $group->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="status"> Status </label>
                                    <select name="status" id="status"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="public" @if($group->status == 'public') selected
                                            @endif>Public</option>

                                        <option value="private" @if($group->status == 'private') selected
                                            @endif>Private</option>

                                        <option value="hidden" @if($group->status == 'hidden') selected
                                            @endif>Hidden</option>

                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="mb-3 error-placeholder">
                                    <label class="form-label">Cover Image</label>
                                    <div>
                                        <input type="file" class="validation-file" name="cover_image">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-center">
                                <h4>Cover Image</h4>
                                <img class="rounded-circle img-responsive mt-2 lazy"
                                    src="{{ asset('storage/'.$group->attachment->path) }}"
                                    alt="{{ $group->cover_image }}" width="300" height="300" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitButton">Save</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection