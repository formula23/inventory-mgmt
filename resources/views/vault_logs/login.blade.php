@extends('layouts.app-noauth')


@section('content')
    <style>
        label { font-size: 2rem;}
        input.form-control, select.form-control { font-size: 3rem;}
        button.btn, a.btn { font-size: 3rem;display: block; width: 100%;}
    </style>
    <h1>Who are you?</h1>

    {{ Form::open(['class'=>'form-horizontal', 'url'=>route('vault-logs.force-login', request('ref_number'))]) }}

    {{ Form::select("user_id", $users, 14, ['class'=>'form-control', 'style'=>'height: 7rem']) }}
<br>
    {{ Form::password("pin", ['class'=>'form-control', 'style'=>'height: 7rem', 'placeholder'=>'PIN']) }}

    <br>
    <button type="submit" class="btn btn-primary waves-effect waves-light" >Start</button>

    {{ Form::close() }}

@endsection