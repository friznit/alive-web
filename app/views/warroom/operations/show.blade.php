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
		
		var mz = 6;
		var size = 16384;
		
		var mapname = "{{$ao->configName}}";
		if (mapname == "Altis") {
			mz = 7;
			size = 32768;
		}
		
        var map = L.map('map', {
			minZoom: 0,
			maxZoom: mz,
			zoomControl: true,
            attributionControl: false,
            crs: L.CRS.Simple
		});
			
		var southWest = map.unproject([0, size], map.getMaxZoom());
		var northEast = map.unproject([size, 0], map.getMaxZoom());
		map.setMaxBounds(new L.LatLngBounds(southWest, northEast));
		
		map.fitWorld();
		
		L.tileLayer("http://alivemod.com/maps/{{ strtolower($ao->configName) }}/{z}/{x}/{y}.png" , {
            attribution: 'ALiVE',
            tms: true	//means invert.
        }).addTo(map);
			
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


<div id="warroom_overview">
    @include('warroom/tables/op_overview')
</div>

<div id="warroom_livefeed">
    <div class="strip clearfix"><span id="warroom_livefeed_toggle"><i class="fa fa-arrow-right"></i></span><span id="warroom_livefeed_label" class="control">{{ $name }} - AAR</span></div>

		@include('warroom/tables/aar_table')

</div>

<div id="warroom_charts">
    <div class="strip"><span class="control-center" id="warroom_charts_toggle"><i class="fa fa-arrow-down"></i></span></div>
    <div class="row">
        <div class="col-md-3">
            @include('warroom/charts/op_blu_losses')
        </div>
        <div class="col-md-3">
            @include('warroom/charts/op_opf_losses')
        </div>
        <div class="col-md-3">
            @include('warroom/charts/op_casualties')
        </div>
    </div>

</div>

@stop