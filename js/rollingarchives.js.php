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

var RollingArchives = {};
RollingArchives = Class.create();

RollingArchives.prototype = {
	initialize: function(prefix, attachitem, targetitem, pagetext, pagecount, url, query, pagedates) {
		var thisRolling = this;

		this.attachitem = prefix+attachitem;
		this.targetitem = prefix+targetitem;
		this.url = url;
		this.pagetext = pagetext;
		this.query = query;
		this.pagenumber = 1;
		this.pagecount = pagecount;
		this.pagedates = pagedates;

		this.rollnext = prefix+'rollnext';
		this.rollprev = prefix+'rollprevious';
		this.rollpages = prefix+'rollpages';
		this.rollload = prefix+'rollload';
		this.rollhome = prefix+'rollhome';
		this.rolldates = prefix+'rolldates';
		this.rollnotices = prefix+'rollnotices';

		this.pagehandle = prefix+'pagehandle';
		this.pagetrack = prefix+'pagetrack';

		this.trimmer = new TextTrimmer(prefix, "texttrimmer", "entry-content", 1, 100);

		var sliderValues = new Array(this.pagecount);
		for (var i = 0; i < this.pagecount; i++) sliderValues[i] = i + 1;
		
		this.pageSlider = new Control.Slider(thisRolling.pagehandle, thisRolling.pagetrack, {
			range: $R(thisRolling.pagecount, 1),
			values: sliderValues,
			sliderValue: 1,
			onSlide: function(v) { thisRolling.updatePageText(v); },
			onChange: function(v) { thisRolling.gotoPage(v); },
			handleImage: thisRolling.pagehandle
		});

		this.nextPageListener = this.gotoNextPage.bindAsEventListener(this);
		this.prevPageListener = this.gotoPrevPage.bindAsEventListener(this);
		this.homePageListener = this.gotoHomePage.bindAsEventListener(this);
		Event.observe(this.rollprev, 'click', this.prevPageListener);
		Event.observe(this.rollnext, 'click', this.nextPageListener);
		Event.observe(this.rollhome, 'click', this.homePageListener);

		$(this.rollnotices).style.display = 'none';
		$(this.rollload).style.display = 'none';

		this.validatePage(this.pagenumber);
		this.initialized = true;
	},

	updatePageText: function(v) {
		$(this.rollpages).innerHTML = (this.pagetext.replace('%1$d', v)).replace('%2$d', this.pagecount);
		$(this.rolldates).innerHTML = this.pagedates[v - 1];
	},

	validatePage: function(newpage) {
		if (this.pagecount > 1) {
			if (newpage >= this.pagecount) {
				$(this.attachitem).className = 'lastpage';
				this.pagenumber = this.pagecount;

			} else if (newpage == 1) {
				$(this.attachitem).className = 'firstpage';
				this.pagenumber = 1;

			} else {
				$(this.attachitem).className = 'nthpage';
				this.pagenumber = newpage;
			}

			this.updatePageText(this.pagenumber);
			return true;
		}

		$(this.attachitem).className = 'emptypage';
		return false;
	},

	gotoNextPage: function() {
		this.pageSlider.setValueBy(-1);
	},

	gotoPrevPage: function() {
		this.pageSlider.setValueBy(1);
	},

	gotoHomePage: function() {
		this.pageSlider.setValue(1);
	},

	gotoPage: function(newpage) {
		if (newpage != this.pagenumber) {
			new Effect.Appear(this.rollload, {duration: .3});

			this.validatePage(newpage);
			this.processQuery();

			new Ajax.Updater(
				{
					success: this.targetitem,
					failure: this.rollnotices
				},
				this.url,
				{
					method: 'get',
					evalScripts: true,
					parameters: this.query,
					onComplete: this.rollComplete.bind(this),
					onFailure: function() {
						this.rollComplete.bind(this);
						this.rollError.bind(this);
					}
				}
			);
		}
	},

	rollComplete: function() {
		this.rollRemoveLoad();

		/* Spool Texttrimmer */
		if (this.pagenumber == 1) {
			this.trimmer.removeClass();
		} else {
			this.trimmer.trimAgain(this.trimmer.curValue);
		}

		/* Support for Lightbox */
		if (window.initLightbox)
			initLightbox();
	},
	
	rollRemoveLoad: function() {
		new Effect.Fade(this.rollload, {duration: .3});
	},

	rollError: function() {
		$(this.rollnotices).style.display = 'block';
		$(this.rollnotices).innerHTML = 'Some kind of error has occurred! Danger, Will Robinson! Danger!';
	},

	processQuery: function() {
		this.query.merge({ paged: this.pagenumber, k2dynamic: 1 });
	}
}