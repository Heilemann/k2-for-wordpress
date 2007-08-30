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

var k2Rolling = {

	query: {},
	pageNumber: 0,
	pageCount: 0,
	pageDates: [],
	prevQuery: {},

 	build: function(url, pagetext, pagenumber, pagecount, query, pagedates) {
		k2Rolling.url = url;
		k2Rolling.pageText = pagetext;

		k2Rolling.nextBtn = jQuery('div#rollingarchives a#rollnext');
		k2Rolling.prevBtn = jQuery('div#rollingarchives a#rollprevious');

		k2Rolling.pagesPanel = jQuery('div#rollingarchives div#rollpages');
		k2Rolling.loadingPanel = jQuery('div#rollingarchives div#rollload');
		k2Rolling.datesPanel = jQuery('div#rollingarchives div#rolldates');
		k2Rolling.hoverPanel = jQuery('div#rollingarchives div#rollhover');

		k2Rolling.pageHandle = jQuery('div#rollingarchives div#pagehandle');
		k2Rolling.pageTrack = jQuery('div#rollingarchives div#pagetrack');

		k2Rolling.pageCount = pagecount;
		k2Rolling.pageNumber = pagenumber;
		k2Rolling.query = query;
		k2Rolling.pageDates = pagedates;

		if ( k2Rolling.validatePage(pagenumber) ) {
			jQuery('div#rollingarchives').show();

			k2Rolling.loadingPanel.hide();
			k2Rolling.hoverPanel.hide();

			k2Rolling.setupSlider();
			k2Rolling.setupEvents();
		} else {
			jQuery('div#rollingarchives').hide();
		}
	},

	setupSlider: function() {
		var initSlider = true;

		k2Rolling.pageSlider = k2Rolling.pageTrack.newPageSlider({
			accept: k2Rolling.pageHandle,
			values: [[1000, 0]],
			fractions: k2Rolling.pageCount - 1,
			onSlide: function(xpct, ypct, x, y) {
				if (initSlider) {
					k2Rolling.sliderOffset = Math.round(this.dragCfg.gx);
				}

				if ( jQuery('div#pagetrack #dragHelper').length ) {
					jQuery('div#pagetrack #dragHelper').append(k2Rolling.hoverPanel);
					k2Rolling.hoverPanel.show();
				}

				k2Rolling.updatePageText( k2Rolling.pageCount - Math.round(x/this.dragCfg.fracW) );
			},
			onChange: function(xpct, ypct, x, y) {
				if (!initSlider) {
					k2Rolling.pageHandle.append(k2Rolling.hoverPanel);
					k2Rolling.gotoPage( k2Rolling.pageCount - Math.round(x/this.dragCfg.fracW) );
				}
			}
		});

		// Reposition the slider
		if (k2Rolling.pageNumber > 1) {
			k2Rolling.pageSlider.setPageSlider([
				[ 0 - (k2Rolling.sliderOffset * (k2Rolling.pageNumber - 1)), 0 ]
			]);
		}

		initSlider = false;
	},

	setupEvents: function() {
		k2Rolling.nextBtn.click(function() {
			k2Rolling.pageSlider.setPageSlider([
				[ k2Rolling.sliderOffset, 0 ]
			]);

			k2Rolling.gotoPage(k2Rolling.pageNumber - 1);

			return false;
		});

		k2Rolling.prevBtn.click(function() {
			k2Rolling.pageSlider.setPageSlider([
				[ -k2Rolling.sliderOffset, 0 ]
			]);

			k2Rolling.gotoPage(k2Rolling.pageNumber + 1);

			return false;
		});
	},

	updatePageText: function(page) {
		k2Rolling.pagesPanel.html(
			(k2Rolling.pageText.replace('%1$d', page)).replace('%2$d', k2Rolling.pageCount)
		);
		k2Rolling.datesPanel.html(k2Rolling.pageDates[page - 1]);
	},

	validatePage: function(newpage) {
		if (k2Rolling.pageCount > 1) {
			if (newpage >= k2Rolling.pageCount) {
				jQuery('div#rollingarchives').removeClass().addClass('lastpage');
				return k2Rolling.pageCount;

			} else if (newpage <= 1) {
				jQuery('div#rollingarchives').removeClass().addClass('firstpage');
				return 1;

			} else {
				jQuery('div#rollingarchives').removeClass().addClass('nthpage');
				return newpage;
			}
		}

		jQuery('div#rollingarchives').removeClass().addClass('emptypage');

		return 0;
	},

	gotoPage: function(newpage) {
		page = k2Rolling.validatePage(newpage);

		if ( (page != k2Rolling.pageNumber) && (page > 0) ) {
			k2Rolling.pageNumber = page;

			k2Rolling.loadingPanel.fadeIn('fast');
			jQuery.extend(k2Rolling.query, { paged: k2Rolling.pageNumber, k2dynamic: 1 });
			jQuery.get(k2Rolling.url, k2Rolling.query,
				function(data) {
					k2Rolling.hoverPanel.fadeOut('slow');
					k2Rolling.loadingPanel.fadeOut('fast');
					jQuery('#rollingcontent').html(data);
				}
			);
		}
	},

	saveState: function() {
		k2Rolling.prevQuery = k2Rolling.query;
	},

	restoreState: function() {
		if (k2Rolling.prevQuery != null) {
			var url = k2Rolling.url.replace('theloop', 'rollingarchive');
			var query = jQuery.extend(k2Rolling.prevQuery, { k2dynamic: 'init' });

			jQuery.get(url, query, function(data){
				jQuery('#dynamic-content').html(data);
			});
		}
	}
};

jQuery.fn.extend({
	newRollingArchives: k2Rolling.build,
	saveRollingState: k2Rolling.saveState,
	restoreRollingState: k2Rolling.restoreState
});