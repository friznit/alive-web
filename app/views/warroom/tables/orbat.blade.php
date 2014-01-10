<script>

	var clanNames = {{$clanServers}};
	
    $(document).ready(function(){
        $('#orbat').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": 'http://msostore.iriscouch.com/events/_design/groupTable/_view/groupTotals?group_level=1&callback=?',
            "sAjaxDataProp": "rows",
            "bPaginate": true,
			"aaSorting": [[1, "desc" ]],		
            "aoColumnDefs": [
                { "mData": "key", 
				  "aTargets": [ 0 ],
				  "mRender" : function (data, type) {
					  			var clan = $.grep(clanNames, function(e){ return e.IP == data; });
								if (clan.length == 0) {
									return "CLASSIFIED";
								} else {
									return "<a href={{ URL::to('war-room/showorbat') }}/" + clan[0].id + ">" +  clan[0].Name + "</a>";
								}
				  }
				},
                { "mData": "value.Operations", "aTargets": [ 1 ] },
                { "mData": "value.CombatHours", "aTargets": [ 2 ],
							"mRender" : function (data, type) {
				  				return Math.round(data/60*10)/10;} },
                { "mData": "value.Kills", "aTargets": [ 3 ] },
                { "mData": "value.Injured", "aTargets": [ 4 ] },
                { "mData": "value.Deaths", "aTargets": [ 5 ] },
                { "mData": "value.ShotsFired", "aTargets": [ 6 ] },
                { "mData": "value.CombatDives", "aTargets": [ 7 ] },
                { "mData": "value.ParaJumps", "aTargets": [ 8 ] },
                { "mData": "value.Heals", "aTargets": [ 9 ] },
                { "mData": "value.VehicleTime", "aTargets": [ 10 ],
							"mRender" : function (data, type) {
				  				return Math.round(data/60*10)/10;} },
                { "mData": "value.VehicleKills", "aTargets": [ 11 ] },
                { "mData": "value.PilotTime", "aTargets": [ 12 ],
							"mRender" : function (data, type) {
				  				return Math.round(data/60*10)/10;} }
            ]
        } );
    });

</script>

<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="orbat">
    <thead>
    <tr>
        <th width="15%">Group</th>
        <th>Ops</th>
        <th>Operational Hrs</th>
        <th>Kills</th>
        <th>Injuries</th>
        <th>Deaths</th>
        <th>Ammo Used</th>
        <th>Combat Dives</th>
        <th>Para. Jumps</th>
        <th>Medical Support</th>
        <th>Vehicle Hrs</th>
        <th>Mounted Weapon Kills</th>
        <th>Flight Hrs</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>