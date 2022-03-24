<dl class="row">
    <dt class="col-5 text-right">Status:</dt>
    <dd class="col-7"><span class="badge badge-{{ status_class($purchaseOrder->status) }}">{{ ucwords($purchaseOrder->status) }}</span></dd>

    <dt class="col-5 text-right">PO#</dt>
    <dd class="col-7">{{ $purchaseOrder->ref_number }}</dd>

    <dt class="col-5 text-right">Purchase Date:</dt>
    <dd class="col-7">{{ $purchaseOrder->txn_date->format('m/d/Y') }}</dd>

    @if($purchaseOrder->balance)
        <dt class="col-5 text-right">Due Date:</dt>
        <dd class="col-7">{{ $purchaseOrder->due_date->format('m/d/Y') }}</dd>
    @endif

    <dt class="col-5 text-right">Buyer:</dt>
    <dd class="col-7">{{ $purchaseOrder->user->name }}</dd>

    <dt class="col-5 text-right">Destination License:</dt>
    <dd class="col-7">
        {{ (!empty($purchaseOrder->destination_license->legal_business_name)?$purchaseOrder->destination_license->legal_business_name:$purchaseOrder->destination_license->name) }} - {{ $purchaseOrder->destination_license->license_type->name }}<br>
        {{ $purchaseOrder->destination_license->number }}
    </dd>

    <dt class="col-5 text-right">Vendor:</dt>
    <dd class="col-7">
        <a href="{{ route('users.show', $purchaseOrder->vendor) }}">{{ $purchaseOrder->vendor->name }}</a><br>
        {{ $purchaseOrder->vendor->details['address'] }}<br>
        {{ $purchaseOrder->vendor->details['address2'] }}<br>
    </dd>

    <dt class="col-5 text-right">Originating License:</dt>
    <dd class="col-7">

        @if($purchaseOrder->origin_license)

            {{ $purchaseOrder->origin_license->legal_business_name?:$purchaseOrder->originating_entity_model->name }}<br>
            {{ $purchaseOrder->origin_license->premise_address?:$purchaseOrder->originating_entity_model->details['address'] }} {{ $purchaseOrder->origin_license->premise_address2?:'' }}<br>
            {{ $purchaseOrder->origin_license->premise_city ? $purchaseOrder->origin_license->premise_city.", ".$purchaseOrder->origin_license->premise_state." ".$purchaseOrder->origin_license->premise_zip : $purchaseOrder->originating_entity_model->details['address2'] }}<br>

            License # <strong>{{ $purchaseOrder->origin_license->number }}</strong><br>
            License Type: <strong>{{ $purchaseOrder->origin_license->license_type->name }}</strong>

        @else

            <a href="{{ route('users.show', $purchaseOrder->originating_entity_model) }}">{{ $purchaseOrder->originating_entity_model->name }}</a><br>
            {{ $purchaseOrder->originating_entity_model->details['address'] }}<br>
            {{ $purchaseOrder->originating_entity_model->details['address2'] }}<br>
            Lic#
            @if(stristr($purchaseOrder->customer_type, 'manufacturing'))

                {{ $purchaseOrder->originating_entity_model->details['mfg_license_number'] }}

            @elseif(stristr($purchaseOrder->customer_type, 'microbusiness'))

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

            @endif
            <br/>Type: {{ ucwords($purchaseOrder->customer_type) }}

        @endif

    </dd>

    {{--<dt class="col-5 text-right">License Type:</dt>--}}
    {{--<dd class="col-7">{{ ucwords($purchaseOrder->customer_type) }}</dd>--}}

    <dt class="col-5 text-right">Manifest#:</dt>
    <dd class="col-7"><a href="https://ca.metrc.com/reports/transfers/C11-0000347-LIC/manifest?id={{ $purchaseOrder->manifest_no }}" target="_blank">{{ $purchaseOrder->manifest_no }}</a></dd>

    <dt class="col-5 text-right">Fund:</dt>
    <dd class="col-7">{{ $purchaseOrder->fund->name }}</dd>

    <dt class="col-5 text-right">Subtotal:</dt>
    <dd class="col-7">{{ display_currency($purchaseOrder->subtotal) }}</dd>

    @if($purchaseOrder->discount)
        <dt class="col-5 text-right">Cult. Tax Collected:</dt>
        <dd class="col-7 text-danger">({{ display_currency($purchaseOrder->discount) }})</dd>
    @endif

    <dt class="col-5 text-right">Total:</dt>
    <dd class="col-7">{{ display_currency($purchaseOrder->total) }}</dd>

    <dt class="col-5 text-right">Balance:</dt>
    <dd class="col-7">{{ display_currency($purchaseOrder->balance) }}</dd>

</dl>