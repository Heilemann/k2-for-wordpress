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
	initialize: function(searchform, targetitem, hideitem, url, searchprompt, buttonvalue) {
		var thisSearch = this;

		this.searchform = searchform;
		this.targetitem = targetitem;
		this.hideitem = hideitem;
		this.url = url;
		this.searchprompt = searchprompt;
		this.buttonvalue = buttonvalue;

		this.searchfield = 's';
		this.loaditem = 'searchload';
		this.resetbutton = 'searchreset';
		this.submitbutton = 'searchsubmit';
		this.lastsearch = '';
		this.t = null;  // Init timeout variable

		Event.observe(window, "load", this.onLoading.bindAsEventListener(this));
	},

	onLoading: function() {
		if ( $(this.searchform) ) {
			$(this.searchform).update('<input type="text" id="'+this.searchfield+'" name="'+this.searchfield+'" class="livesearch" autocomplete="off" value="'+this.searchprompt+'" /><span id="'+this.resetbutton+'"></span><span id="'+this.loaditem+'"></span><input type="submit" id="'+this.submitbutton+'" value="'+this.buttonvalue+'" />');

			$(this.submitbutton).style.display = "none";
			$(this.loaditem).style.display = "none";
			new Effect.Fade(this.resetbutton, { duration: 0, to: 0.3 });

			this.focusListener = this.onInputFocus.bindAsEventListener(this);
			this.blurListener = this.onInputBlur.bindAsEventListener(this);
			this.keyupListener = this.readyLivesearch.bindAsEventListener(this);
			Event.observe(this.searchfield, 'focus', this.focusListener);
			Event.observe(this.searchfield, 'blur', this.blurListener);
			Event.observe(this.searchfield, 'keyup', this.keyupListener);
		}
	},

	onInputFocus: function() {
		if ($F(this.searchfield) == this.searchprompt) {
			$(this.searchfield).value = '';
		}
	},

	onInputBlur: function() {
		if ($F(this.searchfield) == '') {
			$(this.searchfield).value = this.searchprompt;
		}
	},

	readyLivesearch: function(event) {
		var code = event.keyCode;
		if (code == Event.KEY_ESC || ((code == Event.KEY_DELETE || code == Event.KEY_BACKSPACE) && $F(this.searchfield) == '')) {
			this.resetLivesearch.bind(this);
		} else if (code != Event.KEY_RETURN) {
			if (this.t) clearTimeout(this.t);
	        this.t = setTimeout(this.doLivesearch.bind(this), 400);
		}
	},

    doLivesearch: function() {
		if ($F(this.searchfield) == this.lastsearch) return;

		new Effect.Fade(this.resetbutton, { duration: 0.1 });
		new Effect.Appear(this.loaditem, { duration: 0.1 });

		var query = $(this.searchfield).serialize();
		new Ajax.Updater(
			this.targetitem,
			this.url,
			{
				method: 'get',
				evalScripts: true,
				parameters: query + '&k2dynamic=1',
				onComplete: this.searchComplete.bind(this)
		});

		this.lastsearch = $F(this.searchfield);
	},
	
	searchComplete: function() {
		$(this.hideitem).style.display = 'none';
		new Effect.Fade(this.loaditem, { duration: 0.1 });
		new Effect.Appear(this.resetbutton, { duration: 0.1 });
		
		Event.observe(this.resetbutton, 'click', this.resetLivesearch.bindAsEventListener(this));
		$(this.resetbutton).style.cursor = 'pointer';
	},

	resetLivesearch: function() {
		$(this.targetitem).update();
		$(this.hideitem).style.display = 'block';

		$(this.searchfield).value = this.searchprompt;
		new Effect.Fade(this.resetbutton, { duration: 0.1, to: 0.3 });
		$(this.resetbutton).style.cursor = 'default';
	}
}
