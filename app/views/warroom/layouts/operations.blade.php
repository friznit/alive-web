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
        var playerList = [{"id":"76561197982137286","name":"Tupolov"},{"id":"76561197960945508","name":"_bryan"},{"id":"76561197965741589","name":"Izayo"},{"id":"76561197969272205","name":"Cyruz"},{"id":"76561197970863175","name":"Insane"},{"id":"76561197972043388","name":"dancemoox"},{"id":"76561197972132272","name":"SachaL"},{"id":"76561197972356731","name":"Homercleese"},{"id":"76561197972639555","name":"Croc"},{"id":"76561197979135920","name":"Chairborne"},{"id":"76561197980085355","name":"Smokey"},{"id":"76561197987310887","name":"Vettic"},{"id":"76561197991616522","name":"GhostRaccoon"},{"id":"76561197996004327","name":"Dogface"},{"id":"76561197996120389","name":"Ardyvee"},{"id":"76561197999949105","name":"Draakon"},{"id":"76561198001868030","name":"Kami"},{"id":"76561198002257703","name":"Greg"},{"id":"76561198004613429","name":"Koliko"},{"id":"76561198006249023","name":"HeXeY"},{"id":"76561198006584405","name":"Shock"},{"id":"76561198011407435","name":"Satire"},{"id":"76561198011510219","name":"Fladderpony"},{"id":"76561198013420815","name":"Wally"},{"id":"76561198013702905","name":"Fluffeh"},{"id":"76561198014129701","name":"John"},{"id":"76561198014910208","name":"Soap"},{"id":"76561198018118154","name":"Collins"},{"id":"76561198024688998","name":"-Drew-"},{"id":"76561198024753356","name":"Lastobeth"},{"id":"76561198041914205","name":"N.Robson"},{"id":"76561198045218451","name":"Muffin"},{"id":"76561198064132532","name":"Ego"},{"id":"76561198085534064","name":"Cowboy"},{"id":"76561198116635068","name":"Lindahl The Viking"},{"id":"76561198215710585","name":"a123oclock"}];
        var cacheAvailable = true;
        var mappingConfig = {
            "chernarus_summer": "chernarus",
            "chernarus_winter": "chernarus",
            "thirskw": "thirsk"
        };
        /* var playerList = [{"id":"76561197982137286","name":"Tupolov"}]; */
    </script>
    <script type="text/javascript">
        var webPath = 'http://localhost:8080/r3';
        var configDefaults = { speed: 10 };
        var opName = '{{ Input::get('name') }}';
        var opMap = '{{ Input::get('map') }}';
        var opClan = '{{ Input::get('clan') }}';
        var opSize = '{{ AO::where('configName', '=', Input::get('map'))->first()->size }}'
    </script>

    <link rel="stylesheet" href="{{ URL::to('/') }}/r3/assets/app.css">
    <script src="{{ URL::to('/') }}/r3/assets/app-third-party.min.js"></script>
    <script src="{{ URL::to('/') }}/r3/assets/app.js"></script>
</head>
<body id="warroom_operations_body">

@yield('content')

</body>
</html>
