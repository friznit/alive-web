<div id="topnav" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><img src="{{ URL::to('/') }}/img/logo.png"/></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li><a href="{{ URL::to('/') }}">Home</a></li>
                <li><a href="/missions">Missions</a></li>
                <li><a href="/wiki">Wiki</a></li>
            </ul>
            <ul class="nav navbar-nav pull-right">
                @if (Sentry::check() && Sentry::getUser()->hasAccess('admin'))
                <li id="login"><a href="{{ URL::to('war-room') }}"><img src="{{ URL::to('/') }}/img/alive_warroom_tiny.png" class="img-responsive navbar-warroom"/></a></li>
                @else
                 @endif
            </ul>
        </div>
    </div>
</div>
