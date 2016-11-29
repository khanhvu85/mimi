@extends('layouts.admin')

@section('content')
<div class="col-xs-12">

    <h1>admin {{ $admin->id }}
        <a href="{{ url('admin/admins/' . $admin->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit admin"><span class="glyphicon glyphicon-pencil" aria-hidden="true"/></a>
        {!! Form::open([
            'method'=>'DELETE',
            'url' => ['admin/admins', $admin->id],
            'style' => 'display:inline'
        ]) !!}
            {!! Form::button('<span class="glyphicon glyphicon-trash" aria-hidden="true"/>', array(
                    'type' => 'submit',
                    'class' => 'btn btn-danger btn-xs',
                    'title' => 'Delete admin',
                    'onclick'=>'return confirm("Confirm delete?")'
            ));!!}
        {!! Form::close() !!}
    </h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <tbody>
                <tr>
                    <th>ID</th><td>{{ $admin->id }}</td>
                </tr>
                <tr><th> Username </th><td> {{ $admin->username }} </td></tr><tr><th> Password </th><td> {{ $admin->password }} </td></tr><tr><th> Role </th><td> {{ $admin->role }} </td></tr>
            </tbody>
        </table>
    </div>

</div>
@endsection
