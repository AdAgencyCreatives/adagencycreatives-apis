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

                                    @foreach(\App\Models\Job::EMPLOYMENT_TYPE as $type)
                                    <option value="{{$type}}" @if($user->creative->type_of_work == $type) selected
                                        @endif>{{$type}}</option>
                                    @endforeach

                                </select>
                            </div>

                        </div>
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
                                    <input type="text" disabled class="form-control" name="country_code"
                                        id="country_code" placeholder="Country Code" value="+1">
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number (Personal)</label>
                                        <input type="text" class="form-control" name="phone" id="phone"
                                            placeholder="Phone Number (without country code)"
                                            value="{{ $phoneNumber }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_featured"> Featured Candidate </label>
                                <select name="is_featured" id="is_featured"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="1" @if($user->creative->is_featured == 1) selected @endif> Yes
                                    </option>
                                    <option value="0" @if($user->creative->is_featured == 0) selected @endif> No
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_urgent"> Urgent Candidate</label>
                                <select name="is_urgent" id="is_urgent"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="1" @if($user->creative->is_urgent == 1) selected @endif> Yes
                                    </option>
                                    <option value="0" @if($user->creative->is_urgent == 0) selected @endif> No
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_opentoremote"> Open to Remote </label>
                                <select name="is_opentoremote" id="is_opentoremote"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="1" @if($user->creative->is_opentoremote == 1) selected @endif> Yes
                                    </option>
                                    <option value="0" @if($user->creative->is_opentoremote == 0) selected @endif> No
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="is_opentorelocation"> Open to Relocation </label>
                                <select name="is_opentorelocation" id="is_opentorelocation"
                                    class="form-control form-select custom-select select2" data-toggle="select2">
                                    <option value="1" @if($user->creative->is_opentorelocation == 1) selected @endif>
                                        Yes
                                    </option>
                                    <option value="0" @if($user->creative->is_opentorelocation == 0) selected @endif> No
                                    </option>
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