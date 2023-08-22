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

    $("#agency-form").on("submit", function(event) {
        event.preventDefault();

        var formData = {
            name: $("input[name='name']").val(),
            size: $("input[name='size']").val(),
            type_of_work: $("select[name='type_of_work']").val(),
            about: $("textarea[name='about']").val(),
            _token: "{{ csrf_token() }}"
        };
        console.log(formData);
        $.ajax({
            url: "/api/v1/agencies/" + "{{ $user->agency?->uuid }}",
            type: "PATCH",
            data: JSON.stringify(formData),

            contentType: "application/json",
            success: function(response) {
                console.log("API call success:", response);
                Swal.fire({
                    title: 'Success',
                    text: "Agency info updated successfully",
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

    $("#creative-form").on("submit", function(event) {
        event.preventDefault();

        var formData = {
            years_of_experience: $("input[name='years_of_experience']").val(),
            type_of_work: $("select[name='type_of_work']").val(),
            _token: "{{ csrf_token() }}"
        };
        console.log(formData);
        $.ajax({
            url: "/api/v1/creatives/" + "{{ $user->creative?->uuid }}",
            type: "PATCH",
            data: JSON.stringify(formData),

            contentType: "application/json",
            success: function(response) {
                console.log("API call success:", response);
                Swal.fire({
                    title: 'Success',
                    text: "Agency info updated successfully",
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


@if($user->role == 'agency')
@include('pages.users._inc.agency')
@elseif($user->role == 'creative')
@include('pages.users._inc.creative')
@endif

@include('pages.users._inc.personal_info')
@include('pages.users._inc.password')

@endsection
