<script>
    $(document).ready(function(){
        $('#medics').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/orbatmedics?id={{{$clan->tag}}}',
            "sAjaxDataProp": "rows",
					"bPaginate": false,
					"bInfo": false,
					"bFilter": false,
            "aaSorting": [[1, "desc" ]],
            "aoColumnDefs": [
                { "mDataProp": "key", "aTargets": [ 0 ],
					"mRender" : function (data, type) {
						return "<a href={{ URL::to('war-room/showpersonnel') }}/" + data[1] + ">" +  data[2] + "</a>";
					}  
				},
                { "mDataProp": "value", "aTargets": [ 1 ]  }
            ]
        } );
    });

</script>

<div id="medics_container">
<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="medics">
    <thead>
    <tr>
        <th>Unit</th>
        <th>Medical Support</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>