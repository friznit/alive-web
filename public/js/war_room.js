$(document).ready(function() {

    $("#live_feed_container").mCustomScrollbar({
        scrollButtons:{
            enable:true
        },
        advanced:{
            updateOnContentResize: true
        },
        autoHideScrollbar:true,
        theme:"light-thin"
    });

    $("#recent_ops_container").mCustomScrollbar({
        scrollButtons:{
            enable:true
        },
        advanced:{
            updateOnContentResize: true
        },
        autoHideScrollbar:true,
        theme:"light-thin"
    });

    $("#t1operators_container").mCustomScrollbar({
        scrollButtons:{
            enable:true
        },
        advanced:{
            updateOnContentResize: true
        },
        autoHideScrollbar:true,
        theme:"light-thin"
    });

    $("#warroom_charts_toggle").click(function(e){

        e.preventDefault();

        var toggler = $('#warroom_charts_toggle');
        var container = $('#warroom_charts');
        var table = container.children('.row');
        var timeline = new TimelineLite();

        if(container.hasClass('clicked')){
            timeline.to(container, .2, {css:{top:792}}).to(container, .2, {height:200}).to(table, .2, {autoAlpha:1});
            toggler.html('<i class="fa fa-arrow-down"></i>');
            container.removeClass('clicked');
        } else {
            timeline.to(table, .2, {autoAlpha:0}).to(container, .2, {height:25}).to(container, .2, {css:{top:930}});
            toggler.html('<i class="fa fa-arrow-up"></i>');
            container.addClass('clicked');
        }
    });

    $("#warroom_recent_toggle").click(function(e){

        e.preventDefault();

        var toggler = $('#warroom_recent_toggle');
        var container = $('#warroom_recent');
        var table = $('#recent_ops_container');
        var timeline = new TimelineLite();

        if(container.hasClass('clicked')){
            timeline.to(container, .2, {css:{left:10}}).to(container, .2, {height:330}).to(table, .2, {autoAlpha:1});
            toggler.html('<i class="fa fa-arrow-left"></i>');
            container.removeClass('clicked');
        } else {
            timeline.to(table, .2, {autoAlpha:0}).to(container, .2, {height:20}).to(container, .2, {css:{left:-250}});
            toggler.html('<i class="fa fa-arrow-right"></i>');
            container.addClass('clicked');
        }
    });

    $("#warroom_t1_toggle").click(function(e){

        e.preventDefault();

        var toggler = $('#warroom_t1_toggle');
        var container = $('#warroom_t1operators');
        var table = $('#t1operators_container');
        var timeline = new TimelineLite();

        if(container.hasClass('clicked')){
            timeline.to(container, .2, {css:{left:10}}).to(container, .2, {height:330}).to(table, .2, {autoAlpha:1});
            toggler.html('<i class="fa fa-arrow-left"></i>');
            container.removeClass('clicked');
        } else {
            timeline.to(table, .2, {autoAlpha:0}).to(container, .2, {height:20}).to(container, .2, {css:{left:-250}});
            toggler.html('<i class="fa fa-arrow-right"></i>');
            container.addClass('clicked');
        }
    });

    $("#warroom_livefeed_toggle").click(function(e){

        e.preventDefault();

        var toggler = $('#warroom_livefeed_toggle');
        var container = $('#warroom_livefeed');
        var table = $('#live_feed_container');
        var label = $('#warroom_livefeed_label');
        var strip = container.children('.strip');
        var timeline = new TimelineLite();

        if(container.hasClass('clicked')){
            timeline.to(strip, .2, {width:300}).to(container, .2, {height:500}).to(container, .2, {width:300});
            timeline.to(container, .2, {css:{right:10}});
            timeline.to(table, .2, {autoAlpha:1});
            toggler.html('<i class="fa fa-arrow-right"></i>');
            label.text('Live event feed');
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

    $("#warroom_notice_close").click(function(e){

        e.preventDefault();

        var container = $('#warroom_notice');
        var timeline = new TimelineLite();

        timeline.to(container, .2, {autoAlpha:0});

    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target);
        if(target.attr('href') == '#two'){
            $(window).resize();
        }
    });

    MyCustomMarker = L.Marker.extend({

        bindPopup: function(htmlContent, options) {

            if (options && options.showOnMouseOver) {

                // call the super method
                L.Marker.prototype.bindPopup.apply(this, [htmlContent, options]);

                // unbind the click event - change to launching AO page
                this.off("click", this.openPopup, this);

                // bind to mouse over
                this.on("mouseover", function(e) {

                    // get the element that the mouse hovered onto
                    var target = e.originalEvent.fromElement || e.originalEvent.relatedTarget;
                    var parent = this._getParent(target, "leaflet-popup");

                    // check to see if the element is a popup, and if it is this marker's popup
                    if (parent == this._popup._container)
                        return true;

                    // show the popup
                    this.openPopup();

                }, this);

                // and mouse out
                this.on("mouseout", function(e) {

                    // get the element that the mouse hovered onto
                    var target = e.originalEvent.toElement || e.originalEvent.relatedTarget;

                    // check to see if the element is a popup
                    if (this._getParent(target, "leaflet-popup")) {

                        L.DomEvent.on(this._popup._container, "mouseout", this._popupMouseOut, this);
                        return true;

                    }

                    // hide the popup
                    this.closePopup();

                }, this);

            }

        },

        _popupMouseOut: function(e) {

            // detach the event
            L.DomEvent.off(this._popup, "mouseout", this._popupMouseOut, this);

            // get the element that the mouse hovered onto
            var target = e.toElement || e.relatedTarget;

            // check to see if the element is a popup
            if (this._getParent(target, "leaflet-popup"))
                return true;

            // check to see if the marker was hovered back onto
            if (target == this._icon)
                return true;

            // hide the popup
            this.closePopup();

        },

        _getParent: function(element, className) {

            var parent = element.parentNode;

            while (parent != null) {

                if (parent.className && L.DomUtil.hasClass(parent, className))
                    return parent;

                parent = parent.parentNode;

            }

            return false;

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
    if (diff <= 15552000) {return Math.round(diff / 2592000) + " months ago";}
	if (diff > 15552000) {return "Over 6 months ago";}
    return "on " + system_date;
}



