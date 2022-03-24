@extends('layouts.app')

@section('content')

    <div class="row print-container">

        <div class="col-lg-12">
            <div class="card-box">

                <div class="row">

                @foreach($purchaseOrder->batches as $batch)

                    <div class="col-sm-6 col-md-6" style="height: 300px;">

                        <div class="row">
                            <div class="col-sm-5">
                                <img style="height: 120px; width: 120px" src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('batches.show', $batch->ref_number), "QRCODE") }}">
                            </div>
                            <div class="col-sm-7">
                                <h6>{{ $batch->name }} ({{ $batch->inventory }} {{ $batch->uom }})</h6>
                                <h6>{{ $batch->category->name }}</h6>
                                <h6>{{ $batch->purchase_order->added_to_inventory_date }}</h6>
                                <h6>{{ $batch->purchase_order->vendor->name }}</h6>
                            </div>
                        </div>

                    </div>

                    @if( ! ($loop->iteration % 6))
                        <div class="page-break"></div>
                    @endif

                @endforeach

                </div>

            </div>
        </div>
    </div>

@endsection