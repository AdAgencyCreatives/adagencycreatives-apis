<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title') - {{ env('APP_NAME') }}</title>

     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link class="js-stylesheet" href="{{ asset('assets/css/light.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    @yield('styles')
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-behavior="sticky">


<div class="wrapper">

    @include('includes.aside')

    <div class="main" >
        @include('includes.header')

        <main class="content">
            <div class="container-fluid p-0">

                @yield('content')
            </div>
        </main>

        @include('includes.footer')
    </div>
</div>


<script src="{{ asset('/assets/js/app.js') }}"></script>

{{--<script src="{{ mix('/js/app.js') }}"></script>--}}

<script>
    $(".select2").each(function () {
        $(this).select2();
    })
</script>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.2/sweetalert2.all.min.js"></script>
<script src="{{ asset('assets/js/alerts.js') }}"></script>

@yield('alert')
@yield('scripts')

</body>

</html>
