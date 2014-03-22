<div id="warroom_livefeed">
    <div class="strip clearfix"><span id="warroom_livefeed_toggle"><i class="fa fa-arrow-right"></i></span><span id="warroom_livefeed_label" class="control">Live event feed</span></div>
    @include('warroom/tables/live_feed')
</div>


<div id="warroom_notice">
    <div class="strip"><span class="control-right" id="warroom_notice_close"><i class="fa fa-times"></i></span><span id="warroom_livefeed_label" class="control">Notice</span></div>
    <h2>Squad XML Import and spaces</h2>
    <p>All email addresses containing spaces have had the spaces removed in the DB. Most likely due to a bug in squad XML import, if your group members are having problems logging in, please try without spaces.</p>
    <h2>Server Port stripped from IP's</h2>
    <p>All ports from server IP's have been stripped, please do not include the port number when setting your servers IP.</p>
    <h2>General Support Requests</h2>
    <p>For any support issues please join the skype public ALiVE group. If you can send a PM to a team member on the forums with your skype details we will add you in.</p>

</div>

@include('warroom/partials/_abs_footer')

</body>
</html>
