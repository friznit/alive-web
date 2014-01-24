<script>

    $(document).ready(function(){
        $('#operations1').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": '{{ URL::to('/') }}/api/operations',
            "bPaginate": true,
            "aaSorting": [[1, "desc" ]],
            "aoColumnDefs": [
                { "mData": "key.2",  "aTargets": [ 0 ]},
                { "mData": "value.InfKills", "aTargets": [ 1 ]},
                { "mData": "value.VehKills", "aTargets": [ 2 ]},
                { "mData": "value.AirKills", "aTargets": [ 3 ]},
                { "mData": "value.ShipKills", "aTargets": [ 4 ]},
                { "mData": "value.OtherKills", "aTargets": [ 5 ]}
            ]
        });
    });

</script>

<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="operations1">
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