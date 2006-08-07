/* FastInit Version 1.1 by Andrew Tetlaw */
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
		if(!func or typeof func != 'function') return;
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