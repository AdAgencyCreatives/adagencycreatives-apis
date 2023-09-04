<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="filter-form">
                    <input type="hidden" class="d-none" name="filter" value="true" hidden>
                    <div class="row">
                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="role"> Role </label>
                                <select name="role" id="role" class="form-control form-select custom-select select2"
                                    data-toggle="select2">
                                    <option value="-100"> Select Role</option>
                                    <option value="2"> Advisor</option>
                                    <option value="3"> Agency</option>
                                    <option value="4"> Creative</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="status"> Status </label>
                                <select name="status" id="status" class="form-control form-select custom-select select2"
                                    data-toggle="select2">
                                    <option value="-100"> Select Status</option>
                                    <option value="0"> Pending</option>
                                    <option value="1"> Active</option>
                                    <option value="2"> Inactive</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <input id="email" class="form-control" type="text" name="email"
                                    placeholder="Enter Email" />
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="first_name">First Name</label>
                                <input id="first_name" class="form-control" type="text" name="first_name"
                                    placeholder="Enter First Name" />
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="last_name">Last Name</label>
                                <input id="last_name" class="form-control" type="text" name="last_name"
                                    placeholder="Enter Last Name" />
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="username">Username</label>
                                <input id="username" class="form-control" type="text" name="username"
                                    placeholder="Enter username" />
                            </div>
                        </div>



                    </div>

                    <div class="row">
                        <div class="col-sm mt-4">
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Search</button>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="clear-button">Clear</button>


                        </div>
                    </div>


                </form>

            </div>
        </div>
    </div>
</div>