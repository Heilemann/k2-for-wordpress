/**
 * Inserts the Rolling Archives user interface.
 *
 * @param {string}	content		An element to place the UI before. Eg. '#content';
 * @param {string}	pagetext  	A localized string of 'X of Y' inserted into the UI.
 * @param {string}	older  		A localized string of 'Older' inserted into the UI.
 * @param {string}	newer  		A localized string of 'Newer' inserted into the UI.
 * @param {string}	loading		A localized string of 'Loading' inserted into the UI. 
 */
function RollingArchives(content, pagetext, older, newer, loading) {
	ra = this;
	ra.content				= content;
	ra.pageText				= pagetext;
	ra.active				= false;

	// Insert the Rolling Archives UI
	jQuery(content).before('\
		<div id="rollingarchivesbg"></div>\
		<div id="rollingarchives">\
			<div id="rollnavigation">\
				<div id="pagetrackwrap"><div id="pagetrack"><div id="pagehandle"><div id="rollhover"><div id="rolldates"></div></div></div></div></div>\
				\
				<div id="rollpages"></div>\
				\
				<a id="rollprevious" title="' + older + '" href="#"><span>&laquo;</span> '+ older +'</a>\
				<div id="rollload" title="'+ loading +'"><span>'+ loading +'</span></div>\
				<a id="rollnext" title="'+ newer +'" href="#">'+ newer +' <span>&raquo;</span></a>\
				\
				<div id="texttrimmer">\
					<div id="trimmertrim"><span>&raquo;&nbsp;&laquo;</span></div>\
					<div id="trimmeruntrim"><span>&laquo;&nbsp;&raquo;</span></div>\
				</div>\
			</div> <!-- #rollnavigation -->\
		</div> <!-- #rollingarchives -->\
	')
};							

/**
 * Initializes the Rolling Archives system at load or after a new page has been fetched by RA.
 *
 * @param {int} 	pagenumber	The page to get.
 * @param {int}		pagecount	The total number of pages.
 * @param {array}	query		The query to fetch from WordPress
 * @param {array}	pagedates	An array of 'month, year' to show as you scrub the RA slider.
 */
RollingArchives.prototype.setState = function(pagenumber, pagecount, query, pagedates) {
	ra.pageNumber			= pagenumber;
	ra.pageCount 			= pagecount;
	ra.query 				= query;
	ra.pageDates 			= pagedates;

	if ( !jQuery('body').hasClass('rollingarchives') ) {
		// Add click events
		jQuery('#rollnext').click(function() {
			ra.pageSlider.setValueBy(1);
			return false;
		});

		jQuery('#rollprevious').click(function() {
			ra.pageSlider.setValueBy(-1);
			return false;
		});

		jQuery('#trimmertrim').click(function() {
			jQuery('body').addClass('trim');
		})
	
		jQuery('#trimmeruntrim').click(function() {
			jQuery('body').removeClass('trim');
		})

		jQuery('body').addClass('rollingarchives')
	}

	if ( ra.validatePage(pagenumber) ) {
		jQuery('body').removeClass('hiderollingarchives').addClass('showrollingarchives')

		jQuery('#rollingarchives').show();

		jQuery('#rollload').hide();
		jQuery('#rollhover').hide();

		// Setup the page slider
		ra.pageSlider = new K2Slider('#pagehandle', '#pagetrackwrap', {
			minimum: 1,
			maximum: ra.pageCount,
			value: ra.pageCount - ra.pageNumber + 1,
			onSlide: function(value) {
				jQuery('#rollhover').show();
				ra.updatePageText( ra.pageCount - value + 1);
			},
			onChange: function(value) {
				ra.updatePageText( ra.pageCount - value + 1);
				ra.gotoPage( ra.pageCount - value + 1 );
			}
		});

		ra.updatePageText( ra.pageNumber );

		ra.active = true;
	} else {
		jQuery('body').removeClass('showrollingarchives').addClass('hiderollingarchives');
	}
};

/**
 * Save the current set of data for later retrieval using .restoreState.
 */
RollingArchives.prototype.saveState = function() {
	// ra.prevQuery = ra.query;
	ra.originalContent = jQuery(ra.content).html();
};

/**
 * Restore the data saved using .saveState.
 */
RollingArchives.prototype.restoreState = function() {
	if (ra.originalContent != '') {
		jQuery('body').removeClass('livesearchactive').addClass('livesearchinactive'); // Used to show/hide elements w. CSS.

		jQuery(ra.content).html(ra.originalContent)

		jQuery.bbq.removeState('page');
		jQuery.bbq.removeState('search');

		initialRollingArchives();
	}
};

