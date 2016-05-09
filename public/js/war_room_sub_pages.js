$(document).ready(function() {

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target);
        if(target.attr('href') == '#tab_tempo'){
            $(window).resize();
        }
    });

});

function getServerDetails(ipaddr) {
    var url = 'http://zeus-community.net/query/armaquery.php?server=' + ipaddr +':2302&lang=en';
    var serverName = $("<div/>'").load(url, function() {
        $("*:contains(Hostname)").closest("td").next().text();
    });

    return serverName;
}

function parseArmaDate(input) {
    var system_date = new Date(input);
    var user_date = new Date();
    var diff = Math.floor((user_date - system_date) / 1000);
    if (diff < 0) {return "on " + system_date;}
    if (diff <= 1) {return "just now";}
    if (diff < 20) {return diff + " seconds ago";}
    if (diff < 40) {return "half a minute ago";}
    if (diff < 60) {return "less than a minute ago";}
    if (diff <= 90) {return "one minute ago";}
    if (diff <= 3540) {return Math.round(diff / 60) + " minutes ago";}
    if (diff <= 5400) {return "1 hour ago";}
    if (diff <= 86400) {return Math.round(diff / 3600) + " hours ago";}
    if (diff <= 129600) {return "1 day ago";}
    if (diff < 604800) {return Math.round(diff / 86400) + " days ago";}
    if (diff <= 907200) {return "1 week ago";}
    if (diff < 2419200) {return Math.round(diff / 604800) + " weeks ago";}
    if (diff <= 3888000) {return "1 month ago";}
    if (diff < 15552000) {return Math.round(diff / 2592000) + " months ago";}
    if (diff => 15552000) {return "Over 6 months ago";}
    return "on " + system_date;
}




