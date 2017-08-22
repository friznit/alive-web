<script>
    $(document).ready(function(){
		
        $('#top_groupweap').dataTable({
					"bJQueryUI": true,
					"sAjaxSource": '{{ URL::to('/') }}/api/orbatweapons?id={{{$clan->tag}}}',
					"sAjaxDataProp": "rows",
                    "bPaginate": false,
                    "bFilter": false,
                    "bInfo": false,
					"aaSorting": [[1, "desc" ]],
					"aoColumnDefs": [
						{ "mDataProp": "key",  "aTargets": [ 0 ],
							"mRender" : function (data, type) {
				  				return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgWeapons_') }}" + data[1] + ".png alt='" + data[2] +"' title='" + data[2] +"'/>";}
						},
						{ "mDataProp": "value", "aTargets": [ 1 ]}
					]
		} );

    });

</script>

<div id="weapons_container">
    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="top_groupweap">
        <thead>
        <tr>
            <th width="80%">Weapon</th>
            <th width="20%">Ammo Used</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>