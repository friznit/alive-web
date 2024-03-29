@extends('admin.layouts.default')

{{-- Content --}}
@section('content')

<div class="dark-panel form-holder">

    <div class="container">
    
        <div class="row">

            <div class="col-md-12">
                @include('alerts/alerts')
            </div>

        </div>
        <div class = "row">
        	<div class="col-md-13">
                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Select Position on Map</h3>
                    </div>
                    <div class="panel-body">
                    	<div id="map" style="height: 400px;"></div>
                        
                        <script type="text/javascript">
                        <!--
						 var map = L.map('map', {
								minZoom: 0,
								maxZoom: 5,
								crs: L.CRS.Simple
							}).setView([4096,4096], 2);
							
							var southWest = map.unproject([0,1654], map.getMaxZoom());
							var northEast = map.unproject([8192,6400], map.getMaxZoom());
							map.setMaxBounds(new L.LatLngBounds(southWest, northEast));
							L.tileLayer("{{ URL::to('/') }}/maps/globalmap3/{z}/{x}/{y}.png" , {
								attribution: 'ALiVE',
								tms: true	//means invert.
							}).addTo(map);
							
							map.on('click', function(e) {
								
								var p = map.project(e.latlng, map.getMaxZoom());

								  document.getElementById("imageMapX").value = p.x;
  								  document.getElementById("imageMapY").value = p.y;
								  alert("You selected an area on the map: " + map.project(e.latlng, map.getMaxZoom())).latlng;
							});
							
						$(document).ready(function() {
							$(".trigger").click(function(){
								$(".panel").toggle("fast");
								$(this).toggleClass("active");
								return false;
							});
						});
						 //-->
                        </script>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-md-4">
                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Edit Area of Operation</h3>
                    </div>
                    <form action="{{ URL::to('admin/ao/edit') }}/{{ $ao->id }}" method="post">

                        {{ Form::token() }}

                        <div class="panel-body">

                             <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}" for="name">
                                <label class="control-label" for="name">Area Name</label>
                                <input name="name" value="{{ (Request::old('name')) ? Request::old("name") : $ao->name }}" type="text" class="form-control" placeholder="username">
                                <?php
                                if($errors->has('name')){
                                    echo '<span class="label label-danger">' . $errors->first('name') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('size')) ? 'has-error' : '' }}" for="size">
                                <label class="control-label" for="size">Size</label>
                                <input name="size" value="{{ (Request::old('size')) ? Request::old("size") : $ao->size }}" type="text" class="form-control" placeholder="size">
                                <?php
                                if($errors->has('size')){
                                    echo '<span class="label label-danger">' . $errors->first('size') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('configName')) ? 'has-error' : '' }}" for="configName">
                                <label class="control-label" for="configName">Configuration Name</label>
                                <input name="configName" value="{{ (Request::old('configName')) ? Request::old("configName") : $ao->configName }}" type="text" class="form-control" placeholder="configName">
                                <?php
                                if($errors->has('configName')){
                                    echo '<span class="label label-danger">' . $errors->first('configName') . '</span>';
                                }
                                ?>
                            </div>

                            <div class="form-group {{ ($errors->has('imageMapX')) ? 'has-error' : '' }}" for="imageMapX">
                                <label class="control-label" for="imageMapX">Position on Global Map (X)</label>
                                <input id="imageMapX" name="imageMapX" value="{{ (Request::old('imageMapX')) ? Request::old("imageMapX") : $ao->imageMapX }}" type="text" class="form-control" placeholder="imageMapX">
                                <?php
                                if($errors->has('imageMapX')){
                                    echo '<span class="label label-danger">' . $errors->first('imageMapX') . '</span>';
                                }
                                ?>
                            </div>
                            
                           <div class="form-group {{ ($errors->has('imageMapY')) ? 'has-error' : '' }}" for="imageMapY">
                                <label class="control-label" for="imageMapY">Position on Global Map (Y)</label>
                                <input id="imageMapY" name="imageMapY" value="{{ (Request::old('imageMapY')) ? Request::old("imageMapY") : $ao->imageMapY }}" type="text" class="form-control" placeholder="imageMapY">
                                <?php
                                if($errors->has('imageMapY')){
                                    echo '<span class="label label-danger">' . $errors->first('imageMapY') . '</span>';
                                }
                                ?>
                            </div>
                            
                             <div class="form-group {{ ($errors->has('latitude')) ? 'has-error' : '' }}" for="latitude">
                                <label class="control-label" for="latitude">Latitude</label>
                                <input name="latitude" value="{{ (Request::old('latitude')) ? Request::old("latitude") : $ao->latitude }}" type="text" class="form-control" placeholder="latitude">
                                <?php
                                if($errors->has('latitude')){
                                    echo '<span class="label label-danger">' . $errors->first('latitude') . '</span>';
                                }
                                ?>
                            </div>
                            
                            <div class="form-group {{ ($errors->has('longitude')) ? 'has-error' : '' }}" for="longitude">
                                <label class="control-label" for="longitude">Longitude</label>
                                <input name="longitude" value="{{ (Request::old('longitude')) ? Request::old("longitude") : $ao->longitude }}" type="text" class="form-control" placeholder="longitude">
                                <?php
                                if($errors->has('longitude')){
                                    echo '<span class="label label-danger">' . $errors->first('longitude') . '</span>';
                                }
                                ?>
                            </div>

                        </div>
                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <input class="btn btn-yellow" type="submit" value="Save">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-md-8">

                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Change Image</h3>
                    </div>

                    <form action="{{ URL::to('admin/ao/changeimage') }}/{{ $ao->id }}" method="post" enctype="multipart/form-data">
                        {{ Form::token() }}

                        <div class="strip">
                  				 <p>We recommend using the Arma 3 Map UI image here</p>
                        </div>

                        <div class="panel-body avatars">
                            <p>Medium (512px x 256px)</p>
                            <img src="<?= $ao->image->url('mediumAO') ?>" ><br/><br/>
                            <p>Thumbnail (256px x 128px)</p>
                            <img src="<?= $ao->image->url('thumbAO') ?>" ><br/><br/>
                            <input type="file" id="image_upload" name="image" />
                        </div>
                        <div class="panel-footer clearfix">
                            <div class="btn-toolbar pull-right" role="toolbar">
                                <input class="btn btn-yellow" type="submit" value="Change Image">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="panel panel-dark">
					<?php               
						$file = 'http://db.alivemod.com/maps/'.$ao->configName.'.png';
						$folder = 'http://db.alivemod.com/maps/'.$ao->configName.'/';
						$file_headers = @get_headers($file);
						$folder_headers = @get_headers($folder);
						if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
							$exists = false;
						}
						else {
							$exists = true;
						}
						if($folder_headers[0] == 'HTTP/1.1 404 Not Found') {
							$fexists = false;
						}
						else {
							$fexists = true;
						}
                    ?>	              
                    <div class="panel-heading">
                        <h3 class="panel-title">Change Picture</h3>
                    </div>
					@if ($exists)
                     	<form action="{{ URL::to('admin/ao/createtiles') }}/{{ $ao->id }}" method="post">
                        {{ Form::token() }}                   
                    @else
                    	<form action="{{ URL::to('admin/ao/changepic') }}/{{ $ao->id }}" method="post" enctype="multipart/form-data">
                        {{ Form::token() }}
					@endif
                        <div class="strip">
                        <?php						
							if ($exists) {
								echo '<p>A picture has already been uploaded to War Room</p>';
							} else {
								echo '<p>We recommend using the ALiVE_fnc_exportMapWarRoom in game to get the map picture</p>';								
							}
						?>
                        </div>

                        <div class="panel-body avatars">
							@if ($exists)
                        		<img src="http://db.alivemod.com/maps/{{ $ao->configName }}.png" /><br/><br/>
                                @if (!$fexists) 
                                <div class="panel-footer clearfix">
                                 	<p>Although a map image has been uploaded, no map tiles exist. This may take a few minutes to create.</p>
                                    <div class="btn-toolbar pull-right" role="toolbar">
                                        <input class="btn btn-yellow" type="submit" value="Create Tiles">
                                    </div>
                                </div>                                     
                                @endif
                        	@else                  
                                <p>Small (512px x 512px)</p>
                                <img src="<?= $ao->pic->url('smallPic') ?>" ><br/><br/>
                                <p>Thumbnail (256px x 256px)</p>
                                <img src="<?= $ao->pic->url('thumbPic') ?>" ><br/><br/>
                         	 	<input type="file" id="pic_upload" name="pic" />  
                                 <div class="panel-footer clearfix">
                                    <div class="btn-toolbar pull-right" role="toolbar">
                                        <input class="btn btn-yellow" type="submit" value="Change Picture">
                                    </div>
                                </div>                                                            
                       		@endif    
                        </div>
                        

                    </form>
                </div>

             </div>
		</div>
    </div>
</div>

@stop
