@extends('warroom.layouts.default')

{{-- Content --}}
@section('content')


<div id="statsnav" class="navbar navbar-warroom" role="navigation">
    <div class="container">
        <ul class="nav navbar-nav nav-tabs" data-tabs="tabs">
            <li class="active"><a data-toggle="tab" href="#tab_personnel">Personnel</a></li>
            <li><a data-toggle="tab" href="#tab_tier1">Tier 1</a></li>
            <li><a data-toggle="tab" href="#tab_score">High Scores</a></li>
        </ul>
    </div>
</div>

<div class="tab-content">

    <div class="tab-pane active" id="tab_personnel">

        <div class="table-container dark2-panel container">

            <div class="row">
                <div class="col-md-12">
                    <h2>Personnel</h2>
                    @include('warroom/tables/personnel')
                </div>
            </div>

        </div>

    </div>

    <div class="tab-pane" id="tab_tier1">

        <div class="table-container dark2-panel container">

            <div class="row">
                <div class="col-md-12">
                    <h2>Tier 1 Marksmen</h2>
                    @include('warroom/tables/t1marksmen')

                    <h2>Top Vehicle Commanders</h2>
                    @include('warroom/tables/vehicles')
                    
                    <h2>Top Guns</h2>
                    @include('warroom/tables/pilots')
                    
                    <h2>Top Medics</h2>
                    @include('warroom/tables/medics')
                </div>
            </div>

        </div>

    </div>

    <div class="tab-pane" id="tab_score">

        <div class="table-container dark2-panel container">

            <div class="row">
                <div class="col-md-4">

                    <h2>Cumulative Score</h2>
                    @include('warroom/tables/score')
                   </div>
                 <div class="col-md-4">   
                    <h2>Average Score</h2>
                    @include('warroom/tables/avescore')
                  </div>
                 <div class="col-md-4">   
                    <h2>Average Rating</h2>
                    @include('warroom/tables/rating')
                </div>
            </div>

        </div>

    </div>

</div>

@stop