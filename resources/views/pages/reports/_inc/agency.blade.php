<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Agency info</h5>
            </div>
            <div class="card-body">
                <form id="agency-form">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="name" class="form-label">Agency Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Agency Name"
                                    value="{{ $user->agency->name }}">
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="size" class="form-label">Size</label>
                                <input type="text" class="form-control" name="size" placeholder="Size"
                                    value="{{ $user->agency->size }}">
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
                                    <option value="Freelance" @if($user->agency->type_of_work == 'Freelance') selected
                                        @endif>Freelance</option>
                                    <option value="Contract" @if($user->agency->type_of_work == 'Contract') selected
                                        @endif>Contract</option>
                                    <option value="Part-time" @if($user->agency->type_of_work == 'Part-time') selected
                                        @endif>Part-time</option>
                                    <option value="Full-time" @if($user->agency->type_of_work == 'Full-time') selected
                                        @endif>Full-time</option>
                                </select>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Industry Speciality</label>
                                <input type="text" disabled class="form-control" placeholder="Industry Speciality"
                                    value="{{ implode(', ', getIndustryNames($user->agency->industry_specialty)) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="status"> About </label>
                                    <textarea name="about" class="form-control tip-tap-editor" rows="2" placeholder="About"
                                        spellcheck="true" style="height: 225px;">{{ $user->agency->about }}</textarea>
                                </div>
                            </div>
                        </div>


                    </div>

                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>

        </div>
    </div>
</div>