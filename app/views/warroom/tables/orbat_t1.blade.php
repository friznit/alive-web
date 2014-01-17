<script>
    $(document).ready(function(){
        $('#t1_operators').dataTable({
					"bJQueryUI": true,
					"sAjaxSource": '{{ URL::to('/') }}/api/orbatt1?id={{{$clan->tag}}}',
					"sAjaxDataProp": "rows",
					"bPaginate": false,
					"bInfo": false,
					"bFilter": false,
					"aaSorting": [[1, "desc" ]],
					"aoColumnDefs": [
						{ "mDataProp": "key",  "aTargets": [ 0 ],
						  "mRender" : function (data, type) {
							return "<a href={{ URL::to('war-room/showpersonnel') }}/" + data[1] + ">" +  data[2] + "</a>";}
						},
						{ "mDataProp": "value", "aTargets": [ 1 ]}
					]
		} );

    });

</script>
<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="t1_operators">
    <thead>
    <tr>
        <th width="80%">Unit</th>
        <th width="20%">EKIA</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>