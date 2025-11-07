<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Frest admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Frest admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>admin template</title>
    <meta name="base-url" content="{{ url('/') }}">
    <!-- BEGIN: CSS-->
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/calendars/tui-calendar.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/custom.css') }}">
    <link rel="icon" href="{{ asset('images/logo-icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.css') }}">

    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/pickers/daterange/daterangepicker.css')}}">
    <!-- END: CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern boxicon-layout no-card-shadow 2-columns  navbar-sticky footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

<!-- BEGIN: Header-->
<div class="header-navbar-shadow"></div>
@include('pages.admin.layouts.partials.header')
<!-- END: Header-->


<!-- BEGIN: Main Menu-->
@include('pages.admin.layouts.partials.sidebar')
<!-- END: Main Menu-->

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            @yield('content-header')
        </div>
        <div class="content-body">
            @yield('content')
        </div>
    </div>
</div>
<!-- END: Content-->

<!-- BEGIN: Footer-->
@include('pages.admin.layouts.partials.footer')
<!-- END: Footer-->
{{-- <script src="https://cdn.tailwindcss.com"></script> --}}

<!-- Scripts -->
    @stack('scripts')

</body>
<!-- END: Body-->

</html>
