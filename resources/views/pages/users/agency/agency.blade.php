<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Agency info</h5>
            </div>
            <div class="card-body">
                <form id="agency-form2" action="{{ route('agency.update', $user->agency?->uuid) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf()
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="name" class="form-label">Agency Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Agency Name" value="{{ $user->agency?->name }}">
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="size" class="form-label">Size</label>
                                <input type="text" class="form-control" name="size" placeholder="Size"
                                    value="{{ $user->agency?->size }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="industry"> Industry Experience </label>
                                <select name="industry_experience[]" id="industry" required
                                    class="form-control form-select custom-select select2" multiple="multiple"
                                    data-toggle="select2">
                                    <option value="-100"> Select Industry Experience</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="media"> Media Experience </label>
                                <select name="media_experience[]" id="media" required
                                    class="form-control form-select custom-select select2" multiple="multiple"
                                    data-toggle="select2">
                                    <option value="-100"> Select Media Experience</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="linkedin"> Company LinkedIn </label>
                                <input type="text" class="form-control" name="linkedin"
                                    placeholder="Company LinkedIn"
                                    value="{{ $user->links->where('label', 'linkedin')->first()?->url }}">
                                </select>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="website">Company Website </label>
                                <input type="text" class="form-control" name="website" placeholder="Company LinkedIn"
                                    value="{{ $user->links->where('label', 'website')->first()?->url }}">
                                </select>
                            </div>

                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="about"> About </label>
                                    <textarea name="about" class="form-control" rows="2" placeholder="About" spellcheck="true"
                                        style="height: 225px;">{{ $user->agency?->about }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_visible"> Show Profile </label>
                                <select name="is_visible" id="is_visible"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="1" @if ($user->is_visible == 1) selected @endif> Show
                                    </option>
                                    <option value="0" @if ($user->is_visible == 0) selected @endif> Hide
                                    </option>

                                </select>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="workplace_experience">Workplace Preference</label>
                                <select class="form-control select2" multiple="multiple" name="workplace_experience[]">
                                    @foreach (\App\Models\Job::WORKPLACE_PREFERENCE as $value => $label)
                                        <option value="{{ $value }}"
                                            @if ($user->agency?->{$value}) selected @endif>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_featured"> Featured? </label>
                                <select name="is_featured" id="is_featured"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="1" @if ($user->agency?->is_featured == 1) selected @endif> Yes
                                    </option>
                                    <option value="0" @if ($user->agency?->is_featured == 0) selected @endif> No
                                    </option>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_urgent"> Urgent? </label>
                                <select name="is_urgent" id="is_urgent"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="1" @if ($user->agency?->is_urgent == 1) selected @endif> Yes
                                    </option>
                                    <option value="0" @if ($user->agency?->is_urgent == 0) selected @endif> No
                                    </option>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="form-label" for="state"> State </label>
                                <select name="state" id="state"
                                    class="form-control form-select custom-select select2" data-toggle="select2"
                                    required>
                                    <option value="-100"> Select State</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">

                            <div class="form-group">
                                <label class="form-label" for="city"> City </label>
                                <select name="city" id="city"
                                    class="form-control form-select custom-select select2" data-toggle="select2"
                                    required>
                                    <option value="-100"> Select City</option>
                                </select>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="mb-3 error-placeholder">
                                    <label class="form-label">Agency Logo</label>
                                    <div>
                                        <input type="file" class="validation-file" name="file">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-center">
                                <h4>Logo</h4>
                                @if (count($user->attachments) > 0)
                                    <img class="rounded-circle img-responsive mt-2 lazy"
                                        src="{{ getAttachmentBasePath() . $user->attachments[0]['path'] }}"
                                        alt="" width="300" height="300" />
                                @else
                                    <p>No logo uploaded yet</p>
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
