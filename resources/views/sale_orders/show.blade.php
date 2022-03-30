@extends('layouts.app')


@section('content')

    <div class="clearfix">
        <div class="pull-right">
    @if( ! $saleOrder->order_details->count())

        {{ Form::open(['class'=>'form-horizontal', 'url'=>route('sale-orders.remove', $saleOrder->id)]) }}
        <button type="submit" class="btn btn-danger waves-effect waves-light" onclick="return confirm('Are you sure you want to delete this order?')">Delete Order</button>
        {{ Form::close() }}

    @else

        {{--<a href="{{ route('sale-orders.shipping-manifest', $saleOrder->id) }}" class="btn btn-dark waves-effect waves-light">Shipping Manifest <i class="ti-receipt"></i></a>--}}

        {{--@if(!$saleOrder->batchRequiresRetag)--}}
            <a href="{{ route('sale-orders.invoice', $saleOrder->id) }}" class="btn btn-dark waves-effect waves-light">Invoice <i class="ti-receipt"></i></a>
        {{--@endif--}}

    @endif
        </div>
    </div>

<div class="row mt-3">

    <div class="col-lg-12 mb-3">

        <div class="card">
            <div class="card-block">

                <div class="row">
                    <div class="col-lg-3">

                        <h4>Order Summary</h4>

                        <dl class="row">
                            <dt class="col-4  text-right">Order#:</dt>
                            <dd class="col-8 ">{{ $saleOrder->ref_number }}</dd>
                            @if($saleOrder->parent_order)
                                <dt class="col-4  text-right">Original Order#:</dt>
                                <dd class="col-8 "><a href="{{ route('sale-orders.show', $saleOrder->parent_order) }}">{{ $saleOrder->parent_order->ref_number }}</a></dd>
                            @endif
                            <dt class="col-4  text-right">Entered By:</dt>
                            <dd class="col-8 ">{{ $saleOrder->user->name }}</dd>
                            <dt class="col-4  text-right">Status:</dt>
                            <dd class="col-8 "><span class="badge badge-{{ status_class($saleOrder->status) }}"> {{ ucwords($saleOrder->status) }} </span>
                            @if($saleOrder->status=='delivered' && !empty($saleOrder->delivered_at))
                                {{ $saleOrder->delivered_at->format('m/d/Y g:ia') }}
                            @endif
                            </dd>
                            <dt class="col-4 text-right">Sale Date:</dt>
                            <dd class="col-8">{{ $saleOrder->txn_date->format('m/d/Y') }}</dd>
                            <dt class="col-4 text-right">Sale Type:</dt>
                            <dd class="col-8">{{ ucwords($saleOrder->sale_type) }}</dd>

                            <dt class="col-4 text-right">Terms:</dt>
                            <dd class="col-8">
                                @if( ! is_null($saleOrder->terms))
                                    {{ config('highline.payment_terms')[$saleOrder->terms] }}
                                @else
                                    {{ (!empty($saleOrder->customer->details['terms']) ? config('highline.payment_terms')[$saleOrder->customer->details['terms']] : 'Due on Receipt' ) }}
                                @endif
                            </dd>

                            <dt class="col-4 text-right">Exp. Delivery Date:</dt>
                            <dd class="col-8">
                                @if( ! empty($saleOrder->expected_delivery_date) )
                                    {{ $saleOrder->expected_delivery_date->format('m/d/Y') }}
                                    {{--<br><small>{{ $saleOrder->expected_delivery_date->diffForHumans() }}</small>--}}
                                @else
                                    --
                                @endif
                            </dd>

                            @if($saleOrder->due_date)
                                <dt class="col-4 text-right">Due Date:</dt>
                                <dd class="col-8">{{ $saleOrder->due_date->format('m/d/Y') }}</dd>
                            @endif

                            @if($saleOrder->sales_rep)
                                <dt class="col-4 text-right">Sales Rep:</dt>
                                <dd class="col-8"><a href="{{ route('users.show', $saleOrder->sales_rep->id) }}">{{ $saleOrder->sales_rep->name }}</a></dd>
                            @endif

                            @if($saleOrder->broker)
                                <dt class="col-4 text-right">Broker:</dt>
                                <dd class="col-8"><a href="{{ route('users.show', $saleOrder->broker->id) }}">{{ $saleOrder->broker->name }}</a></dd>
                            @endif


                            {{--<dt class="col-4 text-right">Manifest#:</dt>--}}
                            {{--<dd class="col-8">--}}
                                {{--@if($saleOrder->manifest_no)--}}
                                    {{--<a href="https://ca.metrc.com/reports/transfers/C11-0000347-LIC/manifest?id={{ $saleOrder->manifest_no }}" target="_blank">{{ $saleOrder->manifest_no }} <i class="ion ion-share"></i> </a>--}}
                                {{--@else--}}
                                    {{------}}
                                {{--@endif--}}
                            {{--</dd>--}}


                            <dt class="col-4 text-right">Ship To:</dt>
                            <dd class="col-8">

                                @if( ! empty($saleOrder->destination_license) )
                                    <a href="{{ route('users.show', $saleOrder->destination_license->id) }}">
                                        {!! $saleOrder->destination_license->present()->name_address()  !!}
                                    </a>
                                    {{--<br>--}}
                                    {{--{{ $saleOrder->destination_license->name }}:--}}
                                @else
                                    <a href="{{ route('users.show', $saleOrder->customer->id) }}">{!! $saleOrder->customer->present()->name_address()  !!}</a>
                                    {{--{{ ucwords($saleOrder->customer_type) }}--}}
                                @endif

                                {{--@if( ! empty($saleOrder->destination_license))--}}

                                    {{--{{ $saleOrder->destination_license->number }}--}}

                                {{--@else--}}

                                    {{--@if(stripos($saleOrder->customer_type, 'microbusiness') !== false)--}}
                                        {{--@if(!empty($saleOrder->customer->details['mb_license_number']))--}}
                                            {{--{{ $saleOrder->customer->details['mb_license_number'] }}--}}
                                        {{--@endif--}}
                                    {{--@elseif( stripos($saleOrder->customer_type, 'distributor') !== false )--}}
                                        {{--@if($saleOrder->customer->details['distro_rec_license_number'])--}}
                                            {{--{{ $saleOrder->customer->details['distro_rec_license_number'] }}--}}
                                        {{--@elseif($saleOrder->customer->details['distro_med_license_number'])--}}
                                            {{--{{ $saleOrder->customer->details['distro_med_license_number'] }}--}}
                                        {{--@endif--}}
                                    {{--@elseif( stripos($saleOrder->customer_type, 'manufacturing') !== false)--}}
                                        {{--@if(!empty($saleOrder->customer->details['mfg_license_number']))--}}
                                            {{--{{ $saleOrder->customer->details['mfg_license_number'] }}--}}
                                        {{--@endif--}}
                                    {{--@else--}}
                                        {{--@if($saleOrder->customer->details['rec_license_number'])--}}
                                            {{--{{ $saleOrder->customer->details['rec_license_number'] }}--}}
                                        {{--@elseif($saleOrder->customer->details['med_license_number'])--}}
                                            {{--{{ $saleOrder->customer->details['med_license_number'] }}--}}
                                        {{--@endif--}}
                                    {{--@endif--}}

                                {{--@endif--}}
                            </dd>

                            <dt class="col-4 text-right">Bill To:</dt>
                            <dd class="col-8">
                                <a href="{{ route('users.show', $saleOrder->customer->id) }}">{!! $saleOrder->customer->present()->name_address()  !!}</a>

                                {{--@if($saleOrder->bill_to)--}}
                                    {{--<a href="{{ route('users.show', $saleOrder->bill_to->id) }}">{!! $saleOrder->bill_to->present()->name_address()  !!}</a>--}}
                                    {{--@else--}}
                                    {{--<a href="{{ route('users.show', $saleOrder->customer->id) }}">{!! $saleOrder->customer->present()->name_address()  !!}</a>--}}
                                {{--@endif--}}
                            </dd>

                            {{--@if(!empty($saleOrder->customer->details['delivery_window']))--}}
                                {{--<dt class="col-4 text-right">Delivery Window:</dt>--}}
                                {{--<dd class="col-8">{{ $saleOrder->customer->details['delivery_window'] }}</dd>--}}
                            {{--@endif--}}

                            {{--<dt class="col-4 text-right">Subtotal:</dt>--}}
                            {{--<dd class="col-8 ">{{ display_currency($saleOrder->subtotal) }}</dd>--}}


                            {{--@if($saleOrder->excise_tax_pre_discount)--}}

                                {{--@if($saleOrder->tax)--}}
                                {{--<dt class="col-4 text-right">Excise Tax @ 27%:</dt>--}}
                                {{--<dd class="col-8 ">{{ display_currency($saleOrder->tax) }}</dd>--}}
                                {{--@endif--}}

                                {{--@if($saleOrder->discount)--}}
                                    {{--<dt class="col-4 text-right">Subtotal w/ Excise Tax:</dt>--}}
                                    {{--<dd class="col-8">{{ display_currency($saleOrder->subtotal + $saleOrder->tax) }}</dd>--}}
                                    {{--<dt class="col-4 text-right">Discount:</dt>--}}
                                    {{--<dd class="col-8 text-danger">({{ display_currency($saleOrder->discount) }})</dd>--}}
                                {{--@endif--}}

                            {{--@else--}}

                                {{--@if($saleOrder->discount)--}}
                                    {{--<dt class="col-4 text-right">Discount:</dt>--}}
                                    {{--<dd class="col-8 text-danger">({{ display_currency($saleOrder->discount) }})</dd>--}}

                                    {{--<dt class="col-4 text-right">Subtotal w/ Discount:</dt>--}}
                                    {{--<dd class="col-8">{{ display_currency($saleOrder->subtotal - $saleOrder->discount) }}</dd>--}}
                                {{--@endif--}}

                                {{--@if($saleOrder->tax)--}}
                                    {{--<dt class="col-4 text-right">Excise Tax @ 27%:</dt>--}}
                                    {{--<dd class="col-8 ">{{ display_currency($saleOrder->tax) }}</dd>--}}
                                {{--@endif--}}

                            {{--@endif--}}


                            {{--<dt class="col-4 text-right">Order Total:</dt>--}}
                            {{--<dd class="col-8 ">{{ display_currency($saleOrder->total) }}</dd>--}}

                            {{--<dt class="col-4 text-right">Order Balance:</dt>--}}
                            {{--<dd class="col-8">{{ display_currency($saleOrder->balance) }}</dd>--}}

                        </dl>

                        {{--@role('admin')--}}
                        {{--<hr>--}}
                        {{--<dl class="row">--}}
                            {{--<dt class="col-4 text-right hidden-print">Total Cost:</dt>--}}
                            {{--<dd class="col-8 hidden-print">{{ display_currency($saleOrder->cost) }}</dd>--}}

                            {{--<dt class="col-4 text-right hidden-print">Cost Breakdown:</dt>--}}
                            {{--<dd class="col-8 hidden-print">--}}
                                {{--@foreach($saleOrder->cost_by_fund as $fund_name => $od)--}}
                                   {{--{{ $fund_name }}: {{ display_currency($od->sum('cost')) }}<br>--}}
                                {{--@endforeach--}}
                            {{--</dd>--}}

                            {{--<dt class="col-4 text-right hidden-print">Revenue:</dt>--}}
                            {{--<dd class="col-8 hidden-print">{{ display_currency($saleOrder->revenue) }}</dd>--}}

                            {{--<dt class="col-4 text-right hidden-print">Margin:</dt>--}}
                            {{--<dd class="col-8 hidden-print text-{{ ($saleOrder->margin > 0?'success':'danger') }}">{{ display_currency($saleOrder->margin) }} <small>({{ $saleOrder->margin_pct }}%) <i class="ion-arrow-{{ ($saleOrder->margin > 0?'up':'down') }}-c"></i> </small></dd>--}}
                        {{--</dl>--}}
                        {{--@endrole--}}

                    </div>
                    <div class="col-lg-4 hidden-print">

                        <h4>Discounts</h4>

                        {{ Form::open(['url'=>route('sale-orders.apply-discount', ['sale_order'=>$saleOrder->id])]) }}
                        {{ method_field('PUT') }}

                        <dl class="row">
                            <div class="form-group col-xl-6">
                                <select  class="form-control" name="discount_type">
                                    <option value="none" @if ('none' == old('discount_type', $saleOrder->discount_type)) selected="selected" @endif>None</option>
                                    <option value="amt" @if ('amt' == old('discount_type', $saleOrder->discount_type)) selected="selected" @endif>Dollar Amount</option>
                                    <option value="perc" @if ('perc' == old('discount_type', $saleOrder->discount_type)) selected="selected" @endif>Percentage</option>
                                </select>
                            </div>
                            <div class="form-group col-xl-6">
                                <div class="input-group mb-2">
                                    <input class="form-control" name="discount_applied" value="{{ display_currency_no_sign(old('discount_applied', $saleOrder->discount_applied)) }}" placeholder="">
                                </div>
                            </div>
                            <div class="form-group col-xl-12">
                                <label for="discount_description">Description</label>
                                <textarea class="form-control" id="discount_description" name="discount_description" rows="2">{{ old('discount_description', $saleOrder->discount_description) }}</textarea>
                            </div>
                            <div class="form-group col-xl-6">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Apply Discount</button>
                            </div>
                        </dl>

                        {{ Form::close() }}
                        {{--<hr>--}}

                        <h4>Extras</h4>

                        {{ Form::open(['url'=>route('sale-orders.update', ['sale_order'=>$saleOrder->id])]) }}
                        {{ method_field('PUT') }}

                        <dl class="row">

                            <div class="col-6">
                                <dt class="">Sales Rep:</dt>
                                <dd class=" ">
                                    <select name="sales_rep_id" class="form-control">
                                        <option value="">-- Select --</option>
                                        @foreach($sales_reps as $sales_rep_id => $sales_rep_name)
                                            <option value="{{ $sales_rep_id }}" {{ ($sales_rep_id == $saleOrder->sales_rep_id?"selected=\"\"":"") }}>{{ $sales_rep_name }}</option>
                                        @endforeach
                                    </select>
                                </dd>
                            </div>

                            <div class="col-6">
                                <dt class="">Broker:</dt>
                                <dd class="">
                                    <select name="broker_id" class="form-control">
                                        <option value="">-- Select --</option>
                                        @foreach($brokers as $broker_id => $broker_name)
                                            <option value="{{ $broker_id }}" {{ ($broker_id == $saleOrder->broker_id?"selected=\"\"":"") }}>{{ $broker_name }}</option>
                                        @endforeach
                                    </select>
                                </dd>
                            </div>

                        </dl>

                        <dl class="row">

                            @if($saleOrder->status != 'delivered')

                            <div class="col-6">

                            <dt class="">Order Date:</dt>
                            <dd class=""><input type="date" class="form-control" name="txn_date" value="{{ ($saleOrder->txn_date?$saleOrder->txn_date->format('Y-m-d'):'') }}"></dd>
                            </div>
                            <div class="col-6">
                            <dt class="">Exp. Delivery Date:</dt>
                            <dd class=" "><input type="date" class="form-control" name="expected_delivery_date" value="{{ ($saleOrder->expected_delivery_date?$saleOrder->expected_delivery_date->format('Y-m-d'):'') }}"></dd>
                            </div>
                            @endif

                            <div class="col-6">

                            <dt class="">Metrc Manifest #:</dt>
                            <dd class=" ">
                                <input type="text" class="form-control" name="manifest_no" value="{{ ($saleOrder->manifest_no) }}">
                            </dd>
                            </div>

                            <div class="col-6">
                            <dt class="">Terms:</dt>
                            <dd class="">
                                <select name="terms" class="form-control">
                                    @foreach(config('highline.payment_terms') as $payment_term_days => $payment_term)
                                    <option value="{{ $payment_term_days }}" {{ ($payment_term_days == $saleOrder->terms?"selected=\"\"":"") }}>{{ $payment_term }}</option>
                                    @endforeach
                                </select>
                            </dd>

                            </div>

                                <div class="col-6">
                            <dt class="">Internal Notes:</dt>
                            <dd class=""><textarea class="form-control" id="notes" name="notes" rows="3">{{ $saleOrder->notes }}</textarea></dd>
                                </div>
                                    <div class="col-6">
                            <dt class="">Order Notes:</dt>
                            <dd class=""><textarea class="form-control" id="order_notes" name="order_notes" rows="3">{{ $saleOrder->order_notes }}</textarea></dd>
                                    </div>
                        </dl>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save</button>


                        {{ Form::close() }}

                    </div>



                    <div class="col-lg-5 hidden-print">

                        <h4>Order Totals</h4>

                        <div class="row">

                            <div class="col-7">

                                <dl class="row" style="font-size: 14px">
                                    <dt class="col-4 text-right">Subtotal:</dt>
                                    <dd class="col-8 ">{{ display_currency($saleOrder->subtotal) }}</dd>

                                    @if($saleOrder->excise_tax_pre_discount)

                                        @if($saleOrder->tax)
                                        <dt class="col-4 text-right">Excise Tax @ 27%:</dt>
                                        <dd class="col-8 ">{{ display_currency($saleOrder->tax) }}</dd>
                                        @endif

                                        @if($saleOrder->discount)
                                        <dt class="col-4 text-right">Subtotal w/ Excise Tax:</dt>
                                        <dd class="col-8">{{ display_currency($saleOrder->subtotal + $saleOrder->tax) }}</dd>
                                        <dt class="col-4 text-right">Discount:</dt>
                                        <dd class="col-8 text-danger">({{ display_currency($saleOrder->discount) }})</dd>
                                        @endif

                                    @else

                                        @if($saleOrder->discount)
                                        <dt class="col-4 text-right">Discount:</dt>
                                        <dd class="col-8 text-danger">({{ display_currency($saleOrder->discount) }})</dd>

                                        <dt class="col-4 text-right">Subtotal w/ Discount:</dt>
                                        <dd class="col-8">{{ display_currency($saleOrder->subtotal - $saleOrder->discount) }}</dd>
                                        @endif

                                        @if($saleOrder->tax)
                                        <dt class="col-4 text-right">Excise Tax @ 27%:</dt>
                                        <dd class="col-8 ">{{ display_currency($saleOrder->tax) }}</dd>
                                        @endif

                                    @endif

                                    <dt class="col-4 text-right">Total:</dt>
                                    <dd class="col-8 ">{{ display_currency($saleOrder->total) }}</dd>
                                </dl>

                            </div>
                            <div class="col-5">

                                <dl class="row" style="font-size: 14px">

                                    <dt class="col-4 text-right">Balance:</dt>
                                    <dd class="col-8">{{ display_currency($saleOrder->balance) }}</dd>

                                </dl>

                            </div>
                        </div>

                        <hr>

                        <div class="row">

                            <div class="col-7">

                                <dl class="row" style="font-size: 14px">

                                    @foreach($saleOrder->cost_by_fund as $fund_name => $od)
                                        <dt class="col-4 text-right hidden-print">{{ $fund_name }}:</dt>
                                        <dd class="col-8 hidden-print">{{ display_currency($od->sum('cost')) }}</dd>
                                    @endforeach


                                    <dt class="col-4 text-right hidden-print">Total Cost:</dt>
                                    <dd class="col-8 hidden-print">{{ display_currency($saleOrder->cost) }}</dd>
                                </dl>

                            </div>

                            <div class="col-5">

                                <dl class="row" style="font-size: 14px">
                                    <dt class="col-4 text-right hidden-print">Revenue:</dt>
                                    <dd class="col-8 hidden-print">{{ display_currency($saleOrder->revenue) }}</dd>

                                    <dt class="col-4 text-right hidden-print">Margin:</dt>
                                    <dd class="col-8 hidden-print text-{{ ($saleOrder->margin > 0?'success':'danger') }}">{{ display_currency($saleOrder->margin) }}<br><small>({{ $saleOrder->margin_pct }}%) <i class="ion-arrow-{{ ($saleOrder->margin > 0?'up':'down') }}-c"></i> </small></dd>
                                </dl>

                            </div>
                        </div>



                        @if((Auth::user()->isAdmin() && $saleOrder->isDelivered()) || Auth::user()->id == 13)

                        {{ Form::open(['url'=>route('sale-orders.payment', ['sale_order'=>$saleOrder->id])]) }}

                        <h4>Payments</h4>

                        <div class="row">
                            {{--<div class="col-xl-6">--}}
                            <div class="form-group col-xl-6">
                                <label for="txn_date">Payment Date</label>
                                <input type="date" class="form-control" id="txn_date" name="txn_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                                {{--<small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>--}}
                            </div>
                            {{--</div>--}}
                            {{--<div class="col-xl-6">--}}
                            <div class="form-group col-xl-6">
                                <label for="txn_date">Payment</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" step="0.01" class="form-control" name="payment" value="{{ display_currency_no_sign($saleOrder->balance) }}" placeholder="">
                                </div>
                            </div>
                            {{--</div>--}}
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select class="form-control" name="payment_method">
                                        <option value="Cash">Cash</option>
                                        <option value="Check">Check</option>
                                        <option value="Credit">Credit</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="ref_number">Reference #</label>
                                    <input type="text" class="form-control" id="ref_number" name="ref_number">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <label for="memo">Memo</label>
                                    <textarea class="form-control" id="memo" name="memo" rows="2"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Save Payment</button>
                            </div>
                        </div>


                        {{ Form::close() }}
                        @endif

                        @if($saleOrder->transactions->count())

                        <h4>Transactions</h4>

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
                                    <th>By</th>
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
                                        <td>{{ $transaction->user->name }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                                <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>{{ display_currency($saleOrder->transactions->sum('amount')) }}</td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        @endif

                        @if($saleOrder->sales_commission_details->count())
                        <h4>Paid Sales Commissions</h4>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>Rep</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                    <th>% of Mrg</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($saleOrder->sales_commission_details as $sales_commission_detail)
                                <tr>
                                    <td>{{ $sales_commission_detail->sales_rep->name }}</td>
                                    <td>{{ $sales_commission_detail->rate*100 }}%</td>
                                    <td>{{ display_currency($sales_commission_detail->amount) }}</td>
                                    <td>{{ ($sales_commission_detail->amount && $saleOrder->margin ? round(($sales_commission_detail->amount/$saleOrder->margin)*100, 2) : 0) }}%</td>
                                </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <td>{{ round($saleOrder->sales_commission_details->sum('rate')*100,2) }}%</td>
                                        <th>{{ display_currency($saleOrder->sales_commission_details->sum('amount')) }}</th>
                                        <th>{{ ($saleOrder->sales_commission_details->sum('amount') && $saleOrder->margin ? round(($saleOrder->sales_commission_details->sum('amount')/$saleOrder->margin)*100, 2) : 0) }}%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        @endif

                        @if($saleOrder->return_orders->count())
                            <h4>Returns</h4>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                    <tr>
                                        <th>Order#</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($saleOrder->return_orders as $return_order)
                                        <tr>
                                            <td><a href="{{ route('sale-orders.show', $return_order) }}">{{ $return_order->ref_number }}</a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            </div>

                        @endif

                    </div>


                </div>

            </div>

            <div class="card-footer hidden-print">

            @if($saleOrder->isOpen() && !$batches_need_retag->count())

                    {{ Form::open(['url'=>route('sale-orders.ready-for-delivery', ['sale_order'=>$saleOrder->id])]) }}
                    <button type="submit" class="btn btn-primary waves-effect waves-light pull-right conf_action">Ready for delivery</button>
                    {{ Form::close() }}

            @elseif($saleOrder->isReadyForDelivery())

                    {{ Form::open(['url'=>route('sale-orders.in-transit', ['sale_order'=>$saleOrder->id])]) }}
                        <button type="submit" class="btn btn-primary waves-effect waves-light pull-right conf_action">In-Transit</button>
                    {{ Form::close() }}

            @elseif($saleOrder->isInTransit())

                    {{ Form::open(['url'=>route('sale-orders.accept-all', [$saleOrder->id])]) }}
                    {{ method_field('PUT') }}
                            <button type="submit" class="btn btn-primary waves-effect waves-light pull-right conf_action">Accept All</button>
                    {{ Form::close() }}

            @endif

            @if(($saleOrder->isReadyForDelivery() || $saleOrder->isInTransit()) || Auth::user()->isAdmin() && !$saleOrder->isOpen())

                    {{ Form::open(['url'=>route('sale-orders.open', ['sale_order'=>$saleOrder->id])]) }}
                    <button type="submit" class="btn btn-primary waves-effect waves-light pull-right m-r-10">Open Order</button>
                    {{ Form::close() }}
            @endif

            @if($saleOrder->isOpen() && ($saleOrder->order_details->sum('units') == $saleOrder->order_details->sum('units_accepted')))
                {{ Form::open(['url'=>route('sale-orders.update', ['sale_order'=>$saleOrder->id, 'status'=>'delivered'])]) }}
                {{ method_field('PUT') }}
                <button type="submit" class="btn btn-primary waves-effect waves-light pull-right m-r-10">Close Order</button>
                {{ Form::close() }}
            @endif

            </div>

        </div>

    </div>

</div>

@if($saleOrder->isOpen())

<div class="row">

    <div class="col-lg-12">

        <div class="card-box">
            <h4 class="m-t-0 header-title">Add Items</h4>

            {{ Form::open(['url'=>route('order-details.store'), 'method'=>'post', 'id'=>"add-new-item"]) }}
            {{ Form::hidden('cog', 0, ['class'=>'cog']) }}
            {{ Form::hidden('_sale_order_id', $saleOrder->id) }}
            {{ Form::hidden('batch_id', 0, ['class'=>'batch_id']) }}

                <table class="table add-new-item">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Sold As Name</th>
                        <th>Quantity</th>
                        <th>Cost</th>
                        <th>Suggested Sale Price</th>
                        <th></th>
                        <th>Markup</th>
                        {{--<th class="pass_on_tax_cell">Pass-on Tax</th>--}}
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td class="col-3">

                            <div class="mb-2">
                                <div class="typeahead__container">

                                        <div class="typeahead__query">
                                            <input class="js-typeahead-batches form-control"
                                                   name="_q"
                                                   autocomplete="off"
                                                   placeholder="Search Batches">
                                        </div>

                                </div>
                            </div>

                        </td>
                        <td class="col-2">
                            <div class="input-group">
                                <input type="text" class="form-control sold_as_name" name="_sold_as_name_input" value="" placeholder="Sold As Name">

                            </div>
                        </td>
                        <td class="">
                            <div class="input-group">
                                <input type="text" class="form-control qty" name="units" value="" required>
                                <span class="input-group-addon uom">&nbsp;</span>
                            </div>
                        </td>
                        <td class="">
                            {{--<span class="unit_cost">--</span>--}}
                            <div class="input-group mb-2">
                                <span class="input-group-addon">$</span>
                                <input type="text" class="form-control unit_cost" name="" value="" disabled>
                            </div>
                            {{--<p><span class="pre-tax-cost"></span></p>--}}
                            {{ Form::hidden('unit_cost', 0, ['class'=>'unit_cost']) }}
                        </td>
                        <td class="">
                            <div class="input-group mb-2">
                                <span class="input-group-addon">$</span>
                                <input type="text" class="form-control sale_price" name="_unit_sale_price" value="">
                            </div>
                        </td>
                        <td class="" style="white-space:nowrap;">
                            <strong>- OR -</strong>
                        </td>
                        <td>
                            <div class="input-group mb-2">
                                <span class="input-group-addon">$</span>
                                <input type="text" class="form-control unit_markup" name="_unit_markup" value="">
                            </div>
                        </td>
                        {{--<td class="pass_on_tax_cell">--}}
                            {{--<select name="pass_cult_tax" class="form-control pass-cult-tax" style="width:75px">--}}
                                {{--<option value="1">Yes</option>--}}
                                {{--<option value="0">No</option>--}}
                            {{--</select>--}}
                        {{--</td>--}}
                        <td><button type="submit" class="btn btn-primary waves-effect waves-light">Add</button></td>


                    </tr>
                    </tbody>
                </table>
            {{--</div>--}}

            {{ Form::close() }}

        </div>

    </div>

</div>

@endif

<div class="row">

    <div class="col-lg-12">

        <div class="card-box">
            <h4 class="m-t-0 header-title">Line Items</h4>


            <div class="table-responsive">

                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th class="hidden-print">Pkg#</th>
                        {{--<th class="hidden-print">M</th>--}}
                        {{--<th class="hidden-print">COA</th>--}}
                        {{--<th class="hidden-print">Batch#</th>--}}
                        {{--<th class="hidden-print">Harvest</th>--}}
                        {{--<th class="hidden-print">Fund</th>--}}
                        <th class="hidden-print">
                            SKU
                            {{--<a href="{{ route('sale-orders.retag-uids', $saleOrder->id) }}">View Retags</a>--}}
                            {{--@if($batches_need_retag->count())--}}

                                {{--{{ Form::open(['url'=>route('sale-orders.retag-uids', $saleOrder->id), 'method' => 'get']) }}--}}

                                {{--<div class="row">--}}
                                    {{--<div class="col-md-8">--}}
                                        {{--<div class="input-group mb-2">--}}
                                            {{--<input type="text" class="form-control" name="start_tag_id" value="" placeholder="First Tag Id" required>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="row">--}}
                                    {{--<div class="col-md-2">--}}
                                        {{--<button type="submit" class="btn btn-primary waves-effect waves-light">Retag All</button>--}}
                                    {{--</div>--}}
                                {{--</div>--}}

                                {{--{{ Form::close() }}--}}

                            {{--@endif--}}

                        </th>
                        {{--<th class="hidden-print">Pkg Date</th>--}}
                        {{--<th>Brand</th>--}}
                        <th>Category</th>
                        <th>Sold as Name</th>
                        <th class="hidden-print">Unit Cost</th>
                        <th>Qty @ Price</th>
                        <th>Unit Mark-up</th>
                        {{--<th>Retag</th>--}}
                        <th>Ordered</th>
                        <th>Received</th>
                        <th>Returned</th>
                        <th>Inventory</th>
                        {{--<th>Uom</th>--}}

                        {{--<th>Unit Sale Price</th>--}}
                        <th>Subtotal</th>

                        <th class="hidden-print">Profit</th>
                        <th class="hidden-print">Margin %</th>
                        {{--<th class="hidden-print">Pass Cult. Tax</th>--}}
                        <th class="hidden-print"></th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($saleOrder->order_details->where('cog', 1)->groupBy('batch.uom') as $uom => $order_details)

                        @foreach($order_details->sortBy('sold_as_name') as $order_detail)

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                {{--<td>--}}
                                    {{--@if($order_detail->batch->in_metrc)<i class=" mdi mdi-checkbox-marked text-success"></i>@else<i class=" mdi mdi-checkbox-blank text-danger"></i>@endif--}}
                                {{--</td>--}}
                                {{--<td class="hidden-print">--}}
                                    {{--@if(!empty($order_detail->batch->COASourceBatch->coa_link))--}}
                                        {{--<a href="{{ $order_detail->batch->COASourceBatch->coa_link }}" target="_blank"><i class="mdi mdi-qrcode"></i></a>--}}
                                    {{--@endif--}}
                                {{--</td>--}}
                                {{--<td class="hidden-print">--}}
                                    {{--@if($order_detail->batch->batch_number)--}}
                                    {{--{{ $order_detail->batch->batch_number }}--}}
                                    {{--@endif--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{ ($order_detail->batch->harvest_date) }}--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{{ $order_detail->batch->fund->name }}--}}
                                {{--</td>--}}
                                <td class="hidden-print text-nowrap">
                                    @if( ! empty($order_detail->batch))
                                        @if($batches_need_retag->has($order_detail->batch->id))
                                            <i class=" mdi mdi-alert text-danger" style="font-size: 14px;"></i>
                                        @endif
                                        <a href="{{ route('batches.show', $order_detail->batch->ref_number) }}">{{ $order_detail->batch->ref_number }}</a>

                                        {{--@if($saleOrder->isOpen() && $order_detail->batch->requiresRetag)--}}

                                        {{--{{ Form::open(['url'=>route('order-details.retag', $order_detail)]) }}--}}
                                        {{--{{ method_field('PUT') }}--}}

                                            {{--<div class="row">--}}
                                                {{--<div class="col-md-8">--}}
                                                    {{--<div class="input-group mb-2">--}}
                                                        {{--<input type="text" class="form-control" name="tag_id" value="">--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                            {{--<div class="row">--}}
                                                {{--<div class="col-md-2">--}}
                                                    {{--<button type="submit" class="btn btn-primary waves-effect waves-light">Retag</button>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}

                                        {{--{{ Form::close() }}--}}

                                        {{--@endif--}}

                                    @endif
                                </td>

                                {{--<td class="hidden-print">--}}
                                    {{--@if(!empty($order_detail->batch))--}}
                                    {{--{{ ($order_detail->batch->packaged_date ? $order_detail->batch->packaged_date->format(config('highline.date_format')) : '--' ) }}--}}
                                    {{--@endif--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--@if(!empty($order_detail->batch))--}}
                                    {{--{{ ($order_detail->batch->brand ? $order_detail->batch->brand->name : '--' ) }}--}}
                                    {{--@endif--}}
                                {{--</td>--}}

                                <td class="text-nowrap">
                                    @if(!empty($order_detail->batch))
                                        {{ $order_detail->batch->category->name }}
                                    @endif
                                </td>

                                {{ Form::open(['url'=>route('order-details.update', $order_detail)]) }}
                                {{ method_field('PUT') }}
                                {{ Form::hidden('cog', 1) }}

                                <td>


                                        @if($saleOrder->isOpen())

                                            <div class="row" style="width: 200px">
                                                <div class="input-group mb-2 col-12">
                                                    <input type="text" class="form-control" name="sold_as_name" value="{{ $order_detail->sold_as_name }}">
                                                    {{--<button type="submit" class="btn btn-primary waves-effect waves-light ml-1">Save</button>--}}
                                                </div>
                                            </div>

                                        @else
                                            {{ $order_detail->sold_as_name?:$order_detail->batch->name }}
                                        @endif

                                        @if($order_detail->batch->wt_based)
                                            <br>UOM: {{ $order_detail->batch->uom }}
                                        @endif

                                    @if($order_detail->metrc_uid)
                                        <br>{{ $order_detail->metrc_uid }}
                                    @endif
                                </td>

                                <td class="hidden-print">
                                    {{ display_currency($order_detail->unit_cost) }}
                                    {{--@if($order_detail->batch->tax_rate)--}}
                                        {{--<br><small>Pre-Tax: {{ display_currency($order_detail->batch->preTaxCost) }}</small>--}}
                                    {{--@endif--}}
                                </td>

                                <td>
                                    @if(!empty($order_detail->batch) && $saleOrder->isOpen())

                                        {{--{{ Form::open(['url'=>route('order-details.update', $order_detail)]) }}--}}
                                        {{--{{ method_field('PUT') }}--}}
                                        {{--{{ Form::hidden('cog', 1) }}--}}

                                        <div class="row" style="width: 245px">
                                            <div class="col-6">

                                                @if($order_detail->batch->wt_based)
                                                    {!! display_inventory($order_detail->batch) !!}

                                                    {{ Form::hidden('units', $order_detail->units) }}

                                                    {{--<input type="hidden" name="units" value="{{ $order_detail->units }}" />--}}
                                                @else
                                                    <div class="input-group mb-2">
                                                        <input type="text" class="form-control" name="units" value="{{ $order_detail->units }}">
                                                        <span class="input-group-addon">{{ $order_detail->batch->uom }}</span>
                                                    </div>
                                                @endif

                                            </div>
                                            <div class="col-6">
                                                <div class="input-group mb-2">
                                                    <span class="input-group-addon">$</span>
                                                    <input type="text" class="form-control" name="unit_sale_price" value="{{ display_currency_no_sign($order_detail->unit_sale_price) }}" disabled>
                                                    {{--<button type="submit" class="btn btn-primary waves-effect waves-light ml-1">Save</button>--}}
                                                </div>
                                            </div>
                                        </div>
                                        {{--<div class="row" style="width: 245px">--}}
                                            {{--<div class="offset-6 col-6">--}}
                                            {{--Pre-Tax: {{ display_currency($order_detail->unit_sale_price - $order_detail->batch->unitTaxAmount) }}--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        {{--{{ Form::close() }}--}}

                                        @else

                                            @if($order_detail->batch->wt_based)
                                                {{ $order_detail->batch->wt_grams }} g
                                            @else
                                                {{ $order_detail->units }} <small>{{ (!empty($order_detail->batch)?$order_detail->batch->uom:'') }}</small>
                                            @endif

                                            @ {{ display_currency($order_detail->unit_sale_price) }}

                                    @endif
                                </td>

                                <td class="hidden-print text-{{ ($order_detail->margin > 0?'success':'danger') }}">

                                    @if($saleOrder->isOpen())

                                        {{--{{ Form::open(['url'=>route('order-details.update', $order_detail)]) }}--}}
                                        {{--{{ method_field('PUT') }}--}}
                                        {{--{{ Form::hidden('cog', 1) }}--}}

                                        <div class="row" style="width: 200px">
                                            <div class="col-8">
                                                <div class="input-group mb-2">
                                                    <span class="input-group-addon">$</span>
                                                    <input type="text" class="form-control {{ ($order_detail->unit_margin <=0 ? "text-danger" : "text-success") }}" name="_markup" value="{{ display_currency_no_sign($order_detail->unit_margin) }}">

                                                </div>

                                            </div>
                                            <div class="col-4">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light ml-1">Save</button>
                                            </div>
                                        </div>

                                    @else
                                        {{ display_currency($order_detail->unit_margin) }} <small>({{ $order_detail->markup_pct }})</small>
                                    @endif
                                </td>

                                {{ Form::close() }}


                                <td>
                                    {{ $order_detail->units }} {{ $uom }}
                                </td>

                                <td nowrap>
                                    @if(!empty($order_detail->batch) && $saleOrder->isInTransit() && $order_detail->notAccepted())

                                        {{ Form::open(['url'=>route('sale-orders.accept-order-detail', [$saleOrder->id, $order_detail->id])]) }}
                                        {{ method_field('PUT') }}

                                        <div class="row">
                                            <div class="col-md-4">

                                                {{--<div class="input-group mb-2">--}}
                                                    <input type="text" class="form-control" name="units_accepted" value="{{ $order_detail->units }}">
                                                    {{--<span class="input-group-addon">--}}

                                                        {{--{{ (!empty($order_detail->batch)?$order_detail->batch->uom:'') }}--}}
                                                    {{--</span>--}}
                                                {{--</div>--}}
                                                {{--@if($order_detail->batch->wt_based)--}}
                                                    {{--<br>{{ $order_detail->batch->wt_grams }} g--}}
                                                {{--@endif--}}

                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light">Accept</button>
                                            </div>
                                        </div>
                                        {{ Form::close() }}

                                    @else

                                        {{ (is_null($order_detail->units_accepted)?"--":(float)$order_detail->units_accepted." ".$uom) }}

                                    @endif
                                </td>

                                <td>
                                    {{ (!empty($order_detail->batch)?$order_detail->order_detail_returned->sum('units_accepted'):'--') }}  {{ $uom }}
                                </td>
                                <td>{{ (!empty($order_detail->batch)?$order_detail->batch->inventory:'--') }}</td>

                                <td>{{ display_currency($order_detail->subtotal) }}</td>

                                <td class="text-{{ ($order_detail->margin > 0?'success':'danger') }}">{{ display_currency($order_detail->margin) }}</td>

                                <td class="text-{{ ($order_detail->margin > 0?'success':'danger') }}">{{ $order_detail->margin_pct }}%</td>

                                {{--<td>--}}
                                    {{--@if($order_detail->unit_tax_amount)--}}
                                    {{--<i class=" mdi mdi-marker-check text-success " style="font-size: 1rem"></i>--}}
                                    {{--@endif--}}
                                {{--</td>--}}

                                <td class="hidden-print">

                                    @if($saleOrder->status == 'open')
                                        {{ Form::open(['class'=>'form-horizontal pull-right', 'url'=>route('sale-orders.remove-item', [$saleOrder->id, $order_detail->id])]) }}
                                        <button type="submit" class="btn btn-danger waves-effect waves-light" onclick="return confirm('Are you sure you want to remove from order?')">X</button>
                                        {{ Form::close() }}
                                    @endif

                                </td>
                            </tr>

                        @endforeach

                        <tr style="border-top: double 3px #ccc; border-bottom: double 3px #ccc;">
                            <td class="hidden-print" colspan="1"><strong>Subtotal:</strong></td>
                            <td class="hidden-print" colspan="6">
                                {{--<a href="{{ route('sale-orders.uid-export', $saleOrder->id) }}" class="btn btn-primary">Export UIDs</a>--}}
                            </td>
                            <td>
                                @if($saleOrder->order_details->count())
                                    {{ $order_details->sum('units') }} {{ $uom }}
                                    {{--{{ $order_details->first()->batch->uom }}--}}
                                @endif
                            </td>
                            <td colspan="1">
                                @if($saleOrder->order_details->count())
                                    {{ $order_details->sum('units_accepted') }} {{ $uom }}
                                    {{--{{ $order_details->first()->batch->uom }}--}}
                                @endif
                            </td>
                            <td></td>
                            <td></td>

                            <td colspan="5">{{ display_currency($order_details->sum('subtotal')) }}</td>
                        </tr>

                    @endforeach

                    @foreach($saleOrder->order_details->where('cog', 0) as $order_detail_misc)

                        <tr>
                            <td colspan="3"></td>

                            @if($saleOrder->isOpen())

                                {{ Form::open(['url'=>route('order-details.update', $order_detail_misc)]) }}
                                {{ method_field('PUT') }}
                                {{ Form::hidden('cog', 0) }}

                                <td colspan="2">
                                    <input type="text" class="form-control" name="sold_as_name" value="{{ $order_detail_misc->sold_as_name }}">
                                </td>

                                <td colspan="" style="width: 245px;">

                                    <div class="row">
                                        <div class="col-6">

                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="units" value="{{ $order_detail_misc->units }}">
                                            </div>

                                        </div>
                                        <div class="col-6">
                                            <div class="input-group mb-2">
                                                <span class="input-group-addon">$</span>
                                                <input type="text" class="form-control" name="unit_sale_price" value="{{ display_currency_no_sign($order_detail_misc->unit_sale_price) }}">
                                            </div>
                                        </div>
                                    </div>

                                </td>

                                <td>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                                </td>

                                {{ Form::close() }}

                            @else

                                <td>{{ $order_detail_misc->sold_as_name }}</td>
                                {{--<td></td>--}}
                                <td>{{ display_currency($order_detail_misc->unit_cost) }}</td>
                                <td colspan="2">{{ $order_detail_misc->units }} @ {{ display_currency($order_detail_misc->unit_sale_price) }}</td>

                            @endif

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ display_currency($order_detail_misc->subtotal) }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                @if($saleOrder->isOpen())
                                    {{ Form::open(['class'=>'form-horizontal pull-right', 'url'=>route('sale-orders.remove-item', [$saleOrder->id, $order_detail_misc->id])]) }}
                                    <button type="submit" class="btn btn-danger waves-effect waves-light" onclick="return confirm('Are you sure you want to remove from order?')">X</button>
                                    {{ Form::close() }}
                                @endif
                            </td>
                        </tr>

                    @endforeach


                    @if(count($saleOrder->tax_passed_on()))

                        @foreach($saleOrder->tax_passed_on() as $tax_type_name => $tax_amounts_by_uom)

                            @foreach($tax_amounts_by_uom as $tax_uom => $tax_amounts)

                            <tr>
                                <td colspan="3"></td>

                                <td>{{ $tax_type_name }}</td>

                                <td></td>
                                <td colspan="2">{{ $tax_amounts["weight"] }} {{ $tax_uom }} @ {{ display_currency(-$tax_amounts["line_tax_rate"]) }}</td>

                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    {{ display_currency(-$tax_amounts['total_line_tax_amount']) }}
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    {{--@if($saleOrder->isOpen())--}}
                                        {{--{{ Form::open(['class'=>'form-horizontal pull-right', 'url'=>route('sale-orders.remove-item', [$saleOrder->id, $order_detail_misc->id])]) }}--}}
                                        {{--<button type="submit" class="btn btn-danger waves-effect waves-light" onclick="return confirm('Are you sure you want to remove from order?')">X</button>--}}
                                        {{--{{ Form::close() }}--}}
                                    {{--@endif--}}
                                </td>
                            </tr>

                            @endforeach

                        @endforeach


                    @endif


    </tbody>

    @if($saleOrder->excise_tax_pre_discount)

        <tfoot>

        <tr>
            <td class="text-right" colspan="11"><strong>Subtotal:</strong></td>
            <td colspan="4"><strong>{{ display_currency($saleOrder->order_details->sum('subtotal')) }}</strong></td>
        </tr>

        @if($saleOrder->tax)
            <tr>
                <td class="text-right" colspan="11"><strong>Excise Tax @ 27%:</strong></td>
                <td colspan="4"><strong>{{ display_currency($saleOrder->tax) }}</strong></td>
            </tr>
        @endif

        @if($saleOrder->tax)
            <tr>
                <td class="text-right" colspan="11"><strong>Subtotal w/ Excise Tax:</strong></td>
                <td colspan="4"><strong>{{ display_currency($saleOrder->subtotal + $saleOrder->tax) }}</strong></td>
            </tr>
        @endif

        @if($saleOrder->discount)
            <tr>
                <td class="text-right" colspan="11"><strong>Discount:</strong><br> {{ ($saleOrder->discount_description?:'') }}</td>
                <td colspan="4"><strong class="text-danger">({{ display_currency($saleOrder->discount) }})</strong></td>
            </tr>
        @endif

        <tr>

            <td class="text-right" colspan="11"><strong>Order Total:</strong></td>
            <td colspan="4"><strong>{{ display_currency($saleOrder->total) }}</strong></td>
        </tr>

        </tfoot>

    @else

        <tfoot>

        <tr>
            <td class="text-right" colspan="11"><strong>Subtotal:</strong></td>
            <td colspan="4"><strong>{{ display_currency($saleOrder->subtotal) }}</strong></td>
        </tr>

        @if($saleOrder->discount)
            <tr>
                <td class="text-right" colspan="11"><strong>Discount:</strong><br> {{ ($saleOrder->discount_description?:'') }}</td>
                <td colspan="4"><strong class="text-danger">({{ display_currency($saleOrder->discount) }})</strong></td>
            </tr>

            <tr>
                <td class="text-right" colspan="11"><strong>Subtotal w/ Discount:</strong></td>
                <td colspan="4"><strong>{{ display_currency($saleOrder->order_details->sum('subtotal') - $saleOrder->discount) }}</strong></td>
            </tr>

        @endif

        @if($saleOrder->tax)
            <tr>
                <td class="text-right" colspan="11"><strong>Excise Tax @ 27%:</strong></td>
                <td colspan="4"><strong>{{ display_currency($saleOrder->tax) }}</strong></td>
            </tr>
        @endif

        <tr>

            <td class="text-right" colspan="11"><strong>Order Total:</strong></td>
            <td colspan="4"><strong>{{ display_currency($saleOrder->total) }}</strong></td>
        </tr>

        </tfoot>

    @endif

</table>
</div>


        </div>
    </div>
</div>
@endsection