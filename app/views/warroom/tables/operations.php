<script>

    $(document).ready(function(){
        $('#operations').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/operations&callback=?',
            "sAjaxDataProp": "rows",
            "bPaginate": true,
            "aaSorting": [[1, "desc" ]],
            "aoColumnDefs": [
                { "mDataProp": "key.2",  "aTargets": [ 0 ]},
                { "mDataProp": "value.InfKills", "aTargets": [ 1 ]},
                { "mDataProp": "value.VehKills", "aTargets": [ 2 ]},
                { "mDataProp": "value.AirKills", "aTargets": [ 3 ]},
                { "mDataProp": "value.ShipKills", "aTargets": [ 4 ]},
                { "mDataProp": "value.OtherKills", "aTargets": [ 5 ]}
            ]
        } );
    });

</script>

<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="operations">
    <thead>
    <tr>
        <th>Operation</th>
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