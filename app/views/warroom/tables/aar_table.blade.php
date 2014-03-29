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
            {
            "mData": "value",
            "aTargets": [ 0 ],
            "mRender" : function (data, type) {
                var value = data;
                var action = value.Event;
                var output = '';

                console.log(value);
                console.log(action);

                if (action == "Kill")
                {
                    var posx = value.KilledGeoPos[0];
                    var posy = value.KilledGeoPos[1];
                    var multiplier = size / {{$ao->size}};

                    if (value.Death == "true")
                    {
                        output = value.Map + ' - Grid:' + value.KilledPos + ' - ' + value.gameTime + ' local<br>' + value.Killedfaction + ' ' + value.KilledType + '<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="highlight"> ' + value.PlayerName + '</span></a> has been KIA';
                    } else {
                        if (value.KilledClass != "Infantry")
                        {
                            output = value.Map + ' - Grid:' + value.KilledPos + ' - ' + value.gameTime + ' local<br>' + value.Killedfaction + ' <span class="highlight">' + value.KilledType + '</span> has been destroyed';
                        } else {
                            output = value.Map + ' - Grid:' + value.KilledPos + ' - ' + value.gameTime + ' local<br>' + value.Killerfaction + ' ' + value.KillerType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) </span> kills ' + value.Killedfaction + '<span class="highlight"> ' + value.KilledType + '</span> with an ' + value.Weapon + ' from ' + value.Distance + 'm';
                        }
                    }

                    var popup = L.popup().setContent('<div class="admin-panel">' + action + '</div>');

                    if (value.KilledSide == "WEST")
                    {
                        var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(westkills);
                        marker.bindPopup(popup, {
                            showOnMouseOver: true,
                            offset: new L.Point(0, 0)
                        });
                    }
                    if (value.KilledSide == "EAST")
                    {
                        var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: east_unit}).addTo(eastkills);
                        marker.bindPopup(popup, {
                            showOnMouseOver: true,
                            offset: new L.Point(0, 0)
                        });
                    }
                    if (value.KilledSide == "INDY")
                    {
                        var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: indy_unit}).addTo(indykills);
                        marker.bindPopup(popup, {
                            showOnMouseOver: true,
                            offset: new L.Point(0, 0)
                        });
                    }
                    if (value.KilledSide == "CIV")
                    {
                        var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: civ_unit}).addTo(civkills);
                        marker.bindPopup(popup, {
                            showOnMouseOver: true,
                            offset: new L.Point(0, 0)
                        });
                    }
                }

                if (action == "GetIn")
                {
                    var posx = value.unitGeoPos[0];
                    var posy = value.unitGeoPos[1];
                    var multiplier = size / {{$ao->size}};
                    output = value.Map + ' - Grid:' + value.unitPos + ' - ' + value.gameTime + ' local<br><a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="highlight"> ' + value.PlayerName + '</span></a> got in a ' + value.vehicleType;
                }

                if (action == "GetOut")
                {
                    var posx = value.unitGeoPos[0];
                    var posy = value.unitGeoPos[1];
                    var multiplier = size / {{$ao->size}};
                    output = value.Map + ' - Grid:' + value.unitPos + ' - ' + value.gameTime + ' local<br><a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="highlight"> ' + value.PlayerName + '</span></a> got out of a ' + value.vehicleType;
                }

                if (action == "OperationStart")
                {
                    output = value.Map + ' - ' + value.gameTime + ' local<br>Operation <span class="highlight2">' + value.Operation + '</span> has been launched.';
                }

                if (action == "OperationFinish")
                {
                    output = value.Map + ' - ' + value.gameTime + ' local<br>Operation <span class="highlight2">' + value.Operation + '</span> has ended after ' + value.timePlayed + ' minutes.';
                }

                if (action == "Hit" && !(value.PlayerHit))
                {
                    output = value.Map + ' - Grid:' + value.hitPos + ' - ' + value.gameTime + ' local<br>' + value.sourcefaction + ' ' + value.sourceType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) has scored a hit on a ' + value.hitfaction + ' ' + value.hitType + '.';
                }

                if (action == "Missile")
                {
                    if (value.FiredAt == "true")
                    {
                        output = value.Map + ' - Grid:' + value.targetPos + ' - ' + value.gameTime + ' local<br>' + value.targetFaction + ' ' + value.targetType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) has been engaged by a ' + value.sourceFaction + ' ' + value.sourceType + '.';
                    } else {
                        output = value.Map + ' - Grid:' + value.sourcePos + ' - ' + value.gameTime + ' local<br>' + value.sourceFaction + ' ' + value.sourceType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>)</span><b> is engaging ' + value.targetFaction + value.targetType + ' with a ' + value.Weapon + ' from ' + value.Distance + 'm using a ' + value.projectile;
                    }
                }

                console.log(output);

                return output;
            }
        }]
    });
});

</script>

<div id="live_feed_container">
<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="aar">
    <thead>
    <tr>
        <th>Event</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>