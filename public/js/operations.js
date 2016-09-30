$(document).ready(function() {

    /*
    $("#event_container").mCustomScrollbar({
        scrollButtons:{
            enable:true
        },
        advanced:{
            updateOnContentResize: true
        },
        autoHideScrollbar:true,
        theme:"light-thin"
    });

    $("#event_container_toggle").click(function(e){

        e.preventDefault();

        var toggler = $('#event_container_toggle');
        var container = $('#event_container');
        var table = $('#event_container_data');
        var label = $('#event_container_data_label');
        var strip = container.children('.strip');
        var timeline = new TimelineLite();

        if(container.hasClass('clicked')){
            timeline.to(strip, .2, {width:300}).to(container, .2, {height:500}).to(container, .2, {width:300});
            timeline.to(container, .2, {css:{right:10}});
            timeline.to(table, .2, {autoAlpha:1});
            toggler.html('<i class="fa fa-arrow-right"></i>');
            label.text('Event log');
            timeline.to(label, .2, {autoAlpha:1});
            container.removeClass('clicked');
        } else {
            timeline.to(label, .2, {autoAlpha:0});
            label.text('');
            timeline.to(table, .2, {autoAlpha:0});
            timeline.to(strip, .2, {width:60}).to(container, .2, {height:24}).to(container, .2, {width:60});
            timeline.to(container, .2, {css:{right:0}});
            toggler.html('<i class="fa fa-arrow-left"></i>');
            container.addClass('clicked');
        }
    });
    */
});

function getServerDetails(ipaddr) {
    var url = 'http://zeus-community.net/query/armaquery.php?server=' + ipaddr +':2302&lang=en';
    var serverName = $("<div/>'").load(url, function() {
        $("*:contains(Hostname)").closest("td").next().text();
    });

    return serverName;
}

function counter(element, end) {
    var	$text	= element,
        endVal	= 0,
        currVal	= 0,
        obj	= {};

    obj.getTextVal = function() {
        return parseInt(currVal, 10);
    };

    obj.setTextVal = function(val) {
        currVal = parseInt(val, 10);
        $text.text(currVal);
    };

    obj.setTextVal(0);

    currVal = 0; // Reset this every time
    endVal = end;

    TweenLite.to(obj, 7, {setTextVal: endVal, ease: Power2.easeInOut});
};


function parseArmaDate(input) {
    var system_date = new Date(input);
    var user_date = new Date();
    var diff = Math.floor((user_date - system_date) / 1000);
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
    if (diff <= 777600) {return "1 week ago";}
		if (diff > 777600) {return "Over a week ago";}
    return "on " + system_date;
}



