<div class="row">

    <div class="col-md-12 col-xl-12">
        <div class="card">
            <div class="card-header">
                <h1>All Members</h1>
            </div>
            <div class="card-body">
                <table id="users-table" class="table table-striped dataTable no-footer dtr-inline" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Role</th>
                            <!-- <th>Actions</th> -->
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($group->members as $member)
                        <tr>
                            <td>{{ $member->id }}</td>
                            <td>{{ $member->user->first_name }} {{ $member->user->last_name }}</td>
                            <td>{{ $member->role }} </td>
                        </tr>
                        @endforeach

                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>