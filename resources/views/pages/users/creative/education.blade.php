<div class="row">

    <div class="col-md-12 col-xl-12">
        <form id="creative-form2" action="{{ route('creative.education.update', $user->creative?->uuid) }}"
            method="POST">
            @csrf()
            @method('PUT')


            @if(isset($user->resume->educations))
            @foreach ( isset($user->resume->educations) ? $user->resume->educations : [] as $key => $education)
            <div class="card">

                <div class="card-header">
                    <h5 class="card-title mb-0">Education {{ $key + 1 }}</h5>
                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6">
                            <input type="hidden" name="education_id[]" value="{{ $education->id }}">
                            <div class="mb-3">
                                <label for="degree" class="form-label">Degree</label>
                                <input type="text" class="form-control" name="degree[]" placeholder="Degree"
                                    value="{{ $education->degree }}">
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="college" class="form-label">College</label>
                                <input type="text" class="form-control" name="college[]" placeholder="College"
                                    value="{{ $education->college }}">
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="completed_at"> Completion Date </label>
                                    <input type="text" class="form-control" placeholder="Completed At" disabled
                                        value="{{ $education->completed_at }}">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            @endforeach
            <button type="submit" class="btn btn-primary">Save changes</button>
            @else
            <p>No Education found</p>
            @endif

        </form>
    </div>
</div>