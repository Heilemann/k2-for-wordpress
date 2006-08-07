<?php
	require("../../../../wp-blog-header.php");
	// check to see if the user has enabled gzip compression in the WordPress admin panel
	if ( !get_settings('gzipcompression') and !ini_get('zlib.output_compression') and ini_get('output_handler') != 'ob_gzhandler' and ini_get('output_handler') != 'mb_output_handler' ) {
		ob_start('ob_gzhandler');
	}

	header("Cache-Control: public");
	header("Pragma: cache");

	$offset = 60*60*24*60;
	$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT";
	$LmStr = "Last-Modified: ".gmdate("D, d M Y H:i:s",filemtime(__FILE__))." GMT";

	header($ExpStr);
	header($LmStr);
	header('Content-Type: text/javascript; charset: UTF-8');
?>
// Copyright (c) 2005 Marty Haught, Thomas Fuchs 
// See http://script.aculo.us for more info

if(!Control) var Control = {};
Control.Slider = Class.create();

// options:
//  axis: 'vertical', or 'horizontal' (default)
//
// callbacks:
//  onChange(value)
//  onSlide(value)
Control.Slider.prototype = {
  initialize: function(handle, track, options) {
    var slider = this;
    
    if(handle instanceof Array) {
      this.handles = handle.collect( function(e) { return $(e) });
    } else {
      this.handles = [$(handle)];
    }
    
    this.track   = $(track);
    this.options = options or {};

    this.axis      = this.options.axis or 'horizontal';
    this.increment = this.options.increment or 1;
    this.step      = parseInt(this.options.step or '1');
    this.range     = this.options.range or $R(0,1);
    
    this.value     = 0; // assure backwards compat
    this.values    = this.handles.map( function() { return 0 });
    this.spans     = this.options.spans ? this.options.spans.map(function(s){ return $(s) }) : false;
    this.options.startSpan = $(this.options.startSpan or null);
    this.options.endSpan   = $(this.options.endSpan or null);

    this.restricted = this.options.restricted or false;

    this.maximum   = this.options.maximum or this.range.end;
    this.minimum   = this.options.minimum or this.range.start;

    // Will be used to align the handle onto the track, if necessary
    this.alignX = parseInt(this.options.alignX or '0');
    this.alignY = parseInt(this.options.alignY or '0');
    
    this.trackLength = this.maximumOffset() - this.minimumOffset();
    this.handleLength = this.isVertical() ? this.handles[0].offsetHeight : this.handles[0].offsetWidth;

    this.active   = false;
    this.dragging = false;
    this.disabled = false;

    if(this.options.disabled) this.setDisabled();

    // Allowed values array
    this.allowedValues = this.options.values ? this.options.values.sortBy(Prototype.K) : false;
    if(this.allowedValues) {
      this.minimum = this.allowedValues.min();
      this.maximum = this.allowedValues.max();
    }

    this.eventMouseDown = this.startDrag.bindAsEventListener(this);
    this.eventMouseUp   = this.endDrag.bindAsEventListener(this);
    this.eventMouseMove = this.update.bindAsEventListener(this);

    // Initialize handles in reverse (make sure first handle is active)
    this.handles.each( function(h,i) {
      i = slider.handles.length-1-i;
      slider.setValue(parseFloat(
        (slider.options.sliderValue instanceof Array ? 
          slider.options.sliderValue[i] : slider.options.sliderValue) or 
         slider.range.start), i);
      Element.makePositioned(h); // fix IE
      Event.observe(h, "mousedown", slider.eventMouseDown);
    });
    
    Event.observe(this.track, "mousedown", this.eventMouseDown);
    Event.observe(document, "mouseup", this.eventMouseUp);
    Event.observe(document, "mousemove", this.eventMouseMove);
    
    this.initialized = true;
  },
  dispose: function() {
    var slider = this;    
    Event.stopObserving(this.track, "mousedown", this.eventMouseDown);
    Event.stopObserving(document, "mouseup", this.eventMouseUp);
    Event.stopObserving(document, "mousemove", this.eventMouseMove);
    this.handles.each( function(h) {
      Event.stopObserving(h, "mousedown", slider.eventMouseDown);
    });
  },
  setDisabled: function(){
    this.disabled = true;
  },
  setEnabled: function(){
    this.disabled = false;
  },  
  getNearestValue: function(value){
    if(this.allowedValues){
      if(value >= this.allowedValues.max()) return(this.allowedValues.max());
      if(value <= this.allowedValues.min()) return(this.allowedValues.min());
      
      var offset = Math.abs(this.allowedValues[0] - value);
      var newValue = this.allowedValues[0];
      this.allowedValues.each( function(v) {
        var currentOffset = Math.abs(v - value);
        if(currentOffset <= offset){
          newValue = v;
          offset = currentOffset;
        } 
      });
      return newValue;
    }
    if(value > this.range.end) return this.range.end;
    if(value < this.range.start) return this.range.start;
    return value;
  },
  setValue: function(sliderValue, handleIdx){
    if(!this.active) {
      this.activeHandleIdx = handleIdx or 0;
      this.activeHandle    = this.handles[this.activeHandleIdx];
      this.updateStyles();
    }
    handleIdx = handleIdx or this.activeHandleIdx or 0;
    if(this.initialized and this.restricted) {
      if((handleIdx>0) and (sliderValue<this.values[handleIdx-1]))
        sliderValue = this.values[handleIdx-1];
      if((handleIdx < (this.handles.length-1)) and (sliderValue>this.values[handleIdx+1]))
        sliderValue = this.values[handleIdx+1];
    }
    sliderValue = this.getNearestValue(sliderValue);
    this.values[handleIdx] = sliderValue;
    this.value = this.values[0]; // assure backwards compat
    
    this.handles[handleIdx].style[this.isVertical() ? 'top' : 'left'] = 
      this.translateToPx(sliderValue);
    
    this.drawSpans();
    if(!this.dragging or !this.event) this.updateFinished();
  },
  setValueBy: function(delta, handleIdx) {
    this.setValue(this.values[handleIdx or this.activeHandleIdx or 0] + delta, 
      handleIdx or this.activeHandleIdx or 0);
  },
  translateToPx: function(value) {
    return Math.round(
      ((this.trackLength-this.handleLength)/(this.range.end-this.range.start)) * 
      (value - this.range.start)) + "px";
  },
  translateToValue: function(offset) {
    return ((offset/(this.trackLength-this.handleLength) * 
      (this.range.end-this.range.start)) + this.range.start);
  },
  getRange: function(range) {
    var v = this.values.sortBy(Prototype.K); 
    range = range or 0;
    return $R(v[range],v[range+1]);
  },
  minimumOffset: function(){
    return(this.isVertical() ? this.alignY : this.alignX);
  },
  maximumOffset: function(){
    return(this.isVertical() ?
      this.track.offsetHeight - this.alignY : this.track.offsetWidth - this.alignX);
  },  
  isVertical:  function(){
    return (this.axis == 'vertical');
  },
  drawSpans: function() {
    var slider = this;
    if(this.spans)
      $R(0, this.spans.length-1).each(function(r) { slider.setSpan(slider.spans[r], slider.getRange(r)) });
    if(this.options.startSpan)
      this.setSpan(this.options.startSpan,
        $R(0, this.values.length>1 ? this.getRange(0).min() : this.value ));
    if(this.options.endSpan)
      this.setSpan(this.options.endSpan, 
        $R(this.values.length>1 ? this.getRange(this.spans.length-1).max() : this.value, this.maximum));
  },
  setSpan: function(span, range) {
    if(this.isVertical()) {
      span.style.top = this.translateToPx(range.start);
      span.style.height = this.translateToPx(range.end - range.start + this.range.start);
    } else {
      span.style.left = this.translateToPx(range.start);
      span.style.width = this.translateToPx(range.end - range.start + this.range.start);
    }
  },
  updateStyles: function() {
    this.handles.each( function(h){ Element.removeClassName(h, 'selected') });
    Element.addClassName(this.activeHandle, 'selected');
  },
  startDrag: function(event) {
    if(Event.isLeftClick(event)) {
      if(!this.disabled){
        this.active = true;
        
        var handle = Event.element(event);
        var pointer  = [Event.pointerX(event), Event.pointerY(event)];
        var track = handle;
        if(track==this.track) {
          var offsets  = Position.cumulativeOffset(this.track); 
          this.event = event;
          this.setValue(this.translateToValue( 
           (this.isVertical() ? pointer[1]-offsets[1] : pointer[0]-offsets[0])-(this.handleLength/2)
          ));
          var offsets  = Position.cumulativeOffset(this.activeHandle);
          this.offsetX = (pointer[0] - offsets[0]);
          this.offsetY = (pointer[1] - offsets[1]);
        } else {
          // find the handle (prevents issues with Safari)
          while((this.handles.indexOf(handle) == -1) and handle.parentNode) 
            handle = handle.parentNode;
        
          this.activeHandle    = handle;
          this.activeHandleIdx = this.handles.indexOf(this.activeHandle);
          this.updateStyles();
        
          var offsets  = Position.cumulativeOffset(this.activeHandle);
          this.offsetX = (pointer[0] - offsets[0]);
          this.offsetY = (pointer[1] - offsets[1]);
        }
      }
      Event.stop(event);
    }
  },
  update: function(event) {
   if(this.active) {
      if(!this.dragging) this.dragging = true;
      this.draw(event);
      // fix AppleWebKit rendering
      if(navigator.appVersion.indexOf('AppleWebKit')>0) window.scrollBy(0,0);
      Event.stop(event);
   }
  },
  draw: function(event) {
    var pointer = [Event.pointerX(event), Event.pointerY(event)];
    var offsets = Position.cumulativeOffset(this.track);
    pointer[0] -= this.offsetX + offsets[0];
    pointer[1] -= this.offsetY + offsets[1];
    this.event = event;
    this.setValue(this.translateToValue( this.isVertical() ? pointer[1] : pointer[0] ));
    if(this.initialized and this.options.onSlide)
      this.options.onSlide(this.values.length>1 ? this.values : this.value, this);
  },
  endDrag: function(event) {
    if(this.active and this.dragging) {
      this.finishDrag(event, true);
      Event.stop(event);
    }
    this.active = false;
    this.dragging = false;
  },  
  finishDrag: function(event, success) {
    this.active = false;
    this.dragging = false;
    this.updateFinished();
  },
  updateFinished: function() {
    if(this.initialized and this.options.onChange) 
      this.options.onChange(this.values.length>1 ? this.values : this.value, this);
    this.event = null;
  }
}
