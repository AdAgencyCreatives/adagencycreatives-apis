@extends('layouts.app')

@section('title', __('Add New Resource'))

@section('scripts')

    <script>
        function fetchTopicsForFilter() {

            $.ajax({
                url: '/api/v1/topics',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateTopicFilter(response.data, '#topic');
                },
                error: function() {
                    alert('Failed to fetch topics from the API.');
                }
            });
        }

        function populateTopicFilter(topics, div_id) {
            var selectElement = $(div_id);

            $.each(topics, function(index, topic) {
                console.log('d');
                var option = $('<option>', {
                    value: topic.id,
                    text: topic.title
                });

                selectElement.append(option);
            });
        }

        $(document).ready(function() {

            fetchTopicsForFilter();

            $('#new_topic_form').submit(function(event) {
                event.preventDefault();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    }
                });

                var data = {
                    title: $('#title').val(),
                    link: $('#link').val(),
                    description: $('#description').val(),
                    topic_id: $('#topic').val()
                };

                $.ajax({
                    url: '/api/v1/mentor-resources',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        Swal.fire({
                            title: 'Success',
                            text: "Resource Created Successfully.",
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
    </script>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Add New Resource</h5>

                    <form id="new_topic_form">
                        @csrf

                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label" for="topic"> Topics </label>
                                <select name="topic" id="topic" class="form-control form-select custom-select select2"
                                    data-toggle="select2">
                                    <option value="-100">Select Topic</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Resource Name</label>
                            <input type="text" class="form-control" id="title">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description">
                        </div>



                        <div class="mb-3">
                            <label for="slug" class="form-label">Link</label>
                            <input type="text" class="form-control" id="link">
                        </div>


                        <button type="submit" class="btn btn-primary">Add New Resource</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
