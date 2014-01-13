<script>
    $(document).ready(function(){
		
        $('#alias').dataTable({
					"bJQueryUI": true,
					"sAjaxSource": '{{ URL::to('/') }}/api/playeralias?id={{{$player_id}}}',
					"sAjaxDataProp": "rows",
                    "bPaginate": false,
                    "bFilter": false,
					"aaSorting": [],
                    "bInfo": false,
					"aoColumnDefs": [
						{ "mDataProp": "key.1",  "aTargets": [ 0 ]},
						{ "mDataProp": "value",  "aTargets": [ 1 ]}
					]
		} );

    });

</script>

    <table cellpadding="0" cellspacing="0" border="0" class="table" id="alias">
        <thead>
        <tr>
            <th width="70%">Name</th>
			<th width="30%">Operations</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
