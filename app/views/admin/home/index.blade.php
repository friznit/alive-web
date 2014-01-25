@extends('admin.layouts.default')

{{-- Content --}}
@section('content')


<div class="admin-panel">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <h2>Admin</h2>

                @include('alerts/alerts')

                @if ($auth['isAdmin'])
                <li><a href="{{ URL::to('admin/ao') }}">AOs</a></li>
                <li><a href="{{ URL::to('admin/clan') }}">Groups</a></li>
                <li><a href="{{ URL::to('admin/server') }}">Servers</a></li>
                <li><a href="{{ URL::to('admin/user') }}">Users</a></li>
                <li><a href="{{ URL::to('admin/group') }}">User Groups</a></li>
                @endif

                <br/><br/><br/>

            </div>
        </div>
    </div>
</div>


@stop