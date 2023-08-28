<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">
            <div class="card-header">
                <h1>Add Member</h1>
            </div>
            <div class="card-body">
                <form id="add_members" action="{{ route('groups.new-member') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <input type="hidden" name="group_id" value="{{ $group->id }}">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="user"> Member </label>
                                    <select name="user_id" id="user"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="-100"> Select Member</option>

                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary" id="submitButton">Save</button>
                </form>
            </div>

        </div>
    </div>
</div>