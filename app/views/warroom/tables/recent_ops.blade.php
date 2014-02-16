<script>
    $(document).ready(function(){
        $('#recent_ops').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/recentoperations',
            "sAjaxDataProp": "rows",
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
			"aaSorting":[],
            "aoColumnDefs": [
                {
                    "aTargets": [ 0 ],
                    "mDataProp": "key",
                    "mRender": function ( data, type, row ) {
                        return parseArmaDate(data);
                    }
                },{
                    "aTargets": [ 1 ],
                    "mDataProp": "value",
                    "mRender": function ( data, type, row) {
                        return data[1];
                    }
                },{
                    "aTargets": [ 2 ],
                    "mDataProp": "value",
                    "mRender": function ( data, type, row) {
						return "<a href={{ URL::to('war-room') }}/showoperation?name=" + encodeURIComponent(data[2]) +"&map=" + encodeURIComponent(data[1]) + "&clan=" + encodeURIComponent(data[3]) + ">" +  data[2] + " (" + data[3] +")</a>";
                    }
                }
            ]
        } );
    });

</script>

<div id="recent_ops_container">

    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="recent_ops">
        <thead>
        <tr>
            <th width="30%">Date</th>
            <th width="10%">Map</th>
            <th width="60%">Operation</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</div>