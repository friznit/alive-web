<script>
    $(document).ready(function(){
        $('#top_weapons').dataTable({
					"bJQueryUI": true,
					"sAjaxSource": "http://msostore.iriscouch.com/events/_design/events/_view/players_weapons?&group_level=3&startkey=[%22{{ $profile->a3_id }}%22]&endkey=[%22{{ $profile->a3_id }}%22,{}]&callback=?",
					"sAjaxDataProp": "rows",
                    "bPaginate": false,
                    "bFilter": false,
                    "bInfo": false,
					"aaSorting": [[2, "desc" ]],
					"aoColumnDefs": [
						{ "mDataProp": "key.1",  "aTargets": [ 0 ],
							"mRender" : function (data, type) {
				  				return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgWeapons_') }}" + data + ".png>";}
						},
						{ "mDataProp": "key.2",  "aTargets": [ 1 ]},
						{ "mDataProp": "value", "aTargets": [ 2 ]}
					]
		} );

    });

</script>

<div id="weapons_container">
    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="top_weapons">
        <thead>
        <tr>
            <th width="20%">Image</th>
            <th width="60%">Weapon</th>
            <th width="20%">Shots Fired</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>