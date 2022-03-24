@extends('layouts.app')


@section('content')
<h3>Results: {{ $users->count() }}</h3>

<div class="row">

    <div class="col-lg-12 mb-3">
        <div id="datatable-buttons" class="pull-right"></div>
    </div>

</div>

<div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <div class="table-responsive">
                    <table id="user-datatable" class="table table-hover table-striped">

                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>DBA</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Active</th>
                            <th>Roles</th>
                            <th>Types</th>
                            <th>
                                @can('users.create')
                                <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="ion-person-add"></i></a>
                                @endcan
                            </th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td><a href="{{ route('users.show', $user->id) }}">{{ $user->name }}</a></td>
                                <td>{{ !empty($user->details['business_name'])?$user->details['business_name']:"--" }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ ($user->active?'Yes':'No') }}</td>
                                <td>{!! display_roles($user) !!}</td>
                                <td>{!! display_list($user->license_types) !!}</td>
                                <td>
                                    @can('users.edit')
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                        <a href="{{ route('users.edit', ['user'=>$user->id]) }}" class="btn btn-secondary"><i class="ion-edit"></i></a>
                                        {{ method_field('DELETE') }}
                                        {{ csrf_field() }}
                                        <button class="btn btn-danger"><i class="ion-trash-a"></i></button>
                                        {{--<a href="{{ route('users.destroy', ) }}" class="btn btn-danger"></a>--}}
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('css')

    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">

@endsection

@section('js')

    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('plugins/moment/min/moment.min.js') }}"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            $.fn.dataTable.moment('MM/DD/YYYY');

            // $('[type="date"]').datepicker();

            var table = $('#user-datatable').DataTable({
                lengthChange: true,
                paging: true,
                "order": [[ 1, "asc" ]],
                "displayLength": 25,
                buttons: ['excel', 'pdf', 'colvis']
            });

            table.buttons().container().appendTo('#datatable-buttons');
        } );

    </script>

@endsection