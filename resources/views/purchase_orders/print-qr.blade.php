@extends('layouts.app')

@section('content')

    <style>
        @media print {
            body {font-size: 11px;}
            .container {
                overflow: visible;
            }
            .col-5 {
                width: 47.5%;
                max-width: 47.5%;
                flex:0 0 47.5%
            }
            .col-7 {
                width: 52.5%;
                max-width: 52.5%;
                flex:0 0 52.5%
            }
            .print-container {
                margin-top: 12px;
            }
            .print-container .col-lg-12 {
                padding: 0;
            }
        }


    </style>


    <h1 class="header-title hidden-print">#<i>{{ $purchaseOrder->ref_number }}</i></h1>

    <div class="row hidden-print">

        <div class="col-lg-12">
            <div class="card-box">

                <dl class="row">
                    <dt class="col-1 text-right">Date:</dt>
                    <dd class="col-11">{{ $purchaseOrder->txn_date->format('m/d/Y') }}</dd>
                    <dt class="col-1 text-right">Buyer:</dt>
                    <dd class="col-11">{{ $purchaseOrder->user->name }}</dd>
                    <dt class="col-1 text-right">Vendor:</dt>
                    <dd class="col-11">{{ $purchaseOrder->vendor->name }}</dd>
                    <dt class="col-1 text-right">Total:</dt>
                    <dd class="col-11">{{ display_currency($purchaseOrder->total) }}</dd>
                </dl>
            </div>
        </div>
    </div>


    <h4 class="m-t-0 header-title hidden-print">Line Items</h4>

    <div class="row print-container">

        <div class="col-lg-12">
            <div class="card-box">

                <?php $start_col=7; ?>

                    @foreach($qr_code_collection as $batch)

                        @if($loop->iteration==1 || ($c=$loop->iteration-1)%10 == 0)
                        <div class="row" style="padding-top:{{ $loop->iteration>9 ? '20px' : '0' }}">
                        @endif

                            <div class="col-{{ ($start_col ) }} col-md-3" style="border: solid 1px #fff; padding: 15px;">

                                <div class="row" style="height: 160px">

                                    <div class="col-xs-12">

                                    <div class="row">
                                        <div class="col-xs-3">
                                            <a href="{{ route('batches.show', $batch->ref_number) }}">
                                                <img style="height: 70px; width: 70px" src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('batches.show', $batch->ref_number), "QRCODE") }}">
                                            </a>
                                            </div>
                                        <div class="col-xs-8 col-offset-1">
                                            <img src="/images/highline.png" style="margin-left: 0px; margin-top: 5px; width: 180px;"/>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-xs-12" style="margin-top: 10px;">
                                            #{{ $batch->ref_number }} | {{ $batch->name }} | {{ $batch->type }}<br>
                                            {{--THC: {{ $product->batch->thc }}% | CBD: {{ $product->batch->thc }}% | CBN: {{ $product->batch->thc }}%<br>--}}

{{--                                            @if($batch->category->uom != $product->batch->uom && $product->weight/$product->batch->category->unit_weight_grams >= 1)--}}
{{--                                                {{ number_format($product->weight/$product->batch->category->unit_weight_grams, 2) }} {{ $product->batch->category->uom }}--}}
                                            {{--@else--}}

                                            @if($batch->uom == 'lb')
                                                1 {{ $batch->uom }}
                                            @else
                                                {{ $batch->units_purchased }} {{ $batch->uom }}
                                            @endif
                                            <p style="font-size: 10px; margin-top: 15px;">Medical Cannabis In strict compliance with CA Prop 215 & S B 420.<br>Keep out of reach of children. Unlawful to redistribute.</p>

                                        </div>
                                    </div>

                                    </div>

                                </div>

                            </div>

                        @if($loop->iteration == $loop->count || $loop->iteration%10 == 0)
                        </div> <!-- Close row div -->
                        @endif

                            {{--@endfor--}}

                        <?php $start_col = $start_col==7 ? 5 : 7 ; ?>

                    @endforeach

            </div>
        </div>
    </div>

@endsection