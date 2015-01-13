<div id="warroom_livefeed">
    <div class="strip clearfix"><span id="warroom_livefeed_toggle"><i class="fa fa-arrow-right"></i></span><span id="warroom_livefeed_label" class="control">Live event feed</span></div>
    @include('warroom/tables/live_feed')
</div>


<div id="warroom_notice">
    <div class="strip"><span class="control-right" id="warroom_notice_close"><i class="fa fa-times"></i></span><span id="warroom_livefeed_label" class="control">Notice</span></div>
	<h2>War Room Upgrade</h2>
    <p>NEW! We've catalogued thousands of weapons and vehicles from popular mods. This pimps your <a href="{{ URL::to('war-room/showpersonnel') }}/{{ $auth['profile']->a3_id }}">Stats Page</a>. Check it out today!</p>        
    <h2>MANW ALiVE!</h2>
    <p><a href="http://makearmanotwar.com/entry/0MI2rqQ5aQ#.VCx96hawQ6U">Click here to support us</a> in the Make Arma Not War Contest</p>    
    <h2>War Room forum footers</h2>
    <p>Display your stats in forums and on websites! We have added the code you need to use to player profile pages.</p>
    <h2>General Support Requests</h2>
    <p>For any support issues please join the skype public ALiVE group. If you can send a PM to a team member on the forums with your skype details we will add you in.</p>

</div>

@include('alerts/home_alerts')

@include('warroom/partials/_abs_footer')

</body>
</html>
