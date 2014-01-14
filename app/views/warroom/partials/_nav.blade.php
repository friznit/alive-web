<div id="topnav" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ URL::to('/') }}"><img src="{{ URL::to('/') }}/img/logo.png"/></a>
            <a class="navbar-brand" href="{{ URL::to('/war-room') }}"><img src="{{ URL::to('/') }}/img/alive_warroom_tiny.png"/></a>

        </div>
        <div class="navbar-collapse collapse">
        	<ul class="nav navbar-nav">
                <li{{ (Request::is('war-room/personnel*') ? ' class="active"' : '') }}><a href="{{ URL::to('war-room') }}/personnel">Personnel</a></li>
                <li{{ (Request::is('war-room/ops*') ? ' class="active"' : '') }}><a href="{{ URL::to('war-room') }}/operations">Operations</a></li>

                <li{{ (Request::is('war-room/orbat*') ? ' class="active"' : '') }}><a href="{{ URL::to('war-room') }}/orbat">ORBAT</a></li>
               <!--
                <li{{ (Request::is('war-room/AO*') ? ' class="active"' : '') }}><a href="{{ URL::to('war-room') }}/">Maps</a></li>
                -->
            </ul>
            <ul class="nav navbar-nav pull-right">

				@if ($auth['profile']->clan_id)
                	<li {{ (Request::is('war-room/showorbat*') ? 'class="active"' : '') }}><a href="{{ URL::to('war-room/showorbat') }}/{{ $auth['profile']->clan_id }}">Your Group</a></li>
				@else
                	<li {{ (Request::is('admin/clan/create*') ? 'class="active"' : '') }}><a href="{{ URL::to('admin/clan/create') }}">Create a Group</a></li>
                    <li {{ (Request::is('admin/application*') ? 'class="active"' : '') }}><a href="{{ URL::to('admin/application') }}">Join a Group</a></li>
                @endif
                <li {{ (Request::is('admin/user/show/*') ? 'class="active"' : '') }}><a href="{{ URL::to('admin/user/show/') }}/{{ Sentry::getUser()->getId() }}">Profile</a></li>
 
                @if ($auth['isAdmin'])
                <li {{ (Request::is('admin/ao*') ? 'class="active"' : '') }}><a href="{{ URL::to('admin/ao') }}">AOs</a></li>
                <li {{ (Request::is('admin/clan*') ? 'class="active"' : '') }}><a href="{{ URL::to('admin/clan') }}">Groups</a></li>
                <li {{ (Request::is('admin/server*') ? 'class="active"' : '') }}><a href="{{ URL::to('admin/server') }}">Servers</a></li>
                <li {{ (Request::is('admin/user*') ? 'class="active"' : '') }}><a href="{{ URL::to('admin/user') }}">Users</a></li>
                <li {{ (Request::is('admin/group*') ? 'class="active"' : '') }}><a href="{{ URL::to('admin/group') }}">User Groups</a></li>
                @endif

                <li id="logout"><a href="{{ URL::to('user/logout') }}">Logout</a></li>
            </ul>
        </div>
    </div>
</div>