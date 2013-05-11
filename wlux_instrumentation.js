// This is the main WebLabUX test site instrumentation & data reporting
// code. Anything that needs to be done when the page is loaded should
// be added to the start() function in our WLUX module (which is called
// by the jQuery ready() function at the bottom of this file).

// The code below assumes the first page is passed a query string
// parameter containing the session id, which it uses to query the
// server for additional info.
// Upon opening, the page checks for a wlux_session query string
// parameter. If found, it makes it a cookie and immediately redirects
// the page so the param is no longer part of the url. If not found, it
// checks for the cookie.

// avoid conflicting with jQuery on the study site (rename $ to $wlux)
$wlux = jQuery.noConflict();

// We're using the Javascript module pattern to avoid polluting the
// global namespace. The only object we'll export is called WLUX, which
// will be used to access any functions or properties we wish to make
// publicly available.
var WLUX = (function() {

    /***************************************************************
     *             Variables global to the module                  *
     **************************************************************/

    var sessIdKey = "wlux_session";
    var condIdKey = "wlux_condition";
    var SESSION_ID = null;
    var done = false; // are we done loading wlux?
    var study_data = {};

    // set the url dynamically depending on whether we're in the
    // development environment or the production environment
    var host = window.location.host;
    var LOCAL = (host.indexOf("localhost") != -1) ||
                (host.indexOf("127.0.0.1") != -1);

    var loggerURL = "http://staff.washington.edu/rbwatson/logger.php";
    var studyDataURL = "http://staff.washington.edu/rbwatson/study_data.php";

    if (LOCAL) {
        loggerURL = "/server/logger.php";
        studyDataURL = "/server/study_data.php";
    }

    /***************************************************************
     *                   Function Definitions                      *
     **************************************************************/

    // gets a querystring parameter using its key
    // http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values
    function getQsParam(key) {
        var match = RegExp('[?&]' + key + '=([^&]*)')
                        .exec(window.location.search);
        return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
    }

    // logs page transitions.
    function logTransition(from, to, a_id, a_class) {
        // $.post is asynchronous by default, which causes problems if the
        // browser decides to follow the link before carrying out our request
        $wlux.ajaxSetup({async: false}); //
        // if undefined, use ""
        a_id = a_id || "";
        a_class = a_class || "";
        $wlux.post(loggerURL, {"data" : {"type": "transition",
                                         "wlux_session": SESSION_ID,
                                         "conditionId" : study_data.conditionId,
                                         "from": from,
                                         "to": to,
                                         "a_class": a_class,
                                         "a_id": a_id}
                              }
                  );
    }

    // logs page openings
    function logOpen() {
        $wlux.ajaxSetup({async: false}); // do this immediately
        $wlux.post(loggerURL, {"data" : {"type": "open",
                                         "wlux_session": SESSION_ID,
                                         "conditionId" : study_data.conditionId,
                                         "location": window.location.href}
                              }
                  );
    }

    // loads styles from the css file at the url supplied to
    // us with the study data
    function loadCSS() {
        // IE requires us to call this ie-only function
        if (document.createStyleSheet) {
            document.createStyleSheet(study_data.cssURL);
    		document.createStyleSheet(study_data.taskBarCSS);
        } else {
            var css = $wlux('<link>').attr({'rel': 'stylesheet',
                                            'type': 'text/css',
                                            'href': study_data.cssURL});
            $wlux('head').append(css);
            css = $wlux('<link>').attr({'rel': 'stylesheet',
                                            'type': 'text/css',
                                            'href': study_data.taskBarCSS});
            $wlux('head').append(css);
        }
    }


    // Adds a hideable taskbar to the top of the page, which includes task text
    // and an 'End Study' button that sends the user to the return url.
    function setupTaskbar() {		
        var button = $wlux('<button>').attr({'class': 'wlux_button'})
                                      .text(study_data.buttonText);
        // Appending session id will not be necessary when server stores its own cookie/session
        var link = $wlux('<a>').attr({'href': study_data.returnURL + "?wlux_session=" + SESSION_ID, 
									  'class': 'wlux_button_link'});
							   
		var buttonDiv = $wlux('<div>').attr({'id': 'wlux_return',
											 'class': 'wlux_button_div'});
										  
        var taskbar = $wlux('<div>').attr({'id': 'wlux_taskbar',
										   'class': 'wlux_taskbar'});
										   
        var toggle = $wlux('<div>').attr({'id': 'wlux_toggle',
										  'class': 'wlux_tab'})
                                   .text(study_data.tabHideText);
								   
		if ((undefined === study_data.taskHTML) || (study_data.taskHTML.length == 0)) {
			var taskText = $wlux('<p>').attr({'class': 'wlux_taskbar_text'})
										.text(study_data.taskText);
		} else {
			var taskText = study_data.taskHTML;
		}
        // animate the taskbar
        toggle.click(function() {
			// getting the taskbar element dynamically at run-time
			// so the taskbar can be configured in the .css file
			var localTaskbar = $wlux('#wlux_taskbar');
			var localTaskbarHeight = localTaskbar[0].clientHeight;
            var hidden = localTaskbar[0].offsetTop >= 0 ? false : true;
            if (hidden) { // show the taskbar
                localTaskbar.animate({top: '0px'});
                // toggle.animate({top: localTaskbarHeight});
                toggle.text(study_data.tabHideText);
                localTaskbar.removeClass('hidden');
            } else { // hide the taskbar
                localTaskbar.animate({top: '-' + localTaskbarHeight});
                toggle.text(study_data.tabShowText);
                localTaskbar.addClass('hidden');
            } 
        });

        // layout the taskbar
        taskbar.append(taskText);
        $wlux('body').append(taskbar);
        taskbar.append(toggle);
		// new to add button to frame, not hideable task info bar
		buttonDiv.append(link);
		$wlux('body').append(buttonDiv);
		// new to add button to frame, not hideable task info bar
        link.append(button);
		buttonDiv.append(link);
		$wlux('body').append(buttonDiv);

        // adds some margin to the direct children of the taskbar
        $wlux("#wlux_taskbar > *").css("margin",".3em");

        // automatically hide the taskbar after 2 seconds, so the user doesn't need to
        // do it manually on every page load
		// ** per design review: we want the user to hide it manually.
        // setTimeout(function() { toggle.trigger('click'); }, 2000);
    }

    // Currently only handles page transitions caused by clicking anchor tags.
    function addClickHandlers() {
        // Log transitions via anchor tags.
        $wlux("a").click(function(e) {
            var from = window.location.href;
            // currentTarget is necessary for clicking of buttons wrapped in an a tag
            var to = e.target.href || e.currentTarget.href;
            var a_id = $wlux(this).attr('id');
            var a_class = $wlux(this).attr('class');
            logTransition(from, to, a_id, a_class);
        });
    }

    // Returns the value of the given cookie if the user has it
    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }

    // Removes the given parameter from the URL, preserving other parameters and hash
    function removeQsParam(key, url) {
        var re = new RegExp("&" + key + "(=[^&]*)?|^" + key + "(=[^&]*)?&?");

        // grab the query string if it exists
        var qsIndex = url.indexOf('?');
        if (qsIndex != -1) {
            var baseURL = url.substr(0, qsIndex);
            var qs = url.substr(qsIndex+1);
        }

        // grab the fragment if it exists
        var hashIndex = qs.indexOf('#');
        if (hashIndex != -1) {
            var fragment = qs.substr(hashIndex);
            qs = qs.substr(0, hashIndex);
        }

        var newQs = qs.replace(re, "");

        var newURL = baseURL;
        if (newQs)
            newURL += "?" + newQs;
        if (fragment)
            newURL += fragment;

        return newURL;
    }

    /***************************************************************
     *                 Functions Called Externally                 *
     **************************************************************/

    // This function will be called immediately, when this script is
    // being parsed (i.e. crucially, before the body loads).
    // Instead of disabling, enabling clicks (which would require
    // waiting for the page to load), we just hide the body as soon
    // as it loads, and show it again later, in the start function
    var interval;
    function preLoad() {
        // try to find/hide body as the page loads, to keep users from interacting
        // with the page until all of our instrumentation is setup
        interval = setInterval(function() {
            var body = $wlux('body');
            if (done) { // set in ajax success callback after study data is loaded
                // the page loaded really fast - even before this method was called
                // clear the timeout and don't hide the body, we're DONE
                clearInterval(interval);
                return;
            }
            if (body.length > 0) {
                body.css({'visibility': 'hidden'});
                clearInterval(interval);
            }
        }, 50); // try every 50ms
    }

    // This function will be called on dom ready.
    // SESSION_ID and study_data should have already been set in preLoad()
    function start() {
        var sessionId = getQsParam(sessIdKey);
        // Store a cookie if session parameter was passed, cookie expires after browser is closed
        if (sessionId !== null) {
            document.cookie = "wluxSessionCookie="+sessionId+"; path=/";
            window.location.replace(removeQsParam("wlux_session", document.URL));
        }

        SESSION_ID = readCookie("wluxSessionCookie");
        if (SESSION_ID === null)
            return;

        // request the study data from the server asynchronously
        // (via JSONP to avoid the browser's same origin policy) while we're
        // waiting for the page to load
        $wlux.ajax({
            url: studyDataURL,
            type: "GET",
            dataType: "jsonp",
            data: {"wlux_session": SESSION_ID},
            success: function(data) {
                study_data = data; // just store it locally for later use

                logOpen();
                loadCSS();
                setupTaskbar();
                addClickHandlers();

                // everything is loaded and setup. show the body
                $wlux('body').css({'visibility': 'visible'});
                done = true;
            },
            error: function() {
                // error getting study data, ensure body is visible and return
                $wlux('body').css({'visibility': 'visible'});
                done = true;
                return;
            },
            cache: true // cache the results, in case subsequent pages make the same request
        });
    }

    // Module pattern - functions and variables added to exports object
    // will become parte of the WLUX object, everything else is private
    var exports = {};
    exports.preLoad = preLoad;
    exports.start = start;

    return exports;
})(); // module pattern - we've created an anonymous function and immediately call it

// use for initial set up, currently just hides the body
WLUX.preLoad();

// do wlux stuff as soon as the dom is ready
$wlux(document).ready(function() {
    WLUX.start();
});
