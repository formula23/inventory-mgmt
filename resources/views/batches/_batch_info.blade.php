<dl class="row">

    <dt class="col-xl-4 text-xl-right">ID:</dt>
    <dd class="col-xl-5">{{ $batch->id }}</dd>

    <dt class="col-xl-4 text-xl-right">Metrc:</dt>
    <dd class="col-xl-5">
        @if($batch->in_metrc)<i class=" mdi mdi-checkbox-marked text-success"></i>@else<i class=" mdi mdi-checkbox-blank text-danger"></i>@endif
    </dd>

    @can('po.show')
        @if($batch->purchase_order)
            <dt class="col-xl-4 text-xl-right">Purchase Order#:</dt>
            <dd class="col-xl-5"><a href="{{ route('purchase-orders.show', $batch->purchase_order) }}">{{ $batch->purchase_order->ref_number }}</a></dd>
        @endif
    @endcan

    <dt class="col-xl-4 text-xl-right">Source Batch:</dt>
    <dd class="col-xl-5">
            @if( ! empty($batch->parent_batch))
            <a href="{{ route('batches.show', $batch->parent_batch->ref_number) }}">
                {{ $batch->parent_batch->ref_number }}
            </a>
            @elseif(!empty($batch->source_batches) && $batch->source_batches->count() > 1)
                @foreach($batch->source_batches as $source_batch)

                <a href="{{ route('batches.show', $source_batch->ref_number) }}">
                    {{ $source_batch->ref_number }}
                    @if(!empty($source_batch->transfer_log))
                    ({{ $source_batch->transfer_log->quantity_transferred }} {{ $source_batch->uom }})
                    @endif
                </a><br>
                @endforeach
                @else
                --
            @endif
            {{--<a href="{{ route('batches.show', $batch->top_level_parent->ref_number) }}">--}}
            {{--{{ $batch->top_level_parent->batch_number?:$batch->top_level_parent->ref_number }}--}}
            {{--</a>--}}
    </dd>

    <dt class="col-xl-4 text-xl-right">License:</dt>
    <dd class="col-xl-5">
        {{ $batch->license->legal_business_name }} - {{ $batch->license->license_type->name }}<br>
        {{ $batch->license->number }}
    </dd>

    <dt class="col-xl-4 text-xl-right">Metrc/UID:</dt>

    <dd class="col-xl-5">
        {{ old('ref_number') }}
        @if(Request::segment(3)=='edit')
            <input type="text" class="form-control" id="ref_number" name="ref_number" value="{{ (old('ref_number')?:$batch->ref_number) }}">
        @else
            {{ $batch->ref_number }}
        @endif
    </dd>

    <dt class="col-xl-4 text-xl-right">Internal Batch#:</dt>
    <dd class="col-xl-5">
        @if(Request::segment(3)=='edit')
            <input type="text" class="form-control" id="batch_number" name="batch_number" value="{{ (old('batch_number')?:$batch->batch_number) }}">
        @else
        {{ ($batch->batch_number?:'--') }}
        @endif
    </dd>

    <dt class="col-xl-4 text-xl-right">Produced Batch:</dt>
    <dd class="col-xl-5">
        @if( ! empty($batch->created_batch))
        <a href="{{ route('batches.show', $batch->created_batch->ref_number) }}">
            {{ $batch->created_batch->ref_number }}
        </a>
        @else
        --
        @endif
    </dd>

    <dt class="col-xl-4 text-xl-right">Packaged Date :</dt>
    <dd class="col-xl-5">
        @if(Request::segment(3)=='edit')
        <input type="date" class="form-control" id="packaged_date" name="packaged_date" value="{{ ($batch->packaged_date?$batch->packaged_date->format('Y-m-d'):'') }}">
        @else
        {{ ($batch->packaged_date ? $batch->packaged_date->format('m/d/Y') : '--' ) }}
        @endif

    </dd>




<dt class="col-xl-4 text-xl-right">Added To Inventory:</dt>
<dd class="col-xl-5">{{ $batch->created_at->diffForHumans() }} - {{ $batch->created_at->format('m/d/Y') }}</dd>

@if($batch->created_at->diffInMinutes($batch->updated_at))
    <dt class="col-xl-4 text-xl-right">Last Updated:</dt>
    <dd class="col-xl-5">{{ $batch->updated_at->diffForHumans() }} - {{ $batch->updated_at->format('m/d/Y H:i:s') }}</dd>
@endif

<dt class="col-xl-4 text-xl-right">Batch Size:</dt>
<dd class="col-xl-5">{{ $batch->units_purchased }} {{ $batch->uom }}</dd>

    @can('batches.showcost')
<dt class="col-xl-4 text-xl-right">Unit Cost:</dt>
<dd class="col-xl-5">{{ display_currency($batch->unit_price) }}</dd>
    @endcan

</dl>