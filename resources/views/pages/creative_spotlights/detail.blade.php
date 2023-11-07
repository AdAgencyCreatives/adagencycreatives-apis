@extends('layouts.app')

@section('title', 'Group Details')

@section('styles')
<style>
.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
}

.image-grid img {
    max-width: 100%;
    height: auto;
}

.image-container {
    display: flex;
    flex-wrap: wrap;
}

.image-item {
    flex: 0 0 33.33%;
    /* Distribute images in 3 columns */
    padding: 10px;
    box-sizing: border-box;
    max-width: 100%;
    height: 200px;
    /* Set a fixed height for the image container */
    overflow: hidden;
    position: relative;
}

.image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    /* Maintain aspect ratio and cover container */
}
</style>
@endsection

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

function fetchPostAttachments(post_id) {
    $.ajax({
        url: '/api/v1/attachments?filter[post_id]=' + post_id,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            var imageContainer = $('#imageContainer');

            var imageGrid = $('<div>', {
                class: 'image-grid'
            });

            $.each(response.data, function(index, attachment) {
                var imageItem = $('<div>', {
                    class: 'image-item'
                }).append(
                    $('<img>', {
                        src: attachment.url,
                        alt: 'Image ' + (index + 1)
                    })
                );

                imageGrid.append(imageItem);
            });

            imageContainer.append(imageGrid);
        },
        error: function() {
            alert('Failed to fetch attachments from the API.');
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

    $('select[name="group_role"]').change(function() {
        const memberId = $(this).data('member-id');
        const newRole = $(this).val();
        const url = $(this).data('url');

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                member_id: memberId,
                new_role: newRole,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log(response);
                if (response == true) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Role has been updated.',
                        icon: 'success',
                    })
                }
            },

            error: function(xhr) {
                // Handle error response if needed
            }
        });
    });

    fetchPostAttachments('9c04b8ff-224f-4d93-8e14-db789e303542');


});
</script>
@endsection

@section('content')
<h1 class="h3 mb-3">Post Details</h1>

<div id="error-messages" class="alert alert-danger alert-dismissible" style="display: none;" role="alert">
    <div class="alert-message">
        <strong>Error!</strong> Please fix the following issues:
        <ul></ul>
    </div>
</div>

<div class="row">

    <div class="col-md-4 col-xl-6">
        <div class="card">

            <div class="card-body px-4 pt-2">
                <h5>Content</h5>
                <p> {{ $post->content}} </p>
                <div class="badge bg-warning my-2">{{ ucfirst($post->status)}}</div>
                <p class="mb-2 fw-bold">Author: <span style="float:right">Created At:</span></p>
                <p class="mb-2 fw-bold">{{ $post->user->username }} <span
                        style="float:right">{{ $post->created_at}}</span></p>
            </div>

        </div>
    </div>

</div>

@include('pages.posts._inc.attachments')

@include('pages.posts._inc.likes')
@include('pages.posts._inc.comments')
@endsection