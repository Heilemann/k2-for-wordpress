<?php require('header.php'); ?>

jQuery.noConflict();

if (typeof K2 == 'undefined') var K2 = {};

K2.debug = false;

K2.ajaxGet = function(url, data, complete_fn) {
	jQuery.ajax({
		url:		url,
		data:		data,

		error: function(request) {
			jQuery('#notices')
				.show()
				.append('<p class="alert">Error ' + request.status + ': ' + request.statusText + '</p>');
		},

		success: function() {
			jQuery('#notices').hide().html();
		},

		complete: function(request) {
			if ( K2.debug ) {
				console.log(request);
			}

			if ( complete_fn ) {
				complete_fn( request.responseText );
			}

			// Lightbox v2.03.3 - Adds new images to lightbox
			if (typeof myLightbox != "undefined" && myLightbox instanceof Lightbox && myLightbox.updateImageList) {
				myLightbox.updateImageList();
			}
		}
	});
}



function OnLoadUtils() {
	jQuery('#comment-personaldetails').hide();
	jQuery('#showinfo').show();
	jQuery('#hideinfo').hide();
};

function ShowUtils() {
	jQuery('#comment-personaldetails').slideDown();
	jQuery('#showinfo').hide();
	jQuery('#hideinfo').show();
};

function HideUtils() {
	jQuery('#comment-personaldetails').slideUp();
	jQuery('#showinfo').show();
	jQuery('#hideinfo').hide();
};


// Manipulation of cookies (credit: http://www.webreference.com/js/column8/functions.html)
function setCookie(name, value, expires, path, domain, secure) {
  var curCookie = name + "=" + escape(value) +
      ((expires) ? "; expires=" + expires.toGMTString() : "") +
      ((path) ? "; path=" + path : "") +
      ((domain) ? "; domain=" + domain : "") +
      ((secure) ? "; secure" : "");
  document.cookie = curCookie;
};

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
};

function deleteCookie(name, path, domain) {
  if (getCookie(name)) {
    document.cookie = name + "=" +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    "; expires=Thu, 01-Jan-70 00:00:01 GMT";
  }
};


/*
	Base, version 1.0.2
	Copyright 2006, Dean Edwards
	License: http://creativecommons.org/licenses/LGPL/2.1/
*/

var Base = function() {
	if (arguments.length) {
		if (this == window) { // cast an object to this class
			Base.prototype.extend.call(arguments[0], arguments.callee.prototype);
		} else {
			this.extend(arguments[0]);
		}
	}
};

Base.version = "1.0.2";

Base.prototype = {
	extend: function(source, value) {
		var extend = Base.prototype.extend;
		if (arguments.length == 2) {
			var ancestor = this[source];
			// overriding?
			if ((ancestor instanceof Function) && (value instanceof Function) &&
				ancestor.valueOf() != value.valueOf() && /\bbase\b/.test(value)) {
				var method = value;
			//	var _prototype = this.constructor.prototype;
			//	var fromPrototype = !Base._prototyping && _prototype[source] == ancestor;
				value = function() {
					var previous = this.base;
				//	this.base = fromPrototype ? _prototype[source] : ancestor;
					this.base = ancestor;
					var returnValue = method.apply(this, arguments);
					this.base = previous;
					return returnValue;
				};
				// point to the underlying method
				value.valueOf = function() {
					return method;
				};
				value.toString = function() {
					return String(method);
				};
			}
			return this[source] = value;
		} else if (source) {
			var _prototype = {toSource: null};
			// do the "toString" and other methods manually
			var _protected = ["toString", "valueOf"];
			// if we are prototyping then include the constructor
			if (Base._prototyping) _protected[2] = "constructor";
			for (var i = 0; (name = _protected[i]); i++) {
				if (source[name] != _prototype[name]) {
					extend.call(this, name, source[name]);
				}
			}
			// copy each of the source object's properties to this object
			for (var name in source) {
				if (!_prototype[name]) {
					extend.call(this, name, source[name]);
				}
			}
		}
		return this;
	},

	base: function() {
		// call this method from any other method to invoke that method's ancestor
	}
};

Base.extend = function(_instance, _static) {
	var extend = Base.prototype.extend;
	if (!_instance) _instance = {};
	// build the prototype
	Base._prototyping = true;
	var _prototype = new this;
	extend.call(_prototype, _instance);
	var constructor = _prototype.constructor;
	_prototype.constructor = this;
	delete Base._prototyping;
	// create the wrapper for the constructor function
	var klass = function() {
		if (!Base._prototyping) constructor.apply(this, arguments);
		this.constructor = klass;
	};
	klass.prototype = _prototype;
	// build the class interface
	klass.extend = this.extend;
	klass.implement = this.implement;
	klass.toString = function() {
		return String(constructor);
	};
	extend.call(klass, _static);
	// single instance
	var object = constructor ? klass : _prototype;
	// class initialisation
	if (object.init instanceof Function) object.init();
	return object;
};

Base.implement = function(_interface) {
	if (_interface instanceof Function) _interface = _interface.prototype;
	this.prototype.extend(_interface);
};

/*
 * jQuery EasIng v1.1.2 - http://gsgd.co.uk/sandbox/jquery.easIng.php
 *
 * Uses the built In easIng capabilities added In jQuery 1.1
 * to offer multiple easIng options
 *
 * Copyright (c) 2007 George Smith
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 */

// t: current time, b: begInnIng value, c: change In value, d: duration

jQuery.extend( jQuery.easing,
{
	easeInQuad: function (x, t, b, c, d) {
		return c*(t/=d)*t + b;
	},
	easeOutQuad: function (x, t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	easeInOutQuad: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t + b;
		return -c/2 * ((--t)*(t-2) - 1) + b;
	},
	easeInCubic: function (x, t, b, c, d) {
		return c*(t/=d)*t*t + b;
	},
	easeOutCubic: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t + 1) + b;
	},
	easeInOutCubic: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t + b;
		return c/2*((t-=2)*t*t + 2) + b;
	},
	easeInQuart: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t + b;
	},
	easeOutQuart: function (x, t, b, c, d) {
		return -c * ((t=t/d-1)*t*t*t - 1) + b;
	},
	easeInOutQuart: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
		return -c/2 * ((t-=2)*t*t*t - 2) + b;
	},
	easeInQuint: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t*t + b;
	},
	easeOutQuint: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t*t*t + 1) + b;
	},
	easeInOutQuint: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
		return c/2*((t-=2)*t*t*t*t + 2) + b;
	},
	easeInSine: function (x, t, b, c, d) {
		return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
	},
	easeOutSine: function (x, t, b, c, d) {
		return c * Math.sin(t/d * (Math.PI/2)) + b;
	},
	easeInOutSine: function (x, t, b, c, d) {
		return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
	},
	easeInExpo: function (x, t, b, c, d) {
		return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
	},
	easeOutExpo: function (x, t, b, c, d) {
		return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
	},
	easeInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	},
	easeInCirc: function (x, t, b, c, d) {
		return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
	},
	easeOutCirc: function (x, t, b, c, d) {
		return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
	},
	easeInOutCirc: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
		return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
	},
	easeInElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	},
	easeOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	},
	easeInOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	},
	easeInBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*(t/=d)*t*((s+1)*t - s) + b;
	},
	easeOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
	},
	easeInOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158; 
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	},
	easeInBounce: function (x, t, b, c, d) {
		return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
	},
	easeOutBounce: function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	},
	easeInOutBounce: function (x, t, b, c, d) {
		if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
		return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
	}
});