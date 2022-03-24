@extends('layouts.app')

@section('content')

    <style>
        body {
            color: #000000;
        }
        h1 {font-size: 17px; line-height: 20px;}
        h2 {font-size: 14px; line-height: 13px;}
        h3 {font-size: 12px; line-height: 11px;}

        h2, h3 {margin: 5px; font-weight: bold;}

        .table td, .table th { padding: 0.5rem; }

        .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th
        {
            border-top: 1px solid #000;
        }

        .table-bordered td, .table-bordered th {
            border: 1px solid #000;
        }

        #shipping-manifest {
            text-transform: uppercase;
        }
        .table td.bg-primary,
        .table th.bg-primary {
            background-color: #def6ff !important;
        }

        .table td.bg-secondary,
        .table th.bg-secondary {
            background-color: #ebebeb !important;
        }
        @media print {
            body,html {
                background-color: #fff;
            }
            body, p {
                font-size: 6pt;
            }
            p {
                margin-bottom: .5rem;
            }
            .invoice_num {
                font-size: 10pt;
                margin-top: 30px;
                margin-bottom: 0;
            }
            .hl-logo {
                width: 100px;}
            h4 {
                font-size: 18pt;
            }
            .table td, .table th {
                padding: .3rem;
            }
            .highline-delivered {
                text-transform: uppercase; line-height: 0rem;
                font-size: 7pt;
            }
            .balance {
                font-size: 10pt;
            }



        }

    </style>
    <div class="hidden-print clearfix mb-2">
        <div class="text-right">
            <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>

            {{--<a href="#" class="btn btn-primary waves-effect waves-light">Submit</a>--}}
        </div>
    </div>
    <div class="card-box" id="shipping-manifest">

        <div class="panel-body">

            <h1 style="text-align: center">Sales Invoice / Shipping Manifest</h1>

            <div class="row">
                <div class="col-6">
                    <table class="table table-bordered">

                        <tbody>

                            <tr>
                                <td><strong>Invoice/Manifest Number:</strong></td>
                                <td colspan="3">{{ $saleOrder->ref_number }}</td>
                            </tr>

                            <tr>
                                <td><strong>Attached Page(s)?</strong></td>
                                <td>@if($additional_order_details->count()) Yes @else No @endif</td>
                                <td><strong># of Attached Pages:</strong></td>
                                <td>@if($additional_order_details->count()) 1 @endif</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <div class="col-6">

                    <table class="table table-bordered">

                        <tbody>

                        <tr>
                            <td><strong>Actual Date and Time of departure:</strong></td>
                            <td>{{ \Carbon\Carbon::now()->format('m/d/Y') }}</td>
                            {{--<td>{{ $saleOrder->txn_date->format('m/d/Y') }}</td>--}}
                            <td>_______ AM _______ PM</td>
                        </tr>

                        <tr>
                            <td><strong>Estimated Date and Time of Arrival:</strong></td>
                            <td>{{ \Carbon\Carbon::now()->format('m/d/Y') }}</td>
                            {{--<td>{{ $saleOrder->txn_date->format('m/d/Y') }}</td>--}}
                            <td>_______ AM _______ PM</td>
                        </tr>

                        </tbody>
                    </table>

                </div>

            </div>


            <div class="row">
                <div class="col-6">
                    <table class="table table-bordered">

                        <tbody>

                        <tr>
                            <td colspan="2" class="bg-primary"><h2 style="text-align: center">Shipper Information</h2></td>
                        </tr>
                        <tr>
                            <td>State License #</td>
                            <td>{{ config('highline.license_number_adult') }}</td>
                        </tr>
                        <tr>
                            <td>Type of License</td>
                            <td>Distribution</td>
                        </tr>
                        <tr>
                            <td>Business Name</td>
                            <td>{{ config('highline.license_name') }}</td>
                        </tr>
                        <tr>
                            <td>Business Address</td>
                            <td>{{ config('highline.license.address') }}</td>
                        </tr>
                        <tr>
                            <td>City, State Zip Code</td>
                            <td>{{ config('highline.license.address2') }}</td>
                        </tr>
                        <tr>
                            <td>Phone Number</td>
                            <td>888-402-3607</td>
                        </tr>
                        <tr>
                            <td>Contact Name</td>
                            <td>Nicholas Danias</td>
                        </tr>


                        </tbody>
                    </table>
                </div>

                <div class="col-6">
                    <table class="table table-bordered">

                        <tbody>

                        <tr>
                            <td colspan="2" class="bg-primary"><h2 style="text-align: center">Receiver Information</h2></td>
                        </tr>
                        <tr>
                            <td>State License #</td>
                            <td>

                                @if(stripos($saleOrder->customer_type, 'microbusiness')!==false)
                                    {{ ! empty($saleOrder->customer->details['mb_license_number']) ? $saleOrder->customer->details['mb_license_number'] : '--' }}
                                @elseif(stripos($saleOrder->customer_type, 'retailer')!==false)
                                    {{ ( ! empty($saleOrder->customer->details['rec_license_number']) ? $saleOrder->customer->details['rec_license_number'] : ($saleOrder->customer->details['med_license_number']?$saleOrder->customer->details['med_license_number']:'')) }}
                                @elseif(stripos($saleOrder->customer_type, 'manufacturing')!==false)
                                    @if(!empty($saleOrder->customer->details['mfg_license_number']))
                                        {{ $saleOrder->customer->details['mfg_license_number'] }}
                                    @endif
                                @else
                                    {{ ( ! empty($saleOrder->customer->details['distro_rec_license_number']) ? $saleOrder->customer->details['distro_rec_license_number'] : ($saleOrder->customer->details['distro_med_license_number']?$saleOrder->customer->details['distro_med_license_number']:'')) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Type of License</td>
                            <td>
                                {{ $saleOrder->customer_type }}
                                {{--{{ str_contains($saleOrder->customer_type, 'retail') ? 'Retailer' : studly_case($saleOrder->customer_type) }}--}}
                            </td>
                        </tr>
                        <tr>
                            <td>Business Name</td>
                            <td>{{ ( ! empty($saleOrder->customer->details['business_name']) ? $saleOrder->customer->details['business_name'] : '') }}</td>
                        </tr>
                        <tr>
                            <td>Business Address</td>
                            <td>{{ ( ! empty($saleOrder->customer->details['address']) ? $saleOrder->customer->details['address'] : '') }}</td>
                        </tr>
                        <tr>
                            <td>City, State Zip Code</td>
                            <td>{{ ( ! empty($saleOrder->customer->details['address2']) ? $saleOrder->customer->details['address2'] : '') }}</td>
                        </tr>
                        <tr>
                            <td>Phone Number</td>
                            <td>{{ $saleOrder->customer->present()->phone_number }}</td>
                        </tr>
                        <tr>
                            <td>Contact Name</td>
                            <td>{{ ( ! empty($saleOrder->customer->details['contact_name']) ? $saleOrder->customer->details['contact_name'] : '') }}</td>
                        </tr>


                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">

                <div class="col-12">
                    <table class="table table-bordered">

                        <tbody>

                        <tr>
                            <td class="bg-primary">

                                <h2 class="text-center">Distributor Information</h2>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>


            <div class="row">

                <div class="col-6">

                    <table class="table table-bordered">

                        <tbody>

                        <tr>
                            <td>State License #</td>
                            <td>{{ config('highline.license_number_adult') }}</td>
                        </tr>

                        <td>Business Name</td>
                        <td>{{ config('highline.license_name') }}</td>
                        </tr>
                        <tr>
                            <td>Business Address</td>
                            <td>{{ config('highline.license.address') }}</td>
                        </tr>
                        <tr>
                            <td>City, State Zip Code</td>
                            <td>{{ config('highline.license.address2') }}</td>
                        </tr>
                        <tr>
                            <td>Phone Number</td>
                            <td>888-402-3607</td>
                        </tr>
                        <tr>
                            <td>Contact Name</td>
                            <td>Nicholas Danias</td>
                        </tr>


                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <table class="table table-bordered">

                        <tbody>

                        <tr>
                            <td>Driver's Name</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>CA Driver's License#</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Vehicle Make</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Vehicle Model</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Vehicle Lic. Plate #</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Actual Date and Time of Arrival</td>
                            <td>{{ \Carbon\Carbon::now()->format('m/d/Y') }} _________ AM _________ PM</td>
                            {{--<td>{{ $saleOrder->txn_date->format('m/d/Y') }} _________ AM _________ PM</td>--}}
                        </tr>

                        </tbody>
                    </table>
                </div>

            </div>

            @include('sale_orders._shipped_details', ['order_details' => $initial_order_details])


            @if($additional_order_details->count())
            <div class="additional_page" style="page-break-before: always;">

                <h1 style="text-align: center">Sales Invoice / Shipping Manifest<br>PRODUCT DETAILS ATTACHMENT PAGE</h1>

                <div class="row">
                    <div class="col-6">
                        <table class="table table-bordered">

                            <tbody>

                            <tr>
                                <td><strong>Invoice/Manifest Number<br>Attached To:</strong></td>
                                <td colspan="3">{{ $saleOrder->ref_number }}</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="col-6">

                        <table class="table table-bordered">

                            <tbody>
                            <tr>
                                <td class="font-weight-bold">Attached Page</td>
                                <td>1</td>
                                <td class="font-weight-bold">OF</td>
                                <td>2</td>
                                <td class="font-weight-bold">Total Pages</td>
                            </tr>

                            </tbody>
                        </table>

                    </div>

                </div>



                @include('sale_orders._shipped_details', ['order_details' => $additional_order_details])

            </div>
            @endif
        </div>

    </div>

@endsection
