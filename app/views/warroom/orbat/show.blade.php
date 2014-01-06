@extends('warroom.layouts.personnel')
                
{{-- Content --}}
@section('content')

<script type="text/javascript">

	var Totals = {{$clanTotals}};
	var clanTotals = Totals[0].value;
	
	$(document).ready(function() {

		$('#clantp').append(Math.round(clanTotals.CombatHours/60*10)/10 + " hrs");
		$('#clanops').append(clanTotals.Operations);
		$('#clankills').append(clanTotals.Kills);
		$('#clandeaths').append(clanTotals.Deaths);
		$('#clankd').append(Math.round(clanTotals.Kills/clanTotals.Deaths*10)/10);
		$('#clanshots').append(clanTotals.ShotsFired);
		$('#clanvehicletime').append(Math.round(clanTotals.VehicleTime/60*10)/10 + " hrs");
		$('#clangunnery').append(clanTotals.VehicleKills);
		$('#clanflighttime').append(Math.round(clanTotals.PilotTime/60*10)/10 + " hrs");
		$('#clandives').append(clanTotals.CombatDives);
		$('#clanjumps').append(clanTotals.ParaJumps);
		$('#clanmedic').append(clanTotals.Heals + " hrs");
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

                    <h2>Leadership</h2>

                    <table class="table">

                        @if (!is_null($clan->twitch_stream) && !$clan->twitch_stream=='')
                        <tr>
                            <td width="40%">Twitch Stream</td>
                            <td width="60%"><a target="_blank" href="{{{ $profile->twitch_stream }}}">{{{ $profile->twitch_stream }}}</a></td>
                        </tr>
                        @endif

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
                            <td width="40%">Vehicle Experience:</td>
                            <td width="60%" id="clanvehicletime"></td>
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
                            <td>Medical Experience:</td>
                            <td id="clanmedic"></td>
                        </tr>
                    </table>

                </div>
            </div>

            <div class="col-md-4">
                <div class="dark2-panel">

                    <h1>Battle Feed</h1>
                    <hr/>

                    <div id="clan_livefeed">

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
			</div>
			<div class="col-md-4">
                <h1>Weapons Usage</h1>
                <hr/>
			</div>
			<div class="col-md-4">
                <h1>Units Deployed</h1>
                <hr/>
			</div>
            </div>

        </div><br/>

    </div>
</div>

@stop
