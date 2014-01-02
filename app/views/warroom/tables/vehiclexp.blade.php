<script>
    $(document).ready(function(){
        $('#top_vehicles').dataTable({
					"bJQueryUI": true,
					"bFilter": false,
					"sAjaxSource": "http://msostore.iriscouch.com/events/_design/events/_view/players_vehxp?&group_level=3&startkey=[%22{{ $profile->a3_id }}%22]&endkey=[%22{{ $profile->a3_id }}%22,{}]&callback=?",
					"sAjaxDataProp": "rows",
					"sScrollY": "300px",
					"bPaginate": false,
					"bInfo": false,
					"bScrollCollapse": true,
					"aaSorting": [[2, "desc" ]],
					"aoColumnDefs": [
						{ "mDataProp": "key.1",  "aTargets": [ 0 ],
							"mRender" : function (data, type) {
				  				return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgVehicles_') }}" + data + ".png>";}
						},
						{ "mDataProp": "key.2",  "aTargets": [ 1 ]},
						{ "mDataProp": "value", "aTargets": [ 2 ]}
					]
		} );

    });

</script>
<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="top_vehicles">
    <thead>
    <tr>
        <th width="40%">Image</th>
        <th width="50%">Vehicle</th>
        <th width="10%">Experience (mins)</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>