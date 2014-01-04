@extends('admin.layouts.default')

{{-- Content --}}
@section('content')

<div class="dark-panel form-holder" xmlns="http://www.w3.org/1999/html">

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
                        <h3 class="panel-title">Edit Group</h3>
                    </div>
                    <form action="{{ URL::to('admin/clan/edit') }}/{{ $clan->id }}" method="post">

                        {{ Form::token() }}

                        <div class="panel-body">

                            <div class="form-group {{ ($errors->has('name')) ? 'has-error' : '' }}" for="name">
                                <label class="control-label" for="name">Name</label>
                                <input name="name" value="{{ (Request::old('name')) ? Request::old("name") : $clan->name }}" type="text" class="form-control" placeholder="Name">
                                <?php
                                if($errors->has('name')){
                                    echo '<span class="label label-danger">' . $errors->first('name') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('title')) ? 'has-error' : '' }}" for="title">
                                <label class="control-label" for="title">Title</label><span class="badge" data-toggle="modal" data-target="#titleModal">?</span>
                                <input name="title" value="{{ (Request::old('title')) ? Request::old("title") : $clan->title }}" type="text" class="form-control" placeholder="Title">
                                <?php
                                if($errors->has('title')){
                                    echo '<span class="label label-danger">' . $errors->first('title') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="modal fade" id="titleModal" tabindex="-1" role="dialog" aria-labelledby="titleModal" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel"></h4>
                                        </div>
                                        <div class="strip">
                                            <p>The short name for your group. Exporting your group as a squad XML will use this text in game to display on vehicles manned by squad members.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group {{ ($errors->has('country')) ? 'has-error' : '' }}" for="country">
                                <label class="control-label" for="country">Country</label>
                                <select name="country" value="{{ (Request::old('country')) ? Request::old("country") : $clan->country }}" type="text" class="form-control" placeholder="Country">
                                @foreach ($countries as $key =>$value)
                                @if ($key == $clan->country)
                                <option value="{{$key}}" selected="selected">{{$value}}</option>
                                @else
                                <option value="{{$key}}">{{$value}}</option>
                                @endif
                                @endforeach
                                </select>
                                <?php
                                if($errors->has('country')){
                                    echo '<span class="label label-danger">' . $errors->first('country') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('tag')) ? 'has-error' : '' }}" for="tag">
                                <label class="control-label" for="tag">Tag</label><span class="badge" data-toggle="modal" data-target="#tagModal">?</span>
                                <input name="tag" value="{{ (Request::old('tag')) ? Request::old("tag") : $clan->tag }}" type="text" class="form-control" placeholder="Tag">
                                <?php
                                if($errors->has('title')){
                                    echo '<span class="label label-danger">' . $errors->first('title') . '</span>';
                                }
                                ?>
                            </div>
                            
                             <div class="modal fade" id="tagModal" tabindex="-1" role="dialog" aria-labelledby="tagModal" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel"></h4>
                                        </div>
                                        <div class="strip">
                                            <p>This is the unique identifier in our global database. It has to be unique. If you delete your group and recreate it, ensure you use the same tag.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group {{ ($errors->has('type')) ? 'has-error' : '' }}" for="type">
                                <label class="control-label" for="type">Type</label>
                                <select name="type" value="{{ (Request::old('type')) ? Request::old("type") : $orbat['type'][0]->type }}" type="text" class="form-control" placeholder="Type">
                                @foreach ($groupTypes as $key =>$value)
                                @if ($key == $orbat['type'][0]->type)
                                <option value="{{$key}}" selected="selected">{{$value}}</option>
                                @else
                                <option value="{{$key}}">{{$value}}</option>
                                @endif
                                @endforeach
                                </select>
                                <?php
                                if($errors->has('type')){
                                    echo '<span class="label label-danger">' . $errors->first('type') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('size')) ? 'has-error' : '' }}" for="size">
                                <label class="control-label" for="size">Size</label>
                                <select name="size" value="{{ (Request::old('size')) ? Request::old("size") : $orbat['size'][0]->type  }}" size="text" class="form-control" placeholder="Size">
                                @foreach ($groupSizes as $group)
                                @if ($group->type == $orbat['size'][0]->type)
                                <option value="{{$group->type}}" selected="selected">{{$group->name}} ({{$group->min}}-{{$group->max}})</option>
                                @else
                                <option value="{{$group->type}}">{{$group->name}} ({{$group->min}}-{{$group->max}})</option>
                                @endif
                                @endforeach
                                </select>
                                <?php
                                if($errors->has('size')){
                                    echo '<span class="label label-danger">' . $errors->first('size') . '</span>';
                                }
                                ?>
                            </div>
                            
                            <div class="form-group {{ ($errors->has('website')) ? 'has-error' : '' }}" for="website">
                                <label class="control-label" for="website">Website</label>
                                <input name="website" value="{{ (Request::old('website')) ? Request::old("website") : $clan->website }}" type="text" class="form-control" placeholder="Website">
                                <?php
                                if($errors->has('website')){
                                    echo '<span class="label label-danger">' . $errors->first('website') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ $errors->has('twitchStream') ? 'has-error' : '' }}" for="twitchStream">
                                <label class="control-label" for="twitchStream">Twitch Stream</label>
                                <input name="twitchStream" value="{{ (Request::old('twitchStream')) ? Request::old("twitchStream") : $clan->twitch_stream }}" type="text" class="form-control" placeholder="Twitch Stream">
                                <?php
                                if($errors->has('twitchStream')){
                                    echo '<span class="label label-danger">' . $errors->first('twitchStream') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ $errors->has('teamspeak') ? 'has-error' : '' }}" for="teamspeak">
                                <label class="control-label" for="teamspeak">Teamspeak Server</label>
                                <input name="teamspeak" value="{{ (Request::old('teamspeak')) ? Request::old("teamspeak") : $clan->teamspeak }}" type="text" class="form-control" placeholder="Teamspeak Server">
                                <?php
                                if($errors->has('teamspeak')){
                                    echo '<span class="label label-danger">' . $errors->first('teamspeak') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('description')) ? 'has-error' : '' }}" for="description">
                                <label class="control-label" for="description">Description</label>
                                <textarea name="description" type="text" rows="6" class="form-control" placeholder="Description">{{ (Request::old('description')) ? Request::old("description") : $clan->description }}</textarea>
                                <?php
                                if($errors->has('description')){
                                    echo '<span class="label label-danger">' . $errors->first('description') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('allowApplicants')) ? 'has-error' : '' }}" for="allowApplicants">
                                <label class="checkbox inline">
                                    <input type="checkbox" name="allowApplicants"
                                    <?php
                                        if($clan->allow_applicants){
                                            echo 'checked';
                                        }
                                    ?>> Allow applicants<span class="badge" data-toggle="modal" data-target="#allowApplicationModal">?</span>
                                </label>

                                <?php
                                if($errors->has('allowApplicants')){
                                    echo '<span class="label label-danger">' . $errors->first('allowApplicants') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="modal fade" id="allowApplicationModal" tabindex="-1" role="dialog" aria-labelledby="allowApplicationModal" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel"></h4>
                                        </div>
                                        <div class="strip">
                                            <p>Enable players to apply to join your group, your group will be displayed in the 'find a group' list displayed to players looking for a group to join.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group {{ ($errors->has('applicationText')) ? 'has-error' : '' }}" for="applicationText">
                                <label class="control-label" for="applicationText">Application Text</label><span class="badge" data-toggle="modal" data-target="#applicationModal">?</span>
                                <textarea name="applicationText" type="text" rows="6" class="form-control" placeholder="Application Text">{{ (Request::old('applicationText')) ? Request::old("applicationText") : $clan->application_text }}</textarea>
                                <?php
                                if($errors->has('applicationText')){
                                    echo '<span class="label label-danger">' . $errors->first('applicationText') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="modal fade" id="applicationModal" tabindex="-1" role="dialog" aria-labelledby="applicationModal" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel"></h4>
                                        </div>
                                        <div class="strip">
                                            <p>This text will be displayed to players who choose to join your group, on the group application page. You can use this text to specify requirements for joining your group.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <input class="btn btn-dark" type="reset" value="Reset">
                                <input class="btn btn-yellow" type="submit" value="Save">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">

                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Add Group Member <span class="badge" data-toggle="modal" data-target="#addGroupMemberModal">?</span></h3>
                    </div>
                    
                    <div class="modal fade" id="addGroupMemberModal" tabindex="-1" role="dialog" aria-labelledby="addGroupMemberModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Guidance on adding Group Members</h4>
                                </div>
                                <div class="strip">
                                    <p>You can manually create users here and add them to your group. If you wish to bulk register group members then use the Import Squad XML function.</p>
                                </div>
                                <div class="modal-body">
                                    <h4>Email</h4>
                                    <p> - This must be an email address that is not registered with Alivemod.com. If the user is already registered, get them to join your group via the group application function on their profile page. The user will not recieve an activation email from us, you will have to notify them of their username and password manually.</p>
                                    <h4>Username</h4>
                                    <p> - This is usually their game "handle" or nickname. They will be able to change this once they log in.</p>
                                    <h4>Password</h4>
                                    <p> - Choose a temporary password for them but ensure they change their password when they first login.</p>
                                    <h4>Active Members</h4>
                                    <p>Please only add active members who will use Alivemod.com, we track activity so any inactive user accounts will be deleted.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ URL::to('admin/clan/memberadd') }}/{{ $clan->id }}" method="post">
                        {{ Form::token() }}

                        <div class="panel-body">

                            <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}" for="email">
                                <label for="email" class="control-label">Email</label>
                                <input name="email" type="email" class="form-control" id="email" placeholder="Email" value="{{ Request::old('email') }}">
                                <?php
                                if($errors->has('email')){
                                    echo '<span class="label label-danger">' . $errors->first('email') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('username')) ? 'has-error' : '' }}" for="username">
                                <label for="username" class="control-label">User name</label>
                                <input name="username" type="username" class="form-control" id="username" placeholder="Username" value="{{ Request::old('username') }}">
                                <?php
                                if($errors->has('username')){
                                    echo '<span class="label label-danger">' . $errors->first('username') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('password')) ? 'has-error' : '' }}" for="email">
                                <label for="password" class="control-label">Password</label>
                                <input name="password" value="" type="password" class="form-control" placeholder="Password">
                                <?php
                                if($errors->has('password')){
                                    echo '<span class="label label-danger">' . $errors->first('password') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('password_confirmation')) ? 'has-error' : '' }}" for="password_confirmation">
                                <label for="password_confirmation" class="control-label">Confirm Password</label>
                                <input name="password_confirmation" value="" type="password" class="form-control" placeholder="Password again">
                                <?php
                                if($errors->has('password_confirmation')){
                                    echo '<span class="label label-danger">' . $errors->first('password_confirmation') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ $errors->has('remark') ? 'has-error' : '' }}" for="remark">
                                <label class="control-label" for="remark">Remark</label><span class="badge" data-toggle="modal" data-target="#remarkModal">?</span>
                                <input name="remark" value="{{ (Request::old('remark')) }}" type="text" class="form-control" placeholder="Remark">
                                <?php
                                if($errors->has('remark')){
                                    echo '<span class="label label-danger">' . $errors->first('remark') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="remarkModal" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel"></h4>
                                        </div>
                                        <div class="strip">
                                            <p>Your remarks about a group member are displayed in the group lists, and also exported in generated squad XML files.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <input class="btn btn-yellow" type="submit" value="Add member">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Import Squad XML</h3>
                    </div>

                    <form action="{{ URL::to('admin/clan/importsquad') }}/{{ $clan->id }}" method="post">
                        {{ Form::token() }}

                        <div class="strip">
                            <p>Insert your squad XML file URL and we will create your group members automatically.</p>
                        </div>

                        <div class="panel-body">

                            <div class="form-group {{ ($errors->has('squadURL')) ? 'has-error' : '' }}" for="name">
                                <label class="control-label" for="name">Squad XML URL</label>
                                <input name="squadURL" type="text" class="form-control" placeholder="Squad XML URL">
                                <?php
                                if($errors->has('squadURL')){
                                    echo '<span class="label label-danger">' . $errors->first('squadURL') . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <input class="btn btn-yellow" type="submit" value="Import Squad XML File">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Export Squad XML</h3>
                    </div>

                    <form action="{{ URL::to('admin/clan/exportsquad') }}/{{ $clan->id }}" method="post">
                        {{ Form::token() }}

                        <div class="strip">
                            <p>Create squad XML based on the current group settings.</p>
                        </div>

                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <input class="btn btn-yellow" type="submit" value="Export Squad XML File">
                            </div>
                        </div>
                    </form>
                </div>

            </div>

            <div class="col-md-4">

                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Change Avatar</h3>
                    </div>

                    <form action="{{ URL::to('admin/clan/changeavatar') }}/{{ $clan->id }}" method="post" enctype="multipart/form-data">
                        {{ Form::token() }}

                        <div class="strip">
                            <p>Ensure your image is square to avoid distortion.</p>
                        </div>

                        <div class="panel-body avatars">
                            <p>Large (300px x 300px)</p>
                            <img src="<?= $clan->avatar->url('medium') ?>" ><br/><br/>
                            <p>Medium (100px x 100px)</p>
                            <img src="<?= $clan->avatar->url('thumb') ?>" ><br/><br/>
                            <p>Small (40px x 40px)</p>
                            <img src="<?= $clan->avatar->url('tiny') ?>" ><br/><br/>
                            <input type="file" id="avatar_upload" name="avatar" />
                        </div>
                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <input class="btn btn-yellow" type="submit" value="Change Avatar">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Delete Group</h3>
                    </div>

                    <form action="{{ URL::to('admin/clan/delete') }}/{{ $clan->id }}" method="post">
                        {{ Form::token() }}

                        <div class="strip">
                            <p>Delete this group and remove all members.</p>
                        </div>

                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <button class="btn btn-red action_confirm" href="{{ URL::to('admin/clan/delete') }}/{{ $clan->id}}" data-token="{{ Session::getToken() }}" data-method="post">Delete Group</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

@stop
