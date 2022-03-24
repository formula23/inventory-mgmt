<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $purchaseOrder->vendor->name.' - '.$purchaseOrder->ref_number }}</title>

    <style type="text/css">
        @page {
            margin: 10px;
        }
        body {
            margin: 10px;
            font-size: 12px;
        }
        * {
            font-family: Verdana, Arial, sans-serif;
        }
        a {
            color: #fff;
            text-decoration: none;
        }
        table {
            font-size: x-small;
        }
        th {
            text-align: left;
        }

        .payments th {
            padding: 4px;
            background: #eee;
        }

        .order-items td,
        .payments  td {
            border-bottom: 1px solid #EEEEEE;
        }

        td {
            padding: 3px;

        }
        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }
        .invoice table {
            margin: 15px;
        }
        .invoice h3 {
            margin-left: 15px;
        }
        .spacer {
            width: 65%;
        }
        .label {
            text-align: right;
        }
        .balance {
            font-size: 16px;
        }
        .information {
            /*background-color: ;*/
            color: #000;
            padding: 0 15px;
        }
        .information .logo {
            margin: 5px;
        }
        .information table {
            /*padding: 10px;*/
        }

        .subtotal_table td.label {
            width: 70%;
        }
        .subtotal_table td {
            width: 30%;
        }
    </style>

</head>
<body>

@if($purchaseOrder->balance == 0)
    <div class="paid-stamp">
        <img src="{{ public_path() }}/images/paid-stamp.png" width="140px" style="position: absolute; left: 50%; top: 240px; margin-left: -70px">
    </div>
@endif

