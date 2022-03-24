@extends('layouts.app')

@section('content')

    <style>

        .column1 {
            padding-left: 5px !important;
        }
        .column3 {
            padding-left: 30px !important;
        }
        .description p {
            margin: 0;
        }
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

    <div class="row print-container">

        <div class="col-lg-12">
            <div class="card-box">

                <?php $start_col=7; ?>

                    @foreach($qr_code_collection as $batch)

                        @if($loop->iteration==1 || ($c=$loop->iteration-1)%30 == 0)
                        <div class="row" style="padding-top:{{ $loop->iteration>9 ? '18px' : '0' }}">
                        @endif

                            <div class="col-4 {{ 'column'.( ! ($loop->iteration % 3) ? 3 : (($loop->iteration-1)%3 == 0 ? 1 : 2 ) ) }}" style="padding: 13px 15px; border: solid 1px #fff">

                                <div class="row" style="height: auto">

                                    <div class="col-xs-12">

                                    <div class="row no-gutters">

                                        <div class="col-xs-4" style="padding-left: 10px; padding-right: 10px;">
                                            <a href="{{ route('batches.show', $batch->ref_number) }}">
                                                <img style="height: 43px; width: 43px" src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('batches.show', $batch->ref_number), "QRCODE") }}">
                                            </a>
                                        </div>

                                        <div class="col-xs-8 description" style="font-size: 10px; color: #000;">
                                            <p><strong>{{ $batch->name }}</strong>{{ ($batch->type?' | '.$batch->type:'') }}</p>

                                            {{--THC: {{ $product->batch->thc }}% | CBD: {{ $product->batch->thc }}% | CBN: {{ $product->batch->thc }}%<br>--}}

                                            <p>
                                            @if($batch->uom == 'lb')
                                                @if($batch->units_purchased < 1)
                                                    {{ $batch->units_purchased }}
                                                @else
                                                    1
                                                @endif
                                                {{ $batch->uom }}
                                            @else
                                                __________ {{ $batch->uom }}
                                                {{--{{ $batch->units_purchased }} {{ $batch->uom }}--}}
                                            @endif
                                            </p>

                                            <p>#{{ $batch->ref_number }}</p>

                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-xs-3">
                                            <p style="font-size: 6px; margin-top: 5px; color: #000; " class="mb-0">&nbsp;</p>
                                        </div>
                                    </div>

                                    </div>

                                </div>

                            </div>

                        @if($loop->iteration == $loop->count || $loop->iteration%30 == 0)
                        </div> <!-- Close row div -->
                        @endif

                            {{--@endfor--}}

                        <?php $start_col = $start_col==7 ? 5 : 7 ; ?>

                    @endforeach

            </div>
        </div>
    </div>

@endsection