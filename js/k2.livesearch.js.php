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

k2Search = {
	active: false,
	timer: false,
	prevSearch: false,

	build: function(url, searchprompt) {
		k2Search.url = url;
		k2Search.searchPrompt = searchprompt;

		// Define our elements
		k2Search.targetContent = jQuery('#dynamic-content');
		k2Search.hideContent = jQuery('#current-content');
		k2Search.searchSubmit = jQuery('form#searchform input[@type=submit]');

		// Insert reset and loading elements
		k2Search.searchSubmit.before('<span id="searchreset"></span><span id="searchload"></span>');

		// Define our elements
		k2Search.searchLoad = jQuery('form#searchform span#searchload');
		k2Search.searchReset = jQuery('form#searchform span#searchreset');
		k2Search.searchInput = jQuery('form#searchform input#s');

		k2Search.searchInput.addClass('livesearch').val(k2Search.searchPrompt);

		k2Search.searchSubmit.hide();
		k2Search.searchLoad.hide();
		k2Search.searchReset.fadeTo('fast', 0.3);

		// Bind events to the search input
		k2Search.searchInput
			.focus(function() {
				if ( jQuery(this).val() == k2Search.searchPrompt )
					jQuery(this).val('');
			})
			.blur(function() {
				if ( jQuery(this).val() == '' )
					jQuery(this).val(k2Search.searchPrompt);
			})
			.keyup(function(event) {
				var code = event.keyCode;

				if ( code == 27 || ( (code == 46 || code == 8) && jQuery(this).val() == '' ) ) {
					k2Search.resetSearch();
				} else if (code != 13) {
					if (k2Search.timer) {
						clearTimeout(k2Search.timer);
					}
					k2Search.timer = setTimeout(k2Search.doSearch, 500);
				}
			});
	},

	doSearch: function() {
		if (k2Search.searchInput.val() == k2Search.prevSearch) return;

		k2Search.searchReset.fadeOut('fast');
		k2Search.searchLoad.fadeIn('fast');

		if ( ! k2Search.active ) {
			k2Search.active = true;

			if ( jQuery('div#rollingarchives').length ) {
				jQuery('div#rollingarchives').saveRollingState();
			}
		}

		k2Search.prevSearch = k2Search.searchInput.val();
		jQuery.get(k2Search.url, k2Search.searchInput.serialize() + '&k2dynamic=init',
			function(data) {
				k2Search.hideContent.hide();
				k2Search.targetContent.show().html(data);

				k2Search.searchLoad.fadeOut('fast');

				k2Search.searchReset.click(k2Search.resetSearch).fadeTo('fast', 1.0).css('cursor', 'pointer');
			}
		);
	},

	resetSearch: function()
	{
		k2Search.active = false;
		k2Search.prevSearch = '';

		k2Search.searchInput.val(k2Search.searchPrompt);

		k2Search.searchReset.unbind('click').fadeTo('fast', 0.3).css('cursor', 'default');

		if ( k2Search.hideContent.length ) {
			k2Search.targetContent.hide().html('');
			k2Search.hideContent.show();
		} else {
			jQuery('div#rollingarchives').restoreRollingState();
		}
	}
};

jQuery.fn.extend({
	newLiveSearch: k2Search.build
});
