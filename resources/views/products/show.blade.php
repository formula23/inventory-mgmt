@extends('layouts.app')


@section('content')

<div class="row">

        <div class="col-lg-8 {{ $product->status=='sold'?'col-lg-12':'' }}">
            <div class="card-box">

                <div class="row">

                    <div class="col-lg-6">
                        <h1 class="header-title">{{ $product->batch->category->name }} : {{ $product->batch->name }} {{ $product->sold_as_name ? '('.$product->sold_as_name.')' : '' }}</h1>
                        <p>{{ $product->batch->description }}</p>
                    </div>
                    <div class="col-lg-6">
                        @if( ! empty($product->batch->sales_notes))
                        <h6>Sales Notes:</h6>
                        <p>{{ $product->batch->sales_notes }}</p>
                        @endif

                        @if(!empty($product->batch->character))
                        <h6>Characters:</h6>
                        <p>{{ implode(", ", $product->batch->character) }}</p>
                        @endif
                    </div>

                </div>


                <hr>

                <dl class="row">
                    <dt class="col-5 text-right">Status:</dt>
                    <dd class="col-7"><span class="badge badge-{{ status_class($product->status) }}">{!! display_status($product->status) !!}</span></dd>

                    <dt class="col-5 text-right">SKU:</dt>
                    <dd class="col-7">{{ $product->ref_number }}</dd>

                    @if($product->transporter)
                    <dt class="col-5 text-right">Transporter:</dt>
                    <dd class="col-7">{{ $product->transporter->name }}</dd>
                        <dt class="col-5 text-right">Picked Up:</dt>
                        <dd class="col-7">{{ $product->transit_at->format('m/d/Y H:i:s') }}</dd>
                    @endif

                    @if($product->batch->min_flex)
                    <dt class="col-5 text-right">Min Sale Price:</dt>
                    <dd class="col-7">{{ display_currency($product->batch->suggested_sale_price - $product->batch->min_flex) }}</dd>
                    @endif

                    <dt class="col-5 text-right">Suggested Sale Price:</dt>
                    <dd class="col-7">

                        <span class="text-hint">(- {{ display_currency($product->batch->min_flex) }})</span> <br>{{ display_currency($product->batch->suggested_sale_price) }}<br>(+{{ display_currency($product->batch->max_flex) }})

                    </dd>

                    @if($product->batch->max_flex)
                        <dt class="col-5 text-right">Max Sale Price:</dt>
                        <dd class="col-7">{{ display_currency($product->batch->suggested_sale_price + $product->batch->max_flex) }}</dd>
                    @endif

                </dl>


                <hr>

                    @if(Auth::user()->isAdmin())
                    <dl class="row">
                        <dt class="col-5 text-right">Purchase Date:</dt>
                        <dd class="col-7">{{ $product->batch->purchase_order->created_at->format('m/d/Y H:i:s') }}</dd>

                        <dt class="col-5 text-right">PO:</dt>
                        <dd class="col-7"><a href="{{ route('purchase-orders.show', ['purchase-order'=>$product->batch->purchase_order->id]) }}">{{ $product->batch->purchase_order->ref_number }}</a></dd>


                        @if($product->sale_order)

                            <dt class="col-5 text-right">Sale Date:</dt>
                            <dd class="col-7">{{ $product->sale_order->created_at->format('m/d/Y H:i:s') }}</dd>

                            <dt class="col-5 text-right">SO:</dt>
                            <dd class="col-7"><a href="{{ route('sale-orders.show', $product->sale_order->id) }}">{{ $product->sale_order->ref_number }}</a></dd>
                        @endif

                        <dt class="col-5 text-right">Purchase Price:</dt>
                        <dd class="col-7">{{ display_currency($product->batch->unit_purchase_price) }}</dd>

                        <dt class="col-5 text-right">Actual Sale Price:</dt>
                        <dd class="col-7">{{ display_currency($product->unit_sale_price) }}</dd>
                    </dl>
                    @endif



            </div>
        </div>

        @if($product->status != 'sold')

        <div class="col-lg-4">
            <div class="card-box">
                <h2 class="header-title">Actions</h2>
                <hr>

                @if($product->status == 'inventory')

                    {{ Form::open(['url'=>route('products.pickup', ['product'=>$product->ref_number])]) }}

                    <button type="submit" class="btn btn-success waves-effect waves-light conf_action" id="pickup">Pick-Up</button>

                    {{ Form::close() }}


                @elseif($product->status == 'transit')

                    @can('products.show.sellreturn', $product)

                        {{ Form::open(['url'=>route('products.sell_return', ['product'=>$product->ref_number])]) }}

                        <div class="form-group row">
                            <label class="col-12 col-form-label">Action</label>
                            <div class="col-12">
                                <select class="form-control" name="action" id="sell_return_action">
                                    <option value="sell">Sell</option>
                                    <option value="return">Return</option>
                                </select>
                            </div>
                        </div>

                        <div id="sell_container">

                            @if( ! $open_sales_orders->count())

                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Customer</label>
                                    <div class="col-12">
                                        <select class="form-control" name="customer_id">
                                            <option value="">-- Select --</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        <a href="{{ route('users.create') }}?role=Customer" class="btn btn-success waves-effect waves-light">Add Customer</a>
                                    </div>
                                </div>

                            @else

                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Open Sale Orders</label>
                                    <div class="col-12">
                                        <select class="form-control" name="sale_order_id">
                                            <option value="">-- Select --</option>
                                            @foreach($open_sales_orders as $open_sales_order)
                                                <option value="{{ $open_sales_order->id }}">{{ $open_sales_order->customer->name }} - {{ $open_sales_order->txn_date->format('m/d/Y') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            @endif

                            <hr>

                            <div class="form-group row">
                                <label class="col-12 col-form-label">Sold As Name</label>
                                <div class="col-12">
                                    <input id="sold_as_name" type="text" value="{{ $product->batch->name }}" name="sold_as_name" class="form-control" style="display: block;" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-12 col-form-label">Sale Price</label>
                                <div class="input-group bootstrap-touchspin col-12">
                                    <span class="input-group-addon bootstrap-touchspin-prefix">$</span>
                                    <input id="sale_price" type="number" value="" name="sale_price" class="form-control" style="display: block;">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success waves-effect waves-light">Save</button>

                        {{ Form::close() }}

                    @endcan


                @elseif($product->status == 'returned')

                    {{ Form::open(['url'=>route('products.approve-return', ['product'=>$product->ref_number])]) }}

                    <button type="submit" class="btn btn-success waves-effect waves-light conf_action" id="approve_return">Approve Return</button>

                    {{ Form::close() }}

                @endif

            </div>
        </div>

        @endif
    </div>

@endsection