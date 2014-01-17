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

            <div class="col-md-4">
                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Edit Server <span class="badge" data-toggle="modal" data-target="#myModal">?</span></h3>
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
                    
                    <form action="{{ URL::to('admin/server/edit') }}/{{ $server->id }}" method="post">

                        {{ Form::token() }}

                        <div class="panel-body">

                             <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}" for="name">
                                <label class="control-label" for="name">Server name</label>
                                <input name="name" value="{{ (Request::old('name')) ? Request::old("name") : $server->name }}" type="text" class="form-control" placeholder="username">
                                <?php
                                if($errors->has('name')){
                                    echo '<span class="label label-danger">' . $errors->first('name') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('hostname')) ? 'has-error' : '' }}" for="hostname">
                                <label class="control-label" for="hostname">Hostname</label>
                                <input name="hostname" value="{{ (Request::old('hostname')) ? Request::old("hostname") : $server->hostname }}" type="text" class="form-control" placeholder="Hostname">
                                <?php
                                if($errors->has('hostname')){
                                    echo '<span class="label label-danger">' . $errors->first('hostname') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('ip')) ? 'has-error' : '' }}" for="ip">
                                <label class="control-label" for="ip">IP Address</label>
                                <input name="ip" value="{{ (Request::old('ip')) ? Request::old("ip") : $server->ip }}" type="text" class="form-control" placeholder="IP Address">
                                <?php
                                if($errors->has('ip')){
                                    echo '<span class="label label-danger">' . $errors->first('ip') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('note')) ? 'has-error' : '' }}" for="note">
                                <label class="control-label" for="note">Notes</label>
                                <textarea name="note" class="form-control" placeholder="Notes">{{ (Request::old('note')) ? Request::old("note") : $server->note }}</textarea>
                                <?php
                                if($errors->has('note')){
                                    echo '<span class="label label-danger">' . $errors->first('note') . '</span>';
                                }
                                ?>
                            </div>

                        </div>
                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <input class="btn btn-yellow" type="submit" value="Save">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Connect server to War Room</h3>
                    </div>

                    {{ Form::token() }}

                    <div class="panel-body">

                        <div class="strip">
                            <p>Getting your server up and running</p>
                        </div>

                        <table class="table">
                            <tr>
                                <td width="80">Step 1</td>
                                <td>Download the ALiVE server addin and extract the folder into the @ALiVE folder in your Dedicated Server Arma 3 root folder.</td>
                                <td><a class="btn btn-yellow" href="{{ URL::to('/') }}/downloads/ALiVE.zip"><i class="fa fa-download"></i> Download</a></td>
                            </tr>
                            <tr>
                                <td>Step 2</td>
                                <td>Download and save the alive.cfg file to <i>C:\Users\USERNAME\AppData\Local\ALiVE</i>.<br />You may need to create the directory yourself, if it's not there.</td>
                                <td><a class="btn btn-yellow" href="{{ URL::to('admin/server/config') }}/{{ $clan->id }}"><i class="fa fa-download"></i> Download</a></td>
                            </tr>
                            <tr>
                                <td>Step 3</td>
                                <td>Important: Your ALiVE mission needs the ALiVE System Database module (available in the Arma 3 Editor) placed for this feature to work!</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Step 4</td>
                                <td>Run your ALiVE MP mission on your dedicated server and connect with your client</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Step 5</td>
                                <td>Go to alivemod.com War Room, under Recent Operations or Live Data Feed on the home page and you should see a message stating your mission was launched.</td>
                                <td></td>
                            </tr>
                        </table>

                        <div class="strip">
                            <p>Validating the setup (optional)</p>
                        </div>

                        <table class="table">
                            <tr>
                                <td width="80">Step 1</td>
                                <td>Download and install BareTail (log monitor)</td>
                                <td><a class="btn btn-yellow" target="_blank" href="http://www.baremetalsoft.com/baretail"><i class="fa fa-download"></i> Download</a></td>
                            </tr>
                            <tr>
                                <td>Step 2</td>
                                <td>Launch Arma3server.exe with the @ALiVE in the mod line on your dedicated server. Ensure the ALiVE Server addin has been downloaded into the @ALiVE folder</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Step 3</td>
                                <td>Launch your arma3.exe as normal (with @ALiVE but no need for server addin on your client)</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Step 4</td>
                                <td>Run any MP mission (with the Database Module placed) on your dedicated local server and connect with your client, go into the game</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Step 5</td>
                                <td>In baretail open users/username/appdata/local/arma2net/arma2net.log</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Step 6</td>
                                <td>In baretail open Arma3Server RPT usually users/username/appdata/local/Arma3/</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Step 7</td>
                                <td>Check for the log CONNECTED TO DATABASE OK</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Step 8</td>
                                <td>Go to alivemod.com War Room, under Recent Operations or Live Data Feed you should see a message stating your mission was launched.</td>
                                <td></td>
                            </tr>
                        </table>

                      

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@stop
