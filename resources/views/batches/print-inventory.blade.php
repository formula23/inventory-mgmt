@extends('layouts.app')


@section('content')

    @can('batches.show.cost')
    <div class="row">
        <div class="col-lg-12">
            @if($remove_cost)
                <a class="btn btn-primary pull-right mb-2 hidden-print" href="{{ route('batches.print-inventory') }}">Reset</a>
            @else
                <a class="btn btn-primary pull-right mb-2 hidden-print" href="{{ route('batches.print-inventory', 1) }}">Remove Cost</a>
            @endif
        </div>
    </div>
    @endcan

    <div class="row">
        <div class="col-lg-12">

            {{--{{ dd($inventory_by_category) }}--}}


            @foreach($inventory_by_category as $category)

                <div class="card mb-3">

                    <div class="card-header">

                        <div class="row">
                            <div class="col-sm-4">
                                <strong>{{ $category['name'] }}@can('batches.show.cost') - <small>({{ display_currency($category['inventory_value']) }})</small>@endcan</strong>
                            </div>
                        </div>

                    </div>

                    <div class="card-block">
                        <table id="inventory" class="table">
                            <thead>
                            <tr>
                                <th>PO Date</th>
                                <th>Qty</th>
                                <th>Name</th>
                                {{--<th>THC</th>--}}
                                {{--<th>CBD</th>--}}
                                <th>Batch ID</th>
                                <th>Unique/Pkg ID</th>

                                <th>Brand</th>
                                @if(!$remove_cost)
                                @can('batches.show.cost')<th>Cost</th>@endcan
                                @can('batches.show.cost')<th>Pre-Tax Cost</th>@endcan
                                {{--<th>Unit Cost w/Tax</th>--}}
                                {{--<th>Sugg. Sale Price</th>--}}
                                @can('batches.show.vendor')<th>Vendor</th>@endcan
                                <th>Added</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($category['batches'] as $brand_name => $batches)
                                <tr>
                                    <td colspan="8"><h6>{{ ($brand_name?:'White Label') }}</h6></td>
                                </tr>
                                @foreach($batches as $batch)

                                <tr>
                                    <td>{{ $batch->top_level_parent->purchase_order?$batch->top_level_parent->purchase_order->txn_date->format(config('highline.date_format')):"" }}</td>
                                    <td>{!! display_inventory($batch) !!}</td>
                                    <td>{{ $batch->name }}</td>
                                    {{--<td>{{ $batch->present()->thc_potency()  }}</td>--}}
                                    {{--<td>{{ $batch->present()->cbd_potency()  }}</td>--}}
                                    <td>{{ $batch->top_level_parent->batch_number?:$batch->top_level_parent->ref_number }}</td>
                                    <td>{{ $batch->ref_number }}</td>

                                    <td>{{ ($batch->brand?$batch->brand->name:'') }}</td>
                                    @if(!$remove_cost)

                                        @can('batches.show.cost')
                                    @if($batch->cost_includes_cult_tax)
                                        {{--<td>{{ display_currency($batch->unit_price) }}</td>--}}
                                        <td>
                                            @if($batch->uom=='g')
                                                {{ display_currency($batch->unit_price) }}<br>
                                                <small>({{ display_currency($batch->unit_price * 453.592) }} / lb)</small>
                                            @else

                                            {{ display_currency($batch->unit_price) }}

                                            @endif
                                        </td>
                                    @else
                                        {{--<td>{{ display_currency($batch->unit_price) }}</td>--}}
                                        <td>
                                            @if($batch->tax)
                                            {{ @display_currency($batch->unit_price + ($batch->tax/$batch->units_purchased)) }}
                                            @else
                                                {{ display_currency($batch->unit_price) }}
                                            @endif
                                        </td>
                                    @endif

                                            <td>

                                                @if($batch->uom=='g')
                                                    {{ display_currency($batch->pre_tax_cost) }}<br>
                                                    <small>({{ display_currency($batch->pre_tax_cost * 453.592) }} / lb)</small>
                                                @else
                                                    {{ display_currency($batch->pre_tax_cost) }}
                                                @endif

                                            </td>

                                    @endcan

{{--                                    <td>{{ display_currency($batch->suggested_unit_sale_price) }}</td>--}}


                                    @can('batches.show.vendor')
                                    <td>
                                        @if($batch->top_level_parent->purchase_order)
                                            {{ $batch->top_level_parent->purchase_order->vendor->name }}
                                        @endif
                                    </td>
                                    @endcan

                                    <td>{{ $batch->added_to_inventory }}</td>
                                    @endif

                                </tr>

                                @endforeach

                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

                {{--@if(($category['name']=='flower'))--}}
                {{--<div class="page-break"></div>--}}
                {{--@endif--}}
                {{----}}
            @endforeach

        </div>

    </div>

@endsection

@section('js')
    <script type="text/javascript">
        <!--
        // window.print();
        //-->
    </script>
@endsection