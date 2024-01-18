@extends('layouts.app')

@section('title', __('Add New City'))

@section('scripts')

    <script>
        function fetchCitiesForFilter() {

            $.ajax({
                url: '/api/v1/cities?per_page=-1',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    populateCityFilter(response.data, '#city');
                },
                error: function() {
                    alert('Failed to fetch cities from the API.');
                }
            });
        }

        function populateCityFilter(topics, div_id) {
            var selectElement = $(div_id);

            $.each(topics, function(index, topic) {
                var option = $('<option>', {
                    value: topic.id,
                    text: topic.name
                });

                selectElement.append(option);
            });
        }

        function validateForm() {
            var topicSelect = document.getElementById("city");
            var selectedValue = topicSelect.value;

            // Check if the first option is selected
            if (selectedValue === "-100") {
                // Show an alert or any other message to indicate the issue
                alert("Please select a valid city.");

                // Prevent form submission
                event.preventDefault();
            }
        }

        $(document).ready(function() {
            fetchCitiesForFilter();
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
                    <h5 class="card-title">Add New City</h5>

                    <form id="new_topic_form" action="{{ route('featured-cities.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <div class="form-group">
                                <label class="form-label" for="city"> City </label>
                                <select name="location_id" id="city"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100">Select City</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="mb-3">
                                <div class="mb-3 error-placeholder">
                                    <label class="form-label">City Preview Image</label>
                                    <div>
                                        <input type="file" required class="validation-file" name="file">
                                    </div>
                                </div>
                            </div>

                        </div>


                        <button type="submit" class="btn btn-primary" onclick="validateForm()">Add New City</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
