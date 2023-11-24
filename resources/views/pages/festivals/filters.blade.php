<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="filter-form">
                    <input type="hidden" class="d-none" name="filter" value="true" hidden>

                    <div class="row">

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="users"> User </label>
                                <select name="users" id="all_users"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select User</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="category"> Category </label>
                                <select name="category" id="category"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Category</option>
                                    <option value="humor"> Humor </option>
                                    <option value="storytelling"> Storytelling</option>
                                    <option value="design"> Design</option>
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
