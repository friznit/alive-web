@extends('public.layouts.thankyou')

{{-- Content --}}
@section('content')

<div class="jumbotron white-panel">
    <div class="container">
        <div class="row">
             <div class="col-md-6">
                <br/><h2>Thanks for your support!</h2><br/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                <img src="{{ URL::to('/') }}/img/thankyou.jpg" class="img-responsive" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                <h4>ARJay, Friznit, Gunny, Highhead, Jman, Raptor, Rye, Tupolov, WobblyHeadedBob, and Wolffy.au</h4>
                <p>You have given us the opportunity to improve our hardware and our services!<br/>Let us say THANK YOU again, from the whole ALiVE team!</p>
            </div>
        </div>
    </div>
</div>

@stop
