@extends('layouts.app-noauth')

@section('css')
<style>

    .batch_cost_show {
        display: flex !important;
    }

</style>
@endsection

@section('content')

    <div class="container col-lg-4">

    <style>
        label { font-size: 2rem;}
        input.form-control, select.form-control { font-size: 3rem;}
        button.btn, a.btn { font-size: 3rem;display: block; width: 100%;}
    </style>

    <h2>Welcome, {{ Auth::user()->name }}!</h2>
    <hr>
    @if($batch)

        <dl class="row" style="font-size: 1.5em">
            <dt class="col-4">Purchase Date:</dt>
            <dd class="col-8">{{ ($batch->purchase_order?$batch->purchase_order->txn_date->format('m/d/Y'):"--") }}</dd>

            <dt class="col-4">Strain:</dt>
            <dd class="col-8">
                @if($batch->description)
                {{ $batch->description }} <i>({{ $batch->name }})</i>
                @else
                    {{ $batch->name }}
                @endif
            </dd>
            <dt class="col-4">Batch Size:</dt>
            <dd class="col-8">{{ $batch->units_purchased }} {{ $batch->uom }}</dd>

            <dt class="col-4">Available:</dt>
            <dd class="col-8">{{ $batch->inventory }} {{ $batch->uom }}</dd>

            <dt class="col-4">Pending Sale Orders:</dt>
            <dd class="col-8">{{ $batch->order_details_cog->sum('units') }} {{ $batch->uom }}</dd>

            <dt class="col-4">Vault Log Total:</dt>
            <dd class="col-8">{{ abs($batch->vault_logs->sum('quantity')) }} {{ $batch->uom }}</dd>

        </dl>

        <a href="javascript:void(0)" style="font-size: 2em" id="batch_cost_toggle_view">Show Info</a>

        <dl class="row" style="font-size: 1.5em; display:none;" id="batch_cost">
            <dt class="col-4">Vendor:</dt>
            <dd class="col-8">{{ ($batch->purchase_order?$batch->purchase_order->vendor->name:"--") }}</dd>

            <dt class="col-4">Cost:</dt>
            <dd class="col-8">{{ display_currency($batch->preTaxCost) }} <small><i>({{ display_currency($batch->unit_price) }})</i></small></dd>

        </dl>

            <div class="card-box">

            {{ Form::open(['class'=>'form-horizontal', 'url'=>route('vault-logs.store')]) }}

                {{ Form::hidden('batch_id', $batch->id) }}

                <div class="form-group">

                    <select id="broker" name="broker_id" class="form-control" style="height: 5rem;"@if(session()->has('broker_id')) disabled @endif>
                        <option value="">- Select Broker -</option>
                        <option value="new">< Create New ></option>
                        @foreach($brokers as $broker_id=>$broker_name)
                            <option value="{{ $broker_id }}"{{ ( session()->has('broker_id') ? ($broker_id == session('broker_id') ? 'selected' : '' ) : '') }} >{{$broker_name}}</option>
                        @endforeach
                    </select>

                    <div class="m-t-15">
                        <input type="text" class="form-control " id="new_broker" name="new_broker" placeholder="New Broker" style="display: none;">
                    </div>

                    {{--<label for="order_title">Order Title</label>--}}
                    {{--<input placeholder="Order Title" type="text" class="form-control " id="order_title" name="order_title" value="{{ session('order_title') }}" required="required" {{ (session('order_title')?"disabled":"") }}>--}}

                    @if(session()->has('broker_id'))
                        {{ Form::hidden('broker_id', session('broker_id')) }}
                    @endif
                </div>

                <div class="form-group">
                    <input type="text" class="form-control " id="notes" name="notes"placeholder="Notes">
                </div>

                <div class="form-group">
                    {{--<label for="strain_name">Different Strain Name:</label>--}}
                    <input type="text" class="form-control " id="strain_name" name="strain_name" value="" placeholder="Alt. Strain Name">
                </div>

                <div class="form-group">

                    <div class="input-group bootstrap-touchspin">
                        <span class="input-group-addon bootstrap-touchspin-prefix" style="font-size: 3em;">$</span>
                        <input id="price" type="number" value="" name="price" class="form-control" style="display: block;" placeholder="Sugg. Price" step="0.01">
                        <span class="input-group-addon bootstrap-touchspin-postfix" style="font-size: 3em;">.00</span>
                    </div>
                </div>

                <div class="form-group">
                    {{--<label for="quantity">Qty</label>--}}
                    <input type="number" class="form-control " id="quantity" name="quantity" required="required" placeholder="Quantity" step="0.0001">
                </div>

            <div class="form-group">
                <select name="in_out" id="in_out" class="form-control" style="height: 5rem;">
                    <option value="out" {{ (session('in_out')=="out"?"selected":"") }}>Out</option>
                    <option value="in"{{ (session('in_out')=="in"?"selected":"") }}>Return</option>
                </select>
            </div>

                <button type="submit" class="btn btn-primary" style="">Save</button>

                {{ Form::close() }}
            </div>

    @endif

    @if($vault_logs->count())

            <div class="card-box">

                @if(session('in_out')=='in')
                <h2 class="text-danger">RETURN</h2>
                @endif
                <h2>Broker: {{ $broker->name }}</h2>

                <table id="table" class="table" style="font-size: 1.25rem">
                    <thead>
                    <tr>
                        <th>Strain</th>
                        <th>Quantity</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($vault_logs as $vault_log)
                        <tr>

                            <td>
                                {!!  $vault_log->present()->strain_notes_price("<br>", display_currency($vault_log->price))  !!}

                            </td>
                            <td>{{ $vault_log->quantity }} lb</td>
                            <td>
                                <form action="{{ route('vault-logs.destroy', $vault_log->id) }}" method="post">
                                    {{--<input class="btn btn-default" type="submit" value="Delete" />--}}
                                    <button type="submit" class="btn btn-danger waves-effect waves-light" onclick="return confirm('Are you sure you want to delete this item?')">X</button>
                                    @method('delete')
                                    @csrf
                                </form>
                            </td>
                        </tr>

                    @endforeach

                    </tbody>

                </table>

                {{ Form::open(['class'=>'form-horizontal', 'url'=>route('vault-logs.complete')]) }}

                    <button type="submit" class="btn btn-primary" style="">Complete</button>

                {{ Form::close() }}

                {{--<a href="{{ route('logout') }}" onclick="event.preventDefault();--}}
        {{--document.getElementById('logout-form').submit();" class="btn btn-primary">--}}

                    {{--<i class="mdi mdi-logout"></i> <span>Complete</span>--}}
                {{--</a>--}}

                {{--<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">--}}
                    {{--{{ csrf_field() }}--}}
                {{--</form>--}}


            </div>

    @endif

    </div>

@endsection

@section('js')

    <script type="text/javascript">
        $(document).ready(function() {

            $("#broker").change(function() {
                if($(this).val()=='new') {
                    $('#new_broker').show();
                } else {
                    $('#new_broker').hide();
                }
            });

            $('#batch_cost_toggle_view').click(function() {

                $('#batch_cost').toggleClass('batch_cost_show')

                $(this).text($(this).text() == "Show Info" ? "Hide Info" : "Show Info");
                // $(this).text(
                //     text == "Show Background" ? "Show Text" : "Show Background");

            });


        });

    </script>

@endsection