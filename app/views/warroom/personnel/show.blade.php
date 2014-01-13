@extends('warroom.layouts.personnel')
                
{{-- Content --}}
@section('content')

<script type="text/javascript">
               
	var playerDetails = {{$playerdata['Details']}};
	var playerTotals = {{$playerdata['Totals']}};
	var playerVehicle = {{$playerdata['Vehicle']}};
	var playerWeapon = {{$playerdata['Weapon']}};
	var playerClass = {{$playerdata['Class']}};

	
	$(document).ready(function() {
		if (playerDetails.PlayerRank){
			$('#playername').prepend(playerDetails.PlayerRank);
		}
		$('#playerclass').append(playerClass[2]);
		$('#playerlastclass').append(playerDetails.PlayerClass);
		$('#playerclassicon').append("<img src='{{ URL::to('/') }}/img/classes/large/Arma3_CfgVehicles_" + playerClass[1] + ".png' alt='" + playerClass[2] +"'/>");
		
		$('#playertp').append(Math.round(playerTotals.CombatHours/60*10)/10 + " hrs");
		$('#playerops').append(playerTotals.Operations);
		$('#playerkills').append(playerTotals.Kills);
		$('#playerdeaths').append(playerTotals.Deaths);
		$('#playerkd').append(Math.round(playerTotals.Kills/playerTotals.Deaths*10)/10);
		$('#playershots').append(playerTotals.ShotsFired);
		$('#playervehicletime').append(Math.round(playerTotals.VehicleTime/60*10)/10 + " hrs");
		$('#playergunnery').append(playerTotals.VehicleKills);
		$('#playerflighttime').append(Math.round(playerTotals.PilotTime/60*10)/10 + " hrs");
		$('#playerdives').append(playerTotals.CombatDives);
		$('#playerdivetime').append(playerTotals.DiveTime + " mins");
		$('#playerjumps').append(playerTotals.ParaJumps);
		$('#playermedic').append(playerTotals.Heals + " hrs");
		$('#playerlastop').append(playerDetails.Operation);
		$('#playerlastactive').append(parseArmaDate(playerDetails.date));

		if (playerWeapon) {
			$('#playerweap').append(playerWeapon[2]);
			$('#playerweapicon').append("<img src='{{ URL::to('/') }}/img/classes/large/Arma3_CfgWeapons_" + playerWeapon[1] + ".png' alt='" + playerWeapon[2] +"'/>");
		} else {
			$('#playerweap').append("None");
		}
		
		
		if (playerVehicle) {
			$('#playerveh').append(playerVehicle[2]);
			$('#playervehicon').append("<img src='{{ URL::to('/') }}/img/classes/medium/Arma3_CfgVehicles_" + playerVehicle[1] + ".png' alt='" + playerVehicle[2] +"'/>");
		} else {
			$('#playerveh').append("None");
		}
	});

</script>

