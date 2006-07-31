<?php
	require("../../../../wp-blog-header.php");

	// check to see if the user has enabled gzip compression in the WordPress admin panel
	if ( !get_settings('gzipcompression') and !ini_get('zlib.output_compression') and ini_get('output_handler') != 'ob_gzhandler' and ini_get('output_handler') != 'mb_output_handler' ) {
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
	initialize: function(targetitem, url, query, pagecount) {
		var rolling = this;

		this.targetitem = targetitem;
		this.url = url;
		this.query = query;
		this.pagecount = pagecount;
		this.pagenumber = 1;

		this.rollnext = $('rollnext');
		this.rollprev = $('rollprevious');

		var sliderValues = new Array(this.pagecount);
		for (var i = 0; i < this.pagecount; i++) {
			sliderValues[i] = i + 1;
		}
		this.PageSlider = new Control.Slider('pagehandle','pagetrack', {
			range: $R(rolling.pagecount, 1),
			values: sliderValues,
			sliderValue: 1,
			onSlide: function(v) { $('rollpages').innerHTML = 'Page '+v+' of '+rolling.pagecount; },
			onChange: function(v) { rolling.gotoPage(v); },
			handleImage: 'pagehandle'
		});

		Event.observe('rollprevious', 'click', function(){ rolling.rollPrevPage(); });
		Event.observe('rollnext', 'click', function(){ rolling.rollNextPage(); });

		this.checkRollingElements();
		this.rollRemoveLoad();

		this.initialized = true;
	},

	rollNextPage: function() {
		this.PageSlider.setValueBy(-1);
	},

	rollPrevPage: function() {
		this.PageSlider.setValueBy(1);
	},

	checkRollingElements: function() {
		if (this.pagenumber == 1) {
			$('rollprevious').className = null;
			$('rollnext').className = 'inactive';
			$('rollhome').className = 'inactive';
		} else if (this.pagenumber > 1) {
			$('rollnext').className = null;
			$('rollhome').className = null;
		}

		if (this.pagenumber >= this.pagecount) {
			$('rollprevious').className = 'inactive';
		} else {
			$('rollprevious').className = null;
		}

		$('rollpages').innerHTML = 'Page '+this.pagenumber+' of '+this.pagecount;
	},
	
	gotoPage: function(newpage) {
		var direction = 0;

		if (newpage != this.pagenumber) {
			if (newpage > this.pagecount) {
				direction = 'end';
			} else if (newpage < 1) {
				direction = 'home';
			} else {
				direction = 1;
			}
			this.pagenumber = (newpage - 1);
			this.rollArchive(direction);
		}
	},

	rollRemoveLoad: function() {
		new Effect.Fade('rollload', {duration: .1});
	},

	rollSuccess: function() {
		this.rollRemoveLoad();
	},

	rollError: function() {
		$('rollnotices').innerHTML = 'Error!';
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
	},

	rollArchive: function(direction) {
		if (direction == 1 || direction == -1) {
			this.pagenumber += direction;
			new Effect.Appear('rollload', {duration: .1});
		} else if (direction == 'home') {
			this.pagenumber = 1;
		}

		this.checkRollingElements();
		this.processQuery();

		new Ajax.Updater(
			this.targetitem,
			this.url,
			{
				method: 'get',
				parameters: this.query,
				onSuccess: this.rollSuccess.bind(this),
				onFailure: this.rollError.bind(this)
			}
		);
	}
};