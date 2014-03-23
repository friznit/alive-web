<script>

    $(document).ready(function(){
		
		
        $('#aar').dataTable({
			"sDom": '<"top"l>rt<"bottom"p><"clear">',
            "bJQueryUI": true,
			"bFilter": false,
			"bSort": false,
            "sAjaxSource": '{{ URL::to('/') }}/api/oplivefeed?name={{ $name }}&clan={{ $clan->tag }}&map={{ $ao->configName }}',
            "sAjaxDataProp": "rows",
            "bPaginate": true,		
            "aoColumnDefs": [
                { "mData": "value", 
				  "aTargets": [ 0 ],
				  "mRender" : function (data, type) {
				  		var value = data;
						var action = value.Event;
						if (value.Event == "Kill")
						{
							var posx = value.KilledGeoPos[0];
							var posy = value.KilledGeoPos[1];
							var marker= L.marker(map.unproject([posy,posx], map.getMaxZoom())).addTo(map);
							if (value.Death == "true")
								{
									action = value.Map + ' - Grid:' + value.KilledPos + ' - ' + value.gameTime + ' local<br>' + value.Killedfaction + ' ' + value.KilledType + '<a href={{ URL::to("war-room/showpersonnel") }}/' + value.Player +'><span class="highlight"> ' + value.PlayerName + '</span></a> has been KIA';
			
								} else {
									if (value.KilledClass != "Infantry")
									{
										action = value.Map + ' - Grid:' + value.KilledPos + ' - ' + value.gameTime + ' local<br>' + value.Killedfaction + ' <span class="highlight">' + value.KilledType + '</span> has been destroyed';
									} else {
										action = value.Map + ' - Grid:' + value.KilledPos + ' - ' + value.gameTime + ' local<br>' + value.Killerfaction + ' ' + value.KillerType + '(<a href={{ URL::to("war-room/showpersonnel") }}/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) </span> kills ' + value.Killedfaction + '<span class="highlight"> ' + value.KilledType + '</span> with an ' + value.Weapon + ' from ' + value.Distance + 'm';
									}
								}
							var popup = L.popup().setContent('<div class="admin-panel">' + action + '</div>');
			           		marker.bindPopup(popup, {
								showOnMouseOver: true,
								offset: new L.Point(0, 0)
							});

						}
			
						if (value.Event == "OperationStart")
						{
							action = value.Map + ' - ' + value.gameTime + ' local<br>Operation <span class="highlight2">' + value.Operation + '</span> has been launched.';
						}
		
						if (value.Event == "OperationFinish")
						{
							action = value.Map + ' - ' + value.gameTime + ' local<br>Operation <span class="highlight2">' + value.Operation + '</span> has ended after ' + value.timePlayed + ' minutes.';
						}
		
						if (value.Event == "Hit" && !(value.PlayerHit))
						{
							action = value.Map + ' - Grid:' + value.hitPos + ' - ' + value.gameTime + ' local<br>' + value.sourcefaction + ' ' + value.sourceType + '(<a href={{ URL::to("war-room/showpersonnel") }}/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) has scored a hit on a ' + value.hitfaction + ' ' + value.hitType + '.';
						}
		
						if (value.Event == "Missile")
						{
							if (value.FiredAt == "true")
							{
								action = value.Map + ' - Grid:' + value.targetPos + ' - ' + value.gameTime + ' local<br>' + value.targetFaction + ' ' + value.targetType + '(<a href={{ URL::to("war-room/showpersonnel") }}/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) has been engaged by a ' + value.sourceFaction + ' ' + value.sourceType + '.';
							} else {
								action = value.Map + ' - Grid:' + value.sourcePos + ' - ' + value.gameTime + ' local<br>' + value.sourceFaction + ' ' + value.sourceType + '(<a href={{ URL::to("war-room/showpersonnel") }}/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>)</span><b> is engaging ' + value.targetFaction + value.targetType + ' with a ' + value.Weapon + ' from ' + value.Distance + 'm using a ' + value.projectile;
							}
		
						}

						return action;}
				}
			]
        });
    });

</script>

<div id="live_feed_container">
<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="aar">
    <thead>
    <tr>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>