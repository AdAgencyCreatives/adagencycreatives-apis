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
                                <label class="form-label" for="apply_type"> Apply Type </label>
                                <select name="apply_type" id="apply_type"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Apply Type</option>
                                    <option value="Internal"> Internal</option>
                                    <option value="External"> External</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="employment_type"> Employment Type </label>
                                <select name="employment_type" id="employment_type"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                </select>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="status"> Status </label>
                                <select name="status" id="status"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Status</option>
                                    <option value="5"> Draft</option>
                                    <option value="0"> Pending</option>
                                    <option value="1"> Approved</option>
                                    <option value="2"> Rejected</option>
                                    <option value="3"> Expired</option>
                                    <option value="4"> Filled</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="industry"> Industry Experience </label>
                                <select name="industry[]" id="industry"
                                    class="form-control form-select custom-select select2" multiple="multiple"
                                    data-toggle="select2">
                                    <option value="-100"> Select Industry</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="media"> Media Experience </label>
                                <select name="media[]" id="media"
                                    class="form-control form-select custom-select select2" multiple="multiple"
                                    data-toggle="select2">
                                    <option value="-100"> Select Media</option>

                                </select>
                            </div>
                        </div>



                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="employement_type"> Workplace Preference </label>
                                <select class="form-control select2" id="labels" multiple="multiple" name="labels[]">
                                    <option value="is_remote">Remote</option>
                                    <option value="is_hybrid">Hybrid</option>
                                    <option value="is_onsite">Onsite</option>
                                    <optgroup label="Job Priority">
                                        <option value="is_featured">Featured</option>
                                        <option value="is_urgent">Urgent</option>
                                    </optgroup>


                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm mt-4">
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Apply</button>
                            <button type="button" class="btn btn-sm btn-secondary mt-2"
                                id="clear-button">Clear</button>
                        </div>
                    </div>


                </form>

            </div>
        </div>
    </div>
</div>
