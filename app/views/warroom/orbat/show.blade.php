@extends('warroom.layouts.personnel')
                
{{-- Content --}}
@section('content')

<script type="text/javascript">

	var clanTotals = {{$clanTotals}};
	var clanPlayerTotals = {{$clanPlayerTotals}};
	var clanLastOp = {{$clanLastOp}};
	
	$(document).ready(function() {
		$('#clantp').append(Math.round(clanTotals.CombatHours/60*10)/10 + " hrs");
		$('#clanops').append(clanTotals.Operations);
		$('#clankills').append(clanPlayerTotals.Kills);
		$('#clandeaths').append(clanPlayerTotals.Deaths);
		$('#clankd').append(Math.round(clanPlayerTotals.Kills/clanPlayerTotals.Deaths*10)/10);
		$('#clanshots').append(clanPlayerTotals.ShotsFired);
		$('#clanvehicletime').append(Math.round(clanPlayerTotals.VehicleTime/60*10)/10 + " hrs");
		$('#clangunnery').append(clanPlayerTotals.VehicleKills);
		$('#clanflighttime').append(Math.round(clanPlayerTotals.PilotTime/60*10)/10 + " hrs");
		$('#clandives').append(clanPlayerTotals.CombatDives);
		$('#clanjumps').append(clanPlayerTotals.ParaJumps);
		$('#clanmedic').append(clanPlayerTotals.Heals + " hrs");
		
		$('#lastop').append(clanLastOp.Operation);
		$('#lastmap').append(clanLastOp.Map);
		$('#lastactive').append(parseArmaDate(clanLastOp.date));
	});

</script>


<div class="warroom-profile">

    <div class="container">

        <div class="row">

            <div class="col-md-4">
                <div class="dark2-panel">

                    <h1>
                        [{{{ $clan->tag }}}]
                        {{{ $clan->name }}}&nbsp;
                        <img src="{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($clan->country) }}.png" alt="{{ $clan->country_name }}" title="{{ $clan->country_name }}"/>
						
                    </h1>

                    <hr/>

                     <h4>
                   		 
                         {{{ $clan->type }}} {{{ $clan->size }}} - {{{ $clan->title }}}
                    </h4>

                    <div class="black-panel">
                        <img src="{{ $clan->avatar->url('medium') }}" >
                    </div>
 				<h2>Most Recent Operation</h2>
				<table class="table">
                    <tbody>
                        <tr>
                            <td width="20%" id="lastmap"></td>
                            <td width="60%" id="lastop"></td>
                            <td width="20%" id="lastactive"></td>
                        </tr>
                    </tbody>
                </table>
 				<h2>Leadership</h2>
                <h4>Leader</h4>
				<table class="table">
                    <tbody>
                    <tr>
                        <td><img src="{{ $leader->avatar->url('tiny') }}" ></td>
                        <td>{{{ $leader->username }}}</td>
                        <td>Leader</td>
                        <td>{{{ $leader->remark }}}</td>
                    </tr>
                    </tbody>
                </table>
 				<h4>Officers</h4>
				<table class="table">
                    <tbody>
                    @foreach ($officers as $member)
                    <tr>
                        <td><img src="{{ $member->avatar->url('tiny') }}" ></td>
                        <td>{{{ $member->username }}}</td>
                        <td>Officer</td>                      
                        <td>{{{ $member->remark }}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
             
   
                </div>
            </div>

            <div class="col-md-4">
                <div class="dark2-panel">
                 <h1>Overview</h1>
				<hr/>
                    <h3><span id="clantp"></span> of combat</h3>
                    <h3><span id="clanops"></span> operations conducted</h3>
                    <h3><span id="clankills"></span> confirmed kills</h3>
                    <h3><span id="clandeaths"></span> critical injuries / deaths</h3>
                    <h3><span id="clankd"></span> kill / death ratio</h3>
                    <h3><span id="clanshots"></span> shots fired</h3>
  
                   <h2>Combat Experience</h2>

                    <table class="table">
                        <tr>
                            <td width="60%">Vehicle Usage:</td>
                            <td width="40%" id="clanvehicletime"></td>
                        </tr>
                        <tr>
                            <td>Mounted Weapon Kills:</td>
                            <td id="clangunnery"></td>
                        </tr>
                        <tr>
                            <td>Flight hours:</td>
                            <td id="clanflighttime"></td>
                        </tr>
                        <tr>
                            <td>Combat Dives:</td>
                            <td id="clandives"></td>
                        </tr>
                        <tr>
                            <td>Para Jumps:</td>
                            <td id="clanjumps"></td>
                        </tr>
                        <tr>
                            <td>Medical Support:</td>
                            <td id="clanmedic"></td>
                        </tr>
                    </table>
                    
            @if ((!is_null($clan->twitch_stream) && !$clan->twitch_stream=='') || (!is_null($clan->website) && !$clan->website=='') || (!is_null($clan->teamspeak) && !$clan->teamspeak==''))
                    <h2>Communications</h2>
                    <table class="table">

                        @if (!is_null($clan->twitch_stream) && !$clan->twitch_stream=='')
                        <tr>
                            <td width="40%">Twitch Stream</td>
                            <td width="60%"><a target="_blank" href="{{{ $clan->twitch_stream }}}">{{{ $clan->twitch_stream }}}</a></td>
                        </tr>
                        @endif
                        @if (!is_null($clan->website) && !$clan->website=='')
                        <tr>
                            <td width="40%">Web Site</td>
                            <td width="60%"><a target="_blank" href="{{{ $clan->website }}}">{{{ $clan->website }}}</a></td>
                        </tr>
                        @endif
                        @if (!is_null($clan->teamspeak) && !$clan->teamspeak=='')
                        <tr>
                            <td width="40%">TeamSpeak</td>
                            <td width="60%"><a target="_blank" href="{{{ $clan->teamspeak }}}">{{{ $clan->teamspeak }}}</a></td>
                        </tr>
                        @endif
                    </table>
                  @endif
                </div>

            </div>

            <div class="col-md-4">
                <div class="dark2-panel">
 				<h1>Units</h1>
                <h2>Deployed</h2>
				<table class="table">
                    <tbody>
                    @foreach ($soldiers as $member)
                    <tr>
                        <td><img src="{{ $member->avatar->url('tiny') }}" ></td>
                        <td>{{{ $member->username }}}</td>
                        <td>Rank</td>                      
                        <td>{{{ $member->remark }}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                    <h1>Battle Feed</h1>
                    <hr/>

                    <div id="personnel_livefeed">
                        @include('warroom/tables/clan_feed')
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
                <h1>Vehicle Usage</h1>
                <hr/>
  				@include('warroom/tables/orbat_veh')       
  			</div>
			<div class="col-md-4">
                <h1>Weapons Usage</h1>
                <hr/>
                @include('warroom/tables/orbat_weap') 
			</div>
			<div class="col-md-4">
                <h1>Units Deployed</h1>
                <hr/>
                @include('warroom/tables/orbat_classes') 
			</div>

        </div>
        
        <div class="row">
        
            <div class="col-md-4">
                <h1>Mounted Weapons</h1>
                <hr/>
  				@include('warroom/tables/orbat_mwk')
  			</div>
			<div class="col-md-4">
                <h1>Weapons Effectiveness</h1>
                <hr/>
                @include('warroom/tables/orbat_weap_kills') 
			</div>
			<div class="col-md-4">
                <h1>Unit Effectiveness</h1>
                <hr/>
                @include('warroom/tables/orbat_player_kills') 
			</div>
            
        </div><br />
        
    </div>
</div>

@stop
