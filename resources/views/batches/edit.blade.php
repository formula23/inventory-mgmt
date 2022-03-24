@extends('layouts.app')

@section('content')

    @can('batches.show')
        <a href="{{ route('batches.show', $batch->ref_number) }}" class="btn btn-primary waves-effect waves-light m-b-10">Back</a>
    @endcan

    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">

                {{ Form::model($batch, ['class'=>'form-horizontal', 'url'=>route('batches.update', $batch->ref_number)]) }}

                {{ method_field('PUT') }}

                <div class="row">

                    <div class="col-xl-7">
                        <h2>{{ $batch->category->name }}: {{ $batch->present()->branded_name }}</h2>

                        @include('batches._batch_info', $batch)



                        <dl class="row">

                            <dt class="col-xl-4 text-xl-right">Status:</dt>
                            <dd class="col-xl-5">
                                {{ Form::select('status', array_combine(config('highline.batch_statuses'), config('highline.batch_statuses')), ucfirst($batch->status), ['class'=>'form-control']) }}
                                {{--<span class="badge badge-{{ status_class($batch->status) }}">{!! display_status($batch->status) !!}</span>--}}
                            </dd>

                            <dt class="col-xl-4 text-xl-right">Cultivator:</dt>
                            <dd class="col-xl-5">

                                <select name="cultivator_id" id="cultivator_id" class="form-control">
                                    <option value="">-- Select --</option>
                                    @foreach($cultivators as $cultivator)
                                        <option value="{{ $cultivator->id }}" {{ ($cultivator->id == $batch->cultivator_id?"selected='selected'":"") }}>{{ $cultivator->name }}</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('users.create') }}" class="">Add New Cultivator</a>
                            </dd>

                            <dt class="col-xl-4 text-xl-right">Cultivation Date:</dt>
                            <dd class="col-xl-5">
                                @if(is_null($batch->parent_id))
                                    <input type="date" class="form-control" id="cultivation_date" name="cultivation_date" value="{{ ($batch->cultivation_date?$batch->cultivation_date->format('Y-m-d'):'') }}">
                                @else
                                    {{ $batch->harvest_date }}<br >
                                    <a href="{{ route('batches.edit', $batch->top_level_parent->ref_number) }}">Edit Source Cultivaton Date</a>
                                @endif
                            </dd>

                        </dl>

                        <dl class="row">
                            <dt class="col-xl-4 text-xl-right">Testing Laboratory:</dt>
                            <dd class="col-xl-5">
                                <select name="testing_laboratory_id" id="testing_laboratory_id" class="form-control">
                                    <option value="">-- Select --</option>
                                    @foreach($testing_laboratories as $testing_laboratory)
                                        <option value="{{ $testing_laboratory->id }}" {{ ($testing_laboratory->id == $batch->testing_laboratory_id?"selected='selected'":"") }}>{{ $testing_laboratory->name }} (Lic# {{ $testing_laboratory->details['lab_license_number'] }})</option>
                                    @endforeach
                                </select>
                                <a href="{{ route('users.create') }}" class="">Add New Lab</a>
                            </dd>

                            <dt class="col-xl-4 text-xl-right">Testing Completed Date:</dt>
                            <dd class="col-xl-5">
                                <input type="date" class="form-control" id="tested_at" name="tested_at" value="{{ ($batch->tested_at?$batch->tested_at->format('Y-m-d'):'') }}">
                            </dd>

                            <dt class="col-xl-4 text-xl-right">Testing Status:</dt>
                            <dd class="col-xl-5">



                                @if( ! in_array($batch->testing_status, ['Passed','Failed', 'In-Testing']) )
                                    {{ Form::select('testing_status', array_combine(config('highline.testing_statuses'),config('highline.testing_statuses')), $batch->testing_status, ['placeholder' => '- Select -','class'=>'form-control']) }}
                                @else
                                    <span class="badge badge-{{ status_class($batch->testing_status) }}">{!! display_status($batch->testing_status) !!}</span>
                                    @if($batch->inTesting())
                                        <a href="{{ route('batches.show', $batch->ref_number) }}">Add COA here...</a>
                                    @endif
                                @endif
                            </dd>
                        </dl>

                        <dl class="row">

                            <dt class="col-xl-4 text-xl-right">Brand:</dt>
                            <dd class="col-xl-5">
                                {{ Form::select('brand_id', $brands->pluck('name','id')->toArray(), null, ['placeholder' => '- Select -','class'=>'form-control']) }}
                            </dd>

                            <dt class="col-xl-4 text-xl-right">Category:</dt>
                            <dd class="col-xl-5">
                            {{ Form::select('category_id', $categories->pluck('name','id')->toArray(), null, ['class'=>'form-control']) }}
                            </dd>

                            <dt class="col-xl-4 text-xl-right">Name:</dt>
                            <dd class="col-xl-5"><input type="text" class="form-control" id="name" name="name" value="{{ $batch->name }}"></dd>

                            <dt class="col-xl-4 text-xl-right">Short Name:</dt>
                            <dd class="col-xl-5"><input type="text" class="form-control" id="description" name="description" value="{{ $batch->description }}"></dd>



                            <dt class="col-xl-4 text-xl-right">Type:</dt>
                            <dd class="col-xl-5">{{ ($batch->type?:'--') }}</dd>

                            @can('batches.show.cost')
                                <dt class="col-xl-4 text-xl-right">Unit Cost:</dt>
                                <dd class="col-xl-5">{{ display_currency($batch->unit_price) }}</dd>
                            @endcan

                        </dl>

                        <dl class="row">

                            <dt class="col-xl-4 text-xl-right">Sugg. Sale Price:</dt>
                            <dd class="col-xl-5">
                                <div class="input-group bootstrap-touchspin">
                                    <span class="input-group-addon bootstrap-touchspin-prefix">$</span>
                                    <input id="suggested_unit_sale_price" type="number" value="{{ ($batch->suggested_unit_sale_price?:'') }}" name="suggested_unit_sale_price" class="form-control" style="display: block;" placeholder="Sugg. Sale Price" step="0.01">
                                </div>
                            </dd>

                            <dt class="col-xl-4 text-xl-right">Min. Flex:</dt>
                            <dd class="col-xl-5">
                                <input type="text" class="form-control" id="min_flex" name="min_flex" value="{{ ($batch->min_flex?:'') }}" placeholder="Min. Flex - Ex: 100">

                                {{--<select id="min_range_price" name="min_flex" class="form-control col-lg-3">--}}
                                    {{--<option value="0">0</option>--}}
                                    {{--@for($min_f=50;$min_f<=300;$min_f+=50)--}}
                                        {{--<option value="{{ $min_f }}" {{ ( $batch->min_flex == $min_f ? 'selected' : '' ) }}>-{{ $min_f }}</option>--}}
                                    {{--@endfor--}}
                                {{--</select>--}}
                            </dd>

                            <dt class="col-xl-4 text-xl-right">Max. Flex:</dt>
                            <dd class="col-xl-5">
                                <input type="text" class="form-control" id="max_flex" name="max_flex" value="{{ ($batch->max_flex?:'') }}" placeholder="Max. Flex - Ex: 100">
                                {{--<select id="max_range_price" name="max_flex" class="form-control col-lg-3">--}}
                                    {{--@for($max_f=200;$max_f>=50;$max_f-=50)--}}
                                        {{--<option value="{{ $max_f }}" {{ ( $batch->max_flex == $max_f ? 'selected' : '' ) }}>+{{ $max_f }}</option>--}}
                                    {{--@endfor--}}
                                        {{--<option value="0"{{ ( ! $batch->max_flex ? 'selected' : '' ) }}>0</option>--}}
                                {{--</select>--}}
                            </dd>

                        </dl>

                    </div>

                    <div class="col-xl-5">
                        <h6>Additional Info</h6>

                        {{--<div class="form-group">--}}
                            {{--<label for="description">Description</label>--}}
                            {{--{{ Form::textarea('description', $batch->description, ['class'=>'form-control', 'rows'=>'4', 'placeholder'=>'Add a Description']) }}--}}
                        {{--</div>--}}

                        <div class="form-group">
                            {{--<label for="sales_notes">Sales Notes</label>--}}
                            {{ Form::textarea('sales_notes', $batch->sales_notes, ['class'=>'form-control', 'rows'=>'4', 'placeholder'=>'Add Sales Notes']) }}
                        </div>

                        @if($batch->character)
                        <h6>Characteristics</h6>
                        {{ implode(", ", (array)$batch->character) }}
                        @endif

                        @if(is_null($batch->parent_id))
                        <hr>

                        <h6>R&D Results</h6>

                        <div class="row">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="rnd_link">Link</label>
                                    {{ Form::input('text', 'rnd_link', $batch->rnd_link, ['class'=>'form-control', 'rows'=>'4', 'placeholder'=>'Link']) }}
                                </div>
                            </div>

                        </div>

                        @if($batch->rnd_link)
                            <div class="form-group">
                                <a href="{{ $batch->rnd_link }}" target="_blank">
                                    <img style="height: 120px; width: 120px" src="data:image/png;base64,{{ DNS2D::getBarcodePNG($batch->rnd_link, "QRCODE") }}">
                                </a>
                            </div>
                        @endif

                        <div class="row">

                            <div class="col-4">
                                <label for="thc_rnd">THC</label>
                                <div class="input-group bootstrap-touchspin">
                                <input id="thc_rnd" type="number" value="{{ $batch->thc_rnd }}" name="thc_rnd" min="0" max="100.00" step="0.01" class="form-control col-lg-10" style="display: block;" placeholder="0.00">
                                <span class="input-group-addon bootstrap-touchspin-postfix">%</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="cbd_rnd">CBD</label>
                                <div class="input-group bootstrap-touchspin">
                                <input id="cbd_rnd" type="number" value="{{ $batch->cbd_rnd }}" name="cbd_rnd" step="0.01" min="0" max="100" class="form-control col-lg-10" style="display: block;" placeholder="0.00">
                                <span class="input-group-addon bootstrap-touchspin-postfix">%</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="cbn_rnd">CBN</label>
                                <div class="input-group bootstrap-touchspin">
                                <input id="cbn_rnd" type="number" value="{{ $batch->cbn_rnd }}" name="cbn_rnd" step="0.01" min="0" max="100" class="form-control col-lg-10" style="display: block;" placeholder="0.00">
                                <span class="input-group-addon bootstrap-touchspin-postfix">%</span>
                                </div>
                            </div>

                        </div>

                        @endif

                        @if($batch->coa_link)
                        <hr>

                        <h6>COA Results</h6>

                        <div class="row">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="rnd_link">Link</label>
                                    {{ Form::input('text', 'coa_link', $batch->coa_link, ['class'=>'form-control', 'rows'=>'4', 'placeholder'=>'Link']) }}
                                </div>
                            </div>

                        </div>

                        @if($batch->rnd_link)
                            <div class="form-group">
                                <a href="{{ $batch->coa_link }}" target="_blank">
                                    <img style="height: 120px; width: 120px" src="data:image/png;base64,{{ DNS2D::getBarcodePNG($batch->coa_link, "QRCODE") }}">
                                </a>
                            </div>
                        @endif

                        <div class="row">

                            <div class="col-4">
                                <label for="thc">THC</label>
                                <div class="input-group bootstrap-touchspin">
                                    <input id="thc" type="number" value="{{ $batch->thc }}" name="thc" min="0" max="100.00" step="0.01" class="form-control col-lg-10" style="display: block;" placeholder="0.00">
                                    <span class="input-group-addon bootstrap-touchspin-postfix">%</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="cbd">CBD</label>
                                <div class="input-group bootstrap-touchspin">
                                    <input id="cbd" type="number" value="{{ $batch->cbd }}" name="cbd" step="0.01" min="0" max="100" class="form-control col-lg-10" style="display: block;" placeholder="0.00">
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

                        @endif

                    </div>

                </div>

                <hr>

                <button class="btn btn-primary waves-effect waves-light w-md pull-right" type="submit">Save</button>
                <div class="clearfix"></div>

                {{ Form::close() }}

            </div>
        </div>
    </div>

@endsection