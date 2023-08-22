<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Creative info</h5>
            </div>
            <div class="card-body">
                <form id="creative-form">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="name" class="form-label">Years of experience</label>
                                <input type="text" class="form-control" name="years_of_experience"
                                    id="years_of_experience" placeholder="Years of experience"
                                    value="{{ $user->creative->years_of_experience }}">
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="type_of_work"> Type of work </label>
                                <select name="type_of_work" id="type_of_work"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Type</option>
                                    <option value="Freelance" @if($user->creative->type_of_work == 'Freelance') selected
                                        @endif>Freelance</option>
                                    <option value="Contract" @if($user->creative->type_of_work == 'Contract') selected
                                        @endif>Contract</option>
                                    <option value="Part-time" @if($user->creative->type_of_work == 'Part-time') selected
                                        @endif>Part-time</option>
                                    <option value="Full-time" @if($user->creative->type_of_work == 'Full-time') selected
                                        @endif>Full-time</option>
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
