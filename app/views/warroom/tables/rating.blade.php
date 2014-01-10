<script>

    $(document).ready(function(){
        $('#rating').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/ratings',
            "sAjaxDataProp": "rows",
            "bPaginate": true,
            "aaSorting": [[1, "desc" ]],
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

<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="rating">
    <thead>
    <tr>
        <th>Unit</th>
        <th>Average Rating</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>