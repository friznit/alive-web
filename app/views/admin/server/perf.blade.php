@extends('admin.layouts.default')
 
<div class="admin-panel">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

            {{-- Content --}}
            @section('content')

            <h2>Server Performance</h2>
            <form class="light" action="{{ URL::to('admin/server/perfsearch') }}" method="post">

                {{ Form::token() }}

                <div class="row">
                    <div class="col-lg-4">
                        <div class="input-group">
                            <input type="text" name="query" class="form-control">
                            <span class="input-group-btn">
                            <input class="btn btn-default" type="submit" value="Search">
                            </span>
                        </div>
                        <?php
                        if($errors->has('query')){
                            echo '<span class="label label-danger">' . $errors->first('query') . '</span>';
                        }
                        ?>
                        <div class="input-group">
                            <label class="radio-inline">
                                <input type="radio" name="type" value="name" checked> by Name
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="type" value="hostname"> by Hostname
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="type" value="ip"> by IP
                            </label>
                        </div>
                    </div>
                </div>

            </form>
            <button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/server/index') }}'">See Server List</button>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Clan</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($allServers as $server)
                        <tr>
                            <td><a href="{{ URL::to('admin/server/show') }}/{{ $server->id }}">{{{ $server->name }}}</a></td>
                            <td>{{{ $server->clan->name }}}</td>
                            <td>@include('warroom/charts/smallserverperf')</td>
                        </tr>
                @endforeach
                </tbody>
            </table>

            <?php echo $allServers->links(); ?>

            </div>
        </div>
    </div>
</div>



@stop
