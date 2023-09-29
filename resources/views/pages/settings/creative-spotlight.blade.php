<div class="row">
    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">CREATIVE SPOTLIGHT SEO</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.creative_spotlight') }}" method="POST">
                    @csrf()
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="title">Title</label>
                                <input type="text" class="form-control" name="creative_spotlight_title"
                                    placeholder="SEO Title" value="{{ $settings['creative_spotlight_title'] }}">
                                <br><span class="font-13 text-muted">%post_name%</span>
                                <br><span class="font-13 text-muted">%post_date%</span>

                            </div>
                        </div>

                    </div>



                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>

        </div>
    </div>
</div>
