<?php
	// check to see if the user has enabled gzip compression in the WordPress admin panel
	if ( ob_get_length() === FALSE and !ini_get('zlib.output_compression') and ini_get('output_handler') != 'ob_gzhandler' and ini_get('output_handler') != 'mb_output_handler' ) {
		ob_start('ob_gzhandler');
	}

	// The headers below tell the browser to cache the file and also tell the browser it is JavaScript.
	header("Cache-Control: public");
	header("Pragma: cache");

	$offset = 60*60*24*60;
	$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT";
	$LmStr = "Last-Modified: ".gmdate("D, d M Y H:i:s",filemtime(__FILE__))." GMT";

	header($ExpStr);
	header($LmStr);
	header('Content-Type: text/javascript; charset: UTF-8');
?>

function OnLoadUtils() {
	$("comment-personaldetails").style.display = "none";
	$("showinfo").style.display = "";
	$("hideinfo").style.display = "none";
}

function ShowUtils() {
	new Effect.BlindDown('comment-personaldetails', {duration: 0.3});
	new Effect.Appear('comment-personaldetails', {duration: 0.3});
	//new Effect.Appear($('commentlist').lastChild, { duration: 1.0, afterFinish: function() { new Effect.ScrollTo($('commentlist').lastChild); } } );
	$("showinfo").style.display = "none";
	$("hideinfo").style.display = "";
}

function HideUtils() {
	new Effect.Fade('comment-personaldetails', {duration: 0.3});
	new Effect.BlindUp('comment-personaldetails', {duration: 0.3});
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
 * Version 1.2 (2006-10-19)
 * Based on:
 * http://dean.edwards.name/weblog/2006/03/faster
 * http://dean.edwards.name/weblog/2006/06/again/
 * 
 */
var FastInit = {
	done : false,
	onload : function() {
		if (FastInit.done) return;
		FastInit.done = true;
		FastInit.actions.each(function(func) {
			func();
		})
	},
	actions : $A([]),
	addOnLoad : function() {
		for(var x = 0; x < arguments.length; x++) {
			var func = arguments[x];
			if(!func || typeof func != 'function') continue;
			FastInit.actions.push(func);
		}
	}
}

if (/WebKit|khtml/i.test(navigator.userAgent)) {
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
