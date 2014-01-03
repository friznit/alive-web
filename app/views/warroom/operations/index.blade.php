@extends('warroom.layouts.default')

{{-- Content --}}
@section('content')


<div id="statsnav" class="navbar navbar-warroom" role="navigation">
    <div class="container">
        <ul class="nav navbar-nav nav-tabs" data-tabs="tabs">
            <li class="active"><a data-toggle="tab" href="#tab_operations">Operations</a></li>
            <li><a data-toggle="tab" href="#tab_tempo">Operational Tempo</a></li>
        </ul>
    </div>
</div>

<div class="tab-content">

    <div class="tab-pane active" id="tab_operations">

        <div class="table-container dark3-panel container">

            <div class="row">
                <div class="col-md-12">
                
                    <h2>Operation Breakdown</h2>
                    @include('warroom/tables/breakdown')
                    
                    <h2>Operations Kills Breakdown</h2>
                    @include('warroom/tables/operations')

                </div>
            </div>

        </div>

    </div>

    <div class="tab-pane" id="tab_tempo">

        <div class="stats-container dark2-panel">

            <div class="row">
                <div class="col-md-12">
                    @include('warroom/charts/tempo')
                </div>
            </div>

        </div>

    </div>


</div>

@stop