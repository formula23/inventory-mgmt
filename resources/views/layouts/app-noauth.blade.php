<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="57x57" href="/images/favicon/owl-ico.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/images/favicon/owl-ico.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/favicon/owl-ico.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/images/favicon/owl-ico.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/images/favicon/owl-ico.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/images/favicon/owl-ico.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/images/favicon/owl-ico.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/images/favicon/owl-ico.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/favicon/owl-ico.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/images/favicon/owl-ico.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon/owl-ico.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/images/favicon/owl-ico.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon/owl-ico.png">
    <link rel="manifest" href="/images/favicon/manifest.json">

    <title>{{ config('app.name') }}</title>

    <link href="/plugins/switchery/switchery.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/plugins/morris/morris.css">

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">--}}

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <script src="/js/plugins/modernizr.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    @yield('css')

</head>


<body class="fixed-left">

<div id="app">

    <!-- Begin page -->
    <div id="wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">

        <!-- LOGO -->
        <div class="topbar-left">
            <div class="text-center">
                <a href="{{ route('dashboard') }}" class="logo">
                    <i class="mdi mdi-owl"></i>
                </a>
            </div>
        </div>

        <!-- Button mobile view to collapse sidebar menu -->
        <nav class="navbar-custom">

            {{--<ul class="list-inline float-right mb-0">--}}

                {{--<li class="list-inline-item dropdown notification-list">--}}

                    {{--<a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="javascript:void(0);" role="button"--}}
                       {{--aria-haspopup="false" aria-expanded="false">--}}
                        {{--<img src="/images/users/avatar-0.png" alt="user" class="rounded-circle">--}}
                        {{--{{ Auth::user()->present()->first_name() }}--}}
                    {{--</a>--}}

                    {{--<div class="dropdown-menu dropdown-menu-right profile-dropdown " aria-labelledby="Preview">--}}
                        {{--<!-- item-->--}}
                        {{--<div class="dropdown-item noti-title">--}}
                            {{--<h5 class="text-overflow"><small>Welcome {{ Auth::user()->present()->first_name() }}</small> </h5>--}}
                        {{--</div>--}}

                        {{--<!-- item-->--}}
                        {{--<a href="{{ route('profile') }}" class="dropdown-item notify-item">--}}
                            {{--<i class="mdi mdi-account-star-variant"></i> <span>Profile</span>--}}
                        {{--</a>--}}

                        {{--<a href="{{ route('logout') }}" onclick="event.preventDefault();--}}
                            {{--document.getElementById('logout-form').submit();" class="dropdown-item notify-item">--}}

                            {{--<i class="mdi mdi-logout"></i> <span>Logout</span>--}}
                        {{--</a>--}}

                        {{--<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">--}}
                            {{--{{ csrf_field() }}--}}
                        {{--</form>--}}

                    {{--</div>--}}
                {{--</li>--}}

            {{--</ul>--}}

            {{--<ul class="list-inline menu-left mb-0">--}}
                {{--<li class="float-left">--}}
                    {{--<button class="button-menu-mobile open-left waves-light waves-effect">--}}
                        {{--<i class="mdi mdi-menu"></i>--}}
                    {{--</button>--}}
                {{--</li>--}}
                {{--<li class="hide-phone app-search">--}}
                    {{--<form role="search" class="" method="get" action="{{ route('search') }}">--}}
                        {{--<input type="text" name="q" placeholder="Search..." class="form-control">--}}
                        {{--<a href=""><i class="fa fa-search"></i></a>--}}
                    {{--</form>--}}
                {{--</li>--}}
            {{--</ul>--}}

        </nav>

    </div>
    <!-- Top Bar End -->

    <!-- ========== Left Sidebar Start ========== -->

{{--    @include('layouts.partials.nav')--}}

    <!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page" style="margin-left:0">

        <div class="content">
            <div id="page-{{ Str::slug($title) }}" class="container">

                <!-- Page-Title -->
                <div class="row hidden-print">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">{{ $title }}</h4>
                            <ol class="breadcrumb float-right">
                                {{--<li class="breadcrumb-item"><a href="{{ URL::previous() }}">&laquo; Back</a></li>--}}
                                {{--<li class="breadcrumb-item active">Back</li>--}}
                            </ol>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                @include('flash::message')

                @if($errors->all())
                <div class="alert alert-danger" role="alert">
                    <h5 class="alert-heading">Error</h5>
                    {{ Html::ul($errors->all()) }}
                </div>
                @endif

                @if($warnings->all())
                    <div class="alert alert-warning" role="alert">
                        <h5 class="alert-heading">Notice</h5>
                        {{ Html::ul($warnings->all()) }}
                    </div>
                @endif

                <!-- Start content -->
                @yield('content')
                <!-- end content -->

            </div>
        </div>


        @include('layouts.partials.footer')

    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->


    <!-- Right Sidebar -->
    {{--@include('layouts.partials.notifications')--}}
    <!-- /Right-bar -->

</div>
    <!-- END wrapper -->

</div>


<script>
    var resizefunc = [];
</script>

<script src="{{ mix('js/app.js') }}"></script>

<script src="{{ asset('plugins/switchery/switchery.min.js') }}"></script>
<script src="{{ asset('js/plugins/wow.min.js') }}"></script>

<script src="{{ asset('js/plugins/tether.min.js') }}"></script>
<script src="{{ asset('js/plugins/bootstrap.min.js') }}"></script>


<script src="{{ asset('js/plugins/plugins.js') }}"></script>
<script src="{{ asset('js/plugins/fastclick.js') }}"></script>


<!-- Custom main Js -->
<script src="{{ asset('js/jquery.core.js') }}"></script>
<script src="{{ asset('js/jquery.app.js') }}"></script>

@yield('js')

</body>
</html>