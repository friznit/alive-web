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
        $('#map-content').fadeIn();
		$(".trigger").click(function(){
			$(".panel").toggle("fast");
			$(this).toggleClass("active");
			return false;
		});

        @foreach ($allAOs as $ao)

            var mapdata = {{$ao->couchData}};
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

        @endforeach


        @foreach ($devs as $dev)

            var data = {{$dev->couchData}};
            var myIcon = new groupIcon({
                iconSize:     [50, 50], // size of the icon
                shadowSize:   [50, 50], // size of the shadowicon
                shadowUrl: '{{ URL::to('/') }}/img/icons/w_{{$dev->sizeicon}}.png',
                iconUrl: '{{ URL::to('/') }}/img/icons/b_{{$dev->icon}}.png'});
            var marker = new MyCustomMarker(map.unproject([data.globalX,data.globalY], map.getMaxZoom()), {
                icon: myIcon
            });

            var popup = L.popup()
                .setContent("<div class='strip'>Lead Unit</div>" +
                    "<div class='unit-popup'>" +
                    "<p>" +
                    "<span class='title'>{{$dev->clan->name}} [{{$dev->clan->tag}}]</span></br>" +
                    "<span class='highlight'>{{$dev->orbatname}} {{$dev->size}}</span></br>" +
                    "<span class='highlight'>Cmdr:</span> " + data.PlayerName +
                    " <img src='{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($dev->country) }}.png' alt='{{ $dev->country_name }}' title='{{ $dev->country_name }}' width='16' height='16'/><br/>" +
                    "<span class='highlight'>Credits:</span> " + data.Credits +
                    "</p>" +
                    "</div>");

            marker.bindPopup(popup, {
                showOnMouseOver: true,
                offset: new L.Point(-3, -5)
            });
            map.addLayer(marker);

        @endforeach


        @foreach ($clans as $clan)

            var data = {{$clan->couchData}};


			if (!(data.Operations)) {
					var myIcon = new groupIcon({
						iconUrl: '{{ URL::to('/') }}/img/icons/dot.png', 
						shadowURL: '{{URL::to('/') }}/img/icons/dotshadow.png',
						iconSize:     [4, 4], // size of the icon
						shadowSize:   [4, 4], // size of the shadow
                        iconAnchor: [2, 2],  // point of the icon which will correspond to markers location
						shadowAnchor: [2, 2]  // the same for the shadow
					});		
			} else {

                var clanLastOp = {{$clan->lastop}};             
                var lastop = clanLastOp.date;                  
                var lsystem_date = new Date(lastop);
                var luser_date = new Date();
                var ldiff = Math.floor((luser_date - lsystem_date) / 1000);

                var iconSizer = 30 - Math.round(ldiff / 777600);

                if (ldiff < 15552000) {
           		 var myIcon = new groupIcon({
                        iconSize: [iconSizer,iconSizer],
                        shadowSize: [iconSizer,iconSizer],
                        iconAnchor:   [iconSizer/2, iconSizer/2], 
                        shadowAnchor: [iconSizer/2, iconSizer/2],                        
					 	shadowUrl: '{{ URL::to('/') }}/img/icons/w_group_0.png',
						iconUrl: '{{ URL::to('/') }}/img/icons/b_{{$clan->icon}}.png'
					});
                } else {
                    var myIcon = new groupIcon({
                        iconUrl: '{{ URL::to('/') }}/img/icons/dot.png', 
                        shadowURL: '{{URL::to('/') }}/img/icons/dotshadow.png',
                        iconSize:     [10, 10], // size of the icon
                        shadowSize:   [10, 10], // size of the shadow
                        iconAnchor:   [5, 5], // point of the icon which will correspond to markers location
                        shadowAnchor: [5, 5]  // the same for the shadow
                    });                 
                }
			}
            var marker = new MyCustomMarker(map.unproject([{{$clan->lon}},{{$clan->lat}}], map.getMaxZoom()), {
                icon: myIcon
            });
			
			if (!(data.Operations)) {
				data.Operations = 0;
				data.Kills = 0;
				data.Deaths = 0;
				data.CombatHours = 0.1;
				data.ShotsFired = 0;
				data.VehicleTime = 0.01;
				data.PilotTime = 0.01;
			}
			
			function onErrFunction(source){
				source.src = "http://alivemod.com/avatars/thumb/clan.png";
				source.onerror = "";
				return true;
			}
			
            var popup = L.popup({maxWidth:400})
                .setContent("<table><tr><td colspan='2'><div class='strip'>Unit</div></td></tr><tr><td><img width='100' src='{{ $clan->avatar->url('thumb') }}' onerror='this.src=\"http://alivemod.com/avatars/thumb/clan.png\"'></td><td>" +
                    "<div class='unit-popup'>" +
                    "<p>" +
                    "<a href={{ URL::to('war-room/showorbat') }}/{{$clan->id}}><span class='title'>{{$clan->name}} [{{$clan->tag}}]</span></a></br>" +
                    "<span class='highlight'>{{$clan->name}} {{$clan->size}}</span>" +
                    " <img src='{{ URL::to('/') }}/img/flags_iso/32/{{ strtolower($clan->country) }}.png' alt='{{ $clan->country_name }}' title='{{ $clan->country_name }}' width='18' height='18'/><br/>" +
                    "<span class='highlight'>OPS:</span> " + data.Operations + " <span class='highlight'>| EKIA:</span> " + data.Kills + " <span class='highlight'>| LOSSES:</span> " + data.Deaths + "</br>" +
                    "<span class='highlight'>HRS:</span> " + Math.round((data.CombatHours / 60)*10)/10 + " <span class='highlight'>| AMMO:</span> " + data.ShotsFired + "</br>" +
                    "<span class='highlight'>VEHICLE HRS:</span> " + Math.round((data.VehicleTime / 60)*10)/10 + " <span class='highlight'>| FLIGHT HRS:</span> " + Math.round((data.PilotTime / 60)*10)/10 + "</br>" +
                    "<span class='highlight'>LAST OP:</span> " + clanLastOp.Operation + "</br>" + parseArmaDate(lastop) +                                  
                    "</p>" +
                    "</div></td></tr></table>");

            marker.bindPopup(popup, {
                showOnMouseOver: true,
                offset: new L.Point(-3, -5)
            });
            map.addLayer(marker);

        @endforeach

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
			iconSize:     [30, 30], // size of the icon
			shadowSize:   [30, 30], // size of the shadow
			iconAnchor:   [15, 15], // point of the icon which will correspond to markers location
			shadowAnchor: [15, 15],  // the same for the shadow
			popupAnchor:  [100, 100] // point from which the popup should open relative to the iconAnchor
		}
	});

</script>

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