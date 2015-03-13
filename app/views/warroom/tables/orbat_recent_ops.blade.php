<script>
    $(document).ready(function(){
        $('#orecent_ops').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/orbatrecentoperations?id={{{$clan->tag}}}',
            "sAjaxDataProp": "rows",
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
			"aaSorting":[],
            "aoColumnDefs": [
                {
                    "aTargets": [ 0 ],
                    "mDataProp": "value.date",
                    "mRender": function ( data, type, row ) {
                        return parseArmaDate(data);
                    }
                },{
                    "aTargets": [ 1 ],
                    "mDataProp": "value.Map",
                    "mRender": function ( data, type, row) {
                        return data;
                    }
                },{
                    "aTargets": [ 2 ],
                    "mDataProp": "value",
                    "mRender": function ( data, type, row) {
                        return "<a href={{ URL::to('war-room') }}/showoperation?name=" + encodeURIComponent(data.Operation) +"&map=" + encodeURIComponent(data.Map) + "&clan={{{$clan->tag}}}>" +  data.Operation + "</a>";
                    }
                }
            ]
        } );
		$("#recent_ops_container").mCustomScrollbar("update");
    });

</script>

<div id="recent_ops_container" style="max-height:260px">

    <table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="orecent_ops">
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