@extends('layouts.app')


@section('content')

    <div class="row mb-3 hidden-print">
        <div class="col-12">

            {{ Form::open(['route' => 'vault-logs.index', 'method' => 'get']) }}

            <div class="card">

                <div class="card-header cursor-pointer" role="tab" id="filters" >

                    <div class="row">
                        <div class="col-md-3">
                            <a href="#collapse-filters" data-toggle="collapse"><strong><i class="ti-arrow-circle-down"></i> Filters</strong></a>
                            <a href="{{ route('vault-logs.reset-filters') }}" class="small ml-2">Reset</a>
                        </div>
                        <div class="col-md-5">
                            @if($filters)
                                @foreach($filters as $filter=>$vals)
                                    <span style="margin-right: 15px;">{!! display_filters($filter, $vals, $vault_logs) !!}</span>
                                @endforeach
                            @endif
                        </div>
                        <div class="col-md-4 text-right">
                            {{--Subtotal:<strong>{{ display_currency($purchase_orders->sum('subtotal')) }}</strong> | Total:<strong>{{ display_currency($purchase_orders->sum('total')) }}</strong> | Outstanding Balance: <strong>{{ display_currency($purchase_orders->sum('balance')) }}</strong>--}}
                        </div>
                    </div>

                </div>

                <div id="collapse-filters" class=" card-block" role="tabpanel" aria-labelledby="collapse-filters" >

                    <div class="row">
                        <div class="col-lg-2">
                            <dl class="row">

                                <dt class="col-lg-4 text-lg-right">Brokers:</dt>
                                <dd class="col-lg-8">

                                    <select class="form-control mb-2" name="filters[broker_id]">
                                        <option value="">-- Brokers --</option>
                                        @foreach($brokers as $broker_id=>$broker_name)
                                            <option value="{{ $broker_id }}"{{ (isset($filters['broker_id']) ? ($broker_id == $filters['broker_id'] ? 'selected' : '' ) : '') }}>{{ $broker_name }}</option>
                                        @endforeach
                                    </select>

                                </dd>


                            </dl>

                        </div>

                        <div class="col-lg-3">
                            <dl class="row">
                                <dt class="col-lg-5 text-lg-right">Date Preset:</dt>
                                <dd class="col-lg-6">

                                    <select id="date_preset" name="filters[date_preset]" class="form-control">
                                        <option value="">- Select -</option>
                                        @for($i=0; $i<=3; $i++)
                                            <option value="{{ \Carbon\Carbon::now()->firstOfMonth()->subMonth($i)->format('m-Y') }}"{{ (isset($filters['date_preset']) ? (\Carbon\Carbon::now()->firstOfMonth()->subMonth($i)->format('m-Y') == $filters['date_preset'] ? 'selected' : '' ) : '') }}>{{ \Carbon\Carbon::now()->firstOfMonth()->subMonth($i)->format('F, Y') }}</option>
                                        @endfor
                                    </select>
                                </dd>
                                <dt class="col-lg-5 text-lg-right"></dt>
                                <dd class="col-lg-6"><p>-- OR --</p>
                                </dd>
                                <dt class="col-lg-5 text-lg-right">Custom Date:</dt>
                                <dd class="col-lg-6">
                                    From:<input class="form-control" type="date" name="filters[from_date]" value="{{ (isset($filters['from_date']) ? $filters['from_date'] : '') }}">
                                    To:<input class="form-control" type="date" name="filters[to_date]" value="{{ (isset($filters['to_date']) ? $filters['to_date'] : '') }}">
                                </dd>
                            </dl>

                        </div>


                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Filter</button>

                </div>

            </div>

            {{ Form::close() }}

        </div>
    </div>


    <hr>

<div class="row">

    <div class="col-lg-12 mb-3">
        <div id="datatable-buttons" class="pull-right"></div>
    </div>

</div>

