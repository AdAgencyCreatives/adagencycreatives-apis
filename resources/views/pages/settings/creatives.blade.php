<div class="row">
    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">CREATIVES SEO</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.creatives') }}" method="POST">
                    @csrf()
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="title">Title</label>
                                <input type="text" class="form-control" name="creative_title" placeholder="SEO Title"
                                    value="{{ $settings['creative_title'] }}">
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="title">Description</label>
                                <input type="text" class="form-control" name="creative_description"
                                    placeholder="SEO Description" value="{{ $settings['creative_description'] }}">

                                <br><span class="font-13 text-muted">%creatives_first_name%</span>
                                <br><span class="font-13 text-muted">%creatives_last_name%</span>
                                <br><span class="font-13 text-muted">%creatives_title%</span>
                                <br><span class="font-13 text-muted">%creatives_location%</span>
                                <br><span class="font-13 text-muted">%creatives_about%</span>
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
