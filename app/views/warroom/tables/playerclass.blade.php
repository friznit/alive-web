<script>
    $(document).ready(function(){
		
        $('#top_classes').dataTable({
					"bJQueryUI": true,
					"sAjaxSource": "http://msostore.iriscouch.com/events/_design/events/_view/players_class?&group_level=3&startkey=[%22{{ $player_id }}%22]&endkey=[%22{{ $player_id }}%22,{}]&callback=?",
					"sAjaxDataProp": "rows",
                    "bPaginate": false,
                    "bFilter": false,
                    "bInfo": false,
					"aaSorting": [[2, "desc" ]],
					"aoColumnDefs": [
						{ "mDataProp": "key.1",  "aTargets": [ 0 ],
							"mRender" : function (data, type) {
				  				return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgVehicles_') }}" + data + ".png>";}
						},
						{ "mDataProp": "key.2",  "aTargets": [ 1 ]},
						{ "mDataProp": "value", "aTargets": [ 2 ],
							"mRender" : function (data, type) {
				  				return Math.round(data/60*10)/10;}}
					]
		} );

    });

</script>

<div id="playerclass_container">
    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="top_classes">
        <thead>
        <tr>
            <th width="20%">Image</th>
            <th width="60%">Role</th>
            <th width="20%">Experience (hrs)</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>