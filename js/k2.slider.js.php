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
/**
 * Interface Elements for jQuery
 * 
 * http://interface.eyecon.ro
 * 
 * Copyright (c) 2006 Stefan Petre
 * Dual licensed under the MIT (MIT-LICENSE.txt) 
 * and GPL (GPL-LICENSE.txt) licenses.
 *   
 *
 */

if (!K2) var K2 = {};

K2.Slider = {
	tabindex : 1,

	next : function (index) {
		var success = false;
		this.each(
			function()
			{
				var elm = this.slideCfg.sliders[index - 1];
				if ( !elm ) return true;
				
				K2.Slider.dragmoveBy(elm, [elm.dragCfg.gx||1, 0] );
				success = true;
				
				return false;
			}
			
		);
		return success;
	},

	prev : function (index) {
		var success = false;
		this.each(
			function()
			{
				var elm = this.slideCfg.sliders[index - 1];
				if ( !elm ) return true;
				
				K2.Slider.dragmoveBy(elm, [-elm.dragCfg.gx||1, 0] );
				success = true;
				
				return false;
			}
			
		);
		return success;
	},

	set : function (values)
	{
		var values = values;
		return this.each(
			function()
			{
				this.slideCfg.sliders.each(
					function (key) 
					{ 
						K2.Slider.dragmoveBy(this,values[key]);
					}
				);
			}
		);
	},
	
	get : function()
	{
		var values = [];
		this.each(
			function(slider)
			{
				if (this.isSlider) {
					values[slider] = [];
					var elm = this;
					var sizes = K2.iUtil.getSize(this);
					this.slideCfg.sliders.each(
						function (key) 
						{
							var x = this.offsetLeft;
							var y = this.offsetTop;
							xproc = Math.round(x * 100 / (sizes.w - this.offsetWidth));
							yproc = Math.round(y * 100 / (sizes.h - this.offsetHeight));
							values[slider][key] = [xproc||0, yproc||0, x||0, y||0];
						}
					);
				}
			}
		);
		return values;
	},
	
	modifyContainer : function (elm)
	{
		elm.dragCfg.containerMaxx = elm.dragCfg.cont.w - elm.dragCfg.oC.wb;
		elm.dragCfg.containerMaxy = elm.dragCfg.cont.h - elm.dragCfg.oC.hb;
		elm.dragCfg.maxx = elm.dragCfg.cont.w - elm.dragCfg.oC.wb;
		elm.dragCfg.maxy = elm.dragCfg.cont.h - elm.dragCfg.oC.hb;
		if(elm.dragCfg.fractions) {
			elm.dragCfg.gx = ((elm.dragCfg.cont.w - elm.dragCfg.oC.wb)/elm.dragCfg.fractions) || 1;
			elm.dragCfg.gy = ((elm.dragCfg.cont.h - elm.dragCfg.oC.hb)/elm.dragCfg.fractions) || 1;
			elm.dragCfg.fracW = elm.dragCfg.maxx / elm.dragCfg.fractions;
			elm.dragCfg.fracH = elm.dragCfg.maxy / elm.dragCfg.fractions;
		}
		
		elm.dragCfg.cont.dx = elm.dragCfg.cont.x - elm.dragCfg.oR.x;
		elm.dragCfg.cont.dy = elm.dragCfg.cont.y - elm.dragCfg.oR.y;
		
		K2.iDrag.helper.css('cursor', 'default');
	},
	
	onSlide : function(elm, x, y)
	{
		if (elm.dragCfg.fractions) {
				xfrac = Math.round(x/elm.dragCfg.fracW);
				xproc = xfrac * 100 / elm.dragCfg.fractions;
				yfrac = Math.round(y/elm.dragCfg.fracH);
				yproc = yfrac * 100 / elm.dragCfg.fractions;
		} else {
			xproc = Math.round(x * 100 / elm.dragCfg.containerMaxx);
			yproc = Math.round(y * 100 / elm.dragCfg.containerMaxy);
		}
		elm.dragCfg.lastSi = [xproc||0, yproc||0, x||0, y||0, xfrac||0, yfrac||0];
		if (elm.dragCfg.onSlide)
			elm.dragCfg.onSlide.apply(elm, elm.dragCfg.lastSi);
	},
	
	dragmoveByKey : function (event)
	{
		pressedKey = event.charCode || event.keyCode || -1;
		
		switch (pressedKey)
		{
			//end
			case 35:
				K2.Slider.dragmoveBy(this.dragElem, [2000, 2000] );
			break;
			//home
			case 36:
				K2.Slider.dragmoveBy(this.dragElem, [-2000, -2000] );
			break;
			//left
			case 37:
				K2.Slider.dragmoveBy(this.dragElem, [-this.dragElem.dragCfg.gx||-1, 0] );
			break;
			//up
			case 38:
				K2.Slider.dragmoveBy(this.dragElem, [0, -this.dragElem.dragCfg.gy||-1] );
			break;
			//right
			case 39:
				K2.Slider.dragmoveBy(this.dragElem, [this.dragElem.dragCfg.gx||1, 0] );
			break;
			//down;
			case 40:
				K2.iDrag.dragmoveBy(this.dragElem, [0, this.dragElem.dragCfg.gy||1] );
			break;
		}
	},
	
	dragmoveBy : function (elm, position) 
	{
		if (!elm.dragCfg) {
			return;
		}
		
		elm.dragCfg.oC = jQuery.extend(
			K2.iUtil.getPosition(elm),
			K2.iUtil.getSize(elm)
		);
		
		elm.dragCfg.oR = {
			x : parseInt(jQuery.css(elm, 'left'))||0,
			y : parseInt(jQuery.css(elm, 'top'))||0
		};
		
		elm.dragCfg.oP = jQuery.css(elm, 'position');
		if (elm.dragCfg.oP != 'relative' && elm.dragCfg.oP != 'absolute') {
			elm.style.position = 'relative';
		}
		
		K2.iDrag.getContainment(elm);
		K2.Slider.modifyContainer(elm);		
		
		dx = Math.round(position[0]) || 0;
		dy = Math.round(position[1]) || 0;
		
		nx = elm.dragCfg.oR.x + dx;
		ny = elm.dragCfg.oR.y + dy;
		if(elm.dragCfg.fractions) {
			newCoords = K2.iDrag.snapToGrid.apply(elm, [nx, ny, dx, dy]);
			if (newCoords.constructor == Object) {
				dx = newCoords.dx;
				dy = newCoords.dy;
			}
			nx = elm.dragCfg.oR.x + dx;
			ny = elm.dragCfg.oR.y + dy;
		}
		
		newCoords = K2.iDrag.fitToContainer.apply(elm, [nx, ny, dx, dy]);
		if (newCoords && newCoords.constructor == Object) {
			dx = newCoords.dx;
			dy = newCoords.dy;
		}
		
		nx = elm.dragCfg.oR.x + dx;
		ny = elm.dragCfg.oR.y + dy;
		
		if (elm.dragCfg.si && (elm.dragCfg.onSlide || elm.dragCfg.onChange)) {
			K2.Slider.onSlide(elm, nx, ny);
		}
		nx = !elm.dragCfg.axis || elm.dragCfg.axis == 'horizontally' ? nx : elm.dragCfg.oR.x||0;
		ny = !elm.dragCfg.axis || elm.dragCfg.axis == 'vertically' ? ny : elm.dragCfg.oR.y||0;
		elm.style.left = nx + 'px';
		elm.style.top = ny + 'px';
	},
	
	build : function(o) {
		return this.each(
			function()
			{
				if (this.isSlider == true || !o.accept || !K2.iUtil || !K2.iDrag || !K2.iDrop){
					return;
				}
				toDrag = jQuery(o.accept, this);
				if (toDrag.size() == 0) {
					return;
				}
				var params = {
					containment: 'parent',
					si : true,
					onSlide : o.onSlide && o.onSlide.constructor == Function ? o.onSlide : null,
					onChange : o.onChange && o.onChange.constructor == Function ? o.onChange : null,
					handle: this,
					opacity: o.opacity||false
				};
				if (o.fractions && parseInt(o.fractions)) {
					params.fractions = parseInt(o.fractions)||1;
					params.fractions = params.fractions > 0 ? params.fractions : 1;
				}
				if (toDrag.size() == 1)
					toDrag.Draggable(params);
				else {
					jQuery(toDrag.get(0)).Draggable(params);
					params.handle = null;
					toDrag.Draggable(params);
				}
				toDrag.keydown(K2.Slider.dragmoveByKey);
				toDrag.attr('tabindex',K2.Slider.tabindex++);	
				
				this.isSlider = true;
				this.slideCfg = {};
				this.slideCfg.onslide = params.onslide;
				this.slideCfg.fractions = params.fractions;
				this.slideCfg.sliders = toDrag;
				sliderEl = this;
				sliderEl.slideCfg.sliders.each(
					function(nr)
					{
						this.SliderIteration = nr;
						this.SliderContainer = sliderEl;
					}
				);
				if (o.values && o.values.constructor == Array) {
					for (i = o.values.length -1; i>=0;i--) {
						if (o.values[i].constructor == Array && o.values[i].length == 2) {
							el = this.slideCfg.sliders.get(i);
							if (el.tagName) {
								K2.Slider.dragmoveBy(el, o.values[i]);
							}
						}
					}
				}
			}
		);
	}
};

