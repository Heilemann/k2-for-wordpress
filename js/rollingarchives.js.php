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
	initialize: function(attachitem, targetitem, url, pagetext) {
		this.attachitem = attachitem;
		this.targetitem = targetitem;
		this.url = url;
		this.pagetext = pagetext;

		this.rollnext = 'rollnext';
		this.rollprev = 'rollprevious';
		this.rollpages = 'rollpages';
		this.rollload = 'rollload';
		this.rollhome = 'rollhome';
		this.rolldates = 'rolldates';
		this.rollnotices = 'rollnotices';
		this.rollhover = 'rollhover';

		this.pagehandle = 'pagehandle';
		this.pagetrack = 'pagetrack';

		this.trimmer = new TextTrimmer("texttrimmer", "dynamic-content", "entry-content", 1, 100);

		this.query = null;
		this.pagenumber = 0;
		this.pagecount = 0;
		this.pagedates = null;
		
		this.rollingState = null;

		this.nextPageListener = this.gotoNextPage.bindAsEventListener(this);
		this.prevPageListener = this.gotoPrevPage.bindAsEventListener(this);
		this.homePageListener = this.gotoHomePage.bindAsEventListener(this);

		this.initialized = false;
	},

	startEvents: function() {
		if (this.initialized) {
			this.stopEvents();
		}

		Event.observe(this.rollprev, 'click', this.prevPageListener);
		Event.observe(this.rollnext, 'click', this.nextPageListener);
		Event.observe(this.rollhome, 'click', this.homePageListener);
	},

	stopEvents: function() {
		Event.stopObserving(this.rollprev, 'click', this.prevPageListener);
		Event.stopObserving(this.rollnext, 'click', this.nextPageListener);
		Event.stopObserving(this.rollhome, 'click', this.homePageListener);
	},

	setupSlider: function() {
		var thisRolling = this;
		var sliderValues = new Array(this.pagecount);

		if (this.pageSlider instanceof Control.Slider) {
			this.pageSlider.dispose();
		}

		for (var i = 0; i < this.pagecount; i++) {
			sliderValues[i] = i + 1;
		}

		this.pageSlider = new Control.Slider(thisRolling.pagehandle, thisRolling.pagetrack, {
			range: $R(thisRolling.pagecount, 1),
			values: sliderValues,
			sliderValue: thisRolling.pagenumber,
			onSlide: function(v) { thisRolling.updatePageText(v); },
			onChange: function(v) { thisRolling.gotoPage(v); },
			handleImage: thisRolling.pagehandle
		});
	},

	updatePageText: function(v) {
		$(this.rollhover).show();
		
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
				onFailure: this.rollError.bind(this)
			}
		);
	},

	rollComplete: function() {
		new Effect.Fade(this.rollhover, {duration: 1});
		new Effect.Fade(this.rollload, {duration: .3});

		/* Spool Texttrimmer */
		this.trimmer.trimAgain(this.trimmer.curValue);
	},
	
	rollError: function() {
		$(this.rollnotices).style.display = 'block';
		$(this.rollnotices).innerHTML = 'Some kind of error has occurred! Danger, Will Robinson! Danger!';
	},

	processQuery: function() {
		this.query.merge({ paged: this.pagenumber, k2dynamic: 1 });
	},

	setRollingState: function(pagenumber, pagecount, query, pagedates) {
		if ( typeof(query) == 'string' ) {
			query = $H(query.toQueryParams());
		}

		this.query = query;
		this.pagenumber = pagenumber;
		this.pagecount = pagecount;
		this.pagedates = pagedates;

		if (this.validatePage(this.pagenumber)) {
			this.startEvents();
			$(this.rollload).hide();
			$(this.rollnotices).hide();
			$(this.rollhover).hide()
			this.setupSlider();

			$(this.attachitem).style.visibility = 'visible';
		} else {
			$(this.attachitem).style.visibility = 'hidden';
		}

		this.trimmer.setupTrimmer(100);

		this.initialized = true;
	},

	saveRollingState: function() {
		this.rollingState = new Hash({
			query: this.query,
			pagenumber: this.pagenumber,
			pagecount: this.pagecount,
			pagedates: this.pagedates
		});

		this.trimmer.saveState();
	},

	restoreRollingState: function() {
		if (this.rollingState instanceof Hash) {
			//console.log(this.rollingState.inspect());

			this.setRollingState(this.rollingState.pagenumber, this.rollingState.pagecount, this.rollingState.query, this.rollingState.pagedates);

			if (this.pagecount > 1) {
				this.pageSlider.setValue(this.rollingState.pagenumber);
			}

			this.rollingState = null;

			this.trimmer.restoreState();
		}
	},

	saveCookie: function() {
		setCookie('k2RollingQuery', this.query.toQueryString());
	}
}