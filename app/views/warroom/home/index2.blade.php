@extends('warroom.layouts.content')

{{-- Content --}}
@section('content')
 
<div id="map" style="height: 900px;"></div>

<script type="text/javascript">

		L.Map = L.Map.extend({
			openPopup: function(popup) {
				//        this.closePopup();  // just comment this
				this._popup = popup;
		
				return this.addLayer(popup).fire('popupopen', {
					popup: this._popup
				});
			}
		});
		
		L.Popup = L.Popup.extend({
			
		});

        var map = L.map('map', {
			minZoom: 0,
			maxZoom: 5,
			zoomControl: false,
            attributionControl: false,
            crs: L.CRS.Simple
		}).setView([4674,3845], 2);
		
		var southWest = map.unproject([0,1654], map.getMaxZoom());
		var northEast = map.unproject([8192,6400], map.getMaxZoom());
		map.setMaxBounds(new L.LatLngBounds(southWest, northEast));
		L.tileLayer("{{ URL::to('/') }}/maps/globalmap3/{z}/{x}/{y}.png" , {
            attribution: 'ALiVE',
            tms: true	//means invert.
        }).addTo(map);
		
		map.addControl(L.control.zoom({
			position: "topright"
		}));
		
		var items = {};
				
    $(document).ready(function() {
		$(".trigger").click(function(){
			$(".panel").toggle("fast");
			$(this).toggleClass("active");
			return false;
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

</script>

@foreach ($allAOs as $ao)

<script type="text/javascript">

	var ajaxUrl = '{{ URL::to('/') }}/api/maptotals?name={{$ao->configName}}';
	 $.getJSON(ajaxUrl, function(data) {

		var mapdata = data;
		var marker = new MyCustomMarker(map.unproject([{{$ao->imageMapX}},{{$ao->imageMapY}}], map.getMaxZoom()), {
			icon: hostileIcon
		});
	
		var popup = L.popup()
			.setContent("<div class='strip'>AO</div>" +
                        "<div class='ao-popup'>" +
                        "<p>" +
                        "<span class='title'>{{$ao->name}}</span></br>" +
						"<img src='{{ $ao->image->url('thumbAO') }}' ></br>" +
                        "<span class='highlight'>OPS:</span> " + mapdata.Operations + " <span class='highlight'>| EKIA:</span> " + mapdata.Kills + " <span class='highlight'>| LOSSES:</span> " + mapdata.Deaths + "</br>" +
                        "<span class='highlight'>HRS:</span> " + Math.round((mapdata.CombatHours / 60)*10)/10 + " <span class='highlight'>| AMMO:</span> " + mapdata.ShotsFired + " <span class='highlight'>| UNITS:</span> " + mapdata.Operations +
                        "</p>" +
                        "</div>");


			
		marker.bindPopup(popup, {
            showOnMouseOver: true,
            offset: new L.Point(-4, -12)
        });
		map.addLayer(marker);
	 });
	 
</script>

@endforeach

@foreach ($devs as $dprofile)
 <?php
    $clan = $dprofile->clan;
    $orbattype = $dprofile->orbat['type'];
    $orbatsize = $dprofile->orbat['size'];

    $icon = '';
    $name = '';
    $size = '';
    $sizeicon = '';

    if(count($orbattype) > 0){
        $icon = $orbattype[0]->icon;
        $name = $orbattype[0]->name;
    }
    if(count($orbatsize) > 0){
        $size = $orbatsize[0]->name;
        $sizeicon = $orbatsize[0]->icon;
    }
?>

<script type="text/javascript">
	var ajaxUrl = '{{ URL::to('/') }}/api/devcredits?id={{$dprofile->a3_id}}';
	 $.getJSON(ajaxUrl, function(data) {
		var myIcon = new groupIcon({	iconSize:     [40, 40], // size of the icon
										shadowSize:   [40, 40], // size of the shadowicon
										shadowUrl: '{{ URL::to('/') }}/img/icons/w_{{$sizeicon}}.png',
										iconUrl: '{{ URL::to('/') }}/img/icons/b_{{$icon}}.png'});
		var marker = new MyCustomMarker(map.unproject([data.globalX,data.globalY], map.getMaxZoom()), {
			icon: myIcon
		});
		
		var popup = L.popup()
			.setContent("<div class='strip'>Lead Unit</div>" +
                        "<div class='unit-popup'>" +
                        "<p>" +
                        "<span class='title'>{{$clan->name}} [{{$clan->tag}}]</span></br>" +
                        "<span class='highlight'>{{$name}} {{$size}}</span></br>" +
                        "<span class='highlight'>Cmdr:</span> " + data.PlayerName +
                        " <img src='{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($dprofile->country) }}.png' alt='{{ $dprofile->country_name }}' title='{{ $dprofile->country_name }}' width='16' height='16'/><br/>" +
                        "<span class='highlight'>Credits:</span> " + data.Credits +
                        "</p>" +
                        "</div>");
			
		marker.bindPopup(popup, {
            showOnMouseOver: true,
            offset: new L.Point(-3, -5)
		});
		map.addLayer(marker);
	 });

</script>

@endforeach

@foreach ($clans as $clan)
 <?php
 	$clanorbat = $clan->orbat();
    $orbattype = $clanorbat['type'];
    $orbatsize = $clanorbat['size'];

    $icon = '';
    $name = '';
    $size = '';
    $sizeicon = '';

    if(count($orbattype) > 0){
        $icon = $orbattype[0]->icon;
        $name = $orbattype[0]->name;
    }
    if(count($orbatsize) > 0){
        $size = $orbatsize[0]->name;
        $sizeicon = $orbatsize[0]->icon;
    }
	
	if (is_null ($clan->lat)) {
		$lat = rand(3000,4500);
	} else {
		$lat = $clan->lat;
	}
	if (is_null ($clan->lon)) {
		$lon = rand(1800,6400);
	} else {
		$lon = $clan->lon;
	}
?>

<script type="text/javascript">
	var ajaxUrl = '{{ URL::to('/') }}/api/grouptotalsbytag?id={{$clan->tag}}';
	 $.getJSON(ajaxUrl, function(data) {
			var myIcon = new groupIcon({iconUrl: '{{ URL::to('/') }}/img/icons/b_{{$icon}}.png'});
			var marker = new MyCustomMarker(map.unproject([{{$lon}},{{$lat}}], map.getMaxZoom()), {
				icon: myIcon
			});
			
			var popup = L.popup()
				.setContent("<table><tr><td colspan='2'><div class='strip'>Unit</div></td></tr><tr><td><img width='100' src='{{ $clan->avatar->url('thumb') }}' ></td><td>" +
							"<div class='unit-popup'>" +
							"<p>" +
							"<a href={{ URL::to('war-room/showorbat') }}/{{$clan->id}}><span class='title'>{{$clan->name}} [{{$clan->tag}}]</span></a></br>" +
							"<span class='highlight'>{{$name}} {{$size}}</span>" +
							" <img src='{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($clan->country) }}.png' alt='{{ $clan->country_name }}' title='{{ $clan->country_name }}' width='18' height='18'/><br/>" +
							 "<span class='highlight'>OPS:</span> " + data.Operations + " <span class='highlight'>| EKIA:</span> " + data.Kills + " <span class='highlight'>| LOSSES:</span> " + data.Deaths + "</br>" +
                        "<span class='highlight'>HRS:</span> " + Math.round((data.CombatHours / 60)*10)/10 + " <span class='highlight'>| AMMO:</span> " + data.ShotsFired + "</br>" +
						"<span class='highlight'>VEHICLE HRS:</span> " + Math.round((data.VehicleTime / 60)*10)/10 + " <span class='highlight'>| FLIGHT HRS:</span> " + Math.round((data.PilotTime / 60)*10)/10 + 
							"</p>" +
							"</div></td></tr></table>");
				
			marker.bindPopup(popup, {
				showOnMouseOver: true,
				offset: new L.Point(-3, -5)
			});
			map.addLayer(marker);
	});

</script>

@endforeach

<div id="warroom_overview">
    @include('warroom/tables/overview')
</div>

<div id="warroom_recent">
    <div class="strip">Recent Operations<span class="control" id="warroom_recent_toggle"><i class="fa fa-arrow-left"></i></span></div>
    @include('warroom/tables/recent_ops')
</div>

<div id="warroom_t1operators">
    <div class="strip">Tier 1 Operators<span class="control" id="warroom_t1_toggle"><i class="fa fa-arrow-left"></i></span></div>
    @include('warroom/tables/t1operators_home')
</div>

<div id="warroom_livefeed">
    <div class="strip clearfix"><span id="warroom_livefeed_toggle"><i class="fa fa-arrow-right"></i></span><span id="warroom_livefeed_label" class="control">Live event feed</span></div>
    @include('warroom/tables/live_feed')
</div>

<div id="warroom_charts">
    <div class="strip"><span class="control-center" id="warroom_charts_toggle"><i class="fa fa-arrow-down"></i></span></div>
    <div class="row">
        <div class="col-md-3">
            @include('warroom/charts/blu_losses')
        </div>
        <div class="col-md-3">
            @include('warroom/charts/opf_losses')
        </div>
        <div class="col-md-3">
            @include('warroom/charts/casualties')
        </div>
        <div class="col-md-3">
            @include('warroom/charts/ops')
        </div>
    </div>

</div>

@stop