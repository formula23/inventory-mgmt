@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-xl-3 col-lg-4">
            @can('users.edit')
            <a href="{{ route('users.edit', $user->id) }}">Edit</a>
            @endcan
            <div class="text-center card-box">
                <div class="member-card">
                    {{--<div class="thumb-xl member-thumb m-b-10 center-block">--}}
                    {{--<img src="assets/images/users/avatar-1.jpg" class="rounded-circle img-thumbnail" alt="profile-image">--}}
                    {{--</div>--}}

                    <div class="">
                        <h5 class="m-b-5">{{ $user->name }}</h5>
                        {{--<p class="text-muted">@webdesigner</p>--}}
                    </div>

                    {{--<button type="button" class="btn btn-success btn-sm w-sm waves-effect m-t-10 waves-light">Follow</button>--}}
                    {{--<button type="button" class="btn btn-danger btn-sm w-sm waves-effect m-t-10 waves-light">Message</button>--}}


                    <div class="text-left m-t-40">
                        <p class="text-muted font-13"><strong>Full Name :</strong> <span class="m-l-15">{{ $user->name }}</span></p>

                        <p class="text-muted font-13"><strong>Mobile :</strong><span class="m-l-15">{{ $user->present()->phone_number }}</span></p>

                        <p class="text-muted font-13"><strong>Email :</strong> <span class="m-l-15"><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></span></p>

                        <p class="text-muted font-13"><strong>License Types :</strong>
                            <span class="m-l-15">
                            <ul>
                                @foreach($user->license_types as $license_type)
                                    <li>{{ $license_type->name }}</li>
                                @endforeach
                            </ul>
                            </span>
                        </p>


                        <p class="text-muted font-13"><strong>Roles :</strong>
                            <span class="m-l-15">
                            <ul>
                                @foreach($user->roles as $role)
                                    <li>{{ $role->name }}
                                        <ul>
                                        @foreach($role->permissions as $permission)
                                                <li>{{ $permission->name }}</li>
                                            @endforeach

                                    </ul>

                                    </li>
                                @endforeach
                            </ul>
                            </span>
                        </p>

                        <p class="text-muted font-13"><strong>Permissions :</strong>
                            <span class="m-l-15">
                            <ul>
                                @foreach($user->userPermissions as $permission)
                                    <li>{{ $permission->name }}</li>
                                @endforeach
                            </ul>
                            </span>
                        </p>

                        {{--<p class="text-muted font-13"><strong>Location :</strong> <span class="m-l-15">USA</span></p>--}}
                    </div>

                    {{--<ul class="social-links list-inline m-t-30">--}}
                    {{--<li class="list-inline-item">--}}
                    {{--<a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Facebook"><i class="fa fa-facebook"></i></a>--}}
                    {{--</li>--}}
                    {{--<li class="list-inline-item">--}}
                    {{--<a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Twitter"><i class="fa fa-twitter"></i></a>--}}
                    {{--</li>--}}
                    {{--<li class="list-inline-item">--}}
                    {{--<a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Skype"><i class="fa fa-skype"></i></a>--}}
                    {{--</li>--}}
                    {{--</ul>--}}

                </div>

            </div> <!-- end card-box -->

            {{--<div class="card-box">--}}
            {{--<h4 class="m-t-0 m-b-20 header-title">Skills</h4>--}}

            {{--<div class="p-b-10">--}}
            {{--<p>HTML5</p>--}}
            {{--<div class="progress progress-sm">--}}
            {{--<div class="progress-bar" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<p>PHP</p>--}}
            {{--<div class="progress progress-sm">--}}
            {{--<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<p>Wordpress</p>--}}
            {{--<div class="progress progress-sm mb-0">--}}
            {{--<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width: 70%">--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}

        </div> <!-- end col -->


        <div class="col-lg-8 col-xl-9">
            <div class="">
                <div class="card-box">
                    <ul class="nav nav-tabs tabs-bordered">
                        {{--<li class="nav-item">--}}
                        {{--<a href="#home" data-toggle="tab" aria-expanded="false" class="nav-link">--}}
                        {{--ABOUT ME--}}
                        {{--</a>--}}
                        {{--</li>--}}
                        {{--<li class="nav-item">--}}
                        {{--<a href="#profile" data-toggle="tab" aria-expanded="true" class="nav-link active">--}}
                        {{--GALLERY--}}
                        {{--</a>--}}
                        {{--</li>--}}

                        <li class="nav-item">
                            <a href="#info" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                INFO
                            </a>
                        </li>

                    </ul>

                    <div class="tab-content">
                        {{--<div class="tab-pane" id="home">--}}
                        {{--<p class="m-b-5">Hi I'm Johnathn Deo,has been the industry's standard dummy text ever--}}
                        {{--since the 1500s, when an unknown printer took a galley of type.--}}
                        {{--Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.--}}
                        {{--In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo.--}}
                        {{--Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras--}}
                        {{--dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend--}}
                        {{--tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend--}}
                        {{--ac, enim.</p>--}}

                        {{--<div class="m-t-30">--}}
                        {{--<h5>Experience</h5>--}}

                        {{--<div class=" p-t-10">--}}
                        {{--<h6 class="text-primary m-b-5">Lead designer / Developer</h6>--}}
                        {{--<p class="">websitename.com</p>--}}
                        {{--<p><b>2010-2015</b></p>--}}

                        {{--<p class="text-muted font-13 m-b-0">Lorem Ipsum is simply dummy text--}}
                        {{--of the printing and typesetting industry. Lorem Ipsum has--}}
                        {{--been the industry's standard dummy text ever since the--}}
                        {{--1500s, when an unknown printer took a galley of type and--}}
                        {{--scrambled it to make a type specimen book.--}}
                        {{--</p>--}}
                        {{--</div>--}}

                        {{--<hr>--}}

                        {{--<div class="">--}}
                        {{--<h6 class="text-primary m-b-5">Senior Graphic Designer</h6>--}}
                        {{--<p class="">coderthemes.com</p>--}}
                        {{--<p><b>2007-2009</b></p>--}}

                        {{--<p class="text-muted font-13">Lorem Ipsum is simply dummy text--}}
                        {{--of the printing and typesetting industry. Lorem Ipsum has--}}
                        {{--been the industry's standard dummy text ever since the--}}
                        {{--1500s, when an unknown printer took a galley of type and--}}
                        {{--scrambled it to make a type specimen book.--}}
                        {{--</p>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="tab-pane " id="profile">--}}
                        {{--<div class="row">--}}
                        {{--<div class="col-sm-4">--}}
                        {{--<div class="gal-detail thumb">--}}
                        {{--<a href="#" class="image-popup" title="Screenshot-2">--}}
                        {{--<img src="assets/images/gallery/1.jpg" class="thumb-img" alt="work-thumbnail">--}}
                        {{--</a>--}}
                        {{--<h4 class="text-center">Gallary Image</h4>--}}
                        {{--<div class="ga-border"></div>--}}
                        {{--<p class="text-muted text-center"><small>Photography</small></p>--}}
                        {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="col-sm-4">--}}
                        {{--<div class="gal-detail thumb">--}}
                        {{--<a href="#" class="image-popup" title="Screenshot-2">--}}
                        {{--<img src="assets/images/gallery/2.jpg" class="thumb-img" alt="work-thumbnail">--}}
                        {{--</a>--}}
                        {{--<h4 class="text-center">Gallary Image</h4>--}}
                        {{--<div class="ga-border"></div>--}}
                        {{--<p class="text-muted text-center"><small>Photography</small></p>--}}
                        {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="col-sm-4">--}}
                        {{--<div class="gal-detail thumb">--}}
                        {{--<a href="#" class="image-popup" title="Screenshot-2">--}}
                        {{--<img src="assets/images/gallery/3.jpg" class="thumb-img" alt="work-thumbnail">--}}
                        {{--</a>--}}
                        {{--<h4 class="text-center">Gallary Image</h4>--}}
                        {{--<div class="ga-border"></div>--}}
                        {{--<p class="text-muted text-center"><small>Photography</small></p>--}}
                        {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="col-sm-4">--}}
                        {{--<div class="gal-detail thumb">--}}
                        {{--<a href="#" class="image-popup" title="Screenshot-2">--}}
                        {{--<img src="assets/images/gallery/4.jpg" class="thumb-img" alt="work-thumbnail">--}}
                        {{--</a>--}}
                        {{--<h4 class="text-center">Gallary Image</h4>--}}
                        {{--<div class="ga-border"></div>--}}
                        {{--<p class="text-muted text-center"><small>Photography</small></p>--}}
                        {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="col-sm-4">--}}
                        {{--<div class="gal-detail thumb">--}}
                        {{--<a href="#" class="image-popup" title="Screenshot-2">--}}
                        {{--<img src="assets/images/gallery/5.jpg" class="thumb-img" alt="work-thumbnail">--}}
                        {{--</a>--}}
                        {{--<h4 class="text-center">Gallary Image</h4>--}}
                        {{--<div class="ga-border"></div>--}}
                        {{--<p class="text-muted text-center"><small>Photography</small></p>--}}
                        {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="col-sm-4">--}}
                        {{--<div class="gal-detail thumb">--}}
                        {{--<a href="#" class="image-popup" title="Screenshot-2">--}}
                        {{--<img src="assets/images/gallery/6.jpg" class="thumb-img" alt="work-thumbnail">--}}
                        {{--</a>--}}
                        {{--<h4 class="text-center">Gallary Image</h4>--}}
                        {{--<div class="ga-border"></div>--}}
                        {{--<p class="text-muted text-center"><small>Photography</small></p>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        <div class="tab-pane active" id="info">

                            @if($user->details)
                                <pre>
                                {{ print_r($user->details, 1) }}
                                </pre>
                            @endif

                        </div>



                    </div>
                </div>
            </div>

        </div> <!-- end col -->


    </div>
    <!-- end row -->

    @if($user->purchase_orders->count())

        <div class="card-box">

            <h4 class="text-dark  header-title m-t-0">Purchase Orders / Batches / Margins</h4>

            <div class="row mb-3 hidden-print">
                <div class="col-lg-12">

                    {{ Form::open(['route' => ['users.show', $user->id], 'method' => 'get']) }}

                    <div class="card">

                        <div class="card-header cursor-pointer" role="tab" id="filters" >

                            <div class="row">
                                <div class="col-md-3">
                                    <a href="#collapse-filters" data-toggle="collapse"><strong><i class="ti-arrow-circle-down"></i> Filters</strong></a>
                                    <a href="{{ route('purchase-orders.reset-filters') }}" class="small ml-2">Reset</a>
                                </div>
                                <div class="col-md-5">
                                    @if($filters)
                                        @foreach($filters as $filter=>$vals)
                                            <span style="margin-right: 15px;">{!! display_filters($filter, $vals, $purchase_orders) !!}</span>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-md-4 text-right">
                                    {{--Subtotal:<strong>{{ display_currency($purchase_orders->sum('subtotal')) }}</strong> | Total:<strong>{{ display_currency($purchase_orders->sum('total')) }}</strong> | Outstanding Balance: <strong>{{ display_currency($purchase_orders->sum('balance')) }}</strong>--}}
                                </div>
                            </div>

                        </div>

                        <div id="collapse-filters" class="collapse card-block" role="tabpanel" aria-labelledby="collapse-filters" >

                            <div class="row">
                                {{--<div class="col-lg-2">--}}
                                    {{--<dl class="row">--}}
                                        {{--<dt class="col-lg-4 text-lg-right">Status:</dt>--}}
                                        {{--<dd class="col-lg-8">--}}

                                            {{--@foreach(config('highline.po_statuses') as $order_status)--}}
                                                {{--<div class="checkbox">--}}
                                                    {{--<input id="checkbox_{{$order_status}}" type="checkbox" name="filters[status][{{$order_status}}]" value="{{ ucwords($order_status) }}" {{ (isset($filters['status']) ? (in_array($order_status, array_keys($filters['status']))?'checked':''):'') }}>--}}

                                                    {{--<label for="checkbox_{{$order_status}}">--}}
                                                        {{--<span class="badge badge-{{ status_class($order_status) }}">{!! display_status($order_status) !!}</span>--}}
                                                    {{--</label>--}}
                                                {{--</div>--}}
                                            {{--@endforeach--}}

                                        {{--</dd>--}}
                                    {{--</dl>--}}

                                {{--</div>--}}

                                <div class="col-lg-3">
                                    <dl class="row">
                                        <dt class="col-lg-5 text-lg-right">Date Preset:</dt>
                                        <dd class="col-lg-6">

                                            <select id="date_preset" name="filters[date_preset]" class="form-control">
                                                <option value="">- Select -</option>
                                                @for($i=0; $i<=3; $i++)
                                                    <option value="{{ \Carbon\Carbon::now()->firstOfMonth()->subMonth($i)->format('m-Y') }}"{{ (isset($filters['date_preset']) ? (\Carbon\Carbon::now()->firstOfMonth()->subMonth($i)->format('m-Y') == $filters['date_preset'] ? 'selected' : '' ) : '') }}>{{ \Carbon\Carbon::now()->firstOfMonth()->subMonth($i)->format('F, Y') }}</option>
                                                @endfor
                                            </select>
                                        </dd>
                                        <dt class="col-lg-5 text-lg-right"></dt>
                                        <dd class="col-lg-6"><p>-- OR --</p>
                                        </dd>
                                        <dt class="col-lg-5 text-lg-right">Custom Date:</dt>
                                        <dd class="col-lg-6">
                                            From:<input class="form-control" type="date" name="filters[from_date]" value="{{ (isset($filters['from_date']) ? $filters['from_date'] : '') }}">
                                            To:<input class="form-control" type="date" name="filters[to_date]" value="{{ (isset($filters['to_date']) ? $filters['to_date'] : '') }}">
                                        </dd>
                                    </dl>

                                </div>
                                <div class="col-lg-3">
                                    {{--<dl class="row">--}}
                                        {{--<dt class="col-lg-3 text-lg-right">Funding:</dt>--}}
                                        {{--<dd class="col-lg-9">--}}
                                            {{--{{ Form::select("filters[fund_id]", $funds, (!empty($filters['fund_id'])?$filters['fund_id']:null), ['class'=>'form-control', 'placeholder'=>'-- Select --']) }}--}}
                                        {{--</dd>--}}
                                    {{--</dl>--}}
                                </div>

                                {{--<div class="col-lg-3">--}}
                                    {{--<dl class="row">--}}
                                        {{--<dt class="col-lg-3 text-lg-right">Vendor:</dt>--}}
                                        {{--<dd class="col-lg-9">--}}

                                            {{--<select id="vendor" name="filters[vendor]" class="form-control">--}}
                                                {{--<option value="">- Select -</option>--}}
                                                {{--@foreach($vendors as $vendor)--}}
                                                    {{--<option value="{{ $vendor->id }}"{{ (isset($filters['vendor']) ? ($vendor->id == $filters['vendor'] ? 'selected' : '' ) : '') }}>{{$vendor->name}}</option>--}}
                                                {{--@endforeach--}}
                                            {{--</select>--}}

                                        {{--</dd>--}}
                                    {{--</dl>--}}
                                    {{--<dl class="row">--}}
                                        {{--<dt class="col-lg-3 text-lg-right">License Type:</dt>--}}
                                        {{--<dd class="col-lg-9">--}}

                                            {{--@foreach(['cultivator','distributor','microbusiness'] as $license_type)--}}
                                                {{--<div class="checkbox">--}}
                                                    {{--<input id="checkbox_{{$license_type}}" type="checkbox" name="filters[license_type][{{$license_type}}]" value="{{ ucwords($license_type) }}" {{ (isset($filters['license_type']) ? (in_array($license_type, array_keys($filters['license_type']))?'checked':''):'') }}>--}}

                                                    {{--<label for="checkbox_{{$license_type}}">--}}
                                                        {{--<span class="badge badge-{{ status_class($license_type) }}">{!! display_status($license_type) !!}</span>--}}
                                                    {{--</label>--}}
                                                {{--</div>--}}
                                            {{--@endforeach--}}
                                        {{--</dd>--}}
                                    {{--</dl>--}}
                                {{--</div>--}}

                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Filter</button>

                        </div>

                    </div>

                    {{ Form::close() }}

                </div>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>PO Date</th>
                    <th>Order#</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                @foreach($purchase_orders as $purchase_order)

                    <tr style="background: #d6d6d6;">
                        <td>{{ $purchase_order->txn_date->format('m/d/Y') }}</td>
                        <td><a href="{{ route('purchase-orders.show', $purchase_order) }}">{{ $purchase_order->ref_number }}</a></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    @if($purchase_order->batches->count())

                        <tr>
                            {{--<th></th>--}}
                            <th>ID</th>
                            <th>UID</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Units Purchased</th>
                            <th>Inventory</th>
                            <th>Sale</th>
                            <th>Units Accepted</th>
                            <th>Cost</th>
                            <th>Revenue</th>
                            <th>Profit/Loss</th>
                            <th>Reconciliations +/-</th>
                        </tr>

                        @include('users._purchase-orders', ['child_batches'=>$purchase_order->batches, 'depth'=>0, $created_batches])

                    @endif

                @endforeach

                </tbody>

            </table>

        </div>

    @endif

@endsection
