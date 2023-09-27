<div class="row">
    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">JOB SEO</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.job') }}" method="POST">
                    @csrf()
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="title">Title</label>
                                <input type="text" class="form-control" name="job_title" placeholder="SEO Title"
                                    value="{{ $settings['job_title'] }}">
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="title">Description</label>
                                <input type="text" class="form-control" name="job_description"
                                    placeholder="SEO Description" value="{{ $settings['job_description'] }}">
                                <br>

                                <span class="font-13 text-muted">%job_title%</span>
                                <br>
                                <span class="font-13 text-muted">%job_description%</span>
                                <br>
                                <span class="font-13 text-muted">%site_name%</span>
                                <br>
                                <span class="font-13 text-muted">%site_description%</span>
                                <br>
                                <span class="font-13 text-muted">%separator%</span>
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
