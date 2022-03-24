<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">

    <title>{{ config('app.name') }}</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <style>
        body {font-size: 14px}
        .container {
            overflow: visible;
        }

        .col-xs-5 {width: 46.666667% }
        .col-xs-7 {
            width: 53.333333%;
        }

    </style>

    <!--[if lt IE 9]>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>-->
    <![endif]-->
</head>

<body>

<div class="container">

    @yield('content')

</div>

</body>
</html>