@extends('layouts.app')

@section('title', __('Add New Resource'))

@section('scripts')

    <script>
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

        });
    </script>
@endsection

@section('content')

    @if (session('success'))
        <x-created-alert type="success"></x-created-alert>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Add New Resource</h5>

                    <form id="new_topic_form" action="{{ route('publication-resource.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="slug" class="form-label">Link</label>
                            <input type="text" class="form-control" id="link" name="link">
                        </div>

                        <div class="mb-3">
                            <div class="mb-3">
                                <div class="mb-3 error-placeholder">
                                    <label class="form-label">Logo</label>
                                    <div>
                                        <input type="file" required class="validation-file" name="file">
                                    </div>
                                </div>
                            </div>

                        </div>


                        <button type="submit" class="btn btn-primary" onclick="validateForm()">Add New Resource</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
