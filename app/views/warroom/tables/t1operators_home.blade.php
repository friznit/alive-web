<script>
    $(document).ready(function(){
        $('#t1_operators').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/t1operators',
            "sAjaxDataProp": "rows",
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
			"aaSorting": [[1, "desc" ]],
            "fnDrawCallback": function ( oSettings ) {
                $("#t1operators_container").mCustomScrollbar("update");
            },
            "aoColumnDefs": [
                { "mDataProp": "key",  "aTargets": [ 0 ],
				"mRender" : function (data, type) {
						return "<a href={{ URL::to('war-room/showpersonnel') }}/" + data[0] + ">" +  data[1] + "</a>";}
				},
                { "mDataProp": "value", "aTargets": [ 1 ]}
            ]
		} );

    });

</script>
<div id="t1operators_container">

    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="t1_operators">
        <thead>
        <tr>
            <th width="70%">Call Sign</th>
            <th width="30%">EKIA</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>