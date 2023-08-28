@extends('layouts.app')

@section('title', 'Group Details')

@section('scripts')

<script>
function populateUserFilter(users, div_id) {
    var selectElement = $(div_id);
    $.each(users, function(index, user) {
        var option = $('<option>', {
            value: user.id,
            text: user.first_name + ' ' + user.last_name + ' - ' + user.role
        });

        selectElement.append(option);
    });
}

function fetchUsers() {
    $.ajax({
        url: '/api/v1/get_users',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            populateUserFilter(response, '#user');
        },
        error: function() {
            alert('Failed to fetch users from the API.');
        }
    });
}

$(document).ready(function() {


    fetchUsers();

    $("#add_members").on("submit", function(event) {

        submitButton.disabled = true; // Disable the submit button
        event.preventDefault();

        var formData = new FormData(this);
        $.ajax({
            url: '{{ route("groups.new-member") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    text: "Member added successfully",
                    icon: 'success'
                }).then((result) => {
                    location.reload();
                });

            },
            error: function(xhr, status, error) {
                console.log(error);
                Swal.fire({
                    title: 'Error',
                    text: "Something went wrong",
                    icon: 'error'
                });
            }
        });
    });


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
                                    src="{{ isset($group->attachment) ? asset('storage/' . $group->attachment->path) : asset('images/default.png') }}"
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
@include('pages.groups._inc.add_members')
@include('pages.groups._inc.members')
@endsection