/**
 * Updates the x part of the 'x of y' page counter.
 *
 * @param {int}	page	The page to update to.
 */
RollingArchives.prototype.updatePageText = function(page) {
	jQuery('#rollpages').html(
		(ra.pageText.replace('%1$d', page)).replace('%2$d', ra.pageCount)
	);
	jQuery('#rolldates').html(ra.pageDates[page - 1]);
};


/**
 * Validates a given page number, modifies the classes on 'body' and returns the pagenumber (or 0 if it's outside the available range).
 *
 * @param 	Int	$newpage A requested page number. 
 * @return	Int	A validated page number, or 0 if the number given is outside the legal range.
 */
RollingArchives.prototype.validatePage = function(newpage) {
	if (!isNaN(newpage) && ra.pageCount > 1) {

		if (newpage >= ra.pageCount) {
			jQuery('body').removeClass('onepageonly firstpage nthpage').addClass('lastpage');
			return ra.pageCount;

		} else if (newpage <= 1) {
			jQuery('body').removeClass('onepageonly nthpage lastpage').addClass('firstpage');
			return 1;

		} else {
			jQuery('body').removeClass('onepageonly firstpage lastpage').addClass('nthpage');
			return newpage;
		}
	}

	jQuery('body').removeClass('firstpage nthpage lastpage').addClass('onepageonly');

	return 0;
};


/**
 * Adds removes the 'rollload' class to or from 'body'.
 *
 * @param String $gostop If set to 'start', adds the 'rollload' class, otherwise removes it.
 */
RollingArchives.prototype.loading = function(gostop) {
	if (gostop == 'start')
		jQuery('body').addClass('rollload')
	else
		jQuery('body').removeClass('rollload')
};


/**
 * Makes Rolling Archives go to the page requested.
 *
 * @param Int $newpage The page to go to.
 */
RollingArchives.prototype.gotoPage = function(newpage) {
	var page = ra.validatePage(newpage);

	// Detect if the user was using hotkeys.
	var selected = jQuery('.selected').length > 0;
	
	// New valid page?
	if ( page != ra.pageNumber && page != 0) {
		ra.lastPage = ra.pageNumber;
		ra.pageNumber = page;

		// Update the hash/fragment
		jQuery.bbq.pushState( 'page='+page ); 

		// Show the loading spinner
		ra.loading('start'); 

		// Do fancy animation stuff
		ra.flashElement(page > ra.lastPage ? '#rollprevious' : '#rollnext');
		jQuery('#primary').height(jQuery('#primary').height()) // Don't skip in height
		jQuery(ra.content).hide("slide", { direction: (page > ra.lastPage ? 'right' : 'left') }, 200);

		// ...and scroll to the top if needed
		scrollToContent();

		jQuery.extend(ra.query, { paged: ra.pageNumber, k2dynamic: 1 });

		K2.ajaxGet(ra.query,
			function(data) {
				jQuery('#rollhover').fadeOut('slow');
				ra.loading('stop');

				// Insert the content and show it.
				jQuery(ra.content).html(data)
					.show("slide", { direction: (page > ra.lastPage ? 'left' : 'right') }, 200);
				jQuery('#primary').height('inherit') // Reflow height

				if (selected == true) {
					ra.nextObj = -1;
					ra.scrollTo('.post', 50, 1) // If the hotkeys were used, select the first post
				}
			}
		);
	}

	if (page == 1) // Reset trimmer setting
		jQuery('body').removeClass('trim')
};





/*
 * Scroll to next/previous of given elements. 
 *
 * @param	String	$obj		The element(s) to go to. Is fed directly to jQuery.
 * @param 	Int		$offset		An offset in pixels added to the top, to scroll to.
 * @param 	Int		$direction	1 to go to next, -1 to go to previous.
 * @type	DOM Object
 */
