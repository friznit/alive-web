<script>

    $(document).ready(function() {

        var ajaxUrl = '{{ URL::to('/') }}/api/optotals?name={{ $name }}&clan={{ $clan->tag }}&map={{ $ao->configName }}';
        $.getJSON(ajaxUrl, function(data) {
            var totals = data;

            var kills = totals.Kills;
            counter($('#ekia').append(),kills);

            var losses = totals.Deaths;
            counter($('#losses').append(),losses);

            var hours = Math.round((totals.CombatHours / 60)*10)/10;
            counter($('#combat_hours').append(),hours);

            var fired = totals.ShotsFired;
            counter($('#ammo').append(),fired);
        });

        var ajaxUrl = '{{ URL::to('/') }}/api/opactiveunitcount?name={{ $name }}&clan={{ $clan->tag }}&map={{ $ao->configName }}';
        $.getJSON(ajaxUrl, function(data) {
            var activeunits = data;

            counter($('#active_units').append(),activeunits);
        });
    })
</script>

<div id="overview_logo">
    <a href="{{ URL::to('/') }}"><img src="{{ URL::to('/') }}/img/logo.png"/></a>
    <a href="{{ URL::to('/war-room') }}"><img src="{{ URL::to('/') }}/img/alive_warroom_tiny.png"/></a>
</div>

<div id="overview_container">
    <span class="highlight">LIVE FEED</span> - ENEMY KILLED: <span id="ekia">0</span> |
    LOSSES: <span id="losses">0</span> |
    COMBAT HRS: <span id="combat_hours">0</span> |
    AMMUNITION: <span id="ammo">0</span> |
    ACTIVE UNITS: <span id="active_units">0</span> - <span class="highlight">LIVE FEED</span>
</div>