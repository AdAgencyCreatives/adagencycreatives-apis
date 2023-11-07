@extends('layouts.app')

@section('title', __('Edit Spotlight'))

@section('scripts')
    <script>
        $(document).ready(function() {
            fetchUsers();
            $('#new_category_form').submit(function(event) {
                event.preventDefault();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    }
                });

                var data = {
                    name: $('#new_category').val()
                };

                $.ajax({
                    url: '/api/v1/categories',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        Swal.fire({
                            title: 'Success',
                            text: "Category Created Successfully.",
                            icon: 'success'
                        }).then((result) => {
                            fetchData();
                        })
                    },
                    error: function(error) {
                        if (error.responseJSON && error.responseJSON.errors) {
                            var errorMessages = error.responseJSON.errors;

                            // Process and display error messages
                            var errorMessage = '';
                            $.each(errorMessages, function(field, messages) {
                                errorMessage += field + ': ' + messages.join(', ') +
                                    '\n';
                            });

                            Swal.fire({
                                title: 'Validation Error',
                                text: errorMessage,
                                icon: 'error'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: error.message,
                                icon: 'error'
                            });
                        }
                    }
                });
            });

        });

        function fetchUsers() {

            $.ajax({
                url: '/api/v1/get_users/spotlights',
                method: 'GET',
                dataType: 'json',
                success: function(response) {

                    populateUserDropdown(response, '#all_users');
                    var spotlightUserId = <?php echo json_encode($spotlight->user_id); ?>;
                    // Use jQuery to set the selected value
                    $('#all_users').val(spotlightUserId).trigger('change');
                },
                error: function() {
                    alert('Failed to fetch creatives from the API.');
                }
            });
        }

        function populateUserDropdown(users, div_id) {
            var selectElement = $(div_id);
            $.each(users, function(index, user) {

                var option = $('<option>', {
                    value: user.id,
                    text: user.first_name + ' ' + user.last_name + ' (' + user.email + ')'
                });

                selectElement.append(option);
            });
        }
    </script>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Update Spotlight</h5>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <x-alert-created type="success"></x-alert>
                    @endif

                    <form action="{{ route('creative-spotlights.store') }}" method="POST" enctype="multipart/form-data"
                        id="myForm">
                        @csrf
                        @method('POST')

                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label" for="users"> Author </label>
                                <select name="author" id="all_users" class="form-control form-select custom-select select2"
                                    data-toggle="select2" required>
                                    <option value="-100" selected disabled> Select Author</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" value="{{ $spotlight->title }}">
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug" value="{{ $spotlight->slug }}">
                        </div>

                        <div class="mb-3">
                            <div class="mb-3 error-placeholder">
                                <label class="form-label">Spotlight Video</label>
                                <div>
                                    <input type="file" class="validation-file" name="file" required>
                                </div>
                            </div>
                            <div>
                                <h1>
                                    <a href="{{ getAttachmentBasePath() . $spotlight->path }}" target="_blank">Spotlight
                                        URL</a>
                                </h1>

                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="mb-3 error-placeholder">

                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
