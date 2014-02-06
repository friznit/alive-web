<script>

    $(document).ready(function(){

        $('#breakdown').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/opsbreakdown',
            "sAjaxDataProp": "rows",
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bAutoWidth": true,
            "aaSorting": [[ 4, "desc" ]],
            "bProcessing" : true,
            "aoColumnDefs": [
                { "mData": "key", "aTargets": [ 0 ],
				  "mRender" : function (data, type) {
						return "<a href={{ URL::to('war-room') }}/showoperation?name=" + encodeURIComponent(data[2]) +"&map=" + encodeURIComponent(data[0]) + "&clan=" + encodeURIComponent(data[1]) + ">" +  data[2] + " (" + data[1] +")</a>";}
				},
                { "mData": "key.0", "aTargets": [ 1 ] },
                { "mData": "value.Operations", "aTargets": [ 2 ] },
                { "mData": "value.CombatHours", "aTargets": [ 3 ] },
                { "mData": "value.Kills", "aTargets": [ 4 ] },
                { "mData": "value.Injured", "aTargets": [ 5 ] },
                { "mData": "value.Deaths", "aTargets": [ 6 ] },
                { "mData": "value.ShotsFired", "aTargets": [ 7 ] },
                { "mData": "value.CombatDives", "aTargets": [ 8 ] },
                { "mData": "value.ParaJumps", "aTargets": [ 9 ] },
                { "mData": "value.Heals", "aTargets": [ 10 ] },
                { "mData": "value.VehicleTime", "aTargets": [ 11 ] },
                { "mData": "value.VehicleKills", "aTargets": [ 12 ] },
                { "mData": "value.PilotTime", "aTargets": [ 13 ] }
            ]
        });
    });

</script>

<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="breakdown">
    <thead>
    <tr>
        <th width="20%">Operation</th>
        <th>AO</th>
        <th>Sessions</th>
        <th>Minutes Played</th>
        <th>Kills</th>
        <th>Injuries</th>
        <th>Deaths</th>
        <th>Ammo</th>
        <th>Combat Dives</th>
        <th>Para. Jumps</th>
        <th>Medical Support</th>
        <th>Vehicle Time</th>
        <th>Gunnery Kills</th>
        <th>Flight Time</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>