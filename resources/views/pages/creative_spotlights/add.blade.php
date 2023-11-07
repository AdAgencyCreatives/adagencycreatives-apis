@extends('layouts.app')

@section('title', __('Add New Spotlight'))

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

        $(document).ready(function() {
            $('#myForm').submit(function(event) {
                var selectedValue = $('#all_users').val();
                if (selectedValue === '-100') {
                    alert(
                        'Please select an author.'
                    ); // You can also use a different method to notify the user
                    event.preventDefault(); // Prevent form submission
                }
                // Add other validation or form submission logic as needed
            });
        });
    </script>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Add New Spotlight</h5>

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

                    <form action="{{ route('creative_spotlights.store') }}" method="POST" enctype="multipart/form-data"
                        id="myForm">
                        @csrf
                        @method('POST')

                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label" for="users"> Author </label>
                                <select name="author" id="all_users" class="form-control form-select custom-select select2"
                                    data-toggle="select2" required>
                                    <option value="-100" selected disabled> Select Author</option>
                                    <!-- Add other options if available -->
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" name="title">
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug">
                        </div>

                        <div class="mb-3">
                            <div class="mb-3 error-placeholder">
                                <label class="form-label">Spotlight Video</label>
                                <div>
                                    <input type="file" class="validation-file" name="file" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add New Spotlight</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
