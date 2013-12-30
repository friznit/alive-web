@extends('warroom.layouts.home')

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
			iconSize:     [35, 35], // size of the icon
			shadowSize:   [35, 35], // size of the shadow
			iconAnchor:   [20, 20], // point of the icon which will correspond to markers location
			shadowAnchor: [15, 15],  // the same for the shadow
			popupAnchor:  [105, 105] // point from which the popup should open relative to the iconAnchor
		}
	});

</script>

<?php
	$allAOs = AO::all();
?>

@foreach ($allAOs as $ao)

<script type="text/javascript">

	var ajaxUrl = '{{ URL::to('/') }}/api/maptotals?name={{$ao->configName}}';
	 $.getJSON(ajaxUrl, function(data) {
		var mapdata = data;
		var marker = new MyCustomMarker(map.unproject([{{$ao->imageMapX}},{{$ao->imageMapY}}], map.getMaxZoom()), {
			icon: hostileIcon
		});
	
		var popup = L.popup()
			.setContent("<div class='war-room_popup'><p><span class='title'>{{$ao->name}}</span></br>OPS: " + mapdata.Operations + " | EKIA: " + mapdata.Kills + " | LOSSES: " + mapdata.Deaths + "</br>HRS: " + Math.round((mapdata.CombatHours / 60)*10)/10 + " | AMMO: " + mapdata.ShotsFired + " | UNITS: " + mapdata.Operations + "</p></div>");
			
		marker.bindPopup(popup, { 			
				showOnMouseOver: true
		});
		map.addLayer(marker);
	 });
	 
</script>

@endforeach

<?php
	$devs= Profile::where('remark', '=', 'Developer')->get();
?>

@foreach ($devs as $profile)
 <?php
	$clan = Clan::where('id', '=', $profile->clan_id)->firstOrFail();
	$orbattype = DB::select('select * from orbattypes where type = ?', array($clan->type));
	$orbatsize = DB::select('select * from orbatsizes where type = ?', array($clan->size));
	$icon = $orbattype[0]->icon;
	$name = $orbattype[0]->name;
	$size = $orbatsize[0]->name;
	$sizeicon = $orbatsize[0]->icon;
?>

<script type="text/javascript">
	var ajaxUrl = '{{ URL::to('/') }}/api/devcredits?id={{$profile->a3_id}}';
	 $.getJSON(ajaxUrl, function(data) {
		var myIcon = new groupIcon({iconUrl: '{{ URL::to('/') }}/img/icons/b_{{$icon}}.png'});
		var marker = new MyCustomMarker(map.unproject([data.globalX,data.globalY], map.getMaxZoom()), {
			icon: myIcon
		});
		
		var popup = L.popup()
			.setContent("<div class='unit_popup'><p><span class='title'>{{$clan->name}} [{{$clan->tag}}]</span></br>- {{$name}} {{$size}}</br><img src='{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($profile->country) }}.png' alt='{{ $profile->country_name }}' title='{{ $profile->country_name }}'/>Cmdr: " + data.PlayerName + "</br>Credits: " + data.Credits + " </p></div>");
			
		marker.bindPopup(popup, { 			
				showOnMouseOver: true
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