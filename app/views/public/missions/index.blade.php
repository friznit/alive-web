@extends('public.layouts.missions')

{{-- Content --}}
@section('content')

<div class="jumbotron white-panel">
    <div class="container">
        <div class="row">
             <div class="col-md-6">
                <br/><h2>Sample Missions</h2><br/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h4>Pyrgos Assault</h4>
                <p><small><b>by [KH]Jman</b></small><br/>The Rebels have taken over Pyrgos. Take it back and drive them into the sea!. Player respawn and revive are enabled. You will see player's injured via markers. </p>
                <p><b>Requirements</b><br/>
                    @CBA, @ALiVE
                </p>
                <a class="btn btn-yellow" href="downloads/CO10_ALiVE_Pyrgos_Assault_v1_11.Altis.7z"><i class="fa fa-download"></i> Download</a><br/>
            </div>
            <div class="col-md-4">
                <img src="{{ URL::to('/') }}/img/pyrgos.jpg" class="img-responsive" />
            </div>
        </div>
        <br/>
        <div class="row top-margin">
            <div class="col-md-6">
                <h4>Valley of Just Pure Death</h4>
                <p><small><b>by ARJay</b></small><br/>A hectic 3 faction battle in a small valley on the western side of Altis.</p>
                <p><b>Requirements</b><br/>
                    @CBA, @ALiVE
                </p>
                <a class="btn btn-yellow" href="downloads/ALiVE_ValleyOfJPD.Altis.zip"><i class="fa fa-download"></i> Download</a><br/>
            </div>
            <div class="col-md-4">
               <img src="{{ URL::to('/') }}/img/jpd.jpg" class="img-responsive" />
            </div>
        </div>
        <br/>
        <div class="row top-margin">
            <div class="col-md-6">
                <h4>The Grind</h4>
                <p><small><b>by ARJay</b></small><br/>You occupy a small base close to the front lines, the Russians are approaching!</p>
                <p><b>Requirements</b><br/>
                    @CBA, @ALiVE, <a href="http://www.armaholic.com/page.php?id=20106" target="_blank">@AiA</a>, <a href="http://forums.bistudio.com/showthread.php?170687-Iron-Front-as-mod-in-Arma-3" target="_blank">@IronFront</a>
                </p>
                <a class="btn btn-yellow" href="downloads/ALiVE_TheGrind.Staszow.zip"><i class="fa fa-download"></i> Download</a><br/>
            </div>
            <div class="col-md-4">
                <img src="{{ URL::to('/') }}/img/the_grind.jpg" class="img-responsive" />
            </div>
        </div>
        <br/>
        <div class="row top-margin">
            <div class="col-md-6">
                <h4>Hell of Zargabad</h4>
                <p><small><b>by [KH]Jman</b></small><br/>Rebel insurgents have taken over Zargabad. Take it back!</p>
                <p><b>Requirements</b><br/>
                    @CBA, @ALiVE, <a href="http://www.armaholic.com/page.php?id=20106" target="_blank">@AiA</a> / <a href="http://www.armaholic.com/page.php?id=23863" target="_blank">@A3MP</a>
                </p>
                <a class="btn btn-yellow" href="downloads/CO10_HellofZargabad_v1_5.Zargabad.7z"><i class="fa fa-download"></i> Download</a><br/>
            </div>
            <div class="col-md-4">
                <img src="{{ URL::to('/') }}/img/hellzarg.jpg" class="img-responsive" />
            </div>
        </div>
        <br/>
        <div class="row top-margin">
            <div class="col-md-6">
                <h4>Black Needle</h4>
                <p><small><b>by [KH]Jman</b></small><br/>Clear the fuelstation buildings and secure the crossroads. Make contact with UN troops and escort them to safety.</p>
                <p><b>Requirements</b><br/>
                    @CBA, @ALiVE, <a href="http://www.armaholic.com/page.php?id=20106" target="_blank">@AiA</a> / <a href="http://www.armaholic.com/page.php?id=23863" target="_blank">@A3MP</a>
                </p>
                <a class="btn btn-yellow" href="downloads/CO10_ALiVE_BNeedle_1_4.ProvingGrounds_PMC.7z"><i class="fa fa-download"></i> Download</a><br/>
            </div>
            <div class="col-md-4">
                <img src="{{ URL::to('/') }}/img/blackneedle.jpg" class="img-responsive" />
            </div>
        </div>
        <br/>
        <div class="row top-margin">
            <div class="col-md-6">
                <h4>Insurgency ALiVE</h4>
                <p><small><b>by Sacha Ligthert + Mphillips'Hazey'</b></small><br/> This is an Insurgency template based on the ALiVE-mod. Based on the classic A2 Insurgency, find intel and destroy weapons caches. Offered to the ArmA 3 community as a template to fit your (community's) ArmA 3 needs. Feel free to edit!</p>
                <p><b>Requirements</b><br/>
                    @CBA, @ALiVE
                </p>
                <a class="btn btn-yellow" href="downloads/insurgency.7z"><i class="fa fa-download"></i> Download</a><br/>
            </div>
            <div class="col-md-4">
                <img src="{{ URL::to('/') }}/img/insurgency.jpg" class="img-responsive" />
            </div>
        </div>
        <br/>
        <div class="row top-margin">
            <div class="col-md-6">
                <h4>AS Alamo</h4>
                <p><small><b>by ARJay</b></small><br/> This mission shows some handy things for scripters working with ALiVE, custom blacklists, custom location spawn script. Features Aggressors from CAF, and AIS Wounding System from [TcB]-Psycho, and custom scripted radio messages</p>
                <p><b>Requirements</b><br/>
                    @CBA, @ALiVE, <a href="http://forums.bistudio.com/showthread.php?172069-Arma3-AGGRESSORS" target="_blank">@CAF_AG</a>
                </p>
                <a class="btn btn-yellow" href="downloads/ASAlamo.7z"><i class="fa fa-download"></i> Download</a><br/>
            </div>
            <div class="col-md-4">
                <img src="{{ URL::to('/') }}/img/as_alamo_ag.jpg" class="img-responsive" />
            </div>
        </div>
        <br/>
        <div class="row top-margin">
            <div class="col-md-6">
                <h4>The Scavengers</h4>
                <p><small><b>by Highhead</b></small><br/> Scavenge weapons and vehicles to continue the resistance! You start with a pistol and very limited supplies, can you gather the resources to survive in a hostile land?</p>
                <p><b>Requirements</b><br/>
                    @CBA, @ALiVE
                </p>
                <a class="btn btn-yellow" href="downloads/Scavengers.7z"><i class="fa fa-download"></i> Download</a><br/>
            </div>
            <div class="col-md-4">
                <img src="{{ URL::to('/') }}/img/scavengers.jpg" class="img-responsive" />
            </div>
        </div>
    </div>
</div>

@stop
