@extends('layouts.app')

@section('content')

    <div class="row">

        <div class="col-lg-12">

            <h4 class="m-t-0 header-title"><b>Create Purchase Order</b></h4>

            {{ Form::open(['class'=>'form-horizontal', 'files'=>'true', 'url'=>route('purchase-orders.'.($segment_name=='upload'?'process-upload':'store'))]) }}

                <div class="card-box">

                    @if( ! $vendor->exists)

                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Vendor</label>
                            <div class="col-lg-3">

                                {{--{{ dump($vendors) }}--}}
                                <input id="vendor_id" type="text" list="originating_entity" class="form-control" value="" placeholder="Search Vendor">
                                <datalist id="originating_entity">
                                    @foreach($vendors->prepend('-- Select --','') as $vendor_id=>$vendor_name )
                                        <option value="{{ $vendor_name }}" id="{{ $vendor_id }}">{{ $vendor_name }}
                                        @endforeach
                                </datalist>

                                <h3 id="vendor-loading" class="d-none">Loading...</h3>
                            </div>
                            <div class="col-lg-2">
                                <a href="{{ route('users.create') }}?role=Vendor" class="btn btn-primary waves-effect waves-light">Add Vendor</a>
                            </div>

                        </div>

                    @else

                        <div class="form-group row">
                            {{ Form::Input('hidden', 'bill_to_id', $vendor->id) }}
                            <label class="col-lg-2 col-form-label">Vendor</label>
                            <div class="col-lg-3">

                                <address>
                                <h4 class="m-t-0 header-title"><b>{{ $vendor->name }}</b></h4>
                                    <p>{{ $vendor->details['address'] }}<br>{{ $vendor->details['address2'] }}</p>
                                    <p><a href="{{ request()->headers->get('referer') }}">Change</a></p>
                                </address>
                            </div>
                        </div>

                        {{--<div class="form-group row">--}}
                            {{--<label class="col-lg-2 col-form-label">Originating License Type</label>--}}
                            {{--<div class="col-lg-3">--}}
                                {{--<select class="form-control" name="origin_license_id" required>--}}
                                    {{--<option value="">- Select -</option>--}}

                                    {{--@foreach($vendor->licenses as $license)--}}
                                        {{--<option value="{{ $license->id }}">{{ $license->number }} ({{ $license->license_type->name }})</option>--}}
                                    {{--@endforeach--}}

                                {{--</select>--}}
                            {{--</div>--}}

                        {{--</div>--}}

                        {{--<div class="form-group row">--}}
                            {{--<label class="col-lg-2 col-form-label">Vendor</label>--}}
                            {{--<div class="col-lg-3">--}}
                                {{--{{ Form::select("vendor_id", $vendors->prepend('-- Select --',''), $vendor->id, ['class'=>'form-control', 'required'=>'required']) }}--}}
                            {{--</div>--}}

                        {{--</div>--}}


