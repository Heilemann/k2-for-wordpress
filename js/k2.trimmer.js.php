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
		k2Trimmer.attachitem = attachitem;
		k2Trimmer.targetitem = targetitem;
		k2Trimmer.chunkClass = chunkClass;
		k2Trimmer.minValue = minValue;
		k2Trimmer.maxValue = maxValue;

		k2Trimmer.curValue = maxValue;
		k2Trimmer.chunks = false;
		k2Trimmer.sliderhandle = 'trimmerhandle';
		k2Trimmer.slidertrack = 'trimmertrack';
		k2Trimmer.trimmore = 'trimmermore';
		k2Trimmer.trimless = 'trimmerless';

		k2Trimmer.prevState = null;

		k2Trimmer.trimMoreListener = k2Trimmer.trimMoreAction.bindAsEventListener(k2Trimmer);
		k2Trimmer.trimLessListener = k2Trimmer.trimLessAction.bindAsEventListener(k2Trimmer);

		k2Trimmer.init = false;
	},
	
	setupTrimmer: function(newvalue) {
		k2Trimmer.curValue = newvalue;

		if (k2Trimmer.curValue >= k2Trimmer.maxValue) {
			k2Trimmer.curValue = k2Trimmer.maxValue;
			$(k2Trimmer.targetitem).removeClassName("trimmed");
		} else if (k2Trimmer.curValue < k2Trimmer.minValue) {
			k2Trimmer.curValue = k2Trimmer.minValue;
		}

		k2Trimmer.chunks = false;

		var k2TrimmerTrimmer = k2Trimmer;
		if (k2Trimmer.trimSlider instanceof Control.Slider) {
			k2Trimmer.trimSlider.dispose();
		}

		k2Trimmer.trimSlider = new Control.Slider(k2TrimmerTrimmer.sliderhandle, k2TrimmerTrimmer.slidertrack, {
			range: $R(k2TrimmerTrimmer.minValue, k2TrimmerTrimmer.maxValue),
			sliderValue: k2TrimmerTrimmer.curValue,
			onSlide: function(value) { k2TrimmerTrimmer.doTrim(value); },
			onChange: function(value) { k2TrimmerTrimmer.doTrim(value); }
		});

		if (k2Trimmer.init) {
			Event.stopObserving(k2Trimmer.trimmore, 'click', k2Trimmer.trimMoreListener);
			Event.stopObserving(k2Trimmer.trimless, 'click', k2Trimmer.trimLessListener);
		}

		Event.observe(k2Trimmer.trimmore, 'click', k2Trimmer.trimMoreListener);
		Event.observe(k2Trimmer.trimless, 'click', k2Trimmer.trimLessListener);

		k2Trimmer.init = true;
   	},

	trimMoreAction: function() {
		k2Trimmer.trimSlider.setValue(k2Trimmer.curValue + 20);
	},

	trimLessAction: function() {
		k2Trimmer.trimSlider.setValue(k2Trimmer.curValue - 20);
	},

	trimAgain: function(value) {
		k2Trimmer.loadChunks();
		k2Trimmer.doTrim(value);
	},

    loadChunks: function() {
		var everything = $(k2Trimmer.targetitem).getElementsByClassName(k2Trimmer.chunkClass);

		k2Trimmer.chunks = [];

		for (i=0; i<everything.length; i++) {
			k2Trimmer.chunks.push({
				ref: everything[i],
				original: everything[i].innerHTML
			});
		}
	},

    doTrim: function(interval) {
		/* Spit out the trimmed text */
		if (!k2Trimmer.chunks)
			k2Trimmer.loadChunks();

		/* var interval = parseInt(interval); */
		k2Trimmer.curValue = interval;

		for (i=0; i<k2Trimmer.chunks.length; i++) {
			if (interval == k2Trimmer.maxValue) {
				k2Trimmer.chunks[i].ref.innerHTML = k2Trimmer.chunks[i].original;
			} else if (interval == k2Trimmer.minValue) {
				k2Trimmer.chunks[i].ref.innerHTML = '';
			} else {
				var a = k2Trimmer.chunks[i].original.stripTags();
				a = a.truncate(interval * 5, '');
				k2Trimmer.chunks[i].ref.innerHTML = '<p>' + a + '&nbsp;[...]</p>';
			}
		}

		/* Add 'trimmed' class to <BODY> while active */
		if (k2Trimmer.curValue != k2Trimmer.maxValue) {
			$(k2Trimmer.targetitem).addClassName("trimmed");
		} else {
			$(k2Trimmer.targetitem).removeClassName("trimmed");
		}
	},

	saveState: function() {
		k2Trimmer.prevState = new Hash({
			curValue: k2Trimmer.curValue
		});
	},

	restoreState: function() {
		if (k2Trimmer.prevState instanceof Hash) {
			k2Trimmer.setupTrimmer(k2Trimmer.prevState.curValue);
			k2Trimmer.prevState = null;
		}
	}
}