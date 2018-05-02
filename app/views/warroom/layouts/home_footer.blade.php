<div id="warroom_livefeed">
    <div class="strip clearfix"><span id="warroom_livefeed_toggle"><i class="fa fa-arrow-right"></i></span><span id="warroom_livefeed_label" class="control">Live event feed</span></div>
    @include('warroom/tables/live_feed')
</div>


<div id="warroom_notice">
    <div class="strip"><span class="control-right" id="warroom_notice_close"><i class="fa fa-times"></i></span><span id="warroom_livefeed_label" class="control">Notice</span></div>
    <h2>Announcing a partnership with ArmaHosts!</a></h2>
    <p>ALiVE War Room is now sponsored and hosted by ArmaHosts, click on the logo to check out their dedicated server offerings! Huge thanks to ArmaHosts for supporting us!<br /><a href="https://armahosts.com/"><img src="https://armahosts.com/img/logo.png" width=100%></a></p>    
	<h2>War Room Cloud Saves</h2>
    <p>We've moved ALiVE War Room to ArmaHosts, all cloud saves from before April 6th 2018 are no longer available. Apologies for any inconvenince!</p>        
    <h2>War Room forum footers</h2>
    <p>Display your stats in forums and on websites! We have added the code you need to use to your <a href="{{ URL::to('admin/user/show/') }}/{{ $auth['userId'] }}">Player Profile</a> page.</p>
    <h2>General Support Requests</h2>
    <p>For any support issues please post on the <a href="http://alivemod.com/forum/">ALiVE Forum</a>.</p>

</div>

@include('alerts/home_alerts')

@include('warroom/partials/_abs_footer')

</body>
</html>
