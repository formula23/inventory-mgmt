<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="">

    {{--<link rel="shortcut icon" href="/images/favicon_1.ico">--}}

    <title>{{ config('app.name') }}</title>

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
        <h1 class="display-1"><i class="mdi mdi-owl display-1"></i></h1>
    </div>
{{--<hr>--}}
    {{--<div class="text-center"><strong>Track-N-Trace Inventory</strong></div>--}}
    {{--<div class="text-center"><strong>Inventory</strong></div>--}}

    @yield('content')

</div>

</body>
</html>