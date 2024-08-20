@extends('layouts.app')

@section('title', __('Add User'))
@section('scripts')
    <script>
        function selectRoleDropdown() {
            var currentUrl = window.location.href;
            var valueToMatch = "";

            if (currentUrl.includes("advisor/create")) {
                valueToMatch = "advisor";
            } else if (currentUrl.includes("agency/create")) {
                valueToMatch = "agency";
            } else if (currentUrl.includes("creative/create")) {
                valueToMatch = "creative";
            } else if (currentUrl.includes("recruiter/create")) {
                valueToMatch = "recruiter";
            }
            $("#role").val(valueToMatch);
            $('#role').trigger('change');
        }

        $(document).ready(function() {

            selectRoleDropdown();
            const submitButton = document.getElementById('submitButton');
            $("#add-user-form").on("submit", function(event) {

                submitButton.disabled = true; // Disable the submit button
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
                    agency_name: $("#agency_name").val(),
                    linkedin_profile: $("#linkedin_profile").val(),
                    first_name: $("#first_name").val(),
                    last_name: $("#last_name").val(),
                    email: $("#email").val(),
                    password: $("#password").val(),
                    username: $("#username").val(),
                    status: $("#status").val(),
                    role: $("#role").val(),
                    _token: "{{ csrf_token() }}"
                };
                console.log(formData);
                $.ajax({
                    url: "{{ route('users.store') }}",
                    type: "POST",
                    data: JSON.stringify(formData),

                    contentType: "application/json",
                    success: function(response) {
                        console.log("API call success:", response);
                        Swal.fire({
                            title: 'Success',
                            text: response.data.role.toUpperCase() +
                                " created successfully",
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

                    },
                    complete: function() {
                        submitButton.disabled = false;
                    }
                });
            });



        });
    </script>
@endsection
@section('content')


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
                    <h5 class="card-title mb-0">Personal Info</h5>
                </div>
                <div class="card-body">
                    <form id="add-user-form">

                        <div class="row">
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label for="agency_name" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="agency_name" placeholder="Company Name">
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="linkedin_profile" class="form-label">Company LinkedIn</label>
                                    <input type="text" class="form-control" id="linkedin_profile"
                                        placeholder="Company LinkedIn">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label for="first_name" class="form-label">Contact First Name</label>
                                    <input type="text" class="form-control" id="first_name" placeholder="First Name">
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Contact Last Name</label>
                                    <input type="text" class="form-control" id="last_name" placeholder="Last Name">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" placeholder="Username">
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="email" placeholder="Email">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" placeholder="Password">
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password"
                                        placeholder="Confirm Password">
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

                                            <option value="pending">
                                                Pending</option>
                                            <option value="active">
                                                Active
                                            </option>
                                            <option value="inactive">
                                                Inactive</option>

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="role"> Role </label>
                                        <select name="role" id="role"
                                            class="form-control form-select custom-select select2" data-toggle="select2">
                                            <option value="agency">
                                                Agency
                                            </option>
                                            <option value="advisor">
                                                Advisor</option>
                                            <option value="recruiter">
                                                Recruiter</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="submitButton">Add New User</button>
                    </form>
                </div>

            </div>
        </div>
    </div>


@endsection
