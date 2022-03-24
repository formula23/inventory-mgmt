@extends('layouts.app', ['title' => $title])

@section('content')

    <h1>Activity Log</h1>

    <h2>{{ $product->batch->category->name }}: {{ $product->batch->name }}</h2>

    @if($product->sale_order)
    <h3>SO# <a href="{{ route('sale-orders.show', $product->sale_order->id) }}">{{ $product->sale_order->ref_number }}</a></h3>
    @endif

    <div class="timeline">

        <article class="timeline-item alt">
            <div class="text-right">
                <div class="time-show first">
                    <span class="btn btn-primary w-lg">Now</span>
                </div>
            </div>
        </article>

        @foreach($activity_logs as $activity_log)

            <article class="timeline-item {{ $loop->iteration%2?'alt':'' }}">
                <div class="timeline-desk">
                    <div class="panel">
                        <div class="panel-body">
                            <span class="arrow-alt"></span>
                            <span class="timeline-icon"></span>
                            <h4 class="text-success">{{ $activity_log->action }}</h4>
                            <h4 class="text-danger">{{ $activity_log->created_at->diffForHumans() }}</h4>
                            <p class="timeline-date text-muted"><small>{{ $activity_log->created_at->format('m/d/Y H:i:s') }}</small></p>
                            <p>By: {{ $activity_log->user->name }}</p>
                        </div>
                    </div>
                </div>
            </article>

        @endforeach

    </div>

@endsection