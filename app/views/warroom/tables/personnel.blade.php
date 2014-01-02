<script>

    $(document).ready(function(){
        $('#personnel').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": 'http://msostore.iriscouch.com/events/_design/playerTable/_view/playerTotals?group_level=2&callback=?',
            "sAjaxDataProp": "rows",
            "bPaginate": true,
			"aaSorting": [[1, "desc" ]],
            "fnDrawCallback": function ( oSettings ) {
                /* Need to redo the counters if filtered or sorted */
                /*
                 if ( oSettings.bSorted || oSettings.bFiltered )
                 {
                 for ( var i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++ )
                 {
                 $('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( i+1 );
                 }
                 }
                 */
            },
		
		
            "aoColumnDefs": [
                { "mData": "key", 
				  "aTargets": [ 0 ],
				  "mRender" : function (data, type) {
				  
						return "<a href={{ URL::to('war-room/showpersonnel') }}/" + data[1] + ">" +  data[0] + "</a>";}
				},
                { "mData": "value.Operations", "aTargets": [ 1 ] },
                { "mData": "value.CombatHours", "aTargets": [ 2 ] },
                { "mData": "value.Kills", "aTargets": [ 3 ] },
                { "mData": "value.Injured", "aTargets": [ 4 ] },
                { "mData": "value.Deaths", "aTargets": [ 5 ] },
                { "mData": "value.ShotsFired", "aTargets": [ 6 ] },
                { "mData": "value.CombatDives", "aTargets": [ 7 ] },
                { "mData": "value.ParaJumps", "aTargets": [ 8 ] },
                { "mData": "value.Heals", "aTargets": [ 9 ] },
                { "mData": "value.VehicleTime", "aTargets": [ 10 ] },
                { "mData": "value.VehicleKills", "aTargets": [ 11 ] },
                { "mData": "value.PilotTime", "aTargets": [ 12 ] }
            ]
        } );
    });

</script>

<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="personnel">
    <thead>
    <tr>
        <th width="15%">Name</th>
        <th>Ops</th>
        <th>Mins Played</th>
        <th>Kills</th>
        <th>Injuries</th>
        <th>Deaths</th>
        <th>Ammo</th>
        <th>Dives</th>
        <th>Jumps</th>
        <th>Medic Exp.</th>
        <th>Vehicle Exp.</th>
        <th>Gunnery Kills</th>
        <th>Pilot Exp.</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>