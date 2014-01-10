<script>
    $(document).ready(function(){
        $('#pilots').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/pilots',
            "sAjaxDataProp": "rows",
            "bPaginate": true,
            "fnDrawCallback": function ( oSettings ) {
                /* Need to redo the counters if filtered or sorted */
                /*
                if ( oSettings.bSorted || oSettings.bFiltered )
                {
                    for ( var i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++ )
                    {
                        $('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( i+1 );
                    }
                }
                */
            },
            "aaSorting": [[3, "desc" ]],
            "aoColumnDefs": [
                { "mDataProp": "key", "aTargets": [ 0 ],
					"mRender" : function (data, type) {
						return "<a href={{ URL::to('war-room/showpersonnel') }}/" + data[0] + ">" +  data[1] + "</a>";
					}  
				},
                { "mDataProp": "key.2", "aTargets": [ 1 ] },
				{ "mDataProp": "key.3", "aTargets": [ 2 ],
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
        <th>Player</th>
        <th>Vehicle</th>
        <th>Image</th>
        <th>EKIA</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>