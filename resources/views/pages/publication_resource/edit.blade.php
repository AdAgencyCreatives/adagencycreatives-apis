@extends('layouts.app')

@section('title', __('Edit Resource'))

@section('scripts')
    <script>
        $(document).ready(function() {

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

                    <form id="edit_resource_form"
                        action="{{ route('publication-resource.update', $publicationResource->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="slug" class="form-label">Link</label>
                            <input type="text" class="form-control" id="link"
                                value="{{ $publicationResource->link }}" name="link">
                        </div>

                        <div class="mb-3">
                            <div class="mb-3">
                                <div class="mb-3 error-placeholder">
                                    <label class="form-label">Logo</label>
                                    <div>
                                        <input type="file" class="validation-file" name="file">
                                    </div>

                                    @if ($publicationResource->preview_link)
                                        <div class="mt-2">
                                            <label class="form-label">Current Image:</label>
                                            <img src="{{ getAttachmentBasePath() . $publicationResource->preview_link }}"
                                                alt="Current Image" style="max-width: 600px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" onclick="validateForm()">Update</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
