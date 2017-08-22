<script>
    $(document).ready(function(){
		
        $('#top_groupveh').dataTable({
					"bJQueryUI": true,
					"sAjaxSource": '{{ URL::to('/') }}/api/orbatvehicles?id={{{$clan->tag}}}',
					"sAjaxDataProp": "rows",
                    "bPaginate": false,
                    "bFilter": false,
                    "bInfo": false,
					"aaSorting": [[1, "desc" ]],
					"aoColumnDefs": [
						{ "mDataProp": "key",  "aTargets": [ 0 ],
							"mRender" : function (data, type) {
								if (data[2].indexOf("_") > -1) {
									data[2] = data[2].replace(/_/g," ");
								}
				  				return "<img src={{ URL::to('img/classes/thumbs/150px-Arma3_CfgVehicles_') }}" + data[1] + ".png alt='" + data[2] +"' title='" + data[2] +"'/>";}
						},
						{ "mDataProp": "value", "aTargets": [ 1 ],
							"mRender" : function (data, type) {
				  				return Math.round(data/60*10)/10;
				  			}
				  		}
					]
		} );

    });

</script>

<div id="vehiclexp_container">
    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="top_groupveh">
        <thead>
        <tr>
            <th width="80%">Vehicle</th>
            <th width="20%">Deployed (hrs)</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

