@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-10 col-xl-8 mx-auto">


            <div class="tab-content">
                <div class="tab-pane fade show active" id="monthly" role="tabpanel">
                    <div class="row py-4">
                        <div class="col-sm-4 mb-3 mb-md-0">
                            <div class="card text-center h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-4">
                                        <h5>Post a Creative Job</h5>
                                        <span class="display-4">$149</span>
                                    </div>
                                    <h6>Includes:</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            1 users
                                        </li>
                                        <li class="mb-2">
                                            5 projects
                                        </li>
                                        <li class="mb-2">
                                            5 GB storage
                                        </li>
                                    </ul>
                                    <div class="mt-auto">
                                        <a href="https://buy.stripe.com/test_3cseVIbU60qg3aU28b?prefilled_email={{ auth()->user()->email }}"
                                            class="btn btn-lg btn-outline-primary">Buy</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3 mb-md-0">
                            <div class="card text-center h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-4">
                                        <h5>Multiple Creative Jobs</h5>
                                        <span class="display-4">$349</span>
                                        <span>/mo</span>
                                    </div>
                                    <h6>Includes:</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            5 users
                                        </li>
                                        <li class="mb-2">
                                            50 projects
                                        </li>
                                        <li class="mb-2">
                                            50 GB storage
                                        </li>
                                        <li class="mb-2">
                                            Security policy
                                        </li>
                                        <li class="mb-2">
                                            Weekly backups
                                        </li>
                                    </ul>
                                    <div class="mt-auto">
                                        <a href="{{ route('plans.show', 'multiple-creative-jobs') }}"
                                            class="btn btn-lg btn-primary">Buy</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3 mb-md-0">
                            <div class="card text-center h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-4">
                                        <h5>Premium Creative Jobs</h5>
                                        <span class="display-4">$649</span>
                                        <span>/mo</span>
                                    </div>
                                    <h6>Includes:</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            Unlimited users
                                        </li>
                                        <li class="mb-2">
                                            Unlimited projects
                                        </li>
                                        <li class="mb-2">
                                            250 GB storage
                                        </li>
                                        <li class="mb-2">
                                            Priority support
                                        </li>
                                        <li class="mb-2">
                                            Security policy
                                        </li>
                                        <li class="mb-2">
                                            Daily backups
                                        </li>
                                        <li class="mb-2">
                                            Custom CSS
                                        </li>
                                    </ul>
                                    <div class="mt-auto">
                                        <a href="{{ route('plans.show', 'premium-creative-jobs') }}"
                                            class="btn btn-lg btn-outline-primary">Buy</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="annual" role="tabpanel">
                    <div class="row py-4">
                        <div class="col-sm-4 mb-3 mb-md-0">
                            <div class="card text-center h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-4">
                                        <h5>Free</h5>
                                        <span class="display-4">$0</span>
                                    </div>
                                    <h6>Includes:</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            1 users
                                        </li>
                                        <li class="mb-2">
                                            5 projects
                                        </li>
                                        <li class="mb-2">
                                            5 GB storage
                                        </li>
                                    </ul>
                                    <div class="mt-auto">
                                        <a href="#" class="btn btn-lg btn-outline-primary">Sign up</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3 mb-md-0">
                            <div class="card text-center h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-4">
                                        <h5>Standard</h5>
                                        <span class="display-4">$199</span>
                                        <span class="text-small4">/mo</span>
                                    </div>
                                    <h6>Includes:</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            5 users
                                        </li>
                                        <li class="mb-2">
                                            50 projects
                                        </li>
                                        <li class="mb-2">
                                            50 GB storage
                                        </li>
                                        <li class="mb-2">
                                            Security policy
                                        </li>
                                        <li class="mb-2">
                                            Weekly backups
                                        </li>
                                    </ul>
                                    <div class="mt-auto">
                                        <a href="#" class="btn btn-lg btn-primary">Try it for free</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3 mb-md-0">
                            <div class="card text-center h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-4">
                                        <h5>Plus</h5>
                                        <span class="display-4">$399</span>
                                        <span>/mo</span>
                                    </div>
                                    <h6>Includes:</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            Unlimited users
                                        </li>
                                        <li class="mb-2">
                                            Unlimited projects
                                        </li>
                                        <li class="mb-2">
                                            250 GB storage
                                        </li>
                                        <li class="mb-2">
                                            Priority support
                                        </li>
                                        <li class="mb-2">
                                            Security policy
                                        </li>
                                        <li class="mb-2">
                                            Daily backups
                                        </li>
                                        <li class="mb-2">
                                            Custom CSS
                                        </li>
                                    </ul>
                                    <div class="mt-auto">
                                        <a href="#" class="btn btn-lg btn-outline-primary">Try it for free</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
