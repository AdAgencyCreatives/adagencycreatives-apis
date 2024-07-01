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
                                <select name="role" id="role"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Role</option>
                                    <option value="1"> Admin</option>
                                    <option value="2"> Advisor</option>
                                    <option value="3"> Agency</option>
                                    <option value="4"> Creative</option>
                                    <option value="5"> Recruiter</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="status"> Status </label>
                                <select name="status" id="status"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
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
                                <label class="form-label" for="is_featured"> Is Featured? </label>
                                <select name="is_featured" id="is_featured"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Featured</option>
                                    <option value="1"> Yes</option>
                                    <option value="0"> No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="is_deleted"> Is Deleted? </label>
                                <select name="is_deleted" id="is_deleted"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Deleted</option>
                                    <option value="1"> Yes</option>
                                    <option value="0"> No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="is_visible"> Profile Hidden Status </label>
                                <select name="is_visible" id="is_visible"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Visibility</option>
                                    <option value="1"> Active</option>
                                    <option value="0"> Hidden</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-sm" id="first_name_div">
                            <div class="form-group">
                                <label class="form-label" for="first_name">Name</label>
                                <input id="first_name" class="form-control" type="text" name="first_name"
                                    placeholder="Enter Name" />
                            </div>
                        </div>
                        {{-- <div class="col-sm">
                            <div class="form-group">
                                <label class="form-label" for="last_name">Last Name</label>
                                <input id="last_name" class="form-control" type="text" name="last_name"
                                    placeholder="Enter Last Name" />
                            </div>
                        </div> --}}
                        <div class="col-sm" id="username_div">
                            <div class="form-group">
                                <label class="form-label" for="username">Username</label>
                                <input id="username" class="form-control" type="text" name="username"
                                    placeholder="Enter username" />
                            </div>
                        </div>



                    </div>

                    <div class="row">

                        <div class="col-sm d-none" id="agency_name_filter">
                            <div class="form-group">
                                <label class="form-label" for="name">Agency Name</label>
                                <input id="agency_name" class="form-control" type="text" name="agency_name"
                                    placeholder="Enter Agency Name" />
                            </div>
                        </div>


                        <div class="col-sm d-none" id="agency_slug_filter">
                            <div class="form-group">
                                <label class="form-label" for="slug">Slug</label>
                                <input id="agency_slug" class="form-control" type="text" name="agency_slug"
                                    placeholder="Enter slug" />
                            </div>
                        </div>
                    </div>

                    @include('pages.users._inc.creative_filters')

                    <div class="row">
                        <div class="col-sm mt-4">
                            <button type="submit" class="btn btn-sm btn-primary mt-2">Search</button>
                            <button type="button" class="btn btn-sm btn-secondary mt-2"
                                id="clear-button">Clear</button>
                        </div>
                    </div>


                </form>

            </div>
        </div>
    </div>
</div>
