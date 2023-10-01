<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Qualifications</h5>
            </div>
            <div class="card-body">
                <form id="creative-form2" action="{{ route('creative.qualification.update', $user->creative?->uuid) }}"
                    method="POST">
                    @csrf()
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="title">Title</label>
                                <input type="text" class="form-control" name="title" placeholder="Job Title"
                                    value="{{ $user->creative?->title }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="linkedin">LinkedIn Profile</label>
                                <input type="url" class="form-control" name="linkedin"
                                    placeholder="LinkedIn Profile"
                                    value="{{ $user->links->where('label', 'linkedin')->first()?->url }}">
                                </select>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="portfolio">Portfolio Website</label>
                                <input type="url" class="form-control" name="portfolio"
                                    placeholder="Portfolio Website"
                                    value="{{ $user->links->where('label', 'portfolio')->first()?->url }}">
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="category">Industry Job Title</label>
                                <select name="category" id="category" required
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Title</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label class="form-label" for="industry"> Industry Experience </label>
                                <select name="industry_experience[]" id="industry" required
                                    class="form-control form-select custom-select select2" multiple="multiple"
                                    data-toggle="select2">
                                    <option value="-100"> Select Industry</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label class="form-label" for="media"> Media Experience </label>
                                <select name="media_experience[]" id="media" required
                                    class="form-control form-select custom-select select2" multiple="multiple"
                                    data-toggle="select2">
                                    <option value="-100"> Select Media</option>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-12">
                            <div class="form-group">
                                <label class="form-label" for="strengths">Character Strengths (Select up to 5)</label>
                                <select name="strengths[]" id="strengths"
                                    class="form-control form-select custom-select select2" multiple="multiple"
                                    data-toggle="select2">
                                    <option value="-100"> Select Strengths </option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>

        </div>
    </div>
</div>
