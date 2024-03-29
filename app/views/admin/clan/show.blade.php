@extends('admin.layouts.default')

{{-- Content --}}
@section('content')

<div class="admin-panel">
    <div class="container">
        <div class="row">

            <div class="col-md-12">
                <br/><br/>
                @include('alerts/alerts')
            </div>

        </div>
        <div class="row">

            <div class="col-md-4">

                <h2>
                    @if ($clan)
                    [{{{ $clan->tag }}}]
                    @endif
                    {{{ $clan->name }}}
                </h2>

                <img src="{{ $clan->avatar->url('medium') }}" onerror="this.src='{{ URL::to('/') }}/avatars/medium/clan.png';"><br/><br/>

                <table class="table">
                    @if (!is_null($clan->country))
                    <tr>
                        <td>Country</td>
                        <td><img src="{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($clan->country) }}.png" alt="{{ $clan->country_name }}" title="{{ $clan->country_name }}"/><br/></td>
                    </tr>
                    @endif
                    @if (!is_null($clan->website) && !$clan->website=="")
                    <tr>
                        <td>Website</td>
                        <td><a target="_blank" href="{{{ $clan->website }}}">{{{ $clan->website }}}</a></td>
                    </tr>
                    @endif
                    @if (!is_null($clan->twitch_stream) && !$clan->twitch_stream=="")
                    <tr>
                        <td>Twitch Stream</td>
                        <td><a target="_blank" href="{{{ $clan->twitch_stream }}}">{{{ $clan->twitch_stream }}}</a></td>
                    </tr>
                    @endif
                    @if (!is_null($clan->teamspeak) && !$clan->teamspeak=="")
                    <tr>
                        <td>Teamspeak Server</td>
                        <td>{{{ $clan->teamspeak }}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>Created</td>
                        <td>{{ $clan->created_at }}</td>
                    </tr>
                    <tr>
                        <td>Updated</td>
                        <td>{{ $clan->updated_at }}</td>
                    </tr>
                     <tr>
                        <td>Description</td>
                        <td>{{{ $clan->description }}}</td>
                    </tr>                   
                    <tr>
                        <td>
                            @if ($auth['isLeader'])
                            <button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/clan/leaderstepdown') }}/{{ $clan->id}}'">Step down as Leader</button>
                            @endif
                            @if ($auth['isOfficer'])
                            <button class="btn btn-yellow action_confirm" href="{{ URL::to('admin/clan/officerstepdown') }}/{{ $clan->id}}" data-token="{{ Session::getToken() }}" data-method="post">Step down as Officer</button>
                            @endif
                        </td>
                        <td></td>
                    </tr>
                    @if ($auth['isOfficer'] || $auth['isGrunt'])
                    <tr>
                        <td>
                            <button class="btn btn-yellow action_confirm" href="{{ URL::to('admin/clan/leave') }}/{{ $clan->id}}" data-token="{{ Session::getToken() }}" data-method="post">Leave this group</button>
                        </td>
                        <td></td>
                    </tr>
                    @endif
                    @if ($auth['isLeader'])
                    <tr>
                        <td>
                            <button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/clan/edit') }}/{{ $clan->id}}'">Group Settings</button>
                        </td>
                        <td></td>
                    </tr>
                    @endif
                </table>

            </div>

            <div class="col-md-7 col-md-offset-1">

                <h2>Members</h2>

                @if ($auth['isLeader'])
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/clan/edit') }}/{{ $clan->id}}'">Add Group members</button></td>
                    </tr>
                    </tbody>
                </table>
                @endif

                <table class="table table-hover">
                    <tbody>
                    @foreach ($members as $member)
                    <?php

                    $user = Sentry::findUserById($member->user_id);
                    $memberIsGrunt = $user->inGroup($auth['gruntGroup']);
                    $memberIsOfficer = $user->inGroup($auth['officerGroup']);
                    $memberIsLeader = $user->inGroup($auth['leaderGroup']);

                    ?>
                    <tr>
                        @if ($auth['isAdmin'])
                        <td><a href="http://alivemod.com/admin/user/show/{{ $member->user_id }}">{{ $member->user_id }}</a></td>
                        @endif
                        <td><img src="{{ $member->avatar->url('tiny') }}" onerror="this.src='{{Gravatar::src($user->email)}}';" ></td>
                        <td>{{{ $member->username }}}</td>
                        @if ($memberIsLeader)
                        <td>Leader</td>
                        @endif
                        @if ($memberIsOfficer)
                        <td>Officer</td>
                        @endif
                        @if ($memberIsGrunt)
                        <td></td>
                        @endif
                        <td>{{{ $member->remark }}}</td>
                        <td>
                            <table class="table-condensed">
                                <tr>
                                    <td>
                                    @if (($auth['isAdmin'] || $auth['isLeader'] || $auth['isOfficer']) && ($auth['userId'] != $member->user_id))
                                    <button class="btn btn-default" onClick="location.href='{{ URL::to('admin/clan/memberedit') }}/{{ $member->user_id}}'">Edit</button>
                                    <button class="btn btn-default action_confirm" href="{{ URL::to('admin/clan/removemember') }}/{{ $member->user_id}}" data-token="{{ Session::getToken() }}" data-method="post">Remove</button>
                                        @if ($memberIsGrunt)
                                        <button class="btn btn-default action_confirm" href="{{ URL::to('admin/clan/promote') }}/{{ $member->user_id}}" data-token="{{ Session::getToken() }}" data-method="post">Promote</button>
                                        @endif
                                    @endif
                                    @if (($auth['isAdmin'] || $auth['isLeader']) && ($auth['userId'] != $member->user_id))
                                        @if ($memberIsOfficer)
                                        <button class="btn btn-default action_confirm" href="{{ URL::to('admin/clan/demote') }}/{{ $member->user_id}}" data-token="{{ Session::getToken() }}" data-method="post">Demote</button>
                                        @endif
                                    @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    @if (is_null($member->remote_id))
                                    <br/><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/clan/connectmember') }}/{{ $member->user_id }}'">Connect to Game Data</button>
                                    @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>

                <?php echo $members->links(); ?>


                @if (isset($servers))

                @if ($auth['isLeader'] || $auth['isOfficer'])

                <h2>Servers</h2>

                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/server/create') }}/{{ $clan->id }}'">Setup another server</button></td>
                    </tr>
                    </tbody>
                </table>

                @endif

                @if ($auth['isLeader'] || $auth['isOfficer'])

                @endif

                <table class="table table-hover">
                    <thead>
                    <th>Name</th>
                    </thead>
                    <tbody>
                    @foreach ($servers as $server)
                    <tr>
                        <td><a href="{{ URL::to('admin/server/show') }}/{{ $server->id }}">{{{ $server->name }}}</a></td>
                        <td>
                            @if ($auth['isLeader'] || $auth['isOfficer'])
                            <button class="btn btn-default" onClick="location.href='{{ URL::to('admin/server/edit') }}/{{ $server->id }}'">Edit</button>
                            <button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/server/edit') }}/{{ $server->id }}'">Server Setup Instructions</button>
                            <button class="btn btn-default action_confirm" href="{{ URL::to('admin/server/delete') }}/{{ $server->id }}" data-token="{{ Session::getToken() }}" data-method="post">Delete</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>

                @else

                @if ($auth['isLeader'] || $auth['isOfficer'])

                @if (!is_null($clan->remote_id))

                <h2>Servers</h2>

                <p>Your group has no servers setup</p>
                <p><b>Connect your servers to the war room to start capturing live data from your sessions.</b></p>
                <p><b>Start by setting up a server below.</b></p>

                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/server/create') }}/{{ $clan->id }}'">Setup a server</button></td>
                    </tr>
                    </tbody>
                </table>

                @endif

                @endif

                @endif

                @if ($auth['isAdmin'] || $auth['isLeader'])

                @if (is_null($clan->remote_id))

                <h2>Game Data</h2>

                <div class="alert alert-danger">Warning, your group is not linked to its game data, please connect to game data below.</div>

                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td><button class="btn btn-red" onClick="location.href='{{ URL::to('admin/clan/connect') }}/{{ $clan->id }}'">Connect to Game Data</button></td>
                    </tr>
                    </tbody>
                </table>

                @endif

                @endif

                @if ($auth['isAdmin'] || $auth['isLeader'] || $auth['isOfficer'])

                @if (isset($applications))

                <h2>Applicants</h2>
                <table class="table table-hover">
                    <thead>
                    <th>Name</th>
                    <th>Age Group</th>
                    <th>Country</th>
                    <th>Actions</th>
                    </thead>
                    <tbody>
                    @foreach ($applications as $application)
                    @if (!$application->denied)
                    <tr>
                        <td>{{{ $application->user->profile->username }}}</td>
                        <td>{{{ $application->age_group }}}</td>
                        <td><?php
                            if(!is_null($application->country)){
                                ?>
                                <img src="{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($application->country) }}.png" alt="{{ $application->country_name }}" title="{{ $application->country_name }}"/>
                            <?php
                            }
                            ?></td>
                        <td>
                            <button class="btn btn-default" onClick="location.href='{{ URL::to('admin/application/showrecipient') }}/{{ $application->id}}'">View</button>
                            <button class="btn btn-default action_confirm" href="{{ URL::to('admin/application/deny') }}/{{ $application->id}}" data-token="{{ Session::getToken() }}" data-method="post">Deny</button>
                            <button class="btn btn-default action_confirm" href="{{ URL::to('admin/application/accept') }}/{{ $application->id}}" data-token="{{ Session::getToken() }}" data-method="post">Accept</button>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    </tbody>
                </table>

                @endif

                @endif


                @if ($auth['isAdmin'])

                <h2>Admin Utils</h2>

                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/clan/connectdebug') }}/{{ $clan->id}}'">TEST CONNECTION</button></td>
                    </tr>
                    </tbody>
                </table>

                @endif


            </div>

        </div>
    </div>
</div>

@stop
