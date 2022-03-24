@extends('layouts.app')


@section('content')

    {{ Form::open(['class'=>'form-horizontal', 'files'=>'true', 'url'=>route('purchase-orders.store')]) }}

    <div class="row">

        <h4 class="m-t-0 header-title"><b>Upload Purchase Order</b></h4>

        <div class="col-lg-12">

            <div class="form-group row">
                {{ Form::Input('hidden', 'txn_date', $txn_date) }}
                <label class="col-lg-2 col-form-label">Order Date</label>
                <div class="col-lg-3">{{ $txn_date->format('m/d/Y') }}
                </div>
            </div>

            <div class="form-group row">
                {{ Form::Input('hidden', 'vendor_id', $vendor->id) }}
                <label class="col-lg-2 col-form-label">Vendor</label>
                <div class="col-lg-3">

                    <address>
                        <h4 class="m-t-0 header-title"><b>{{ $vendor->name }}</b></h4>
                        <p>{{ $vendor->details['address'] }}<br>{{ $vendor->details['address2'] }}</p>

                        <p><a href="{{ route('purchase-orders.upload') }}">Change</a></p>
                    </address>
                </div>
            </div>

            <div class="form-group row">
                {{ Form::Input('hidden', 'origin_license_id', $origin_license->id) }}
                <label class="col-lg-2 col-form-label">Origin License</label>
                <div class="col-lg-3">{{ $origin_license->number }} - {{ $origin_license->license_type->name }}<br>
                {{ $origin_license->legal_business_name }}</div>
            </div>

            <div class="form-group row">
                {{ Form::Input('hidden', 'destination_license_id', $destination_license->id) }}
                <label class="col-lg-2 col-form-label">Destination License</label>
                <div class="col-lg-3">{{ $destination_license->number }} - {{ $destination_license->license_type->name }}<br>
                {{ $destination_license->legal_business_name }}</div>
            </div>

            <div class="form-group row">
                {{ Form::Input('hidden', 'terms', $terms) }}
                <label class="col-lg-2 col-form-label">Terms</label>
                <div class="col-lg-3">{{ config('highline.payment_terms')[$terms] }}</div>
            </div>

            <div class="form-group row">
                {{ Form::Input('hidden', 'fund_id', $fund->id) }}
                <label class="col-lg-2 col-form-label">Fund</label>
                <div class="col-lg-3">{{ $fund->name }}</div>
            </div>

            <div class="form-group row">
                {{ Form::Input('hidden', 'manifest_no', $manifest_no) }}
                <label class="col-lg-2 col-form-label">Manifest#</label>
                <div class="col-lg-3">{{ $manifest_no }}</div>
            </div>

        </div>

        <hr>

        <h4 class="m-t-0 header-title"><b>Review Packages</b></h4>

        <div class="col-lg-12">

            @if(!$can_continue)
            <h3 class="text-danger">Fix upload file - Some UID's already exist</h3>
            @endif

            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th style="width: 240px">UID</th>
                        <th>BatchID</th>
                        <th>Category</th>
                        {{--<th>Brand</th>--}}
                        <th>Name</th>
                        <th>Short Name</th>
                        <th>Cultivation Date</th>
                        <th style="width: 120px">Qty / Unit Cost</th>
                        <th>Unit Tax</th>
                        {{--<th style="width: 80px">UOM</th>--}}
                        {{--<th>Unit Cost</th>--}}
                        <th>Subtotal</th>
                        <th>Tax Amount</th>
                        <th>Total</th>
                        <th>Tax Rate</th>
                        {{--<th style="width:100px">Collect Tax</th>--}}
                    </tr>
                </thead>

                <tbody>

                @foreach($packages as $package)
                    {{ Form::Input('hidden', '_batches[tax_rate_id][]', $package['tax_rate_id']) }}
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ Form::Input('hidden', '_batches[ref_number][]', $package['Package']) }}
                        <p class="text-{{ ($package['uid_exists']?'danger':'success') }}">{{ $package['Package'] }}</p>
{{--                        {{ Form::text('_batches[ref_number][]', $package['Package'], ['class' => 'form-control'.($package['uid_exists']?' text-danger':''), 'required'=>'required']) }}--}}

                    </td>
                    <td>
                        {{ Form::Input('hidden', '_batches[batch_number][]', $package['BatchID']) }}
                        <p>{{ ($package['BatchID']?$package['BatchID']:"N/A") }}</p>
                        {{--{{ Form::text('_batches[batch_number][]', $package['BatchID'], ['class' => 'form-control']) }}--}}
                    </td>
                    <td>
                        {{ $package['Category'] }}
                        {{ Form::Input('hidden', '_batches[category_id][]', $package['category_id']) }}
{{--                        {{ Form::select('_batches[category_id][]', $categories->pluck('name','id')->toArray(), $package['category_id'], ['placeholder'=>'-- Select --', 'class'=>'form-control', 'required'=>'required']) }}--}}
                    </td>
                    {{--<td>--}}
                        {{--{{ Form::select('_batches[brand_id][]', $brands->pluck('name','id')->toArray(), $package['brand_id'], ['placeholder'=>'-- Select --', 'class'=>'form-control']) }}--}}
                    {{--</td>--}}
                    <td>
{{--                        {{ Form::text('_batches[name][]', $package['Item Name'], ['class' => 'form-control', 'required'=>'required']) }}--}}
                        <p>{{ $package['Item Name'] }}</p>
                        {{ Form::Input('hidden', '_batches[name][]', $package['Item Name']) }}
                    </td>
                    <td>
                        {{--{{ Form::text('_batches[description][]', $package['Item Short Name'], ['class' => 'form-control']) }}--}}
                        <p>{{ $package['Item Short Name'] }}</p>
                        {{ Form::Input('hidden', '_batches[description][]', $package['Item Short Name']) }}
                    </td>
                    <td>
{{--                        {{ Form::date('_batches[cultivation_date][]', $package['Cultivation Date'], ['class' => 'form-control']) }}--}}
                        @if($package['Cultivation Date'])
                        <p>{{ $package['Cultivation Date']->format('m/d/Y') }}</p>
                        {{ Form::Input('hidden', '_batches[cultivation_date][]', $package['Cultivation Date']->format('Y-m-d')) }}
                        @endif
                    </td>
                    <td>
                        <p>{{ $package['Qty'] }} {{ $package['UOM'] }} @ {{ display_currency($package['unit_cost']) }}</p>
                        {{--<input type="text" class="form-control quantity" name="batches[quantity][]" value="{{ $package['Qty'] }}" placeholder="Qty" required="required">--}}
                        {{ Form::Input('hidden', '_batches[quantity][]', $package['Qty']) }}
                        {{ Form::Input('hidden', '_batches[uom][]', $package['UOM']) }}
                        {{ Form::Input('hidden', '_batches[unit_cost][]', $package['unit_cost']) }}
                    </td>
                    <td>{{ display_currency($package['unit_tax_amount']) }}</td>
                    {{--<td>--}}
{{--                        {{ Form::select("batches[uom][]", array_combine(array_keys(config('highline.uom')), array_keys(config('highline.uom'))), $package['UOM'], ['class'=>'form-control']) }}--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--<div class="input-group bootstrap-touchspin">--}}
                            {{--<span class="input-group-addon bootstrap-touchspin-prefix">$</span>--}}
                            {{--<input type="text" value="{{ $package['unit_cost'] }}" name="batches[unit_cost][]" class="form-control unit_cost" style="display: block;" placeholder="Unit Cost" required="required">--}}
                        {{--</div>--}}
                    {{--</td>--}}

                    <td>
                        <p>{{ display_currency($package['subtotal']) }}</p>
                        {{ Form::Input('hidden', '_batches[total_cost][]', $package['subtotal']) }}

                        {{--<div class="input-group bootstrap-touchspin">--}}
                            {{--<span class="input-group-addon bootstrap-touchspin-prefix">$</span>--}}
                            {{--<input type="text" value="{{ $package['subtotal'] }}" name="batches[total_cost][]" class="form-control total_cost" style="display: block;" placeholder="Total Cost" required="required">--}}
                        {{--</div>--}}
                    </td>

                    <td>
                        <p>{{ display_currency($package['tax_amount']) }}</p>
                        {{ Form::Input('hidden', '_batches[tax_amount][]', $package['tax_amount']) }}

                        {{--<div class="input-group bootstrap-touchspin">--}}
                            {{--<span class="input-group-addon bootstrap-touchspin-prefix">$</span>--}}
                            {{--<input type="text" value="{{ $package['tax_amount'] }}" name="batches[tax_amount][]" class="form-control tax_amount" style="display: block;" placeholder="" required="required">--}}
                        {{--</div>--}}
                    </td>

                    <td>
                        {{ display_currency($package['total']) }}
                    </td>

                    <td>
                        @if($package['tax_rate'])
                        {{ $package['tax_rate']->name }}<br>{{ display_currency($package['tax_rate']->amount) }} / {{ $package['tax_rate']->uom }}
                            @else
                            N/A
                        @endif
                    </td>



                    {{--<td>--}}
                        {{--<div class="checkbox">--}}
                            {{--<input id="cult_tax_1" class="cult_tax" type="checkbox" name="" value="{{ ( ! empty($package[9]) && strtolower($package[9])=='yes' ? '1' : '0' ) }}" checked="checked">--}}
                            {{--<label for="cult_tax_1" class="cult_tax"></label>--}}
                        {{--</div>--}}
{{--                        {{ Form::select("batches[collect_cult_tax][]", ['Yes'=>'Yes','No'=>'No'], $package['Collect Tax'], ['placeholder'=>'--', 'class'=>'form-control']) }}--}}
                        {{--</td>--}}

                </tr>
                    @endforeach

                </tbody>

                <tfoot>

                <tr>

                    <th colspan="9"></th>
                    <th>{{ display_currency($packages->sum('subtotal')) }}</th>
                    <th>{{ display_currency($packages->sum('tax_amount')) }}</th>
                    <th>{{ display_currency($packages->sum('total')) }}</th>

                </tr>

                </tfoot>

            </table>

        </div>

        @if($can_continue)
            <button type="submit" class="btn btn-primary waves-effect waves-light">Save Purchase Order</button>
        @endif

    </div>



    {{ Form::close() }}

@endsection