@extends('layouts.app')

@section('title', 'Profile')

@section('styles')

@endsection

@section('scripts')

<script>
$(document).ready(function() {

    $("#profile-form").on("submit", function(event) {
        event.preventDefault();

        var $errorContainer = $('#error-messages');
        $errorContainer.hide();

        var password = $("#password").val();
        var confirm_password = $("#confirm_password").val();
        if (password !== confirm_password) {
            var $errorList = $errorContainer.find('ul');
            $errorList.empty();
            $errorList.append('<li> Passwords do not match </li>');
            $errorContainer.show();
            return;
        }

        var formData = {
            first_name: $("#first_name").val(),
            last_name: $("#last_name").val(),
            email: $("#email").val(),
            username: $("#username").val(),
            status: $("#status").val(),
            role: $("#role").val(),
            _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: "/api/v1/users/" + "{{ $user->uuid }}",
            type: "PATCH",
            data: JSON.stringify(formData),

            contentType: "application/json",
            success: function(response) {
                // Handle success response
                console.log("API call success:", response);
                Swal.fire({
                    title: 'Success',
                    text: "User updated successfully",
                    icon: 'success'
                });

            },
            error: function(error) {
                if (error.status === 422) {
                    var errorMessages = error.responseJSON.errors;
                    var $errorContainer = $('#error-messages');
                    var $errorList = $errorContainer.find('ul');

                    $errorList.empty();

                    $.each(errorMessages, function(field, errors) {
                        $.each(errors, function(index, error) {
                            $errorList.append('<li>' + error + '</li>');
                        });
                    });

                    $errorContainer.show();
                } else {
                    console.error("API call error:", error.responseText);
                }

            }
        });
    });

    $("#password-form").on("submit", function(event) {
        event.preventDefault();

        var $errorContainer = $('#error-messages');
        $errorContainer.hide();

        var password = $("#password").val();
        var confirm_password = $("#confirm_password").val();
        if (password !== confirm_password) {
            var $errorList = $errorContainer.find('ul');
            $errorList.empty();
            $errorList.append('<li> Passwords do not match </li>');
            $errorContainer.show();
            return;
        }

        var formData = {
            password: $("#password").val(),
            user_id: "{{ $user->id }}",
            _token: "{{ csrf_token() }}"
        };


        $.ajax({
            url: "{{ route('user.password.update')}}",
            type: "PUT",
            data: JSON.stringify(formData),

            contentType: "application/json",
            success: function(response) {
                console.log("API call success:", response);
                Swal.fire({
                    title: 'Success',
                    text: "Password updated successfully",
                    icon: 'success'
                });

            },
            error: function(error) {
                if (error.status === 422) {
                    var errorMessages = error.responseJSON.errors;
                    var $errorContainer = $('#error-messages');
                    var $errorList = $errorContainer.find('ul');

                    $errorList.empty();

                    $.each(errorMessages, function(field, errors) {
                        $.each(errors, function(index, error) {
                            $errorList.append('<li>' + error + '</li>');
                        });
                    });

                    $errorContainer.show();
                } else {
                    console.error("API call error:", error.responseText);
                }

            }
        });
    });
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

<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Personal info</h5>
            </div>
            <div class="card-body">
                <form id="profile-form">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" placeholder="First Name"
                                    value="{{ $user->first_name }}">
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" placeholder="Last Name"
                                    value="{{ $user->last_name }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" placeholder="Username"
                                    value="{{ $user->username }}">
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" placeholder="Email"
                                    value="{{ $user->email }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="status"> Status </label>
                                    <select name="status" id="status"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="-100"> Select Status</option>
                                        <option value="pending" @if($user->status == 'pending') selected @endif>
                                            Pending</option>
                                        <option value="active" @if($user->status == 'active') selected @endif>
                                            Active
                                        </option>
                                        <option value="inactive" @if($user->status == 'inactive') selected
                                            @endif>
                                            Inactive</option>

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="role"> Role </label>
                                    <select name="role" id="role" class="form-control form-select custom-select select2"
                                        data-toggle="select2">
                                        <option value="-100"> Select Role</option>
                                        <option value="advisor" @if($user->role == 'advisor') selected @endif>
                                            Advisor</option>
                                        <option value="agency" @if($user->role == 'agency') selected @endif>
                                            Agency
                                        </option>
                                        <option value="creative" @if($user->role == 'creative') selected @endif>
                                            Creative</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>

        </div>
    </div>
</div>

@include('pages.users._inc.password')

@endsection
