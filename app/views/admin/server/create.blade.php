@extends('admin.layouts.default')

{{-- Content --}}
@section('content')
<div class="dark-panel form-holder">

    <div class="container">
        <div class="row">

            <div class="col-md-12">
                @include('alerts/alerts')
            </div>

        </div>
        <div class="row">

            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Create Server <span class="badge" data-toggle="modal" data-target="#myModal">?</span></h3>
                    </div>

                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Guidance on Server Settings</h4>
                                </div>
                                <div class="strip">
                                    <p>We require a number of identifiers for your group's servers</p>
                                </div>
                                <div class="modal-body">
                                    <h4>Server Name</h4>
                                    <p> - This should be entered as it appears in your server.cfg file, i.e. what the Arma 3 MP server name is listed as.</p>
                                    <h4>Hostname </h4>
                                    <p>- This should be the actual machine name of your server, i.e. run <i>hostname</i> from the command prompt</p>
                                    <h4>IP Address</h4>
                                    <p> - The external IP address of your Arma 3 dedicated server. We only support dedicated servers for War Room.</p>
                                    <h4>Notes</h4>
                                    <p>Add any additional information here like Teamspeak IP:port, times its available, public game times, etc.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ URL::to('admin/server/create') }}/{{ $clan->id }}" method="post">

                        {{ Form::token() }}

                        <div class="panel-body">

                            <div class="form-group {{ ($errors->has('name')) ? 'has-error' : '' }}" for="name">
                                <label class="control-label" for="name">Name</label>
                                <input name="name" value="{{ Request::old("name") }}" type="text" class="form-control" placeholder="Name">
                                <?php
                                if($errors->has('name')){
                                    echo '<span class="label label-danger">' . $errors->first('name') . '</span>';
                                }
                                ?>
                            </div>
                            


                            <div class="form-group {{ ($errors->has('hostname')) ? 'has-error' : '' }}" for="hostname">
                                <label class="control-label" for="hostname">Hostname</label>
                                <input name="hostname" value="{{ Request::old("hostname") }}" type="text" class="form-control" placeholder="Hostname">
                                <?php
                                if($errors->has('hostname')){
                                    echo '<span class="label label-danger">' . $errors->first('hostname') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('ip')) ? 'has-error' : '' }}" for="ip">
                                <label class="control-label" for="ip">IP Address</label>
                                <input name="ip" value="{{ Request::old("ip") }}" type="text" class="form-control" placeholder="IP Address">
                                <?php
                                if($errors->has('ip')){
                                    echo '<span class="label label-danger">' . $errors->first('ip') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('note')) ? 'has-error' : '' }}" for="note">
                                <label class="control-label" for="note">Notes</label>
                                <textarea name="note" class="form-control" placeholder="Notes">{{ Request::old("note") }}</textarea>
                                <?php
                                if($errors->has('note')){
                                    echo '<span class="label label-danger">' . $errors->first('note') . '</span>';
                                }
                                ?>
                            </div>

                        </div>
                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <input class="btn btn-yellow" type="submit" value="Create New Server">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@stop