<tr>
    <td>{{ $batch->id }}</td>
    <td>
        @if($batch->top_level_parent->purchase_order)
            {{ $batch->top_level_parent->purchase_order->txn_date->format(config('highline.date_format')) }}
        @endif
    </td>
    <td>
        <a href="{{ route('batches.show', $batch->ref_number) }}">
            {!! display_coa_icons($batch) !!}
            {{ $batch->present()->branded_name }}
        </a>
    </td>
    <td>{{ ( ! empty($batch->license) ? Str::substr($batch->license->license_type->name, 0, 3):'--') }}</td>
    {{--<td>--}}
    {{--@if(!empty($batch->COASourceBatch->coa_link))--}}
        {{--<a href="{{ $batch->COASourceBatch->coa_link }}" target="_blank"><i class="mdi mdi-qrcode"></i></a>--}}
    {{--@endif--}}
    {{--</td>--}}
    {{--<td>{{ display_potency_results($batch)  }}</td>--}}
    <td style="white-space: nowrap">
        {!! display_inventory($batch) !!}
        {{--@if($batch->wt_grams)--}}
            {{--<br><small>{{ $batch->wt_grams }} g</small>--}}
        {{--@endif--}}
    </td>
    {{--<td class="hidden-print">{{ ($batch->packaged_date ? $batch->packaged_date->format('m/d/Y') : '--' ) }}</td>--}}

    {{--<td class="hidden-print">--}}
{{--        {{ ($batch->batch_number?:'--') }}--}}
        {{--@if($batch->top_level_parent)--}}
            {{--<a href="{{ route('batches.show', $batch->top_level_parent->ref_number) }}">{{ ($batch->top_level_parent->batch_number?:$batch->top_level_parent->ref_number) }}</a>--}}
        {{--@else--}}
            {{--<a href="{{ route('batches.show', $batch->ref_number) }}">{{ ($batch->batch_number?:$batch->ref_number) }}</a>--}}
        {{--@endif--}}
    {{--</td>--}}

    <td class="hidden-print">
        {{--@if($batch->batch_number)--}}
            <a href="{{ route('batches.show', $batch->ref_number) }}">{{ $batch->ref_number }}</a>
        {{--@endif--}}
    </td>
    {{--<td>--}}
        {{--@if( ! empty($batch->parent_batch))--}}
            {{--<a href="{{ route('batches.show', $batch->parent_batch->ref_number) }}">{{ $batch->parent_batch->ref_number }}</a>--}}
        {{--@elseif(!empty($batch->source_batches) && $batch->source_batches->count() > 1)--}}
            {{--<span>(multi-package)</span>--}}
        {{--@endif--}}
    {{--</td>--}}

    @can('batches.show.vendor')
    <td>
        @if($batch->top_level_parent->purchase_order)
            {{ $batch->top_level_parent->purchase_order->vendor->name }}
        @endif
    </td>
    @endcan

    {{--<td class="hidden-print" style="white-space: nowrap">--}}
        {{--@if($batch->units_purchased)--}}
            {{--{{ $batch->units_purchased }} <small>{{ $batch->uom }}</small>--}}
        {{--@endif--}}
    {{--</td>--}}

    {{--@can(['so.show','batches.show.sold'])--}}
        {{--<td class="hidden-print" style="white-space: nowrap">--}}
            {{--@if($batch->order_details->sum('units') > 0)--}}
                {{--<a href="{{ route('batches.sales', $batch->ref_number) }}">{{ $batch->order_details->sum('units_accepted')  }}</a>--}}
                {{--({{$batch->order_details->groupBy('sale_order_id')->count()}})--}}
            {{--@else--}}
                {{------}}
            {{--@endif--}}
        {{--</td>--}}
    {{--@endcan--}}

    {{--@can('batches.transfer')--}}
        {{--<td style="white-space: nowrap">--}}
        {{--<a href="{{ route('batches.transfer-log', $batch->ref_number) }}">--}}
            {{--{{ $batch->transfer_pre_pack->sum('quantity_transferred') }}--}}
            {{--@if($batch->wt_based)--}}
                {{--<small> g</small>--}}
                {{--@else--}}
                {{--{{ $batch->uom }}--}}
            {{--@endif--}}
        {{--</a>--}}
        {{--</td>--}}
    {{--@endcan--}}

    @can('batches.show.cost')
        <td>
            {{ display_currency($batch->pre_tax_cost) }}
{{--            @if($batch->pre_tax_cost)--}}
            {{--<br><i><small>True Cost: {{ display_currency($batch->unit_price) }}</small></i>--}}
            {{--@endif--}}
        </td>
        <td>
            {{ display_currency($batch->unit_tax_amount) }}
        </td>
    @endcan

    {{--<td class="hidden-print">{{ display_currency($batch->suggested_unit_sale_price) }}</td>--}}

    {{--<td>{{ $batch->added_to_inventory_date }}</td>--}}
    <td>{{ $batch->added_to_inventory }}</td>

    {{--@can('po.show')--}}
        {{--<td class="hidden-print">--}}
            {{--@if($batch->purchase_order)--}}
                {{--<a href="{{ route('purchase-orders.show', $batch->purchase_order->id) }}">{{ $batch->purchase_order->ref_number }}</a>--}}
            {{--@endif--}}
        {{--</td>--}}
    {{--@endcan--}}

    {{--<td class="hidden-print"><span class="badge badge-{{ status_class($batch->top_level_parent->testing_status) }}">{!! display_status($batch->top_level_parent->testing_status) !!}</span></td>--}}
    {{--<td class="hidden-print"><span class="badge badge-{{ status_class($batch->testing_status) }}">{!! display_status($batch->testing_status) !!}</span></td>--}}
    {{--<td class="hidden-print"><span class="badge badge-{{ status_class($batch->status) }}">{!! display_status($batch->status) !!}</span></td>--}}

    <td class="hidden-print">
        @can('batches.edit')
            <a href="{{ route('batches.edit', $batch->ref_number) }}"><i class="mdi mdi-lead-pencil"></i></a>
        @endcan

        @can('batches.transfer')
        @if($batch->canCreatePackages())
            <a href="{{ route('batches.transfer', $batch->ref_number) }}"><i class="ion-loop"></i></a>
        @endif
        @endcan

        {{--<a href="{{ route('batches.qr-code', ['id'=>$batch->ref_number]) }}"><i class="mdi mdi-qrcode"></i></a>--}}
        @can('batches.reconcile')
        <br><a href="{{ route('batches.reconcile-batch', $batch->ref_number) }}">Adjust Qty</a>
        @endcan
    </td>
</tr>