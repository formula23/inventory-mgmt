@extends('layouts.app')


@section('content')

    <h4>Search Results: <strong>{{ request('q') }}</strong></h4>

    <div class="row">
        <div class="col-12">

            <div class="card-box">

                <h4 class="header-title m-t-0 m-b-30">Batches</h4>

                @if($batches->count())

                    <table id="batches_datatable" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr role="row">

                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Id: activate to sort column ascending">Id</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending">Name</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Metrc/UID: activate to sort column ascending">Metrc/UID</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Batch Id: activate to sort column ascending">Batch Id</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Source: activate to sort column ascending">Source</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Available: activate to sort column ascending">Batch Size</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Available: activate to sort column ascending">Available</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Unit Cost: activate to sort column ascending">Cost</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Unit Cost: activate to sort column ascending">Added</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Testing Status: activate to sort column ascending">Testing Status</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Status</th>

                        </tr>
                        </thead>


                        <tbody class="table-striped">

                        @foreach($batches as $batch)

                            <tr role="row">
                                <td>{{ $batch->id }}</td>
                                <td>{{ $batch->category->name }}: {{ $batch->present()->branded_name }}</td>
                                <td><a href="{{ route('batches.show', $batch->ref_number) }}">{{ $batch->ref_number }}</a></td>
                                <td>{{ $batch->batch_number }}</td>
                                <td>
                                    @if($batch->purchase_order)
                                        <a href="{{ route('purchase-orders.show', $batch->purchase_order) }}">{{ $batch->purchase_order->ref_number }}</a>
                                        @elseif($batch->parent_batch)
                                        <a href="{{ route('batches.show', $batch->parent_batch->ref_number) }}">{{ $batch->parent_batch->ref_number }}</a>
                                    @endif
                                </td>
                                <td>{{ $batch->units_purchased }} {{ $batch->uom }}</td>
                                <td>{{ $batch->inventory }} {{ $batch->uom }}</td>
                                <td>{{ display_currency($batch->unit_price) }}</td>
                                <td>{{ ($batch->added_to_inventory) }}</td>
                                <td><span class="badge badge-{{ status_class($batch->testing_status) }}">{!! display_status($batch->testing_status) !!}</span></td>
                                <td><span class="badge badge-{{ status_class($batch->status) }}">{!! display_status($batch->status) !!}</span></td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                @else
                    <p class="text-muted m-b-15 font-13">No Results</p>
                @endif

            </div>

            <div class="card-box">

                <h4 class="header-title m-t-0 m-b-30">Purchase Orders</h4>

                @if($purchase_orders->count())
                    <table id="po_datatable" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Date: activate to sort column ascending">Order Date</th>
                            <th class="sorting_asc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="PO Number: activate to sort column descending" aria-sort="ascending">PO#</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Vendor: activate to sort column ascending">Vendor</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Total: activate to sort column ascending">Total</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Balance: activate to sort column ascending">Balance</th>
                        </tr>
                        </thead>


                        <tbody class="table-striped">

                        @foreach($purchase_orders as $purchase_order)

                            <tr role="row" class="even">
                                <td>{{ $purchase_order->txn_date->format(config('highline.date_format')) }}</td>
                                <td class="sorting_1"><a href="{{ route('purchase-orders.show', $purchase_order) }}">{{ $purchase_order->ref_number }}</a></td>
                                <td>
                                    <a href="{{ route('users.show', $purchase_order->vendor) }}">{{ $purchase_order->vendor->name }}</a>
                                    @if( ! empty($purchase_order->customer->details['business_name']))
                                        <br><i>{{ $purchase_order->customer->details['business_name'] }}</i>
                                    @endif
                                </td>
                                <td>{{ display_currency($purchase_order->total) }}</td>
                                <td>{{ display_currency($purchase_order->balance) }}</td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>
                @else
                    <p class="text-muted m-b-15 font-13">No Results</p>
                @endif


            </div>


            <div class="card-box">

                <h4 class="header-title m-t-0 m-b-30">Sale Orders</h4>

                @if($sale_orders->count())

                    <table id="so_datatable" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr role="row">
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Date: activate to sort column ascending">Order Date</th>
                            <th class="sorting_asc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="SO Number: activate to sort column descending" aria-sort="ascending">SO#</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Customer: activate to sort column ascending">Customer</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Total: activate to sort column ascending">Total</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Balance: activate to sort column ascending">Balance</th>
                        </tr>
                        </thead>


                        <tbody class="table-striped">

                        @foreach($sale_orders as $sale_order)

                        <tr role="row" class="even">
                            <td>{{ $sale_order->txn_date->format(config('highline.date_format')) }}</td>
                            <td class="sorting_1"><a href="{{ route('sale-orders.show', $sale_order) }}">{{ $sale_order->ref_number }}</a></td>
                            <td>
                                <a href="{{ route('users.show', $sale_order->customer) }}">{{ $sale_order->customer->name }}</a>
                                @if( ! empty($sale_order->customer->details['business_name']))
                                    <br><i>{{ $sale_order->customer->details['business_name'] }}</i>
                                @endif
                            </td>
                            <td>{{ display_currency($sale_order->total) }}</td>
                            <td>{{ display_currency($sale_order->balance) }}</td>
                        </tr>

                        @endforeach

                        </tbody>
                    </table>

                @else
                    <p class="text-muted m-b-15 font-13">No Results</p>
                @endif

            </div>

            <div class="card-box">

                <h4 class="header-title m-t-0 m-b-30">Vendors</h4>

                @if($vendors->count())

                    <table id="vendors_datatable" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr role="row">

                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Id: activate to sort column ascending">Id</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Customer: activate to sort column ascending">Name</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Phone: activate to sort column ascending">Phone</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Email</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Region: activate to sort column ascending">Region</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="House Account: activate to sort column ascending">House Account</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Active: activate to sort column ascending">Active</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Active: activate to sort column ascending">Balance</th>

                        </tr>
                        </thead>


                        <tbody class="table-striped">

                        @foreach($vendors as $vendor)

                            <tr role="row" class="even">

                                <td>{{ $vendor->id }}</td>
                                <td><a href="{{ route('users.show', $vendor) }}">{{ $vendor->name }}</a>
                                @if( ! empty($vendor->details['business_name']))
                                    <br><i>{{ $vendor->details['business_name'] }}</i>
                                @endif
                                </td>
                                <td>{{ $vendor->phone }}</td>
                                <td><a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a></td>
                                <td>{{ (!empty($vendor->details['region'])?$vendor->details['region']:'--') }}</td>
                                <td>{{ (!empty($vendor->details['house_account'])?'Yes':'No') }}</td>
                                <td>{{ $vendor->active?'Yes':'No' }}</td>
                                <td>{{ display_currency($vendor->outstanding_balance) }}</td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                @else
                    <p class="text-muted m-b-15 font-13">No Results</p>
                @endif

            </div>

            <div class="card-box">

                <h4 class="header-title m-t-0 m-b-30">Customers</h4>
{{--{{ dd($customers) }}--}}
                @if($customers->count())

                    <table id="customers_datatable" class="table table-striped table-bordered dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr role="row">

                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Id: activate to sort column ascending">Id</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending">Name</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Phone: activate to sort column ascending">Phone</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Email</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Region: activate to sort column ascending">Region</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="House Account: activate to sort column ascending">House Account</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Active: activate to sort column ascending">Active</th>
                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Active: activate to sort column ascending">Balance</th>

                        </tr>
                        </thead>


                        <tbody class="table-striped">

                        @foreach($customers as $customer)

                            <tr role="row" class="even">

                                <td>{{ $customer->id }}</td>
                                <td>
                                    <a href="{{ route('users.show', $customer) }}">{{ $customer->name }}</a>
                                    @if( ! empty($customer->details['business_name']))
                                        <br><i>{{ $customer->details['business_name'] }}</i>
                                    @endif
                                </td>
                                <td>{{ $customer->phone }}</td>
                                <td><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></td>
                                <td>{{ (!empty($customer->details['region'])?$customer->details['region']:'--') }}</td>
                                <td>{{ (!empty($customer->details['house_account'])?'Yes':'No') }}</td>
                                <td>{{ $customer->active?'Yes':'No' }}</td>
                                <td>{{ display_currency($customer->outstanding_balance) }}</td>

                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                @else
                    <p class="text-muted m-b-15 font-13">No Results</p>
                @endif

            </div>

        </div>
    </div>

@endsection

@section('css')

    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">

@endsection

@section('js')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('plugins/moment/min/moment.min.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

    <script type="text/javascript">
        jQuery(document).ready(function($) {

            $.fn.dataTable.moment('MM/DD/YYYY');

            $('.dataTable').DataTable({
                lengthChange: true,
                paging: true,
                order: [[0, "desc"]],
                displayLength: 10,
            });

            $('#batches_datatable').DataTable({
                retrieve: true,
                order: [[0, "asc"]],
                displayLength: 20,
            });


        });
    </script>



@endsection