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
                    {{{ $profile->username }}}
                </h2>

                <img src="{{ $profile->avatar->url('medium') }}"  onerror="this.src='{{Gravatar::src($user->email, 300)}}';" ><br/><br/>

                <table class="table">
                    @if (!is_null($profile->country))
                    <tr>
                        <td>Country</td>
                        <td><img src="{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($profile->country) }}.png" alt="{{ $profile->country_name }}" title="{{ $profile->country_name }}"/></td>
                    </tr>
                    @endif
                    @if (!is_null($profile->twitch_stream) && !$profile->twitch_stream=='')
                    <tr>
                        <td>Twitch Stream</td>
                        <td><a target="_blank" href="{{{ $profile->twitch_stream }}}">{{{ $profile->twitch_stream }}}</a></td>
                    </tr>
                    @endif
                    <tr>
                        <td>Created</td>
                        <td>{{ $user->created_at }}</td>
                    </tr>
                    <tr>
                        <td>Updated</td>
                        <td>{{ $user->updated_at }}</td>
                    </tr>
                    <tr>
                        <td><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/user/edit') }}/{{ $user->id}}'">Edit Profile</button></td>
                        <td></td>
                    </tr>
                </table>

            </div>

            <div class="col-md-6">

                @if (!$clan)

                    <h2>Group</h2>

                    <p>You are not a member of a group</p>

                    <p>Are you the leader of a group?</p>

                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <td><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/clan/create') }}'">Create a group</button></td>
                        </tr>
                        </tbody>
                    </table>

                    <p>Do you want to join an existing group?</p>

                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <td><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/application/') }}'">Find a group to join</button></td>
                        </tr>
                        </tbody>
                    </table>

                    @if (isset($applications))

                    <h4>Open Group Applications</h4>
                    <table class="table table-hover">
                        <thead>
                            <th>Name</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                        @foreach ($applications as $application)
                        <tr>
                            <td>{{{ $application->clan->name }}}</a></td>
                            <td>
                                <button class="btn btn-default" onClick="location.href='{{ URL::to('admin/application/showapplicant') }}/{{ $application->id}}'">View</button>
                                <button class="btn btn-default action_confirm" href="{{ URL::to('admin/application/deleteapplicant') }}/{{ $application->id}}" data-token="{{ Session::getToken() }}" data-method="post">Cancel</button>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                    @endif

                @elseif ($profile->clan_id > 0)

                    <h2>{{{ $clan->name }}}</h2>
                    <img src="{{ $clan->avatar->url('thumb') }}" onerror="this.src='{{ URL::to('/') }}/avatars/thumb/clan.png';"><br/><br/>

                    <?php

                    $userIsOfficer = $user->inGroup($auth['officerGroup']);
                    $userIsLeader = $user->inGroup($auth['leaderGroup']);

                    ?>

                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            @if ($userIsLeader)
                            <td>Position</td>
                            <td>Group Leader of {{{ $clan->name }}}</td>
                            @elseif ($userIsOfficer)
                            <td>Position</td>
                            <td>Group Officer in {{{ $clan->name }}}</td>
                            @else
                            <td>Position</td>
                            <td>Group Member of {{{ $clan->name }}}</td>
                            @endif
                        </tr>
                        <tr>
                            <td><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/clan/show') }}/{{ $clan->id }}'">Group details</button></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>

                @endif

                @if (is_null($profile->remote_id) && $profile->clan_id > 0)

                <h2>Game Data</h2>

                <p>You're profile is not linked to your Game Data</p>

                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/user/connect') }}/{{ $clan->id }}'">Connect to Game Data</button></td>
                    </tr>
                    </tbody>
                </table>

                @else

                <h2>Forum Signature</h2>
                
                <img src="http://www.alivemod.com/api/sig?id={{$profile->a3_id}}" width="601" height="100" onerror="this.src='{{ URL::to('/') }}/sigs/missing.png';"/>

                <p>Apply the following code to your BI forums or other forums signature to display your WarRoom stats!</p>

                <pre><code>[IMG]http://www.alivemod.com/api/sig?id={{{$profile->a3_id}}}[/IMG]</code></pre>

                <p>If you would like to display your signature via HTML apply this HTML code</p>

                <pre><code><?php echo htmlspecialchars('<img src="http://www.alivemod.com/api/sig?id='. $profile->a3_id.'" width="601" height="100"/>');?></pre></code>

                @endif
 
                @if ($auth['isAdmin'] && $profile->clan_id > 0)

                <h2>Mod Utils</h2>

                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/user/uploadimages')}}'">Upload Mod Images</button></td>
                    </tr>
                    </tbody>
                </table>
                
                <h2>Admin Utils</h2>

                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/user/connectdebug') }}/{{ $user->id}}'">TEST CONNECTION</button></td>
                    </tr>
                    </tbody>
                </table>

                @endif

            </div>

        </div>
    </div>
</div>

@stop
