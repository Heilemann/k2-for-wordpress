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

/*	Thank you Drew McLellan for starting us off
	with http://24ways.org/2006/tasty-text-trimmer	*/

TextTrimmer = Class.create();

TextTrimmer.prototype = {
    initialize: function(attachitem, targetitem, chunkClass, minValue, maxValue) {
		this.attachitem = attachitem;
		this.targetitem = targetitem;
		this.chunkClass	= chunkClass;
		this.minValue = minValue;
		this.maxValue = maxValue;

		this.curValue = maxValue;
		this.chunks = false;
		this.sliderhandle = 'trimmerhandle';
		this.slidertrack = 'trimmertrack';
		this.trimmore = 'trimmermore';
		this.trimless = 'trimmerless';

		this.prevState = null;

		this.trimMoreListener = this.trimMoreAction.bindAsEventListener(this);
		this.trimLessListener = this.trimLessAction.bindAsEventListener(this);

		this.init = false;
	},
	
	setupTrimmer: function(newvalue) {
		this.curValue = newvalue;

		if (this.curValue >= this.maxValue) {
			this.curValue = this.maxValue;
			$(this.targetitem).removeClassName("trimmed");
		} else if (this.curValue < this.minValue) {
			this.curValue = this.minValue;
		}

		this.chunks = false;

		var thisTrimmer = this;
		if (this.trimSlider instanceof Control.Slider) {
			this.trimSlider.dispose();
		}

		this.trimSlider = new Control.Slider(thisTrimmer.sliderhandle, thisTrimmer.slidertrack, {
			range: $R(thisTrimmer.minValue, thisTrimmer.maxValue),
			sliderValue: thisTrimmer.curValue,
			onSlide: function(value) { thisTrimmer.doTrim(value); },
			onChange: function(value) { thisTrimmer.doTrim(value); }
		});

		if (this.init) {
			Event.stopObserving(this.trimmore, 'click', this.trimMoreListener);
			Event.stopObserving(this.trimless, 'click', this.trimLessListener);
		}

		Event.observe(this.trimmore, 'click', this.trimMoreListener);
		Event.observe(this.trimless, 'click', this.trimLessListener);

		this.init = true;
   	},

	trimMoreAction: function() {
		this.trimSlider.setValue(this.curValue + 20);
	},

	trimLessAction: function() {
		this.trimSlider.setValue(this.curValue - 20);
	},

	trimAgain: function(value) {
		this.loadChunks();
		this.doTrim(value);
	},

    loadChunks: function() {
		var everything = $(this.targetitem).getElementsByClassName(this.chunkClass);

		this.chunks = [];

		for (i=0; i<everything.length; i++) {
			this.chunks.push({
				ref: everything[i],
				original: everything[i].innerHTML
			});
		}
	},

    doTrim: function(interval) {
		/* Spit out the trimmed text */
		if (!this.chunks)
			this.loadChunks();

		/* var interval = parseInt(interval); */
		this.curValue = interval;

		for (i=0; i<this.chunks.length; i++) {
			if (interval == this.maxValue) {
				this.chunks[i].ref.innerHTML = this.chunks[i].original;
			} else if (interval == this.minValue) {
				this.chunks[i].ref.innerHTML = '';
			} else {
				var a = this.chunks[i].original.stripTags();
				a = a.truncate(interval * 5, '');
				this.chunks[i].ref.innerHTML = '<p>' + a + '&nbsp;[...]</p>';
			}
		}

		/* Add 'trimmed' class to <BODY> while active */
		if (this.curValue != this.maxValue) {
			$(this.targetitem).addClassName("trimmed");
		} else {
			$(this.targetitem).removeClassName("trimmed");
		}
	},

	saveState: function() {
		this.prevState = new Hash({
			curValue: this.curValue
		});
	},

	restoreState: function() {
		if (this.prevState instanceof Hash) {
			this.setupTrimmer(this.prevState.curValue);
			this.prevState = null;
		}
	}
}