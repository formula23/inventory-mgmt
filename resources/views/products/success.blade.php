@extends('layouts.app')


@section('js')

    <script type="application/javascript">
        window.setTimeout(function() {
            window.location.href = '/';
        }, 2000);

    </script>


@endsection