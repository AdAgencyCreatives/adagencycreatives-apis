<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title') - {{ env('APP_NAME') }}</title>

{{--    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">--}}
    <link href="{{ asset('/assets/css/light.css') }}" rel="stylesheet">
    @yield('styles')
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-behavior="sticky">
    <div class="main d-flex justify-content-center w-100">
        <main class="content d-flex p-0">
            <div class="container d-flex flex-column">
                <div class="row h-100">
                    <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                        <div class="d-table-cell align-middle">

                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="{{ asset('/assets/js/app.js') }}"></script>
    @yield('alert')
    @yield('scripts')
</body>

</html>
