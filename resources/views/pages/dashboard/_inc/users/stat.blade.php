<div class="row">
    <div class="col-12 col-sm-6 col-xxl d-flex">
        <div class="card illustration flex-fill">
            <div class="card-body p-0 d-flex flex-fill">
                <div class="row g-0 w-100">
                    <div class="col-6">
                        <div class="illustration-text p-3 m-1">
                            <h4 class="illustration-text">Welcome Back, {{ Auth()->user()->name }}</h4>
                            <p class="mb-0">{{ env('APP_NAME') }} Dashboard</p>
                        </div>
                    </div>
                    <div class="col-6 align-self-end text-end">
                        <img src="{{ asset('assets/img/customer-support.png') }}" alt="Customer Support"
                            class="img-fluid illustration-img">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xxl d-flex">
        <div class="card flex-fill">
            <div class="card-body py-4">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-2"> <span id="total_users"></span> Users </h3>
                        <p class="mb-2">Roles</p>
                        <div class="mb-0">
                            <span class="badge badge-soft-success mr-2">
                                <i class="mdi mdi-arrow-bottom-right"></i>
                                <span id="admin_users"></span> Admin
                            </span>

                            <span class="badge badge-soft-primary mr-2">
                                <i class="mdi mdi-arrow-bottom-right"></i>
                                <span id="agency_users"></span> Agency
                            </span>

                            <span class="badge badge-soft-info mr-2">
                                <i class="mdi mdi-arrow-bottom-right"></i>
                                <span id="creative_users"></span> Creative
                            </span>

                            <span class="badge badge-soft-warning mr-2">
                                <i class="mdi mdi-arrow-bottom-right"></i>
                                <span id="advisor_users"></span> Advisor
                            </span>


                        </div>
                    </div>
                    <div class="d-inline-block ml-3">
                        <div class="stat">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-users align-middle text-success">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xxl d-flex">
        <div class="card flex-fill">
            <div class="card-body py-4">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mb-2"> Active Users </h3>
                        <p class="mb-2">Status</p>
                        <div class="mb-0">
                            <span class="badge badge-soft-warning mr-2">
                                <i class="mdi mdi-arrow-bottom-right"></i>
                                <span id="pending_users"></span> Pending
                            </span>

                            <span class="badge badge-soft-success mr-2">
                                <i class="mdi mdi-arrow-bottom-right"></i>
                                <span id="active_users"></span> Active
                            </span>

                            <span class="badge badge-soft-danger mr-2">
                                <i class="mdi mdi-arrow-bottom-right"></i>
                                <span id="inactive_users"></span> Inactive
                            </span>
                        </div>
                    </div>
                    <div class="d-inline-block ml-3">
                        <div class="stat">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-users align-middle text-success">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>