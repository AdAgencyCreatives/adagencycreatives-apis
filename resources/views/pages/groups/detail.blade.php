@extends('layouts.app')

@section('title', 'Group Details')

@section('scripts')

    <script>
        function populateUserFilter(users, div_id) {
            var selectElement = $(div_id);
            $.each(users, function(index, user) {
                var option = $('<option>', {
                    value: user.id,
                    text: user.first_name + ' ' + user.last_name + ' (' + user.email + ' )'
                });

                selectElement.append(option);
            });
        }

        function fetchUsers() {
            $.ajax({
                url: '/api/v1/get_users/spotlights',
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
                    url: '{{ route('groups.new-member') }}',
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

            $(document).on('click', '.delete-user-btn', function() {
                var resourceId = $(this).data('id');
                var csrfToken = '{{ csrf_token() }}';
                deleteConfirmation(resourceId, 'group member', 'group-members', csrfToken);
            });

            function deleteConfirmation(userId, resource, url, csrfToken) {

                Swal.fire({
                    title: 'Confirm Delete',
                    text: 'Are you sure you want to delete this ' + resource + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteResource(userId, resource, url, csrfToken);
                    }
                });
            }

            function deleteResource(userId, resource, url, csrfToken) {

                var msg = resource.charAt(0).toUpperCase() + resource.slice(1) + ' has been deleted.';
                $.ajax({
                    url: '/api/v1/' + url + '/' + userId,

                    method: 'DELETE',
                    data: {
                        _token: csrfToken
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            title: 'Success',
                            text: msg,
                            icon: 'success'
                        }).then((result) => {
                            location.reload();
                        });
                    },
                    error: function() {
                        alert('Failed to delete the ' + resource);
                    }
                });
            }


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

    @if (session('success'))
        <x-alert type="success"></x-alert>
    @endif

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
                                    <textarea class="form-control" name="description" spellcheck="true" style="height: 120px;">{{ $group->description }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <div class="form-group">
                                        <label class="form-label" for="status"> Status </label>
                                        <select name="status" id="status"
                                            class="form-control form-select custom-select select2" data-toggle="select2">
                                            <option value="public" @if ($group->status == 'public') selected @endif>Public
                                            </option>

                                            <option value="private" @if ($group->status == 'private') selected @endif>
                                                Private</option>

                                            <option value="hidden" @if ($group->status == 'hidden') selected @endif>Hidden
                                            </option>

                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="mb-3 error-placeholder">
                                        <label class="form-label">Cover Image</label>
                                        <div>
                                            <input type="file" class="validation-file" name="file">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-center">
                                    <h4>Cover Image</h4>
                                    @if (isset($group->attachment))
                                        <img class="rounded-circle img-responsive mt-2 lazy"
                                            src="{{ isset($group->attachment) ? getAttachmentBasePath() . $group->attachment->path : asset('images/default.png') }}"
                                            alt="{{ $group->attachment->resource_type }}" width="300" height="300" />
                                    @else
                                        <p>No cover image uploaded yet</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="submitButton">Save</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    @include('pages.groups._inc.members')
    @include('pages.groups._inc.add_members')
@endsection
