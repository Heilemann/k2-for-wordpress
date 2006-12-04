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

//Based on Drew McLellan's code: http://24ways.org/2006/tasty-text-trimmer

TextTrimmer = Class.create();

TextTrimmer.prototype = {
    initialize: function(sliderID, chunkClass, minValue, maxValue) {
		var trimming = this;
		this.sliderID = sliderID;
		this.chunkClass	= chunkClass;
		this.minValue = minValue;
		this.maxValue = maxValue;
		this.curValue = maxValue;
		this.chunks = false;

		$(sliderID).innerHTML = '<div id="trimmertrack"><div id="trimmertrackend"><div id="trimmerhandle"></div></div></div>';

		this.TrimSlider = new Control.Slider("trimmerhandle", "trimmertrack", {
			range: $R(trimming.minValue, trimming.maxValue),
			sliderValue: trimming.curValue,
			alignX: -10,
			onSlide: function(v) { trimming.curValue = v; trimming.doTrim(v); }
		});

		$(sliderID).style.display = 'none';
   	},

    loadChunks: function() {
		var everything = document.getElementsByClassName(this.chunkClass);

		var i, l;
		this.chunks = [];

		for (i=0, l=everything.length; i<l; i++) {
			this.chunks.push({
				ref: everything[i],
				original: everything[i].innerHTML
			});
		}
	},

    doTrim: function(interval) {
		if (!this.chunks) this.loadChunks();
		
		var i, l;
		
		for (i=0, l=this.chunks.length; i<l; i++){

			if (interval == this.maxValue){
				this.chunks[i].ref.innerHTML = this.chunks[i].original;
			} else if (interval == this.minValue) {
				this.chunks[i].ref.innerHTML	= '';
			} else {
				var a = this.chunks[i].original.split(' ');
				a = a.slice(0, interval);
				this.chunks[i].ref.innerHTML = a.join(' ') + '&hellip;';
			}
		}
	}
}