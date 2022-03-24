@extends('layouts.app')

@section('content')

    <div class="row print-container">

        <div class="col-lg-12">
            <div class="card-box">

                <div class="row">

                    <div class="col" style="height: 300px;">

                        <div class="row">
                            <div class="col-sm-2">
                                <img style="height: 200px; width: 200px" src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('batches.show', $batch->ref_number), "QRCODE") }}">
                            </div>
                            <div class="col-sm-10">
                                <h4>{{ $batch->name }}</h4>
<br>
                                <h6><strong>Packaged:</strong> {{ ($batch->packaged_date ? $batch->packaged_date->format('m/d/Y') : '--' ) }}</h6>

                                <p>
                                    <strong>{{ $batch->category->name }}</strong><br><br>
                                    @if($batch->batch_number)
                                        <strong>Batch#:</strong> {{ $batch->batch_number }}<br>
                                        <strong>Package#:</strong> {{ $batch->ref_number }}<br>
                                    @else
                                        <strong>Batch#:</strong> {{ $batch->ref_number }}<br>
                                    @endif
                                    <strong>Batch Size:</strong> {{ $batch->units_purchased }} {{ $batch->uom }}<br>
                                    <strong>Package Id:</strong> {{ $batch->ref_number }}<br>
                                    <strong>Added to Inventory:</strong> {{ $batch->added_to_inventory_date }}<br>

                                </p>

{{--                                <h6>{{ $batch->purchase_order->added_to_inventory_date }}</h6>--}}
{{--                                <h6>{{ $batch->purchase_order->vendor->name }}</h6>--}}
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection