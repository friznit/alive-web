@extends('warroom.layouts.content')

{{-- Content --}}
@section('content')
 <div id="loading" class="loading">
   <h1>
    <span class="let1">I</span>  
    <span class="let2">N</span>  
    <span class="let3">I</span>  
    <span class="let4">T</span>  
    <span class="let5">I</span>  
    <span class="let6">A</span>  
    <span class="let7">L</span>  
    <span class="let8">I</span>  
    <span class="let9">S</span>  
    <span class="let10">I</span>  
    <span class="let11">N</span>  
    <span class="let12">G</span>  
  </h1>
 </div>

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

$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        })

$.ajax({
    url: '{{ URL::to('/') }}/api/aos',
    type: "GET", 
    success: function(data){
$.each(data, function(index, data){
            var mapdata = $.parseJSON(data.couchData);
            var mapid = data.id
 
            if(mapid < 10) {
                mapid = "00"+mapid;
            } else if (mapid < 100) {
                mapid = "0"+mapid;
            } else {
                mapid = mapid;
            }
            var marker = new MyCustomMarker(map.unproject([data.imageMapX,data.imageMapY], map.getMaxZoom()), {
                icon: hostileIcon
            });
            var popup = L.popup()
                .setContent("<div class='strip'>AO</div>" +
                    "<div class='ao-popup'>" +
                    "<p>" +
                    "<span class='title'>" + data.name +"</span></br>" +
                    "<img src='http://alivemod.com/system/AO/images/000/000/"+ mapid +"/thumbAO/" + data.image_file_name + "' ></br>" +
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

//DEVS
$.ajax({
    url: '{{ URL::to('/') }}/api/devs',
    type: "GET", 
    success: function(data){
$.each(data, function(index, data){
  var devdata = $.parseJSON(data.couchData);
            var myIcon = new groupIcon({
                iconSize:     [50, 50], // size of the icon
                shadowSize:   [50, 50], // size of the shadowicon
                shadowUrl: '{{ URL::to('/') }}/img/icons/w_'+ data.sizeicon +'.png',
                iconUrl: '{{ URL::to('/') }}/img/icons/b_'+ data.icon +'.png'});
            var marker = new MyCustomMarker(map.unproject([devdata.globalX,devdata.globalY], map.getMaxZoom()), {
                icon: myIcon
            });

            var popup = L.popup()
                .setContent("<div class='strip'>Lead Unit</div>" +
                    "<div class='unit-popup'>" +
                    "<p>" +
                    "<span class='title'>" + data.clan.name + data.clan.tag +"</span></br>" +
                    "<span class='highlight'>" + data.orbatname + data.size +"</span></br>" +
                    "<span class='highlight'>Cmdr:</span> " + devdata.PlayerName +
                    " <img src='{{ URL::to('/') }}/img/flags_iso/32/" + data.country.toLowerCase() +".png' alt='" + data.country_name +"' title='" + data.country_name +"' width='16' height='16'/><br/>" +
                    "<span class='highlight'>Credits:</span> " + devdata.Credits +
                    "</p>" +
                    "</div>");

            marker.bindPopup(popup, {
                showOnMouseOver: true,
                offset: new L.Point(-3, -5)
            });
            map.addLayer(marker);
//end devs each
      });

//clans
$.ajax({
    url: '{{ URL::to('/') }}/api/clans',
    type: "GET", 
    success: function(data){
        $.each(data, function(index, data){
        var clandata = $.parseJSON(data.couchData);

			if (!(clandata.Operations)) {
					var myIcon = new groupIcon({
						iconUrl: '{{ URL::to('/') }}/img/icons/dot.png', 
						shadowURL: '{{URL::to('/') }}/img/icons/dotshadow.png',
						iconSize:     [4, 4], // size of the icon
						shadowSize:   [4, 4], // size of the shadow
                        iconAnchor: [2, 2],  // point of the icon which will correspond to markers location
						shadowAnchor: [2, 2]  // the same for the shadow
					});		
			} else {
               var clanLastOpData = $.parseJSON(data.lastop);
              
                var clanLastOp = clanLastOpData.Operation;     
                var lastOpDate = clanLastOpData.date; 
       ;               
                var lsystem_date = new Date(lastOpDate);
                var luser_date = new Date();
                var ldiff = Math.floor((luser_date - lsystem_date) / 1000);

                var iconSizer = 30 - Math.round(ldiff / 777600);

                var country = "";
                var country_name = "";
                if(data.country === "")
                {
                    country = data.country.toLowerCase();
                    country_name = data.country_name;
                } else {
                    country = "gb";
                    country_name = "United Kingdom";
                }

                if (ldiff < 15552000) {
           		 var myIcon = new groupIcon({
                        iconSize: [iconSizer,iconSizer],
                        shadowSize: [iconSizer,iconSizer],
                        iconAnchor:   [iconSizer/2, iconSizer/2], 
                        shadowAnchor: [iconSizer/2, iconSizer/2],                        
					 	shadowUrl: '{{ URL::to('/') }}/img/icons/w_group_0.png',
						iconUrl: '{{ URL::to('/') }}/img/icons/b_'+ data.icon + '.png'
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
            var marker = new MyCustomMarker(map.unproject([data.lon,data.lat], map.getMaxZoom()), {
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
                .setContent("<table><tr><td colspan='2'><div class='strip'>Unit</div></td></tr><tr><td><img width='100' src='http://alivemod.com/avatars/thumb/clan.png' onerror='this.src=\"http://alivemod.com/avatars/thumb/clan.png\"'></td><td>" +
                    "<div class='unit-popup'>" +
                    "<p>" +
                    "<a href={{ URL::to('war-room/showorbat') }}/" + data.id +"><span class='title'>"+ data.name + data.tag +"</span></a></br>" +
                    "<span class='highlight'>" + data.name + data.size +"</span>" +
                   " <img src='{{ URL::to('/') }}/img/flags_iso/32/"+ country +".png' alt='" + country_name +"' title='" + country_name +"' width='18' height='18'/><br/>" +
                    "<span class='highlight'>OPS:</span> " + data.Operations + " <span class='highlight'>| EKIA:</span> " + data.Kills + " <span class='highlight'>| LOSSES:</span> " + data.Deaths + "</br>" +
                    "<span class='highlight'>HRS:</span> " + Math.round((data.CombatHours / 60)*10)/10 + " <span class='highlight'>| AMMO:</span> " + data.ShotsFired + "</br>" +
                    "<span class='highlight'>VEHICLE HRS:</span> " + Math.round((data.VehicleTime / 60)*10)/10 + " <span class='highlight'>| FLIGHT HRS:</span> " + Math.round((data.PilotTime / 60)*10)/10 + "</br>" +
                    "<span class='highlight'>LAST OP:</span> " + clanLastOp + "</br>" + parseArmaDate(lastOpDate) +                                  
                    "</p>" +
                    "</div></td></tr></table>");

            marker.bindPopup(popup, {
                showOnMouseOver: true,
                offset: new L.Point(-3, -5)
            });
            map.addLayer(marker);
                  //end clans each
                  });
                   $("#loading").fadeOut(3000);
	          }
	  //end clans ajax
	     });
        
      	}
    //end devs ajax
    });
     
    }
	//	end main ajax
 
});	 
   
 //end doc ready
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