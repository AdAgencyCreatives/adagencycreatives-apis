<nav id="sidebar" class="sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="{{ route('dashboard.index') }}">
            <span class="align-middle me-3">

                <img src="{{ asset('assets/img/dashboard-logo.png') }}" alt="adagencycreatives" width="200" />
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
                    <li class="sidebar-item {{ request()->is('users/create') ? 'active' : '' }}">
                        <a class="sidebar-link" href="{{ route('users.create') }}">
                            <i class="align-middle" data-feather="user-plus"></i>
                            <span class="align-middle">Add New User</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</nav>
