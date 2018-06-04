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

    $(document).ready(function() {
        $('#map-content').fadeIn();
		$(".trigger").click(function(){
			$(".panel").toggle("fast");
			$(this).toggleClass("active");
			return false;
		});

        var aos = {{ $allAOs }};
        aos.forEach(function (ao) {
            var marker = new MyCustomMarker(map.unproject([ao.imageMapX,ao.imageMapY], map.getMaxZoom()), {
                icon: hostileIcon
            });

            var popup = L.popup()
                .setContent("<div class='strip'>AO</div>" +
                    "<div class='ao-popup'>" +
                    "<p>" +
                    "<span class='title'>" + ao.name + "</span></br>" +
                    "<img src='" + ao.thumbAO + "' ></br>" +
                    "<span class='highlight'>OPS:</span> " + ao.couchData.Operations + " <span class='highlight'>| EKIA:</span> " + ao.couchData.Kills + " <span class='highlight'>| LOSSES:</span> " + ao.couchData.Deaths + "</br>" +
                    "<span class='highlight'>HRS:</span> " + Math.round((ao.couchData.CombatHours / 60)*10)/10 + " <span class='highlight'>| AMMO:</span> " + ao.couchData.ShotsFired + " <span class='highlight'>| UNITS:</span> " + ao.couchData.Operations +
                    "</p>" +
                    "</div>");

            marker.bindPopup(popup, {
                showOnMouseOver: true,
                offset: new L.Point(-4, -12)
            });

            map.addLayer(marker);
        });


        var devs = {{ $devs }};
        devs.forEach(function (dev) {
            var myIcon = new groupIcon({
                iconSize:     [50, 50], // size of the icon
                shadowSize:   [50, 50], // size of the shadowicon
                shadowUrl: '{{ URL::to('/') }}/img/icons/w_' + dev.sizeicon + '.png',
                iconUrl: '{{ URL::to('/') }}/img/icons/b_' + dev.icon + '.png'});
            var marker = new MyCustomMarker(map.unproject([dev.couchData.globalX,dev.couchData.globalY], map.getMaxZoom()), {
                icon: myIcon
            });

            var popup = L.popup()
                .setContent("<div class='strip'>Lead Unit</div>" +
                    "<div class='unit-popup'>" +
                    "<p>" +
                    "<span class='title'>" + dev.clan.name + " [" + dev.clan.tag + "]</span></br>" +
                    "<span class='highlight'>" + dev.orbatname + " " + dev.size + "</span></br>" +
                    "<span class='highlight'>Cmdr:</span> " + dev.couchData.PlayerName +
                    " <img src='{{ URL::to('/') }}/img/flags_iso/32/" + dev.country.toLowerCase() + ".png' alt='" + dev.country_name + "' title='" + dev.country_name + "' width='16' height='16'/><br/>" +
                    "<span class='highlight'>Credits:</span> " + dev.couchData.Credits +
                    "</p>" +
                    "</div>");

            marker.bindPopup(popup, {
                showOnMouseOver: true,
                offset: new L.Point(-3, -5)
            });
            map.addLayer(marker);
        });

        var clans = {{ $clans }};
        clans.forEach(function (clan) {
            var lastop = clan.lastop.date;
            var lsystem_date = new Date(lastop);
            var luser_date = new Date();
            var ldiff = Math.floor((luser_date - lsystem_date) / 1000);

            var iconSizer = 30 - Math.round(ldiff / 777600);

            var myIcon = new groupIcon({
                iconSize: [iconSizer,iconSizer],
                shadowSize: [iconSizer,iconSizer],
                iconAnchor:   [iconSizer/2, iconSizer/2], 
                shadowAnchor: [iconSizer/2, iconSizer/2],                        
                shadowUrl: '{{ URL::to('/') }}/img/icons/w_group_0.png',
                iconUrl: '{{ URL::to('/') }}/img/icons/b_' + clan.icon + '.png'
            });

            var marker = new MyCustomMarker(map.unproject([clan.lon,clan.lat], map.getMaxZoom()), {
                icon: myIcon
            });

            var popup = L.popup({maxWidth:400})
                .setContent("<table><tr><td colspan='2'><div class='strip'>Unit</div></td></tr><tr><td><img width='100' src='" + clan.thumbAvatar + "' onerror='this.src=\"{{ URL::to('/') }}/avatars/thumb/clan.png\"'></td><td>" +
                    "<div class='unit-popup'>" +
                    "<p>" +
                    "<a href={{ URL::to('war-room/showorbat') }}/" + clan.id + "><span class='title'>" + clan.name + " [" + clan.tag + "]</span></a></br>" +
                    "<span class='highlight'>" + clan.name + " " + clan.size + "</span>" +
                    " <img src='{{ URL::to('/') }}/img/flags_iso/32/" + (clan.country != null ? clan.country.toLowerCase() : '') + ".png' alt='" + clan.country_name + "' title='" + clan.country_name + "' width='18' height='18'/><br/>" +
                    "<span class='highlight'>OPS:</span> " + clan.couchData.Operations + " <span class='highlight'>| EKIA:</span> " + clan.couchData.Kills + " <span class='highlight'>| LOSSES:</span> " + clan.couchData.Deaths + "</br>" +
                    "<span class='highlight'>HRS:</span> " + Math.round((clan.couchData.CombatHours / 60)*10)/10 + " <span class='highlight'>| AMMO:</span> " + clan.couchData.ShotsFired + "</br>" +
                    "<span class='highlight'>VEHICLE HRS:</span> " + Math.round((clan.couchData.VehicleTime / 60)*10)/10 + " <span class='highlight'>| FLIGHT HRS:</span> " + Math.round((clan.couchData.PilotTime / 60)*10)/10 + "</br>" +
                    "<span class='highlight'>LAST OP:</span> " + clan.lastop.Operation + "</br>" + parseArmaDate(lastop) +                                  
                    "</p>" +
                    "</div></td></tr></table>");

            marker.bindPopup(popup, {
                showOnMouseOver: true,
                offset: new L.Point(-3, -5)
            });
            map.addLayer(marker);
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
