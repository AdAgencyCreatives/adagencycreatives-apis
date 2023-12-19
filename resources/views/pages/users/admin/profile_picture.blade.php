<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Profile Picture</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('user.profile.picture', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="mb-3 error-placeholder">
                                    <label class="form-label">Profile Photo</label>
                                    <div>
                                        <input type="file" class="validation-file" name="file">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-center">
                                <h4>Profile Photo</h4>
                                @if ($user->profile_picture)
                                    <img class="rounded-circle img-responsive mt-2 lazy"
                                        src="{{ getAttachmentBasePath() . $user->profile_picture['path'] }}"
                                        alt="" width="300" height="300" />
                                @else
                                    <p>No image uploaded yet</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>

        </div>
    </div>
</div>
