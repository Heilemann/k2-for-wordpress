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
    initialize: function(trimmerContainer, sliderID, chunkClass, minValue, maxValue, prefix) {
/*		if (prefix == '') console.log('Trimmer Init Detected');
		if (prefix != '') console.log('Livesearch Trimmer Init');
*/
		var thisTrimmer = this;
		this.trimmerContainer = prefix+trimmerContainer;
		this.sliderID = prefix+sliderID;
		this.chunkClass	= chunkClass;
		this.minValue = minValue;
		this.maxValue = maxValue;
		this.curValue = maxValue;
		this.prefix = prefix;
		this.chunks = false;

		/* Initialize slider */
		$(this.sliderID).innerHTML = '<div id="'+prefix+'trimmertrackwrap"><div id="'+prefix+'trimmertrack"><div id="'+prefix+'trimmerhandle"></div></div></div>';

		this.TrimSlider = new Control.Slider(prefix+"trimmerhandle", prefix+"trimmertrack", {
			range: $R(thisTrimmer.minValue, thisTrimmer.maxValue),
			sliderValue: thisTrimmer.maxValue,
			onSlide: function(value) { thisTrimmer.doTrim(value); },
			onChange: function(value) { thisTrimmer.doTrim(value); }
		});

		/* Add functionality to trimmer links */
		Event.observe($(prefix+'trimmerLess'), 'click', function() {
			thisTrimmer.TrimSlider.setValue(thisTrimmer.curValue - 10);
			return false;
		});
		Event.observe($(prefix+'trimmerMore'), 'click', function() {
			thisTrimmer.TrimSlider.setValue(thisTrimmer.curValue + 10);
			return false;
		});
		Event.observe($(prefix+'trimmerExcerpts'), 'click', function() {
			thisTrimmer.TrimSlider.setValue(40);
			$(prefix+'trimmerExcerpts').style.display = 'none';
			$(prefix+'trimmerHeadlines').style.display = 'block';
			return false;
		});
		Event.observe($(prefix+'trimmerHeadlines'), 'click', function() {
			thisTrimmer.TrimSlider.setValue(0);
			$(prefix+'trimmerHeadlines').style.display = 'none';
			$(prefix+'trimmerFulllength').style.display = 'block';
			return false;
		});
		Event.observe($(prefix+'trimmerFulllength'), 'click', function() {
			thisTrimmer.TrimSlider.setValue(100);
			$(prefix+'trimmerFulllength').style.display = 'none';
			$(prefix+'trimmerExcerpts').style.display = 'block';
			return false;
		});

		$(this.trimmerContainer).style.display = 'none';
   	},

	addClass: function() {
		if (this.prefix != '') {
			$('dynamic-content').addClassName("trimmed");
		} else {
			$('current-content').addClassName("trimmed");
		}
	},
	
	removeClass: function() {
		if (this.prefix != '') {
			$('dynamic-content').removeClassName("trimmed");
		} else {
			$('current-content').removeClassName("trimmed");
		}
	},

	trimAgain: function(value) {
		this.loadChunks();
		this.doTrim(value);
	},

    loadChunks: function() {
		if (this.prefix != '') {
			/* Dynamic chunks */
			var everything = $('dynamic-content').getElementsByClassName(this.chunkClass);
		} else {
			/* Normal chunks */
			var everything = $('current-content').getElementsByClassName(this.chunkClass);
		}

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
			this.addClass();
		} else {
			this.removeClass();
		}
	}
}