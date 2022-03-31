@extends('layouts.app')


@section('content')


    <div class="row">

        <div class="col-12">

            @if( $purchaseOrder->canBeDeleted)

                {{ Form::open(['class'=>'form-horizontal', 'url'=>route('purchase-orders.remove', $purchaseOrder->id)]) }}
                <button type="submit" class="btn btn-danger waves-effect waves-light pull-right" onclick="return confirm('Are you sure you want to delete this purchase order?')">Delete PO</button>
                {{ Form::close() }}

            @endif
                <a href="{{ route('purchase-orders.print_po', $purchaseOrder->id) }}" class="btn btn-dark waves-effect waves-light mb-20 pull-right">Print PO <i class="ti-receipt"></i></a>
        </div>
    </div>

    <br>

    <div class="row">

        <div class="col-lg-12">
            <div class="card-box">

                <div class="row">

                    <div class="col-lg-4">

                        <h3>Summary</h3>

                        @include('purchase_orders._summary', ['purcahseOrder'=>$purchaseOrder])

                    </div>

                    <div class="col-lg-4">

                        <h3>Payments</h3>

                        {{ Form::open(['url'=>route('purchase-orders.payment', $purchaseOrder->id)]) }}
                        <dl class="row">

                                <dt class="col-4 text-right">Payment Date:</dt>
                                <dd class="col-5">
                                    <input class="form-control" type="date" name="txn_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                                </dd>

                                <dt class="col-4 text-right">Payment Method:</dt>
                                <dd class="col-5">
                                    <select class="form-control" name="payment_method">
                                        <option value="Cash">Cash</option>
                                        <option value="Check">Check</option>
                                        <option value="Credit">Credit</option>
                                        <option value="Vendor Credit">Vendor Credit</option>
                                        <option value="Wire">Wire</option>
                                    </select>
                                </dd>


                                <dt class="col-4 text-right">Reference#:</dt>
                                <dd class="col-5">
                                    <input class="form-control" type="text" name="ref_number" value="" placeholder="Ref#">
                                </dd>

                                <dt class="col-4 text-right">Memo:</dt>
                                <dd class="col-5">
                                    <textarea class="form-control" name="memo" value=""></textarea>
                                </dd>

                                <dt class="col-4 text-right">Payment:</dt>
                                <dd class="col-5">
                                    <div class="input-group mb-2">
                                        <span class="input-group-addon">$</span>
                                        <input type="number" step="0.01" class="form-control" name="payment" value="{{ $purchaseOrder->balance }}" placeholder="">
                                    </div>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save Payment</button>
                                </dd>
                        </dl>
                        {{ Form::close() }}

                    </div>

                    <div class="col-lg-4">

                        <h3>Transactions</h3>

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

                            @foreach($purchaseOrder->transactions as $transaction)
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
                                    <td>{{ display_currency($purchaseOrder->transactions->sum('amount')) }}</td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>

                </div>



            </div>
        </div>
    </div>


    @if(! $purchaseOrder->in_qb)

    <div class="batch_items">

        <h4 class="m-t-0 header-title">Add New Batch</h4>

        {{ Form::open(['class'=>'form-horizontal', 'url'=>route('purchase-orders.show-post', $purchaseOrder->id), 'method'=>'post']) }}

        <div class="add_batch_row card-box mb-3">

            @include('purchase_orders/_add_item', compact('tax_rates'))

        </div>

        <button type="submit" class="btn btn-primary waves-effect waves-light">Add Batch</button>

        {{ Form::close() }}

    </div>

    <hr>

    @endif



    {{--<h4 class="m-t-0 header-title">Re-Tag Items</h4>--}}

    {{--<div class="row">--}}

        {{--<div class="col-lg-12">--}}

            {{--<div class="card">--}}
                {{--<div class="card-block">--}}

                    {{--@if($purchaseOrder->batches->sum('inventory'))--}}
                        {{--{{ Form::open(['class'=>'form-horizontal form-inline', 'url'=>route('purchase-orders.retag', $purchaseOrder->id), 'method'=>'post']) }}--}}
{{--                        <p style="padding-top: 8px; padding-right: 3px;">{{ config('highline.metrc_tag')[$purchaseOrder->destination_license_id] }} </p>--}}
                        {{--<div class="input-group mb-2">--}}

                            {{--<input type="number" class="form-control tag_id" name="tag_id" value="{{ old('tag_id') }}" placeholder="Starting UID" required="required">--}}
                            {{--<br>--}}
                            {{--<input id="create_pounds" class="" type="checkbox" name="create_pounds" value="1" checked="checked">--}}
                            {{--<label for="create_pounds" class="">Convert batches in grams to pounds?</label>--}}

                            {{--<button type="submit" class="btn btn-primary waves-effect waves-light">Re-Tag</button>--}}
                        {{--</div>--}}
                        {{--{{ Form::close() }}--}}

                    {{--@endif--}}

                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    <p><a href="{{ route('purchase-orders.retag', $purchaseOrder->id) }}" class="btn btn-primary">View Report</a></p>

    {{--<hr>--}}



    <h4 class="m-t-0 header-title">Items</h4>

    <div class="row">

        <div class="col-lg-12">

            <div class="card">
                <div class="card-block">

                    {{--<a href="{{ route('purchase-orders.print-qr', ['id'=>$purchaseOrder->id]) }}" class="pull-right btn btn-primary mb-3">Print QRs</a>--}}
                    {{--<div class="clearfix"></div>--}}

                    <div class="table-responsive">
                <table class="table m-t-30 table-hover table-striped">
                    <thead>
                    <tr>
                        {{--<th>M</th>--}}
                        <th>ID</th>
                        {{--<th>Fund</th>--}}
                        {{--<th>Status</th>--}}
                        {{--<th>Testing</th>--}}
                        {{--<th>Internal Batch#</th>--}}
                        {{--<th>Cultivator</th>--}}
                        {{--<th>Harvest Date</th>--}}
                        <th>Name / UID</th>
                        <th>Qty / Unit Cost</th>
                        {{--<th>Tax Rate</th>--}}
                        {{--<th>Cost Pre-Tax</th>--}}
                        <th>Inventory</th>
                        <th>Subtotal</th>
                        {{--<th>Cult Tax Collected</th>--}}
                        {{--<th>Total</th>--}}
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($purchaseOrder->batches as $batch)
                    <tr>
{{--                        <td>@if($batch->in_metrc)<i class=" mdi mdi-checkbox-marked text-success"></i>@else<i class=" mdi mdi-checkbox-blank text-danger"></i>@endif</td>--}}
                        <td>{{ $batch->id }}</td>
{{--                        <td>{{ $batch->fund->name }}</td>--}}
                        {{--<td><span class="badge badge-{{ status_class($batch->status) }}">{{ ucwords($batch->status) }}</span></td>--}}
                        {{--<td><span class="badge badge-{{ status_class($batch->testing_status) }}">{{ ucwords($batch->testing_status) }}</span></td>--}}
                        {{--<td>{{ $batch->batch_number }}</td>--}}
                        {{--<td>{{ ($batch->cultivator?$batch->cultivator->name:'--') }}</td>--}}
{{--                        <td>{{ (!empty($batch->cultivation_date) ? $batch->cultivation_date->format('m/d/Y') : '--' ) }}</td>--}}

                        <td>
                            @if($batch->brand) <strong>{{ $batch->brand->name }}</strong><br> @endif

                            <strong>{{ $batch->category->name }}: {{ $batch->present()->branded_name }}</strong><br>
                                <a href="{{ route('batches.show', $batch->ref_number) }}">{{ $batch->ref_number }}</a>
                        </td>
                        <td>

                            @if($purchaseOrder->in_qb || $batch->order_details->isNotEmpty() || $batch->units_purchased != $batch->inventory)
{{--                            @if(false)--}}

                                {{ $batch->units_purchased }} {{ $batch->uom }} @ {{ display_currency($batch->unit_price) }}
                                <small>
                                    @if($batch->uom=='g')
                                        <br>({{ number_format($batch->units_purchased/config('highline.uom.lb'), 4) }} lb @ {{ display_currency($batch->unit_price * config('highline.uom.lb')) }})
                                    @endif

                                    @if($batch->cost_includes_cult_tax || $batch->tax_rate_id)
                                        <br><i>Pre-Tax: {{ display_currency($batch->preTaxCost) }}</i>
                                            @if($batch->uom=='g')
                                                ({{ display_currency(($batch->unit_price - $batch->tax/$batch->units_purchased) * config('highline.uom.lb')) }})
                                            @endif
                                    @endif
                                </small>
                            @else

                                {{ Form::open(['url'=>route('purchase-orders.update-batch', [$purchaseOrder->id, $batch->ref_number])]) }}
                                {{ method_field('PUT') }}

                                <div class="row">

                                    <div class="col-4">

                                        @if($batch->units_purchased != $batch->inventory)

                                            {{ $batch->units_purchased }} {{ $batch->uom }} @ {{ display_currency($batch->unit_price) }}

                                            @if($batch->uom=='g')
                                                <br><small>({{ number_format($batch->units_purchased/config('highline.uom.lb'), 4) }} lb @ {{ display_currency($batch->unit_price * config('highline.uom.lb')) }})</small>
                                            @endif

                                        @else

                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="units_purchased" value="{{ $batch->units_purchased }}">
                                                <span class="input-group-addon">{{ $batch->uom }}</span>
                                            </div>

                                            @if($batch->uom=='g')
                                                <span>{{ number_format($batch->units_purchased/config('highline.uom.lb'), 4) }} lb</span>
                                            @endif

                                        @endif

                                    </div>

                                    <div class="col-4">

                                        <div class="input-group mb-2">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" step="0.01" class="form-control" name="unit_price" value="{{ display_currency_no_sign($batch->unit_price) }}">
                                        </div>
                                        @if($batch->uom=='g')
                                            <span>{{ display_currency($batch->unit_price * config('highline.uom.lb')) }}</span><br>
                                        @endif

                                        @if($batch->tax_rate_id)
                                            <span>Pre-Tax: {{ display_currency($batch->preTaxCost) }}
                                                @if($batch->uom=='g')
                                                    ({{ display_currency(($batch->unit_price - $batch->tax/$batch->units_purchased) * config('highline.uom.lb')) }})
                                                @endif

                                            </span>
                                        @endif

                                    </div>
                                    <div class="col-2"><button type="submit" class="btn btn-primary waves-effect waves-light">Save</button></div>

                                </div>

                                {{ Form::close() }}

                            @endif
                        </td>
                        {{--<td>--}}
                            {{--@if($batch->tax_rate)--}}
                                {{--{{ $batch->tax_rate->name }}<br>{{ display_currency($batch->tax_rate->amount) }} / {{ $batch->tax_rate->uom }}--}}
                                {{--@else--}}
                                {{--N/A--}}
                            {{--@endif--}}
                        {{--</td>--}}
                        <td>{{ $batch->inventory }} {{ $batch->uom }}</td>
                        <td><strong>{{ display_currency($batch->subtotal_price) }}</strong></td>
                        {{--<td><strong>{{ display_currency($batch->tax) }}</strong></td>--}}
                        {{--<td><strong>{{ display_currency($batch->subtotal_price - $batch->tax) }}</strong></td>--}}
                    </tr>
                    @endforeach
                    <tfoot>
                    <tr>
                        <td colspan="3"></td>

                        <td><strong>Total:</strong></td>
                        <td><strong>{{ display_currency($purchaseOrder->batches->sum('subtotal_price')) }}</strong></td>
                        {{--<td><strong>{{ display_currency($purchaseOrder->batches->sum('tax')) }}</strong></td>--}}
{{--                        <td><strong>{{ display_currency($purchaseOrder->batches->sum('subtotal_price') - $purchaseOrder->batches->sum('tax')) }}</strong></td>--}}
                    </tr>

                    </tfoot>
                    </tbody>
                </table>
            </div>
                </div>
            </div>
        </div>
    </div>
    @endsection


@section('js')

    <script type="text/javascript">

        $(document).ready(function() {

            // $('.cult_tax').click(function() {
            //     if( $(this).is(":checked") ) {
            //         $(this).parents('.cult-tax-row').find('.tax_rate_id').show().prop('required',true);
            //     } else {
            //         $(this).parents('.cult-tax-row').find('.tax_rate_id').hide().prop('required',false);
            //     }
            // });


            $('.add_batch_row').on('blur', '.quantity', function() {

                var form_grp = $(this).parents('.add_batch_row');

                if($(form_grp).find('.unit_cost').val()) {
                    var qty = $(this).val();
                    var unit_cost = $(form_grp).find('.unit_cost').val();
                    $(form_grp).find('.total_cost').val((qty * unit_cost).toFixed(2));
                }
            });

            $('.add_batch_row').on('blur', '.unit_cost', function() {
                var form_grp = $(this).parents('.add_batch_row');
                var qty = $(form_grp).find('.quantity').val();
                var unit_cost = $(this).val();

                $(form_grp).find('.total_cost').val((qty * unit_cost).toFixed(2));
            });

            $('.add_batch_row').on('blur', '.total_cost', function() {

                var form_grp = $(this).parents('.add_batch_row');
                var qty = $(form_grp).find('.quantity').val();
                var total_cost = $(this).val();

                if(qty) $(form_grp).find('.unit_cost').val((total_cost / qty).toFixed(2));
            });

        } );

    </script>


    @endsection