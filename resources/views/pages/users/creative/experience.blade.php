<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Experiences</h5>
            </div>

            <div class="card-body">
                <form id="creative-form2" action="{{ route('creative.experience.update', $user->creative?->uuid) }}"
                    method="POST">
                    @csrf()
                    @method('PUT')

                    @foreach ($user->resume->experiences as $experience)
                    <div class="row">

                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" name="title[]" placeholder="Title"
                                    value="{{ $experience->title }}">
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company" class="form-label">Company</label>
                                <input type="text" class="form-control" name="company[]" placeholder="Company"
                                    value="{{ $experience->company }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description[]" rows="5"
                                    spellcheck="false">{{ $experience->description }}</textarea>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="started_at"> Started At </label>
                                    <input type="text" class="form-control" placeholder="Started At" disabled
                                        value="{{ $experience->started_at }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="completed_at"> Completed At </label>
                                    <input type="text" class="form-control" placeholder="Completed At" disabled
                                        value="{{ $experience->completed_at }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <p>*************************************************************************************************************************************************************************************
                    </p>

                    @endforeach


                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>

        </div>
    </div>
</div>