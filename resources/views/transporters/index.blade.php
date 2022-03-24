@extends('layouts.app')


@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">

                <div id="accordion" role="tablist" aria-multiselectable="false">
                    @foreach($transporters as $transporter)

                        <div class="card">
                            <div class="card-header" role="tab" id="heading{{ $transporter->id }}">

                                <div class="row">
                                    <div class="col-md-2">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#T{{ $transporter->id }}" aria-expanded="true" aria-controls="collapse{{ $transporter->id }}">
                                            <i class="mdi mdi-loupe"></i> {{ $transporter->name }}
                                        </a>
                                    </div>
                                    {{--<div class="col-md-1"><strong>{{ $batch->type }}</strong></div>--}}
                                    {{--<div class="col-md-3"><strong>{{ $batch->category->name }}: {{ $batch->name }}</strong> <i>({{ $batch->quantity.$batch->uom }})</i></div>--}}
                                    {{--<div class="col-md-2"><strong>Bin#</strong> {{ $batch->bin_number?:'--' }}</div>--}}

                                    {{--<div class="col-md-1"><strong>THC:</strong> {{ ($batch->thc?:'-.-') }}%</div>--}}
                                    {{--<div class="col-md-1"><strong>CBD:</strong> {{ ($batch->cbd?:'-.-') }}%</div>--}}
                                    {{--<div class="col-md-1"><strong>CBN:</strong> {{ ($batch->cbn?:'-.-') }}%</div>--}}

                                    {{--<div class="col-md-1 text-right"><strong>{{ display_currency($batch->subtotal_purchase_price) }}</strong></div>--}}

                                </div>

                            </div>

                            <div id="T{{ $transporter->id }}" class="collapse {{ ($loop->iteration==1?'show':'') }}" role="tabpanel" aria-labelledby="heading{{ $transporter->id }}">

                                <div class="card-block">

                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Units</th>

                                                @role('admin')
                                                <th>Price / Unit</th>
                                                <th>Cost</th>
                                                @endrole

                                                <th>Sugg. Sale</th>
                                                <th>Inventory</th>
                                                <th>Picked Up</th>

                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php
                                            $tmp=0;
                                            ?>
                                            @foreach($transporter->batch_pickups as $batch_pickup)

                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td><a href="{{ route('batches.show', ['id'=>$batch_pickup->batch->ref_number]) }}">{{ $batch_pickup->batch->name }}<br><small>{{ $batch_pickup->batch->ref_number }}</small></a></td>
                                                    <td>{{ $batch_pickup->units }} {{ $batch_pickup->batch->uom }}</td>

                                                    @role('admin')
                                                    <td>{{ display_currency($batch_pickup->batch->unit_price) }}</td>
                                                    <td>{{ display_currency($batch_pickup->batch->unit_price * $batch_pickup->units) }}</td>
                                                    <?php $tmp += $batch_pickup->batch->unit_price * $batch_pickup->units; ?>
                                                    @endrole

                                                    <td>{{ display_currency($batch_pickup->batch->suggested_unit_sale_price) }}</td>
                                                    <td>{{ $batch_pickup->batch->inventory }} {{ $batch_pickup->batch->uom }}</td>
                                                    <td>{{ $batch_pickup->created_at->diffForHumans() }}<br><small>{{ $batch_pickup->created_at->format('m/d/Y H:i:s') }}</small></td>
                                                </tr>

                                            @endforeach

                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    @role('admin')
                                                    <td></td>
                                                    <td>{{ display_currency($tmp) }}</td>
                                                    @endrole
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>

                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                    @endforeach

                </div>
            </div>
        </div>
    </div>

@endsection