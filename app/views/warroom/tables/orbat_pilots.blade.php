<script>
    $(document).ready(function(){
        $('#pilots').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/orbat_pilots?id={{{$clan->tag}}}',
            "sAjaxDataProp": "rows",
					"bPaginate": false,
					"bInfo": false,
					"bFilter": false,
            "aaSorting": [[3, "desc" ]],
            "aoColumnDefs": [
                { "mDataProp": "key", "aTargets": [ 0 ],
					"mRender" : function (data, type) {
						return "<a href={{ URL::to('war-room/showpersonnel') }}/" + data[1] + ">" +  data[2] + "</a>";
					}  
				},
                { "mDataProp": "key.3", "aTargets": [ 1 ] },
				{ "mDataProp": "key.4", "aTargets": [ 2 ],
					"mRender" : function (data, type) {
						return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgVehicles_') }}" + data + ".png>";
					}
			    },
                { "mDataProp": "value", "aTargets": [ 3 ]  }
            ]
        } );
    });

</script>

<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="pilots">
    <thead>
    <tr>
        <th>Unit</th>
        <th>Vehicle</th>
        <th>Image</th>
        <th>EKIA</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>