@extends('layouts.app')

@section('title', __('Edit Spotlight'))

@section('scripts')
    <script>
        $(document).ready(function() {

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

        $(".daterange").daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {
                format: "Y-MM-DD"
            }
        });
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
                        <x-alert type="success"></x-alert>
                    @endif

                    <form action="{{ route('creative_spotlights.update', $spotlight->id) }}" method="POST"
                        enctype="multipart/form-data" id="myForm">
                        @csrf
                        @method('PUT')


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
                                    <input type="file" class="validation-file" name="file">
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
