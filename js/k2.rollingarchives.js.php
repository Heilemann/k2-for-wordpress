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
				jQuery('#rollingarchives').removeClass().addClass('lastpage');
				return k2Rolling.pageCount;

			} else if (newpage <= 1) {
				jQuery('#rollingarchives').removeClass().addClass('firstpage');
				return 1;

			} else {
				jQuery('#rollingarchives').removeClass().addClass('nthpage');
				return newpage;
			}
		}

		jQuery('#rollingarchives').removeClass().addClass('emptypage');

		return 0;
	},

	gotoPage: function(newpage) {
		var page = k2Rolling.validatePage(newpage);

		if ( (page != k2Rolling.pageNumber) && (page > 0) ) {
			k2Rolling.pageNumber = page;

			jQuery('#rollload').fadeIn('fast');
			jQuery.extend(k2Rolling.query, { paged: k2Rolling.pageNumber, k2dynamic: 1 });
			jQuery.get(k2Rolling.url, k2Rolling.query,
				function(data) {
					jQuery('#rollhover').fadeOut('slow');
					jQuery('#rollload').fadeOut('fast');
					jQuery('#rollingcontent').html(data);

					k2Trimmer.trimAgain();

					// Lightbox v2.03.3 - Adds new images to lightbox
					if ( myLightbox instanceof Lightbox && myLightbox.updateImageList ) {
						myLightbox.updateImageList();
					}
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
