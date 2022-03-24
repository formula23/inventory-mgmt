@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-xl-3 col-lg-4">
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
                            <a href="#settings" data-toggle="tab" aria-expanded="false" class="nav-link active">
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
                        <div class="tab-pane active" id="settings">

                            {{ Form::model($user, ['role'=>'form', 'class'=>'form-horizontal', 'url'=>route('profile.update')]) }}

                            {{ method_field('PUT') }}

                                <div class="form-group">

                                    {{ Form::label('name', 'Full Name') }}
                                    {{ Form::text('name', old('name'), array('class' => 'form-control', 'placeholder' => 'Full Name')) }}

                                    {{--<label for="FullName">Full Name</label>--}}
                                    {{--<input type="text" value="{{ $user->name }}" id="FullName" class="form-control">--}}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('email', 'E-mail') }}
                                    {{ Form::text('email', old('email'), array('class' => 'form-control', 'placeholder' => 'E-mail')) }}

                                    {{--<label for="Email">Email</label>--}}
                                    {{--<input type="email" value="{{ $user->email }}" id="Email" class="form-control">--}}
                                </div>

                                <div class="form-group">
                                    {{ Form::label('phone', 'Phone') }}
                                    {{ Form::text('phone', old('phone'), array('class' => 'form-control', 'placeholder' => 'Phone')) }}
                                </div>

                                <div class="form-group">
                                    {{ Form::label('password', 'Password') }}
                                    {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Min. 6 characters')) }}
                                </div>

                                <div class="form-group">
                                    {{ Form::label('password_confirmation', 'Confirm Password') }}
                                    {{ Form::password('password_confirmation', array('class' => 'form-control', 'placeholder' => 'Min. 6 characters')) }}
                                </div>

                                <div class="form-group">
                                    {{ Form::label('pin', 'PIN') }}
                                    {{ Form::password('pin', array('class' => 'form-control', 'placeholder' => 'PIN')) }}
                                </div>

                                {{--<div class="form-group">--}}
                                    {{--<label for="Username">Phone</label>--}}
                                    {{--<input type="text" value="{{ $user->present()->phone_number }}" id="Phone" class="form-control">--}}
                                {{--</div>--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="Password">Password</label>--}}
                                    {{--<input type="password" placeholder="6 - 15 Characters" id="Password" class="form-control">--}}
                                {{--</div>--}}
                                {{--<div class="form-group">--}}
                                    {{--<label for="RePassword">Re-Password</label>--}}
                                    {{--<input type="password" placeholder="6 - 15 Characters" id="RePassword" class="form-control">--}}
                                {{--</div>--}}

                                <button class="btn btn-primary waves-effect waves-light w-md" type="submit">Save</button>

                            {{ Form::close() }}

                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- end col -->
    </div>
    <!-- end row -->


@endsection
