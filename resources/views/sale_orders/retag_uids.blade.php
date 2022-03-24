@extends('layouts.app')


@section('content')

<div class="row mt-3">

    <div class="col-lg-12 mb-3">

        <div class="card">
            <div class="card-block">

                <div class="row">
                    <div class="col-lg-3">

                        <h4>Re-tag batches on: <a href="{{ route('sale-orders.show', $saleOrder) }}">{{ $saleOrder->ref_number }}</a></h4>

                        <dl class="row">

                            <dt class="col-4 text-right">Sale Date:</dt>
                            <dd class="col-8">{{ $saleOrder->txn_date->format('m/d/Y') }}</dd>
                            <dt class="col-4 text-right">Sale Type:</dt>
                            <dd class="col-8">{{ ucwords($saleOrder->sale_type) }}</dd>

                            <dt class="col-4 text-right">Destination License:</dt>
                            <dd class="col-8">

                                @if( ! empty($saleOrder->destination_license) )
                                    <a href="{{ route('users.show', $saleOrder->destination_license->user->id) }}">
                                        {!! $saleOrder->destination_license->user->present()->name_address()  !!}
                                    </a>
                                    <br>
                                    {{ $saleOrder->destination_license->license_type->name }}:
                                @else
                                    {{ ucwords($saleOrder->customer_type) }}
                                @endif
                                <br>
                                @if( ! empty($saleOrder->destination_license))

                                    {{ $saleOrder->destination_license->number }}

                                @else

                                    @if(stripos($saleOrder->customer_type, 'microbusiness') !== false)
                                        @if(!empty($saleOrder->customer->details['mb_license_number']))
                                            {{ $saleOrder->customer->details['mb_license_number'] }}
                                        @endif
                                    @elseif( stripos($saleOrder->customer_type, 'distributor') !== false )
                                        @if($saleOrder->customer->details['distro_rec_license_number'])
                                            {{ $saleOrder->customer->details['distro_rec_license_number'] }}
                                        @elseif($saleOrder->customer->details['distro_med_license_number'])
                                            {{ $saleOrder->customer->details['distro_med_license_number'] }}
                                        @endif
                                    @elseif( stripos($saleOrder->customer_type, 'manufacturing') !== false)
                                        @if(!empty($saleOrder->customer->details['mfg_license_number']))
                                            {{ $saleOrder->customer->details['mfg_license_number'] }}
                                        @endif
                                    @else
                                        @if($saleOrder->customer->details['rec_license_number'])
                                            {{ $saleOrder->customer->details['rec_license_number'] }}
                                        @elseif($saleOrder->customer->details['med_license_number'])
                                            {{ $saleOrder->customer->details['med_license_number'] }}
                                        @endif
                                    @endif

                                @endif
                            </dd>

                            <dt class="col-4 text-right">Bill To:</dt>
                            <dd class="col-8">
                                <a href="{{ route('users.show', $saleOrder->customer->id) }}">{!! $saleOrder->customer->present()->name_address()  !!}</a>

                            </dd>

                        </dl>

                    </div>


                </div>

            </div>

        </div>

    </div>

</div>


<div class="row">

    <div class="col-lg-12">

        <div class="card-box">
            <h4 class="m-t-0 header-title">Line Items</h4>


            <div class="table-responsive">

                {{ Form::open(['url'=>route('sale-orders.retag-uids-process', $saleOrder->id)]) }}

                <table class="table table-hover table-striped">
                    <thead>

                    <tr>
                        <th class="hidden-print">Pkg#</th>
                        <th>Name</th>
                        <th>Qty</th>
                        <th class="hidden-print">Parent UID</th>
                        @if($batches_need_retag->count())
                        <th class="hidden-print">UID</th>
                        @endif

                    </tr>

                    </thead>

                    <tbody>
                    @php($counter=0)

                    @foreach($saleOrder->order_details->where('cog', 1)->groupBy('batch.uom') as $uom => $order_details)

                        @foreach($order_details->sortBy('sold_as_name') as $order_detail)

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $order_detail->batch->category->name }}: {{ $order_detail->sold_as_name }}</td>

                                <td>{{ $order_detail->units }} {{ $order_detail->batch->uom }}</td>

                                @if($batches_need_retag->has($order_detail->batch->id))

                                    <td>{{ $order_detail->batch->ref_number }}</td>
                                    <td>
                                        <input type="text" class="form-control {{ !empty($new_uid_tags[$counter])?($existing_uids->contains($new_uid_tags[$counter])?"text-danger":""):"" }}" name="new_uids[{{$order_detail->id}}]"
                                               value="{{ !empty($new_uid_tags[$counter])?$new_uid_tags[$counter]:"" }}" />
                                    </td>
                                    @php($counter++)
                                @else

                                    <td>
                                        @if($order_detail->batch->parent_batch)
                                            <a href="{{ route('batches.show', $order_detail->batch->parent_batch->ref_number) }}">{{ $order_detail->batch->parent_batch->ref_number }}</a>
                                        @elseif($order_detail->batch->child_batches->count())
                                            No parent - Created from many batches.
                                        @else
                                            No parent
                                        @endif
                                    </td>
                                    <td><a href="{{ route('batches.show', $order_detail->batch->ref_number) }}">{{ $order_detail->batch->ref_number }}</a></td>

                                @endif

                            </tr>

                        @endforeach

                    @endforeach

                    @if($batches_need_retag->count())
                    <tr>
                        <td colspan="5">
                            <button type="submit" class="btn btn-primary waves-effect waves-light pull-right">Save</button>

                        </td>

                    </tr>
                    @endif

                </tbody>

            </table>

                {{ Form::close() }}

            </div>


        </div>
    </div>
</div>
@endsection