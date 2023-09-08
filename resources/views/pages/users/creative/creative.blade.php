<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Creative info</h5>
            </div>
            <div class="card-body">
                <form id="creative-form2" action="{{ route('creative.update', $user->creative?->uuid) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf()
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label class="form-label" for="years_of_experience"> Years of experience </label>
                                <select name="years_of_experience" id="years_of_experience"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="-100"> Select Experience</option>
                                </select>
                            </div>

                        </div>
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

                    <div class="row">


                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_visible"> Show Profile </label>
                                <select name="is_visible" id="is_visible"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="1" @if($user->is_visible == 1) selected @endif> Show</option>
                                    <option value="0" @if($user->is_visible == 0) selected @endif> Hide</option>

                                </select>
                            </div>

                        </div>

                        <div class="col-md-6">
                            @php
                            $phoneData = $user->phones->where('label', 'personal')->first();
                            $countryCode = $phoneData ? $phoneData->country_code : '';
                            $phoneNumber = $phoneData ? $phoneData->phone_number : '';
                            @endphp
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="country_code" class="form-label">Country Code</label>
                                    <input type="text" class="form-control" name="country_code" id="country_code"
                                        placeholder="Country Code" value="{{ $countryCode }}">
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number (Personal)</label>
                                        <input type="text" class="form-control" name="phone" id="phone"
                                            placeholder="Phone Number" value="{{ $phoneNumber }}">
                                    </div>
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