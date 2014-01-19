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
						{ "mDataProp": "key.3",  "aTargets": [ 1 ],
							"mRender" : function (data, type) {
				  				return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgWeapons_') }}" + data + ".png>";}
						},
						{ "mDataProp": "key.2",  "aTargets": [ 0 ]},
						{ "mDataProp": "value", "aTargets": [ 2 ]}
					]
		} );

    });

</script>

<div id="playerclass_container">
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