<script>

    $(document).ready(function(){
        $('#kpm').dataTable({
            "bJQueryUI": true,
            "sAjaxSource": 'http://alive.iriscouch.com/events/_design/playerTable/_view/playerKillsperMin?group_level=2&callback=?',
            "sAjaxDataProp": "rows",
            "bPaginate": true,
            "aaSorting": [[1, "desc" ]],
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
            "aoColumnDefs": [
                { "mDataProp": "key.0",  "aTargets": [ 0 ]},
                { "mDataProp": "value.Avg", "aTargets": [ 1 ]}
            ]
        } );
    });

</script>

<table cellpadding="0" cellspacing="0" border="0" class="dataTable table" id="kpm">
    <thead>
    <tr>
        <th>Player</th>
        <th>Kills/min.</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>