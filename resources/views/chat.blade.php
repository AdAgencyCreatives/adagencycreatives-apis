@extends('layouts.app')

@section('title', 'Dashboard')

@section('scripts')
<script>
import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'd9b58fc72205bf407bc0',
    cluster: 'mt1',
    encrypted: true,
});
</script>
@endsection
@section('content')

<div class="card">
    <div class="row g-0">
        <div class="col-12 col-lg-5 col-xl-3 border-end list-group">

            <div class="px-4 d-none d-md-block">
                <div class="d-flex align-items-start align-items-center">
                    <div class="flex-grow-1">
                        <input type="text" class="form-control my-3" placeholder="Search...">
                    </div>
                </div>
            </div>

            <a href="#" class="list-group-item list-group-item-action border-0">
                <div class="badge bg-success float-end">5</div>
                <div class="d-flex align-items-start">

                    <div class="flex-grow-1 ms-3">
                        Ashley Briggs
                        <div class="small"><span class="fas fa-circle chat-online"></span> Online</div>
                    </div>
                </div>
            </a>


            <hr class="d-block d-lg-none mt-1 mb-0">
        </div>
        <div class="col-12 col-lg-7 col-xl-9">
            <div class="py-2 px-4 border-bottom d-none d-lg-block">
                <div class="d-flex align-items-start align-items-center py-1">
                    <div class="position-relative">

                    </div>
                    <div class="flex-grow-1 ps-3">
                        <strong>Bertha Martin</strong>
                        <div class="text-muted small"><em>Typing...</em></div>
                    </div>
                    <div>
                        <button class="btn btn-primary btn-lg me-1 px-3"><svg xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-phone feather-lg">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                </path>
                            </svg></button>
                        <button class="btn btn-info btn-lg me-1 px-3 d-none d-md-inline-block"><svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-video feather-lg">
                                <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                            </svg></button>
                        <button class="btn btn-light border btn-lg px-3"><svg xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-more-horizontal feather-lg">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="19" cy="12" r="1"></circle>
                                <circle cx="5" cy="12" r="1"></circle>
                            </svg></button>
                    </div>
                </div>
            </div>

            <div class="position-relative">
                <div class="chat-messages p-4">

                    <div class="chat-message-right pb-4">
                        <div>
                            <div class="text-muted small text-nowrap mt-2">2:33 am</div>
                        </div>
                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 me-3">
                            <div class="fw-bold mb-1">You</div>
                            Lorem ipsum dolor sit amet, vis erat denique in, dicunt prodesset te vix.
                        </div>
                    </div>

                    <div class="chat-message-left pb-4">
                        <div>

                            <div class="text-muted small text-nowrap mt-2">2:34 am</div>
                        </div>
                        <div class="flex-shrink-1 bg-light rounded py-2 px-3 ms-3">
                            <div class="fw-bold mb-1">Bertha Martin</div>
                            Sit meis deleniti eu, pri vidit meliore docendi ut, an eum erat animal commodo.
                        </div>
                    </div>


                </div>
            </div>

            <div class="flex-grow-0 py-3 px-4 border-top">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Type your message">
                    <button class="btn btn-primary">Send</button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection