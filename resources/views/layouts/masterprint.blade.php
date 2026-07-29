<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="light"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-body-image="none">

<head>
    <meta charset="utf-8">
    <title>@yield('title')  </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="SISDATO - Sistema dado para todos" name="description">
    <meta content=wwwww name="author">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}">

    <script
        src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="
        crossorigin="anonymous"></script>

    <!-- head css -->
    @include('layouts.head-css')
</head>

<body>

    <div id="layout-wrapper" style="width: 100% !important;">
        <div class="main-content" style="width: 100% !important; margin-left: unset;">
            <div class="page-content" style="width: 100% !important; padding: 20px !important;">
                <div class="container-fluid" style="width: 100% !important;">
                    @yield('content')
                </div>
        </div>
    </div>

</body>

</html>