jQuery.fn.extend(
	{
		newPageSlider : K2.Slider.build,
		setPageSlider : K2.Slider.set,
		getPageSlider : K2.Slider.get,
		pageSliderNext : K2.Slider.next,
		pageSliderPrev : K2.Slider.prev
	}
);
K2.iUtil = {
	getPosition : function(e)
	{
		var x = 0;
		var y = 0;
		var es = e.style;
		var restoreStyles = false;
		if (jQuery(e).css('display') == 'none') {
			var oldVisibility = es.visibility;
			var oldPosition = es.position;
			restoreStyles = true;
			es.visibility = 'hidden';
			es.display = 'block';
			es.position = 'absolute';
		}
		var el = e;
		while (el){
			x += el.offsetLeft + (el.currentStyle && !jQuery.browser.opera ?parseInt(el.currentStyle.borderLeftWidth)||0:0);
			y += el.offsetTop + (el.currentStyle && !jQuery.browser.opera ?parseInt(el.currentStyle.borderTopWidth)||0:0);
			el = el.offsetParent;
		}
		el = e;
		while (el && el.tagName  && el.tagName.toLowerCase() != 'body')
		{
			x -= el.scrollLeft||0;
			y -= el.scrollTop||0;
			el = el.parentNode;
		}
		if (restoreStyles == true) {
			es.display = 'none';
			es.position = oldPosition;
			es.visibility = oldVisibility;
		}
		return {x:x, y:y};
	},
	getPositionLite : function(el)
	{
		var x = 0, y = 0;
		while(el) {
			x += el.offsetLeft || 0;
			y += el.offsetTop || 0;
			el = el.offsetParent;
		}
		return {x:x, y:y};
	},
	getSize : function(e)
	{
		var w = jQuery.css(e,'width');
		var h = jQuery.css(e,'height');
		var wb = 0;
		var hb = 0;
		var es = e.style;
		if (jQuery(e).css('display') != 'none') {
			wb = e.offsetWidth;
			hb = e.offsetHeight;
		} else {
			var oldVisibility = es.visibility;
			var oldPosition = es.position;
			es.visibility = 'hidden';
			es.display = 'block';
			es.position = 'absolute';
			wb = e.offsetWidth;
			hb = e.offsetHeight;
			es.display = 'none';
			es.position = oldPosition;
			es.visibility = oldVisibility;
		}
		return {w:w, h:h, wb:wb, hb:hb};
	},
	getSizeLite : function(el)
	{
		return {
			wb:el.offsetWidth||0,
			hb:el.offsetHeight||0
		};
	},
	getClient : function(e)
	{
		var h, w, de;
		if (e) {
			w = e.clientWidth;
			h = e.clientHeight;
		} else {
			de = document.documentElement;
			w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
			h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
		}
		return {w:w,h:h};
	},
	getScroll : function (e)
	{
		var t=0, l=0, w=0, h=0, iw=0, ih=0;
		if (e && e.nodeName.toLowerCase() != 'body') {
			t = e.scrollTop;
			l = e.scrollLeft;
			w = e.scrollWidth;
			h = e.scrollHeight;
			iw = 0;
			ih = 0;
		} else  {
			if (document.documentElement) {
				t = document.documentElement.scrollTop;
				l = document.documentElement.scrollLeft;
				w = document.documentElement.scrollWidth;
				h = document.documentElement.scrollHeight;
			} else if (document.body) {
				t = document.body.scrollTop;
				l = document.body.scrollLeft;
				w = document.body.scrollWidth;
				h = document.body.scrollHeight;
			}
			iw = self.innerWidth||document.documentElement.clientWidth||document.body.clientWidth||0;
			ih = self.innerHeight||document.documentElement.clientHeight||document.body.clientHeight||0;
		}
		return { t: t, l: l, w: w, h: h, iw: iw, ih: ih };
	},
	getMargins : function(e, toInteger)
	{
		var el = jQuery(e);
		var t = el.css('marginTop') || '';
		var r = el.css('marginRight') || '';
		var b = el.css('marginBottom') || '';
		var l = el.css('marginLeft') || '';
		if (toInteger)
			return {
				t: parseInt(t)||0,
				r: parseInt(r)||0,
				b: parseInt(b)||0,
				l: parseInt(l)
			};
		else
			return {t: t, r: r,	b: b, l: l};
	},
	getPadding : function(e, toInteger)
	{
		var el = jQuery(e);
		var t = el.css('paddingTop') || '';
		var r = el.css('paddingRight') || '';
		var b = el.css('paddingBottom') || '';
		var l = el.css('paddingLeft') || '';
		if (toInteger)
			return {
				t: parseInt(t)||0,
				r: parseInt(r)||0,
				b: parseInt(b)||0,
				l: parseInt(l)
			};
		else
			return {t: t, r: r,	b: b, l: l};
	},
	getBorder : function(e, toInteger)
	{
		var el = jQuery(e);
		var t = el.css('borderTopWidth') || '';
		var r = el.css('borderRightWidth') || '';
		var b = el.css('borderBottomWidth') || '';
		var l = el.css('borderLeftWidth') || '';
		if (toInteger)
			return {
				t: parseInt(t)||0,
				r: parseInt(r)||0,
				b: parseInt(b)||0,
				l: parseInt(l)||0
			};
		else
			return {t: t, r: r,	b: b, l: l};
	},
	getPointer : function(event)
	{
		var x = event.pageX || (event.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft)) || 0;
		var y = event.pageY || (event.clientY + (document.documentElement.scrollTop || document.body.scrollTop)) || 0;
		return {x:x, y:y};
	},
	traverseDOM : function(nodeEl, func)
	{
		func(nodeEl);
		nodeEl = nodeEl.firstChild;
		while(nodeEl){
			K2.iUtil.traverseDOM(nodeEl, func);
			nodeEl = nodeEl.nextSibling;
		}
	},
	purgeEvents : function(nodeEl)
	{
		K2.iUtil.traverseDOM(
			nodeEl,
			function(el)
			{
				for(var attr in el){
					if(typeof el[attr] === 'function') {
						el[attr] = null;
					}
				}
			}
		);
	},
	centerEl : function(el, axis)
	{
		var clientScroll = K2.iUtil.getScroll();
		var windowSize = K2.iUtil.getSize(el);
		if (!axis || axis == 'vertically')
			jQuery(el).css(
				{
					top: clientScroll.t + ((Math.max(clientScroll.h,clientScroll.ih) - clientScroll.t - windowSize.hb)/2) + 'px'
				}
			);
		if (!axis || axis == 'horizontally')
			jQuery(el).css(
				{
					left:	clientScroll.l + ((Math.max(clientScroll.w,clientScroll.iw) - clientScroll.l - windowSize.wb)/2) + 'px'
				}
			);
	},
	fixPNG : function (el, emptyGIF) {
		var images = jQuery('img[@src*="png"]', el||document), png;
		images.each( function() {
			png = this.src;				
			this.src = emptyGIF;
			this.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + png + "')";
		});
	}
};

