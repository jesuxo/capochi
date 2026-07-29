<!doctype html>
<html lang="en" data-bs-theme="light" data-footer="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="SISDATO - Sistema dado para todos" name="description">
    <meta content="Themesbrand" name="author">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}">


    <title>https://ww.capochi.com - SISDATO</title>
    <meta name="description" content="CAPOCHI -  Carne Pollo y Cochino">
    <link rel="canonical" href="https://www.capochi.com">
    <meta property="og:title" content="https://www.capochi.com - SISDATO">
    <meta property="og:description" content=" CAPOCHI -  Carne Pollo y Cochino ">
    <meta property="og:type" content="WebPage">
    <meta property="og:image" content="https://capochi.com/build/images/logo.png">
    <meta property="og:url" content="https://capochi.com">

    <meta name="twitter:title" content="https://www.capochi.com - SISDATO ">
    <meta name="twitter:description" content=" CAPOCHI -  Carne Pollo y Cochino ">
    <meta name="twitter:site" content="@capochica">
    <script type="application/ld+json">{"@context":"https://schema.org","@type":"WebPage","name":"CAPOCHI, C.A.","description":"CARNE, POLLO Y COCHINO"}</script>

    <!-- head css -->
    @include('layouts.head-css')
</head>

<body>

    <section
        class="auth-page-wrapper position-relative bg-light min-vh-100 d-flex align-items-center justify-content-between">


        <!--content here-->
        @yield('content')
    </section>

    <!--script-->
    @include('layouts.vendor-scripts')
</body>

</html>
