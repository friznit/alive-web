<script>
    $(document).ready(function(){
		
        $('#top_groupweap').dataTable({
					"bJQueryUI": true,
					"sAjaxSource": '{{ URL::to('/') }}/api/orbatweapons?id={{{$clan->tag}}}',
					"sAjaxDataProp": "rows",
                    "bPaginate": false,
                    "bFilter": false,
                    "bInfo": false,
					"aaSorting": [[2, "desc" ]],
					"aoColumnDefs": [
						{ "mDataProp": "key.1",  "aTargets": [ 0 ],
							"mRender" : function (data, type) {
				  				return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgWeapons_') }}" + data + ".png onerror=this.style.display='none'>";}
						},
						{ "mDataProp": "key.2",  "aTargets": [ 1 ]},
						{ "mDataProp": "value", "aTargets": [ 2 ]}
					]
		} );

    });

</script>

<div id="weapons_container">
    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="top_groupweap">
        <thead>
        <tr>
            <th width="35%">Image</th>
            <th width="40%">Weapon</th>
            <th width="25%">Ammo Used</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>