// Helper function to support older browsers!
[].indexOf || (Array.prototype.indexOf = function(v, n){
	n = (n == null) ? 0 : n;
	var m = this.length;
	for (var i=n; i<m; i++)
		if (this[i] == v)
			return i;
	return -1;
});

K2.iDrag =	{
	helper : null,
	dragged: null,
	destroy : function()
	{
		return this.each(
			function ()
			{
				if (this.isDraggable) {
					this.dragCfg.dhe.unbind('mousedown', K2.iDrag.draginit);
					this.dragCfg = null;
					this.isDraggable = false;
					if(jQuery.browser.msie) {
						this.unselectable = "off";
					} else {
						this.style.MozUserSelect = '';
						this.style.KhtmlUserSelect = '';
						this.style.userSelect = '';
					}
				}
			}
		);
	},
	draginit : function (e)
	{
		if (K2.iDrag.dragged != null) {
			K2.iDrag.dragstop(e);
			return false;
		}
		var elm = this.dragElem;
		jQuery(document)
			.bind('mousemove', K2.iDrag.dragmove)
			.bind('mouseup', K2.iDrag.dragstop);
		elm.dragCfg.pointer = K2.iUtil.getPointer(e);
		elm.dragCfg.currentPointer = elm.dragCfg.pointer;
		elm.dragCfg.init = false;
		elm.dragCfg.fromHandler = this != this.dragElem;
		K2.iDrag.dragged = elm;
		if (elm.dragCfg.si && this != this.dragElem) {
				parentPos = K2.iUtil.getPosition(elm.parentNode);
				sliderSize = K2.iUtil.getSize(elm);
				sliderPos = {
					x : parseInt(jQuery.css(elm,'left')) || 0,
					y : parseInt(jQuery.css(elm,'top')) || 0
				};
				dx = elm.dragCfg.currentPointer.x - parentPos.x - sliderSize.wb/2 - sliderPos.x;
				dy = elm.dragCfg.currentPointer.y - parentPos.y - sliderSize.hb/2 - sliderPos.y;
				K2.Slider.dragmoveBy(elm, [dx, dy]);
		}
		return jQuery.selectKeyHelper||false;
	},

	dragstart : function(e)
	{
		var elm = K2.iDrag.dragged;
		elm.dragCfg.init = true;

		var dEs = elm.style;

		elm.dragCfg.oD = jQuery.css(elm,'display');
		elm.dragCfg.oP = jQuery.css(elm,'position');
		if (!elm.dragCfg.initialPosition)
			elm.dragCfg.initialPosition = elm.dragCfg.oP;

		elm.dragCfg.oR = {
			x : parseInt(jQuery.css(elm,'left')) || 0,
			y : parseInt(jQuery.css(elm,'top')) || 0
		};
		elm.dragCfg.diffX = 0;
		elm.dragCfg.diffY = 0;
		if (jQuery.browser.msie) {
			var oldBorder = K2.iUtil.getBorder(elm, true);
			elm.dragCfg.diffX = oldBorder.l||0;
			elm.dragCfg.diffY = oldBorder.t||0;
		}

		elm.dragCfg.oC = jQuery.extend(
			K2.iUtil.getPosition(elm),
			K2.iUtil.getSize(elm)
		);
		if (elm.dragCfg.oP != 'relative' && elm.dragCfg.oP != 'absolute') {
			dEs.position = 'relative';
		}

		K2.iDrag.helper.empty();
		var clonedEl = elm.cloneNode(true);
		
		jQuery(clonedEl).css(
			{
				display:	'block',
				left:		'0px',
				top: 		'0px'
			}
		);
		clonedEl.style.marginTop = '0';
		clonedEl.style.marginRight = '0';
		clonedEl.style.marginBottom = '0';
		clonedEl.style.marginLeft = '0';
		K2.iDrag.helper.append(clonedEl);
		
		var dhs = K2.iDrag.helper.get(0).style;

		if (elm.dragCfg.autoSize) {
			dhs.width = 'auto';
			dhs.height = 'auto';
		} else {
			dhs.height = elm.dragCfg.oC.hb + 'px';
			dhs.width = elm.dragCfg.oC.wb + 'px';
		}

		dhs.display = 'block';
		dhs.marginTop = '0px';
		dhs.marginRight = '0px';
		dhs.marginBottom = '0px';
		dhs.marginLeft = '0px';

		//remeasure the clone to check if the size was changed by user's functions
		jQuery.extend(
			elm.dragCfg.oC,
			K2.iUtil.getSize(clonedEl)
		);

		if (elm.dragCfg.cursorAt) {
			if (elm.dragCfg.cursorAt.left) {
				elm.dragCfg.oR.x += elm.dragCfg.pointer.x - elm.dragCfg.oC.x - elm.dragCfg.cursorAt.left;
				elm.dragCfg.oC.x = elm.dragCfg.pointer.x - elm.dragCfg.cursorAt.left;
			}
			if (elm.dragCfg.cursorAt.top) {
				elm.dragCfg.oR.y += elm.dragCfg.pointer.y - elm.dragCfg.oC.y - elm.dragCfg.cursorAt.top;
				elm.dragCfg.oC.y = elm.dragCfg.pointer.y - elm.dragCfg.cursorAt.top;
			}
			if (elm.dragCfg.cursorAt.right) {
				elm.dragCfg.oR.x += elm.dragCfg.pointer.x - elm.dragCfg.oC.x -elm.dragCfg.oC.hb + elm.dragCfg.cursorAt.right;
				elm.dragCfg.oC.x = elm.dragCfg.pointer.x - elm.dragCfg.oC.wb + elm.dragCfg.cursorAt.right;
			}
			if (elm.dragCfg.cursorAt.bottom) {
				elm.dragCfg.oR.y += elm.dragCfg.pointer.y - elm.dragCfg.oC.y - elm.dragCfg.oC.hb + elm.dragCfg.cursorAt.bottom;
				elm.dragCfg.oC.y = elm.dragCfg.pointer.y - elm.dragCfg.oC.hb + elm.dragCfg.cursorAt.bottom;
			}
		}
		elm.dragCfg.nx = elm.dragCfg.oR.x;
		elm.dragCfg.ny = elm.dragCfg.oR.y;

		if (elm.dragCfg.insideParent || elm.dragCfg.containment == 'parent') {
			parentBorders = K2.iUtil.getBorder(elm.parentNode, true);
			elm.dragCfg.oC.x = elm.offsetLeft + (jQuery.browser.msie ? 0 : jQuery.browser.opera ? -parentBorders.l : parentBorders.l);
			elm.dragCfg.oC.y = elm.offsetTop + (jQuery.browser.msie ? 0 : jQuery.browser.opera ? -parentBorders.t : parentBorders.t);
			jQuery(elm.parentNode).append(K2.iDrag.helper.get(0));
		}
		if (elm.dragCfg.containment) {
			K2.iDrag.getContainment(elm);
			elm.dragCfg.onDragModifier.containment = K2.iDrag.fitToContainer;
		}

		if (elm.dragCfg.si) {
			K2.Slider.modifyContainer(elm);
		}

		dhs.left = elm.dragCfg.oC.x - elm.dragCfg.diffX + 'px';
		dhs.top = elm.dragCfg.oC.y - elm.dragCfg.diffY + 'px';
		//resize the helper to fit the clone
		dhs.width = elm.dragCfg.oC.wb + 'px';
		dhs.height = elm.dragCfg.oC.hb + 'px';

		K2.iDrag.dragged.dragCfg.prot = false;

		if (elm.dragCfg.gx) {
			elm.dragCfg.onDragModifier.grid = K2.iDrag.snapToGrid;
		}
		if (elm.dragCfg.zIndex != false) {
			K2.iDrag.helper.css('zIndex', elm.dragCfg.zIndex);
		}
		if (elm.dragCfg.opacity) {
			K2.iDrag.helper.css('opacity', elm.dragCfg.opacity);
			if (window.ActiveXObject) {
				K2.iDrag.helper.css('filter', 'alpha(opacity=' + elm.dragCfg.opacity * 100 + ')');
			}
		}

		if(elm.dragCfg.frameClass) {
			K2.iDrag.helper.addClass(elm.dragCfg.frameClass);
			K2.iDrag.helper.get(0).firstChild.style.display = 'none';
		}
		if (elm.dragCfg.onStart)
			elm.dragCfg.onStart.apply(elm, [clonedEl, elm.dragCfg.oR.x, elm.dragCfg.oR.y]);
		if (K2.iDrop && K2.iDrop.count > 0 ){
			K2.iDrop.highlight(elm);
		}
		if (elm.dragCfg.ghosting == false) {
			dEs.display = 'none';
		}
		return false;
	},

	getContainment : function(elm)
	{
		if (elm.dragCfg.containment.constructor == String) {
			if (elm.dragCfg.containment == 'parent') {
				elm.dragCfg.cont = jQuery.extend(
					{x:0,y:0},
					K2.iUtil.getSize(elm.parentNode)
				);
				var contBorders = K2.iUtil.getBorder(elm.parentNode, true);
				elm.dragCfg.cont.w = elm.dragCfg.cont.wb - contBorders.l - contBorders.r;
				elm.dragCfg.cont.h = elm.dragCfg.cont.hb - contBorders.t - contBorders.b;
			} else if (elm.dragCfg.containment == 'document') {
				var clnt = K2.iUtil.getClient();
				elm.dragCfg.cont = {
					x : 0,
					y : 0,
					w : clnt.w,
					h : clnt.h
				};
			}
		} else if (elm.dragCfg.containment.constructor == Array) {
			elm.dragCfg.cont = {
				x : parseInt(elm.dragCfg.containment[0])||0,
				y : parseInt(elm.dragCfg.containment[1])||0,
				w : parseInt(elm.dragCfg.containment[2])||0,
				h : parseInt(elm.dragCfg.containment[3])||0
			};
		}
		elm.dragCfg.cont.dx = elm.dragCfg.cont.x - elm.dragCfg.oC.x;
		elm.dragCfg.cont.dy = elm.dragCfg.cont.y - elm.dragCfg.oC.y;
	},

	hidehelper : function(dragged)
	{
		if (dragged.dragCfg.insideParent || dragged.dragCfg.containment == 'parent') {
			jQuery('body', document).append(K2.iDrag.helper.get(0));
		}
		K2.iDrag.helper.empty().hide().css('opacity', 1);
		if (window.ActiveXObject) {
			K2.iDrag.helper.css('filter', 'alpha(opacity=100)');
		}
	},

	dragstop : function(e)
	{

		jQuery(document)
			.unbind('mousemove', K2.iDrag.dragmove)
			.unbind('mouseup', K2.iDrag.dragstop);

		if (K2.iDrag.dragged == null) {
			return;
		}
		var dragged = K2.iDrag.dragged;

		K2.iDrag.dragged = null;

		if (dragged.dragCfg.init == false) {
			return false;
		}
		if (dragged.dragCfg.so == true) {
			jQuery(dragged).css('position', dragged.dragCfg.oP);
		}
		var dEs = dragged.style;

		if (dragged.si) {
			K2.iDrag.helper.css('cursor', 'move');
		}
		if(dragged.dragCfg.frameClass) {
			K2.iDrag.helper.removeClass(dragged.dragCfg.frameClass);
		}

		if (dragged.dragCfg.revert == false) {
			if (dragged.dragCfg.fx > 0) {
				if (!dragged.dragCfg.axis || dragged.dragCfg.axis == 'horizontally') {
					var x = new jQuery.fx(dragged,{duration:dragged.dragCfg.fx}, 'left');
					x.custom(dragged.dragCfg.oR.x,dragged.dragCfg.nRx);
				}
				if (!dragged.dragCfg.axis || dragged.dragCfg.axis == 'vertically') {
					var y = new jQuery.fx(dragged,{duration:dragged.dragCfg.fx}, 'top');
					y.custom(dragged.dragCfg.oR.y,dragged.dragCfg.nRy);
				}
			} else {
				if (!dragged.dragCfg.axis || dragged.dragCfg.axis == 'horizontally')
					dragged.style.left = dragged.dragCfg.nRx + 'px';
				if (!dragged.dragCfg.axis || dragged.dragCfg.axis == 'vertically')
					dragged.style.top = dragged.dragCfg.nRy + 'px';
			}
			K2.iDrag.hidehelper(dragged);
			if (dragged.dragCfg.ghosting == false) {
				jQuery(dragged).css('display', dragged.dragCfg.oD);
			}
		} else if (dragged.dragCfg.fx > 0) {
			dragged.dragCfg.prot = true;
			var dh = false;
			if(K2.iDrop && jQuery.iSort && dragged.dragCfg.so) {
				dh = K2.iUtil.getPosition(jQuery.iSort.helper.get(0));
			}
			K2.iDrag.helper.animate(
				{
					left : dh ? dh.x : dragged.dragCfg.oC.x,
					top : dh ? dh.y : dragged.dragCfg.oC.y
				},
				dragged.dragCfg.fx,
				function()
				{
					dragged.dragCfg.prot = false;
					if (dragged.dragCfg.ghosting == false) {
						dragged.style.display = dragged.dragCfg.oD;
					}
					K2.iDrag.hidehelper(dragged);
				}
			);
		} else {
			K2.iDrag.hidehelper(dragged);
			if (dragged.dragCfg.ghosting == false) {
				jQuery(dragged).css('display', dragged.dragCfg.oD);
			}
		}

		if (K2.iDrop && K2.iDrop.count > 0 ){
			K2.iDrop.checkdrop(dragged);
		}
		if (jQuery.iSort && dragged.dragCfg.so) {
			jQuery.iSort.check(dragged);
		}
		if (dragged.dragCfg.onChange && (dragged.dragCfg.nRx != dragged.dragCfg.oR.x || dragged.dragCfg.nRy != dragged.dragCfg.oR.y)){
			dragged.dragCfg.onChange.apply(dragged, dragged.dragCfg.lastSi||[0,0,dragged.dragCfg.nRx,dragged.dragCfg.nRy]);
		}
		if (dragged.dragCfg.onStop)
			dragged.dragCfg.onStop.apply(dragged);
		return false;
	},

	snapToGrid : function(x, y, dx, dy)
	{
		if (dx != 0)
			dx = parseInt((dx + (this.dragCfg.gx * dx/Math.abs(dx))/2)/this.dragCfg.gx) * this.dragCfg.gx;
		if (dy != 0)
			dy = parseInt((dy + (this.dragCfg.gy * dy/Math.abs(dy))/2)/this.dragCfg.gy) * this.dragCfg.gy;
		return {
			dx : dx,
			dy : dy,
			x: 0,
			y: 0
		};
	},

	fitToContainer : function(x, y, dx, dy)
	{
		dx = Math.min(
				Math.max(dx,this.dragCfg.cont.dx),
				this.dragCfg.cont.w + this.dragCfg.cont.dx - this.dragCfg.oC.wb
			);
		dy = Math.min(
				Math.max(dy,this.dragCfg.cont.dy),
				this.dragCfg.cont.h + this.dragCfg.cont.dy - this.dragCfg.oC.hb
			);

		return {
			dx : dx,
			dy : dy,
			x: 0,
			y: 0
		}
	},

	dragmove : function(e)
	{
		if (K2.iDrag.dragged == null || K2.iDrag.dragged.dragCfg.prot == true) {
			return;
		}

		var dragged = K2.iDrag.dragged;

		dragged.dragCfg.currentPointer = K2.iUtil.getPointer(e);
		if (dragged.dragCfg.init == false) {
			distance = Math.sqrt(Math.pow(dragged.dragCfg.pointer.x - dragged.dragCfg.currentPointer.x, 2) + Math.pow(dragged.dragCfg.pointer.y - dragged.dragCfg.currentPointer.y, 2));
			if (distance < dragged.dragCfg.snapDistance){
				return;
			} else {
				K2.iDrag.dragstart(e);
			}
		}

		var dx = dragged.dragCfg.currentPointer.x - dragged.dragCfg.pointer.x;
		var dy = dragged.dragCfg.currentPointer.y - dragged.dragCfg.pointer.y;

		for (var i in dragged.dragCfg.onDragModifier) {
			var newCoords = dragged.dragCfg.onDragModifier[i].apply(dragged, [dragged.dragCfg.oR.x + dx, dragged.dragCfg.oR.y + dy, dx, dy]);
			if (newCoords && newCoords.constructor == Object) {
				dx = i != 'user' ? newCoords.dx : (newCoords.x - dragged.dragCfg.oR.x);
				dy = i != 'user' ? newCoords.dy : (newCoords.y - dragged.dragCfg.oR.y);
			}
		}

		dragged.dragCfg.nx = dragged.dragCfg.oC.x + dx - dragged.dragCfg.diffX;
		dragged.dragCfg.ny = dragged.dragCfg.oC.y + dy - dragged.dragCfg.diffY;

		if (dragged.dragCfg.si && (dragged.dragCfg.onSlide || dragged.dragCfg.onChange)) {
			K2.Slider.onSlide(dragged, dragged.dragCfg.nx, dragged.dragCfg.ny);
		}

		if(dragged.dragCfg.onDrag)
			dragged.dragCfg.onDrag.apply(dragged, [dragged.dragCfg.oR.x + dx, dragged.dragCfg.oR.y + dy]);
			
		if (!dragged.dragCfg.axis || dragged.dragCfg.axis == 'horizontally') {
			dragged.dragCfg.nRx = dragged.dragCfg.oR.x + dx;
			K2.iDrag.helper.get(0).style.left = dragged.dragCfg.nx + 'px';
		}
		if (!dragged.dragCfg.axis || dragged.dragCfg.axis == 'vertically') {
			dragged.dragCfg.nRy = dragged.dragCfg.oR.y + dy;
			K2.iDrag.helper.get(0).style.top = dragged.dragCfg.ny + 'px';
		}
		
		if (K2.iDrop && K2.iDrop.count > 0 ){
			K2.iDrop.checkhover(dragged);
		}
		return false;
	},

	build : function(o)
	{
		if (!K2.iDrag.helper) {
			jQuery('body',document).append('<div id="dragHelper"></div>');
			K2.iDrag.helper = jQuery('#dragHelper');
			var el = K2.iDrag.helper.get(0);
			var els = el.style;
			els.position = 'absolute';
			els.display = 'none';
			els.cursor = 'move';
			els.listStyle = 'none';
			els.overflow = 'hidden';
			if (window.ActiveXObject) {
				el.unselectable = "on";
			} else {
				els.mozUserSelect = 'none';
				els.userSelect = 'none';
				els.KhtmlUserSelect = 'none';
			}
		}
		if (!o) {
			o = {};
		}
		return this.each(
			function()
			{
				if (this.isDraggable || !K2.iUtil)
					return;
				if (window.ActiveXObject) {
					this.onselectstart = function(){return false;};
					this.ondragstart = function(){return false;};
				}
				var el = this;
				if(jQuery.browser.msie) {
					var dhe = jQuery(o.handle);
					dhe.each(
						function()
						{
							this.unselectable = "on";
						}
					);
				} else {
					var dhe = o.handle ? jQuery(this).find(o.handle) : jQuery(this);
					dhe.css('-moz-user-select', 'none');
					dhe.css('user-select', 'none');
					dhe.css('-khtml-user-select', 'none');
				}
				this.dragCfg = {
					dhe: dhe,
					revert : o.revert ? true : false,
					ghosting : o.ghosting ? true : false,
					so : o.so ? o.so : false,
					si : o.si ? o.si : false,
					insideParent : o.insideParent ? o.insideParent : false,
					zIndex : o.zIndex ? parseInt(o.zIndex)||0 : false,
					opacity : o.opacity ? parseFloat(o.opacity) : false,
					fx : parseInt(o.fx)||null,
					hpc : o.hpc ? o.hpc : false,
					onDragModifier : {},
					pointer : {},
					onStart : o.onStart && o.onStart.constructor == Function ? o.onStart : false,
					onStop : o.onStop && o.onStop.constructor == Function ? o.onStop : false,
					onChange : o.onChange && o.onChange.constructor == Function ? o.onChange : false,
					axis : /vertically|horizontally/.test(o.axis) ? o.axis : false,
					snapDistance : o.snapDistance ? parseInt(o.snapDistance)||0 : 0,
					cursorAt: o.cursorAt ? o.cursorAt : false,
					autoSize : o.autoSize ? true : false,
					frameClass : o.frameClass || false
					
				};
				if (o.onDragModifier && o.onDragModifier.constructor == Function)
					this.dragCfg.onDragModifier.user = o.onDragModifier;
				if (o.onDrag && o.onDrag.constructor == Function)
					this.dragCfg.onDrag = o.onDrag;
				if (o.containment && ((o.containment.constructor == String && (o.containment == 'parent' || o.containment == 'document')) || (o.containment.constructor == Array && o.containment.length == 4) )) {
					this.dragCfg.containment = o.containment;
				}
				if(o.fractions) {
					this.dragCfg.fractions = o.fractions;
				}
				if(o.grid){
					if(typeof o.grid == 'number'){
						this.dragCfg.gx = parseInt(o.grid)||1;
						this.dragCfg.gy = parseInt(o.grid)||1;
					} else if (o.grid.length == 2) {
						this.dragCfg.gx = parseInt(o.grid[0])||1;
						this.dragCfg.gy = parseInt(o.grid[1])||1;
					}
				}
				if (o.onSlide && o.onSlide.constructor == Function) {
					this.dragCfg.onSlide = o.onSlide;
				}

				this.isDraggable = true;
				dhe.each(
					function(){
						this.dragElem = el;
					}
				);
				dhe.bind('mousedown', K2.iDrag.draginit);
			}
		)
	}
};
jQuery.fn.extend(
	{
		DraggableDestroy : K2.iDrag.destroy,
		Draggable : K2.iDrag.build
	}
);

