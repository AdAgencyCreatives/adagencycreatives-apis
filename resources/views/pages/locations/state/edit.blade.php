@extends('layouts.app')

@section('title', __('Edit ' . ($is_state ? 'State' : 'City')))

@section('scripts')

<script>
$(document).ready(function() {
    @if (Session::has('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ Session::get('success') }}",
                confirmButtonText: 'OK'
            });
    @endif
});


</script>

@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Edit {{ $is_state ? 'State' : 'City' }}</h5>

                <form action="{{ route('locations.update', $location->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') 

                    <div class="mb-3">
                        <label for="location_name" class="form-label">{{ $is_state ? 'State' : 'City' }} Name</label>
                        <input type="text" class="form-control" id="location_name" name="name" value="{{ $location->name }}">
                    </div>

                    

                    <div class="mb-3">
                        <div class="mb-3">
                            <div class="mb-3 error-placeholder">
                                <label class="form-label">{{ $is_state ? 'State' : 'City' }} Image</label>
                                <div>
                                    <input type="file" class="validation-file" name="file">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="mb-3">
                        <img id="existing_image" src="{{ $link }}" class="img-thumbnail" style="max-width: 450px; display: {{ $link ? 'block' : 'none' }}">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="featured" name="featured" 
                               {{ $location->is_featured ? 'checked' : '' }}>
                        <label class="form-check-label" for="featured">Featured</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Update {{ $is_state ? 'State' : 'City' }}</button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
