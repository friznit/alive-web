<script>
    $(document).ready(function(){
		
        $('#top_groupplaykills').dataTable({
			"bJQueryUI": true,
			"sAjaxSource": '{{ URL::to('/') }}/api/orbatplayerkills?id={{{$clan->tag}}}',
			"sAjaxDataProp": "rows",
	        "bPaginate": false,
	        "bFilter": false,
	        "bInfo": false,
			"aaSorting": [[2, "desc" ]],
			"aoColumnDefs": [
				{ "mDataProp": "key",  "aTargets": [ 1 ],
					"mRender" : function (data, type) {
		  				return "<img src='{{ URL::to('img/classes/thumbs/150px-Arma3_CfgWeapons_') }}" + data[3] + ".png' alt='" + data[4] +"' title='" + data[4] +"'/>";}
				},
				{ "mDataProp": "key",  "aTargets": [ 0 ],
				  "mRender" : function (data, type) {
					return "<a href={{ URL::to('war-room/showpersonnel') }}/" + data[1] + ">" +  data[2] + "</a>";}
				},
				{ "mDataProp": "value", "aTargets": [ 2 ]}
			]
		});

    });

</script>

<div id="oweapons_container">
    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="top_groupplaykills">
        <thead>
        <tr>
            <th width="40%">Unit</th>
        	<th width="40%">Weapon</th>
            <th width="20%">Kills</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>