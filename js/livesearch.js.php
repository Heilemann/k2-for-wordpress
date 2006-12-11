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

Livesearch = Class.create();

Livesearch.prototype = {
	initialize: function(father, attachitem, target, hideitem, url, pars, searchform, loaditem, searchtext, resetbutton, buttonvalue) {
		var search = this;

		this.father = father;
		this.attachitem = attachitem;
		this.target = target;
		this.hideitem = hideitem;
		this.url = url;
		this.pars = pars;
		this.searchform = searchform;
		this.loaditem = loaditem;
		this.searchtext = searchtext;
		this.resetbutton = resetbutton;
		this.buttonvalue = buttonvalue;
		this.searchstring = '';
		this.t = null;  // Init timeout variable

		$(father).innerHTML = '<input type="text" id="s" name="s" class="livesearch" autocomplete="off" value="'+this.searchtext+'" /><span id="searchreset"></span><span id="searchload"></span><input type="submit" id="searchsubmit" value="'+this.buttonvalue+'" />';

		// Style the searchform for livesearch
		var inputs = $(searchform).getElementsByTagName('input');
		for (var i = 0; i < inputs.length; i++) {
			var input = inputs.item(i);
			if (input.type == 'submit') 
				input.style.display = "none";
		}

		Effect.Fade(this.resetbutton, { duration: 0, to: 0.3 });
		$(this.loaditem).style.display = 'none';

		Event.observe(search.attachitem, 'focus', function() {
			if ($(search.attachitem).value == search.searchtext)
				$(search.attachitem).setAttribute('value', '');
		});

		Event.observe(search.attachitem, 'blur', function() {
			if ($(search.attachitem).value == '')
				$(search.attachitem).setAttribute('value', search.searchtext);
		});

		// Bind the keys to the input
		Event.observe(this.attachitem, 'keyup', this.readyLivesearch.bindAsEventListener(this));
	},

	readyLivesearch: function(event) {
		var code = event.keyCode;
		if (code == Event.KEY_ESC || ((code == Event.KEY_DELETE || code == Event.KEY_BACKSPACE) && $F(this.attachitem) == '')) {
			this.resetLivesearch.bind(this);
		} else if (code != Event.KEY_RETURN) {
			if (this.t) clearTimeout(this.t);
	        this.t = setTimeout(this.doLivesearch.bind(this), 400);
		}
	},

    doLivesearch: function() {
		if ($F(this.attachitem) == this.searchstring) return;

		Effect.Fade(this.resetbutton, { duration: .1});
		Effect.Appear(this.loaditem, {duration: .1});

		new Ajax.Updater(
			this.target,
			this.url,
			{
				method: 'get',
				evalScripts: true,
				parameters: this.pars + encodeURIComponent($F(this.attachitem)) + '&rolling=1',
				onComplete: this.searchComplete.bind(this)
		});

		this.searchstring = $F(this.attachitem);
	},
	
	searchComplete: function() {
		$(this.hideitem).style.display = 'none';
		Effect.Fade(this.loaditem, {duration: .1});
		Effect.Appear(this.resetbutton, { duration: .1 });
		
		/* Spool Texttrimmer */
		if (MyTrimmer.chunks != false)
			MyTrimmer.loadChunks(this.target);
		/*TAKE INTO ACCOUNT NESTEDNESS*/
		Effect.Appear(MyTrimmer.trimmerContainer, { duration: .3 });
		
		Event.observe(this.resetbutton, 'click', this.resetLivesearch.bindAsEventListener(this));
		$(this.resetbutton).style.cursor = 'pointer';

		// Support for Lightbox
		if (window.initLightbox) {
			initLightbox();
		}
	},

	resetLivesearch: function() {
		$(this.target).innerHTML = '';
		$(this.hideitem).style.display = 'block';

		$(this.attachitem).value = this.searchtext;
		Effect.Fade(this.resetbutton, { duration: .1, to: 0.3 });
		$(this.resetbutton).style.cursor = 'default';
	}
}