<div class="row">
        <div class="col-lg-12">

            <div class="card-box">

                {{ $vault_logs->links() }}

                <div class="table-responsive">
                    <table id="user-datatable" class="table">

            @foreach($vault_logs->groupBy('session_id') as $session_id=>$vault_logs)

                            <thead>
                            <tr style="background-color: #efefef">
                                <td><h5>{{ $vault_logs->first()->created_at->format('m/d/Y') }}</h5>
                                    @if(request()->route('vault_log_session'))
                                        <a href="{{ route('vault-logs.index') }}"><i class="ion-unlocked font-16 text-success"></i> </a>
                                    @else
                                        <a href="{{ route('vault-logs.index', ['vault_log_session'=>$session_id]) }}"><i class="ion-locked text-danger font-16"></i> </a>
                                    @endif
                                </td>
                                <td><h5>{{ ($vault_logs->first()->broker?$vault_logs->first()->broker->name:$vault_logs->first()->order_title) }} | <a href="{{ route('vault-logs.return_order', ['vault_log_session'=>$session_id]) }}">Return Entire Order</a></h5> By: {{ $vault_logs->first()->user->name }}</td>

                                <td colspan="8">

                                </td>

                            </tr>
                            <tr>
                                <th>Vault Log</th>
                                <th>Strain</th>
                                <th>Quantity</th>
                                <th>Inventory</th>
                                <th>Notes</th>
                                <th>Price</th>
                                <th>Unit Cost</th>
                                <th>Vendor</th>
                                <th>Purchase Order</th>
                                <th>Sale Order</th>
                            </tr>
                            </thead>

                            <tbody>

                            @foreach($vault_logs as $vault_log)
                                <tr>
                                    <td><a href="{{ route('vault-logs.login', $vault_log->batch->ref_number) }}"><i class=" mdi mdi-qrcode"/></a></td>
                                    <td>
                                        <a href="{{ route('batches.show', $vault_log->batch->ref_number) }}">
                                            {{ $vault_log->present()->strain_name }}
                                        </a>
                                    </td>
                                    <td>{{ $vault_log->quantity }} {{ $vault_log->batch->uom }}</td>

                                    <td>{{ $vault_log->batch->inventory }} {{ $vault_log->batch->uom }}</td>
                                    <td>{{ $vault_log->notes }}</td>
                                    <td>{{ ($vault_log->price?display_currency($vault_log->price):"--") }}</td>
                                    <td>{{ display_currency($vault_log->batch->pre_tax_cost) }}</td>
                                    <td>{{ ( ! is_null($vault_log->batch->purchase_order) ? $vault_log->batch->purchase_order->vendor->name : "--" ) }}</td>
                                    <td>
                                        @if($vault_log->batch->purchase_order)
                                        <a href="{{ route('purchase-orders.show', $vault_log->batch->purchase_order->id) }}">
                                            {{ $vault_log->batch->purchase_order->ref_number }}
                                        </a>
                                            @else
                                            --
                                        @endif
                                    </td>
                                    <td>

                                        @if( ! empty($vault_log->order_detail) )

                                        <a href="{{ route('sale-orders.show', $vault_log->order_detail->sale_order) }}">{{ $vault_log->order_detail->sale_order->ref_number }}</a>

                                        @elseif($vault_log->CanBeAddedToSaleOrder)

                                            {{ Form::open(['url'=>route('vault-logs.add_to_sale_order', $vault_log)]) }}

                                            <div class="row">

                                                <div class="col-3">
                                                    <label>Sold As Name</label>
                                                    <input id="sold_as_name" type="text" value="{{ $vault_log->batch->present()->branded_name }}" name="sold_as_name" class="form-control" style="display: block;" required>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group row">
                                                        <label>Open Orders | <a href="{{ route('batches.show', ["ref_number"=>$vault_log->batch->ref_number, "vault_log_id"=>$vault_log->id]) }}">Create New Order</a></label>
                                                        <select class="form-control" name="sale_order_id">
                                                            <option value="">-- Sale Orders --</option>
                                                            @foreach($open_sales_orders as $open_sales_order)
                                                                <option value="{{ $open_sales_order->id }}">{{ $open_sales_order->ref_number }} - {{ $open_sales_order->customer->name }} - {{ $open_sales_order->txn_date->format('m/d/Y') }}{{ ($open_sales_order->broker?" - ".$open_sales_order->broker->name:"") }} - {{ Str::substr(preg_replace('/\s+/', ' ', trim($open_sales_order->notes)), 0, 10) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-4">
                                                    <div class="row">
                                                        {{--<div class="col-6">--}}
                                                            {{--<label>Price</label>--}}
                                                            {{--<div class="input-group bootstrap-touchspin">--}}
                                                                {{--<span class="input-group-addon bootstrap-touchspin-prefix">$</span>--}}
                                                                {{--<input id="sale_price" type="text" value="" name="sale_price" class="form-control" style="display: block;">--}}
                                                            {{--</div>--}}
                                                        {{--</div>--}}

                                                        <div class="col-8">
                                                            <label>Markup</label>
                                                            <div class="input-group bootstrap-touchspin">
                                                                <span class="input-group-addon bootstrap-touchspin-prefix">$</span>
                                                                <input id="cost_markup" type="text" value="" name="cost_markup" class="form-control" style="display: block;" placeholder="Markup Amount">
                                                            </div>
                                                            <i>Cost: {{ display_currency($vault_log->batch->pre_tax_cost) }}</i>
                                                        </div>

                                                        @if($vault_log->batch->unit_tax_amount)
                                                        <div class="col-4">
                                                            <label>Cutl Tax</label>
                                                            <select name="pass_cult_tax" class="form-control pass-cult-tax" style="width:75px">
                                                                <option value="1">Yes</option>
                                                                <option value="0">No</option>
                                                            </select>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-1">
                                                    <label>&nbsp;</label>
                                                    <div class="form-group row">
                                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Add</button>
                                                    </div>
                                                </div>

                                            </div>

                                            {{ Form::close() }}


                                        @elseif($vault_log->quantity > 0)

                                            <h4 class="text-danger">N/A</h4> <p>Batch Returned</p>

                                        @elseif($vault_log->batch->inventory < $vault_log->quantity*-1)

                                            <h6 class="text-danger">Not enough inventory available</h6>

                                        @endif
                                    </td>
                                </tr>
                            @endforeach


                            </tbody>

            @endforeach


                    </table>
                </div>
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
    <script src="{{ asset('plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('plugins/moment/min/moment.min.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

    <script type="text/javascript">
        // $(document).ready(function() {
        //
        //     $.fn.dataTable.moment('MM/DD/YYYY');
        //
        //     // $('[type="date"]').datepicker();
        //
        //     var table = $('#user-datatable').DataTable({
        //         lengthChange: true,
        //         paging: true,
        //         "order": [[ 1, "asc" ]],
        //         "displayLength": 25,
        //         buttons: ['excel', 'pdf', 'colvis']
        //     });
        //
        //     table.buttons().container().appendTo('#datatable-buttons');
        // } );

    </script>

@endsection