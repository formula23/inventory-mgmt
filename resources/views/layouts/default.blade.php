<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="">

    {{--<link rel="shortcut icon" href="/images/favicon_1.ico">--}}

    <title>{{ config('app.name') }}</title>

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

    {{--<link href="/plugins/switchery/switchery.min.css" rel="stylesheet" />--}}

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <style>
        html,body { background: #fff; }

    </style>

</head>
<body>

<div class="wrapper-page">

    <div class="text-center m-b-50">
        {{--<a href="{{ route('dashboard') }}" class="logo-lg"><span>{{ config('app.name') }}</span> </a>--}}
        {{--<img class="img-responsive" src="/images/highline.png" width="260" />--}}
        <h1 class="display-1"><i class="mdi mdi-cube-send display-1"></i></h1>
    </div>
{{--<hr>--}}
    {{--<div class="text-center"><strong>Track-N-Trace Inventory</strong></div>--}}
    {{--<div class="text-center"><strong>Inventory</strong></div>--}}

    @yield('content')

</div>

</body>
</html>