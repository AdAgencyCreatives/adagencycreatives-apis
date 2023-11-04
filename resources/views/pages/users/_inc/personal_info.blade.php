<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0">Personal info</h5>
            </div>
            <div class="card-body">
                <form id="profile-form">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" placeholder="First Name"
                                    value="{{ $user->first_name }}">
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" placeholder="Last Name"
                                    value="{{ $user->last_name }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" placeholder="Username"
                                    value="{{ $user->username }}">
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" placeholder="Email"
                                    value="{{ $user->email }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="status"> Status </label>
                                    <select name="status" id="status"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="-100"> Select Status</option>
                                        <option value="pending" @if ($user->status == 'pending') selected @endif>
                                            Pending</option>
                                        <option value="active" @if ($user->status == 'active') selected @endif>
                                            Active
                                        </option>
                                        <option value="inactive" @if ($user->status == 'inactive') selected @endif>
                                            Inactive</option>

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="role"> Role </label>
                                    <select name="role" id="role"
                                        class="form-control form-select custom-select select2" data-toggle="select2">
                                        <option value="-100"> Select Role</option>
                                        <option value="advisor" @if ($user->role == 'advisor') selected @endif>
                                            Advisor</option>
                                        <option value="agency" @if ($user->role == 'agency') selected @endif>
                                            Agency
                                        </option>
                                        <option value="creative" @if ($user->role == 'creative') selected @endif>
                                            Creative</option>
                                        <option value="admin" @if ($user->role == 'admin') selected @endif>
                                            Admin</option>

                                    </select>
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

@if ($user->role == 'admin')
    @include('pages.users.admin.profile_picture')
@endif
