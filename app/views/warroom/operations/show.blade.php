@extends('warroom.layouts.operations')

{{-- Content --}}
@section('content')

<div id="map" style="height: 900px;"></div>

<script type="text/javascript">

    // Leaflet setup
    // ------------------------------------------------------------

    var icon = L.Icon.extend({
        options: {
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            labelAnchor: [8, 0]
        }
    });

    var west_unit = new icon ({iconUrl: 'http://alivemod.com/img/icons/b_iconman_ca.png'});
    var east_unit = new icon ({iconUrl: 'http://alivemod.com/img/icons/o_iconman_ca.png'});
    var indy_unit = new icon ({iconUrl: 'http://alivemod.com/img/icons/i_iconman_ca.png'});
    var civ_unit = new icon ({iconUrl: 'http://alivemod.com/img/icons/c_iconman_ca.png'});

    L.Map = L.Map.extend({
        openPopup: function(popup) {
            //this.closePopup();  // just comment this
            this._popup = popup;

            return this.addLayer(popup).fire('popupopen', {
                popup: this._popup
            });
        }
    });

    L.Popup = L.Popup.extend({

    });

    var mz = 7;
    var size = 32768;

    var westkills = new L.LayerGroup();
    var eastkills = new L.LayerGroup();
    var civkills = new L.LayerGroup();
    var indykills = new L.LayerGroup();


    var map = L.map('map', {
        minZoom: 0,
        maxZoom: mz,
        zoomControl: true,
        layers: [westkills, eastkills, civkills, indykills],
        attributionControl: false,
        crs: L.CRS.Simple,
        fullscreenControl: true,
        fullscreenControlOptions: {
            position: 'topleft'
        }
    });

    var southWest = map.unproject([0, size], map.getMaxZoom());
    var northEast = map.unproject([size, 0], map.getMaxZoom());
    map.setMaxBounds(new L.LatLngBounds(southWest, northEast));

    map.fitWorld();

    var AO = L.tileLayer("http://alivemod.com/maps/{{ strtolower($ao->configName) }}/{z}/{x}/{y}.png" , {
        attribution: 'ALiVE',
        tms: true	//means invert.
    }).addTo(map);


    var baseLayer = {
        "AO": AO
    };

    var overlays = {
        "West Killed": westkills,
        "East Killed": eastkills,
        "Indy Killed": indykills,
        "Civ Killed": civkills
    };

    layerControl = L.control.layers(baseLayer, overlays,{position: 'topleft'});
    layerControl.addTo(map);

    var items = {};
    var markers = [];

    var hostileIcon = L.icon({
        iconUrl: '{{ URL::to('/') }}/img/icons/hostileIcon.png',
        shadowUrl: '{{ URL::to('/') }}/img/icons/hostileIconShadow.png',
        iconSize:     [30, 30], // size of the icon
        shadowSize:   [30, 30], // size of the shadow
        iconAnchor:   [20, 20], // point of the icon which will correspond to markers location
        shadowAnchor: [15, 15],  // the same for the shadow
        popupAnchor:  [90, 90] // point from which the popup should open relative to the iconAnchor
    });

    var groupIcon = L.Icon.extend({
        options: {
            shadowUrl: '{{ URL::to('/') }}/img/icons/groupIconShadow.png',
            shadowUrl: '{{ URL::to('/') }}/img/icons/w_group_0.png',
            iconSize:     [35, 35], // size of the icon
            shadowSize:   [35, 35], // size of the shadow
            iconAnchor:   [20, 20], // point of the icon which will correspond to markers location
            shadowAnchor: [20, 20],  // the same for the shadow
            popupAnchor:  [105, 105] // point from which the popup should open relative to the iconAnchor
        }
    });


    // document has loaded

    $(document).ready(function() {


        // Leaflet setup
        // ------------------------------------------------------------

        // on resize of window resize the map
        $(window).on("resize", function() {
            $("#map").height($(window).height()).width($(window).width());
            map.invalidateSize();
        }).trigger("resize");

        // on map fullscreen event handler
        map.on('enterFullscreen', function(){
            //console.log('entered fullscreen');
        });

        // on map exit fullscreen event handler
        map.on('exitFullscreen', function(){
            //console.log('exited fullscreen');
        });


        // Timeline setup
        // ------------------------------------------------------------

        cursor = 0;
        itemsPerPage = 50;

        loadData(cursor);

        /*
        $(".trigger").click(function(){
            $(".panel").toggle("fast");
            $(this).toggleClass("active");
            return false;
        });
        */

        // load previous dataset
        // shows the loading overlay
        // resets the timeline
        $("#prev").click(function(){
            $("#warroom_timeline_loading").fadeIn();
            $("#warroom_timeline").remove();
            $('<div id="warroom_timeline"></div>').insertBefore("#warroom_timeline_controls");
            loadData(itemsPerPage);
        });

        // load next dataset
        // shows the loading overlay
        // resets the timeline
        $("#next").click(function(){
            $("#warroom_timeline_loading").fadeIn();
            $("#warroom_timeline").remove();
            $('<div id="warroom_timeline"></div>').insertBefore("#warroom_timeline_controls");
            loadData(-itemsPerPage);
        });

    });

    /*
     * Load data set for timeline and map markers
     */
    function loadData(skip) {

        cursor = cursor + skip;

        if(cursor <= 0){
            cursor = 0;
        }

        // clear existing map markers
        clearAllMarkers();

        // get data from the cursor location onwards to limit
        $.getJSON("{{ URL::to('/') }}/api/oplivefeedpaged?name={{ $name }}&clan={{ $clan->tag }}&map={{ $ao->configName }}&limit="+itemsPerPage+"&skip="+cursor, function( data ) {

            if(data.error) {
            }

            // setup the main timeline data structure
            var timelineData = new Object();
            timelineData.timeline = new Object();
            timelineData.timeline.headline = "TEST";
            timelineData.timeline.type = "default";
            timelineData.timeline.text = "CHEESE";
            timelineData.timeline.startDate = "2014,03,30,11,00";
            timelineData.timeline.date = [];

            var startDate = '';
            var endDate = '';
            var eventCount = data.rows.length;

            // loop loaded row data and create timeline objects
            $.each( data.rows, function( key, val ) {

                eventObj = val.value;

                // parse dates into timeline friendly format
                var parsedDateArray = eventObj.realTime.split(" ");
                var parsedDayArray = parsedDateArray[0].split("/");
                var parsedTimeArray = parsedDateArray[1].split(":");

                startDate = parsedDayArray[2] + ',' + parsedDayArray[1] + ',' + parsedDayArray[0] + ',' + parsedTimeArray[0] + ',' + parsedTimeArray[1] + ',' + parsedTimeArray[2];
                endDate = parsedDayArray[2] + ',' + parsedDayArray[1] + ',' + parsedDayArray[0] + ',' + parsedTimeArray[0] + ',' + parsedTimeArray[1] + ',' + (parseInt(parsedTimeArray[2]) + 1);

                var output = prepareEvent(eventCount, eventObj);

                // create the timeline event object
                var event = new Object();
                event.startDate = startDate;
                event.endDate = startDate;
                event.headline = eventObj.Event;
                event.text = output;
                event.asset = new Object();
                event.asset.media = '';
                event.asset.credit = '';
                event.asset.caption = '';

                // push the new event onto the stack
                timelineData.timeline.date.push(event);

                // increment counter
                eventCount--;

            });

            console.log(markers);

            // no data
            if(data.rows.length == 0){
                return;
            }

            // first load of page
            // create the timeline
            if(typeof VMM == 'undefined') {
                createStoryJS({
                    type:		'timeline',
                    width:		'100%',
                    height:		'320',
                    source:		timelineData,
                    embed: true,
                    embed_id:	'warroom_timeline',
                    start_at_end: true,
                    start_zoom_adjust: 0,
                    debug:		false,
                    css: '{{ URL::to("/") }}/css/timeline.css',
                    js: '{{ URL::to("/") }}/js/timeline.js'
                });
            }else{

                // subsequent load of page
                // reset and recreate the timeline

                $(global).unbind()

                createStoryJS({
                    type:		'timeline',
                    width:		'100%',
                    height:		'320',
                    source:		timelineData,
                    embed: true,
                    embed_id:	'warroom_timeline',
                    start_at_end: true,
                    start_zoom_adjust: 0,
                    debug:		false,
                    css: '{{ URL::to("/") }}/css/timeline.css',
                    js: '{{ URL::to("/") }}/js/timeline.js'
                });
            }

            // hide the loading overlay
            $("#warroom_timeline_loading").fadeOut();

        });
    }

    /*
     * Setup map marker and prepare output for timeline and marker display
     */
    function prepareEvent(index, value) {

        var action = value.Event;
        var output = '';

        switch(action){

            case "Kill":

                var posx = value.KilledGeoPos[0];
                var posy = value.KilledGeoPos[1];
                var multiplier = size / {{$ao->size}};

                if (value.Death == "true")
                {
                    output = value.Map + ' - Grid:' + value.KilledPos + ' - ' + value.gameTime + ' local<br>' + value.Killedfaction + ' ' + value.KilledType + '<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="highlight"> ' + value.PlayerName + '</span></a> has been KIA';
                } else {
                    if (value.KilledClass != "Infantry")
                    {
                        output = value.Map + ' - Grid:' + value.KilledPos + ' - ' + value.gameTime + ' local<br>' + value.Killedfaction + ' <span class="highlight">' + value.KilledType + '</span> has been destroyed';
                    } else {
                        output = value.Map + ' - Grid:' + value.KilledPos + ' - ' + value.gameTime + ' local<br>' + value.Killerfaction + ' ' + value.KillerType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) </span> kills ' + value.Killedfaction + '<span class="highlight"> ' + value.KilledType + '</span> with an ' + value.Weapon + ' from ' + value.Distance + 'm';
                    }
                }

                var popup = L.popup().setContent('<div class="admin-panel">' + output + '</div>');

                if (value.KilledSide == "WEST")
                {
                    var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(westkills);
                    marker.bindPopup(popup, {
                        showOnMouseOver: true,
                        offset: new L.Point(0, 0)
                    });

                    markers[index] = marker;
                }
                if (value.KilledSide == "EAST")
                {
                    var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: east_unit}).addTo(eastkills);
                    marker.bindPopup(popup, {
                        showOnMouseOver: true,
                        offset: new L.Point(0, 0)
                    });

                    markers[index] = marker;
                }
                if (value.KilledSide == "GUER")
                {
                    var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: indy_unit}).addTo(indykills);
                    marker.bindPopup(popup, {
                        showOnMouseOver: true,
                        offset: new L.Point(0, 0)
                    });

                    markers[index] = marker;
                }
                if (value.KilledSide == "CIV")
                {
                    var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: civ_unit}).addTo(civkills);
                    marker.bindPopup(popup, {
                        showOnMouseOver: true,
                        offset: new L.Point(0, 0)
                    });

                    markers[index] = marker;
                }

                break;

            case "GetIn":

                var posx = value.unitGeoPos[0];
                var posy = value.unitGeoPos[1];
                var multiplier = size / {{$ao->size}};
                output = value.Map + ' - Grid:' + value.unitPos + ' - ' + value.gameTime + ' local<br><a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="highlight"> ' + value.PlayerName + '</span></a> got in a ' + value.vehicleType;

                break;

            case "GetOut":

                var posx = value.unitGeoPos[0];
                var posy = value.unitGeoPos[1];
                var multiplier = size / {{$ao->size}};
                output = value.Map + ' - Grid:' + value.unitPos + ' - ' + value.gameTime + ' local<br><a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="highlight"> ' + value.PlayerName + '</span></a> got out of a ' + value.vehicleType;

                break;

            case "OperationStart":

                output = value.Map + ' - ' + value.gameTime + ' local<br>Operation <span class="highlight2">' + value.Operation + '</span> has been launched.';

                break;

            case "OperationFinish":

                output = value.Map + ' - ' + value.gameTime + ' local<br>Operation <span class="highlight2">' + value.Operation + '</span> has ended after ' + value.timePlayed + ' minutes.';

                break;

            case "Hit":

                if(!value.PlayerHit){
                    output = value.Map + ' - Grid:' + value.hitPos + ' - ' + value.gameTime + ' local<br>' + value.sourcefaction + ' ' + value.sourceType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) has scored a hit on a ' + value.hitfaction + ' ' + value.hitType + '.';
                }

                break;

            case "Missile":

                if (value.FiredAt == "true")
                {
                    output = value.Map + ' - Grid:' + value.targetPos + ' - ' + value.gameTime + ' local<br>' + value.targetFaction + ' ' + value.targetType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) has been engaged by a ' + value.sourceFaction + ' ' + value.sourceType + '.';
                } else {
                    output = value.Map + ' - Grid:' + value.sourcePos + ' - ' + value.gameTime + ' local<br>' + value.sourceFaction + ' ' + value.sourceType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>)</span><b> is engaging ' + value.targetFaction + value.targetType + ' with a ' + value.Weapon + ' from ' + value.Distance + 'm using a ' + value.projectile;
                }

                break;

            default:

                break;
        }

    output = index + " - " + output;

        return output;
    }

    /*
     * Delete all leaflet markers
     */
    function clearAllMarkers() {
        for(i=0;i<markers.length;i++) {
            map.removeLayer(markers[i]);
        }

        markers = [];
    }

    /*
     * Close all leaflet popups
     */
    function closeAllPopups() {
        if(markers.length > 0){
            for(i=0;i<markers.length;i++) {
                if(typeof(markers[i]) != 'undefined'){
                    if(typeof(markers[i].closePopup) !== 'undefined' && typeof(markers[i].closePopup) === 'function'){
                        markers[i].closePopup();
                    }
                }
            }
        }
    }

    /*
     * On change of current event on the timeline
     */
    function handleTimelineChange(index,data,allData) {

        closeAllPopups();

        if(markers[index]){
            console.log(markers[index]);
            if(typeof(markers[index].openPopup) !== 'undefined' && typeof(markers[index].openPopup) === 'function'){
                markers[index].openPopup();
                map.setView(markers[index].getLatLng(), 8);
                //map.panTo(markers[index].getLatLng());
            }
        }
        timelineData = allData;
    }

    /*
     * Make the timeline jump to a particular slide
     */
    function jumpTimelineTo(index) {
        id = timelineData[index].uniqueid;
        VMM.fireEvent("#marker_" + id + " .flag","click");
    }

    /*
     * Play timeline forward
     */
    currentTimeout = null;

    function playForward() {
        stop();
        (function _playForwardLoop() {

            if (gotoTimelineNext()) {
                currentTimeout = setTimeout(_playForwardLoop, 3000);
                return;
            }

            gotoTimelineStart();
            currentTimeout = setTimeout(_playForwardLoop, 6000);
        }());
    }

    function gotoTimelineStart() {
        $(".vco-toolbar .back-home").trigger("click");
    }

    function gotoTimelineNext() {
        var $next = $(".vco-slider .nav-next:visible");
        if ($next.length !== 1) {
            return false;
        }

        $next.trigger("click");
        return true;
    }

    /*
     * Play timeline reverse
     */
    function playReverse() {
        stop();
        (function _playReverseLoop() {

            if (gotoTimelinePrev()) {
                currentTimeout = setTimeout(_playReverseLoop, 3000);
                return;
            }

            gotoTimelineEnd();
            currentTimeout = setTimeout(_playReverseLoop, 6000);
        }());
    }

    function gotoTimelineEnd() {
        jumpTimelineTo((timelineData.length)-1);
    }

    function gotoTimelinePrev() {
        var $prev = $(".vco-slider .nav-previous:visible");
        if ($prev.length !== 1) {
            return false;
        }

        $prev.trigger("click");
        return true;
    }

    /*
     * Stop playing
     */
    function stop() {
        clearTimeout(currentTimeout);
    }

</script>

<div id="warroom_overview">
    @include('warroom/tables/op_overview')
</div>

<div id="warroom_timeline_container">
    <div id="warroom_timeline_loading"></div>
    <div id="warroom_timeline"></div>
    <div id="warroom_timeline_controls">
        <a class="btn btn-yellow btn-lg" href="javascript:void(0)" id="prev">Load previous 50 events</a>
        <a class="btn btn-yellow btn-lg" href="javascript:void(0)" id="next">Load next 50 events</a>
        <a class="btn btn-yellow btn-lg" href="javascript:jumpTimelineTo(20)">JUMP</a>
        <a class="btn btn-yellow btn-lg" href="javascript:playForward()">PLAY FORWARD</a>
        <a class="btn btn-yellow btn-lg" href="javascript:playReverse()">PLAY REVERSE</a>
        <a class="btn btn-yellow btn-lg" href="javascript:stop()">STOP PLAYBACK</a>
        <a class="btn btn-yellow btn-lg" href="javascript:gotoTimelinePrev()">PREV</a>
        <a class="btn btn-yellow btn-lg" href="javascript:gotoTimelineNext()">NEXT</a>

    </div>
</div>


@stop