@extends('layouts.app')


@section('content')

    <style>
        body {
            color:#000000;
        }

    </style>

        <div class="row" id="invoice">
            <div class="col-12">
                <div class="hidden-print clearfix mb-2">
                    <div class="pull-left">
                        <a href="javascript:window.history.back();" class="btn btn-dark waves-effect waves-light">&langle; Back</a>
                    </div>
                    <div class="pull-right">
                        <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>

                        {{--<a href="#" class="btn btn-primary waves-effect waves-light">Submit</a>--}}
                    </div>
                </div>
                <div class="card-box" id="po">

                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-6 col-md-6">
                                <div class="">
                                    {{--<img src="/images/highline-200.png" />--}}
                                    <h5>
                                        {{ config('highline.license_name') }}
                                    </h5>
                                    <h6>{{ config('highline.license.address') }}<br>{{ config('highline.license.address2') }}</h6>
                                    <p>Adult-Use/Medicinal#<br>{{ config('highline.license_number_adult') }}</p>
                                </div>
                            </div>



                            <div class="col-sm-6 col-md-6">
                                <div class="pull-right text-right">

                                    <p style="">Powered by:<br><img class="img-responsive hl-logo" src="/images/highline-200.png" /></p>
                                    {{--<p class="highline-delivered"><strong>Premium Cannabis Delivered With Care<sup>TM</sup></strong></p>--}}

                                </div>
                            </div>
                        </div>

                        <h4 class="pull-left">Purchase Order</h4>
                        <h6 class="pull-right">#<strong>{{ $purchaseOrder->ref_number }}</strong></h6>
                        <div class="clearfix"></div>
                        <hr>

                        <div class="row">

                            @if($purchaseOrder->balance==0)
                            <div class="paid-stamp">
                                <img src="/images/paid-stamp.png" width="140px" style="position: absolute; left: 50%; top: 165px; margin-left: -70px">
                            </div>
                            @endif

                            <div class="col-12">

                                <div class="row">

                                    <div class="col-4">
                                        <span>Vendor:</span>
                                        <address>
                                            <h5>{{ $purchaseOrder->vendor->name }}</h5>
                                            <p>{{ $purchaseOrder->vendor->details['address'] }}<br>
                                            {{ $purchaseOrder->vendor->details['address2'] }}<br>
                                            Lic#<strong>
                                            @if(stristr($purchaseOrder->customer_type, 'microbusiness'))

                                                {{ $purchaseOrder->vendor->details['mb_license_number'] }}

                                            @elseif(stristr($purchaseOrder->customer_type, 'cultivator'))

                                                @if( ! empty($purchaseOrder->vendor->details['cult_rec_license_number']))
                                                    {{ $purchaseOrder->vendor->details['cult_rec_license_number'] }}
                                                @elseif(!empty($purchaseOrder->vendor->details['cult_med_license_number']))
                                                    {{ $purchaseOrder->vendor->details['cult_med_license_number'] }}
                                                @endif

                                            @else

                                                @if( ! empty($purchaseOrder->vendor->details['distro_rec_license_number']))
                                                    {{ $purchaseOrder->vendor->details['distro_rec_license_number'] }}
                                                @elseif(!empty($purchaseOrder->vendor->details['distro_med_license_number']))
                                                    {{ $purchaseOrder->vendor->details['distro_med_license_number'] }}
                                                @endif

                                            @endif</strong>
                                            </p>
                                        </address>
                                    </div>
                                    <div class="col-4">
                                        <span>Ship To:</span>
                                        <address>
                                            <h5>{{ config('highline.license.legal_name') }}</h5>
                                            <p>{{ config('highline.license.address') }}<br>{{ config('highline.license.address2') }}
                                                <br>Lic# <strong>{{ config('highline.license_number_adult') }}</strong></p>
                                        </address>
                                    </div>
                                    <div class="col-4">
                                        <div class="pull-right">
                                        <p><strong>PO Status: </strong> <span class="badge badge-{{ ( ($purchaseOrder->balance > 0) ? 'success' : 'danger' ) }}">{{ ( ($purchaseOrder->balance > 0) ? 'Open' : 'Paid' ) }}</span></p>
                                        <p><strong>PO Date: </strong> {{ $purchaseOrder->txn_date->format('M d, Y') }}</p>
                                        <p><strong>License Type:</strong> {{ ucfirst($purchaseOrder->customer_type) }}</p>



                                            {{--<h6 class="balance">--}}
                                            {{--<strong>Balance: </strong> {{ display_currency($purchaseOrder->balance) }}--}}
                                        {{--</h6>--}}
                                        {{--<h6>--}}
                                            {{--<strong>Terms:</strong> {{ (!empty($purchaseOrder->vendor->details['terms']) ? config('highline.payment_terms')[$purchaseOrder->vendor->details['terms']] : 'Due on Receipt' ) }}--}}
                                        {{--</h6>--}}
                                        {{--<h6>--}}
                                            {{--<strong>Due Date: </strong>--}}
                                            {{--@if($purchaseOrder->due_date)--}}
                                                {{--{{ $purchaseOrder->due_date->format(config('highline.date_format')) }}--}}
                                            {{--@else--}}
                                                {{--{{ $purchaseOrder->txn_date->addDays((!empty($purchaseOrder->vendor->details['terms']) ? $purchaseOrder->vendor->details['terms'] : 0 ))->format('m/d/Y') }}--}}
                                            {{--@endif--}}
                                        {{--</h6>--}}
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="m-h-10"></div>

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table m-t-30 table-hover table-striped">
                                        <thead>
                                        <tr>

                                            <th>Batch#</th>
                                            <th>Metrc/UID</th>
                                            <th>Name</th>
                                            <th>Qty / Unit Cost</th>
                                            <th>Subtotal</th>
                                            <th>Cult Tax Collected</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($purchaseOrder->batches as $batch)
                                            <tr>

                                                <td>{{ $batch->batch_number?:"N/A" }}</td>
                                                <td>{{ $batch->ref_number }}</td>
                                                <td>
                                                    @if($batch->brand) <strong>{{ $batch->brand->name }}</strong><br> @endif
                                                    {{ $batch->category->name }}: {{ $batch->name }}
                                                </td>
                                                <td>
                                                    {{ $batch->units_purchased }} {{ $batch->uom }} @ {{ display_currency($batch->unit_price) }}

                                                </td>
                                                <td>{{ display_currency($batch->subtotal_price) }}</td>
                                                <td>{{ display_currency($batch->tax) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row m-t-40">
                            {{--<div class="col">--}}
                                {{--<div class="clearfix">--}}
                                    {{--<h4 class="small text-inverse"><strong>PAYMENT TERMS AND POLICIES</strong></h4>--}}
                                    {{--<p><small>Excise tax to be <strong>paid in-full</strong> on delivery.<br>To be paid by check or cash.</small></p>--}}
                                    {{--<p><strong>Checks Made Payable To:</strong><br>--}}
                                    {{--Highline Distribution, Inc.<br>--}}
                                    {{--5042 Venice Blvd.<br>--}}
                                    {{--Los Angeles, CA 90019</p>--}}

                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="col-12">

                                <p class="text-right"><b>Subtotal:</b> {{ display_currency($purchaseOrder->subtotal) }}</p>

                                @if($purchaseOrder->discount)
                                <p class="text-right"><b>{{ $purchaseOrder->discount_description }}:</b> <span class="text-danger">({{ display_currency($purchaseOrder->discount) }})</span></p>

                                <p class="text-right"><b>Subtotal after discount:</b> {{ display_currency($purchaseOrder->subtotal - $purchaseOrder->discount) }}</p>
                                @endif

                                {{--<p class="text-right"><b>LA City 1% Transportation Tax:</b> {{ display_currency($saleOrder->transpo_tax) }}</p>--}}
                                {{----}}
                                <hr>

                                <div class="row">
                                    <div class="col-4">

                                        @if($purchaseOrder->tax > 0)
                                            <p><strong><i>*** The buyer {{ config('highline.license_name') }} is responsible for remitting the cultivation tax on behalf of the vendor ({{ $purchaseOrder->vendor->business_name?:$purchaseOrder->vendor->name }}). ***</i></strong></p>
                                        @else
                                            <p><strong><i>*** The vendor ({{ $purchaseOrder->vendor->business_name?:$purchaseOrder->vendor->name }}) is responsible for remitting the cultivation tax on behalf of the originating cultivator. ***</i></strong></p>
                                        @endif

                                    </div>
                                    <div class="col-8 pull-right">
                                        <h5 class="text-right">Total: {{ display_currency($purchaseOrder->total) }}</h5>

                                        @if($purchaseOrder->total - $purchaseOrder->balance)
                                            <h5 class="text-right">Applied Payments: {{ display_currency($purchaseOrder->total - $purchaseOrder->balance) }}</h5>
                                            <h5 class="text-right">Balance: {{ display_currency($purchaseOrder->balance) }}</h5>

                                        @endif
                                    </div>

                                </div>

                            </div>
                        </div>

                        @if($purchaseOrder->transactions->count())
                        <div class="col-12 visible-print-block">

                            <h6>Applied Payments</h6>

                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Ref#</th>
                                        <th>Memo</th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach($purchaseOrder->transactions as $transaction)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $transaction->txn_date() }}</td>
                                            <td>{{ display_currency($transaction->amount) }}</td>
                                            <td>{{ $transaction->payment_method }}</td>
                                            <td>{{ $transaction->ref_number }}</td>
                                            <td>{{ $transaction->memo }}</td>
                                        </tr>

                                    @endforeach

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>{{ display_currency($purchaseOrder->transactions->sum('amount')) }}</td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                        <div class="clearfix"></div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

@endsection
