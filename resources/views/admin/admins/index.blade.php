@extends('layouts.setting')

@section('content')
<div class="col-xs-12">

    <h1>Admins <a href="{{ url('/admin/admins/create') }}" class="btn btn-primary btn-xs" title="Add New admin"><span class="glyphicon glyphicon-plus" aria-hidden="true"/></a></h1>
    <div class="table">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>S.No</th><th> Username </th><th> Role </th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            {{-- */$x=0;/* --}}
            @foreach($admins as $item)
                {{-- */$x++;/* --}}
                <tr>
                    <td>{{ $x }}</td>
                    <td>{{ $item->username }}</td><td>{{ $item->role }}</td>
                    <td>
                        <a href="{{ url('/admin/admins/' . $item->id) }}" class="btn btn-success btn-xs" title="View admin"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"/></a>
                        <a href="{{ url('/admin/admins/' . $item->id . '/edit') }}" class="btn btn-primary btn-xs" title="Edit admin"><span class="glyphicon glyphicon-pencil" aria-hidden="true"/></a>
                        {!! Form::open([
                            'method'=>'DELETE',
                            'url' => ['/admin/admins', $item->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<span class="glyphicon glyphicon-trash" aria-hidden="true" title="Delete admin" />', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'Delete admin',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ));!!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination-wrapper"> {!! $admins->render() !!} </div>
    </div>

</div>
@endsection
