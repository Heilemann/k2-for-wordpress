<?php require('header.php'); ?>

var k2Search = {
	setup: function(url, searchprompt) {
		k2Search.url = url;
		k2Search.searchPrompt = searchprompt;
		k2Search.input = jQuery('input#s');

		var submitButton = jQuery('#searchform input[@type=submit]');

		// Insert reset and loading elements
		submitButton.before('<span id="searchreset"></span><span id="searchload"></span>');
		k2Search.reset = jQuery('#searchreset');
		k2Search.loading = jQuery('#searchload');

		k2Search.input.addClass('livesearch').val(k2Search.searchPrompt);

		submitButton.hide();
		k2Search.loading.hide();
		k2Search.reset.show().fadeTo('fast', 0.3);

		// Bind events to the search input
		k2Search.input.focus(function() {
				if(k2Search.input.val() == k2Search.searchPrompt) {
					k2Search.input.val('');
				}
			})
			.blur(function() {
				if(k2Search.input.val() == '') {
					k2Search.input.val(k2Search.searchPrompt);
				}
			})
			.keyup(function(event) {
				var code = event.keyCode;

				if(code == 27 || ((code == 46 || code == 8) && k2Search.input.val() == '')) {
					k2Search.resetSearch();
				} else if(code != 13) {
					if(k2Search.timer) {
						clearTimeout(k2Search.timer);
					}
					k2Search.timer = setTimeout(k2Search.doSearch, 500);
				}
			});
	},

	doSearch: function() {
		if(k2Search.input.val() == k2Search.prevSearch) return;

		k2Search.reset.fadeTo('fast', 0.3);
		k2Search.loading.fadeIn('fast');

		if(!k2Search.active) {
			k2Search.active = true;

			if(jQuery('div#rollingarchives').length) {
				k2Rolling.saveState();
			}
		}

		k2Search.prevSearch = k2Search.input.val();
		jQuery.get(k2Search.url, k2Search.input.serialize() + '&k2dynamic=init',
			function(data) {
				jQuery('#current-content').hide();
				jQuery('#dynamic-content').show().html(data);

				k2Search.loading.fadeOut('fast');

				k2Search.reset.click(k2Search.resetSearch).fadeTo('fast', 1.0).css('cursor', 'pointer');

				// Lightbox v2.03.3 - Adds new images to lightbox
				if(myLightbox && myLightbox instanceof Lightbox && myLightbox.updateImageList) {
					myLightbox.updateImageList();
				}
			}
		);
	},

	resetSearch: function() {
		k2Search.active = false;
		k2Search.prevSearch = '';

		k2Search.input.val(k2Search.searchPrompt);

		k2Search.reset.unbind('click').fadeTo('fast', 0.3).css('cursor', 'default');

		if ( jQuery('#current-content').length ) {
			jQuery('#dynamic-content').hide().html('');
			jQuery('#current-content').show();
		} else {
			k2Rolling.restoreState();
		}
	}
};
