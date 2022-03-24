@extends('layouts.app')


@section('content')

    <h1 class="header-title">Create User</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">

                {{ Form::open(['class'=>'form-horizontal', 'url'=>route('users.store')]) }}

                @include('users._form')

                <button class="btn btn-primary waves-effect waves-light w-md" type="submit">Save</button>

                {{--<button class="btn btn-primary waves-effect waves-light w-md" type="submit">Next <i class="ion-arrow-right-c"></i> </button>--}}

                {{ Form::close() }}

            </div>
        </div>
    </div>

        {{--<div class="card-box">--}}
            {{--<h4 class="header-title m-t-0 m-b-30">Tabs Bordered</h4>--}}


        {{--</div>--}}


@endsection