/**
 * Inserts and initializes the Rolling Archives user interface.
 *
 * @param {string}	content		An element to place the UI before. Eg. '.content';
 * @param {string}	posts		Class of the elements containing individual posts.
 * @param {string}	parent		ID of parent element of RA UI, for .smartposition.
 * @param {string}	pagetext  	A localized string of 'of', as in 'X of Y', inserted into the UI.
 * @param {string}	older  		A localized string of 'Older' inserted into the UI.
 * @param {string}	newer  		A localized string of 'Newer' inserted into the UI.
 * @param {string}	loading		A localized string of 'Loading' inserted into the UI. 
 * @param {Int}		offset		Value in pixels to offset scrolls to an element with. Defaults to 0.
 **/
function RollingArchives(args) {
	RA						= this;

	RA.content				= args.content;
	RA.posts				= args.posts;
	RA.parent				= args.parent;
	RA.offsetTop			= args.offset		|| 0;
	RA.cache				= new Array();
	RA.cacheDepth			= args.cachedepth	|| 5;

	// Localization strings for the UI.
	RA.pageText				= args.pagetext		|| 'of';
	var older				= args.older		|| 'Older';
	var newer				= args.newer		|| 'Newer';
	var loading				= args.loading		|| 'Loading';

	// Insert the Rolling Archives UI
	jQuery(RA.content).before('\
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
	');

	// Set and save the initial state
	RA.setState( args.pagenumber, args.pagecount, args.query, args.pagedates );
	RA.saveState();

	// Add click events
	jQuery('#rollnext').click(function() {
		RA.pageSlider.setValueBy(1);
		return false;
	});

	jQuery('#rollprevious').click(function() {
		RA.pageSlider.setValueBy(-1);
		return false;
	});

	jQuery('#trimmertrim, #trimmeruntrim').click(function() {
		if (K2.Animations)
			jQuery('.entry-content').slideToggle(250, 'easeOutExpo')
		jQuery('body').toggleClass('trim')
	})

/* 	RA.assignHotkeys(); // Setup Keyboard Shortcuts */

	jQuery('body').addClass('rollingarchives'); // Put the world on notice.

	RA.smartPosition(RA.parent); // Prepare a 'sticky' scroll point
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
	RA.pageNumber			= pagenumber;
	RA.pageCount 			= pagecount;
	RA.query 				= query;
	RA.pageDates 			= pagedates;

	if ( RA.validatePage(RA.pageNumber) ) {
		jQuery('body').removeClass('hiderollingarchives').addClass('showrollingarchives')
		jQuery('#rollingarchives').show();
		jQuery('#rollload, #rollhover').hide();

		// Setup the page slider
		RA.pageSlider = new K2Slider('#pagehandle', '#pagetrackwrap', {
			minimum:	1,
			maximum:	RA.pageCount,
			value:		RA.pageCount - RA.pageNumber + 1,
			onSlide:	function(value) {
							jQuery('#rollhover').show();
							RA.updatePageText( RA.pageCount - value + 1);
						},
			onChange:	function(value) {
							RA.updatePageText( RA.pageCount - value + 1);
							RA.gotoPage( RA.pageCount - value + 1 );
						}
		})

		RA.updatePageText( RA.pageNumber )
	} else {
		jQuery('body').removeClass('showrollingarchives').addClass('hiderollingarchives');
	}

	RA.resetCache();
};


/**
 * Save the current set of data for later retrieval using .restoreState.
 */
RollingArchives.prototype.saveState = function() {
	RA.prevPageNumber		= RA.pageNumber;
	RA.prevPageCount		= RA.pageCount;
	RA.prevQuery			= RA.query;
	RA.prevPageDates		= RA.pageDates;
	RA.prevContent			= jQuery(RA.content).html();
};


/**
 * Restore the data saved using .saveState.
 */
RollingArchives.prototype.restoreState = function() {
	if (RA.prevContent != '') {
		jQuery('body').removeClass('livesearchactive').addClass('livesearchinactive'); // Used to show/hide elements w. CSS.

		jQuery(RA.content).html(RA.prevContent)

		jQuery.bbq.pushState( '#page=' + RA.prevPageNumber );

		RA.setState( RA.prevPageNumber, RA.prevPageCount, RA.prevQuery, RA.prevPageDates );
	}
};


/**
 * Updates the x part of the 'x of y' page counter.
 *
 * @param {int}	page	The page to update to.
 */
RollingArchives.prototype.updatePageText = function(page) {
	jQuery('#rollpages').html(page +' '+ RA.pageText +' '+ RA.pageCount)
	jQuery('#rolldates').html(RA.pageDates[page - 1])
};


/**
 * Validates a given page number, modifies the classes on 'body' and returns the pagenumber (or 0 if it's outside the available range).
 *
 * @param 	{Int}	newpage A requested page number. 
 * @return	{Int}			A validated page number, or 0 if the number given is outside the legal range.
 */
RollingArchives.prototype.validatePage = function(newpage) {
	var newpage = parseInt(newpage);

	if (!isNaN(newpage) && RA.pageCount > 1) {

		if (newpage >= RA.pageCount) {
			jQuery('body').removeClass('onepageonly firstpage nthpage').addClass('lastpage');
			return RA.pageCount;

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
 * @param {String} gostop If set to 'start', adds the 'rollload' class, otherwise removes it.
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
 * @param {Int} newpage The page to go to.
 */
RollingArchives.prototype.gotoPage = function(newpage) {
	var page = RA.validatePage(newpage);

	// Detect if the user was using hotkeys.
/* 	var selected = jQuery('.selected').length > 0; */
	
	// New valid page?
	if (page != RA.pageNumber && page != 0) {
		RA.lastPage = RA.pageNumber;
		RA.pageNumber = page;

		// Update the hash/fragment
		jQuery.bbq.pushState( 'page=' + page );

		// Show the loading spinner
		RA.loading('start')

		// Do fancy animation stuff
		if (K2.Animations) {
			RA.flashElement(page > RA.lastPage ? '#rollprevious' : '#rollnext')
/*
			jQuery(RA.parent).height(jQuery(RA.parent).height()) // Don't skip in height
			jQuery(RA.content).hide("slide", { direction: (page > RA.lastPage ? 'right' : 'left'), easing: 'easeInExpo'}, 200)
*/
		}

		// ...and scroll to the top if needed
		if (K2.Animations && (RA.pageNumber != 1) && jQuery('body').hasClass('smartposition'))
			jQuery('html,body').animate({ scrollTop: jQuery(RA.parent).offset().top }, 100)

		jQuery.extend(RA.query, { paged: RA.pageNumber, k2dynamic: 1 })

		if (RA.cache[RA.pageNumber] == undefined) {

			K2.ajaxGet(RA.query,
				function(data) {
					jQuery('#rollhover').fadeOut('slow')
					RA.loading('stop')
	
					// Insert the content and show it.
					jQuery(RA.content).html(data)
	
/*
					if (K2.Animations)
						jQuery(RA.content).show("slide", { direction: (page > RA.lastPage ? 'left' : 'right'), easing: 'easeOutExpo' }, 450, jQuery(RA.parent).height('inherit'))
*/

/*
					if (selected == true)
						RA.scrollTo(RA.posts, 1, (page > RA.lastPage ? -1 : jQuery(RA.posts).length -2 )) // If the hotkeys were used, select the first post
*/
				}
			)

		} else {
			
			jQuery('#rollhover').fadeOut('slow')
			RA.loading('stop')

			// Insert the content and show it.
			jQuery(RA.content).html(RA.cache[RA.pageNumber])

/*
			if (K2.Animations)
				jQuery(RA.content).show("slide", { direction: (page > RA.lastPage ? 'left' : 'right'), easing: 'easeOutExpo' }, 450, jQuery(RA.parent).height('inherit'))
*/
		}

		RA.updateCache();
	}

	if (page == 1) {
		jQuery('body').removeClass('trim') // Reset trimmer setting
		var pos = jQuery(window).scrollTop(); // get scroll position
		jQuery.bbq.removeState('page');
		jQuery(window).scrollTop(pos); // set scroll position back
	}
};


/**
 *	Deletes the entries of the cache array.
 **/
RollingArchives.prototype.resetCache = function() {
	RA.cache = new Array();
}


/**
 *	Handles caching of pages around the currently displayed page, for great speed and glory.
 **/
RollingArchives.prototype.updateCache = function() {
	// Cache pages in proximity as needed
	var lowerLimit = RA.pageNumber - RA.cacheDepth; // Newer pages
	var upperLimit = RA.pageNumber + RA.cacheDepth; // Older pages

	// don't go over/under the number of pages
	if ( lowerLimit < 1 ) lowerLimit = 1;
	if ( upperLimit > RA.pageCount ) upperLimit = RA.pageCount;

	for (var i = lowerLimit; i <= upperLimit; i++) {
		if ( i == RA.pageNumber ) continue;

		if (RA.cache[i] == undefined) {
			jQuery.extend(RA.query, { paged: i, k2dynamic: 1 });
	
			(function(i) { K2.ajaxGet(RA.query, function(request) { RA.cache[i] = request } ); })(i);
		}
	}

	// Purge out of bounds cache.
	if (RA.cache.length > 0) {
		for (var j = 0; j < RA.cache.length; j++) {
			if (j >= (lowerLimit - 1) && j <= (upperLimit +1) ) continue;
			
			delete RA.cache[j];
		}
	}

}

/**
 * When a given element scrolls off the top of the screen, add a given classname to 'body'. 
 *
 * @param {String} obj			The element to watch.
 * @param {String} edge			Can be set to 'bottom', in which case it checks to see if it's
 * 								scrolled off the bottom. Otherwise it always checks the top.
 */
RollingArchives.prototype.smartPosition = function(e, edge) {
	RA.parentTop		= jQuery(e).offset().top;
	RA.smartPosClass	= 'smartposition'

	if ( jQuery.browser.msie && parseInt(jQuery.browser.version, 10) < 7) return; // No IE6 or lower

	if (edge != 'bottom') { // Check Obj pos vs top edge by default
		setTimeout( RA.checkTop, 100); // Check on load.
		
		jQuery(window)
			.scroll( RA.checkTop );
	} else {  // Check Obj pos vs bottom edge
		setTimeout( RA.checkTop, 100); // Check on load.

		jQuery(window)
			.scroll( RA.checkBottom )
			.resize( RA.checkBottom )
			.onload( RA.checkBottom )
	}
};


/**
 * Check if an element disappears underneath the fold
 */
RollingArchives.prototype.checkBottom = function() {
	if ( (document.documentElement.scrollTop + document.documentElement.clientHeight || document.body.scrollTop + document.documentElement.clientHeight) >= RA.parentTop && jQuery('body').hasClass('showrollingarchives'))
		jQuery('body').addClass(RA.smartPosClass);
	else
		jQuery('body').removeClass(RA.smartPosClass);
}


/**
 * Check if an element disappears above the window
 */
RollingArchives.prototype.checkTop = function() {
	if ( jQuery(document).scrollTop() >= RA.parentTop  && jQuery('body').hasClass('showrollingarchives'))
		jQuery('body').addClass(RA.smartPosClass);
	else
		jQuery('body').removeClass(RA.smartPosClass);
};


/*
 * Scroll to next/previous of given elements. 
 *
 * @param	{String}	elements	The element(s) to go to. Is fed directly to jQuery.
 * @param 	{Int}		offset		An offset in pixels added to the top, to scroll to.
 * @param 	{Int}		direction	1 to go to next, -1 to go to previous.
 * @type	{DOM Object}
 */
RollingArchives.prototype.scrollTo = function(elements, direction, next) {
	// Turn off our scroll detection.
	jQuery(window).unbind('scroll.scrolldetector')
	jQuery('html, body').stop()

	// Someone telling us where to go?
	RA.nextIndex = (next != undefined ? next : RA.nextIndex);

	// Find the next element below the upper fold
	if (RA.nextIndex == undefined) {
		jQuery(elements).each(function(idx) {
/* 			console.log( jQuery(this).offset().top +' - '+ RA.offsetTop +' > '+ jQuery(window).scrollTop() ); */
			if ( jQuery(this).offset().top - RA.offsetTop > jQuery(window).scrollTop() ) {
				RA.nextIndex = (direction === 1 ? idx -1 : idx);
				console.log( 'Next index: '+RA.nextIndex );
				return false;
			}
		})
	}

	// direction: -1 on the first page? Can't do bub.
	if (direction === -1 && RA.pageNumber === 1 && RA.nextIndex === 0) return;

	// Now, who's next?
	RA.nextIndex = RA.nextIndex + direction;

	// Next element is outside the range of objects? Then let's change the page.
	if ( ( RA.nextIndex > jQuery(elements).length - 1 ) || RA.nextIndex < 0 ) {
		RA.nextIndex = undefined;
		RA.pageSlider.setValueBy(-direction);
		RA.flashElement(direction === 1 ? '#rollprevious' : '#rollnext');
	}

	// And finally scroll to the element (if the last element in the selection isn't on screen in its entirety).
/* 	if ( jQuery(elements+':first').offset().top + jQuery(elements+':last').offset().top + jQuery(elements+':last').height() > jQuery(window).scrollTop() + jQuery(window).height() ) */

	// Move .selected class to new element, return its vertical position to variable
	RA.nextElement			= jQuery(elements).eq(RA.nextIndex);
	var nextElementPos		= RA.nextElement.offset().top - RA.offsetTop;
	var theBrowserWindow 	= (jQuery.browser.safari) ? jQuery('body') : jQuery('html'); // Browser differences, hurray.

	// Scroll to the next element. Then detect if user manually scrolls away, in which case we clear our .selected stuff.
	theBrowserWindow.animate({ scrollTop: nextElementPos }, (K2.Animations ? 150 : 0), 'easeOutExpo', function() { RA.nextElement.effect('highlight',{color: '#dddddd'}, 1500) } );
};


/*
 * 'Flashes' and element by doubling its fontsize a microsecond.
 */
RollingArchives.prototype.flashElement = function(el) {
	if (jQuery(el+':animated').length > 0 || !K2.Animations) return; // Prevent errors

	var origSize = parseInt(jQuery(el).css('font-size'));
	jQuery(el).animate({fontSize: origSize * 2}, 0, 'linear', function() {
		jQuery(el).animate({fontSize: origSize}, 150, 'easeOutQuad')
	})
}


/*
 * Binds keyboard shortcuts for scrolling back and forth between posts and pages.
 */
RollingArchives.prototype.assignHotkeys = function() {
	// J: Scroll to next post
	jQuery(document).bind('keydown.hotkeys', 'J', function() { RA.scrollTo(RA.posts, 1) });

	// K: Scroll to previous post
	jQuery(document).bind('keydown.hotkeys', 'K', function() { RA.scrollTo(RA.posts, -1) });

	// Enter: Go to selected post
	jQuery(document).bind('keydown.hotkeys', 'Return', function() { if (jQuery(RA.nextElement).length > 0) {
		jQuery(RA.nextElement).stop(true,true).effect("highlight", {color: '#eee'}, 150).children('.entry-title a').click() } });

	// K: Scroll to previous post
	jQuery(document).bind('keydown.hotkeys', 'E', function() { if (jQuery('.selected').length > 0) { jQuery('.selected a.post-edit-link').click(); RA.flashElement('.selected a.post-edit-link') } });

	// Esc: Deactivate selected post
	jQuery(document).bind('keydown.hotkeys', 'Esc', function() { jQuery(window).unbind('scroll.scrolldetector'); jQuery('*').removeClass('selected'); RA.nextIndex = undefined });

	// H: Go back to page 1  
	jQuery(document).bind('keydown.hotkeys', 'H', function() { RA.gotoPage(1) })

	// T: Trim, or remove .entry-content  
	jQuery(document).bind('keydown.hotkeys', 'T', function() { jQuery('#texttrimmer div:visible').click() });

	// Left Arrow: Previous Page
	jQuery(document).bind('keydown.hotkeys', 'Left', function() { RA.pageSlider.setValueBy(-1) });

	// Right Arrow: Next Page
	jQuery(document).bind('keydown.hotkeys', 'Right', function() { RA.pageSlider.setValueBy(1) });
}