<script>
    $(document).ready(function(){

        $('#pilots').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/orbatpilots?id={{{$clan->tag}}}',
            "sAjaxDataProp": "rows",
			"bPaginate": false,
			"bInfo": false,
			"bFilter": false,
            "aaSorting": [[2, "desc" ]],
            "aoColumnDefs": [
                { "mDataProp": "key", "aTargets": [ 0 ],
				    "mRender" : function (data, type) {
						return "<a href={{ URL::to('war-room/showpersonnel') }}/" + data[1] + ">" +  data[2] + "</a>";
					}  
				},
				{ "mDataProp": "key", "aTargets": [ 1 ],
                    "mRender" : function (data, type) {
                        if (data[3].indexOf("_") > -1) {
                            data[3] = data[3].replace(/_/g," ");
                        }
                        return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgVehicles_') }}" + data[4] + ".png alt='" + data[3] +"' title='" + data[3] +"'/>";
                    }
			    },
                { "mDataProp": "value", "aTargets": [ 2 ]  }
            ]
        } );
    });

</script>
<div id="pilots_container">
<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="pilots">
    <thead>
    <tr>
        <th>Unit</th>
        <th>Vehicle</th>
        <th>EKIA</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>