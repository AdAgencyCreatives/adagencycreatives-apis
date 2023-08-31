<nav id="sidebar" class="sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="{{ route('dashboard.index') }}">
            <span class="align-middle me-3">
                <span class="align-middle">{{ env('APP_NAME') }}</span>
            </span>
        </a>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                General
            </li>
            <li class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('dashboard.index') }}">
                    <i class="align-middle" data-feather="sliders"></i>
                    <span class="align-middle">Dashboard</span>
                </a>
            </li>

            <li class="sidebar-header">
                Manage
            </li>
            <li class="sidebar-item {{ request()->is('users*') ? 'active' : '' }} ">
                <a data-target="#users" data-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle" data-feather="users"></i>
                    <span class="align-middle">Users</span>
                </a>
                <ul id="users"
                    class="sidebar-dropdown list-unstyled collapse {{ request()->is('users*') ? 'show' : '' }}"
                    data-parent="#sidebar">

                    <li class="sidebar-item {{ request()->is('users') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('users.index') }}">
                            <i class="align-middle" data-feather="users"></i>
                            <span class="align-middle">All Users</span>
                        </a>
                    </li>
                    <li class="sidebar-item" data-role="3">
                        <a class="sidebar-link" href="{{ route('users.index') }}?role=3">
                            <i class="align-middle" data-feather="layers"></i>
                            <span class="align-middle">Agencies</span>
                        </a>
                    </li>
                    <li class="sidebar-item" data-role="4">
                        <a class="sidebar-link" href="{{ route('users.index') }}?role=4">
                            <i class="align-middle" data-feather="cloud"></i>
                            <span class="align-middle">Creatives</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->is('users/create') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('users.create') }}">
                            <i class="align-middle" data-feather="user-plus"></i>
                            <span class="align-middle">Add New User</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item {{ request()->is('jobs*') ? 'active' : '' }} ">
                <a data-target="#jobs" data-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle" data-feather="list"></i>
                    <span class="align-middle">Jobs</span>
                </a>
                <ul id="jobs" class="sidebar-dropdown list-unstyled collapse {{ request()->is('jobs*') ? 'show' : '' }}"
                    data-parent="#sidebar">

                    <li class="sidebar-item {{ request()->is('jobs') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('jobs.index') }}">
                            <i class="align-middle" data-feather="list"></i>
                            <span class="align-middle">All Jobs</span>
                        </a>
                    </li>

                </ul>
            </li>

            <li class="sidebar-item {{ request()->is('groups*') ? 'active' : '' }} ">
                <a data-target="#groups" data-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle" data-feather="globe"></i>
                    <span class="align-middle">Groups</span>
                </a>
                <ul id="groups"
                    class="sidebar-dropdown list-unstyled collapse {{ request()->is('groups*') ? 'show' : '' }}"
                    data-parent="#sidebar">

                    <li class="sidebar-item {{ request()->is('groups') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('groups.index') }}">
                            <i class="align-middle" data-feather="globe"></i>
                            <span class="align-middle">All Groups</span>
                        </a>
                    </li>
                    <li class="sidebar-item {{ request()->is('groups/create') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('groups.create') }}">
                            <i class="align-middle" data-feather="plus-circle"></i>
                            <span class="align-middle">Add New Group</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item {{ request()->is('posts*') ? 'active' : '' }} ">
                <a data-target="#posts" data-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle" data-feather="edit"></i>
                    <span class="align-middle">Posts</span>
                </a>
                <ul id="posts"
                    class="sidebar-dropdown list-unstyled collapse {{ request()->is('posts*') ? 'show' : '' }}"
                    data-parent="#sidebar">

                    <li class="sidebar-item {{ request()->is('posts') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('posts.index') }}">
                            <i class="align-middle" data-feather="edit"></i>
                            <span class="align-middle">All Posts</span>
                        </a>
                    </li>

                </ul>
            </li>

            <li class="sidebar-item {{ request()->is('locations*') ? 'active' : '' }} ">
                <a data-target="#locations" data-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle" data-feather="map-pin"></i>
                    <span class="align-middle">Locations</span>
                </a>
                <ul id="locations"
                    class="sidebar-dropdown list-unstyled collapse {{ request()->is('locations*') ? 'show' : '' }}"
                    data-parent="#sidebar">

                    <li class="sidebar-item {{ request()->is('locations') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('locations.index') }}">
                            <i class="align-middle" data-feather="map-pin"></i>
                            <span class="align-middle">All Locations</span>
                        </a>
                    </li>

                </ul>
            </li>

            <li class="sidebar-header">
                Reports
            </li>
            <li class="sidebar-item {{ request()->is('reports') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('reports.index') }}">
                    <i class="align-middle" data-feather="trending-up"></i>
                    <span class="align-middle">Sales</span>
                </a>
            </li>

            <li class="sidebar-header">
                Personal
            </li>
            <li class="sidebar-item">
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <a class="sidebar-link" href="javascript:void(0);"
                        onclick="document.getElementById('logout-form').submit();">
                        <i class="align-middle" data-feather="log-out"></i>
                        <span class="align-middle">Logout</span>
                    </a>
                </form>
            </li>

        </ul>
    </div>
</nav>