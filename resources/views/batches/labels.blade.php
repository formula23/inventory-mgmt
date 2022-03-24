@extends('layouts.app')

@section('content')
<style>

    .label {font-size: 1.5em;}
    .mini-label { min-width: 400px !important; max-width: 400px !important; }
    /*.bin-label { min-width: 650px; max-width: 650px; min-height: 200px; max-height: 200px;overflow: hidden;}*/
    .badge {font-size: 25px;}
    .test-status { font-size: 25px; padding: 5px 20px; border: solid 1px #000; line-height: 40px;}
    .pending {color: #0b0b0b;}
    .in-testing {color: #f4c63d;}
    .pass {color: #00CC00;}
    .fail {color: #FF0000;}

</style>
    <a href="{{ route('batches.show', $batch->ref_number) }}" class="btn btn-primary waves-effect waves-light m-b-10">Batch Info</a>

    <h3>Bin Labels</h3>

    <div class="row">
        <div class="col-xl-8 bin-label">
            <div class="card-box">

                <div class="row">
                    <div class="col-xl-6">
                        <div class="">
                        <img src="/images/highline-200.png" class="m-b-20">
                        </div>
                        <div>
                            <b>Strain: </b>{{ $batch->name }}<br>
                            <b>Batch ID: </b>{{ $batch->batch_number?:'--' }}<br>
                            <b>Unique Pkg ID: </b>{{ $batch->ref_number }}<br>
                            <b>Received Date: </b>{{ ($batch->purchase_order ? $batch->purchase_order->txn_date->format(config('highline.date_format')) : '' ) }}<br>
                            <b>Cultivator: </b>
                                @if($batch->cultivator)
                                    {{ $batch->cultivator->name }} - #{{ $batch->cultivator->details['cult_rec_license_number'] }}
                                @else
                                    --
                                @endif
                            <br>
                            <b>Harvest Date: </b>{{ $batch->cultivation_date?$batch->cultivation_date->format(config('highline.date_format')):'--' }}<br>
                            <b>Tested By/On:</b>
                            @if($batch->testing_laboratory)
                                {{ $batch->testing_laboratory->name }} - {{ ($batch->tested_at?$batch->tested_at->format(config('highline.date_format')):'') }}
                                <br>#{{ (!empty($batch->testing_laboratory->details['lab_license_number'])?$batch->testing_laboratory->details['lab_license_number']:'') }}
                            @else
                                --
                            @endif
                            {{--<br><br>--}}

                            {{--<span class="test-status pending">Pending</span><span class="test-status in-testing">In-Testing</span><br>--}}
                            {{--<span class="test-status pass">Pass</span><span class="test-status fail">Fail</span>--}}

                        </div>

                    </div>
                    <div class="col-xl-6">
                        <dl class="row">
                            <dt class="col-xl-4">Batch Qty: </dt>
                            <dd class="col-xl-8" style="font-size: 80px; font-weight: bold;">{!!  display_inventory($batch, 'units_purchased', true)  !!} </dd>

                        </dl>

                        <div class="row">
                            <div class="col-xl-4">
                                <h4>COA</h4>
                                @if($batch->coa_link)
                                    <a href="{{ $batch->coa_link }}">
                                <img style="height: 50px; width: 50px" src="data:image/png;base64,{{ DNS2D::getBarcodePNG($batch->coa_link, "QRCODE") }}">
                                    </a>
                                    @endif
                            </div>
                            <div class="col-xl-8">
                                <h4>Additional Info</h4>
                                <a href="{{ route('batches.show', $batch->ref_number) }}">
                                <img style="height: 50px; width: 50px" src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('batches.show', $batch->ref_number), "QRCODE") }}">
                                </a>
                            </div>

                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <h3>In-Take Label</h3>

    <div class="row">
        <div class="col-xl-3 mini-label">
            <div class="card-box">

                <div class="row">
                    <div class="col-9">

                        <b>Strain:</b> {{ $batch->name }}<br>
                        <b>Batch ID:</b> {{ $batch->batch_number?:$batch->ref_number }}<br>
                        <b>Pkg ID:</b> {{ ($batch->batch_number && $batch->batch_number!=$batch->ref_number) ? $batch->ref_number :'--' }}<br>
                        <b>Cultivator:</b>
                        @if($batch->cultivator)
                            {{ $batch->cultivator->name }} - #{{ $batch->cultivator->details['cult_rec_license_number'] }}
                        @else
                            --
                        @endif
                        <br>
                        <b>Harvest:</b> {{ $batch->cultivation_date?$batch->cultivation_date->format(config('highline.date_format')):'--' }}<br>
                        <b>Batch Size:</b> {!!  display_inventory($batch, 'units_purchased', true)  !!}<br>
                        <b>Net Weight:</b> 454 g
                    </div>
                </div>
            </div>
        </div>
    </div>
<h3>COA Label</h3>
<div class="row">
    <div class="col-xl-3 mini-label">
        <div class="card-box">

            <div class="row">
                <div class="col-9">

                    <b>Strain:</b> {{ $batch->name }}<br>
                    <b>Batch ID:</b> {{ $batch->batch_number?:$batch->ref_number }}<br>
                    <b>Pkg ID:</b> {{ ($batch->batch_number && $batch->batch_number!=$batch->ref_number) ? $batch->ref_number :'--' }}<br>
                    <b>THC:</b> {{ ($batch->thc?$batch->thc.'%':'--') }} | <b>CBD:</b> {{ ($batch->cbd?$batch->cbd.'%':'--') }}<br>
                </div>
                <div class="col-3">
                    @if($batch->coa_link)
                        <a href="{{ $batch->coa_link }}">
                            <img style="height: 75px; width: 75px; margin-bottom: 5px;" src="data:image/png;base64,{{ DNS2D::getBarcodePNG($batch->coa_link, "QRCODE") }}">
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection