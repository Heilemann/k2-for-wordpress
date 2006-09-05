// ABC Boot Loader Simon: http://simon.incutio.com/archive/2004/05/26/addLoadEvent
function addLoadEvent(func) {
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			oldonload();
			func();
		}
	}
}


function OnLoadUtils() {
	$("comment-personaldetails").style.display = "none";
	$("showinfo").style.display = "";
	$("hideinfo").style.display = "none";
}

function ShowUtils() {
	new Effect.Phase('comment-personaldetails', {duration: 0.3});
	//new Effect.Appear($('commentlist').lastChild, { duration: 1.0, afterFinish: function() { new Effect.ScrollTo($('commentlist').lastChild); } } );
	$("showinfo").style.display = "none";
	$("hideinfo").style.display = "";
}

function HideUtils() {
	new Effect.Phase('comment-personaldetails', {duration: 0.3});
	$("showinfo").style.display = "";
	$("hideinfo").style.display = "none";
}


// Manipulation of cookies (credit: http://www.webreference.com/js/column8/functions.html)
function setCookie(name, value, expires, path, domain, secure) {
  var curCookie = name + "=" + escape(value) +
      ((expires) ? "; expires=" + expires.toGMTString() : "") +
      ((path) ? "; path=" + path : "") +
      ((domain) ? "; domain=" + domain : "") +
      ((secure) ? "; secure" : "");
  document.cookie = curCookie;
}

function getCookie(name) {
  var dc = document.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1) {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } else
    begin += 2;
  var end = document.cookie.indexOf(";", begin);
  if (end == -1)
    end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}

function deleteCookie(name, path, domain) {
  if (getCookie(name)) {
    document.cookie = name + "=" +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    "; expires=Thu, 01-Jan-70 00:00:01 GMT";
  }
}

/*
 * FastInit
 * http://tetlaw.id.au/view/blog/prototype-class-fastinit/
 * Andrew Tetlaw
 * Version 1.1 (2006-06-19)
 * Based on:
 * http://dean.edwards.name/weblog/2006/03/faster
 * http://dean.edwards.name/weblog/2006/06/again/
 * 
 *  
 * http://creativecommons.org/licenses/by-sa/2.5/
 */
var FastInit = Class.create();

Object.extend(FastInit, {
	done : false,
	onload : function() {
		if (FastInit.done) return;
		FastInit.done = true;
		FastInit.actions.each(function(func) {
			func();
		})
	},
	actions : $A([]),
	addOnLoad : function(func) {
		if(!func || typeof func != 'function') return;
		FastInit.actions.push(func);
	}
});

FastInit.prototype = {
	initialize : function() {
		for(var x = 0; x < arguments.length; x++) {
			if(arguments[x]) FastInit.addOnLoad(arguments[x]);
		}
		
		if (/WebKit/i.test(navigator.userAgent)) {
    		var _timer = setInterval(function() {
		        if (/loaded|complete/.test(document.readyState)) {
		            clearInterval(_timer);
		            delete _timer;
		            FastInit.onload();
		        }
	    	}, 10);
		}
		if (document.addEventListener) {
			document.addEventListener('DOMContentLoaded', FastInit.onload, false);
			FastInit.legacy = false;
		}
		
		Event.observe(window, 'load', FastInit.onload);
	}
}

/*@cc_on @*/
/*@if (@_win32)
document.write('<script id="__ie_onload" defer src="javascript:void(0)"><\/script>');
var script = $('__ie_onload');
script.onreadystatechange = function() {
    if (this.readyState == 'complete') {
        FastInit.onload();
    }
};
/*@end @*/