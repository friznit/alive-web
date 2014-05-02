<div id="warroom_livefeed">
    <div class="strip clearfix"><span id="warroom_livefeed_toggle"><i class="fa fa-arrow-right"></i></span><span id="warroom_livefeed_label" class="control">Live event feed</span></div>
    @include('warroom/tables/live_feed')
</div>


<div id="warroom_notice">
    <div class="strip"><span class="control-right" id="warroom_notice_close"><i class="fa fa-times"></i></span><span id="warroom_livefeed_label" class="control">Notice</span></div>
    <h2>WarRoom forum footers</h2>
    <p>Display your stats in forums and on websites! We have added the code you need to use to player profile pages.</p>
    <h2>Group Tag Bugfix</h2>
    <p>We identified an issue for group tags that contain spaces and some special characters, data should now be showing for these groups.</p>
    <h2>General Support Requests</h2>
    <p>For any support issues please join the skype public ALiVE group. If you can send a PM to a team member on the forums with your skype details we will add you in.</p>

</div>

@include('alerts/home_alerts')

@include('warroom/partials/_abs_footer')

</body>
</html>
