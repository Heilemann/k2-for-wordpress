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

RollingArchives = Class.create();

RollingArchives.prototype = {
	initialize: function(targetitem, url, query, pagecount, prefix, pagetext) {
		var rolling = this;

		this.targetitem = targetitem;
		this.url = url;
		this.query = query;
		this.pagecount = pagecount;
		this.pagenumber = 1;
		this.pagetext = pagetext;

		this.rollnext = prefix+'rollnext';
		this.rollprev = prefix+'rollprevious';
		this.rollpages = prefix+'rollpages';
		this.rollload = prefix+'rollload';
		this.rollhome = prefix+'rollhome';
		this.rollnotices = prefix+'rollnotices';

		this.pagehandle = prefix+'pagehandle';
		this.pagetrack = prefix+'pagetrack';
		this.pagetrackend = prefix+'pagetrackend';

		this.rollRemoveLoad();

		var sliderValues = new Array(this.pagecount);
		for (var i = 0; i < this.pagecount; i++) {
			sliderValues[i] = i + 1;
		}
		this.PageSlider = new Control.Slider(rolling.pagehandle,rolling.pagetrack, {
			range: $R(rolling.pagecount, 1),
			values: sliderValues,
			sliderValue: 1,
			onSlide: function(v) { rolling.updatePageText(v); },
			onChange: function(v) { rolling.gotoPage(v); },
			handleImage: rolling.pagehandle
		});

		Event.observe(this.rollprev, 'click', function(){ rolling.gotoPrevPage(); });
		Event.observe(this.rollnext, 'click', function(){ rolling.gotoNextPage(); });
		$(this.rollprev).onclick = function() { return false; };
		$(this.rollnext).onclick = function() { return false; };

		$(this.rollnext).className = 'inactive';
		$(this.rollhome).className = 'inactive';
		$(this.rollnotices).style.display = 'none';
		this.updatePageText(this.pagenumber);

		this.initialized = true;
	},

	updatePageText: function(v) {
		$(this.rollpages).innerHTML = (this.pagetext.replace('%1$d', v)).replace('%2$d', this.pagecount);
	},

	gotoNextPage: function() {
		this.PageSlider.setValueBy(-1);
	},

	gotoPrevPage: function() {
		this.PageSlider.setValueBy(1);
	},

	gotoPage: function(newpage) {
		if (newpage != this.pagenumber) {
			new Effect.Appear(this.rollload, {duration: .1});

			if (newpage >= this.pagecount) {
				$(this.rollprev).className = 'inactive';
				$(this.rollnext).className = null;
				$(this.rollhome).className = null;

				this.pagenumber = this.pagecount;
			} else if (newpage <= 1) {
				$(this.rollprev).className = null;
				$(this.rollnext).className = 'inactive';
				$(this.rollhome).className = 'inactive';

				this.pagenumber = 1;
			} else {
				$(this.rollprev).className = null;
				$(this.rollnext).className = null;
				$(this.rollhome).className = null;

				this.pagenumber = newpage;
			}

			this.updatePageText(this.pagenumber);
			this.processQuery();

			new Ajax.Updater(
				this.targetitem,
				this.url,
				{
					method: 'get',
					parameters: this.query,
					onComplete: this.rollComplete.bind(this),
					onFailure: this.rollError.bind(this)
				}
			);

		}
	},

	rollComplete: function() {
		// Spool Texttrimmer
		if (this.pagenumber == 1) {
			$('texttrimmer').style.display = 'none';
		} else {
			$('texttrimmer').style.display = 'block';

			MyTrimmer.chunks = false;
			MyTrimmer.doTrim(MyTrimmer.curValue);
		}

		this.rollRemoveLoad();

		// Support for Lightbox
		if (window.initLightbox) {
			initLightbox();
		}
	},
	
	rollRemoveLoad: function() {
		new Effect.Fade(this.rollload, {duration: .1});
	},

	rollError: function() {
		$(this.rollnotices).style.display = 'block';
		$(this.rollnotices).innerHTML = 'An error has occurred. Danger, Will Robinson! Danger!';
	},

	processQuery: function() {
		if (this.query.indexOf('&paged=') != -1) {
			this.query = this.query.replace(/&paged=\d+/,'&paged='+this.pagenumber);
		} else {
			this.query += "&paged=" + this.pagenumber;
		}

		if (this.query.indexOf('&rolling=') == -1) {
			this.query += '&rolling=1';
		}
	}
};