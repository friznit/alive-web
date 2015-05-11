<div id="warroom_livefeed">
    <div class="strip clearfix"><span id="warroom_livefeed_toggle"><i class="fa fa-arrow-right"></i></span><span id="warroom_livefeed_label" class="control">Live event feed</span></div>
    @include('warroom/tables/live_feed')
</div>


<div id="warroom_notice">
    <div class="strip"><span class="control-right" id="warroom_notice_close"><i class="fa fa-times"></i></span><span id="warroom_livefeed_label" class="control">Notice</span></div>
    <h2>We're running low on server funds! <a href="http://alivemod.com/#Donate">Donate now!</a></h2>
    <p>It's easy to donate via Pay Pal or Bitcoin. Click <a href="http://alivemod.com/#Donate">here</a> to donate.</p>    
	<h2>War Room Upgrade</h2>
    <p>NEW! We've catalogued thousands of weapons and vehicles from popular mods. This pimps your <a href="{{ URL::to('war-room/showpersonnel') }}/{{ $auth['profile']->a3_id }}">Statistics Page</a>. Check it out today!</p>        
    <h2>War Room forum footers</h2>
    <p>Display your stats in forums and on websites! We have added the code you need to use to your <a href="{{ URL::to('admin/user/show/') }}/{{ $auth['userId'] }}">Player Profile</a> page.</p>
    <h2>General Support Requests</h2>
    <p>For any support issues please post on the <a href="http://alivemod.com/forum/">ALiVE Forum</a>.</p>

</div>

@include('alerts/home_alerts')

@include('warroom/partials/_abs_footer')

</body>
</html>
