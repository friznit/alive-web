<script>
    $(document).ready(function(){
		
        $('#top_groupclasses').dataTable({
					"bJQueryUI": true,
					"sAjaxSource": '{{ URL::to('/') }}/api/orbatclasses?id={{{$clan->tag}}}',
					"sAjaxDataProp": "rows",
                    "bPaginate": false,
                    "bFilter": false,
                    "bInfo": false,
					"aaSorting": [[2, "desc" ]],
					"aoColumnDefs": [
						{ "mDataProp": "key.1",  "aTargets": [ 0 ],
							"mRender" : function (data, type) {
				  				return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgVehicles_') }}" + data + ".png onerror=this.style.display='none'>";}
						},
						{ "mDataProp": "key.2",  "aTargets": [ 1 ]},
						{ "mDataProp": "value", "aTargets": [ 2 ],
							"mRender" : function (data, type) {
				  				return Math.round(data/60*10)/10;}}
					]
		} );

    });

</script>

<div id="units_container">
    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="top_groupclasses">
        <thead>
        <tr>
            <th width="40">Image</th>
            <th width="40%">Role</th>
            <th width="20%">Deployed (hrs)</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>