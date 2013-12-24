<script>
    $(document).ready(function(){
        $('#t1_operators').dataTable({
					"bJQueryUI": true,
					"sAjaxSource": 'http://msostore.iriscouch.com/events/_design/kill/_view/player_kills_count?group_level=1&callback=?',
					"sAjaxDataProp": "rows",
					"sScrollY": "300px",
					"bPaginate": false,
					"bInfo": false,
					"bScrollCollapse": true,
					"aaSorting": [[1, "desc" ]],
					"aoColumnDefs": [
						{ "mDataProp": "key",  "aTargets": [ 0 ]},
						{ "mDataProp": "value", "aTargets": [ 1 ]}
					]
		} );

    });

</script>
<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="t1_operators">
    <thead>
    <tr>
        <th width="70%">Player</th>
        <th width="30%">EKIA</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>