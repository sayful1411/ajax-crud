<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> @yield('title') </title>

    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />

    @stack('style')
</head>

<body class="sb-nav-fixed">

    @include('partials.admin.header')

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">

            @include('partials.admin.sidebar')

        </div>
        <div id="layoutSidenav_content">

            <main>
                @yield('content')
            </main>

            @include('partials.admin.footer')

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <script src="{{ asset('js/scripts.js') }}"></script>

    @stack('script')

</body>

</html>
