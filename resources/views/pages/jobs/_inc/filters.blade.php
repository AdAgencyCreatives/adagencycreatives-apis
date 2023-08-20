<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="filter-form">
                    <input type="hidden" class="d-none" name="filter" value="true" hidden>
                    <div class="row">

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="title">Title</label>
                                <input id="title" class="form-control" type="text" name="title"
                                    placeholder="Enter Title" />
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="category"> Category </label>
                                <select name="category" id="category"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Category</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="employement_type"> Employement Type </label>
                                <select name="employement_type" id="employement_type"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Employement Type</option>
                                    <option value="Freelance">Freelance</option>
                                    <option value="Contract">Contract</option>
                                    <option value="Part-time">Part-time</option>
                                    <option value="Full-time">Full-time</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="industry"> Industry </label>
                                <select name="industry[]" id="industry"
                                    class="form-control form-select custom-select select2" multiple="multiple"
                                    data-toggle="select2">
                                    <option value="-100"> Select Industry</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="media"> Media </label>
                                <select name="media[]" id="media" class="form-control form-select custom-select select2"
                                    multiple="multiple" data-toggle="select2">
                                    <option value="-100"> Select Media</option>

                                </select>
                            </div>
                        </div>



                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="employement_type"> Labels </label>
                                <select class="form-control select2" id="labels" multiple="multiple" name="labels[]">
                                    <option value="is_remote">Remote</option>
                                    <option value="is_hybrid">Hybrid</option>
                                    <option value="is_onsite">Onsite</option>
                                    <option value="is_featured">Featured</option>
                                    <option value="is_urgent">Urgent</option>

                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm mt-4">
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Apply</button>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="clear-button">Clear</button>


                        </div>
                    </div>


                </form>

            </div>
        </div>
    </div>
</div>