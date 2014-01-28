<script>
    $(document).ready(function() {
        $.getJSON('{{ URL::to('/') }}/api/clanfeed?id={{{$clan->tag}}}', function(data) {

			$.each(data.rows, function (index, row) {

                if (row.value.Event == "Kill")
                {
                    if (row.value.Death == "true")
                    {
                        $('#clan_feed')
                            .append(row.value.Map + ' - Grid:' + row.value.KilledPos + ' - ')
                            .append(row.value.gameTime + ' local<br>')
                            .append(row.value.Killedfaction + ' ' + row.value.KilledType + ' <a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="highlight">' + row.value.PlayerName + '</span></a> has been KIA')
                            .append('<br>' + parseArmaDate(row.key[1]) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')

                    } else {
                        if (row.value.KilledClass != "Infantry")
                        {
                            $('#clan_feed')
                                .append(row.value.Map + ' - Grid:' + row.value.KilledPos + ' - ')
                                .append(row.value.gameTime + ' local<br>')
                                .append(row.value.Killedfaction + ' <span class="highlight">' + row.value.KilledType + '</span> has been destroyed')
                                .append('<br>' + parseArmaDate(row.key[1]) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                        } else {
                            $('#clan_feed')
                                .append(row.value.Map + ' - Grid:' + row.value.KilledPos + ' - ')
                                .append(row.value.gameTime + ' local<br>')
                                .append(row.value.Killerfaction + ' ' + row.value.KillerType + ' <a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="highlight">(' + row.value.PlayerName + '</span></a>)')
                                .append('</span> kills ' + row.value.Killedfaction)
                                .append('<span class="highlight"> ' + row.value.KilledType)
                                .append('</span> with an ' + row.value.Weapon)
                                .append(' from ' + row.value.Distance + 'm')
                                .append('<br>' + parseArmaDate(row.key[1]) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                        }
                    }

                }

                if (row.value.Event == "PlayerStart")
                {
                    $('#clan_feed')
                        .append(row.value.Map + ' - ')
                        .append(row.value.gameTime + ' local<br>')
                        .append('<a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="highlight"> ' + row.value.PlayerName + '</span></a> is participating in Operation <span class="highlight2">' + row.value.Operation + '</span>.')
                        .append('<br>' + parseArmaDate(row.key[1]) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                }

                if (row.value.Event == "PlayerFinish")
                {
                    $('#clan_feed')
                        .append(row.value.Map + ' - ')
                        .append(row.value.gameTime + ' local<br>')
                        .append('<a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="highlight"> ' + row.value.PlayerName + '</span></a> has left Operation <span class="highlight2">' + row.value.Operation + '</span> after ' + row.value.timePlayed + ' minutes.')
                        .append('<br>' + parseArmaDate(row.key[1]) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                }

                if (row.value.Event == "Hit" && !(row.value.PlayerHit))
                {
                    $('#clan_feed')
                        .append(row.value.Map + ' - Grid:' + row.value.hitPos + ' - ')
                        .append(row.value.gameTime + ' local<br>')
                        .append(row.value.sourcefaction + ' ' + row.value.sourceType + ' <a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="highlight">' + row.value.PlayerName + '</span></a> has scored a hit on a ' + row.value.hitfaction + ' ' + row.value.hitType + '.')
                        .append('<br>' + parseArmaDate(row.key[1]) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                }

                if (row.value.Event == "Missile")
                {
                    if (row.value.FiredAt == "true")
                    {
                        $('#clan_feed')
                            .append(row.value.Map + ' - Grid:' + row.value.targetPos + ' - ')
                            .append(row.value.gameTime + ' local<br>')
                            .append(row.value.targetFaction + ' ' + row.value.targetType + ' <a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="highlight">' + row.value.PlayerName + '</span></a> has been engaged by a ' + row.value.sourceFaction + ' ' + row.value.sourceType + '.')
                            .append('<br>' + parseArmaDate(row.key[1]) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                    } else {
                        $('#clan_feed')
                            .append(row.value.Map + ' - Grid:' + row.value.sourcePos + ' - ')
                            .append(row.value.gameTime + ' local<br>')
                            .append(row.value.sourceFaction + ' ' + row.value.sourceType + ' <a href={{ URL::to("war-room/showpersonnel") }}/' + row.value.Player +'><span class="highlight">' + row.value.PlayerName + '</span></a> is engaging ' + row.value.targetFaction)
                            .append(row.value.targetType)
                            .append(' with a ' + row.value.Weapon)
                            .append(' from ' + row.value.Distance + 'm using a ' + row.value.projectile)
                            .append('<br>' + parseArmaDate(row.key[1]) + ' - <span class="operation">Operation ' + row.value.Operation +'</span><hr>')
                    }

                }

            });

            $("#clan_feed_container").mCustomScrollbar("update");
        });
    });
</script>

<div id="live_feed_container" style="max-height:380px">
    <div id="clan_feed">

    </div>
</div>