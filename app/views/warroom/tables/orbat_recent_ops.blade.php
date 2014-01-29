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
                    "mDataProp": "value.Operation",
                    "mRender": function ( data, type, row) {
                        return data;
                    }
                }
            ]
        } );

    });

</script>

<div id="#orbat_recent_ops_container">

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