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
// script.aculo.us slider.js v1.7.0, Fri Jan 19 19:16:36 CET 2007

// Copyright (c) 2005, 2006 Marty Haught, Thomas Fuchs 
//
// script.aculo.us is freely distributable under the terms of an MIT-style license.
// For details, see the script.aculo.us web site: http://script.aculo.us/

function K2Slider(handle, track, options) {
	var thisObj = this;

	this.handle  = jQuery(handle);
    this.track   = jQuery(track);
    this.options = options || {};

    this.value     = this.options.value || 0;

    this.maximum   = this.options.maximum || 1;
    this.minimum   = this.options.minimum || 0;

    this.trackLength  = this.track.width();
    this.handleLength = this.handle.width();
	this.handle.css('position', 'absolute');

    this.active   = false;
    this.dragging = false;

    this.setValue(this.value);
   
    this.handle.mousedown(function(event) {
		thisObj.active = true;

        var pointer	= thisObj.pointerX(event);
		var offset	= thisObj.track.offset();

		thisObj.setValue(
			thisObj.translateToValue(
				pointer-offset.left-(thisObj.handleLength/2)
          	)
		);

		var offset = thisObj.handle.offset();
		thisObj.offsetX = (pointer - offset.left);
	});

	jQuery(document).mouseup(function(event){
		if (thisObj.active && thisObj.dragging) {
			thisObj.active = false;
			thisObj.dragging = false;

			thisObj.updateFinished(thisObj);
		}
		thisObj.active = false;
		thisObj.dragging = false;
	});

	jQuery(document).mousemove(function(event){
		if (thisObj.active) {
			if (!thisObj.dragging) thisObj.dragging = true;

			thisObj.draw(event);

			// fix AppleWebKit rendering
			if (navigator.appVersion.indexOf('AppleWebKit')>0) window.scrollBy(0,0);
		}
	});

	this.initialized = true;
};

K2Slider.prototype.getNearestValue = function(value) {
	if (value > this.maximum) return this.maximum;
	if (value < this.minimum) return this.minimum;
	return value;
};

K2Slider.prototype.setValue = function(value) {
	this.value = this.getNearestValue(value);

	this.handle.css('left', this.translateToPx(this.value));
   
	if (!this.dragging || !this.event) this.updateFinished(this);
};

K2Slider.prototype.setValueBy = function(delta) {
	this.setValue(this.value + delta);
};

K2Slider.prototype.translateToPx = function(value) {
	return Math.round(
		((this.trackLength-this.handleLength)/(this.maximum-this.minimum)) * 
		(value - this.minimum)) + "px";
};

K2Slider.prototype.translateToValue = function(offset) {
	return Math.round(
		((offset/(this.trackLength-this.handleLength) * 
		(this.maximum-this.minimum)) + this.minimum));
};

K2Slider.prototype.draw = function(event) {
	var pointer = this.pointerX(event);
	var offset	= this.track.offset();
	pointer		-= this.offsetX + offset.left;

    this.event = event;
	this.setValue( this.translateToValue(pointer) );

	if (this.initialized && this.options.onSlide)
		this.options.onSlide(this.value);
};

K2Slider.prototype.updateFinished = function(thisObj) {
	if (thisObj.initialized && thisObj.options.onChange) 
		thisObj.options.onChange(thisObj.value);

	thisObj.event = null;
};

K2Slider.prototype.pointerX = function(event) {
	return event.pageX || (event.clientX +
		(document.documentElement.scrollLeft || document.body.scrollLeft));
};

K2Slider.prototype.isLeftClick = function(event) {
	return (((event.which) && (event.which == 1)) ||
		((event.button) && (event.button == 1)));
};
