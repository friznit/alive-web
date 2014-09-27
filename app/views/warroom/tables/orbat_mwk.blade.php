<script>
    $(document).ready(function(){
		
        $('#top_groupmwk').dataTable({
					"bJQueryUI": true,
					"sAjaxSource": '{{ URL::to('/') }}/api/orbatmountedkills?id={{{$clan->tag}}}',
					"sAjaxDataProp": "rows",
                    "bPaginate": false,
                    "bFilter": false,
                    "bInfo": false,
					"aaSorting": [[2, "desc" ]],
					"aoColumnDefs": [
						{ "mDataProp": "key.2",  "aTargets": [ 0 ],
							"mRender" : function (data, type) {
				  				return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgWeapons_') }}" + data + ".png onerror=this.style.display='none'>";}
						},
						{ "mDataProp": "key.1",  "aTargets": [ 1 ],
							"mRender" : function (data, type) {
								if (data.indexOf("_") > -1) {
									data = data.replace(/_/g," ");
								}
								return data;
						}},
						{ "mDataProp": "value", "aTargets": [ 2 ]}
					]
		} );

    });

</script>

<div id="mwk_container">
    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="top_groupmwk">
        <thead>
        <tr>
            <th width="40%">Image</th>
            <th width="40%">Weapon</th>
            <th width="20%">Kills</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>