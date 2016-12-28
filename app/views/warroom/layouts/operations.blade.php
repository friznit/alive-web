<!DOCTYPE html>
<html>
<head>
    <title>ALiVE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css" rel="stylesheet" >
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap-theme.min.css" rel="stylesheet" >
    <!-- Marcel
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.6/leaflet.css" />
    -->
    <link href="{{ URL::to('/') }}/css/style.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.2/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700|Roboto:400,900,500italic,500,300' rel='stylesheet' type='text/css'>
    <script src="https://code.jquery.com/jquery.js"></script>
    <script src="{{ URL::to('/') }}/js/greenthumb/plugins/CSSPlugin.min.js"></script>
    <script src="{{ URL::to('/') }}/js/greenthumb/easing/EasePack.min.js"></script>
    <script src="{{ URL::to('/') }}/js/greenthumb/TweenLite.min.js"></script>
    <script src="{{ URL::to('/') }}/js/greenthumb/TimelineLite.min.js"></script>
    <script src="http://cdn.leafletjs.com/leaflet-0.6/leaflet.js"></script>
    <script src="{{ URL::to('/') }}/js/operations.js"></script>
    <script src="{{ URL::to('/') }}/js/Control.FullScreen.js"></script>
    <script src="{{ URL::to('/') }}/js/polylineDecorator.js"></script>
    <!-- TimelineJS -->
    <!-- Marcel
    <script type="text/javascript" src="{{ URL::to('/') }}/js/storyjs-embed.js"></script>
    -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script>
        var replayDetails = {"map":"{{ Input::get('map') }}", "tileSubDomains":false};
        var sharedPresets = {};
        var cacheAvailable = false;
        var mappingConfig = {
            "chernarus_summer": "chernarus",
            "chernarus_winter": "chernarus",
            "thirskw": "thirsk"
        };
    </script>
    <script type="text/javascript">
        var webPath = '{{ URL::to('/') }}/r3';
        var webRoot = '{{ URL::to('/') }}';
        var configDefaults = { speed: 10 };
        var opName = '{{ addslashes(Input::get('name')) }}';
        var opMap = '{{ addslashes(Input::get('map')) }}';
        var opClan = '{{ addslashes(Input::get('clan')) }}';
        var opSize = '{{ AO::where('configName', '=', Input::get('map'))->first()->size }}'
    </script>

    <link rel="stylesheet" href="{{ URL::to('/') }}/r3/assets/app.css">
    <script src="{{ URL::to('/') }}/r3/assets/app-third-party.min.js"></script>
    <script src="{{ URL::to('/') }}/r3/assets/app.js"></script>
</head>
<body id="warroom_operations_body">
@include('warroom/partials/_nav')

@yield('content')

</body>
</html>