<div class="modal fade" id="classifiedModal" tabindex="-1" role="dialog" aria-labelledby="classifiedModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Unregistered Unit</h4>
            </div>
            <div class="modal-strip">
                <p>This unit has not yet registered with ALiVE War Room. Either add them manually to your group or invite them to sign up.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="warroom-profile">

    <div class="container">

        <div class="row">

            <div class="col-md-4">
                <div class="dark2-panel">

                    	@if(is_null($profile))
                    		<h1><div class="classified">CLASSIFIED <span class="badge" data-toggle="modal" data-target="#classifiedModal">!</span></div></h1>
                        @else
                        <h1>
                            @if ($clan)
                            [{{{ $clan->tag }}}]
                            @endif
                            {{{ $profile->username }}}&nbsp;
                            <img src="{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($profile->country) }}.png" alt="{{ $profile->country_name }}" title="{{ $profile->country_name }}"/>
                            </h1>
                         @endif
                    

                    <hr/>

           		 @if(!is_null($profile))
                    @if($profile->clan_id > 0)

                    <?php
                    $userIsOfficer = $user->inGroup($auth['officerGroup']);
                    $userIsLeader = $user->inGroup($auth['leaderGroup']);
                    ?>
                    <h4>
                        @if ($userIsLeader)
                        Group Leader {{{ $clan->name }}}
                        @elseif ($userIsOfficer)
                        Group Officer {{{ $clan->name }}}
                        @else
                        Group Member {{{ $clan->name }}}
                        @endif
                    </h4>

                    @endif

                        <div class="black-panel">
                            <img src="{{ $profile->avatar->url('medium') }}" >
                        </div>
				@endif
                    
                    <h2>Latest</h2>
                    <table class="table">
                        <tr>
                            <td width="40%">Last Role:</td>
                            <td width="60%" id="playerlastclass"></td>
                        </tr>
                        <tr>
                            <td>Last Operation:</td>
                            <td id="playerlastop"></td>
                        </tr>
                        <tr>
                            <td>Last Active:</td>
                            <td id="playerlastactive"></td>
                        </tr>
                    </table>

                    <h2>Aliases</h2>
					@include('warroom/tables/alias')

				@if(!is_null($profile))
                    <table class="table">

                        @if (!is_null($profile->twitch_stream) && !$profile->twitch_stream=='')
                        <tr>
                            <td width="40%">Twitch Stream</td>
                            <td width="60%"><a target="_blank" href="{{{ $profile->twitch_stream }}}">{{{ $profile->twitch_stream }}}</a></td>
                        </tr>
                        @endif

                    </table>
				@endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="dark2-panel">

                    <h1>Overview</h1>
                    <hr/>

                    <h3><span id="playertp"></span> played</h3>
                    <h3><span id="playerops"></span> operations conducted</h3>
                    <h3><span id="playerkills"></span> confirmed kills</h3>
                    <h3><span id="playerdeaths"></span> critical injuries / deaths</h3>
                    <h3><span id="playerkd"></span> kill / death ratio</h3>
                    <h3><span id="playershots"></span> shots fired</h3>

                    <h2>Combat Experience</h2>

                    <table class="table">
                        <tr>
                            <td width="40%">Vehicle Experience:</td>
                            <td width="60%" id="playervehicletime"></td>
                        </tr>
                        <tr>
                            <td>Mounted Weapon Kills:</td>
                            <td id="playergunnery"></td>
                        </tr>
                        <tr>
                            <td>Flight hours:</td>
                            <td id="playerflighttime"></td>
                        </tr>
                        <tr>
                            <td>Combat Dives:</td>
                            <td id="playerdives"></td>
                        </tr>
                        <tr>
                            <td>Dive Time:</td>
                            <td id="playerdivetime"></td>
                        </tr>
                        <tr>
                            <td>Para Jumps:</td>
                            <td id="playerjumps"></td>
                        </tr>
                        <tr>
                            <td>Medical Experience:</td>
                            <td id="playermedic"></td>
                        </tr>
                    </table>

                </div>
            </div>

            <div class="col-md-4">
                <div class="dark2-panel">

                    <h1>Battle Feed</h1>
                    <hr/>

                    <div id="personnel_livefeed">
                        @include('warroom/tables/player_feed')
                    </div>

                </div>
            </div>

        </div><br/>
    </div>
</div>

<div class="jumbotron white-panel">
    <div class="container">

        <div class="row">

            <div class="col-md-4">

                <h1><span id="playerclass"></span></h1>
                <hr/>
                <div id="playerclassicon"></div>

                <h2>Weapon</h2>
                <hr/>
                <h4><span id="playerweap"></span></h4>
                <div id="playerweapicon"></div>

                <h2>Vehicle</h2>
                <hr/>
                <h4><span id="playerveh"> </span></h4>
                <div id="playervehicon"></div>

            </div>

            <div class="col-md-8">
                <h1>Vehicle Experience</h1>
                <hr/>
                @include('warroom/tables/vehiclexp')

                <h1>Weapons Experience</h1>
                <hr/>
                @include('warroom/tables/weapons')

                <h1>Field Experience</h1>
                <hr/>
                @include('warroom/tables/playerclass')
            </div>

        </div><br/>

    </div>
</div>

@stop
