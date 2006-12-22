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
	initialize: function(searchform, attachitem, targetitem, hideitem, url, pars, loaditem, searchtext, resetbutton, submitbutton, buttonvalue) {
		var thisSearch = this;

		this.searchform = searchform;
		this.attachitem = attachitem;
		this.targetitem = targetitem;
		this.hideitem = hideitem;
		this.url = url;
		this.pars = pars;
		this.loaditem = loaditem;
		this.searchtext = searchtext;
		this.resetbutton = resetbutton;
		this.submitbutton = submitbutton;
		this.buttonvalue = buttonvalue;
		this.searchstring = '';
		this.t = null;  // Init timeout variable

		$(this.searchform).innerHTML = '<input type="text" id="'+this.attachitem+'" name="'+this.attachitem+'" class="livesearch" autocomplete="off" value="'+this.searchtext+'" /><span id="'+this.resetbutton+'"></span><span id="'+this.loaditem+'"></span><input type="submit" id="'+this.submitbutton+'" value="'+this.buttonvalue+'" />';

		$(this.submitbutton).style.display = "none";
		$(this.loaditem).style.display = "none";
		new Effect.Fade(this.resetbutton, { duration: 0, to: 0.3 });

		Event.observe(thisSearch.attachitem, 'focus', function() {
			if ($F(thisSearch.attachitem) == thisSearch.searchtext)
				$(thisSearch.attachitem).setAttribute('value', '');
		});

		Event.observe(thisSearch.attachitem, 'blur', function() {
			if ($F(thisSearch.attachitem) == '')
				$(thisSearch.attachitem).setAttribute('value', thisSearch.searchtext);
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

		new Effect.Fade(this.resetbutton, { duration: 0.1 });
		new Effect.Appear(this.loaditem, { duration: 0.1 });

		new Ajax.Updater(
			this.targetitem,
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
		new Effect.Fade(this.loaditem, { duration: 0.1 });
		new Effect.Appear(this.resetbutton, { duration: 0.1 });
		
		Event.observe(this.resetbutton, 'click', this.resetLivesearch.bindAsEventListener(this));
		$(this.resetbutton).style.cursor = 'pointer';

		// Support for Lightbox
		if (window.initLightbox) {
			initLightbox();
		}
	},

	resetLivesearch: function() {
		$(this.targetitem).innerHTML = '';
		$(this.hideitem).style.display = 'block';

		$(this.attachitem).value = this.searchtext;
		new Effect.Fade(this.resetbutton, { duration: 0.1, to: 0.3 });
		$(this.resetbutton).style.cursor = 'default';
	}
}