<hr>

                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Purchase Date</label>
                            <div class="col-lg-3">
                                <input class="form-control" type="date" name="txn_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                        </div>


                        {{--<div class="form-group row">--}}
                            {{--<label class="col-lg-2 col-form-label">Destination License</label>--}}
                            {{--<div class="col-lg-3">--}}

                                {{--<select class="form-control" name="destination_license_id" required>--}}
                                    {{--<option value="">- Select -</option>--}}
                                    {{--@foreach($destination_licenses as $destination_license)--}}
                                        {{--<option {{ ($destination_license->is_valid()?:'disabled') }} value="{{ $destination_license->id }}">{{ $destination_license->display_name }} - Exp: {{ $destination_license->expires->format(config('highline.date_format')) }}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</div>--}}

                        {{--</div>--}}

                        <div class="form-group row">
                            {{ Form::label('terms', 'Terms', ['class'=>'col-lg-2 col-form-label']) }}
                            <div class="col-lg-3">
                                {{ Form::select("terms", config('highline.payment_terms'), ($vendor->details['terms']?:30), ['class'=>'form-control']) }}
                            </div>
                        </div>

                        {{ Form::Input('hidden', 'fund_id', 1) }}

                        {{--<div class="form-group row">--}}
                            {{--{{ Form::label('funds', 'Funding', ['class'=>'col-lg-2 col-form-label']) }}--}}
                            {{--<div class="col-lg-3">--}}
                                {{--{{ Form::select("fund_id", $funds->prepend('-- Select --',''), null, ['class'=>'form-control', 'required'=>'required']) }}--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group row">--}}
                            {{--<label class="col-lg-2 col-form-label">Manifest No.</label>--}}
                            {{--<div class="col-lg-3">--}}
                                {{--<input class="form-control" type="text" name="manifest_no" value="" required>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                    @endif

            </div>

                @if($vendor->exists)

                    @if($segment_name == 'upload')

                    <div class="upload-batches">

                        <h3 class="m-t-0 header-title"><b>Upload Packages</b></h3>

                        <br>
                        <a href="https://docs.google.com/spreadsheets/d/1TwGn_cUWQjI5NwYkC4bwPW_TdP4PagxpMaxE5KzKM_Y/edit?usp=sharing" target="_blank">Create Data File</a>
                        <br>

                        <div class="form-group row">

                            <div class="col-lg-12">

                                {{ Form::file('packages', ['class'=>'form-control']) }}
                                <br>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Upload</button>

                            </div>

                        </div>

                    </div>

                    @else

                    <div class="batch_items">

                        <h3 class="m-t-0 header-title"><b>Add Packages</b></h3>

                        <div class="batch_row card-box mb-3">

                            <h5 class="batch_number"><span>Batch 1</span> <a href="javascript:void(0);" class="d-none delete_batch btn btn-danger waves-effect waves-light pull-right"><i class="ion-trash-a"></i></a></h5>

                            <hr class="clearfix">

                            @include('purchase_orders/_add_item')

                        </div>

                        <a href="javascript:void(0);" id="add_batch" class="btn btn-primary waves-effect waves-light">Add Batch</a>

                        <hr class="">

                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save Purchase Order</button>

                    </div>

                    @endif

                @endif

            {{ Form::close() }}

        </div>

    </div>

@endsection

@section('js')

    <script type="text/javascript">
        $(document).ready(function() {

            $("#vendor_id").change(function() {

                var el=$("#vendor_id")[0];  //used [0] is to get HTML DOM not jquery Object
                var dl=$("#originating_entity")[0];
                if(el.value.trim() != '') {
                    var opSelected = dl.querySelector(`[value="${el.value}"]`);
                    $('#vendor-loading').addClass('d-block');
                    window.location = window.location.href + '/' + opSelected.getAttribute('id');
                }

            }).keypress(function(e) {
                if(e.which == 13) {
                    e.preventDefault();
                    return false;
                }
            });;

            // $('#vendor_id').change(function () {

            // });

            $('.batch_items').on('click', '.delete_batch', function() {
                // console.log('delete batch');
                $(this).parents('.batch_row').remove();
                set_batch_row_name();
                return;
            });

            $('#add_batch').click(function() {
                var new_row = $('.batch_row:first').clone();

                reset_values($(new_row).find(':input'));

                $(new_row).find('.delete_batch').removeClass('d-none');

                //set checkboxes
                update_checkboxes('cult_tax', $(new_row).find('.cult_tax'));
                update_checkboxes('in_metrc', $(new_row).find('.in_metrc'));

                $('.batch_row:last').after(new_row);
                set_batch_row_name();
            });

            // $(document).on('click', '.cult_tax', function() {
            //     if( $(this).is(":checked") ) {
            //         $(this).parents('.cult-tax-row').find('.tax_rate_id').show();
            //     } else {
            //         $(this).parents('.cult-tax-row').find('.tax_rate_id').hide();
            //     }
            // });

            $('.batch_items').on('blur', '.quantity', function() {

                var form_grp = $(this).parents('.batch_row');

                if($(form_grp).find('.unit_cost').val()) {
                    var qty = $(this).val();
                    var unit_cost = $(form_grp).find('.unit_cost').val();
                    $(form_grp).find('.total_cost').val((qty * unit_cost).toFixed(2));
                }
            });

            $('.batch_items').on('blur', '.unit_cost', function() {
                var form_grp = $(this).parents('.batch_row');
                var qty = $(form_grp).find('.quantity').val();
                var unit_cost = $(this).val();

                $(form_grp).find('.total_cost').val((qty * unit_cost).toFixed(2));
            });

            $('.batch_items').on('blur', '.total_cost', function() {

                var form_grp = $(this).parents('.batch_row');
                var qty = $(form_grp).find('.quantity').val();
                var total_cost = $(this).val();

                if(qty) $(form_grp).find('.unit_cost').val((total_cost / qty).toFixed(2));
            });

        } );

        var set_batch_row_name = function()
        {
            $('h5.batch_number span').each(function(index) {
                $(this).text('Batch '+(index+1))
            });

        }

        var reset_values = function(elems)
        {
            $(elems).each(function() {
                if($(this).is("select")) {
                    $(this, 'option:first').attr('selected','selected');
                } else {
                    $(this).val('');
                }
            });
        }

        var update_checkboxes = function (name, elems)
        {
            $(elems).each(function() {
                $(this).val(1).prop('checked',true);
                if($(this).is(':checkbox')) {
                    $(this).attr('id', name+'_'+($('.batch_row').length+1));
                }
                if($(this).is('label')) {
                    $(this).attr('for', name+'_'+($('.batch_row').length+1));
                }
            });
        }

    </script>


@endsection