<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        body {
            color:#000000;
        }

    </style>

        <div class="row" id="invoice">
            <div class="col-12">
                <div class="hidden-print clearfix mb-2">
                    <div class="text-right">
                        <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>

                        {{--<a href="#" class="btn btn-primary waves-effect waves-light">Submit</a>--}}
                    </div>
                </div>
                <div class="card-box">
                    <!-- <div class="panel-heading">
                        <h4>Invoice</h4>
                    </div> -->

                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-6 col-md-6">
                                <div class="">
                                    {{--<img src="/images/highline-200.png" />--}}
                                    <h5>
                                        {{ config('highline.license_name') }}
                                    </h5>
                                    <h6>{{ config('highline.license.address') }}<br>{{ config('highline.license.address2') }}</h6>
                                    <p><strong>License#</strong> {{ config('highline.license.adult') }}</p>
                                </div>
                            </div>



                            <div class="col-sm-6 col-md-6">
                                <div class="pull-right text-right">

                                    <p style="">Powered by:<br><img class="img-responsive hl-logo" src="{{ public_path() }}/images/highline-200.png" /></p>
                                    {{--<p class="highline-delivered"><strong>Premium Cannabis Delivered With Care<sup>TM</sup></strong></p>--}}

                                    {{--<p class="invoice_num">Invoice#--}}
                                        {{--<strong>{{ $saleOrder->ref_number }}</strong>--}}
                                    {{--</p>--}}
                                </div>
                            </div>
                        </div>

                        <h4 class="pull-left">Invoice</h4>
                        <h6 class="pull-right">#<strong>{{ $saleOrder->ref_number }}</strong></h6>
                        <div class="clearfix"></div>

                        <hr>

                        <div class="row">

                            {{ $saleOrder->balance }}
                            @if($saleOrder->balance == 0)
                            <div class="paid-stamp">
                                <img src="{{ public_path() }}/images/paid-stamp.png" width="140px" style="position: absolute; left: 50%; top: 165px; margin-left: -70px">
                            </div>
                            @endif

                            <div class="col-12">
                                <div class="pull-left m-t-30">
                                    <span>Bill To:</span>
                                    <address>
                                        <h6>{{ $saleOrder->customer->name }}</h6>
                                        @if(!empty($saleOrder->customer->details['business_name']) && $saleOrder->customer->name != $saleOrder->customer->details['business_name'])
                                            <h6>{{ ($saleOrder->customer->details['business_name']) }}</h6>
                                        @endif
                                        <p>{{ $saleOrder->customer->details['address'] }}<br>
                                            {{ $saleOrder->customer->details['address2'] }}</p>
                                        @if($saleOrder->customer_type == 'distributor')
                                            <p>
                                                @if($saleOrder->customer->details['distro_rec_license_number'])
                                                    <strong>License#</strong> {{ $saleOrder->customer->details['distro_rec_license_number'] }}
                                                @elseif($saleOrder->customer->details['distro_med_license_number'])
                                                    <strong>License#</strong> {{ $saleOrder->customer->details['distro_med_license_number'] }}<br>
                                                @endif
                                            </p>
                                            @elseif($saleOrder->customer_type == 'micros business' || $saleOrder->customer_type == 'micros business distributor')
                                                @if(!empty($saleOrder->customer->details['mb_license_number']))
                                                    <strong>License#</strong> {{ $saleOrder->customer->details['mb_license_number'] }}
                                                @endif
                                            @else
                                            <p>
                                                @if($saleOrder->customer->details['rec_license_number'])
                                                <strong>License#</strong> {{ $saleOrder->customer->details['rec_license_number'] }}
                                                @elseif($saleOrder->customer->details['med_license_number'])
                                                <strong>License#</strong> {{ $saleOrder->customer->details['med_license_number'] }}<br>
                                                @endif
                                            </p>
                                            @endif

                                    </address>

                                    @if(!empty($saleOrder->customer->details['delivery_window']))
                                        <p><strong>Delivery Window:</strong> {{ $saleOrder->customer->details['delivery_window'] }}</p>
                                    @endif

                                    @if($saleOrder->order_notes)
                                        <h6 class="header-title">Order Notes</h6>
                                        <p>{!! nl2br($saleOrder->order_notes) !!}</p>
                                    @endif
                                </div>
                                <div class="pull-right m-t-30">
                                    <p class=""><strong>Order Status: </strong> <span class="badge badge-{{ status_class($saleOrder->status) }}">{{ ucwords($saleOrder->status) }}</span></p>
                                    {{--<p class=""><strong>Payment Status: </strong> <span class="badge badge-{{ ( ($saleOrder->balance > 0) ? 'success' : 'danger' ) }}">{{ ( ($saleOrder->balance > 0) ? 'Open' : 'Paid' ) }}</span></p>--}}
                                    <p><strong>Order Date: </strong> {{ $saleOrder->txn_date->format('M d, Y') }}</p>
                                    @if($saleOrder->expected_delivery_date)
                                    <p><strong>Expected Delivery Date: </strong> {{ $saleOrder->expected_delivery_date->format('M d, Y') }}</p>
                                    @endif
                                    <p><strong>Order Type:</strong> {{ ucfirst($saleOrder->customer_type) }}</p>

                                    <p>
                                        <strong>Terms:</strong>
                                        @if( ! is_null($saleOrder->terms))
                                            {{ config('highline.payment_terms')[$saleOrder->terms] }}
                                        @else
                                            {{ (!empty($saleOrder->customer->details['terms']) ? config('highline.payment_terms')[$saleOrder->customer->details['terms']] : 'Due on Receipt' ) }}
                                        @endif
                                    </p>
                                    <p>
                                        <strong>Due Date: </strong>
                                        @if($saleOrder->due_date)
                                            {{ $saleOrder->due_date->format(config('highline.date_format')) }}
                                        @else
                                            {{ $saleOrder->txn_date->addDays((!empty($saleOrder->customer->details['terms']) ? $saleOrder->customer->details['terms'] : 0 ))->format('m/d/Y') }}
                                        @endif
                                    </p>

                                    <p class=""><strong>Applied Payments: </strong> {{ display_currency($saleOrder->total - $saleOrder->balance) }}</p>
                                    <h6 class="balance">
                                        <strong>Balance: </strong> {{ display_currency($saleOrder->balance) }}
                                    </h6>

                                </div>
                            </div>
                        </div>
                        <div class="m-h-10"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table m-t-30">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Batch ID</th>
                                            <th>Packaged</th>
                                            <th>Brand</th>
                                            <th>Item</th>
                                            {{--<th>Description</th>--}}
                                            <th class="text-right">Ordered</th>
                                            @if($saleOrder->status=='delivered')
                                            <th class="text-right">Rec'd</th>
                                            @endif
                                            <th class="text-right">Price</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                        </thead>

                                        <tbody>

                                        @foreach($saleOrder->order_details->groupBy('batch.category.name') as $category_name => $order_details)

                                            <tr>
                                                <td colspan="6"><strong>{{ $category_name }}</strong></td>
                                            </tr>

                                            @foreach($order_details->sortBy('sold_as_name') as $order_detail)

                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ ($order_detail->batch->batch_number?:$order_detail->batch->ref_number) }}</td>
                                                <td>{{ ($order_detail->batch->packaged_date?$order_detail->batch->packaged_date->format(config('highline.date_format')):'') }}</td>
                                                <td>{{ ($order_detail->batch->brand ? $order_detail->batch->brand->name : '--') }}</td>
                                                <td>{{ $order_detail->sold_as_name }}</td>
                                                {{--<td></td>--}}
                                                <td class="text-right">{{ $order_detail->units }} <span style="font-size: 70%;"> {{ $order_detail->batch->uom }}</span></td>
                                                @if($saleOrder->status=='delivered')
                                                <td class="text-right">{{ $order_detail->units_accepted }} <span style="font-size: 70%;"> {{ $order_detail->batch->uom }}</span></td>
                                                @endif
                                                <td class="text-right">{{ display_currency($order_detail->unit_sale_price) }}</td>
                                                <td class="text-right">{{ display_currency($order_detail->subtotal) }}</td>
                                            </tr>

                                            @endforeach

                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-40">
                            <div class="col-3">
                                <div class="clearfix">
                                    <h4 class="small text-inverse"><strong>PAYMENT TERMS AND POLICIES</strong></h4>
                                    <p><small>Excise tax to be <strong>paid in-full</strong> on delivery.<br>To be paid by check or cash.</small></p>
                                    <p><strong>Checks Made Payable To:</strong><br>
                                    High Line Distribution, Inc.<br>
                                    11165 Tennessee Ave.<br>
                                    Los Angeles, CA 90064</p>
                                </div>
                            </div>
                            <div class="col-9">

                                <p class="text-right"><b>Subtotal:</b> {{ display_currency($saleOrder->subtotal) }}</p>

                                <p class="text-right"><b>Excise Tax:</b> {{ display_currency($saleOrder->tax) }}</p>

                                @if($saleOrder->discount)
                                    <p class="text-right"><b>Subtotal w/ Excise Tax:</b> {{ display_currency($saleOrder->subtotal + $saleOrder->tax) }}</p>
                                    <p class="text-right"><b>Discount</b> - {{ $saleOrder->discount_description }}: <span class="text-danger">({{ display_currency($saleOrder->discount) }})</span></p>
                                @endif

                                <p class="text-right"><b>Total: {{ display_currency($saleOrder->total) }}</p>

                                {{--<p class="text-right"><b>LA City 1% Transportation Tax:</b> {{ display_currency($saleOrder->transpo_tax) }}</p>--}}

                                <hr>
                                <h5 class="text-right">Balance: {{ display_currency($saleOrder->balance) }}</h5>

                                {{--@if(str_contains(strtolower($saleOrder->customer_type), 'distributor'))--}}
                                    {{--<p><i></i></p>--}}
                                {{--@endif--}}

                            </div>
                        </div>

                        @if($saleOrder->transactions->count())
                        <h6>Payments</h6>

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


                                @foreach($saleOrder->transactions as $transaction)
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
                                    <td>{{ display_currency($saleOrder->transactions->sum('amount')) }}</td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                        @endif

                    </div>
                </div>
            </div>
        </div>
