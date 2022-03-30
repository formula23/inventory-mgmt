@extends('layouts.app')

@section('content')

    {{--<h1 class="header-title">Batch ID: {{ $batch->ref_number }} </h1>--}}

    @can('batches.edit')
    <a href="{{ route('batches.edit', $batch->ref_number) }}" class="btn btn-primary waves-effect waves-light m-b-10">Edit</a>
    @endcan

    @can('batches.print.largelabel')
    @if($batch->isTopParent())
    <a href="{{ route('batches.labels', $batch->ref_number) }}" class="btn btn-primary waves-effect waves-light m-b-10">Print Labels</a>
    @endif
    @endcan

    @if($batch->testing_status=='Pending' && $batch->inventory)
    <a href="{{ route('batches.submit_for_testing', $batch->ref_number) }}" class="btn btn-primary waves-effect waves-light m-b-10">Submit for Testing</a>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">

                <h2>{{ $batch->category->name }}: {!! $batch->present()->branded_name !!}
                    {!! display_coa_icons($batch) !!}
                </h2>

                <div class="row">

                    <div class="col-xl-5">

                        @include('batches._batch_info', $batch)

                        <dl class="row">

                            {{--<dt class="col-xl-4 text-xl-right">Fund:</dt>--}}
                            {{--<dd class="col-xl-5">--}}
                                {{--<span>{{ $batch->fund->name }}</span>--}}
                            {{--</dd>--}}

                            <dt class="col-xl-4 text-xl-right">Status:</dt>
                            <dd class="col-xl-5">
                                <span class="badge badge-{{ status_class($batch->status) }}">{!! display_status($batch->status) !!}</span>
                            </dd>

                            {{--<dt class="col-xl-4 text-xl-right">Cultivator:</dt>--}}
                            {{--<dd class="col-xl-5">--}}
                                {{--@if($batch->cultivator)--}}
    {{----}}
                                {{--<address>--}}
                                {{--<p>--}}
                                    {{--{!! $batch->cultivator->present()->name_address !!}<br>--}}
                                    {{--@if($batch->cultivator->cultivation_license)--}}
                                        {{--Lic# {{ $batch->cultivator->cultivation_license->first()->number }}--}}
                                    {{--@endif--}}
                                {{--</p>--}}
                                {{--</address>--}}
                                {{--@endif--}}
                            {{--</dd>--}}
    {{----}}
                            {{--<dt class="col-xl-4 text-xl-right">Cultivation Date:</dt>--}}
                            {{--<dd class="col-xl-5">{{ $batch->harvest_date }}</dd>--}}

                        </dl>
                        {{--<dl class="row">--}}
                                    {{--@if($batch->testing_laboratory_id)--}}
                                        {{--<dt class="col-xl-4 text-xl-right">Testing Laboratory:</dt>--}}
                                        {{--<dd class="col-xl-5">--}}
                                            {{--@if($batch->testing_laboratory)--}}
                                            {{--<address>--}}
                                                {{--<p>{!! $batch->testing_laboratory->present()->name_address !!}<br>--}}
                                                {{--Lic# {{ $batch->testing_laboratory->details['lab_license_number'] }}</p>--}}
                                            {{--</address>--}}
                                            {{--@endif--}}
                                        {{--</dd>--}}
                                    {{--@endif--}}

                                    {{--@if($batch->tested_at)--}}
                                    {{--<dt class="col-xl-4 text-xl-right">Laboratory Test Date:</dt>--}}
                                    {{--<dd class="col-xl-5">{{ $batch->tested_at->format(config('highline.date_format')) }}</dd>--}}
                                    {{--@endif--}}

                                    {{--<dt class="col-xl-4 text-xl-right">Testing Status:</dt>--}}
                                    {{--<dd class="col-xl-5"><span class="badge badge-{{ status_class($batch->testing_status) }}">{!! display_status($batch->testing_status) !!}</span></dd>--}}
                        {{--</dl>--}}

                        <dl class="row">
                                    <dt class="col-xl-4 text-xl-right">Available Inventory:</dt>
                                    <dd class="col-xl-5">
                                        {!! display_inventory($batch) !!}
                                    </dd>

                                <dt class="col-xl-4 text-xl-right">Packages:
                                </dt>
                                <dd class="col-xl-5">
{{--                                    {{ dd($batch->transfer_logs_prepack->sum('quantity_transferred')) }}--}}
                                    <a href="{{ route('batches.transfer-log', $batch->ref_number) }}">
                                        {{ $batch->transfer_logs_prepack->sum('quantity_transferred') }} {{ ($batch->wt_based?"g":$batch->uom) }}
                                    </a>

                                    @if($batch->canCreatePackages())
                                    <br><a href="{{ route('batches.transfer', $batch->ref_number) }}">Create</a>
                                    @endif

                                </dd>

                                <dt class="col-xl-4 text-xl-right">Pending:</dt>
                                <dd class="col-xl-5"><a href="{{ route('batches.sales', $batch->ref_number) }}">{{ ($batch->order_details_not_accepted->sum('units') - $batch->order_details_not_accepted->sum('units_accepted')) }} {{ $batch->uom }}</a></dd>

                                @can(['so.show','batches.show.sold'])
                                <dt class="col-xl-4 text-xl-right">Sold:</dt>
                                <dd class="col-xl-5"><a href="{{ route('batches.sales', $batch->ref_number) }}">{{ $batch->order_details->sum('units_accepted') }} {{ $batch->uom }}</a></dd>
                                @endcan

                                <dt class="col-xl-4 text-xl-right">Reconciled:</dt>
                                <dd class="col-xl-5">

                                    <a href="{{ route('batches.reconcile-log-batch', $batch->ref_number) }}">{{ $batch->transfer_logs_reconcile->sum('quantity_transferred') }} {{ $batch->uom }}</a>
                                    <br>
                                    <a href="{{ route('batches.reconcile-batch', $batch->ref_number) }}">Reconcile</a>
                                </dd>

                            {{--@if($batch->harvest_date)--}}

                            {{--@endif--}}

                            <dt class="col-xl-4 text-xl-right">Type:</dt>
                            <dd class="col-xl-5">{{ ($batch->type?:'--') }}</dd>

                            @can('batches.show.cost')
                            <dt class="col-xl-4 text-xl-right">Unit Cost:</dt>
                            <dd class="col-xl-5">{{ display_currency($batch->unit_price) }}</dd>

                            @if($batch->tax_rate)
                            <dt class="col-xl-4 text-xl-right">Pre-Tax Cost:</dt>
                            <dd class="col-xl-5">{{ display_currency($batch->preTaxCost) }}</dd>
                            @endif

                            @if($batch->tax_rate)
                                <dt class="col-xl-4 text-xl-right">Tax Rate:</dt>
                                <dd class="col-xl-5">{{ $batch->tax_rate->name }}<br>{{ display_currency($batch->tax_rate->amount) }} / {{ $batch->tax_rate->uom }}</dd>
                            @endif

                            @endcan

                            <dt class="col-xl-4 text-xl-right">Sugg. Sale Price:</dt>
                            <dd class="col-xl-5">
                                {{ display_currency($batch->suggested_unit_sale_price) }}<br>
                                <small>-{{ ($batch->min_flex?display_currency($batch->min_flex):0) }} / + {{ ($batch->max_flex?display_currency($batch->max_flex):0) }}</small>
                            </dd>

                            <dt class="col-xl-4 text-xl-right">Price Flex:</dt>
                            <dd class="col-xl-5">{{ display_currency($batch->suggested_unit_sale_price-($batch->min_flex?:0)) }} - {{ display_currency($batch->suggested_unit_sale_price+($batch->max_flex?:0)) }}</dd>

                            @if($batch->isTopParent())
                            <dt class="col-xl-4 text-xl-right">
                                Vault Log:
                            </dt>
                            <dd class="col-xl-5">
                                <a href="{{ route('vault-logs.create', $batch->ref_number) }}">
                                    {{--<img style="height: 100px; width: 100px" src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('vault-logs.login', $batch->ref_number), "QRCODE") }}"><br>--}}
                                    Create Vault Log
                                </a>
                            </dd>
                            @endif
                        </dl>

                        @if($batch->rnd_link)
                            <h3 class="header-title">R&D Results</h3>
                            <dl class="row">

                                <dt class="col-xl-4 text-xl-right">THC:</dt>
                                <dd class="col-xl-5">{{ $batch->present()->thc_rnd_potency()  }}</dd>

                                <dt class="col-xl-4 text-xl-right">CBD:</dt>
                                <dd class="col-xl-5">{{ $batch->present()->cbd_rnd_potency() }}</dd>

                                <dt class="col-xl-4 text-xl-right">CBN:</dt>
                                <dd class="col-xl-5">{{ $batch->present()->cbn_rnd_potency() }}</dd>

                                <dt class="col-xl-4 text-xl-right">R&D Link:</dt>
                                <dd class="col-xl-5">
                                    <a href="{{ $batch->rnd_link }}" target="_blank">
                                        <img style="height: 120px; width: 120px" src="data:image/png;base64,{{ DNS2D::getBarcodePNG($batch->rnd_link, "QRCODE") }}">
                                    </a>
                                </dd>
                            </dl>
                        @endif

                        @if($batch->COASourceBatch && $batch->COASourceBatch->coa_link)
                            {{--{{ dump($batch->present()->thc_potency()) }}--}}
                            {{--{{ dd($batch->COASourceBatch) }}--}}
                            <h3 class="header-title">COA Results</h3>
                            <dl class="row">

                                <dt class="col-xl-4 text-xl-right">THC:</dt>
                                <dd class="col-xl-5">{{ $batch->present()->thc_potency()  }}</dd>

                                <dt class="col-xl-4 text-xl-right">CBD:</dt>
                                <dd class="col-xl-5">{{ $batch->present()->cbd_potency() }}</dd>

                                <dt class="col-xl-4 text-xl-right">CBN:</dt>
                                <dd class="col-xl-5">{{ $batch->present()->cbn_potency() }}</dd>

                                <dt class="col-xl-4 text-xl-right">Link:</dt>
                                <dd class="col-xl-5">
                                    <a href="{{ $batch->COASourceBatch->coa_link }}" target="_blank">
                                        <img style="height: 120px; width: 120px" src="data:image/png;base64,{{ DNS2D::getBarcodePNG($batch->COASourceBatch->coa_link, "QRCODE") }}">
                                    </a>
                                </dd>
                            </dl>
                        @endif

                        {{--@if($batch->description)--}}
                            {{--<h6>Description</h6>--}}
                            {{--<p>{{ $batch->description }}</p>--}}
                        {{--@endif--}}

                        @if($batch->sales_notes)
                            <h6>Sales Notes</h6>
                            <p>{{ $batch->sales_notes }}</p>
                        @endif

                        @if($batch->character)
                            <h6>Characteristics</h6>
                            {{ implode(", ", (array)$batch->character) }}
                        @endif

                    </div>



                    <div class="col-xl-7">

                    @if(($batch->unit_price || $cost_override))


                        @if($batch->inventory && Gate::allows('batches.sell') && ($batch->passedTesting() || !$batch->inTesting()) )

                            {{ Form::open(['url'=>route('batches.sell', $batch->ref_number)]) }}

                            <div id="sell_container">

                                <h2 class="offset-2">Sell Item</h2>

                                <div class="form-group row">
                                    {{--<label class="col-12 col-form-label">Destination License</label>--}}
                                    <div class="offset-2 col-8">

                                        <input id="destination_user_id" type="text" list="destination_user_id_list" class="form-control" value="{{ $selected_customer?$selected_customer->name:"" }}" placeholder="-- Customer (Ship To) --">

                                        <datalist id="destination_user_id_list">
                                            @foreach($customers as $customer )
                                                <option value="{{ $customer->name }}" id="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach

                                                {{--@foreach($customers as $customer)--}}
                                                    {{--<option value="{{ $customer->id }}" @if ($customer->id == $selected_customer->id) selected="selected" @endif>{{ $customer->name }}</option>--}}
                                                {{--@endforeach--}}
                                        </datalist>


                                        {{--<select class="form-control mb-2" name="destination_user_id" id="destination_user_id">--}}
                                            {{--<option value="">-- Destination License (Ship To) --</option>--}}
                                            {{--@foreach($customers as $customer)--}}
                                                {{--<option value="{{ $customer->id }}" @if ($customer->id == $selected_customer->id) selected="selected" @endif>{{ $customer->name }}</option>--}}
                                            {{--@endforeach--}}
                                        {{--</select>--}}
                                        @if($selected_customer->exists)
                                            <input type="hidden" name="destination_user_id" value="{{ $selected_customer->id }}">
                                        {{--<a href="{{ route('users.edit', $selected_customer) }}">Edit Destination</a> | --}}
                                            <a href="{{ route('batches.show', $batch->ref_number) }}">Reset</a>
                                        @endif
                                    </div>

                                    <div class="col-lg-2 col-xl-2">
                                        <a href="{{ route('users.create') }}?role=Customer" class="btn btn-primary waves-effect waves-light">Add License</a>

                                    </div>
                                </div>


                                @if($selected_customer->exists)

                                {{--<div class="form-group row">--}}

                                    {{--<div class="offset-2 col-8">--}}
                                        {{--<select class="form-control" name="destination_license_id" required>--}}
                                            {{--<option value="">- Destination License -</option>--}}

                                            {{--@foreach($selected_customer->licenses as $license)--}}
                                                {{--<option value="{{ $license->id }}">{{ $license->number }} ({{ $license->license_type->name }})</option>--}}
                                            {{--@endforeach--}}

                                        {{--</select>--}}
                                    {{--</div>--}}

                                {{--</div>--}}

                                <div class="form-group row">

                                    <div class="offset-2 col-8">
                                        <select class="form-control mb-2" name="customer_id">
                                            <option value="">-- Bill To, if different than customer --</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" @if ($customer->id == old('customer_id')) selected="selected" @endif>{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <div class="offset-2 col-4">
                                        <label class="col-12 col-form-label">Order Date <span class="text-danger">*</span></label>
                                        <input class="form-control" type="date" name="txn_date" value="{{ old('txn_date',\Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                                    </div>

                                    <div class="col-4">
                                        <label class="col-12 col-form-label">Expected Delivery Date</label>
                                        <input class="form-control" type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}">
                                    </div>

                                </div>

                                    <div class="form-group row">

                                        <div class="offset-2 col-4">
                                            <select class="form-control mb-2" name="sale_type" required="required">
                                                <option value="">-- Sale Type --</option>
                                                @foreach(config('highline.sale_types') as $sale_type)
                                                    <option value="{{ $sale_type }}"  @if ($sale_type == 'bulk') selected="selected" @endif>{{ ucwords($sale_type) }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-4">
                                            <select class="form-control mb-2" name="terms" required="required">
                                                <option value="">-- Payment Terms --</option>

                                                @foreach(config('highline.payment_terms') as $payment_term_days => $payment_term)
                                                    <option value="{{ $payment_term_days }}" {{ ( ! is_null($selected_customer->details['terms']) ? ($selected_customer->details['terms'] == $payment_term_days ? "selected=selected" : "" ) : '' ) }}>{{ $payment_term }}</option>
                                                @endforeach

                                            </select>

                                        </div>



                                    </div>

                                <div class="form-group row">

                                    {{--<div class="col-lg-4 col-xl-5">--}}
                                        {{--<select class="form-control mb-2" name="customer_type" required="required">--}}
                                            {{--<option value="">-- Customer Type --</option>--}}
                                            {{--@foreach($selected_customer->license_types as $license_type)--}}
                                            {{--<option value="{{ $license_type->name }}" @if ($license_type->name == old('customer_type')) selected="selected" @endif>{{ ucwords($license_type->name) }}</option>--}}
                                            {{--@endforeach--}}

                                            {{--<option value="micro business retailer" @if ('micro business retailer' == old('customer_type')) selected="selected" @endif>Micro Business (Retailer) - Excise Tax</option>--}}
                                            {{--<option value="micro business distributor" @if ('micro business distributor' == old('customer_type')) selected="selected" @endif>Micro Business (Distributor) - No Excise Tax</option>--}}
                                            {{--<option value="retailer" @if ('retailer' == old('customer_type')) selected="selected" @endif>Retailer (Arms Length) - Excise Tax</option>--}}
                                            {{--<option value="retailer nonarms length" @if ('retailer nonarms length' == old('customer_type')) selected="selected" @endif>Retailer (NonArms Length) - No Excise Tax</option>--}}
                                            {{--<option value="manufacturing" @if ('manufacturing' == old('customer_type')) selected="selected" @endif>Manufacturing</option>--}}
                                            {{--<option value="non-storefront retail" @if ('non-storefront retail' == old('customer_type')) selected="selected" @endif>Non-Storefront Retail</option>--}}
                                        {{--</select>--}}
                                    {{--</div>--}}

                                    @if (Auth::user()->level() >= 60)
                                        <div class="offset-2 col-4">
                                            <select class="form-control mb-2" name="sales_rep_id">
                                                <option value="">-- Sales Rep --</option>
                                                @foreach($sales_reps as $sales_rep)
                                                    <option value="{{ $sales_rep->id }}" @if ($sales_rep->id == 14) selected="selected" @endif>{{ $sales_rep->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <input type="hidden" name="sales_rep_id" value="{{ Auth::user()->id }}"/>

                                    @endif

                                    <div class="col-4">
                                        <select class="form-control mb-2" name="broker_id">
                                            <option value="">-- Brokers --</option>
                                            @foreach($brokers as $broker_id=>$broker_name)
                                                <option value="{{ $broker_id }}" @if ($broker_id == old('$broker')) selected="selected" @endif>{{ $broker_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                </div>

                                <div class="form-group row">

                                    <div class="offset-2 col-8">
                                        <label class="col-12 col-form-label">Internal Notes</label>
                                        <textarea class="form-control" placeholder="Internal Notes" name="notes"></textarea>

                                    </div>
                                </div>
<hr>

                                @elseif($open_sales_orders->count())

                                    <hr>

                                    <div class="form-group row">
                                        <label class="offset-2 col-8 col-form-label">Open Sale Orders</label>
                                        <div class="offset-2 col-8">
                                            <select class="form-control" name="sale_order_id">
                                                <option value="">-- Sale Orders --</option>
                                                @foreach($open_sales_orders as $open_sales_order)
                                                    <option value="{{ $open_sales_order->id }}">{{ $open_sales_order->ref_number }} - {{ $open_sales_order->customer->name }} - {{ $open_sales_order->txn_date->format('m/d/Y') }} {{ ($open_sales_order->broker?" - ".$open_sales_order->broker->name:"") }} {{ Str::substr(preg_replace('/\s+/', ' ', trim($open_sales_order->notes)), 0, 10) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif


                                <dl class="row">
                                    <dt class="col-2 text-xl-right">Units:</dt>
                                    <dd class="col-10">


                                        @if($batch->wt_based)
                                            <input type="hidden" name="sell_units" value="1" />
                                            {!! display_inventory($batch) !!}
                                        @else
                                            <input type="hidden" name="sell_units" value="{{ $batch->inventory }}" />
                                            <div class="input-group">
                                                <input name="sell_units" type="text" class="form-control col-2" id="sell_units" aria-describedby="sell_unitsHelp" value="{{ $batch->inventory }}">
                                                <span class="input-group-addon">{{ $batch->uom }}</span>
                                            </div>
                                        @endif


                                    </dd>

                                    {{--<dt class="col-2 text-xl-right"></dt>--}}
                                    {{--<dd class="col-10">--}}
                                        {{--<div class="form-group row">--}}
                                            {{--<div class="col-9">--}}
                                                {{--<div class="checkbox checkbox-primary">--}}
                                                    {{--<input id="checkbox2" type="checkbox" name="add_sample" value="0.5">--}}
                                                    {{--<label for="checkbox2">--}}
                                                        {{--Add a sample @ $0.50?--}}
                                                    {{--</label>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</dd>--}}

                                    {{--@if($batch->brand)--}}
                                    {{--<dt class="col-2 text-xl-right"></dt>--}}
                                    {{--<dd class="col-10">--}}
                                        {{--<h6>{{ $batch->brand->name }}</h6>--}}
                                    {{--</dd>--}}
                                    {{--@endif--}}

                                    <dt class="col-2 text-xl-right">Sold As Name:</dt>
                                    <dd class="col-10">
                                        <input id="sold_as_name" type="text" value="{{ $batch->present()->branded_name }}" name="sold_as_name" class="form-control col-4" style="display: block;" required>
                                    </dd>

                                    <dt class="col-2 text-xl-right">Sale Price:</dt>
                                    <dd class="col-10">

                                        <div class="row">
                                            {{--<div class="col-4">--}}
                                                {{--<p>Including Tax Price</p>--}}
                                                {{--<div class="input-group bootstrap-touchspin">--}}
                                                    {{--<span class="input-group-addon bootstrap-touchspin-prefix">$</span>--}}
                                                    {{--<input id="sale_price" type="text" value="{{ $sale_price }}" name="sale_price" class="form-control" style="display: block;">--}}
                                                {{--</div>--}}
                                            {{--</div>--}}

                                            <div class="col-4">
                                                {{--<p>Sale Price</p>--}}
                                                <div class="input-group bootstrap-touchspin">
                                                    <span class="input-group-addon bootstrap-touchspin-prefix">$</span>
                                                    <input id="sale_price" type="text" value="" name="pre_tax_sale_price" class="form-control" style="display: block;" placeholder="Sale Price">
                                                </div>
                                            </div>

                                        </div>

                                    </dd>

                                    <dt class="col-2 text-xl-right"></dt>
                                    <dd class="col-10">
                                        <h5><span><strong>-- OR --</strong></span></h5>
                                    </dd>

                                    <dt class="col-2 text-xl-right">Markup:</dt>
                                    <dd class="col-10">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="input-group bootstrap-touchspin">
                                                    <span class="input-group-addon bootstrap-touchspin-prefix">$</span>
                                                    <input id="cost_markup" type="text" value="" name="cost_markup" class="form-control" style="display: block;" placeholder="Markup Amount">
                                                </div>
                                                <p>Cost: {{ display_currency($batch->preTaxCost) }}</p>

                                            </div>
                                        </div>
                                    </dd>

                                    @if($batch->tax_rate)
                                        <dt class="col-2 text-xl-right">Pass Cult Tax:</dt>
                                        <dd class="col-10">
                                            <div class="row">
                                                <div class="col-4">
                                                    <select class="form-control" name="pass_cult_tax">
                                                        <option value="1">Yes</option>
                                                        <option value="0">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </dd>
                                    @endif

                                    <dt class="col-2"></dt>
                                    <dd class="col-10"><button type="submit" class="btn btn-primary waves-effect waves-light">Sell</button></dd>

                                </dl>


                            </div>

                            {{ Form::close() }}

                        @endif

                    @else

                            <h4>Item has no cost and cannot be sold. <a href="{{ route('batches.show', ['batch'=>$batch->ref_number, 'cost_override'=>1]) }}">Click here to override</a></h4>

                    @endif

                    </div>

                    @if($batch->testing_status == 'In-Testing')

                    <div class="col-xl-7">

                        <h3 class="header-title">Test Results</h3>

                        {{ Form::open(['url'=>route('batches.testing_results', $batch->ref_number)]) }}
                        {{ Form::hidden('coa_batch', 1) }}

                        <div class="form-group">
                            {{ Form::select('testing_status', ['Passed'=>'Passed','Failed'=>'Failed'], $batch->testing_status, ['placeholder' => '- Select -','class'=>'form-control','required'=>'required']) }}
                        </div>

                        <div class="form-group">
                            <label for="coa_link">COA Link</label>
                            {{ Form::input('text', 'coa_link', $batch->coa_link, ['class'=>'form-control', 'rows'=>'4', 'placeholder'=>'COA Link', 'required'=>'required']) }}
                        </div>

                        <div class="form-group row">

                            <div class="col-4">
                                <label for="thc">THC</label>
                                <div class="input-group bootstrap-touchspin">
                                    <input id="thc" type="number" value="{{ $batch->thc }}" name="thc" min="0" max="100.00" step="0.01" class="form-control col-lg-10" style="display: block;" placeholder="0.00" required="required">
                                    <span class="input-group-addon bootstrap-touchspin-postfix">%</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="cbd">CBD</label>
                                <div class="input-group bootstrap-touchspin">
                                    <input id="cbd" type="number" value="{{ $batch->cbd }}" name="cbd" step="0.01" min="0" max="100" class="form-control col-lg-10" style="display: block;" placeholder="0.00" required="required">
                                    <span class="input-group-addon bootstrap-touchspin-postfix">%</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="cbn">CBN</label>
                                <div class="input-group bootstrap-touchspin">
                                    <input id="cbn" type="number" value="{{ $batch->cbn }}" name="cbn" step="0.01" min="0" max="100" class="form-control col-lg-10" style="display: block;" placeholder="0.00">
                                    <span class="input-group-addon bootstrap-touchspin-postfix">%</span>
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save</button>

                        {{ Form::close() }}

                    </div>

                    @endif

                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">

                <h4>Pending Sale Orders: {{ $pending_sale_orders->count() }}</h4>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th>#</th>

                            <th>Date</th>
                            <th>SO</th>
                            <th>Status</th>
                            <th>Customer</th>
                            <th>Broker</th>
                            <th>Internal Notes</th>
                            <th>Qty</th>
                            <th>Cost</th>
                            <th>Price</th>
                            <th>Markup</th>
                            <th>Order Total</th>
                            <th>Order Margin</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($pending_sale_orders as $sale_order)

                            @foreach($sale_order->order_details as $order_detail)

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sale_order->txn_date->format('m/d/Y') }}</td>
                                <td><a href="{{ route('sale-orders.show', $sale_order->id) }}">{{ $sale_order->ref_number }}</a></td>
                                <td><span class="badge badge-{{ status_class($sale_order->status) }}">{!! display_status($sale_order->status) !!}</span></td>
                                <td>{{ $sale_order->customer->name }}</td>
                                <td>{{ ($sale_order->broker ? $sale_order->broker->name : "") }}</td>
                                <td>{{ $sale_order->notes }}</td>
                                <td>{{ $order_detail->units }} {{ $batch->uom }}</td>
                                <td>{{ display_currency($order_detail->unit_cost) }}</td>
                                <td>{{ display_currency($order_detail->unit_sale_price) }}</td>
                                <td>{{ display_currency($order_detail->unit_sale_price - $order_detail->unit_cost) }}</td>
                                <td>{{ display_currency($order_detail->unit_sale_price * $order_detail->units) }}</td>
                                <td class="text-{{ ($order_detail->margin > 0?'success':'danger') }}">{{ display_currency($order_detail->margin) }} <small>({{ $order_detail->margin_pct }}%) <i class="ion-arrow-{{ ($order_detail->margin > 0?'up':'down') }}-c"></i> </small></td>
                            </tr>

                            @endforeach

                        @endforeach


                        </tbody>

                        <tfoot>
                        <tr>
                            <td colspan="7"></td>
                            <td>{{ $pending_sale_orders->pluck('order_details')->collapse()->sum('units') }} {{ $batch->uom }}</td>
                            <td colspan="3"></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>

    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">

                <h4>Vault Logs: {{ $batch->vault_logs->count() }}</h4>

                    <div class="table-responsive">

                        <table class="table table-hover table-striped">

                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Order Title</th>
                                <th>Strain Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Notes</th>
                                <th>User</th>
                            </tr>
                            </thead>

                            <tbody>

                            @foreach($batch->vault_logs as $idx => $vault_log)
                                <tr>
                                    <td>{{ $vault_log->created_at->format('m/d/Y') }}</td>
                                    <td>{!! ($vault_log->broker ? $vault_log->broker->name."<br>".$vault_log->order_title : $vault_log->order_title)  !!}</td>
                                    <td>{{ $vault_log->strain_name }}</td>
                                    <td>{{ $vault_log->quantity }} {{ $batch->uom }}</td>
                                    <td>{{ display_currency($vault_log->price) }}</td>
                                    <td>{{ $vault_log->notes }}</td>
                                    <td>{{ $vault_log->user->name }}</td>
                                </tr>
                            @endforeach

                            </tbody>

                            <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>{{ $batch->vault_logs->sum('quantity') }} {{ $batch->uom }}</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>

                            </tfoot>
                        </table>

                    </div>


            </div>
        </div>
    </div>


@endsection

@section('js')

    <script type="text/javascript">
        $(document).ready(function() {

            $('#destination_user_id').change(function () {

                $('#customer-loading').addClass('d-block');

                var el=$("#destination_user_id")[0];  //used [0] is to get HTML DOM not jquery Object
                var dl=$("#destination_user_id_list")[0];
                if(el.value.trim() != '') {
                    var opSelected = dl.querySelector(`[value="${el.value}"]`);

                    // window.location = window.location.href + '/' + opSelected.getAttribute('id');
                    window.location = '{{ route('batches.show', $batch->ref_number) }}/customer/' + opSelected.getAttribute('id');
                }

                // window.location = window.location.href + '/customer/' + this.value;

            });

        });
    </script>

@endsection