<div class="row">

    <div class="col-6">

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    {{ Form::label('license_type_id', 'License Type') }}
                    {{ Form::select("license_type_id", [""=>'-- Select One --'] + $license_types->toArray(), old('license_type_id'), ['class'=>'form-control']) }}
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    {{ Form::label('number', 'License Number') }}
                    {{ Form::text('number', old('number'), array('class' => 'form-control', 'placeholder' => 'License Number')) }}
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    {{ Form::label('valid', 'Valid From') }}
                    {{ Form::date('valid', (request('valid')?request('valid'):(!empty($license)?$license->valid:'')), array('class' => 'form-control', 'placeholder' => '')) }}
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    {{ Form::label('expires', 'Expires') }}
                    {{ Form::date('expires', (request('expires')?request('expires'):(!empty($license)?$license->expires:'')), array('class' => 'form-control', 'placeholder' => '')) }}
                </div>
            </div>
        </div>


        <div class="form-group">
            {{ Form::label('link', 'License Link') }}
            {{ Form::text('link', old('link'), array('class' => 'form-control', 'placeholder' => 'Link')) }}
        </div>


    </div>

    <div class="col-6">

        <div class="form-group">
            {{ Form::label('legal_business_name', 'Legal Business Name') }}
            {{ Form::text('legal_business_name', old('legal_business_name'), array('class' => 'form-control', 'placeholder' => 'Legal Business Name')) }}
        </div>

        <div class="form-group">
            {{ Form::label('premise_address', 'Premise Address') }}
            {{ Form::text('premise_address', old('premise_address'), array('class' => 'form-control', 'placeholder' => 'Premise Address')) }}
        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    {{ Form::label('premise_city', 'Premise City') }}
                    {{ Form::text('premise_city', old('premise_city'), array('class' => 'form-control', 'placeholder' => 'Premise City')) }}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    {{ Form::label('premise_state', 'Premise State') }}
                    {{ Form::text('premise_state', "CA", array('class' => 'form-control', 'placeholder' => 'Premise State', 'disabled'=>'disabled')) }}
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    {{ Form::label('premise_zip', 'Premise Zip') }}
                    {{ Form::text('premise_zip', old('premise_zip'), array('class' => 'form-control', 'placeholder' => 'Premise Zip')) }}
                </div>
            </div>

        </div>

    </div>

</div>
