@extends('layouts.app')


@section('content')

    <div class="row">

        <div class="col-lg-12 mb-3">

            <a class="btn btn-primary" href="{{ route('batches.reconcile-log') }}">Log</a>

            <div id="datatable-buttons" class="pull-right"></div>
        </div>

    </div>

    <div class="row mb-3 hidden-print">
        <div class="col-lg-12">

            {{ Form::open(['class'=>'form-horizontal', 'files'=>'true', 'url'=>route('batches.reconcile')]) }}

            <div class="card-box">

                {{ Form::file('adjustment_file', ['class'=>'form-control']) }}
                <br>
                <button type="submit" class="btn btn-primary waves-effect waves-light">Upload Adjustment File</button>

            </div>

            {{ Form::close() }}

        </div>
    </div>

    <div class="row mb-3 hidden-print">
        <div class="col-lg-12">
            {{ Form::open(['route' => 'batches.reconcile', 'method' => 'post']) }}

            <div class="card-box">

                {{--@foreach($batches->groupBy('category.name') as $category_name=>$batches)--}}

{{--                    <h3>{{ $category_name }}</h3>--}}
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="inventory-datatable">

                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Batch ID</th>
                                <th>UID</th>
                                <th>Brand</th>
                                <th>Name</th>
                                {{--<th>Vendor</th>--}}
                                <th>Added</th>
                                <th>Cost</th>

                                <th>Inventory</th>
                                <th>Packaged Date</th>
                                <th>Change To</th>
                                <th>Reason</th>
                                <th>Notes</th>
                            </tr>
                        </thead>

                        <tbody>

                        @foreach($batches as $batch)
                            <tr>
                                <td>{{ $batch->category->name }}</td>
                                <td>{{ $batch->batch_number }}</td>
                                <td><a href="{{ route('batches.show', $batch->ref_number) }}">{{ $batch->ref_number }}</a></td>
                                <td>{{ ($batch->brand?$batch->brand->name:'') }}</td>
                                <td>{{ $batch->name }}</td>
{{--                                <td>{{ ($batch->purchase_order ? $batch->purchase_order->vendor->name : '') }}</td>--}}
                                <td>{{ $batch->added_to_inventory }}</td>
                                <td>{{ display_currency($batch->unit_price) }}</td>
                                <td>
                                    @if($batch->wt_based)
                                        {{ $batch->wt_grams }} g<br>
                                        <small><i>{{ $batch->inventory }}-{{ $batch->uom }}</i></small>
                                        <input type="hidden" name="batch[{{ $batch->id }}][current_value]" value="{{ $batch->wt_grams }}" />
                                    @else
                                        {{ $batch->inventory }} {{ $batch->uom }}
                                        <input type="hidden" name="batch[{{ $batch->id }}][current_value]" value="{{ $batch->inventory }}" />
                                    @endif


                                </td>
                                <td>{{ ($batch->packaged_date?$batch->packaged_date->format('m/d/Y'):'') }}</td>
                                <td>

                                    @if($batch->wt_based)

                                        <div class="input-group bootstrap-touchspin">
                                            <input id="new_inventory" type="text" value="{{ $batch->wt_grams }}" name="batch[{{$batch->id}}][new_value]" class="form-control">
                                            <span class="input-group-addon bootstrap-touchspin-postfix">g</span>
                                        </div>
                                        {{ $batch->inventory }}-{{ $batch->uom }}

                                    @else

                                        <div class="input-group bootstrap-touchspin">
                                            <input id="new_inventory" type="text" value="{{ $batch->inventory }}" name="batch[{{$batch->id}}][new_value]" class="form-control">
                                            <span class="input-group-addon bootstrap-touchspin-postfix">{{ $batch->uom }}</span>
                                        </div>

                                    @endif
                                </td>
                                <td>
                                    <select name="batch[{{$batch->id}}][reason]" id="reason" class="form-control">
                                        <option value="">-- Select --</option>
                                        <option value="COG Adjustment">COG Adjustment</option>
                                        <option value="Shrinkage">Shrinkage</option>
                                        <option value="Shortage">Shortage</option>
                                        <option value="Lab Test Sample">Lab Test Sample</option>
                                        <option value="Bonus">Bonus</option>
                                        <option value="Sample Kit">Sample Kit</option>
                                        <option value="Samples">Samples</option>
                                        <option value="Promotional">Promotional</option>
                                        <option value="Destroyed">Destroyed</option>
                                        <option value="Damaged">Damaged</option>
                                        <option value="Waste">Waste</option>
                                    </select>
                                    {{--<input id="reason" type="text" value="" name="batch[{{$batch->id}}][reason]" class="form-control">--}}
                                </td>
                                <td>
                                    <input id="reason" type="text" value="" name="batch[{{$batch->id}}][notes]" class="form-control">
                                </td>
                            </tr>

                        @endforeach

                        </tbody>

                    </table>
                </div>
                {{--@endforeach--}}

            </div>

            <button class="btn btn-primary waves-effect waves-light" type="submit">Reconcile</button>

            {{ Form::close() }}
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
        $(document).ready(function() {

            // $.fn.dataTable.moment('MM/DD/YYYY');

            var table = $('#inventory-datatable').DataTable({
                // lengthChange: true,
                paging: false,
                "order": [[ 2, "asc" ]],
                // "displayLength": 25,
                buttons: ['excel', 'pdf', 'colvis']
            });

            table.buttons().container().appendTo('#datatable-buttons');
        } );

    </script>

@endsection