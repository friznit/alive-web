<script>

    $(document).ready(function(){
        $('#operations1').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/operations',
            "sAjaxDataProp": "rows",
            "bPaginate": true,
            "aaSorting": [[1, "desc" ]],
            "bDeferRender": true,
            "aoColumnDefs": [
                { "mData": "key",  "aTargets": [ 0 ],
				  "mRender" : function (data, type) {
						return "<a href={{ URL::to('war-room') }}/showoperation?name=" + encodeURIComponent(data[2]) +"&map=" + encodeURIComponent(data[0]) + "&clan=" + encodeURIComponent(data[1]) + ">" +  data[2] + " (" + data[1] +")</a>";}
				},
				{ "mData": "key.0", "aTargets": [ 1 ]},
                { "mData": "value.InfKills", "aTargets": [ 2 ]},
                { "mData": "value.VehKills", "aTargets": [ 3 ]},
                { "mData": "value.AirKills", "aTargets": [ 4 ]},
                { "mData": "value.ShipKills", "aTargets": [ 5 ]},
                { "mData": "value.OtherKills", "aTargets": [ 6 ]}
            ]
        });
    });

</script>

<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="operations1">
    <thead>
    <tr>
        <th>Operation</th>
		<th>AO</th>
        <th>Inf</th>
        <th>Veh</th>
        <th>Air</th>
        <th>Ship</th>
        <th>Other</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
