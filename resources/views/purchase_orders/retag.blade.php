@extends('layouts.app')


@section('content')

    <div class="row">

        <div class="col-lg-12">

            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-primary m-b-15">Back</a>

            <h2>{{ $purchaseOrder->ref_number }}</h2>

            <h4 class="m-t-0 header-title">Summary</h4>

            <div class="card-box">

                <div class="row">

                    <div class="col-lg-4">

                        <dl class="row" style="font-size: 12px">
                            <dt class="col-3 text-right">Total Cost:</dt>
                            <dd class="col-7">{{ display_currency($purchaseOrder->batches->sum('cost')) }}</dd>

                            <dt class="col-3 text-right">Total Revenue:</dt>
                            <dd class="col-7">{{ display_currency($purchaseOrder->batches->sum('revenue2')) }}</dd>

                            <dt class="col-3 text-right">Total Profit:</dt>
                            <dd class="col-7">
                                <span class="text-{{ ($purchaseOrder->batches->sum('margin2') > 0?'success':'danger') }}">{{ display_currency($purchaseOrder->batches->sum('margin2')) }}
                                    @if($purchaseOrder->batches->sum('revenue2')>0)
                                    ({{ number_format(($purchaseOrder->batches->sum('margin2') / $purchaseOrder->batches->sum('revenue2'))*100, 2) }}%)
                                    @endif
                                </span>
                            </dd>

                        </dl>

                        {{--<hr>--}}
                        {{--<dl class="row" style="font-size: 12px">--}}


                            {{--<dt class="col-3 text-right">Available Inventory:</dt>--}}
                            {{--<dd class="col-7">{{ $purchaseOrder->batches->sum('available_weight_grams') }} g ({{ $purchaseOrder->batches->sum('available_weight_pounds') }} lb)</dd>--}}

                            {{--<dt class="col-3 text-right">Pending weight grams:</dt>--}}
                            {{--<dd class="col-7">{{ $purchaseOrder->batches->sum('grams_pending') }}</dd>--}}

                            {{--<dt class="col-3 text-right">Pending weight pounds:</dt>--}}
                            {{--<dd class="col-7">{{ $purchaseOrder->batches->sum('pounds_pending') }}</dd>--}}

                            {{--<dt class="col-3 text-right">Accepted weight grams:</dt>--}}
                            {{--<dd class="col-7">{{ $purchaseOrder->batches->sum('weight_grams_accepted') }}</dd>--}}

                            {{--<dt class="col-3 text-right">Accepted weight pounds:</dt>--}}
                            {{--<dd class="col-7">{{ $purchaseOrder->batches->sum('pounds_accepted') }}</dd>--}}

                        {{--</dl>--}}
                    </div>
                </div>
            </div>


            <h4 class="m-t-0 header-title">Batch Package Report</h4>

            <div class="row">

                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-block">

                            <div class="table-responsive">
                                <table class="table m-t-30 ">
                                    <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Category: Name</th>
                                        <th>UID</th>
                                        <th>Qty / Unit Cost</th>
                                        <th>Sales</th>
                                        <th>Inventory</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @if($purchaseOrder->batches->count())

                                        @include('_child_batches', ['child_batches'=>$purchaseOrder->batches, 'depth'=>0])

                                    @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection