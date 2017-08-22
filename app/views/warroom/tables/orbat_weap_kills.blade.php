<script>
    $(document).ready(function(){
		
        $('#top_groupweapkills').dataTable({
					"bJQueryUI": true,
					"sAjaxSource": '{{ URL::to('/') }}/api/orbatkillsbyweapon?id={{{$clan->tag}}}',
					"sAjaxDataProp": "rows",
                    "bPaginate": false,
                    "bFilter": false,
                    "bInfo": false,
					"aaSorting": [[1, "desc" ]],
					"aoColumnDefs": [
						{ "mDataProp": "key",  "aTargets": [ 0 ],
							"mRender" : function (data, type) {
								if (data[1].indexOf("_") > -1) {
									data[1] = data[1].replace(/_/g," ");
								}								
				  				return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgWeapons_') }}" + data[2] + ".png alt='" + data[1] +"' title='" + data[1] +"'/>";}
						},
						{ "mDataProp": "value", "aTargets": [ 1 ]}
					]
		} );

    });

</script>

<div id="owk_container">
    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="top_groupweapkills">
        <thead>
        <tr>
            <th width="90%">Weapon</th>
            <th width="10%">Kills</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>