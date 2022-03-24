@extends('layouts.app')

@section('content')

    {{ $reconcile_logs->links() }}

    <div class="row mb-3 hidden-print">
        <div class="col-lg-12">

            <div class="card-box">

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="reconcile-log-datatable">

                        <thead>
                        <tr>
                            <th>Qty Adj.</th>
                            <th>St Wt.</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Batch ID</th>
                            <th>UID</th>
                            <th>Loss</th>
                            <th>Weight Loss</th>

                            <th>Short</th>
                            <th>Short Gs</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Comment</th>
                            <th>Date Reconciled</th>
                            <th>By</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($reconcile_logs as $reconcile_log)
                            @if( ! $reconcile_log->batch_converted->exists) @continue; @endif
                            <tr>
                                <td>
                                    @if($reconcile_log->batch_converted->wt_based)
                                        {{ $reconcile_log->quantity_transferred }} g<br> <small>{{ $reconcile_log->batch_converted->uom }}</small>
                                    @else
                                        {{ $reconcile_log->quantity_transferred }} <small>{{ $reconcile_log->batch_converted->uom }}</small>
                                    @endif

                                </td>
                                <td>{{ $reconcile_log->start_wt_grams }} <small>g</small></td>
                                <td>{{ ($reconcile_log->batch_converted->brand?$reconcile_log->batch_converted->brand->name:'') }}</td>
                                <td>{{ $reconcile_log->batch_converted->category?$reconcile_log->batch_converted->category->name:'--' }}</td>
                                <td><a href="{{ route('batches.reconcile-batch', $reconcile_log->batch_converted->ref_number) }}">{{ $reconcile_log->batch_converted->name }}</a></td>
                                <td>{{ $reconcile_log->batch_converted->batch_number }}</td>
                                <td><a href="{{ route('batches.show', $reconcile_log->batch_converted->ref_number) }}">{{ $reconcile_log->batch_converted->ref_number }}</a></td>

                                <td>{{ display_currency($reconcile_log->inventory_loss) }}</td>
                                <td>{{ $reconcile_log->inventory_loss_grams }}g</td>

                                <td>{{ display_currency($reconcile_log->shortage) }}</td>
                                <td>{{ $reconcile_log->shortage_grams }}g</td>

                                <td>{{ $reconcile_log->packer_name }}</td>
                                <td>{{ $reconcile_log->type }}</td>

                                <td>{{ $reconcile_log->reason }}</td>
                                <td>{{ $reconcile_log->notes }}</td>
                                <td>{{ $reconcile_log->created_at->format('m/d/Y h:ia') }}</td>
                                <td>{{ $reconcile_log->user->name }}</td>
                            </tr>

                        @endforeach

                        </tbody>

                    </table>
                </div>

            </div>

        </div>
    </div>

    {{ $reconcile_logs->links() }}

@endsection