//	HUMANIZED UNDO
//	Code: http://humanized.com/weblog/2007/09/21/undo-made-easy-with-ajax-part-15/


//-------------------------------------------------------------------------
// GLOBAL VARIABLES
// ------------------------------------------------------------------------

// Holds the to-do items which have been deleted on the client side. Because
// the user could undo the deletes, the fact of their deletion has not yet
// been sent to the server.
var EVENT_QUEUE = [];

// How long we should wait after the animation for to-do item deletion
// completes before we should show/hide the undo link.
var UNDO_DISPLAY_WAIT = 300;

// The name of the cookie that stores the event queue.
var COOKIE_NAME = "event queue cookie";

// The URL to submit the AJAX call to
var AJAXURL = '';

//-------------------------------------------------------------------------
// GLOBAL EVENT BINDING
// ------------------------------------------------------------------------

jQuery(document).ready(function() {
	jQuery(window).unload( doUnload );
});


//-------------------------------------------------------------------------
// HELPER FUNCTION
// ------------------------------------------------------------------------

// IE doesn't have a array.toSource function, so we have to implement one.
// It does the inverse of eval. So eval(toSource([1,2,3])) = [1,2,3].
function toSource( list ) {
	return '["%s"]'.replace( "%s", list.join('","') );
}

//-------------------------------------------------------------------------
// IMPLEMENTATION
// ------------------------------------------------------------------------

// Handles the logic for showing and hiding the undo link.
function updateUndoLink(){
	// If there are no items that are undoable, hide the undo link.
	if( EVENT_QUEUE.length == 0 )
		setTimeout( 'jQuery("#undo").fadeOut();', UNDO_DISPLAY_WAIT )

	// If there are any items that are undoable, show the undo link.
	else if ( EVENT_QUEUE.length == 1 ) {
		jQuery("#levels").text( "" );

		// Only do the fade in if the undo link is currently hidden.
		if( jQuery("#undo").css("display") == "none" )
			setTimeout( 'jQuery("#undo").fadeIn()', UNDO_DISPLAY_WAIT );
	}

	// If there is more than one thing that can be undone, then let the user
	// know how many levels of undo there are.
	else if ( EVENT_QUEUE.length > 1 )
		jQuery("#levels").text( "("+EVENT_QUEUE.length+")" );

	saveEventQueueCookie();
}

// Saves the undo state to a cookie
function saveEventQueueCookie() {
	jQuery.cookie( COOKIE_NAME, toSource(EVENT_QUEUE) );
}

// Clears the undo state cookie
function clearEventQueueCookie() {
	jQuery.cookie( COOKIE_NAME, null );
}

// Loads the event queue from the cookie, dealing with
// all edge cases.
function getEventQueueFromCookie() {
	var eventQueue = eval( jQuery.cookie(COOKIE_NAME) );
	
	if ( eventQueue == null )
		eventQueue = [];
	
	return eventQueue;
}

// Syncs the event queue with the state from the event queue
// cookie.
function syncEventQueueWithCookie(){  
	EVENT_QUEUE = getEventQueueFromCookie();
	
	// Hide the uncommited but deleted items.
	for (var i in EVENT_QUEUE)
		jQuery("#"+EVENT_QUEUE[i]).hide();
	
	// Show (without animation) the undo button.
	if (EVENT_QUEUE.length > 0) {
		jQuery("#undo").show();
		updateUndoLink();
	}    
}

// Sets up all event binding.
function doInit(url) {
	AJAXURL = url;

	// Handler for clicking on the Undo link
	jQuery("#undo").click( function() {
		var trashedModule = EVENT_QUEUE.pop();

		// Get the origin sidebar ID of the trashed module
		var trashedClasses = jQuery('#'+trashedModule).attr('class').split(' ');
		for (i = 0; i < trashedClasses.length; i++) if (trashedClasses[i] == 'sidebar-1' || 'sidebar-2' || 'disabled') var origin = trashedClasses[i];

		// Get the last to-do item added to the event queue and un-hide it.
		jQuery('#'+trashedModule).appendTo('#'+origin).slideDown('fast')

		updateUndoLink();
	})
	
	syncEventQueueWithCookie();
}

// When the user navigates away from the current page, let the server know
// which to-dos have been deleted.
function doUnload(){
	otherEventQueue = getEventQueueFromCookie();
	
	// Comparing two arrays as equal always returns false, even if they have
	// the same content. So we convert to their string representation
	// to do the comparison.
	// 
	// If this page's event queue is not in sync with the latest saved-in-the-
	// cookie event queue, we know that this page is out-of-sync with
	// the last-modified page. So we sync the event queue to cookie event
	// queue
	if (EVENT_QUEUE.toString() != otherEventQueue.toString())
		EVENT_QUEUE = otherEventQueue;
	
	 // Otherwise, we commit the deletions.
	else {
		for (var i=0; i < EVENT_QUEUE.length; i++)

			// Kill many bothams to bring the server the following information
			jQuery.post(AJAXURL + "?action=remove", {
				action:		"remove",
				module_id:	EVENT_QUEUE[i].toString()
			});

		// We've commited everything to the server. We can safely kill the saved event queue.
		clearEventQueueCookie();
	}
}



/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

/**
 * Create a cookie with the given name and value and other optional parameters.
 *
 * @example jQuery.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example jQuery.cookie('the_cookie', 'the_value', {expires: 7, path: '/', domain: 'jquery.com', secure: true});
 * @desc Create a cookie with all available options.
 * @example jQuery.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example jQuery.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value.
 *
 * @param String name The name of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name jQuery.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given name.
 *
 * @example jQuery.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String name The name of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name jQuery.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        var path = options.path ? '; path=' + options.path : '';
        var domain = options.domain ? '; domain=' + options.domain : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};