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
	var westkilled = new L.LayerGroup();
    var eastkilled = new L.LayerGroup();
    var civkilled = new L.LayerGroup();
    var indykilled = new L.LayerGroup();
	var killlayer = new L.LayerGroup();
    var getIn = new L.LayerGroup();
    var getOut = new L.LayerGroup();
    var heals = new L.LayerGroup();


    var map = L.map('map', {
        minZoom: 0,
        maxZoom: mz,
        zoomControl: true,
        layers: [],
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
	
    var AO = L.tileLayer("http://db.alivemod.com/maps/{{ strtolower($ao->configName) }}/{z}/{x}/{y}.png" , {
        attribution: 'ALiVE',
        tms: true	//means invert.
    }).addTo(map);


    var baseLayer = {7
        "AO": AO
    };

    var overlays = {
        "West Killed": westkills,
        "East Killed": eastkills,
        "Indy Killed": indykills,
        "Civ Killed": civkills,
        "Get In": getIn,
        "Get Out": getOut,
        "Heals": heals
    };

    layerControl = L.control.layers(baseLayer, overlays,{position: 'topleft'});
    layerControl.addTo(map);

    var items = {};
    var markers = [];
	var killermrkrs = [];
	var aarmarkers = [];
	var polylines = [];
	var latlngs = Array();

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

        initialLoad = true;

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
        itemsPerPage = 100;

        loadData(cursor);

        $("#warroom_timeline_toggle").click(function(e){

            e.preventDefault();

            var toggler = $('#warroom_timeline_toggle');
            var container = $('#warroom_timeline_container');
            var leafletControls = $('.leaflet-left');
            var timeline = new TimelineLite();

            if(toggler.hasClass('clicked')){
                timeline.to(container, .2, {css:{top:-270}})
                timeline.to(leafletControls, .2, {css:{top:68}})
                toggler.html('<i class="fa fa-arrow-down"></i>');
                toggler.removeClass('clicked');
            } else {
                timeline.to(leafletControls, .2, {css:{top:260}})
                timeline.to(container, .2, {css:{top:-80}});
                toggler.html('<i class="fa fa-arrow-up"></i>');
                toggler.addClass('clicked');
            }
        });

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

    function dataFailed() {
        if(cursor > 0){
            cursor = cursor - itemsPerPage;
            loadData(0);
        }else{
            $("#warroom_timeline_container").remove();
        }
    }

    /*
     * Load data set for timeline and map markers
     */
    function loadData(skip) {

        cursor = cursor + skip;

        if(cursor <= 0){
            cursor = 0;
        }

        // clear existing map markers
        //clearAllMarkers();

        // get data from the cursor location onwards to limit
        $.getJSON("{{ URL::to('/') }}/api/oplivefeedpaged?name={{ $name }}&clan={{ $clan->tag }}&map={{ $ao->configName }}&limit="+itemsPerPage+"&skip="+cursor, function( data ) {

            if(data.error) {
                console.log("DATA ERROR!!");
            }

            // setup the main timeline data structure
            var timelineData = new Object();
            timelineData.timeline = new Object();
            timelineData.timeline.headline = "TEST";
            timelineData.timeline.type = "default";
            timelineData.timeline.text = "CHEESE";
            //timelineData.timeline.startDate = "2014,03,30,11,00";
            timelineData.timeline.date = [];

            var startDate = '';
            var endDate = '';
            var events = [];

            // loop loaded row data filter out some events
            $.each( data.rows, function( key, val ) {
                eventObj = val.value;
                if(eventObj.Event != 'PlayerFinish' && eventObj.Event != 'Hit'){
                    events.push(eventObj);
                }
            });

            var eventCount = events.length;
			//console.log(eventCount)

            // no data
            if(eventCount == 0){
                dataFailed();
                return;
            }

            // loop filtered event data create timeline objects
            $.each( events, function( key, val ) {

                eventObj = val;

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
                event.headline = output.shortDescription;
                event.text = output.description;
                event.asset = new Object();
                event.asset.media = '';
                event.asset.credit = '';
                event.asset.caption = '';

                // push the new event onto the stack
                timelineData.timeline.date.push(event);

                // increment counter
                eventCount--;

            });

			// Based on the timing of events, load the AAR data
			// get the AAR data
			var parts = events[events.length-1].realTime.match(/(\d+)/g);		
			var start = new Date(parseInt(parts[2]), parseInt(parts[1],10)-1, parseInt(parts[0],10)+1);
			start.setUTCHours(parseInt(parts[3],10));
			start.setUTCMinutes(parseInt(parts[4],10));
			start.setUTCSeconds(parseInt(parts[5],10));  	
				
			parts = events[0].realTime.match(/(\d+)/g);			
			var end = new Date(parseInt(parts[2]), parseInt(parts[1],10)-1, parseInt(parts[0],10)+1);
			end.setUTCHours(parseInt(parts[3],10));
			end.setUTCMinutes(parseInt(parts[4],10));
			end.setUTCSeconds(parseInt(parts[5],10));  	

			start = start.toISOString();
			end = end.toISOString();				
			
        	$.getJSON("{{ URL::to('/') }}/api/opliveaarfeedpaged?name={{ $name }}&clan={{ $clan->tag }}&map={{ $ao->configName }}&start="+start+"&end="+end, function( data ) {
				if(data.error) {
					console.log("DATA ERROR!!");
				}
				//console.log(data);
				var aarcount = 0;			
				// loop loaded row data and create aar map markers			
				$.each( data.rows, function( key, value ) {
					
					var aarData = value.value;
					var dater = aarData.realTime;
					
					// Place AAR record onto timeline
					
					// parse dates into timeline friendly format
					var parsedDateArray = dater.split(" ");
					var parsedDayArray = parsedDateArray[0].split("/");
					var parsedTimeArray = parsedDateArray[1].split(":");					
	
					startDate = parsedDayArray[2] + ',' + parsedDayArray[1] + ',' + parsedDayArray[0] + ',' + parsedTimeArray[0] + ',' + parsedTimeArray[1] + ',' + (parseInt(parsedTimeArray[2]));							
					// create the timeline event object
					var aevent = new Object();
					aevent.startDate = startDate;
					aevent.headline = 'AAR ' + startDate;
					aevent.text = '';
					aevent.asset = new Object();
					aevent.asset.media = '';
					aevent.asset.credit = '';
					aevent.asset.caption = '';
					aevent.asset.markers = [];
											
					// For each row, prepare information for marker
					$.each( aarData.data, function(index, val) {
						// loop through array
							aarObj = val;
							aevent.asset.markers.push(aarcount);
							prepareAAR(aarcount, aarObj, value.gameTime);
							aarcount++;
					});
					
					// push the new event onto the stack
					timelineData.timeline.date.push(aevent);

				});	
				
				// create or recreate the timeline
				createTimeline(timelineData);								
			});	
//			console.log(aarmarkers);
			console.log(timelineData);	
//			console.log(markers);			

            // hide the loading overlay
            $("#warroom_timeline_loading").fadeOut();

        });
    }

    /*
     * Create the timeline
     */
    function createTimeline(data) {
        // first load of page
        // create the timeline
        if(typeof VMM == 'undefined') {
            createStoryJS({
                type:		'timeline',
                width:		'100%',
                height:		'320',
                source:		data,
                embed: true,
                embed_id:	'warroom_timeline',
                start_at_end: true,
                start_zoom_adjust: '9',
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
                source:		data,
                embed: true,
                embed_id:	'warroom_timeline',
                start_at_end: true,
                start_zoom_adjust: '9',
                debug:		false,
                css: '{{ URL::to("/") }}/css/timeline.css',
                js: '{{ URL::to("/") }}/js/timeline.js'
            });
        }
    }

	function prepareAAR(index, value, gameTime) {
        var output = {};
		var posx = value.AAR_pos[0];
		var posy = value.AAR_pos[1];	
		var multiplier = size / {{ $ao->size }};
		
		if (value.AAR_isPlayer == "true") {
			output.description = gameTime + ' local<br><h2>' + value.AAR_fac + '<br><a href="http://alivemod.com/war-room/showpersonnel/' + value.AAR_playerUID +'" target="_blank"><span class="operation">' + value.AAR_name + '<br>' + value.AAR_class + '</span></h2><br/>' + value.AAR_group + '<br/><img src="{{ URL::to("img/classes/small/300px-Arma3_CfgWeapons_") }}' + value.AAR_weapon + '.png" onerror=this.style.display="none"></a><br/>Damage: ' + value.AAR_damage;
		} else {
			output.description = gameTime + ' local<br><h2>' + value.AAR_fac + '<br><span class="operation">' + value.AAR_name + '<br>' + value.AAR_class + '</span></h2><br/>' + value.AAR_group + '<br/><img src="{{ URL::to("img/classes/small/300px-Arma3_CfgWeapons_") }}' + value.AAR_weapon + '.png" onerror=this.style.display="none"></a><br/>Damage: ' + value.AAR_damage;
		}

        var popup = L.popup().setContent('<div class="admin-panel">' + output.description + '</div>');
		
		switch (value.AAR_side) {
			
			case "WEST": 
				var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: west_unit});
				break;
			
			case "EAST": 
				var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: east_unit});
				break;
			
			case "GUER": 
				var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: indy_unit});
				break;
				
			default: 
				var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: civ_unit});				
		}

		marker.bindPopup(popup, {
			showOnMouseOver: true,
			offset: new L.Point(1, 1)
		});		

        aarmarkers[index] = marker;
	}
	
    /*
     * Setup map marker and prepare output for timeline and marker display
     */
    function prepareEvent(index, value) {

        var action = value.Event;
        var output = {};
        output.shortDescription = '';
        output.description = '';

        switch(action){

            case "Kill":

                var posx = value.KilledGeoPos[0];
                var posy = value.KilledGeoPos[1];
				var killposx = value.KillerGeoPos[0];
				var killposy = value.KillerGeoPos[1];
                var multiplier = size / {{$ao->size}};

                if (value.Death == "true")
                {

                    if(value.Killed === value.Killer){
                        output.shortDescription = value.PlayerName + ' has been KIA';

                        output.description =
                            value.gameTime + ' local<br><h2><i class="fa fa-ban"></i> <a href="http://alivemod.com/war-room/showpersonnel/' + value.Player +'" target="_blank"><span class="operation">' + value.PlayerName + '</span></h2><br/></a>' +
                            value.Killedfaction + ' ' + value.KilledType + ' has been KIA';
                    }else{

                        output.shortDescription = value.Killedfaction + ' ' + value.KilledType + ' kills ' + value.PlayerName;

                        output.description =
                            value.gameTime + ' local<br><h2><i class="fa fa-ban"></i> <a href="http://alivemod.com/war-room/showpersonnel/' + value.Player +'" target="_blank"><span class="operation">' + value.PlayerName + '</span></h2><br/></a>' +
                            value.Killedfaction + ' ' + value.KilledType +
                            ' killed by ' + value.Killerfaction + '<span class="highlight"> ' + value.KillerType + '</span> with an <br/>' + value.Weapon + ' from ' + value.Distance + 'm';
                    }



                } else {
                    if (value.KilledClass != "Infantry")
                    {

                        output.shortDescription = value.Killedfaction + ' ' + value.KilledType + ' has been destroyed';

                        output.description =
                            value.gameTime + ' local<br><h2><i class="fa fa-exclamation-triangle"></i> </h2><br/>' +
                            value.Killedfaction + ' <span class="highlight">' + value.KilledType + '</span> has been destroyed';

                    } else {

                        output.shortDescription = '<i class="fa fa-dot-circle-o"></i>' + value.PlayerName + ' kills ' + value.Killedfaction + ' ' + value.KilledType;

                        output.description =
                            value.gameTime + ' local<br><h2><i class="fa fa-dot-circle-o"></i> <a href="http://alivemod.com/war-room/showpersonnel/' + value.Player +'" target="_blank"><span class="operation">' + value.PlayerName + '</span></h2><br/></a>' +
                            value.Killerfaction + ' ' + value.KillerType +
                            ' kills ' + value.Killedfaction + '<span class="highlight"> ' + value.KilledType + '</span> with an <br/>' + value.Weapon + ' from ' + value.Distance + 'm';

                    }
                }

                var popup = L.popup().setContent('<div class="admin-panel">' + output.description + '</div>');
				

                if (value.KilledSide == "WEST")
                {
                    var markerlayer = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(westkills);
					var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: west_unit})
						if (value.KillerSide == "EAST")
							{
								var killermrkr = L.marker(map.unproject([killposx * multiplier,size - (killposy * multiplier)], map.getMaxZoom()), {icon: east_unit}).addTo(westkilled);
							}
							
							if (value.KillerSide == "GUER")
							{
								var killermrkr = L.marker(map.unproject([killposx * multiplier,size - (killposy * multiplier)], map.getMaxZoom()), {icon: indy_unit}).addTo(westkilled);
							}
                    marker.bindPopup(popup, {
                        showOnMouseOver: true,
                        offset: new L.Point(0, 0)
                    });
					
					killermrkrs[index] = killermrkr;
                    markers[index] = marker;

                }
                if (value.KilledSide == "EAST")
                {
                    var markerlayer = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: east_unit}).addTo(eastkills);
					var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: east_unit})
					//added killer marker
							if (value.KillerSide == "WEST")
							{
								var killermrkr = L.marker(map.unproject([killposx * multiplier,size - (killposy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(westkilled);
							}
							
							if (value.KillerSide == "GUER")
							{
								var killermrkr = L.marker(map.unproject([killposx * multiplier,size - (killposy * multiplier)], map.getMaxZoom()), {icon: indy_unit}).addTo(westkilled);
							}
                    marker.bindPopup(popup, {
                        showOnMouseOver: true,
                        offset: new L.Point(0, 0)
                    });
					killermrkrs[index] = killermrkr;
                    markers[index] = marker;

                }
                if (value.KilledSide == "GUER")
                {
                    var markerlayer = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: indy_unit}).addTo(indykills);
					var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: indy_unit});
					
											if (value.KillerSide == "WEST")
							{
								var killermrkr = L.marker(map.unproject([killposx * multiplier,size - (killposy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(westkilled);
							}
							
							if (value.KillerSide == "EAST")
							{
								var killermrkr = L.marker(map.unproject([killposx * multiplier,size - (killposy * multiplier)], map.getMaxZoom()), {icon: east_unit}).addTo(westkilled);
							}
                    marker.bindPopup(popup, {
                        showOnMouseOver: true,
                        offset: new L.Point(0, 0)
                    });

                    killermrkrs[index] = killermrkr;
                    markers[index] = marker;
                }
                if (value.KilledSide == "CIV")
                {
                    var markerLayer = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: civ_unit}).addTo(civkills);
					var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: civ_unit});
					if (value.KillerSide == "WEST")
							{
								var killermrkr = L.marker(map.unproject([killposx * multiplier,size - (killposy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(westkilled);
							}
							
							if (value.KillerSide == "EAST")
							{
								var killermrkr = L.marker(map.unproject([killposx * multiplier,size - (killposy * multiplier)], map.getMaxZoom()), {icon: east_unit}).addTo(westkilled);
							}
							if (value.KillerSide == "GUER")
							{
								var killermrkr = L.marker(map.unproject([killposx * multiplier,size - (killposy * multiplier)], map.getMaxZoom()), {icon: indy_unit}).addTo(westkilled);
							}
                    marker.bindPopup(popup, {
                        showOnMouseOver: true,
                        offset: new L.Point(0, 0)
                    });
					killermrkrs[index] = killermrkr;
                    markers[index] = marker;
                }

                break;

            case "Heal":

                var posx = value.medicGeoPos[0];
                var posy = value.medicGeoPos[1];
                var multiplier = size / {{$ao->size}};

                if(value.medic === value.patient){

                    output.shortDescription = '<a href="http://alivemod.com/war-room/showpersonnel/' + value.Player +'" target="_blank"><span class="highlight"> ' + value.PlayerName + '</span></a> heals self';

                    output.description =
                        value.gameTime + ' local<br><h2><i class="fa fa-medkit"></i> <a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></h2><br/></a>' +
                        ' Heals self ';

                }else{

                    output.shortDescription = '<a href="http://alivemod.com/war-room/showpersonnel/' + value.Player +'" target="_blank"><span class="highlight"> ' + value.PlayerName + '</span></a> heals ' + value.patientType;

                    output.description =
                        value.gameTime + ' local<br><h2><i class="fa fa-medkit"></i> <a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></h2><br/></a>' +
                        ' Heals ' + value.patientfaction + '<span class="highlight"> ' + value.patientType + ' ' + value.patient;

                }



                var popup = L.popup().setContent('<div class="admin-panel">' + output.description + '</div>');
                var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(heals);
                marker.bindPopup(popup, {
                    showOnMouseOver: true,
                    offset: new L.Point(0, 0)
                });

                markers[index] = marker;

                break;

            case "GetIn":

                var posx = value.unitGeoPos[0];
                var posy = value.unitGeoPos[1];
                var multiplier = size / {{$ao->size}};

                output.shortDescription = '<a href="http://alivemod.com/war-room/showpersonnel/' + value.Player +'" target="_blank"><span class="highlight"> ' + value.PlayerName + '</span></a> got in a ' + value.vehicleType;

                output.description =
                    value.gameTime + ' local<br><h2><i class="fa fa-external-link-square"></i> <a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></h2><br/></a>' +
                    ' got in a ' + value.vehicleType;

                var popup = L.popup().setContent('<div class="admin-panel">' + output.description + '</div>');
                var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(getIn);
                marker.bindPopup(popup, {
                    showOnMouseOver: true,
                    offset: new L.Point(0, 0)
                });

                markers[index] = marker;

                break;

            case "GetOut":

                var posx = value.unitGeoPos[0];
                var posy = value.unitGeoPos[1];
                var multiplier = size / {{$ao->size}};

                output.shortDescription = '<a href="http://alivemod.com/war-room/showpersonnel/' + value.Player +'" target="_blank"><span class="highlight"> ' + value.PlayerName + '</span></a> got out of a ' + value.vehicleType;

                output.description =
                    value.gameTime + ' local<br><h2><i class="fa fa-external-link"></i> <a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></h2><br/></a>' +
                    ' got out of a ' + value.vehicleType;

                var popup = L.popup().setContent('<div class="admin-panel">' + output.description + '</div>');
                var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(getOut);
                marker.bindPopup(popup, {
                    showOnMouseOver: true,
                    offset: new L.Point(0, 0)
                });

                markers[index] = marker;

                break;

            case "ParaJump":

                var posx = value.unitGeoPos[0];
                var posy = value.unitGeoPos[1];
                var multiplier = size / {{$ao->size}};

                output.shortDescription = '<a href="http://alivemod.com/war-room/showpersonnel/' + value.Player +'" target="_blank"><span class="highlight"> ' + value.PlayerName + '</span></a> parajumps from a ' + value.vehicleType;

                output.description =
                    value.gameTime + ' local<br><h2><i class="fa fa-level-down"></i> <a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></h2><br/></a>' +
                    ' parajumped from a ' + value.vehicleType + ' at height ' + value.jumpHeight;

                var popup = L.popup().setContent('<div class="admin-panel">' + output.description + '</div>');
                var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(getOut);
                marker.bindPopup(popup, {
                    showOnMouseOver: true,
                    offset: new L.Point(0, 0)
                });

                markers[index] = marker;

                break;

            case "Landed":

                var posx = value.vehicleGeoPos[0];
                var posy = value.vehicleGeoPos[1];
                var multiplier = size / {{$ao->size}};

                output.shortDescription = '<a href="http://alivemod.com/war-room/showpersonnel/' + value.Player +'" target="_blank"><span class="highlight"> ' + value.PlayerName + '</span></a> landed a ' + value.vehicleType;

                output.description =
                    value.gameTime + ' local<br><h2><i class="fa fa-fighter-jet"></i> <a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></h2><br/></a>' +
                    ' landed a ' + value.vehicleType;

                var popup = L.popup().setContent('<div class="admin-panel">' + output.description + '</div>');
                var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(getOut);
                marker.bindPopup(popup, {
                    showOnMouseOver: true,
                    offset: new L.Point(0, 0)
                });

                markers[index] = marker;

                break;

            case "CombatDive":

                var posx = value.unitGeoPos[0];
                var posy = value.unitGeoPos[1];
                var multiplier = size / {{$ao->size}};

                output.shortDescription = '<a href="http://alivemod.com/war-room/showpersonnel/' + value.Player +'" target="_blank"><span class="highlight"> ' + value.PlayerName + '</span></a> combat dive ';

                output.description =
                    value.gameTime + ' local<br><h2><i class="fa fa-arrow-circle-down"></i> <a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></h2><br/></a>' +
                        ' took a combat dive for ' + value.diveTime + ' minutes';

                var popup = L.popup().setContent('<div class="admin-panel">' + output.description + '</div>');
                var marker = L.marker(map.unproject([posx * multiplier,size - (posy * multiplier)], map.getMaxZoom()), {icon: west_unit}).addTo(getOut);
                marker.bindPopup(popup, {
                    showOnMouseOver: true,
                    offset: new L.Point(0, 0)
                });

                markers[index] = marker;

                break;


            case "OperationStart":

                output.shortDescription = 'Operation <span class="highlight2">' + value.Operation + '</span> has been launched.';

                output.description = value.Map + ' - ' + value.gameTime + ' local<br>Operation <span class="highlight2">' + value.Operation + '</span> has been launched.';

                break;

            case "OperationFinish":

                output.shortDescription = 'Operation <span class="highlight2">' + value.Operation + '</span> has ended after ' + value.timePlayed + ' minutes.';

                output.description = value.Map + ' - ' + value.gameTime + ' local<br>Operation <span class="highlight2">' + value.Operation + '</span> has ended after ' + value.timePlayed + ' minutes.';

                break;

            case "Hit":

                if(!value.PlayerHit){

                    output.shortDescription = value.sourcefaction + ' ' + value.sourceType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) has scored a hit on a ' + value.hitfaction + ' ' + value.hitType + '.';

                    output.description = value.Map + ' - Grid:' + value.hitPos + ' - ' + value.gameTime + ' local<br>' + value.sourcefaction + ' ' + value.sourceType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) has scored a hit on a ' + value.hitfaction + ' ' + value.hitType + '.';
                }

                break;

            case "Missile":

                if (value.FiredAt == "true")
                {

                    output.shortDescription = value.targetFaction + ' ' + value.targetType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) has been engaged by a ' + value.sourceFaction + ' ' + value.sourceType + '.';

                    output.description = value.Map + ' - Grid:' + value.targetPos + ' - ' + value.gameTime + ' local<br>' + value.targetFaction + ' ' + value.targetType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>) has been engaged by a ' + value.sourceFaction + ' ' + value.sourceType + '.';

                } else {

                    output.shortDescription = value.sourceFaction + ' ' + value.sourceType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>)</span><b> is engaging ' + value.targetFaction + value.targetType + ' with a ' + value.Weapon + ' from ' + value.Distance + 'm using a ' + value.projectile;

                    output.description = value.Map + ' - Grid:' + value.sourcePos + ' - ' + value.gameTime + ' local<br>' + value.sourceFaction + ' ' + value.sourceType + '(<a href=http://alivemod.com/war-room/showpersonnel/' + value.Player +'><span class="operation">' + value.PlayerName + '</span></a>)</span><b> is engaging ' + value.targetFaction + value.targetType + ' with a ' + value.Weapon + ' from ' + value.Distance + 'm using a ' + value.projectile;

                }

                break;

            default:

                console.log(value);

                break;
        }

        return output;
    }

    /*
     * Delete all leaflet markers
     */
    function clearAllMarkers() {
        for (key in markers) {
            if (String(parseInt(key, 10)) === key && markers.hasOwnProperty(key)) {
                map.removeLayer(markers[key]);
            }
        }
		 for (key in killermrkrs) {
            if (String(parseInt(key, 10)) === key && killermrkrs.hasOwnProperty(key)) {
                map.removeLayer(killermrkrs[key]);
            }
        }
        markers = [];
		killermrkrs = [];

		
    }

    /*
     * Close all leaflet popups
     */
    function closeAllPopups() {
        if(markers.length > 0){
            for (key in markers) {
                if (String(parseInt(key, 10)) === key && markers.hasOwnProperty(key)) {
                    if(typeof(markers[key]) != 'undefined'){
                        if(typeof(markers[key].closePopup) !== 'undefined' && typeof(markers[key].closePopup) === 'function'){
                            markers[key].closePopup();
                        }

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
//GUNNY Added killer pos marker and polyline to timeline
//If the layer was prevuously created clear it
		 if (map.hasLayer(killlayer)){
				killlayer.clearLayers();
		 }
		 
		 console.log(index + ' ' + data.asset.markers);
		
        if(typeof(data.asset.markers) == 'undefined'){
            if(typeof(markers[index].openPopup) !== 'undefined' && typeof(markers[index].openPopup) === 'function'){
//add new layer to map
				map.addLayer(killlayer);
				markers[index].addTo(killlayer);
                markers[index].openPopup();
				latlngs = [];
				  if(killermrkrs[index]){
	
					killermrkrs[index].addTo(killlayer);
					latlngs.push(killermrkrs[index].getLatLng());
					latlngs.push(markers[index].getLatLng());
					
					var arrow = L.polyline(latlngs, {color: 'black',opacity: 1,weight: 2}).addTo(killlayer);
					var arrowHead = L.polylineDecorator(arrow).addTo(killlayer);
					
					var arrowOffset = 0;
					var anim = window.setInterval(function() {
						arrowHead.setPatterns([
							{offset: arrowOffset+'%', repeat: 0, symbol: L.Symbol.arrowHead({pixelSize: 13, polygon: false, pathOptions: {stroke: true,color: '#000',opacity: 1,weight: 2}})}
						])
						if(++arrowOffset > 100)
							arrowOffset = 0;
					}, 100);
				}
				
				
                if(initialLoad){
                    map.setView(markers[index].getLatLng(),4);
                    initialLoad = false;
                }else{
                    map.panTo(markers[index].getLatLng());
                }
				
            }
        } else {			
				// Get AAR marker
				map.addLayer(killlayer);
				$.each(data.asset.markers, function( index, value ) {
					aarmarkers[value].addTo(killlayer);
				});
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
        <div class="btn-group" role="toolbar">
            <a class="btn btn-white btn-sm" title="Load previous 100 events" href="javascript:void(0)" id="prev"><i class="fa fa-fast-backward"></i></a>
            <a class="btn btn-white btn-sm" title="Load next 100 events" href="javascript:void(0)" id="next"><i class="fa fa-fast-forward"></i></a>
        </div>
        <div class="btn-group" role="toolbar">
            <a class="btn btn-white btn-sm" title="Play reverse" onclick="playReverse()"><i class="fa fa-backward"></i></a>
            <a class="btn btn-white btn-sm" title="Step to previous event" onclick="gotoTimelinePrev()"><i class="fa fa-step-backward"></i></a>
            <a class="btn btn-white btn-sm" title="Stop playing" onclick="stop()"><i class="fa fa-stop"></i></a>
            <a class="btn btn-white btn-sm" title="Step to next event" onclick="gotoTimelineNext()"><i class="fa fa-step-forward"></i></a>
            <a class="btn btn-white btn-sm" title="Play forward" onclick="playForward()"><i class="fa fa-forward"></i></a>
        </div>
        <div class="btn-group" role="toolbar">
            <a class="btn btn-white btn-sm clicked" title="Hide timeline" id="warroom_timeline_toggle"><i class="fa fa-arrow-up"></i></a>
        </div>
    </div>
</div>


@stop