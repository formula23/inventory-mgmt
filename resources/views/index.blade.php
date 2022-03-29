@extends('layouts.app')

@section('content')

    <div class="content">
        <div class="container">

            @if(Auth::user()->isAdmin())

            <h2>Orders</h2>

            <div class="row">
                <div class="col-lg-6 col-xl-3">
                    <div class="widget-bg-color-icon card-box">
                        <div class="bg-icon bg-icon-success pull-left">
                            <i class="text-success">{{ $todays_orders->order_count }}</i>
                        </div>
                        <div class="text-right">
                            <h3 class="text-dark"><b>{{ display_currency($todays_orders->subtotal) }}</b></h3>
                            <p class="text-muted mb-0">Today's Order Value<br>
                            {{ \Carbon\Carbon::now()->format('m/d/Y') }}</p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-3">
                    <div class="widget-bg-color-icon card-box fadeInDown animated">
                        <div class="bg-icon bg-icon-primary pull-left">
                            <i class="text-info">{{ $weeks_orders->order_count }}</i>
                        </div>
                        <div class="text-right">
                            <h3 class="text-dark"><b>{{ display_currency($weeks_orders->subtotal) }}</b></h3>
                            <p class="text-muted mb-0">This Weeks Order Value<br>
                                {{ \Carbon\Carbon::now()->startOfWeek()->format('m/d/Y') }} - {{ \Carbon\Carbon::now()->endOfWeek()->format('m/d/Y') }}</p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="col-lg-6 col-xl-3">
                    <div class="widget-bg-color-icon card-box">
                        <div class="bg-icon bg-icon-danger pull-left">
                            <i class="text-pink">{{ $months_orders->order_count }}</i>
                        </div>
                        <div class="text-right">
                            <h3 class="text-dark"><b>{{ display_currency($months_orders->subtotal) }}</b></h3>
                            <p class="text-muted mb-0">This Month Order Value<br>
                            {{ \Carbon\Carbon::now()->startOfMonth()->format('m/d/Y') }} - {{ \Carbon\Carbon::now()->endOfMonth()->format('m/d/Y') }}</p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="col-lg-6 col-xl-3">
                    <div class="widget-bg-color-icon card-box">
                        <div class="bg-icon bg-icon-purple pull-left">
                            <i class="text-purple">{{ $quarter_orders->order_count }}</i>
                        </div>
                        <div class="text-right">
                            <h3 class="text-dark"><b>{{ display_currency($quarter_orders->subtotal) }}</b></h3>
                            <p class="text-muted mb-0">Q{{ \Carbon\Carbon::now()->quarter }} Order Value<br>
                            {{ \Carbon\Carbon::now()->startOfQuarter()->format('m/d/Y') }} - {{ \Carbon\Carbon::now()->endOfQuarter()->format('m/d/Y') }}</p>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            @endif


                <div class="row">

                    @can('dashboard.allcustomers')

                    <div class="col-lg-6">
                        <div class="card-box">
                            <h4 class="text-dark  header-title m-t-0">All Customers ({{ $customers->count() }})</h4>

                            <p class="text-muted m-b-25 font-13">List of customers with number of days since last order.</p>

                            <ul class="nav nav-tabs">

                                @foreach($customers_by_days as $segment=>$segment_data)
                                    <li class="nav-item">
                                        <a href="#panel-{{ clean_string($segment) }}" data-toggle="tab" aria-expanded="true" class="nav-link {{ ($loop->iteration==1?'active':'') }}">
                                            {{ $segment_data['label'] }} <span class="badge badge-{{ badge_color($segment) }}">{{ count($segment_data['customers']) }}</span>
                                        </a>
                                    </li>
                                @endforeach

                            </ul>
                            <div class="tab-content">

                                @foreach($customers_by_days as $segment=>$segment_data)
                                    <div class="tab-pane fade {{ ($loop->iteration==1?'active show':'') }}" id="panel-{{ clean_string($segment) }}" aria-expanded="true">

                                        <div class="table-responsive" style="height: 350px;overflow-y: auto;">
                                            <table class="table mb-0">
                                                <thead>
                                                <tr>
                                                    <th>Days</th>
                                                    <th>Name</th>
                                                    <th>Orders</th>
                                                    <th>Order Value</th>
                                                </tr>
                                                </thead>
                                                <tbody style="height: 300px; overflow-y: auto;">
                                                @foreach($segment_data['customers'] as $customer)
                                                    <tr>
                                                    <td><span class="badge badge-{{ badge_color($customer->days_last_order) }}">{{ $customer->days_last_order }}</span></td>
                                                    <td><a href="{{ route('users.show', $customer->id) }}">{{ $customer->name }}</a></td>
{{--                                                    <td>{{ $customer->first_order->format(config('highline.date_format')) }}</td>--}}
{{--                                                    <td>{{ $customer->last_order->format(config('highline.date_format')) }}</td>--}}
                                                    <td>{{ $customer->number_of_orders }}</td>
                                                    <td>{{ display_currency($customer->total_order_value) }}</td>
                                                    </tr>
                                                @endforeach

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                @endforeach

                            </div>


                            {{--<div class="table-responsive" style="height: 350px;overflow-y: auto;">--}}
                            {{--<table class="table mb-0">--}}
                                {{--<thead>--}}
                                {{--<tr>--}}
                                    {{--<th># Days</th>--}}
                                    {{--<th>Name</th>--}}
                                    {{--<th>First Order</th>--}}
                                    {{--<th>Last Order</th>--}}
                                    {{--<th>Orders</th>--}}
                                    {{--<th>Order Value</th>--}}
                                {{--</tr>--}}
                                {{--</thead>--}}
                                {{--<tbody style="height: 300px; overflow-y: auto;">--}}

                                {{--@foreach($customers as $customer)--}}
                                    {{--<tr>--}}
                                        {{--<td><span class="badge badge-{{ badge_color($customer->days_last_order) }}">{{ $customer->days_last_order }}</span></td>--}}
                                        {{--<td><a href="{{ route('users.show', $customer->id) }}">{{ $customer->name }}</a></td>--}}
                                        {{--<td>{{ $customer->first_order->format(config('highline.date_format')) }}</td>--}}
                                        {{--<td>{{ $customer->last_order->format(config('highline.date_format')) }}</td>--}}
                                        {{--<td>{{ $customer->number_of_orders }}</td>--}}
                                        {{--<td>{{ display_currency($customer->total_order_value) }}</td>--}}
                                    {{--</tr>--}}
                                {{--@endforeach--}}

                                {{--</tbody>--}}
                            {{--</table>--}}
                            {{--</div>--}}
                        </div>
                    </div>
                    <!-- end col -8 -->

                    @endcan

                    @can('dashboard.newcustomers')
                    <div class="col-md-6">
                        <div class="card-box">

                            <h4 class="text-dark  header-title m-t-0">New Customers</h4>
                            <p class="text-muted m-b-25 font-13">New customers by month.</p>

                            <ul class="nav nav-tabs">

                                @foreach($new_customers as $month=>$months)
                                <li class="nav-item">
                                    <a href="#{{ $month }}" data-toggle="tab" aria-expanded="true" class="nav-link {{ ($loop->iteration==1?'active':'') }}">
                                        {{ $month }} ({{ $months->count() }})
                                    </a>
                                </li>
                                @endforeach

                            </ul>
                            <div class="tab-content">

                                @foreach($new_customers as $month=>$months)
                                <div class="tab-pane fade {{ ($loop->iteration==1?'active show':'') }}" id="{{ $month }}" aria-expanded="true">

                                    <div class="table-responsive" style="height: 350px;overflow-y: auto;">
                                        <table class="table mb-0">
                                            <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>First Order</th>
                                                <th>Sales Rep</th>
                                                <th>Orders</th>
                                                <th>Order Value</th>
                                            </tr>
                                            </thead>
                                            <tbody style="height: 300px; overflow-y: auto;">
                                            @foreach($months as $customer)
                                                <tr>
                                                    <td><a href="{{ route('users.show', $customer->id) }}">{{ $customer->name }}</a></td>
                                                    <td>{{ $customer->first_order->format(config('highline.date_format')) }}</td>
                                                    <td>{{ $customer->sales_rep_name }}</td>
                                                    <td>{{ $customer->number_of_orders }}</td>
                                                    <td>{{ display_currency($customer->total_order_value) }}</td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                @endforeach

                            </div>
                        </div>
                    </div>

                    @endcan

                </div>

                {{--@if(Auth::user()->isAdmin())--}}


                {{--<h2>Taxes</h2>--}}

                {{--<div class="row">--}}
                    {{--<div class="col-xl-6">--}}
                        {{--<h4 class="text-dark header-title m-t-0">Cultivation Tax</h4>--}}
                        {{--<div class="widget-bg-color-icon card-box">--}}

                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-xl-6">--}}
                        {{--<h4 class="text-dark header-title m-t-0">Excise Tax Liability</h4>--}}

                            {{--<div class="row">--}}

                                {{--@foreach($excise_tax_by_quarter as $quarter => $excise_tax)--}}

                                    {{--<div class="col-lg-6">--}}
                                    {{--<div class="widget-bg-color-icon card-box">--}}

                                    {{--<div class="bg-icon bg-icon-success pull-left">--}}
                                        {{--<i class="text-success">{{ $excise_tax->first()->order_count }}</i>--}}
                                    {{--</div>--}}
                                    {{--<div class="text-right">--}}
                                        {{--<h5 class="text-dark header-title m-t-0">Q{{ $quarter }} {{ $excise_tax->first()->Year }} Excise Tax</h5>--}}

                                        {{--<h3 class="text-dark"><b>{{ display_currency($excise_tax->first()->excise_tax) }}</b></h3>--}}
                                        {{--<p class="text-muted mb-0">--}}
                                            {{--<span class="badge badge-{{ ($quarter==2 ? 'success' : 'danger') }}">{{ ($quarter==2 ? 'Paid' : 'Pending') }}</span>--}}
                                        {{--</p>--}}
                                    {{--</div>--}}
                                    {{--<div class="clearfix"></div>--}}

                                    {{--</div>--}}
                                    {{--</div>--}}
                                {{--@endforeach--}}



                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

                {{--@endif--}}

            {{--<div class="row">--}}
                {{--<div class="col-lg-7">--}}
                    {{--<div class="card-box">--}}
                        {{--<h4 class="text-dark  header-title m-t-0 m-b-30">Total Revenue</h4>--}}

                        {{--<div class="widget-chart text-center">--}}
                            {{--<div id="dashboard-chart-1" style="height: 300px;"></div>--}}

                        {{--</div>--}}
                    {{--</div>--}}

                {{--</div>--}}

                {{--<div class="col-lg-5">--}}
                    {{--<div class="card-box">--}}
                        {{--<h4 class="text-dark  header-title m-t-0 m-b-30">Yearly Sales Report</h4>--}}

                        {{--<div class="widget-chart text-center">--}}
                            {{--<div id="morris-donut-example" style="height: 300px;"></div>--}}

                        {{--</div>--}}
                    {{--</div>--}}

                {{--</div>--}}

            {{--</div>--}}
            <!-- end row -->



            {{--<div class="row">--}}
                {{--<div class="col-lg-8">--}}
                    {{--<div class="card-box">--}}
                        {{--<h4 class="text-dark  header-title m-t-0">Latest Projects</h4>--}}
                        {{--<p class="text-muted m-b-25 font-13">--}}
                            {{--Your awesome text goes here.--}}
                        {{--</p>--}}

                        {{--<table class="table mb-0">--}}
                            {{--<thead>--}}
                            {{--<tr>--}}
                                {{--<th>#</th>--}}
                                {{--<th>Project Name</th>--}}
                                {{--<th>Start Date</th>--}}
                                {{--<th>Due Date</th>--}}
                                {{--<th>Status</th>--}}
                                {{--<th>Assign</th>--}}
                            {{--</tr>--}}
                            {{--</thead>--}}
                            {{--<tbody>--}}
                            {{--<tr>--}}
                                {{--<td>1</td>--}}
                                {{--<td>Minton Admin v1</td>--}}
                                {{--<td>01/01/2017</td>--}}
                                {{--<td>26/04/2017</td>--}}
                                {{--<td><span class="badge badge-info">Released</span></td>--}}
                                {{--<td>Coderthemes</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>2</td>--}}
                                {{--<td>Minton Frontend v1</td>--}}
                                {{--<td>01/01/2017</td>--}}
                                {{--<td>26/04/2017</td>--}}
                                {{--<td><span class="badge badge-success">Released</span></td>--}}
                                {{--<td>Minton admin</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>3</td>--}}
                                {{--<td>Minton Admin v1.1</td>--}}
                                {{--<td>01/05/2017</td>--}}
                                {{--<td>10/05/2017</td>--}}
                                {{--<td><span class="badge badge-pink">Pending</span></td>--}}
                                {{--<td>Coderthemes</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>4</td>--}}
                                {{--<td>Minton Frontend v1.1</td>--}}
                                {{--<td>01/01/2017</td>--}}
                                {{--<td>31/05/2017</td>--}}
                                {{--<td><span class="badge badge-purple">Work in Progress</span>--}}
                                {{--</td>--}}
                                {{--<td>Minton admin</td>--}}
                            {{--</tr>--}}
                            {{--<tr>--}}
                                {{--<td>5</td>--}}
                                {{--<td>Minton Admin v1.3</td>--}}
                                {{--<td>01/01/2017</td>--}}
                                {{--<td>31/05/2017</td>--}}
                                {{--<td><span class="badge badge-warning">Coming soon</span></td>--}}
                                {{--<td>Coderthemes</td>--}}
                            {{--</tr>--}}

                            {{--</tbody>--}}
                        {{--</table>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<!-- end col -8 -->--}}

                {{--<div class="col-lg-4">--}}
                    {{--<div class="card-box widget-user">--}}
                        {{--<div>--}}
                            {{--<img src="/images/users/avatar-1.jpg" class="img-responsive rounded-circle" alt="user">--}}
                            {{--<div class="wid-u-info">--}}
                                {{--<h5 class="mt-0 m-b-5 font-16">Chadengle</h5>--}}
                                {{--<p class="text-muted m-b-5 font-13">coderthemes@gmail.com</p>--}}
                                {{--<small class="text-warning"><b>Admin</b></small>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="card-box widget-user">--}}
                        {{--<div>--}}
                            {{--<img src="/images/users/avatar-2.jpg" class="img-responsive rounded-circle" alt="user">--}}
                            {{--<div class="wid-u-info">--}}
                                {{--<h5 class="mt-0 m-b-5 font-16">Tomaslau</h5>--}}
                                {{--<p class="text-muted m-b-5 font-13">coderthemes@gmail.com</p>--}}
                                {{--<small class="text-success"><b>User</b></small>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="card-box widget-user">--}}
                        {{--<div>--}}
                            {{--<img src="/images/users/avatar-7.jpg" class="img-responsive rounded-circle" alt="user">--}}
                            {{--<div class="wid-u-info">--}}
                                {{--<h5 class="mt-0 m-b-5 font-16">Ok</h5>--}}
                                {{--<p class="text-muted m-b-5 font-13">coderthemes@gmail.com</p>--}}
                                {{--<small class="text-pink"><b>Admin</b></small>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                {{--</div>--}}

            {{--</div>--}}
            <!-- end row -->

        </div>
        {{--<a href="javascript:void(0);" id="printButton" class="btn">Print Barcode</a>--}}
        <!-- end container -->
    </div>

@endsection

@section('js')

    <script src="{{ asset('plugins/waypoints/lib/jquery.waypoints.js') }}"></script>
    <script src="{{ asset('plugins/counterup/jquery.counterup.min.js') }}"></script>

    <!--Morris Chart-->
    <script src="{{ asset('plugins/morris/morris.min.js') }}"></script>
    <script src="{{ asset('plugins/raphael/raphael-min.js') }}"></script>

    <!-- Page js  -->
    <script src="{{ asset('/js/pages/jquery.dashboard.js') }}"></script>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.counter').counterUp({
                delay: 100,
                time: 1200
            });
        });
    </script>



    <script src="http://labelwriter.com/software/dls/sdk/js/DYMO.Label.Framework.latest.js" type="text/javascript" charset="UTF-8"> </script>


    <script type="text/javascript" charset="UTF-8">

        //----------------------------------------------------------------------------
        //
        //  $Id: PrintLabel.js 38773 2015-09-17 11:45:41Z nmikalko $
        //
        // Project -------------------------------------------------------------------
        //
        //  DYMO Label Framework
        //
        // Content -------------------------------------------------------------------
        //
        //  DYMO Label Framework JavaScript Library Samples: Print label
        //
        //----------------------------------------------------------------------------
        //
        //  Copyright (c), 2010, Sanford, L.P. All Rights Reserved.
        //
        //----------------------------------------------------------------------------


        (function()
        {
            // called when the document completly loaded
            function onload()
            {
                var textTextArea = document.getElementById('textTextArea');
                var printButton = document.getElementById('printButton');

                // prints the label
                printButton.onclick = function()
                {
                    try
                    {
                        // open label
                        var labelXml = '<?xml version="1.0" encoding="utf-8"?>\
    <DieCutLabel Version="8.0" Units="twips">\
        <PaperOrientation>Landscape</PaperOrientation>\
        <Id>Address</Id>\
        <PaperName>30252 Address</PaperName>\
        <DrawCommands/>\
        <ObjectInfo>\
        <BarcodeObject>\
            <Name>BARCODE</Name>\
            <ForeColor Alpha="255" Red="0" Green="0" Blue="0" />\
            <BackColor Alpha="100" Red="255" Green="255" Blue="255" />\
            <LinkedObjectName></LinkedObjectName>\
            <Rotation>Rotation0</Rotation>\
            <IsMirrored>False</IsMirrored>\
            <IsVariable>True</IsVariable>\
            <Text></Text>\
            <Type>Code39</Type>\
            <Size>Medium</Size>\
            <TextPosition>Bottom</TextPosition>\
            <TextFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
            <CheckSumFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />\
            <TextEmbedding>None</TextEmbedding>\
            <ECLevel>0</ECLevel>\
            <HorizontalAlignment>Center</HorizontalAlignment>\
            <QuietZonesPadding Left="0" Top="0" Right="0" Bottom="0" />\
        </BarcodeObject>\
        <Bounds X="331" Y="178" Width="5260" Height="420" />\
        </ObjectInfo>\
    </DieCutLabel>';
                        var label = dymo.label.framework.openLabelXml(labelXml);

                        // set label text
                        // label.setObjectText("Text", textTextArea.value);

                        label.setObjectText("BARCODE", '1A4060300004398000000368');

                        // select printer to print on
                        // for simplicity sake just use the first LabelWriter printer
                        var printers = dymo.label.framework.getPrinters();
                        if (printers.length == 0)
                            throw "No DYMO printers are installed. Install DYMO printers.";

                        var printerName = "";
                        for (var i = 0; i < printers.length; ++i)
                        {
                            var printer = printers[i];
                            if (printer.printerType == "LabelWriterPrinter")
                            {
                                printerName = printer.name;
                                break;
                            }
                        }

                        if (printerName == "")
                            throw "No LabelWriter printers found. Install LabelWriter printer";

                        // finally print the label
                        label.print(printerName);
                    }
                    catch(e)
                    {
                        alert(e.message || e);
                    }
                }
            };

            function initTests()
            {
                if(dymo.label.framework.init)
                {
                    //dymo.label.framework.trace = true;
                    dymo.label.framework.init(onload);
                } else {
                    onload();
                }
            }

            // register onload event
            if (window.addEventListener)
                window.addEventListener("load", initTests, false);
            else if (window.attachEvent)
                window.attachEvent("onload", initTests);
            else
                window.onload = initTests;

        } ());

    </script>

@stop

