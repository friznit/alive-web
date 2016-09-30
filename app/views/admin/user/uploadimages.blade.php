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
        <div class="row">

            <div class="col-md-5">
                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">Upload Mod Images</h3>
                    </div>
                    <div class="panel-body">                   
                      <!-- Button to select & upload files -->
                      <span class="btn btn-success fileinput-button">
                        <span>Select files...</span>
                        <!-- The file input field used as target for the file upload widget -->
                        <input id="fileupload" type="file" name="files[]" multiple>
                      </span>
                      <br><br>
                      <!-- The global progress bar -->
                      <p>Upload progress</p>
                      <div id="progress" class="progress progress-success progress-striped">
                        <div class="bar"></div>
                      </div>
                      
                      <!-- The list of files uploaded -->
                      <p>Files uploaded:</p>
                      <ul id="files"></ul>
                      
                      <!-- Load jQuery and the necessary widget JS files to enable file upload -->
                      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
                      <script src="{{ URL::to('/') }}/js/jquery.ui.widget.js"></script>
                      <script src="{{ URL::to('/') }}/js/jquery.iframe-transport.js"></script>
                      <script src="{{ URL::to('/') }}/js/jquery.fileupload.js"></script>
                  
                      <!-- JavaScript used to call the fileupload widget to upload files -->
                      <script>
                        // When the server is ready...
                        $(function () {
                            'use strict';
                            
                            // Define the url to send the image data to
                            var url = 'files.php';
                            
                            // Call the fileupload widget and set some parameters
                            $('#fileupload').fileupload({
                                url: url,
                                dataType: 'json',
                                done: function (e, data) {
                                    // Add each uploaded file name to the #files list
                                    $.each(data.result.files, function (index, file) {
                                        $('<li/>').text(file.name).appendTo('#files');
                                    });
                                },
                                progressall: function (e, data) {
                                    // Update the progress bar while files are being uploaded
                                    var progress = parseInt(data.loaded / data.total * 100, 10);
                                    $('#progress .bar').css(
                                        'width',
                                        progress + '%'
                                    );
                                }
                            });
                        });
                        
                      </script>
                     </div>
                </div>
            </div> 
           <div class="col-md-7">
                <div class="panel panel-dark">
                    <div class="panel-heading">
                        <h3 class="panel-title">How to Render Mod Images</h3>
                    </div>

                    <div class="panel-body">

                        <div class="strip">
                            <p>Creating Images of Mod Weapons and Vehicles for War Room.<br/><b>NOTE: These steps must be performed on your client machine.</b></p>
                        </div>

                        <table class="table">
                             <tr>
                                <td width="80">Step 1</td>
                                <td>Create a folder called exportCfg in your Arma 3 directory</td>
                                <td></td>
                            </tr>
                             <tr>
                                <td>Step 2</td>
                                <td>Copy render.pbo from the Arma 3 Tools directory (C:\Program Files (x86)\Steam\SteamApps\common\Arma 3 Tools\RenderWorlds) to your @ALiVE/addons folder<br><br>Arma 3 Tools can be installed from Steam.</td>
                                <td></td>
                            </tr>                    
                            <tr>
                                <td>Step 3</td>
                                <td>Download scr_cap.dll from <a href="http://killzonekid.com/pub/scr_cap_v1.0.zip" target="new">here</a> and extract scr_cap.dll to your @ALiVE folder.</td>
                                <td></td>                                
                            </tr>
                            <tr>
                                <td>Step 4</td>
                                <td>Launch Arma 3 with your mod enabled.<br>1. Select Editor from the main menu and select Render map.<br>2. In the Editor, select Load and select the ALiVE War Room Image Capture mission</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Step 5</td>
                                <td>Open each of the triggers in the mission (double click on the flags). In the OnAct box change the prefix parameter to match your mod i.e. change CUP to your TAG. For example, RHS CfgPatches(pbos) rhsusf_weapons (rhsusf_weapons.pbo), the prefix would be RHS.</td>
                                <td></td>                                
                            </tr>
                             <tr>
                                <td>Step 6</td>
                                <td>Alternatively, you can add an additional parameter that specifies the exact CfgPatches (PBOs) to choose, i.e. ["A3_weapons","A3_weapons_beta"].<br>For example, in the OnAct text box for the trigger:<br>  null = [] spawn { ["screenshots","",["CUP_Weapons","CUP_Shiny_Weapons"]] call ALiVE_fnc_exportCfgWeapons; }</td>
                                <td></td>                                
                            </tr>                           
                            <tr>
                                <td>Step 7</td>
                                <td>Preview the mission and change your video settings:<br> 1. Ensure quality is set to Ultra<br> 2. Set visibility to 500<br> 3. Ensure display is set to Fullscreen Window mode (NOT fullscreen) and increase brightness to 1.5.<br> 4. Change your AA & PP settings, turn off bloom, turn off depth of field, increase sharpen to 200.</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Step 8</td>
                                <td>Press 0 then 0 again to bring up radio menu<br>
                                - Select 1 - Weapons (Test) to do a dry run<br>
                                - Select 2 - Weapons to get screenshots (placed in exportCfg)<br>
                                - Select 3 - Vehicles (Test) to do a dry run of vehicles<br>
                                - Select 4 - Vehicles to get screenshots (placed in exportCfg)<br>
                                Maintain window focus on Arma 3 while any screenshots run.</td>
                                <td></td>
                            </tr>
                              <tr>
                                <td>Step 9</td>
                                <td>Press Select Files button on this page and select all the images in your exportCfg folder. The images will be uploaded and converted to War Room format. The images will be available to see on War Room within 1 hour.</td>
                                <td></td>
                            </tr>                                                                                
                        </table>                 

                    </div>

                </div>
            </div>            
        </div>
    </div>
</div>

@stop
