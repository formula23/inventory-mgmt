<div class="row">

    <div class="col-12">


        <table class="table table-bordered">

            <tbody>

            <tr>
                <td colspan="8"class="bg-primary">
                    <h3 class="text-center">Product Shipped Details</h3>
                    <p class="text-center" style="margin: 0; font-weight: bold;">SHIPPER COMPLETES ALL THE UNSHADED COLUMNS BELOW. RECEIVER COMPLETES ONLY THE SHADED COLUMNS BELOW<br>
                        <small>(Please attach additional pages, if needed)</small></p>
                </td>
            </tr>

            <tr class="font-weight-bold">
                <td rowspan="2">UID Tag Number<br><small>(If Applicable)</small></td>
                <td rowspan="2">ITEM NAME <u>AND</u> PRODUCT DESCRIPTION<br><small>(INCLUDE WEIGHT OR COUNT)</small></td>
                <td rowspan="2">Qty<br>Ord'd<br><small>(Weight or Count)</small></td>
                <td rowspan="2" class="bg-secondary">Qty<br />Rec'd<br><small>(Weight or Count)</small></td>
                <td rowspan="2">Unit<br />Cost</td>
                <td rowspan="2">Total<br />Cost</td>

                <td colspan="2" class="bg-secondary">RETAIL ONLY</td>
            </tr>
            <tr class="font-weight-bold">
                <td class="bg-secondary">Unit<br />Retail Value</td>
                <td class="bg-secondary">Total<br />Retail Value</td>

            </tr>

            @foreach($order_details as $order_detail)
                @if(empty($order_detail->batch)) @continue; @endif
                @if($order_detail->batch->in_metrc) @continue; @endif

            <tr>
                <td>{{ ($order_detail->batch->batch_number ? : $order_detail->batch->ref_number) }}</td>
                <td>{{ ($order_detail->batch->brand ? $order_detail->batch->brand->name." - " : '' ) }}{{ $order_detail->sold_as_name }}</td>
                <td>{{ $order_detail->units }} <small>{{ $order_detail->batch->uom }}</small></td>
                <td class="bg-secondary">
                    {{--@if($order_detail->units_accepted)--}}
                    {{--{{ $order_detail->units_accepted }} <small>{{ $order_detail->batch->uom }}</small>--}}
                    {{--@endif--}}
                </td>
                <td>{{ display_currency($order_detail->unit_sale_price) }}</td>
                <td>{{ display_currency($order_detail->units * $order_detail->unit_sale_price) }}</td>
                <td class="bg-secondary"></td>
                <td class="bg-secondary"></td>
            </tr>

            @endforeach

            </tbody>
        </table>


    </div>


</div>

<div class="row">

    <div class="col-12">


        <table class="table table-bordered">

            <tbody>
            <tr>
                <td colspan="4" class="text-center bg-primary"><h3>Product Rejection</h3></td>
            </tr>
            <tr>
                <td colspan="4" class="text-center font-weight-bold">IF PRODUCT(S) ARE REJECTED, PLEASE CIRCLE THE ITEMS BEING REJECTED IN THE PRODUCT SHIPPED DETAILS SECTION ABOVE</td>
            </tr>
            <tr>
                <td><br>REASON FOR REJECTION:</td>
                <td colspan="3"><div style="width: 500px; height: 40px"></div> </td>
            </tr>

            <tr>
                <td colspan="4" class="text-center bg-primary"><h3>PRODUCT RECEIPT CONFIRMATION</h3></td>
            </tr>
            <tr>
                <td colspan="4">
                    <p class="font-weight-bold">I CONFIRM THAT THE CONTENTS OF THIS SHIPMENT MATCH IN WEIGHT AND COUNT AS INDICATED ABOVE.<br>
                            I AGREE TO TAKE CUSTODY OF ALL ITEMS AS INDICATED RECEIVED ABOVE – AND WHICH ARE NOT CIRCLED.<br>
                            THE PRODUCTS CIRCLED ABOVE ARE REJECTED FOR DELIVERY AND REMAIN IN THE CUSTODY OF THE DISTRIBUTOR FOR RETURN TO THE SHIPPER AS INDICATED ON THIS FORM AND ALL ATTACHED PRODUCT DETAILS SHEET(S).</p>
                </td>
            </tr>
            <tr>
                <td class="bg-secondary">NAME OF PERSON RECEIVING<br> AND/OR REJECTING PRODUCT:</td>
                <td><div style="width: 200px"></div> </td>
                <td class="bg-secondary">PHONE NUMBER:</td>
                <td><div style="width: 80px"></div></td>
            </tr>
            <tr>
                <td class="bg-secondary">SIGNATURE OF PERSON RECEIVING<br> AND/OR REJECTING PRODUCT:</td>
                <td></td>
                <td class="bg-secondary">Date Signed:</td>
                <td></td>
            </tr>
            </tbody>
        </table>

    </div>

</div>