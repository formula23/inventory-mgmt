@extends('layouts.app')

@section('content')

    <div class="row mb-3 hidden-print">
        <div class="col-lg-12">

            {{ Form::open(['route' => 'batches.index', 'method' => 'get']) }}

            <div class="card">

                <div class="card-header cursor-pointer" role="tab" id="filters" >

                    <div class="row">
                        <div class="col-md-3">
                            <a href="#collapse-filters" data-toggle="collapse"><strong><i class="ti-arrow-circle-down"></i> Filters</strong></a>
                            <a href="{{ route('batches.reset-filters') }}" class="small ml-2">Reset</a>
                        </div>
                        <div class="col-md-9">
                            @if($filters)
                                @foreach($filters as $filter=>$vals)
                                    <span style="margin-right: 15px;">{!! display_filters($filter, $vals, $batches) !!}</span>
                                @endforeach
                            @endif

                        </div>

                    </div>

                </div>

                <div id="collapse-filters" class="collapse card-block" role="tabpanel" aria-labelledby="collapse-filters" >

                    <div class="row">
                        <div class="col-lg-2">

                            <dl class="row">
                                <dt class="col-lg-3 text-lg-right">In Stock:</dt>
                                <dd class="col-lg-9">

                                    @foreach(['Yes','No'] as $in_stock)
                                        <div class="checkbox">
                                            <input id="checkbox_in_stock_{{$in_stock}}" type="checkbox" name="filters[in_stock][{{$in_stock}}]" value="{{ $in_stock }}" {{ (isset($filters['in_stock']) ? (in_array($in_stock, array_keys($filters['in_stock']))?'checked':''):'') }}>

                                            <label for="checkbox_in_stock_{{$in_stock}}">
                                                {{ $in_stock }}
                                            </label>
                                        </div>
                                    @endforeach

                                </dd>
                            </dl>

                            {{--<dl class="row">--}}
                                {{--<dt class="col-lg-3 text-lg-right">Status:</dt>--}}
                                {{--<dd class="col-lg-9">--}}

                                    {{--@foreach(config('highline.batch_statuses') as $batch_status)--}}
                                        {{--<div class="checkbox">--}}
                                            {{--<input id="checkbox_{{$batch_status}}" type="checkbox" name="filters[status][{{$batch_status}}]" value="{{ ucwords($batch_status) }}" {{ (isset($filters['status']) ? (in_array($batch_status, array_keys($filters['status']))?'checked':''):'') }}>--}}

                                            {{--<label for="checkbox_{{$batch_status}}">--}}
                                                {{--<span class="badge badge-{{ status_class($batch_status) }}">{!! display_status($batch_status) !!}</span>--}}
                                            {{--</label>--}}
                                        {{--</div>--}}
                                    {{--@endforeach--}}

                                {{--</dd>--}}
                            {{--</dl>--}}


                            {{--<dl class="row">--}}
                                {{--<dt class="col-lg-3 text-lg-right">Testing Status:</dt>--}}
                                {{--<dd class="col-lg-9">--}}

                                    {{--@foreach(config('highline.testing_statuses') as $testing_status)--}}
                                        {{--<div class="checkbox">--}}
                                            {{--<input id="checkbox_test_{{$testing_status}}" type="checkbox" name="filters[testing_status][{{$testing_status}}]" value="{{ ucwords($testing_status) }}" {{ (isset($filters['testing_status']) ? (in_array($testing_status, array_keys($filters['testing_status']))?'checked':''):'') }}>--}}

                                            {{--<label for="checkbox_test_{{$testing_status}}">--}}
                                                {{--<span class="badge badge-{{ status_class($testing_status) }}">{!! display_status($testing_status) !!}</span>--}}
                                            {{--</label>--}}
                                        {{--</div>--}}
                                    {{--@endforeach--}}

                                {{--</dd>--}}
                            {{--</dl>--}}


                        </div>
                        <div class="col-lg-6">
                            <dl class="row">
                                <dt class="col-lg-2 text-lg-right">Category:</dt>
                                <dd class="col-lg-10">

                                    <div class="row">

                                    @foreach($categories as $category)
                                        <div class="col-3">
                                        <div class="checkbox">
                                            <input id="checkbox_{{ $category->id }}" type="checkbox" name="filters[category][{{ $category->id }}]" value="{{ $category->name }}" {{ (isset($filters['category'])?(in_array($category->id, array_keys($filters['category']))?'checked':''):'') }}>
                                            {{--//{{ ($filters?(in_array($batch_status, array_keys($filters['status']))?'checked':''):'') }}--}}
                                            <label for="checkbox_{{ $category->id }}">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                        </div>
                                    @endforeach

                                    </div>

                                </dd>
                            </dl>
                            <dl class="row">
                                <dt class="col-lg-2 text-lg-right">UOM:</dt>
                                <dd class="col-lg-10">

                                    <div class="row">

                                        @foreach(config('highline.uom') as $uom=>$gs)
                                            <div class="col-3">
                                                <div class="checkbox">
                                                    <input id="checkbox_{{ $uom }}" type="checkbox" name="filters[uom][{{ $uom }}]" value="{{ $uom }}" {{ (isset($filters['uom'])?(in_array($uom, array_keys($filters['uom']))?'checked':''):'') }}>
                                                    {{--//{{ ($filters?(in_array($batch_status, array_keys($filters['status']))?'checked':''):'') }}--}}
                                                    <label for="checkbox_{{ $uom }}">
                                                        {{ $uom }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>

                                </dd>
                            </dl>

                        </div>
                        <div class="col-lg-4">
                            <dl class="row">
                                <dt class="col-lg-3 text-lg-right">Name:</dt>
                                <dd class="col-lg-9">

                                    <input class="form-control" type="text" name="filters[name]" placeholder="" value="{{ (isset($filters['name']) ? $filters['name'] : '') }}">

                                </dd>
                            </dl>
                            <dl class="row">
                                <dt class="col-lg-3 text-lg-right">Batch/Unique/Pkg ID:</dt>
                                <dd class="col-lg-9">

                                    <input class="form-control" type="text" name="filters[batch_id]" placeholder="" value="{{ (isset($filters['batch_id']) ? $filters['batch_id'] : '') }}">

                                </dd>
                            </dl>

                            {{--<dl class="row">--}}
                                {{--<dt class="col-lg-3 text-lg-right">License:</dt>--}}
                                {{--<dd class="col-lg-9">--}}

                                    {{--<select id="license" name="filters[license_id]" class="form-control">--}}
                                        {{--<option value="">- Select -</option>--}}
                                        {{--@foreach($my_licenses as $my_license)--}}
                                            {{--<option value="{{ $my_license->id }}"{{ (isset($filters['license_id']) ? ($my_license->id == $filters['license_id'] ? 'selected' : '' ) : '') }}>{{$my_license->display_name}}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}

                                {{--</dd>--}}
                            {{--</dl>--}}

                            @can('batches.show.vendor')
                            <dl class="row">
                                <dt class="col-lg-3 text-lg-right">Vendor:</dt>
                                <dd class="col-lg-9">

                                    <select id="vendor" name="filters[vendor]" class="form-control">
                                        <option value="">- Select -</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}"{{ (isset($filters['vendor']) ? ($vendor->id == $filters['vendor'] ? 'selected' : '' ) : '') }}>{{$vendor->name}}</option>
                                        @endforeach
                                    </select>

                                </dd>
                            </dl>
                            @endcan

                            {{--<dl class="row">--}}
                                {{--<dt class="col-lg-3 text-lg-right">Brand:</dt>--}}
                                {{--<dd class="col-lg-9">--}}

                                    {{--<select id="brand" name="filters[brand]" class="form-control">--}}
                                        {{--<option value="">- Select -</option>--}}
                                        {{--@foreach($brands as $brand)--}}
                                            {{--<option value="{{ $brand->id }}"{{ (isset($filters['brand']) ? ($brand->id == $filters['brand'] ? 'selected' : '' ) : '') }}>{{$brand->name}}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}

                                {{--</dd>--}}
                            {{--</dl>--}}

                            {{--<dl class="row">--}}
                                {{--<dt class="col-lg-3 text-lg-right">In Metrc:</dt>--}}
                                {{--<dd class="col-lg-9">--}}

                                    {{--<select id="in_metrc" name="filters[in_metrc]" class="form-control">--}}
                                        {{--<option value="">- Select -</option>--}}
                                        {{--<option value="1"{{ (isset($filters['in_metrc']) ? (1 == $filters['in_metrc'] ? 'selected' : '' ) : '') }}>Yes</option>--}}
                                        {{--<option value="0"{{ (isset($filters['in_metrc']) ? (0 == $filters['in_metrc'] ? 'selected' : '' ) : '') }}>No</option>--}}
                                    {{--</select>--}}

                                {{--</dd>--}}
                            {{--</dl>--}}
                            {{--<dl class="row">--}}
                                {{--<dt class="col-lg-3 text-lg-right">Funding:</dt>--}}
                                {{--<dd class="col-lg-9">--}}
                                    {{--{{ Form::select("filters[fund_id]", $funds, (!empty($filters['fund_id'])?$filters['fund_id']:null), ['class'=>'form-control', 'placeholder'=>'-- Select --']) }}--}}
                                {{--</dd>--}}
                            {{--</dl>--}}
                        </div>


                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Filter</button>

                </div>

            </div>

            {{ Form::close() }}

        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">

                @can('batches.show.cost')
                <h5 class="hidden-print text-danger">Difference: {{ display_currency($total_inventory_value - $derived_inventory_value) }} </h5>
                <h5 class="hidden-print">Derived Inventory Value: {{ display_currency($derived_inventory_value) }}</h5>
                <h5 class="hidden-print">Total Inventory Value: {{ display_currency($total_inventory_value) }}</h5>
                <h5 class="hidden-print">Cultivation Tax Liability: {{ display_currency($cult_tax_liability) }}</h5>
                <h5 class="hidden-print">Filtered Inventory Value: {{ display_currency($filtered_inventory_value) }}</h5>
                @endcan
                {{--<a class="btn btn-primary pull-right mb-2 hidden-print" href="{{ route('batches.print-inventory') }}">Print Inventory</a>--}}

                <div class="clearfix"></div>


                {{--<hr>--}}
{{--                {{ dd($inventory_by_category) }}--}}
                @foreach($inventory_by_category as $category)
{{--{{ dd($category) }}--}}
                    {{--@foreach($batches1->groupBy('uom') as $uom => $batches)--}}


                    <div class="card mb-2">
                            <div class="card-header cursor-pointer" role="tab" id="heading-{{ $category['category_id'] }}" data-toggle="collapse" data-target="#collapse-{{ $category['category_id'] }}">

                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>{{ $category['name'] }}</strong>
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-2">@can('batches.show.cost')Inventory: <small>({{ display_currency($category['inventory_value']) }})</small>@endcan</div>

                                </div>

                            </div>

                            <div id="collapse-{{ $category['category_id'] }}" class="collapse" role="tabpanel" aria-labelledby="heading-{{ $category['category_id'] }}" >

                                <div class="card-body">

                                    <div class="table-responsive">

                                            @foreach($category['batches'] as $brand_name => $batches)


                                                <h6 style="padding: 10px 0 0 20px">{{ ($brand_name?:'White Label') }}</h6>


                                            <table id="inventory" class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>PO Date</th>
                                                    {{--<th>M</th>--}}
                                                    <th>Name</th>
                                                    <th>Lic</th>
                                                    {{--<th>COA</th>--}}
                                                    {{--<th>THC</th>--}}
                                                    <th>Inventory</th>

                                                    {{--<th class="hidden-print">Batch ID</th>--}}
                                                    <th class="hidden-print">Metrc/UID</th>
                                                    {{--<th class="hidden-print">Source UID</th>--}}
                                                    @can('batches.show.vendor')<th>Vendor</th>@endcan
                                                    {{--<th class="hidden-print">Batch Size</th>--}}
                                                    {{--@can(['so.show','batches.show.sold'])<th class="hidden-print">Sold</th>@endcan--}}
                                                    {{--@can('batches.transfer')<th>Transfer</th>@endcan--}}
                                                    @can('batches.show.cost')<th>Unit Cost</th>@endcan
                                                    @can('batches.show.cost')<th>Unit Tax</th>@endcan
                                                    {{--<th class="hidden-print">Sugg. Price</th>--}}
                                                    {{--<th>Date Added</th>--}}
                                                    <th>Added</th>
                                                    {{--@can('po.show')--}}
                                                        {{--<th class="hidden-print">PO#</th>--}}
                                                    {{--@endcan--}}
                                                    {{--<th class="hidden-print">Testing Status</th>--}}
                                                    {{--<th class="hidden-print">Status</th>--}}

                                                    <th class="hidden-print">
                                                        {{--<a href="{{ route('batches.qr-codes', ['id'=>$category['category_id']]) }}"><i class="mdi mdi-qrcode"></i></a>--}}
                                                    </th>
                                                </tr>
                                                </thead>

{{--{{ dd($batches->groupBy(['name','uom'])) }}--}}
                                                @foreach($batches->groupBy(['name','uom']) as $strain_name => $uoms)

                                                    @foreach($uoms as $uom => $batches)

                                                        @if($batches->count() > 1)

                                                            <tbody>
                                                                @include('batches._batch_master_row', ['batches'=>$batches])
                                                            </tbody>

                                                            <tbody id="group-{{ $category['category_id'] }}-{{ clean_string_strict($batches->first()->uom) }}-{{ clean_string_strict($batches->first()->name) }}-{{ $batches->first()->brand_id }}-{{ $loop->iteration }}" class="collapse bg-faded">
                                                            @foreach($batches->sortBy('ref_number') as $batch)
                                                                @include('batches._batch_row', $batch)
                                                            @endforeach

                                                            </tbody>

                                                        @else

                                                            <tbody>

                                                            @include('batches._batch_row', ['batch'=>$batches->first()])
                                                            </tbody>

                                                        @endif

                                                    @endforeach

                                                @endforeach

                                            </table>

                                            @endforeach


                                    </div>

                                </div>
                            </div>
                        </div>

                @endforeach

            </div>

        </div>

    </div>


@endsection