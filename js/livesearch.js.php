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

Livesearch = Class.create();

Livesearch.prototype = {
	initialize: function(father, attachitem, target, hideitem, url, pars, searchform, loaditem, searchtext, resetbutton) {
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
		this.t = null;  // Init timeout variable

		var buttonvalue = '<?php _e('go','k2_domain'); ?>';
		$(father).innerHTML = '<input type="text" id="s" name="s" class="livesearch" autocomplete="off" value="'+searchtext+'" /><span id="searchreset"></span><span id="searchload"></span><input type="submit" id="searchsubmit" value="'+buttonvalue+'" />';

		// Style the searchform for livesearch
		var inputs = $(searchform).getElementsByTagName('input');
		for (var i = 0; i < inputs.length; i++) {
			var input = inputs.item(i);
			if (input.type == 'submit')
				input.style.display = "none";
		}

		Effect.Fade(this.resetbutton, { duration: .1 });
		$(this.loaditem).style.display = 'none';

		Event.observe(attachitem, 'focus', function() { if ($(attachitem).value == searchtext) $(attachitem).setAttribute('value', '') });
		Event.observe(attachitem, 'blur', function() { if ($(attachitem).value == '') $(attachitem).setAttribute('value', searchtext) });

		// Bind the keys to the input
		Event.observe(attachitem, 'keyup', this.readyLivesearch.bindAsEventListener(this));
	},

	readyLivesearch: function(event) {
		var code = event.keyCode;
		if (code == Event.KEY_ESC or ((code == Event.KEY_DELETE or code == Event.KEY_BACKSPACE) and $F(this.attachitem) == '')) {
			this.resetLivesearch.bind(this);
		} else if (code != Event.KEY_LEFT and code != Event.KEY_RIGHT and code != Event.KEY_DOWN and code != Event.KEY_UP and code != Event.KEY_RETURN) {
			if (this.t) { clearTimeout(this.t) };
	        this.t = setTimeout(this.doLivesearch.bind(this), 400);
		}
	},

    doLivesearch: function() {
		Effect.Appear(this.loaditem, {duration: .1});

		new Ajax.Updater(
			this.target,
			this.url,
			{
				method: 'get',
				evalScripts: true,
				parameters: this.pars + encodeURIComponent($F(this.attachitem)) + '&rolling=1',
				onSuccess: this.searchComplete.bind(this)
		});
	},
	
	searchComplete: function() {
		$(this.hideitem).style.display = 'none';
		Effect.Fade(this.loaditem, {duration: .1});
		Effect.Appear(this.resetbutton, { duration: .1 });

		Event.observe(this.resetbutton, 'click', this.resetLivesearch.bindAsEventListener(this));
		$(this.resetbutton).style.cursor = 'pointer';
	},

	resetLivesearch: function() {
		$(this.hideitem).style.display = 'block';
		Effect.Fade(this.resetbutton, { duration: .1 });

		$(this.attachitem).value = '';
		$(this.target).innerHTML = '';
		$(this.resetbutton).style.cursor = 'default';
	}
}

Event.observe(window, "load", function() { new Livesearch('searchform', 's', 'dynamic_content', 'current_content', '<?php bloginfo('template_url'); ?>/rollingarchive.php', '&s=', 'searchform', 'searchload', '<?php _e('Type and Wait to Search','k2_domain'); ?>', 'searchreset'); } , false);