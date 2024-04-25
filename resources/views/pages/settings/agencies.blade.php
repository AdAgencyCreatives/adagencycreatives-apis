<div class="row">
    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">AGENCIES SEO</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.agencies') }}" method="POST">
                    @csrf()
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="title">Title</label>
                                <input type="text" class="form-control" name="agency_title" placeholder="SEO Title"
                                    value="{{ $settings['agency_title'] }}">
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="title">Description</label>
                                <input type="text" class="form-control" name="agency_description"
                                    placeholder="SEO Description" value="{{ $settings['agency_description'] }}">

                                <br><span class="font-13 text-muted">%agencies_contact_first_name%</span>
                                <br><span class="font-13 text-muted">%agencies_contact_last_name%</span>
                                <br><span class="font-13 text-muted">%agencies_company_name%</span>
                                <br><span class="font-13 text-muted">%agencies_location%</span>
                                <br><span class="font-13 text-muted">%agencies_about%</span>
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
