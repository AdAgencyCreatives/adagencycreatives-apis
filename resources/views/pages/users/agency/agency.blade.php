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
                                <input type="text" class="form-control" name="name" id="name" placeholder="Agency Name"
                                    value="{{ $user->agency?->name }}">
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
                                <label class="form-label" for="type_of_work"> Type of work </label>
                                <select name="type_of_work" id="type_of_work"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Type</option>
                                    <option value="Freelance" @if($user->agency?->type_of_work == 'Freelance') selected
                                        @endif>Freelance</option>
                                    <option value="Contract" @if($user->agency?->type_of_work == 'Contract') selected
                                        @endif>Contract</option>
                                    <option value="Part-time" @if($user->agency?->type_of_work == 'Part-time') selected
                                        @endif>Part-time</option>
                                    <option value="Full-time" @if($user->agency?->type_of_work == 'Full-time') selected
                                        @endif>Full-time</option>
                                </select>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="industry"> Industry Experience </label>
                                <select name="industry_specialty[]" id="industry" required
                                    class="form-control form-select custom-select select2" multiple="multiple"
                                    data-toggle="select2">
                                    <option value="-100"> Select Industry Speciality</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="company_linkedin"> Company LinkedIn </label>
                                <input type="url" class="form-control" name="company_linkedin"
                                    placeholder="Company LinkedIn"
                                    value="{{ $user->links->where('label', 'linkedin')->first()?->url }}">
                                </select>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="company_website">Company Website </label>
                                <input type="url" class="form-control" name="company_website"
                                    placeholder="Company LinkedIn"
                                    value="{{ $user->links->where('label', 'website')->first()?->url }}">
                                </select>
                            </div>

                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="status"> About </label>
                                    <textarea name="about" class="form-control" rows="2" placeholder="About"
                                        spellcheck="true" style="height: 225px;">{{ $user->agency?->about }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_visible"> Show Company Profile </label>
                                <select name="is_visible" id="is_visible"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="1" @if($user->is_visible == 1) selected @endif> Show</option>
                                    <option value="0" @if($user->is_visible == 0) selected @endif> Hide</option>

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
                                <img class="rounded-circle img-responsive mt-2 lazy"
                                    src="{{ isset($user->attachments[0]) ? asset('storage/' . $user->attachments[0]['path']) : asset('images/default.png') }}"
                                    alt="" width="300" height="300" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>

        </div>
    </div>
</div>