K2.iDrop = {
	fit : function (zonex, zoney, zonew, zoneh)
	{
		return 	zonex <= K2.iDrag.dragged.dragCfg.nx && 
				(zonex + zonew) >= (K2.iDrag.dragged.dragCfg.nx + K2.iDrag.dragged.dragCfg.oC.w) &&
				zoney <= K2.iDrag.dragged.dragCfg.ny && 
				(zoney + zoneh) >= (K2.iDrag.dragged.dragCfg.ny + K2.iDrag.dragged.dragCfg.oC.h) ? true :false;
	},
	intersect : function (zonex, zoney, zonew, zoneh)
	{
		return 	! ( zonex > (K2.iDrag.dragged.dragCfg.nx + K2.iDrag.dragged.dragCfg.oC.w)
				|| (zonex + zonew) < K2.iDrag.dragged.dragCfg.nx 
				|| zoney > (K2.iDrag.dragged.dragCfg.ny + K2.iDrag.dragged.dragCfg.oC.h) 
				|| (zoney + zoneh) < K2.iDrag.dragged.dragCfg.ny
				) ? true :false;
	},
	pointer : function (zonex, zoney, zonew, zoneh)
	{
		return	zonex < K2.iDrag.dragged.dragCfg.currentPointer.x
				&& (zonex + zonew) > K2.iDrag.dragged.dragCfg.currentPointer.x 
				&& zoney < K2.iDrag.dragged.dragCfg.currentPointer.y 
				&& (zoney + zoneh) > K2.iDrag.dragged.dragCfg.currentPointer.y
				? true :false;
	},
	overzone : false,
	highlighted : {},
	count : 0,
	zones : {},
	
	highlight : function (elm)
	{
		if (K2.iDrag.dragged == null) {
			return;
		}
		var i;
		K2.iDrop.highlighted = {};
		var oneIsSortable = false;
		for (i in K2.iDrop.zones) {
			if (K2.iDrop.zones[i] != null) {
				var iEL = K2.iDrop.zones[i].get(0);
				if (jQuery(K2.iDrag.dragged).is('.' + iEL.dropCfg.a)) {
					if (iEL.dropCfg.m == false) {
						iEL.dropCfg.p = jQuery.extend(
							K2.iUtil.getPositionLite(iEL),
							K2.iUtil.getSizeLite(iEL)
						);//K2.iUtil.getPos(iEL);
						iEL.dropCfg.m = true;
					}
					if (iEL.dropCfg.ac) {
						K2.iDrop.zones[i].addClass(iEL.dropCfg.ac);
					}
					K2.iDrop.highlighted[i] = K2.iDrop.zones[i];
					//if (jQuery.iSort && K2.iDrag.dragged.dragCfg.so) {
					if (jQuery.iSort && iEL.dropCfg.s && K2.iDrag.dragged.dragCfg.so) {
						iEL.dropCfg.el = jQuery('.' + iEL.dropCfg.a, iEL);
						elm.style.display = 'none';
						jQuery.iSort.measure(iEL);
						iEL.dropCfg.os = jQuery.iSort.serialize(jQuery.attr(iEL, 'id')).hash;
						elm.style.display = elm.dragCfg.oD;
						oneIsSortable = true;
					}
					if (iEL.dropCfg.onActivate) {
						iEL.dropCfg.onActivate.apply(K2.iDrop.zones[i].get(0), [K2.iDrag.dragged]);
					}
				}
			}
		}
		//if (jQuery.iSort && K2.iDrag.dragged.dragCfg.so) {
		if (oneIsSortable) {
			jQuery.iSort.start();
		}
	},
	remeasure : function()
	{
		K2.iDrop.highlighted = {};
		for (i in K2.iDrop.zones) {
			if (K2.iDrop.zones[i] != null) {
				var iEL = K2.iDrop.zones[i].get(0);
				if (jQuery(K2.iDrag.dragged).is('.' + iEL.dropCfg.a)) {
					iEL.dropCfg.p = jQuery.extend(
						K2.iUtil.getPositionLite(iEL),
						K2.iUtil.getSizeLite(iEL)
					);
					if (iEL.dropCfg.ac) {
						K2.iDrop.zones[i].addClass(iEL.dropCfg.ac);
					}
					K2.iDrop.highlighted[i] = K2.iDrop.zones[i];
					
					if (jQuery.iSort && iEL.dropCfg.s && K2.iDrag.dragged.dragCfg.so) {
						iEL.dropCfg.el = jQuery('.' + iEL.dropCfg.a, iEL);
						elm.style.display = 'none';
						jQuery.iSort.measure(iEL);
						elm.style.display = elm.dragCfg.oD;
					}
				}
			}
		}
	},
	
	checkhover : function (e)
	{
		if (K2.iDrag.dragged == null) {
			return;
		}
		K2.iDrop.overzone = false;
		var i;
		var applyOnHover = false;
		var hlt = 0;
		for (i in K2.iDrop.highlighted)
		{
			var iEL = K2.iDrop.highlighted[i].get(0);
			if ( 
					K2.iDrop.overzone == false
					 && 
					K2.iDrop[iEL.dropCfg.t](
					 	iEL.dropCfg.p.x, 
						iEL.dropCfg.p.y, 
						iEL.dropCfg.p.wb, 
						iEL.dropCfg.p.hb
					)
					 
			) {
				if (iEL.dropCfg.hc && iEL.dropCfg.h == false) {
					K2.iDrop.highlighted[i].addClass(iEL.dropCfg.hc);
				}
				//chec if onHover function has to be called
				if (iEL.dropCfg.h == false &&iEL.dropCfg.onHover) {
					applyOnHover = true;
				}
				iEL.dropCfg.h = true;
				K2.iDrop.overzone = iEL;
				//if(jQuery.iSort && K2.iDrag.dragged.dragCfg.so) {
				if(jQuery.iSort && iEL.dropCfg.s && K2.iDrag.dragged.dragCfg.so) {
					jQuery.iSort.helper.get(0).className = iEL.dropCfg.shc;
					jQuery.iSort.checkhover(iEL);
				}
				hlt ++;
			} else if(iEL.dropCfg.h == true) {
				//onOut function
				if (iEL.dropCfg.onOut) {
					iEL.dropCfg.onOut.apply(iEL, [e, K2.iDrag.helper.get(0).firstChild, iEL.dropCfg.fx]);
				}
				if (iEL.dropCfg.hc) {
					K2.iDrop.highlighted[i].removeClass(iEL.dropCfg.hc);
				}
				iEL.dropCfg.h = false;
			}
		}
		if (jQuery.iSort && !K2.iDrop.overzone && K2.iDrag.dragged.so) {
			jQuery.iSort.helper.get(0).style.display = 'none';
			//jQuery('body').append(jQuery.iSort.helper.get(0));
		}
		//call onhover
		if(applyOnHover) {
			K2.iDrop.overzone.dropCfg.onHover.apply(K2.iDrop.overzone, [e, K2.iDrag.helper.get(0).firstChild]);
		}
	},
	checkdrop : function (e)
	{
		var i;
		for (i in K2.iDrop.highlighted) {
			var iEL = K2.iDrop.highlighted[i].get(0);
			if (iEL.dropCfg.ac) {
				K2.iDrop.highlighted[i].removeClass(iEL.dropCfg.ac);
			}
			if (iEL.dropCfg.hc) {
				K2.iDrop.highlighted[i].removeClass(iEL.dropCfg.hc);
			}
			if(iEL.dropCfg.s) {
				jQuery.iSort.changed[jQuery.iSort.changed.length] = i;
			}
			if (iEL.dropCfg.onDrop && iEL.dropCfg.h == true) {
				iEL.dropCfg.h = false;
				iEL.dropCfg.onDrop.apply(iEL, [e, iEL.dropCfg.fx]);
			}
			iEL.dropCfg.m = false;
			iEL.dropCfg.h  = false;
		}
		K2.iDrop.highlighted = {};
	},
	destroy : function()
	{
		return this.each(
			function()
			{
				if (this.isDroppable) {
					if (this.dropCfg.s) {
						id = jQuery.attr(this,'id');
						jQuery.iSort.collected[id] = null;
						jQuery('.' + this.dropCfg.a, this).DraggableDestroy();
					}
					K2.iDrop.zones['d' + this.idsa] = null;
					this.isDroppable = false;
					this.f = null;
				}
			}
		);
	},
	build : function (o)
	{
		return this.each(
			function()
			{
				if (this.isDroppable == true || !o.accept || !K2.iUtil || !K2.iDrag){
					return;
				}
				this.dropCfg = {
					a : o.accept,
					ac: o.activeclass||false, 
					hc:	o.hoverclass||false,
					shc: o.helperclass||false,
					onDrop:	o.ondrop||o.onDrop||false,
					onHover: o.onHover||o.onhover||false,
					onOut: o.onOut||o.onout||false,
					onActivate: o.onActivate||false,
					t: o.tolerance && ( o.tolerance == 'fit' || o.tolerance == 'intersect') ? o.tolerance : 'pointer',
					fx: o.fx ? o.fx : false,
					m: false,
					h: false
				};
				if (o.sortable == true && jQuery.iSort) {
					id = jQuery.attr(this,'id');
					jQuery.iSort.collected[id] = this.dropCfg.a;
					this.dropCfg.s = true;
					if(o.onChange) {
						this.dropCfg.onChange = o.onChange;
						this.dropCfg.os = jQuery.iSort.serialize(id).hash;
					}
				}
				this.isDroppable = true;
				this.idsa = parseInt(Math.random() * 10000);
				K2.iDrop.zones['d' + this.idsa] = jQuery(this);
				K2.iDrop.count ++;
			}
		);
	}
};
jQuery.fn.extend(
	{
		DroppableDestroy : K2.iDrop.destroy,
		Droppable : K2.iDrop.build
	}
);
jQuery.recallDroppables = K2.iDrop.remeasure;