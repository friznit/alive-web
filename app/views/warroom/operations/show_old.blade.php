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
        $(".trigger").click(function(){
            $(".panel").toggle("fast");
            $(this).toggleClass("active");
            return false;
        });

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

</script>

<div id="warroom_overview">
    @include('warroom/tables/op_overview')
</div>

<div id="event_container">
    <div class="strip clearfix"><span id="event_container_toggle"><i class="fa fa-arrow-right"></i></span><span id="event_container_data_label" class="control">{{ $name }} - AAR</span></div>

    @include('warroom/tables/aar_table')

</div>

<div id="warroom_charts">
    <div class="strip"><span class="control-center" id="warroom_charts_toggle"><i class="fa fa-arrow-down"></i></span></div>
    <div class="row">
        <div class="col-md-3">
            //@include('warroom/charts/op_blu_losses')
        </div>
        <div class="col-md-3">
            //@include('warroom/charts/op_opf_losses')
        </div>
        <div class="col-md-3">
            //@include('warroom/charts/op_casualties')
        </div>
    </div>

</div>

@stop