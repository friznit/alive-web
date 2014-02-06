<script>
    $(document).ready(function() {

        $.getJSON('{{ URL::to('/') }}/api/oplivefeed?name={{ $name }}&clan={{ $clan->tag }}&map={{ $ao->configName }}', function(data) {

            $.each(data.rows, function (index, row) {

                if (row.value.Event == "Kill")
                {
                    if (row.value.Death == "true")
                    {
                        $('#live_feed')
                            .append(row.value.Map + ' - Grid:' + row.value.KilledPos + ' - ')
                            .append(row.value.gameTime + ' local<br>')
                            .append(row.value.Killedfaction + ' ' + row.value.KilledType + '<a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="highlight"> ' + row.value.PlayerName + '</span></a> has been KIA')
                            .append('<br>' + parseArmaDate(row.key) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')

                    } else {
                        if (row.value.KilledClass != "Infantry")
                        {
                            $('#live_feed')
                                .append(row.value.Map + ' - Grid:' + row.value.KilledPos + ' - ')
                                .append(row.value.gameTime + ' local<br>')
                                .append(row.value.Killedfaction + ' <span class="highlight">' + row.value.KilledType + '</span> has been destroyed')
                                .append('<br>' + parseArmaDate(row.key) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                        } else {
                            $('#live_feed')
                                .append(row.value.Map + ' - Grid:' + row.value.KilledPos + ' - ')
                                .append(row.value.gameTime + ' local<br>')
                                .append(row.value.Killerfaction + ' ' + row.value.KillerType + '(<a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="operation">' + row.value.PlayerName + '</span></a>)')
                                .append('</span> kills ' + row.value.Killedfaction)
                                .append('<span class="highlight"> ' + row.value.KilledType)
                                .append('</span> with an ' + row.value.Weapon)
                                .append(' from ' + row.value.Distance + 'm')
                                .append('<br>' + parseArmaDate(row.key) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                        }
                    }

                }

                if (row.value.Event == "OperationStart")
                {
                    $('#live_feed')
                        .append(row.value.Map + ' - ')
                        .append(row.value.gameTime + ' local<br>')
                        .append('Operation <span class="highlight2">' + row.value.Operation + '</span> has been launched.')
                        .append('<br>' + parseArmaDate(row.key) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                }

                if (row.value.Event == "OperationFinish")
                {
                    $('#live_feed')
                        .append(row.value.Map + ' - ')
                        .append(row.value.gameTime + ' local<br>')
                        .append('Operation <span class="highlight2">' + row.value.Operation + '</span> has ended after ' + row.value.timePlayed + ' minutes.')
                        .append('<br>' + parseArmaDate(row.key) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                }

                if (row.value.Event == "Hit" && !(row.value.PlayerHit))
                {
                    $('#live_feed')
                        .append(row.value.Map + ' - Grid:' + row.value.hitPos + ' - ')
                        .append(row.value.gameTime + ' local<br>')
                        .append(row.value.sourcefaction + ' ' + row.value.sourceType + '(<a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="operation">' + row.value.PlayerName + '</span></a>) has scored a hit on a ' + row.value.hitfaction + ' ' + row.value.hitType + '.')
                        .append('<br>' + parseArmaDate(row.key) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                }

                if (row.value.Event == "Missile")
                {
                    if (row.value.FiredAt == "true")
                    {
                        $('#live_feed')
                            .append(row.value.Map + ' - Grid:' + row.value.targetPos + ' - ')
                            .append(row.value.gameTime + ' local<br>')
                            .append(row.value.targetFaction + ' ' + row.value.targetType + '(<a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="operation">' + row.value.PlayerName + '</span></a>) has been engaged by a ' + row.value.sourceFaction + ' ' + row.value.sourceType + '.')
                            .append('<br>' + parseArmaDate(row.key) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                    } else {
                        $('#live_feed')
                            .append(row.value.Map + ' - Grid:' + row.value.sourcePos + ' - ')
                            .append(row.value.gameTime + ' local<br>')
                            .append(row.value.sourceFaction + ' ' + row.value.sourceType + '(<a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="operation">' + row.value.PlayerName + '</span></a>)')
                            .append(')</span><b> is engaging ' + row.value.targetFaction)
                            .append(row.value.targetType)
                            .append(' with a ' + row.value.Weapon)
                            .append(' from ' + row.value.Distance + 'm using a ' + row.value.projectile)
                            .append('<br>' + parseArmaDate(row.key) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                    }

                }

            });

            $("#live_feed_container").mCustomScrollbar("update");
        });
    });
</script>

<div id="live_feed_container">
    <div id="live_feed">

    </div>
</div>