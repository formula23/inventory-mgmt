@extends('layouts.app')



@section('content')

    <div class="row">
        <div class="col-lg-12">

            <div class="card-box">

                <h5 class="hidden-print">Total Payables: {{ display_currency($vendors->sum('outstanding_balance')) }}</h5>

                @foreach($vendors as $vendor)

                    <div class="card m-b-20">
                        <div class="card-header collapsed" id="heading-{{ $vendor->id }}" data-toggle="collapse" data-target="#collapse-{{ $vendor->id }}" style="cursor: pointer">
                        {{--<div class="card-header" id="heading-{{ $vendor->id }}" >--}}
                            <h5>{{ $vendor->name }}: {{ display_currency($vendor->outstanding_balance) }}</h5>
                        </div>


                        <div id="collapse-{{ $vendor->id }}" class="card-block collapse">

                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">PO Date</th>
                                    <th scope="col">PO #</th>
                                    <th scope="col">Aging</th>
                                    <th scope="col">Orig. Amount</th>
                                    <th scope="col">Balance</th>
                                    <th scope="col">Payments</th>
                                </tr>
                                </thead>
                                <tbody>


                                    @foreach($vendor->purchase_orders as $purchase_order)

                                        {{--<p> --  -- {{ display_currency($sale_order->total) }} -- {{ display_currency($sale_order->balance) }}</p>--}}

                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $purchase_order->txn_date->format('m/d/Y') }}</td>
                                            <td><a href="{{ route('purchase-orders.show', $purchase_order->id) }}">{{ $purchase_order->ref_number }}</a></td>
                                            <td>{{ $purchase_order->txn_date->diffForHumans() }}</td>
                                            <td>{{ display_currency($purchase_order->total) }}</td>
                                            <td>{{ display_currency($purchase_order->balance) }}</td>
                                            <td><a href="{{ route('purchase-orders.show', $purchase_order->id) }}">{{ display_currency($purchase_order->transactions->sum('amount')) }} ({{ $purchase_order->transactions->count() }})</a></td>
                                        </tr>

                                    @endforeach


                                </tbody>
                            </table>


                        </div>
                    </div>

                @endforeach

            </div>
        </div>
    </div>

@endsection