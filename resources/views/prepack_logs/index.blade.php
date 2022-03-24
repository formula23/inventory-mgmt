@extends('layouts.app')


@section('content')

    {{ $transfer_logs->links() }}

    @foreach($transfer_logs->items() as $transfer_log)

        <div class="row">
            <div class="col-lg-12">

                <div class="card-box">

                    <div class="row">

                    <div class="col-lg-4">
                        id: {{ $transfer_log->id }}
                        <h4 class="text-dark  header-title m-t-0 m-b-30">
                            Packaged by: <small>{{ $transfer_log->packer_name }}</small><br>
                            <small>Entered: by {{ $transfer_log->user->name }}<br>{{$transfer_log->created_at->format('m/d/Y H:i:s')}}</small>
                        </h4>

                        <h4>{{ $transfer_log->quantity_transferred }} {{ $transfer_log->batch_converted->uom }} of <strong><a href="{{ route('batches.transfer-log', $transfer_log->batch_converted->ref_number) }}">{{ $transfer_log->batch_converted->name }}</a></strong></h4>

                        <h6>Loss: {{ display_currency($transfer_log->inventory_loss) }} - {{ ($transfer_log->inventory_loss_grams) }} grams</h6>

                        <p>@if($transfer_log->batch_converted->in_metrc)<i class=" mdi mdi-checkbox-marked text-success"></i>@else<i class=" mdi mdi-checkbox-blank text-danger"></i>@endif
                            Metrc UID: {{ $transfer_log->batch_converted->ref_number  }}<br>
                        Batch#: {{ $transfer_log->batch_converted->batch_number  }}</p>
                    </div>

                    <div class="col-lg-8">

                        <h5>Results:</h5>

                        <div class="row">

                        @foreach($transfer_log->transfer_log_details as $transfer_log_details)

                                <div class="col-lg-4">

                            <h6>{{ $transfer_log_details->batch_created->category->name }}</h6>

                                    @if($transfer_log_details->batch_created->in_metrc)<i class=" mdi mdi-checkbox-marked text-success"></i>@else<i class=" mdi mdi-checkbox-blank text-danger"></i>@endif

                                    Packaged Date: {{ ($transfer_log_details->batch_created->packaged_date?$transfer_log_details->batch_created->packaged_date->format(config('highline.date_format')):'--') }}<br>
                                    Batch {{ $transfer_log_details->action }}: <a href="{{ route('batches.show', $transfer_log_details->batch_created->ref_number) }}">{{ $transfer_log_details->batch_created->name }} - {{ $transfer_log_details->batch_created->ref_number }}</a><br>

                                {{--Batch Original Qty: {{ $transfer_log_details->batch_created->units_purchased }} <small>{{ $transfer_log_details->batch_created->uom }}</small><br>--}}
                                    Cost: {{ display_currency($transfer_log_details->batch_created->unit_price) }} / {{ $transfer_log_details->batch_created->uom }}<br>

                                    <h5>Batch Qty: {{ $transfer_log_details->units }} <small>{{ $transfer_log_details->batch_created->uom }}</small></h5>
                                    <h6>Avail Inv: {{ $transfer_log_details->batch_created->inventory }} <small>{{ $transfer_log_details->batch_created->uom }}</small></h6>

                                </div>

                        @endforeach

                        </div>

                    </div>

                    </div>

                </div>

            </div>
        </div>
    @endforeach

    {{ $transfer_logs->links() }}

@endsection