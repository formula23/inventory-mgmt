
    @isset($redirect_to)
    {{ Form::hidden('redirect_to', $redirect_to) }}
    @endisset

    <div class="row">

        <div class="col-md-4">

            <div class="form-group">
                {{ Form::label('name', 'Full Name') }}
                {{ Form::text('name', old('name'), array('class' => 'form-control', 'placeholder' => 'Business Name')) }}
            </div>

            <div class="form-group">
                {{ Form::label('contact_name', 'Contact Name') }}
                {{ Form::text('details[contact_name]', old('contact_name'), array('class' => 'form-control', 'placeholder' => 'Contact Name')) }}
            </div>

            <div class="form-group">
                {{ Form::label('business_name', 'Legal Business Name') }}
                {{ Form::text('details[business_name]', old('business_name'), array('class' => 'form-control', 'placeholder' => 'Legal Business Name')) }}
            </div>

            <div class="form-group">
                {{ Form::label('email', 'E-mail') }}
                {{ Form::text('email', old('email'), array('class' => 'form-control', 'placeholder' => 'E-mail')) }}
            </div>

            <div class="form-group">
                {{ Form::label('phone', 'Phone') }}
                {{ Form::text('phone', old('phone'), array('class' => 'form-control', 'placeholder' => 'Phone')) }}
            </div>



        </div>

        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('address', 'Street Address') }}
                {{ Form::text('details[address]', old('address'), array('class' => 'form-control', 'placeholder' => 'Address')) }}
            </div>
            <div class="form-group">
                {{ Form::label('address2', 'City, State Zip') }}
                {{ Form::text('details[address2]', old('address2'), array('class' => 'form-control', 'placeholder' => 'City, State Zip')) }}
            </div>

            @empty($active_role)

                <div class="form-group">
                    {{ Form::label('password', 'Password') }}
                    {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Min. 6 characters')) }}
                </div>

                <div class="form-group">
                    {{ Form::label('password_confirmation', 'Confirm Password') }}
                    {{ Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => 'Min. 6 characters')) }}
                </div>

            @endempty

            <div class="form-group">
                {{ Form::label('pin', 'PIN') }}
                {{ Form::password('pin', array('class' => 'form-control', 'placeholder' => 'PIN')) }}
            </div>

            {{--<h2 class="header-title">License Type</h2>--}}
            {{--<div class="form-group">--}}
                {{--@foreach($license_types as $license_type_id => $license_type_name)--}}
                    {{--<div class="checkbox checkbox-primary">--}}
                        {{--{{ Form::checkbox('license_types[]', $license_type_id, (!empty($user) && $user->license_types->contains($license_type_id)?true:false), array('id'=>'license_type-checkbox-'.$license_type_id, 'data-parsley-multiple'=>'checkbox-'.$license_type_id, 'data-parsley-id'=>$license_type_id)) }}--}}
                        {{--{{ Form::label('license_type-checkbox-'.$license_type_id, $license_type_name) }}--}}
                    {{--</div>--}}
                {{--@endforeach--}}
            {{--</div>--}}

        </div>

        <div class="col-md-4">

            <div class="form-group">
                {{ Form::label('terms', 'Terms') }}
                {{ Form::select("details[terms]", [''=>'-- Select --'] + config('highline.payment_terms'), null, ['class'=>'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('active', 'Active') }}
                {{ Form::select("active", ['1'=>'Yes', '0'=>'No'], old('active'), ['class'=>'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('details[house_account]', 'House Account') }}
                {{ Form::select("details[house_account]", ['0'=>'No','1'=>'Yes'], old('house_account'), ['class'=>'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('details[region]', 'Region') }}
                {{ Form::select("details[region]", ['NorCal'=>'NorCal','SoCal'=>'SoCal'], old('region'), ['class'=>'form-control']) }}
            </div>

            <div class="form-group">
                {{ Form::label('delivery_window', 'Delivery Window') }}
                {{ Form::text('details[delivery_window]', old('delivery_window'), array('class' => 'form-control', 'placeholder' => 'Delivery Window')) }}
            </div>

            {{--<h5>Cultivation License</h5>--}}
            {{--<div class="form-group">--}}
                {{--{{ Form::label('cult_med_license_number', 'Medical License #') }}--}}
                {{--{{ Form::text('details[cult_med_license_number]', old('cult_med_license_number'), array('class' => 'form-control', 'placeholder' => 'Medical License #')) }}--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--{{ Form::label('cult_rec_license_number', 'Adult-Use License #') }}--}}
                {{--{{ Form::text('details[cult_rec_license_number]', old('cult_rec_license_number'), array('class' => 'form-control', 'placeholder' => 'Adult-Use License #')) }}--}}
            {{--</div>--}}

            {{--<h5>Distributor License</h5>--}}
            {{--<div class="form-group">--}}
                {{--{{ Form::label('distro_med_license_number', 'Medical License #') }}--}}
                {{--{{ Form::text('details[distro_med_license_number]', old('distro_med_license_number'), array('class' => 'form-control', 'placeholder' => 'Medical License #')) }}--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--{{ Form::label('distro_rec_license_number', 'Adult-Use License #') }}--}}
                {{--{{ Form::text('details[distro_rec_license_number]', old('distro_rec_license_number'), array('class' => 'form-control', 'placeholder' => 'Adult-Use License #')) }}--}}
            {{--</div>--}}

            {{--<h5>Retailer License</h5>--}}
            {{--<div class="form-group">--}}
                {{--{{ Form::label('med_license_number', 'Medical License #') }}--}}
                {{--{{ Form::text('details[med_license_number]', old('med_license_number'), array('class' => 'form-control', 'placeholder' => 'Medical License #')) }}--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--{{ Form::label('rec_license_number', 'Adult-Use License #') }}--}}
                {{--{{ Form::text('details[rec_license_number]', old('rec_license_number'), array('class' => 'form-control', 'placeholder' => 'Adult-Use License #')) }}--}}
            {{--</div>--}}

            {{--<h5>Microbusiness License</h5>--}}
            {{--<div class="form-group">--}}
                {{--{{ Form::label('mb_license_number', 'License #') }}--}}
                {{--{{ Form::text('details[mb_license_number]', old('mb_license_number'), array('class' => 'form-control', 'placeholder' => 'License #')) }}--}}
            {{--</div>--}}

            {{--<h5>Testing Laboratory License</h5>--}}
            {{--<div class="form-group">--}}
                {{--{{ Form::label('lab_license_number', 'License #') }}--}}
                {{--{{ Form::text('details[lab_license_number]', old('lab_license_number'), array('class' => 'form-control', 'placeholder' => 'License #')) }}--}}
            {{--</div>--}}

            {{--<h5>Manufacturing License</h5>--}}
            {{--<div class="form-group">--}}
                {{--{{ Form::label('mfg_license_number', 'License #') }}--}}
                {{--{{ Form::text('details[mfg_license_number]', old('mfg_license_number'), array('class' => 'form-control', 'placeholder' => 'License #')) }}--}}
            {{--</div>--}}


        </div>



    </div>



    <hr>

    <h2 class="header-title">Licenses</h2>

    @empty($user->licenses)

        @include('licenses._form')

    @else

        <ul>
        @foreach($user->licenses as $license)
            <li>
                {{ ($license->legal_business_name?:$user->details['business_name']) }} - {{ $license->number }} ({{ $license->license_type->name }}) - <a href="{{ route('users.licenses.edit', [$user->id, $license->id]) }}">Edit</a>
            </li>

        @endforeach
        </ul>

        <a href="{{ route('users.licenses.create', $user->id) }}" class="btn btn-primary">Add New License</a>

    @endempty

        @empty($active_role)


        <hr>

    <div class="row">

        <div class="col-4">

            <h2 class="header-title">Roles</h2>

            <div class="form-group">

                @foreach($roles as $role)

                    <div class="checkbox checkbox-primary">
                        {{ Form::checkbox('roles[]', $role->id, (!empty($user) && $user->roles->contains($role->id)?true:false), array('id'=>'role-checkbox-'.$role->id, 'data-parsley-multiple'=>'checkbox-'.$role->id, 'data-parsley-id'=>$role->id)) }}
                        {{ Form::label('role-checkbox-'.$role->id, $role->name) }}

                        @if($role->permissions->count())

                        <ul>
                            @foreach($role->permissions as $permission)
                            <li>{{ $permission->name }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                @endforeach

            </div>
        </div>

        <div class="col-8">
            <h2 class="header-title">Permissions</h2>

            <div class="form-group">
<div class="row">
                @foreach($permissions as $permission)
                    <div class="checkbox checkbox-primary col-6">
                        {{ Form::checkbox('permissions[]', $permission->id, (!empty($user) && $user->userPermissions->contains($permission->id)?true:false), array('id'=>'perm-checkbox-'.$permission->id, 'data-parsley-multiple'=>'checkbox-'.$permission->id, 'data-parsley-id'=>$permission->id)) }}
                        {{ Form::label('perm-checkbox-'.$permission->id, $permission->name) }}
                    </div>
                @endforeach
</div>
            </div>
        </div>

    </div>

    @endempty

    @isset($active_role)
    {{ Form::hidden('active_role', $active_role) }}
    @endisset

    @isset($active_role_id)
    {{ Form::hidden('roles[]', $active_role_id) }}
    @endisset


