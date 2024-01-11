@extends('layouts.app')

@section('title', __('Edit Resource'))

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
                var option = $('<option>', {
                    value: topic.id,
                    text: topic.title
                });

                selectElement.append(option);
            });

            var selectedTopicId = {{ $resource->topic_id }};
            $('#topic').val(selectedTopicId).trigger('change');
        }

        function validateForm() {
            var topicSelect = document.getElementById("topic");
            var selectedValue = topicSelect.value;

            // Check if the first option is selected
            if (selectedValue === "-100") {
                // Show an alert or any other message to indicate the issue
                alert("Please select a valid topic.");

                // Prevent form submission
                event.preventDefault();
            }
        }

        $(document).ready(function() {
            fetchTopicsForFilter();

        });
    </script>
@endsection


@section('content')

    @if (session('success'))
        <x-alert type="success"></x-alert>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Edit Resource</h5>

                    <form id="edit_resource_form" action="{{ route('resource.update', $resource->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label" for="topic"> Topics </label>
                                <select name="topic_id" id="topic"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100">Select Topic</option>
                                    <!-- The topics will be populated dynamically using the script -->
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Resource Name</label>
                            <input type="text" class="form-control" id="title" value="{{ $resource->title }}"
                                name="title">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description"
                                value="{{ $resource->description }}">
                        </div>

                        <div class="mb-3">
                            <label for="link" class="form-label">Link</label>
                            <input type="text" class="form-control" id="link" name="link"
                                value="{{ $resource->link }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Website Preview (optional)</label>
                            <div>
                                <input type="file" class="validation-file" name="file">
                            </div>

                            @if ($resource->preview_link)
                                <div class="mt-2">
                                    <label class="form-label">Current Image:</label>
                                    <img src="{{ getAttachmentBasePath() . $resource->preview_link }}" alt="Current Image"
                                        style="max-width: 600px;">
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary" onclick="validateForm()">Update</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
