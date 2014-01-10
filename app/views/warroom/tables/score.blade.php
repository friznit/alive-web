<script>

    $(document).ready(function(){
        $('#score').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/scores',
            "sAjaxDataProp": "rows",
            "bPaginate": true,
            "aaSorting": [[1, "desc" ]],
            "aoColumnDefs": [
                { "mDataProp": "key",  "aTargets": [ 0 ], 
					"mRender" : function (data, type) {
						return "<a href={{ URL::to('war-room/showpersonnel') }}/" + data[1] + ">" +  data[0] + "</a>";
					}
				},
                { "mDataProp": "value", "aTargets": [ 1 ]}
            ]
        } );
    });

</script>

<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="score">
    <thead>
    <tr>
        <th>Unit</th>
        <th>Score</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>