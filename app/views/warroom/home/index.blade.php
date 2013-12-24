@extends('warroom.layouts.home')

{{-- Content --}}
@section('content')
 
<div id="map" style="height: 900px;"></div>

<script type="text/javascript">

        var map = L.map('map', {
			minZoom: 0,
			maxZoom: 5,
			zoomControl: false,
			crs: L.CRS.Simple
		}).setView([4676,3864], 2);
		
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
				
    $(document).ready(function() {
		$(".trigger").click(function(){
			$(".panel").toggle("fast");
			$(this).toggleClass("active");
			return false;
		});
	});

</script>

<?php
	$allAOs = AO::all();
?>

@foreach ($allAOs as $ao)

<script type="text/javascript">

	var marker = L.circleMarker(map.unproject([{{$ao->imageMapX}},{{$ao->imageMapY}}], map.getMaxZoom()), {
		color: 'red',
		fillColor: '#f03',
		fillOpacity: 0.5,
		offset: [6,0]
	});
	
	marker.bindPopup("<div class='war-room_popup'><h2>{{$ao->name}}</h2><h3>This is a test<h3><p>This is a test</p></div>");
	
	map.addLayer(marker);
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