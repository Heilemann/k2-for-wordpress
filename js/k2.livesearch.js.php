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

var k2Search = {
	setup: function(url, searchprompt) {
		k2Search.url = url;
		k2Search.searchPrompt = searchprompt;

		// Insert reset and loading elements
		jQuery('#searchform input[@type=submit]').before('<span id="searchreset"></span><span id="searchload"></span>');

		jQuery('input#s').addClass('livesearch').val(k2Search.searchPrompt);

		jQuery('#searchform input[@type=submit]').hide();
		jQuery('#searchload').hide();
		jQuery('#searchreset').show().fadeTo('fast', 0.3);

		// Bind events to the search input
		jQuery('input#s')
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
		if (jQuery('input#s').val() == k2Search.prevSearch) return;

		jQuery('#searchreset').fadeTo('fast', 0.3);
		jQuery('#searchload').fadeIn('fast');

		if ( ! k2Search.active ) {
			k2Search.active = true;

			if ( jQuery('div#rollingarchives').length ) {
				k2Rolling.saveState();
			}
		}

		k2Search.prevSearch = jQuery('input#s').val();
		jQuery.get(k2Search.url, jQuery('input#s').serialize() + '&k2dynamic=init',
			function(data) {
				jQuery('#current-content').hide();
				jQuery('#dynamic-content').show().html(data);

				jQuery('#searchload').fadeOut('fast');

				jQuery('#searchreset').click(k2Search.resetSearch).fadeTo('fast', 1.0).css('cursor', 'pointer');
			}
		);
	},

	resetSearch: function()
	{
		k2Search.active = false;
		k2Search.prevSearch = '';

		jQuery('input#s').val(k2Search.searchPrompt);

		jQuery('#searchreset').unbind('click').fadeTo('fast', 0.3).css('cursor', 'default');

		if ( jQuery('#current-content').length ) {
			jQuery('#dynamic-content').hide().html('');
			jQuery('#current-content').show();
		} else {
			k2Rolling.restoreState();
		}
	}
};
