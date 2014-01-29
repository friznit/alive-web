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
                <h4>Mountain War</h4>
                <p><small><b>by (AEF)Spinfx</b></small><br/>Stratis is split down the middle with BLUFOR holding the western side and OPFOR holding the east. Featuring BIS support modules, BTC Revive, and VAS.</p>
                <p><b>Requirements</b><br/>
                    @CBA, @ALiVE
                </p>
                <a class="btn btn-yellow" href="downloads/ALiVE_MountainWar.Stratis.zip"><i class="fa fa-download"></i> Download</a><br/>
            </div>
            <div class="col-md-4">
                <img src="{{ URL::to('/') }}/img/mountain_war.jpg" class="img-responsive" />
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
                <a class="btn btn-yellow" href="downloads/CO10_HellofZargabad_v1_4.Zargabad.7z"><i class="fa fa-download"></i> Download</a><br/>
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
                    @CBA, @ALiVE, <a href="http://www.armaholic.com/page.php?id=20106" target="_blank">@AiA</a> / <a href="http://www.armaholic.com/page.php?id=23863" target="_blank">@A3MP</a>, <a href="http://www.armaholic.com/page.php?id=22548" target="_blank">@stkr_bi</a>, <a href="http://www.armaholic.com/page.php?id=23169" target="_blank">@Kio_L85A2</a>
                </p>
                <a class="btn btn-yellow" href="downloads/Co10_ALiVE_BNeedle_1_3.ProvingGrounds_PMC.7z"><i class="fa fa-download"></i> Download</a><br/>
            </div>
            <div class="col-md-4">
                <img src="{{ URL::to('/') }}/img/blackneedle.jpg" class="img-responsive" />
            </div>
        </div>
    </div>
</div>

@stop