RollingArchives.prototype.scrollTo = function(obj, offset, direction) {
	self = this;

	// Turn off our scroll detection.
	jQuery(window).unbind('scroll.scrolldetector')
	jQuery('html, body').stop()


	// Find the next element below the upper fold
	if (ra.nextObj == undefined) {
		jQuery(obj).each(function(idx) {
			if ( jQuery(this).offset().top - offset > jQuery(window).scrollTop() ) {
				ra.nextObj = (direction === 1 ? idx -1 : idx);
				return false;
			}
		})
	}

	// direction: -1 on the first page? Can't do bub.
	if (direction === -1 && ra.pageNumber === 1 && ra.nextObj === 0) return;

	// Now, who's next?
	ra.nextObj = ra.nextObj + direction;

	// Next element is outside the range of objects? Then let's change the page.
	if ( ( ra.nextObj > jQuery(obj).length - 1 ) || ra.nextObj < 0 ) {
		ra.nextObj = undefined;
		ra.pageSlider.setValueBy(-direction);
		ra.flashElement(direction === 1 ? '#rollprevious' : '#rollnext');
		return;
	}

	// And finally scroll to the element (if the last element in the selection isn't on screen in its entirety).
/* 	if ( jQuery(obj+':first').offset().top + jQuery(obj+':last').offset().top + jQuery(obj+':last').height() > jQuery(window).scrollTop() + jQuery(window).height() ) */

	// Move .selected class to new element, return its vertical position to variable
	nextElementPos = jQuery(obj).removeClass('selected').eq(ra.nextObj).addClass('selected').offset().top - offset;

	// Scroll to the next element. Then detect if user manually scrolls away, in which case we clear our .selected stuff.
	var theBrowserWindow = (jQuery.browser.safari) ? jQuery('body') : jQuery('html'); // Browser differences, hurray.
	theBrowserWindow.animate({ scrollTop: nextElementPos }, 150, 'easeOutExpo', function() { jQuery(window).bind('scroll.scrolldetector', function() { ra.scrollDetection(self, nextElementPos) }) } );
};


/*
 * 'Flashes' and element by doubling its fontsize a microsecond.
 */
RollingArchives.prototype.flashElement = function(el) {
	if (jQuery(el+':animated').length > 0) return; // Prevent errors

	var origSize = parseInt(jQuery(el).css('font-size'));
	jQuery(el).animate({fontSize: origSize * 2}, 30, 'easeInQuad', function() {
		jQuery(el).animate({fontSize: origSize}, 150, 'easeOutQuad')
	})
}


/*
 * Detect whether the user scrolls more than 40px away from the .selected element and then clears .selected stuff.
 */
RollingArchives.prototype.scrollDetection = function(self, scrollPos) {
	// If we're at the bottom already, bail.
	if  (jQuery(document).scrollTop() + jQuery(window).height() >= jQuery(document).height()) return; 

	// "We went too far. He said we went too far..."
	var tolerance = 40;
	if ( jQuery(document).scrollTop() > scrollPos + tolerance || jQuery(document).scrollTop() < scrollPos - tolerance ) {
		jQuery(window).unbind('scroll.scrolldetector');
		jQuery('*').removeClass('selected')
		ra.nextObj = undefined;
	}
};


/*
 * Binds keyboard shortcuts for scrolling back and forth between posts and pages.
 *
 * @param Int	$offset		An offset in pixels added to the top, to scroll to.
 */
RollingArchives.prototype.hotkeys = function(offset) {
	// J: Scroll to next post
	jQuery(document).bind('keydown.hotkeys', {combi: 'J', disableInInput: true}, function() { K2.RollingArchives.scrollTo('.post', offset, 1) });

	// K: Scroll to previous post
	jQuery(document).bind('keydown.hotkeys', {combi: 'K', disableInInput: true}, function() { K2.RollingArchives.scrollTo('.post', offset, -1) });

	// Enter: Go to selected post
	jQuery(document).bind('keydown.hotkeys', {combi: 'Return', disableInInput: true}, function() { if (jQuery('.selected').length > 0) window.location = jQuery('.selected .post-title a').attr('href') });

	// Esc: Deactivate selected post
	jQuery(document).bind('keydown.hotkeys', {combi: 'Esc', disableInInput: true}, function() { jQuery(window).unbind('scroll.scrolldetector'); jQuery('*').removeClass('selected'); K2.RollingArchives.nextObj = undefined });

	// T: Trim, or remove .post-content  
	jQuery(document).bind('keydown.hotkeys', {combi: 'T', disableInInput: true}, function() { 
		
		if ( !jQuery('body').hasClass('trim') ) {
			jQuery('body').addClass('trim')
		} else {
			jQuery('body').removeClass('trim')
		}

		jQuery('#texttrimmer').animate({fontSize: '2em'}, 30, 'easeInQuad', function() {
			jQuery('#texttrimmer').animate({fontSize: '1em'}, 450, 'easeOutQuad')
		})

	});

	// Left Arrow: Previous Page
	jQuery(document).bind('keydown.hotkeys', {combi: 'Left', disableInInput: true}, function() { K2.RollingArchives.pageSlider.setValueBy(-1) });

	// Right Arrow: Next Page
	jQuery(document).bind('keydown.hotkeys', {combi: 'Right', disableInInput: true}, function() { K2.RollingArchives.pageSlider.setValueBy(1) });


}