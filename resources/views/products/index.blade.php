@extends('layouts.app', ['title' => $title])
@section('content')


    <div class="row mb-3">
        <div class="col-lg-12">

            {{ Form::open(['route' => 'products.index', 'method' => 'get']) }}

            <div class="card">
                <div class="card-header">
                    Filters
                </div>
                <div class="card-block">
{{--{{ dd($filters) }}--}}
                    <div class="row">
                        <div class="col-4">
                            <dl class="row">
                                <dt class="col-3 text-right">Status:</dt>
                                <dd class="col-9">

                                    @foreach(config('highline.product_statuses') as $product_status)
                                        <div class="checkbox">
                                            <input id="checkbox_{{$product_status}}" type="checkbox" name="filters[status][{{$product_status}}]" {{ ($filters?(in_array($product_status, array_keys($filters['status']))?'checked':''):'') }}>
                                            <label for="checkbox_{{$product_status}}">
                                                <span class="badge badge-{{ status_class($product_status) }}">{!! display_status($product_status) !!}</span>
                                            </label>
                                        </div>
                                    @endforeach

                                    <span class="badge"></span>

                                </dd>
                            </dl>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4"></div>

                    </div>


                        {{--<h4 class="card-title">Special title treatment</h4>--}}
                    {{--<p class="card-text">With supporting text below as a natural lead-in to additional content.</p>--}}

                </div>
                <div class="card-footer text-muted">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Filter</button>
                </div>
            </div>

            {{ Form::close() }}

        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">



            <div class="card-box">
                <h4 class="m-t-0 header-title">Total Units: {{ $products->count() }}</h4>
                {{--<p class="text-muted font-14 m-b-20">--}}
                {{--All products that ever existed.--}}
                {{--</p>--}}

                <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>PO</th>
                        <th>Batch</th>
                        <th>SKU</th>
                        <th>Name</th>

                        @role('admin')
                            <th>Price</th>
                            <th>Sold</th>
                            <th>SO</th>
                            <th>Customer</th>
                            <th>Sale Price</th>
                            <th>Profit</th>
                        @endrole

                        <th></th>
                    </tr>
                    </thead>

                    @if( ! $products->count())

                        <p>No Products</p>

                    @else
                        <tbody>
                        @foreach($products as $product)

                            <tr>
                                <td scope="row">{{ $loop->iteration }}</td>
                                <td>{{ $product->created_at->format('m/d/Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ status_class($product->status) }}">{!! display_status($product->status) !!}</span>
                                </td>
                                <td><a href="{{ route('purchase-orders.show', ['id'=>$product->batch->purchase_order->id]) }}">{{ $product->batch->purchase_order->ref_number }}</a></td>
                                <td><a href="{{ route('batches.show', ['id'=>$product->batch->ref_number]) }}">{{ $product->batch->ref_number }}</a></td>
                                <td>{{ $product->ref_number }}</td>

                                <td>{{ $product->batch->category->name }}: {{ $product->batch->name }}</td>

                                @role('admin')
                                    <td>{{ display_currency($product->batch->unit_purchase_price * ($product->weight/454)) }}</td>
                                    <td><i class="{{ sold_class($product->status) }}"></i></td>

                                    @if($product->sale_order)
                                        <td>
                                            <a href="{{ route('sale-orders.show', ['id'=>$product->sale_order->id]) }}">{{ $product->sale_order->ref_number }}</a>
                                        </td>
                                        <td>
                                            {{ $product->sale_order->customer->name }}
                                        </td>
                                        <td>
                                            {{ display_currency($product->subtotal_sale_price) }}
                                        </td>
                                        <td>
                                            {{ display_currency($product->subtotal_sale_price - $product->unit_purchase_price) }} ({{ profit_pct($product, true) }})
                                        </td>
                                    @else
                                        <td>--</td>
                                        <td>--</td>
                                        <td>--</td>
                                        <td>--</td>
                                    @endif
                                @endrole

                                <td>

                                    @role('admin')
                                    <a href="{{ route('products.activity', ['id'=>$product->ref_number]) }}">Activity</a> |
                                    @endrole

                                    <a href="{{ route('products.show', ['id'=>$product->ref_number]) }}">Details</a>
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    @endif

                    </tbody>
                </table>
                </div>
            </div>

        </div>

    </div>


@endsection