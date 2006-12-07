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
    initialize: function(sliderID, chunkClass, minValue, maxValue) {
		var trimming = this;
		this.sliderID = sliderID;
		this.chunkClass	= chunkClass;
		this.minValue = minValue;
		this.maxValue = maxValue;
		this.curValue = maxValue;
		this.chunks = false;

		/* Init Slider */
		$(sliderID).innerHTML = '<div id="trimmertrack"><div id="trimmertrackend"><div id="trimmerhandle"></div></div></div>';

		this.TrimSlider = new Control.Slider("trimmerhandle", "trimmertrack", {
			range: $R(trimming.minValue, trimming.maxValue),
			onSlide: function(v) { trimming.curValue = v; trimming.doTrim(v); },
			onChange: function(v) { trimming.curValue = v; trimming.doTrim(v); }
		});

		/* Add functionality to trimmer links */
		Event.observe($('trimmerLess'), 'click', function() { MyTrimmer.doTrim(curValue - 10) });
		Event.observe($('trimmerMore'), 'click', function() { MyTrimmer.doTrim(curValue + 10) });
		Event.observe($('trimmerExcerpts'), 'click', function() { MyTrimmer.doTrim(40); $('trimmerExcerpts').style.display = 'none'; $('trimmerHeadlines').style.display = 'block'; });
		Event.observe($('trimmerHeadlines'), 'click', function() { MyTrimmer.doTrim(0); $('trimmerHeadlines').style.display = 'none'; $('trimmerFulllength').style.display = 'block'; });
		Event.observe($('trimmerFulllength'), 'click', function() { MyTrimmer.doTrim(100); $('trimmerFulllength').style.display = 'none'; $('trimmerExcerpts').style.display = 'block'; });

		/* Hide trimmer until it is summoned by the almighty RA */
		$('texttrimmer').style.display = 'none';
   	},

    loadChunks: function() {
		/* Slice n' dice the text for trimming */
		var everything = document.getElementsByClassName(this.chunkClass);

		this.chunks = [];

		for (i=0; i<everything.length; i++) {
			this.chunks.push({
				ref: everything[i],
				original: everything[i].innerHTML
			});
		}
	},

    doTrim: function(interval) {
		if (!this.chunks) this.loadChunks();

		/* Spit out the trimmed text */
		for (i=0; i<this.chunks.length; i++){
			if (interval == this.maxValue){
				this.chunks[i].ref.innerHTML = this.chunks[i].original;
			} else if (interval == this.minValue) {
				this.chunks[i].ref.innerHTML = '';
			} else {
				var a = this.chunks[i].original.stripTags();
				a = a.truncate(interval * 4, '&nbsp;[...]');
				this.chunks[i].ref.innerHTML = '<p>' + a + '</p>';;
			}
		}

		/* Make sure slider is sync'd */
		if (this.TrimSlider.value != interval) {
			this.TrimSlider.setValue(interval);
			this.curValue = interval;
		}

		/* Add 'trimmed' class to <BODY> while active */
		if (this.curValue != this.maxValue) {
			document.body.addClassName("trimmed");
		} else {
			document.body.removeClassName("trimmed");
		}
	},
}

