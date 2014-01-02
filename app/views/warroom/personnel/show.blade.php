@extends('warroom.layouts.default')
                
{{-- Content --}}
@section('content')

<script type="text/javascript">
               
	var playerDetails = {{$playerdata['Details']}};
	var playerTotals = {{$playerdata['Totals']}};
	
	$(document).ready(function() {
		if (playerDetails.PlayerRank){
			$('#playername').prepend(playerDetails.PlayerRank);
		}
		$('#playerclass').append(playerDetails.PlayerClass);
		$('#playerlastclass').append(playerDetails.PlayerClass);
		$('#playerclassicon').append("<img src='{{ URL::to('/') }}/img/classes/large/Arma3_CfgVehicles_" + playerDetails.PlayerType + ".png' alt='" + playerDetails.PlayerClass +"'/>");
		
		$('#playertp').append(playerTotals.CombatHours/60 + " hrs");
		$('#playerops').append(playerTotals.Operations);
		$('#playerkills').append(playerTotals.Kills);
		$('#playerdeaths').append(playerTotals.Deaths);
		$('#playerkd').append(Math.round(playerTotals.Kills/playerTotals.Deaths*10)/10);
		$('#playershots').append(playerTotals.ShotsFired);
		$('#playervehicletime').append(playerTotals.VehicleTime + " mins");
		$('#playergunnery').append(playerTotals.VehicleKills);
		$('#playerflighttime').append(playerTotals.PilotTime/60 + " hrs");
		$('#playerdives').append(playerTotals.CombatDives);
		$('#playerdivetime').append(playerTotals.DiveTime + " mins");
		$('#playerjumps').append(playerTotals.ParaJumps);
		$('#playermedic').append(playerTotals.Heals + " hrs");
		$('#playerlastop').append(playerDetails.Operation);
		$('#playerlastactive').append(parseArmaDate(playerDetails.date));

				
	});

</script>

<div class="white-panel">
    <div class="container">
        <div class="row">

            <div class="col-md-12">
                <br/><br/>
                @include('alerts/alerts')
            </div>

        </div>
        <div class="row">

            <div class="col-md-4"><h2>
                <div id="playername">
                    {{{ $profile->username }}} <img src="{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($profile->country) }}.png" alt="{{ $profile->country_name }}" title="{{ $profile->country_name }}"/>
                </div></h2>

                <img src="{{ $profile->avatar->url('medium') }}" >
                
                @if($profile->clan_id > 0)
				<table class="table">
                	<tr>
                    	<td>

                            	<h2>{{{ $clan->name }}}</h2>
                                <?php

								$userIsOfficer = $user->inGroup($auth['officerGroup']);
								$userIsLeader = $user->inGroup($auth['leaderGroup']);
			
								?><h4>
								@if ($userIsLeader)
								Position: Group Leader
								@elseif ($userIsOfficer)
								Position: Group Officer
								@else
								Group Member
								@endif
								</h4>

                        </td>
                        <td><h2><button class="btn btn-yellow" onClick="location.href='{{ URL::to('admin/clan/show') }}/{{ $clan->id }}'">Group details</button></h2>
                		</td>
                    </tr>
                </table>
                @endif
                
                <h2>Latest</h2><h4>
                <div id="playerlastclass">Last Role: </div>
                <div id="playerlastop">Last Operation: </div>
                <div id="playerlastactive">Last Active: </div>
                </h4>
                
                <h2>Combat Experience</h2>
                <h4>
                <div id="playervehicletime">Vehicle Experience: </div>
                <div id="playergunnery">Gunnery Kills: </div>
                <div id="playerflighttime">Flight hours: </div>
                <div id="playerdives">Combat Dives: </div>
                <div id="playerdivetime">Dive Time: </div>
				<div id="playerjumps">Para Jumps: </div>
                <div id="playermedic">Medical Experience: </div>
                

                <table class="table">
                 
                    @if (!is_null($profile->twitch_stream) && !$profile->twitch_stream=='')
                    <tr>
                        <td>Twitch Stream</td>
                        <td><a target="_blank" href="{{{ $profile->twitch_stream }}}">{{{ $profile->twitch_stream }}}</a></td>
                    </tr>
                    @endif

                </table>

            </div>

            <div class="col-md-4">
                <h2><div id="playerclass"></div></h2>
                <div id="playerclassicon"></div>
                <h2><div id="playerweap">Favoured Weapon </div></h2>
                <div id="playerweapicon"></div>
                <h2><div id="playerveh">Favoured Vehicle </div></h2>
                <div id="playervehicon"></div>
            </div>
            
            <div class="col-md-4">
                
                 <table class="table">
                
                    <tr>
                        <td><h3>Time Played</h3><h2><div id="playertp"></div></h2></td>
						<td><h3>Operations Conducted</h3><h2><div id="playerops"></div></h2></td>
                        <td><h3>Confirmed Kills</h3><h2><div id="playerkills"></div></h2></td>
                    </tr>
                    <tr>
                        <td><h3>Serious Injury/Death</h3><h2><div id="playerdeaths"></div></h2></td>
						<td><h3>Kill/Death Ratio</h3><h2><div id="playerkd"></div></h2></td>
                        <td><h3>Shots Fired</h3><h2><div id="playershots"></div></h2></td>
                    </tr>
                </table>
                

                
				<h2>
                    Battle Feed
                </h2>
                <div id="personnel_livefeed">
		    		@include('warroom/tables/player_feed')
				</div>
            </div>

        </div>
        <div class="row">

            <div class="col-md-4">

                <h2>
                    Field Experience
                </h2>

		    	@include('warroom/tables/playerclass')


            </div>

            <div class="col-md-4">
                 <h2>
                    Vehicle Experience
                </h2>
                
                @include('warroom/tables/vehiclexp')
            </div>
            
            <div class="col-md-4">
                 <h2>
                    Weapons Experience
                </h2>
 				@include('warroom/tables/weapons')
            </div>

        </div>
    </div>
</div>

@stop
