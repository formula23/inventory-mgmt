@extends('layouts.app')



@section('content')

    <div class="row">
        <div class="col-lg-8">

            <div class="card-box">

                <h5 class="hidden-print">Transactions ({{ $order_transactions->count() }})</h5>


                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col"></th>
                        <th scope="col">Payee</th>
                        <th scope="col">Notes</th>
                        <th scope="col">Ticket</th>
                        <th scope="col">Amount</th>
                    </tr>
                    </thead>
                    <tbody>


                        @foreach($order_transactions as $order_transaction)

                            {{--<p> --  -- {{ display_currency($sale_order->total) }} -- {{ display_currency($sale_order->balance) }}</p>--}}

                            <tr scope="row">
                                <td>{{ $order_transaction->txn_date->format('m/d/Y') }}</td>
                                <td></td>
                                <td>{{ (!is_null($order_transaction->purchase_order) ? $order_transaction->purchase_order->vendor->name : $order_transaction->sale_order->customer->name ) }}</td>
                                <td>{{ ($order_transaction->payment_method != 'Cash' ? $order_transaction->payment_method.'# '.($order_transaction->ref_number).' - '.$order_transaction->memo.' - '.display_currency($order_transaction->amount) : $order_transaction->memo ) }}</td>
                                <td><a href="{{ route((is_null($order_transaction->purchase_order)?'sale-orders.show':'purchase-orders.show'), $order_transaction->{(is_null($order_transaction->purchase_order)?'sale_order':'purchase_order')}->id) }}">{{ $order_transaction->{(is_null($order_transaction->purchase_order)?'sale_order':'purchase_order')}->ref_number }}</a></td>
                                <td>{{ ($order_transaction->payment_method != 'Cash' ? display_currency(0) : display_currency($order_transaction->amount * ($order_transaction->type == 'paid' ? -1 : 1 ) ) ) }}</td>
                            </tr>

                        @endforeach


                    </tbody>
                </table>

                {{ $order_transactions->links() }}

            </div>
        </div>
    </div>

@endsection