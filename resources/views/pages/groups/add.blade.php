@extends('layouts.app')

@section('title', __('Add Group'))
@section('scripts')
<script>
$(document).ready(function() {

    const submitButton = document.getElementById('submitButton');
    $("form").on("submit", function(event) {

        submitButton.disabled = true; // Disable the submit button
        event.preventDefault();

        var formData = new FormData(this);
        $.ajax({
            url: '{{ route("groups.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'Success',
                    text: "Group has been created successfully",
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
});
</script>
@endsection
@section('content')

@if(session('success'))
<script>
Swal.fire({
    title: 'Success',
    text: 'Status has been updated.',
    icon: 'success'
});
</script>
@endif


<div id="error-messages" class="alert alert-danger alert-dismissible" style="display: none;" role="alert">
    <div class="alert-message">
        <strong>Error!</strong> Please fix the following issues:
        <ul></ul>
    </div>
</div>

<h1 class="h3 mb-3">Add New Group</h1>
<div class="row">
    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-body">
                <form action="{{route('groups.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">

                            <div class="mb-3">
                                <label for="name" class="form-label"> Name</label>
                                <input type="text" class="form-control" name="name" placeholder="Group Name">
                            </div>



                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" spellcheck="true"
                                    style="height: 120px;"></textarea>
                            </div>

                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="status"> Status </label>
                                    <select name="status" id="status"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="public" selected>
                                            Public</option>
                                        <option value="private">
                                            Private
                                        </option>
                                        <option value="hidden">
                                            Hidden</option>

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

                    </div>

                    <button type="submit" class="btn btn-primary" id="submitButton">Add New Group</button>
                </form>
            </div>

        </div>
    </div>
</div>


@endsection