<div class="information">

    <table width="100%">
        <tr>
            <td align="left" style="width: 50%; padding-top: 0px;">
                {{--<img src="{{ public_path() }}/images/highline-200.png" width="160px" />--}}
                {{--<img src="/images/highline-200.png" width="160px" />--}}
                <h3 style="font-size: 18px">Purchase Order# {{ $purchaseOrder->ref_number }}</h3>
            </td>
            <td align="right" style="width: 50%; padding-top: 0px;">
                <img src="{{ public_path() }}/images/highline-200.png" width="160px" />
                {{--<p>--}}
                    {{--<strong>{{ config('highline.license_name') }}</strong><br>--}}
                    {{--{{ config('highline.license.address') }}<br>--}}
                    {{--{{ config('highline.license.address2') }}<br>--}}
                    {{--License# {{ config('highline.license.adult') }}<br>--}}
                    {{--License Type: Distributor--}}
                {{--</p>--}}
            </td>
        </tr>
    </table>

</div>

<div class="information">

    <table width="100%">
        <tr>

            <td align="left" style="width: 35%; padding-top: 0px; vertical-align: top;">
                <strong>Vendor:</strong>
                <address>
                    {{ $purchaseOrder->vendor->name }}
                    @if(!empty($purchaseOrder->vendor->details['business_name']) && $purchaseOrder->vendor->name != $purchaseOrder->vendor->details['business_name'])
                        <br>{{ ($purchaseOrder->vendor->details['business_name']) }}
                    @endif
                    <br>{{ $purchaseOrder->vendor->details['address'] }}
                    <br>{{ $purchaseOrder->vendor->details['address2'] }}
                </address>
            </td>

            <td align="left" style="width: 35%; padding-top: 0px; vertical-align: top;">

                <strong>Originating License:</strong>
                <address>

                    @if($purchaseOrder->origin_license)

                        {{ $purchaseOrder->origin_license->legal_business_name?:$purchaseOrder->originating_entity_model->name }}<br>
                        {{ $purchaseOrder->origin_license->premise_address?:$purchaseOrder->originating_entity_model->details['address'] }} {{ $purchaseOrder->origin_license->premise_address2?:'' }}<br>
                        {{ $purchaseOrder->origin_license->premise_city ? $purchaseOrder->origin_license->premise_city.", ".$purchaseOrder->origin_license->premise_state." ".$purchaseOrder->origin_license->premise_zip : $purchaseOrder->originating_entity_model->details['address2'] }}<br>

                        <p>
                            License# <strong>{{ $purchaseOrder->origin_license->number }}</strong><br>
                            License Type: <strong>{{ $purchaseOrder->origin_license->license_type->name }}</strong>
                        </p>
                    @else

                        {{ $purchaseOrder->originating_entity_model->name }}
                        @if(!empty($purchaseOrder->originating_entity_model->details['business_name']) && $purchaseOrder->originating_entity_model->name != $purchaseOrder->originating_entity_model->details['business_name'])
                            <br>{{ ($purchaseOrder->originating_entity_model->details['business_name']) }}
                        @endif
                        <br>{{ $purchaseOrder->originating_entity_model->details['address'] }}
                        <br>{{ $purchaseOrder->originating_entity_model->details['address2'] }}

                        <p>License# <strong>
                                @if(stristr($purchaseOrder->customer_type, 'microbusiness'))

                                    {{ $purchaseOrder->originating_entity_model->details['mb_license_number'] }}

                                @elseif(stristr($purchaseOrder->customer_type, 'cultivator'))

                                    @if( ! empty($purchaseOrder->originating_entity_model->details['cult_rec_license_number']))
                                        {{ $purchaseOrder->originating_entity_model->details['cult_rec_license_number'] }}
                                    @elseif(!empty($purchaseOrder->originating_entity_model->details['cult_med_license_number']))
                                        {{ $purchaseOrder->originating_entity_model->details['cult_med_license_number'] }}
                                    @endif

                                @else

                                    @if( ! empty($purchaseOrder->originating_entity_model->details['distro_rec_license_number']))
                                        {{ $purchaseOrder->originating_entity_model->details['distro_rec_license_number'] }}
                                    @elseif(!empty($purchaseOrder->originating_entity_model->details['distro_med_license_number']))
                                        {{ $purchaseOrder->originating_entity_model->details['distro_med_license_number'] }}
                                    @endif

                                @endif</strong><br>
                            License Type: <strong>{{ ucfirst($purchaseOrder->customer_type) }}</strong>
                        </p>

                    @endif

                </address>
            </td>



            <td align="right" style="width: 30%; padding-top: 0px; vertical-align: top;">
                <strong>Destination License:</strong>
                <address>
                    {{ config('highline.license.legal_name') }}<br>
                    {{ config('highline.license.address') }}<br>
                        {{ config('highline.license.address2') }}<br>
                <p>License# <strong>{{ $purchaseOrder->destination_license->number }}</strong><br>
                    License Type: <strong>{{ ucfirst($purchaseOrder->destination_license->license_type->name) }}</strong>
                </p>
                </address>
            </td>
            {{--<td align="right" style="width: 10%; padding-top: 0px; vertical-align: top;">--}}
                    {{--<strong>{{ config('highline.license_name') }}</strong>--}}
                {{--<address>--}}
                    {{--{{ config('highline.license.address') }}<br>--}}
                    {{--{{ config('highline.license.address2') }}<br>--}}
                {{--</address>--}}
            {{--</td>--}}
        </tr>

    </table>

</div>
<hr>
<div class="information">

    <table width="100%">
        <tr>
            <td align="left" style="width: 75%; padding-top: 0px;">
                <p><strong>Metrc Manifest#: </strong> {{ $purchaseOrder->manifest_no }}</p>
                <p><strong>Status: </strong> <span class="badge badge-{{ ( ($purchaseOrder->balance > 0) ? 'success' : 'danger' ) }}">{{ ( ($purchaseOrder->balance > 0) ? 'Open' : 'Paid' ) }}</span></p>
                <p><strong>Date: </strong> {{ $purchaseOrder->txn_date->format('M d, Y') }}</p>

            </td>
            <td align="left" style="width: 25%; padding-top: 0px;">
                <p>
                    <strong>Terms:</strong>
                    @if( ! is_null($purchaseOrder->terms))
                        {{ config('highline.payment_terms')[$purchaseOrder->terms] }}
                    @else
                        {{ (!empty($purchaseOrder->vendor->details['terms']) ? config('highline.payment_terms')[$purchaseOrder->vendor->details['terms']] : 'Due on Receipt' ) }}
                    @endif
                </p>
                <p>
                    <strong>Due Date: </strong>
                    @if($purchaseOrder->due_date)
                        {{ $purchaseOrder->due_date->format(config('highline.date_format')) }}
                    @else
                        {{ $purchaseOrder->txn_date->addDays((!empty($purchaseOrder->vendor->details['terms']) ? $purchaseOrder->vendor->details['terms'] : 0 ))->format('m/d/Y') }}
                    @endif
                </p>
                <p style="background: #eee; padding: 5px;" class="balance">Balance: {{ display_currency($purchaseOrder->balance) }}</p>
            </td>
        </tr>
    </table>
</div>

<div class="invoice">

        <table width="100%" class="order-items" style="border: 1px">
            <thead>
            <tr>
                <th>UID</th>
                <th>Name</th>
                {{--<th>Batch#</th>--}}
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
                <th>Cult Tax Collected</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($purchaseOrder->batches as $batch)
                <tr>

                    <td>{{ $batch->ref_number }}</td>
                    <td>
                        @if($batch->brand) <strong>{{ $batch->brand->name }}</strong><br> @endif
                        {{ $batch->category->name }}: {{ $batch->name }}
                    </td>
                    {{--<td>{{ $batch->batch_number?:"N/A" }}</td>--}}
                    <td style="white-space: nowrap;">
                        {{ $batch->units_purchased }} {{ $batch->uom }}
                    </td>
                    <td>
                        {{ display_currency($batch->unit_price) }}
                    </td>
                    <td>{{ display_currency($batch->subtotal_price) }}</td>
                    <td>({{ display_currency($batch->tax) }})</td>
                    <td>{{ display_currency($batch->subtotal_price - $batch->tax) }}</td>
                </tr>
            @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td></td>
                    {{--<td></td>--}}
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ display_currency($purchaseOrder->batches->sum('subtotal_price')) }}</td>
                    <td>({{ display_currency($purchaseOrder->batches->sum('tax')) }})</td>
                    <td>{{ display_currency($purchaseOrder->batches->sum('subtotal_price') - $purchaseOrder->batches->sum('tax')) }}</td>
                </tr>
            </tfoot>
        </table>


</div>

<div class="information" style="">
    <table width="100%">

        <tr>
            <td align="left" style="width: 50%;">
                @if($purchaseOrder->tax > 0)
                    <p><strong><i>*** The buyer {{ config('highline.license_name') }} is responsible for remitting the cultivation tax on behalf of the vendor ({{ $purchaseOrder->vendor->business_name?:$purchaseOrder->vendor->name }}). ***</i></strong></p>
                @else
                    <p><strong><i>*** The vendor ({{ $purchaseOrder->vendor->business_name?:$purchaseOrder->vendor->name }}) is responsible for remitting the cultivation tax on behalf of the originating cultivator. ***</i></strong></p>
                @endif
            </td>
            <td align="right" style="width: 50%;">
                <table width="100%" style="" align="right" class="subtotal_table">
                    <tr>

                        <td class="label">Subtotal:</td>
                        <td>{{ display_currency($purchaseOrder->subtotal) }}</td>
                    </tr>

                    @if($purchaseOrder->discount)
                        <tr>

                            <td class="label">Cultivation Tax:</td>
                            <td>({{ display_currency($purchaseOrder->discount) }})</td>
                        </tr>

                        {{--<tr>--}}

                            {{--<td class="label">Subtotal Less Tax:</td>--}}
                            {{--<td>{{ display_currency($purchaseOrder->subtotal - $purchaseOrder->discount) }}</td>--}}
                        {{--</tr>--}}
                    @endif

                    <tr>
                        <td class="label">Total:</td>
                        <td>{{ display_currency($purchaseOrder->total) }}</td>
                    </tr>

                </table>
            </td>
        </tr>


    </table>
</div>

@if($purchaseOrder->transactions->count())

    <div class="information" style="">
        <h4>Payments</h4>
        <table width="100%" class="payments">

            <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Ref#</th>
                <th>Memo</th>
            </tr>
            </thead>


            <tbody>
            @foreach($purchaseOrder->transactions as $transaction)
                <tr>
                    <td>{{ $transaction->txn_date() }}</td>
                    <td>{{ display_currency($transaction->amount) }}</td>
                    <td>{{ $transaction->payment_method }}</td>
                    <td>{{ $transaction->ref_number }}</td>
                    <td>{{ $transaction->memo }}</td>
                </tr>
            @endforeach

            </tbody>

            <tfoot>
            <tr>
                <td>Total</td>
                <td>{{ display_currency($purchaseOrder->transactions->sum('amount')) }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            </tfoot>

        </table>
    </div>

@endif

</body>
</html>