@extends('warroom.layouts.default')

{{-- Content --}}
@section('content')


<div id="statsnav" class="navbar navbar-warroom" role="navigation">
    <div class="container">
        <ul class="nav navbar-nav nav-tabs" data-tabs="tabs">
            <li class="active"><a data-toggle="tab" href="#tab_group">Groups</a></li>
  <!--            <li><a data-toggle="tab" href="#tab_orbat">ORBAT</a></li> -->
        </ul>
    </div>
</div>

<div class="tab-content">

    <div class="tab-pane active" id="tab_group">

        <div class="table-container dark2-panel container">

            <div class="row">
                <div class="col-md-12">
                    <h2>Groups</h2>
                    @include('warroom/tables/orbat')
                </div>
            </div>

        </div>

    </div>

</div>

@stop