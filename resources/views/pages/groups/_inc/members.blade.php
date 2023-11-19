<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">
            <div class="card-header">
                <h1>All Members</h1>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:40%;">ID</th>
                            <th style="width:40%;">Name</th>
                            <th style="width:25%">Role</th>
                            <th style="width:25%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($group->members as $member)
                            <tr>
                                <td>{{ $member->id }}</td>
                                <td>{{ $member->user->first_name }} {{ $member->user->last_name }}</td>
                                <td> <select name="group_role" class="form-control form-select select2"
                                        data-toggle="select2" data-member-id="{{ $member->id }}"
                                        data-url="{{ route('groups.update-member') }}">
                                        <option value="admin" @if ($member->role == 'Admin') selected @endif>Admin
                                        </option>
                                        <option value="moderator" @if ($member->role == 'Moderator') selected @endif>
                                            Moderator</option>
                                        <option value="member" @if ($member->role == 'Member') selected @endif>Member
                                        </option>
                                    </select></td>

                                <td>

                                    <a href="#" class="btn btn-danger delete-button delete-user-btn"
                                        data-id="{{ $member->uuid }}">Delete</a>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>



            </div>

        </div>
    </div>
</div>
