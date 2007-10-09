<?php require('header.php'); ?>

var k2Rolling = {
	setup: function(url, pagetext, pagenumber, pagecount, query, pagedates) {
		k2Rolling.url = url;
		k2Rolling.pageText = pagetext;

		k2Rolling.pageCount = pagecount;
		k2Rolling.pageNumber = pagenumber;
		k2Rolling.query = query;
		k2Rolling.pageDates = pagedates;

		if ( k2Rolling.validatePage(pagenumber) ) {
			jQuery('#rollingarchives').show();

			jQuery('#rollload').hide();
			jQuery('#rollhover').hide();

			k2Rolling.setupSlider();
			k2Rolling.setupEvents();
			k2Rolling.updatePageText( k2Rolling.pageNumber );
		} else {
			jQuery('#rollingarchives').hide();
		}
	},

	setupSlider: function() {
		k2Rolling.pageSlider = new K2Slider('#pagehandle', '#pagetrack', {
			minimum: 1,
			maximum: k2Rolling.pageCount,
			value: k2Rolling.pageCount - k2Rolling.pageNumber + 1,
			onSlide: function(value) {
				jQuery('#rollhover').show();
				k2Rolling.updatePageText( k2Rolling.pageCount - value + 1);
			},
			onChange: function(value) {
				k2Rolling.updatePageText( k2Rolling.pageCount - value + 1);
				k2Rolling.gotoPage( k2Rolling.pageCount - value + 1 );
			}
		});
	},

	setupEvents: function() {
		jQuery('#rollnext').click(function() {
			k2Rolling.pageSlider.setValueBy(1);
			return false;
		});

		jQuery('#rollprevious').click(function() {
			k2Rolling.pageSlider.setValueBy(-1);
			return false;
		});
	},

	updatePageText: function(page) {
		jQuery('#rollpages').html(
			(k2Rolling.pageText.replace('%1$d', page)).replace('%2$d', k2Rolling.pageCount)
		);
		jQuery('#rolldates').html(k2Rolling.pageDates[page - 1]);
	},

	validatePage: function(newpage) {
		if (k2Rolling.pageCount > 1) {
			if (newpage >= k2Rolling.pageCount) {
				jQuery('#dynamic-content').removeClass().addClass('lastpage');
				return k2Rolling.pageCount;

			} else if (newpage <= 1) {
				jQuery('#dynamic-content').removeClass().addClass('firstpage');
				return 1;

			} else {
				jQuery('#dynamic-content').removeClass().addClass('nthpage');
				return newpage;
			}
		}

		jQuery('#dynamic-content').removeClass().addClass('emptypage');

		return 0;
	},

	gotoPage: function(newpage) {
		var page = k2Rolling.validatePage(newpage);

		if ( (page != k2Rolling.pageNumber) && (page > 0) ) {
			k2Rolling.pageNumber = page;

			jQuery('#rollload').fadeIn('fast');
			jQuery('html,body').animate({ scrollTop: jQuery('#dynamic-content').offset().top -1 }, 1000);
			jQuery.extend(k2Rolling.query, { paged: k2Rolling.pageNumber, k2dynamic: 1 });

			K2.ajaxGet(k2Rolling.url, k2Rolling.query,
				function(data) {

					jQuery('#rollhover').fadeOut('slow');
					jQuery('#rollload').fadeOut('fast');
					jQuery('#rollingcontent').html(data);
					
					k2Trimmer.trimAgain();
				}
			);
		}

		if (page == 1)
			k2Trimmer.slider.setValue(100);
	},

	saveState: function() {
		k2Rolling.prevQuery = k2Rolling.query;
	},

	restoreState: function() {
		if (k2Rolling.prevQuery != null) {
			var url = k2Rolling.url.replace('theloop', 'rollingarchive');
			var query = jQuery.extend(k2Rolling.prevQuery, { k2dynamic: 'init' });

			K2.ajaxGet(url, query,
				function(data) {
					jQuery('#dynamic-content').html(data);
				}
			);
		}
	}
};

function smartPosition() {
	// Detect if content is being scroll offscreen.
	if ( (document.documentElement.scrollTop || document.body.scrollTop) >= jQuery('#dynamic-content').offset().top) {
		jQuery('body').addClass('fixraposition');
	} else {
		jQuery('body').removeClass('fixraposition');
	}
};

jQuery(document).scroll(smartPosition);
