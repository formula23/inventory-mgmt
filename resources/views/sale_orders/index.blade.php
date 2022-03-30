@extends('layouts.app')


@section('content')


    <div class="row">

        <div class="col-xl-6 hidden-print">

            {{ Form::open(['route' => 'sale-orders.index', 'method' => 'get']) }}

            <div class="card">

                <div class="card-header cursor-pointer" role="tab" id="filters" >

                    <div class="row">
                        <div class="col-md-3">
                            <a href="#collapse-filters" data-toggle="collapse"><strong><i class="ti-arrow-circle-down"></i> Filters</strong></a>
                            <a href="{{ route('sale-orders.reset-filters') }}" class="small ml-2">Reset</a>
                        </div>
                        <div class="col-md-9">
                            @if($filters)
                                @foreach($filters as $filter=>$vals)
                                    <span style="margin-right: 15px;">{!! display_filters($filter, $vals) !!}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>

                <div id="collapse-filters" class="card-block" role="tabpanel" aria-labelledby="collapse-filters" >

                    <div class="row">

                        <div class="col-lg-4">
                            <div class="form-group">
                                {{--<label for="balance">Order :</label>--}}
                                {{--<dt class="col-lg-5 text-lg-right">Status:</dt>--}}
                                {{--<dd class="col-lg-12">--}}
                                <select id="balance" name="filters[status]" class="form-control">
                                    <option value="">- Order Status -</option>
                                    <option value="open"{{ (isset($filters['status']) ? ($filters['status'] == 'open' ? 'selected' : '' ) : '') }}>Open</option>
                                    <option value="ready for delivery"{{ (isset($filters['status']) ? ($filters['status'] == 'ready for delivery' ? 'selected' : '' ) : '') }}>Ready for delivery</option>
                                    <option value="in-transit"{{ (isset($filters['status']) ? ($filters['status'] == 'in-transit' ? 'selected' : '' ) : '') }}>In-Transit</option>
                                    <option value="delivered"{{ (isset($filters['status']) ? ($filters['status'] == 'delivered' ? 'selected' : '' ) : '') }}>Delivered</option>
                                    {{--<option value="returned"{{ (isset($filters['status']) ? ($filters['status'] == 'returned' ? 'selected' : '' ) : '') }}>Returned</option>--}}
                                    {{--<option value="rejected"{{ (isset($filters['status']) ? ($filters['status'] == 'rejected' ? 'selected' : '' ) : '') }}>Rejected</option>--}}
                                </select>
                                {{--</dd>--}}
                            </div>

                            <div class="form-group">
                                <label for="balance">Order Balance:</label>

                                <select id="balance" name="filters[balance]" class="form-control">
                                    <option value="">- Balance -</option>
                                    <option value="yes"{{ (isset($filters['balance']) ? ($filters['balance'] == 'yes' ? 'selected' : '' ) : '') }}>Yes</option>
                                    <option value="no"{{ (isset($filters['balance']) ? ($filters['balance'] == 'no' ? 'selected' : '' ) : '') }}>No</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="balance">Sales Comm. Paid:</label>

                                <select id="balance" name="filters[sales_comm_paid]" class="form-control">
                                    <option value="">- Sales Comm. Paid -</option>
                                    <option value="yes"{{ (isset($filters['sales_comm_paid']) ? ($filters['sales_comm_paid'] == 'yes' ? 'selected' : '' ) : '') }}>Yes</option>
                                    <option value="no"{{ (isset($filters['sales_comm_paid']) ? ($filters['sales_comm_paid'] == 'no' ? 'selected' : '' ) : '') }}>No</option>
                                </select>
                            </div>

                            {{--<div class="form-group">--}}
                                {{--<label for="balance">Customer Type:</label>--}}
                                {{--@foreach($customer_types as $customer_type)--}}
                                    {{--<div class="checkbox">--}}
                                        {{--<input id="checkbox_{{ clean_string($customer_type) }}" type="checkbox" name="filters[customer_type][{{$customer_type}}]" value="{{ $customer_type }}" {{ (isset($filters['customer_type']) ? (in_array($customer_type, array_keys($filters['customer_type']))?'checked':''):'') }}>--}}

                                        {{--<label for="checkbox_{{clean_string($customer_type)}}">--}}
                                            {{--<span class="">{{ ucwords($customer_type) }}</span>--}}
                                        {{--</label>--}}
                                    {{--</div>--}}
                                {{--@endforeach--}}

                            {{--</div>--}}

                        </div>

                        <div class="col-lg-4">
                            {{--<dl class="row">--}}
                            {{--<dt class="col-lg-5 text-lg-right">Date Preset:</dt>--}}
                            {{--<dd class="col-lg-12">--}}
                            <div class="form-group">
                                <select id="date_preset" name="filters[date_preset]" class="form-control">
                                    <option value="">- Date Presets -</option>
                                    @for($i=0; $i<=3; $i++)
                                        <option value="{{ \Carbon\Carbon::now()->firstOfMonth()->subMonth($i)->format('m-Y') }}"{{ (isset($filters['date_preset']) ? (\Carbon\Carbon::now()->firstOfMonth()->subMonth($i)->format('m-Y') == $filters['date_preset'] ? 'selected' : '' ) : '') }}>{{ \Carbon\Carbon::now()->firstOfMonth()->subMonth($i)->format('F, Y') }}</option>
                                    @endfor
                                </select>
                            </div>

                            {{--<dt class="col-lg-5 text-lg-right"></dt>--}}
                            {{--<dd class="col-lg-12">--}}
                            {{---- OR ----}}
                            {{--</dd>--}}

                            {{--<dt class="col-lg-5 text-lg-right">Custom Date:</dt>--}}
                            <div class="form-group">
                                {{--<dd class="col-lg-12">--}}
                                From:<input class="form-control" type="date" name="filters[from_date]" value="{{ (isset($filters['from_date']) ? $filters['from_date'] : '') }}">
                                To:<input class="form-control" type="date" name="filters[to_date]" value="{{ (isset($filters['to_date']) ? $filters['to_date'] : '') }}">
                            </div>

                            <div class="form-group">
                                {{--<label for="balance">SO#:</label>--}}
                                <input class="form-control" type="text" placeholder="SO#" name="filters[ref_number]" value="{{ (isset($filters['ref_number']) ? $filters['ref_number'] : '') }}">

                            </div>

                            {{--<div class="form-group">--}}
                                {{--<input class="form-control" type="text" placeholder="Manfiest#" name="filters[manifest_no]" value="{{ (isset($filters['manifest_no']) ? $filters['manifest_no'] : '') }}">--}}

                            {{--</div>--}}

                            </dl>

                        </div>

                        <div class="col-lg-4">
                            <div class="row form-group">
                                {{--<dt class="col-lg-3 text-lg-right">Customers:</dt>--}}
                                <div class="col-lg-12">

                                    <input id="customer" type="text" list="customer_list" class="form-control" value="{{ ( ! empty($filter_customer) ? $filter_customer->name : '') }}" placeholder="-- Customer (Bill To) --">

                                    <input type="hidden" id="filter_customer_id" name="filters[customer]" value="">

                                    <datalist id="customer_list">
                                        @foreach($customers as $customer )
                                            <option value="{{ $customer->name }}" id="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach

                                        {{--@foreach($customers as $customer)--}}
                                        {{--<option value="{{ $customer->id }}" @if ($customer->id == $selected_customer->id) selected="selected" @endif>{{ $customer->name }}</option>--}}
                                        {{--@endforeach--}}
                                    </datalist>

                                    {{--<select id="customer" name="filters[customer]" class="form-control">--}}
                                        {{--<option value="">- Customer (Bill-to) -</option>--}}
                                        {{--@foreach($customers as $customer)--}}
                                            {{--<option value="{{ $customer->id }}"{{ (isset($filters['customer']) ? ($customer->id == $filters['customer'] ? 'selected' : '' ) : '') }}>{{$customer->name}}{{ ($customer->active?'':' (INACTIVE)') }}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}

                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-lg-12">
                                    @can('so.filters.salesrep')
                                        <select id="sales_rep" name="filters[sales_rep]" class="form-control">
                                            <option value="">- Sales Rep -</option>
                                            <option value="None"{{ (isset($filters['sales_rep']) ? ("None" == $filters['sales_rep'] ? 'selected' : '' ) : '') }}>None</option>
                                            @foreach($sales_reps as $sales_rep)
                                                <option value="{{ $sales_rep->id }}"{{ (isset($filters['sales_rep']) ? ($sales_rep->id == $filters['sales_rep'] ? 'selected' : '' ) : '') }}>{{$sales_rep->name}}</option>
                                            @endforeach
                                        </select>
                                    @endcan
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-lg-12">
                                    <select id="broker" name="filters[broker_id]" class="form-control">
                                        <option value="">- Broker -</option>
                                        @foreach($brokers as $broker_id=>$broker_name)
                                            <option value="{{ $broker_id }}"{{ (isset($filters['broker_id']) ? ($broker_id == $filters['broker_id'] ? 'selected' : '' ) : '') }}>{{$broker_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{--<div class="row form-group">--}}
                                {{--<div class="col-lg-12">--}}
                                    {{--<select id="sale_type" name="filters[sale_type]" class="form-control">--}}
                                        {{--<option value="">- Sale Type -</option>--}}
                                        {{--@foreach(config('highline.sale_types') as $sale_type)--}}
                                            {{--<option value="{{ $sale_type }}"{{ (isset($filters['sale_type']) ? ($sale_type == $filters['sale_type'] ? 'selected' : '' ) : '') }}>{{ ucwords($sale_type) }}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}



                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="Notes" name="filters[notes]" value="{{ (isset($filters['notes']) ? $filters['notes'] : '') }}">

                            </div>

                            <div class="form-group">
                                <label for="balance">Sale Type:</label>
                                <div class="row">
                                @foreach(config('highline.sale_types') as $sale_type)
                                    <div class="checkbox col-6">
                                        <input id="checkbox_{{ clean_string($sale_type) }}" type="checkbox" name="filters[sale_type][{{$sale_type}}]" value="{{ $sale_type }}" {{ (isset($filters['sale_type']) ? (in_array($sale_type, array_keys($filters['sale_type']))?'checked':''):'') }}>

                                        <label for="checkbox_{{clean_string($sale_type)}}">
                                            <span class="">{{ ucwords($sale_type) }}</span>
                                        </label>
                                    </div>
                                @endforeach
                                </div>
                            </div>

                        </div>

                    </div>
                    {{--<hr>--}}
                    <button type="submit" class="btn btn-primary waves-effect waves-light mr-1 pull-right">Filter</button>

                </div>

            </div>

            {{ Form::close() }}

        </div>

        <div class="col-xl-6">
            <div class="card mb-3">

                <div class="card-header" role="tab" id="filters" >
                    <strong>Summary</strong>
                </div>

                <div class="card-block">

                    <div class="row">
                        <div class="col-7">
                            <dl class="row">

                                <dt class="col-6  text-right">Receivables:</dt>
                                <dd class="col-6 ">{{ display_currency($sale_orders->sum('balance')) }}</dd>

                                @can('batches.show.cost')
                                <dt class="col-6  text-right">Total Cost:</dt>
                                <dd class="col-6 ">{{ display_currency($sale_orders->sum('cost')) }}</dd>

                                <dt class="col-6  text-right">Cost Breakdown:</dt>
                                <dd class="col-6 ">
                                    @foreach($cost_collection as $fund => $ods)
                                        {{ $fund }}: {{ display_currency($ods->sum('cost')) }}<br>
                                        @endforeach
                                </dd>

                                @endcan

                                <dt class="col-6  text-right">Subtotal:</dt>
                                <dd class="col-6 ">{{ display_currency($sale_orders->sum('subtotal')) }}</dd>
                                <dt class="col-6  text-right">Discounts:</dt>
                                <dd class="col-6 text-danger">({{ display_currency($sale_orders->sum('discount')) }})</dd>
                                <dt class="col-6  text-right">Subtotal after discounts:</dt>
                                <dd class="col-6 ">{{ display_currency($sale_orders->sum('subtotal') - $sale_orders->sum('discount')) }}</dd>
                                {{--<dt class="col-6  text-right">Excise Tax:</dt>--}}
                                {{--<dd class="col-6 ">{{ display_currency($sale_orders->sum('tax')) }}</dd>--}}
                                <dt class="col-6  text-right">Revenue:</dt>
                                <dd class="col-6 ">{{ display_currency($sale_orders->sum('revenue')) }}</dd>
                                @can('batches.show.cost')
                                <dt class="col-6  text-right">Gross Profit:</dt>
                                <dd class="col-6 ">{{ display_currency($sale_orders->sum('margin')) }}</dd>
                                <dt class="col-6  text-right">Margin %:</dt>
                                <dd class="col-6 ">{{ ($sale_orders->sum('margin') && $sale_orders->sum('subtotal') ? number_format($sale_orders->sum('margin') / ($sale_orders->sum('subtotal')) * 100, 2) : 0) }}%</dd>
                                @endcan

                            </dl>
                        </div>
                        <div class="col-5">
                            <dl class="row">
                                <dt class="col-4  text-right">Sold:</dt>
                                <dd class="col-8 ">
                                    @foreach($total_units_sold as $uom=>$sold)
                                        {{ $sold }} {{$uom}}<br>
                                    @endforeach
                                </dd>
                                <dt class="col-4  text-right">Total Lbs:</dt>
                                <dd class="col-8 ">{{ round($total_lbs_sold, 2) }} {{ str_plural('lb', $total_lbs_sold) }}</dd>
                            </dl>

                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>


    <div class="row hidden-print mb-4">
    </div>


    <div class="row">

        <div class="col-lg-12">

            <div class="card-box">

                <ul class="nav nav-tabs">

                    @foreach(config('highline.order_statuses') as $order_status)

                    <li class="nav-item">
                        <a href="#{{ str_slug($order_status) }}" data-toggle="tab" aria-expanded="false" class="nav-link{{ ( ( (empty($filters['status']) && $loop->iteration == 1) || (!empty($filters['status']) && $filters['status'] == $order_status) )?' active':'') }}">
                            {{ ucwords($order_status) }}
                        </a>
                    </li>

                    @endforeach

                        <li class="nav-item">
                            <a href="#all" data-toggle="tab" aria-expanded="false" class="nav-link">
                                All
                            </a>
                        </li>

                </ul>

                <div class="tab-content">

                    @foreach(config('highline.order_statuses') as $order_status)

                        <div class="tab-pane fade {{ ( ( (empty($filters['status']) && $loop->iteration == 1) || (!empty($filters['status']) && $filters['status'] == $order_status) ) ? ' active show':'') }}" id="{{ str_slug($order_status) }}" aria-expanded="false">

                        @if( ! empty($sale_orders_grouped_by_status[$order_status]))

                        <div class="table-responsive">

                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <div id="datatable-buttons-{{ str_slug($order_status) }}" class="pull-right"></div>
                                </div>
                            </div>

                            <table id="table-{{ str_slug($order_status) }}" class="table table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>QB</th>
                                    <th></th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>SO#</th>
                                    {{--<th>Manifest#</th>--}}
                                    <th>Type</th>
                                    <th>Customer (Bill-to)</th>
                                    <th>Sales Rep</th>
                                    <th>Broker</th>
                                    <th>Pkg's</th>
                                    {{--<th>Inv#</th>--}}
                                    {{--<th>Legal</th>--}}
                                    <th>Units</th>
                                    {{--<th>Notes</th>--}}

                                    {{--<th>Comm. Paid</th>--}}
{{--                                    @can('batches.show.cost')--}}
                                        {{--<th>Cost</th>--}}
                                    {{--@endcan--}}
                                    {{--<th>Revenue</th>--}}
                                    {{--<th>Subtotal</th>--}}
                                    {{--<th>Discount</th>--}}
                                    {{--<th>Tax</th>--}}
                                    <th>Total</th>
                                    <th>Balance</th>

                                    @role('admin')
                                    <th>Margin</th>
                                    @endrole
                                    {{--<th></th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sale_orders_grouped_by_status[$order_status] as $sale_order)
                                    <tr class="{{ ($sale_order->hasOrderDetailWithNoPrice()?"table-danger":"") }}">
                                        <td>{{ $sale_order->id }} </td>
                                        <td>
                                            <a class="qb_update" href="{{ route('sale-orders.update', ['id'=>$sale_order->id]) }}" data-in_qb="{{ $sale_order->in_qb }}">
                                                <i class="mdi mdi-checkbox-marked text-{{ ($sale_order->in_qb?"success":"danger") }}"></i>
                                            </a>
                                        </td>
                                        <td><a href="{{ route('sale-orders.invoice', $sale_order) }}"><i class="ion-printer"></i></a></td>
                                        <td><span class="badge badge-{{ status_class($sale_order->status) }}">{{ ucwords($sale_order->status) }}</span></td>
                                        <td scope="row">{{ $sale_order->txn_date->format('m/d/Y') }}</td>
                                        <td class="text-nowrap"><a href="{{ route('sale-orders.show', ['id'=>$sale_order->id]) }}">{{ $sale_order->ref_number }}</a></td>
                                        {{--<td class="text-nowrap">--}}
                                            {{--@if($sale_order->manifest_no)--}}
                                                {{--<a href="https://ca.metrc.com/reports/transfers/C11-0000347-LIC/manifest?id={{ $sale_order->manifest_no }}" target="_blank">{{ $sale_order->manifest_no }} <i class="ion ion-share"></i> </a>--}}
                                                {{--<br>--}}
                                            {{--@endif--}}
                                        {{--</td>--}}
                                        <td>{{ ucwords($sale_order->sale_type) }}</td>
                                        <td>
                                            {!! $sale_order->customer->name  !!}
                                            @if(!empty($sale_order->destination_license) && ($sale_order->destination_license->id != $sale_order->customer_id))
                                                <br><small>Ship To: {{ $sale_order->destination_license->name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ ($sale_order->sales_rep?$sale_order->sales_rep->name:'--') }}</td>
                                        <td>{{ ($sale_order->broker?$sale_order->broker->name:'--') }}</td>
                                        <td>{{ $sale_order->order_details_cog->count() }}</td>
                                        {{--<td class="text-nowrap">{{ $sale_order->inv_number }}</td>--}}


                                        {{--                                <td>{{ (! empty($sale_order->customer->details['business_name'])?$sale_order->customer->details['business_name']:'--')  }}</td>--}}
                                        <td>{{ (!empty($units_purchased[$sale_order->id]) ? implode(", ", $units_purchased[$sale_order->id]) : '--') }}</td>
{{--                                        <td>{{ $sale_order->notes }}</td>--}}
                                        {{--                                <td>{{ (!empty($totals[$sale_order->id]['lbs']) ? $totals[$sale_order->id]['lbs'].' '.str_plural('lb', $totals[$sale_order->id]['lbs']) : '--') }}</td>--}}


                                        {{--                                <td>{{ ($sale_order->sales_commission_details->count()?'Yes':'No') }}</td>--}}
{{--                                        @can('batches.show.cost')<td>{{ display_currency($sale_order->cost) }}</td>@endcan--}}
                                        {{--<td>{{ display_currency($sale_order->revenue) }}</td>--}}
                                        {{--<td>{{ display_currency($sale_order->subtotal) }}</td>--}}
                                        {{--<td>{{ display_currency($sale_order->discount) }}</td>--}}
                                        {{--<td>{{ display_currency($sale_order->tax) }}</td>--}}
                                        <td>{{ display_currency($sale_order->total) }}</td>
                                        <td>{{ display_currency($sale_order->balance) }}</td>

                                        @role('admin')
                                        <td class="text-{{ ($sale_order->margin > 0?'success':'danger') }}">{{ display_currency($sale_order->margin) }} <small>({{ $sale_order->margin_pct }}%) <i class="ion-arrow-{{ ($sale_order->margin>0?'up':'down') }}-c"></i> </small></td>
                                        @endrole
                                        {{--                                <td><a href="{{ route('sale-orders.show', ['id'=>$sale_order->id]) }}"><i class="ion-ios7-search-strong"></i></a></td>--}}
                                    </tr>
                                @endforeach

                                </tbody>

                            </table>
                        </div>


                            @else

                            <p>No Data</p>

                        @endif
                        </div>

                    @endforeach


                        <div class="tab-pane fade " id="all" aria-expanded="false">

                                <div class="table-responsive">

                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <div id="datatable-buttons-all" class="pull-right"></div>
                                        </div>
                                    </div>

                                    <table id="table-all" class="table table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>QB</th>
                                            <th></th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>SO#</th>
                                            {{--<th>Manifest#</th>--}}
                                            <th>Type</th>
                                            <th>Customer</th>
                                            <th>Sales Rep</th>
                                            <th>Broker</th>
                                            <th>Pkg's</th>
                                            {{--<th>Inv#</th>--}}
                                            {{--<th>Legal</th>--}}
                                            <th>Units</th>
                                            {{--<th>Notes</th>--}}

                                            {{--<th>Comm. Paid</th>--}}
{{--                                            @can('batches.show.cost')--}}
                                                {{--<th>Cost</th>--}}
                                            {{--@endcan--}}
                                            {{--<th>Revenue</th>--}}
                                            {{--<th>Subtotal</th>--}}
                                            {{--<th>Discount</th>--}}
                                            {{--<th>Tax</th>--}}
                                            <th>Total</th>
                                            {{--<th>Balance</th>--}}

                                            @role('admin')
                                            <th>Margin</th>
                                            @endrole
                                            {{--<th></th>--}}
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($sale_orders as $sale_order)
                                            <tr class="">
                                                <td>{{ $sale_order->id }}</td>
                                                <td>
                                                    <a class="qb_update" href="{{ route('sale-orders.update', ['id'=>$sale_order->id]) }}" data-in_qb="{{ $sale_order->in_qb }}">
                                                        <i class="mdi mdi-checkbox-marked text-{{ ($sale_order->in_qb?"success":"danger") }}"></i>
                                                    </a>
                                                </td>
                                                <td><a href="{{ route('sale-orders.invoice', $sale_order) }}"><i class="ion-printer"></i></a></td>
                                                <td><span class="badge badge-{{ status_class($sale_order->status) }}">{{ ucwords($sale_order->status) }}</span></td>
                                                <td scope="row">{{ $sale_order->txn_date->format('m/d/Y') }}</td>
                                                <td class="text-nowrap"><a href="{{ route('sale-orders.show', ['id'=>$sale_order->id]) }}">{{ $sale_order->ref_number }}</a></td>
                                                {{--<td class="text-nowrap">--}}
                                                    {{--@if($sale_order->manifest_no)--}}
                                                        {{--<a href="https://ca.metrc.com/reports/transfers/manifest?id={{ $sale_order->manifest_no }}" target="_blank">{{ $sale_order->manifest_no }} <i class="ion ion-share"></i> </a>--}}
                                                        {{--<br>--}}
                                                    {{--@endif--}}
                                                {{--</td>--}}
                                                <td>{{ ucwords($sale_order->sale_type) }}</td>
                                                {{--<td class="text-nowrap">{{ $sale_order->inv_number }}</td>--}}


                                                <td>
                                                    {!! $sale_order->customer->name  !!}

                                                    @if(!empty($sale_order->destination_license) && ($sale_order->destination_license->user_id != $sale_order->customer_id))
                                                        <br><small>Ship To: {{ $sale_order->destination_license->name }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ ($sale_order->sales_rep?$sale_order->sales_rep->name:'--') }}</td>
                                                <td>{{ ($sale_order->broker?$sale_order->broker->name:'--') }}</td>
                                                <td>{{ $sale_order->order_details_cog->count() }}</td>
                                                {{--                                <td>{{ (! empty($sale_order->customer->details['business_name'])?$sale_order->customer->details['business_name']:'--')  }}</td>--}}
                                                <td>{{ (!empty($units_purchased[$sale_order->id]) ? implode(", ", $units_purchased[$sale_order->id]) : '--') }}</td>
{{--                                                <td>{{ $sale_order->notes }}</td>--}}
                                                {{--                                <td>{{ (!empty($totals[$sale_order->id]['lbs']) ? $totals[$sale_order->id]['lbs'].' '.str_plural('lb', $totals[$sale_order->id]['lbs']) : '--') }}</td>--}}


                                                {{--                                <td>{{ ($sale_order->sales_commission_details->count()?'Yes':'No') }}</td>--}}
{{--                                                @can('batches.show.cost')<td>{{ display_currency($sale_order->cost) }}</td>@endcan--}}
                                                {{--<td>{{ display_currency($sale_order->revenue) }}</td>--}}
{{--                                                <td>{{ display_currency($sale_order->subtotal) }}</td>--}}
{{--                                                <td>{{ display_currency($sale_order->discount) }}</td>--}}
                                                {{--<td>{{ display_currency($sale_order->tax) }}</td>--}}
                                                <td>{{ display_currency($sale_order->total) }}</td>
                                                {{--<td>{{ display_currency($sale_order->balance) }}</td>--}}

                                                @role('admin')
                                                <td class="text-{{ ($sale_order->margin > 0?'success':'danger') }}">{{ display_currency($sale_order->margin) }} <small>({{ $sale_order->margin_pct }}%) <i class="ion-arrow-{{ ($sale_order->margin>0?'up':'down') }}-c"></i> </small></td>
                                                @endrole
                                                {{-- <td><a href="{{ route('sale-orders.show', ['id'=>$sale_order->id]) }}"><i class="ion-ios7-search-strong"></i></a></td>--}}
                                            </tr>
                                        @endforeach

                                        </tbody>

                                    </table>
                                </div>

                        </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@section('css')

    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">

@endsection

@section('js')



    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('plugins/moment/min/moment.min.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $('#customer').change(function () {

                var el=$("#customer")[0];  //used [0] is to get HTML DOM not jquery Object
                var dl=$("#customer_list")[0];

                if(el.value.trim() != '') {
                    var opSelected = dl.querySelector(`[value="${el.value}"]`);

                    $('#filter_customer_id').val(opSelected.getAttribute('id'));

                    // window.location = window.location.href + '/' + opSelected.getAttribute('id');
{{--                    window.location = '{{ route('batches.show', $batch->ref_number) }}/customer/' + opSelected.getAttribute('id');--}}
                }

            });

            $.fn.dataTable.moment('MM/DD/YYYY');

            var order_status = @json(config('highline.order_statuses')).concat(["all"]);

            console.log(order_status);
            for (var i = 0; i < order_status.length; i++) {
                // console.log(order_status[i].replace(/\s+/g, '-'));
                // console.log($('#table-'+order_status[i].replace(/\s+/g, '-') ));

                var table = $('#table-'+order_status[i].replace(/\s+/g, '-') ).DataTable({
                    lengthChange: true,
                    paging: true,
                    "order": [[ 0, "desc" ]],
                    "displayLength": 25,
                    buttons: ['excel', 'pdf', 'colvis']
                });

                table.buttons().container().appendTo($('#datatable-buttons-'+order_status[i].replace(/\s+/g, '-')));

                //Do something
            }

            // $('[type="date"]').datepicker();



        } );

    </script>


@endsection