@extends('warroom.layouts.operations')

{{-- Content --}}
@section('content')

<div id="map" style="height: 900px;"></div>

<script type="text/javascript">

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

    $(document).ready(function() {

        cursor = 0;
        itemsPerPage = 50;

        loadData(cursor);

        $(".trigger").click(function(){
            $(".panel").toggle("fast");
            $(this).toggleClass("active");
            return false;
        });

        $("#prev").click(function(){
            console.log("PREV");
            $("#warroom_timeline_loading").fadeIn();
            $("#warroom_timeline").remove();
            $('<div id="warroom_timeline"></div>').insertBefore("#warroom_timeline_controls");
            loadData(itemsPerPage);
        });

        $("#next").click(function(){
            console.log("NEXT");
            $("#warroom_timeline_loading").fadeIn();
            $("#warroom_timeline").remove();
            $('<div id="warroom_timeline"></div>').insertBefore("#warroom_timeline_controls");
            loadData(-itemsPerPage);
        });

        $(window).on("resize", function() {
            $("#map").height($(window).height()).width($(window).width());
            map.invalidateSize();
        }).trigger("resize");

        map.on('enterFullscreen', function(){
            console.log('entered fullscreen');
        });

        map.on('exitFullscreen', function(){
            console.log('exited fullscreen');
        });

    });

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

    function loadData(skip) {

        cursor = cursor + skip;

        if(cursor <= 0){
            cursor = 0;
        }

        console.log(cursor);

        $.getJSON("{{ URL::to('/') }}/api/oplivefeedpaged?name={{ $name }}&clan={{ $clan->tag }}&map={{ $ao->configName }}&limit="+itemsPerPage+"&skip="+cursor, function( data ) {

            if(data.error) {
                console.log(data);
            }

            var timelineData = new Object();
            timelineData.timeline = new Object();
            timelineData.timeline.headline = "TEST";
            timelineData.timeline.type = "default";
            timelineData.timeline.text = "CHEESE";
            timelineData.timeline.startDate = "2014,03,30,11,00";
            timelineData.timeline.date = [];

            var startDate = '';
            var endDate = '';

            $.each( data.rows, function( key, val ) {

                eventObj = val.value;

                //console.log(eventObj.realTime);

                var parsedDateArray = eventObj.realTime.split(" ");
                var parsedDayArray = parsedDateArray[0].split("/");
                var parsedTimeArray = parsedDateArray[1].split(":");

                //console.log(parsedDayArray);
                //console.log(parsedTimeArray);

                startDate = parsedDayArray[2] + ',' + parsedDayArray[1] + ',' + parsedDayArray[0] + ',' + parsedTimeArray[0] + ',' + parsedTimeArray[1] + ',' + parsedTimeArray[2];
                endDate = parsedDayArray[2] + ',' + parsedDayArray[1] + ',' + parsedDayArray[0] + ',' + parsedTimeArray[0] + ',' + parsedTimeArray[1] + ',' + (parseInt(parsedTimeArray[2]) + 1);

                //console.log(startDate);
                //console.log(endDate);

                var event = new Object();
				
				//Start of Gunny's Event Data WIP
						var action = eventObj.Event;
						var output = '';
				
				 if (action == "Kill")
                {
				  output = eventObj.Map + ' - Grid:' + eventObj.KilledPos + ' - ' + eventObj.gameTime + ' local<br>' + eventObj.Killedfaction + ' ' + eventObj.KilledType + '<a href=http://alivemod.com/war-room/showpersonnel/' + eventObj.Player +'><span class="highlight"> ' + eventObj.PlayerName + '</span></a> has been KIA';}
				  else {
				}
				//END of Gunny's Event data WIP
					

                event.startDate = startDate;
                event.endDate = startDate;
                event.headline = eventObj.Event;
                event.text = output;
                event.asset = new Object();
                event.asset.media = '';
                event.asset.credit = '';
                event.asset.caption = '';

                timelineData.timeline.date.push(event);

            });

            if(data.rows.length == 0){
                return;
            }

            if(data.rows.length < itemsPerPage){
                var diff = itemsPerPage - data.rows.length;
                for(var i = 0; i < diff; i++){

                    var event = new Object();
					

                    event.startDate = startDate;
                    event.endDate = endDate;
                    event.headline = '';
                    event.text = '';
                    event.asset = new Object();
                    event.asset.media = '';
                    event.asset.credit = '';
                    event.asset.caption = '';

                    timelineData.timeline.date.push(event);
                }
            }

            createStoryJS({
                type:		'timeline',
                width:		'100%',
                height:		'320',
                source:		timelineData,
                embed_id:	'warroom_timeline',
                start_at_end: true,
                debug:		false
            });

            $("#warroom_timeline_loading").fadeOut();

        });
    }

</script>

<div id="warroom_overview">
    @include('warroom/tables/op_overview')
</div>

<div id="warroom_timeline_container">
    <div id="warroom_timeline_loading"></div>
    <div id="warroom_timeline"></div>
    <div id="warroom_timeline_controls">
        <a class="btn btn-yellow btn-lg" href="javascript:void(0)" id="prev">
        Load previous 50 events</a>
        <a class="btn btn-yellow btn-lg" href="javascript:void(0)" id="next">Load next 50 events</a>
    </div>
</div>


@stop