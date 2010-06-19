jQuery.noConflict();

if (typeof K2 == 'undefined') var K2 = {};

K2.debug = false;


// K2.prototype.ajaxComplete = [];


/**
 * Configure K2's AJAX settings.
 */
K2.ajaxGet = function(data, complete_fn) {
	jQuery.ajax({
		url:		K2.AjaxURL,
		data:		data,
		dataType:	'html',

		error: function(request) {
			jQuery('.content').prepend('<p id="rollingalert" class="alert">Error ' + request.status + ': ' + request.statusText + '</p>');
		},

		success: function() {
			jQuery('#rollingalert').remove();
		},

		complete: function(request) {
			// Disable obtrusive document.write
			document.write = function(str) {};

			if ( complete_fn )
				complete_fn( request.responseText );
		}
	});
};


/**
 * Parse out fragments from the URI (eg. something.com#search=bongo)
 * and execute the relevant Rolling Archive or LiveSearch code.
 * Makes use of the BBQ jQuery plugin.
 */
K2.parseFragments = function(event) {
	// Parse out and perform livesearch fragment
	if ( event.getState('search') && K2.LiveSearch ) {
		K2.LiveSearch.doSearch( K2.LiveSearch );
	}

	// If only a page fragment is present
	if ( event.getState('page') && !event.getState('search') && K2.RollingArchives ) {
		K2.RollingArchives.pageSlider.setValue( K2.RollingArchives.pageCount - event.getState('page') + 1 );
	}
}


/**
 * Set the number of columns based on window size
 */ 
function dynamicColumns() {
	var window_width = jQuery(window).width();

	if ( window_width >= (K2.layoutWidths[2] + 20) )
		jQuery('body').removeClass('columns-one columns-two').addClass('columns-three');
	else if ( window_width >= (K2.layoutWidths[1] + 20) )
		jQuery('body').removeClass('columns-one columns-three').addClass('columns-two');
	else
		jQuery('body').removeClass('columns-two columns-three').addClass('columns-one');
}


/**
 * Enable moving labels into the input element they are attached to.
 */
function initOverLabels () {
	if (!document.getElementById) return;

	var labels, id, field;

	// Set focus and blur handlers to hide and show 
	// labels with 'overlabel' class names.
	labels = jQuery('label');
	for (var i = 0; i < labels.length; i++) {

		if (labels[i].className == 'overlabel') {

			// Skip labels that do not have a named association
			// with another field.
			id = labels[i].htmlFor || labels[i].getAttribute('for');
			if ( !id || !(field = document.getElementById(id)) )
				continue;

			// Change the applied class to hover the label 
			// over the form field.
			labels[i].className = 'overlabel-apply';

			// Hide any fields having an initial value.
			if (field.value !== '')
				hideLabel(field.getAttribute('id'), true);

			// Set handlers to show and hide labels.
			field.onfocus = function () {
				hideLabel(this.getAttribute('id'), true);
			};
			field.onblur = function () {
				if (this.value === '')
					hideLabel(this.getAttribute('id'), false);
			};

			// Handle clicks to label elements (for Safari).
			labels[i].onclick = function () {
				var id, field;
				id = this.getAttribute('for');
				if ( id && (field = document.getElementById(id)) )
					field.focus();
			};

		}
	}
};

function hideLabel(field_id, hide) {
	var field_for;
	var labels = document.getElementsByTagName('label');
	for (var i = 0; i < labels.length; i++) {
		field_for = labels[i].htmlFor || labels[i].getAttribute('for');
		
		if (field_for == field_id) {
			labels[i].style.textIndent = (hide) ? '-1000px' : '0px';

			return true;
		}
	}
};



/*
jQuery('.attachment-image').ready(function(){
	resizeImage('.image-link img', '#page', 20);
});

jQuery(window).resize(function(){
	resizeImage('.image-link img', '#page', 20);
});


function resizeImage(image, container, padding) {
	var imageObj = jQuery(image);
	var containerObj = jQuery(container);

	var imgWidth = imageObj.width();
	var imgHeight = imageObj.height();
	var contentWidth = containerObj.width() - padding;

	var ratio = contentWidth / imgWidth;

	imageObj.width(contentWidth).height(imgHeight * ratio);
	console.log('resized to a ratio of ' + ratio);
}
*/


/**
 * Enable ARIA for better disabled accessibility (http://www.w3.org/WAI/intro/aria)
 */
function initARIA() {
	jQuery('#header').attr('role', 'banner');
	jQuery('#header .menu').attr('role', 'navigation');
	jQuery('.primary').attr('role', 'main');
	jQuery('.content').attr('aria-live', 'polite').attr('aria-atomic', 'true');
	jQuery('.secondary').attr('role', 'complementary');
	jQuery('#footer').attr('role', 'contentinfo');
}
jQuery(document).ready( initARIA )


/**
 * Make menu awesome using Superfish
 */ 
function initMenu() {
	jQuery('.headermenu ul').superfish({
		autoArrows:		false,									// Disable generation of arrow mark-up 
		speed:			80,										// Fade-in fast
		onBeforeShow:	function() {							// Make children inherit parents width
							jQuery(this).css('minWidth', jQuery(this).parent().width())
						},
		onHide:			function() {							// Force display levels 3+
							jQuery('.headermenu ul ul ul')
								.removeClass('sf-js-enabled')
								.css('visibility','visible')	
								.css('display', 'block')
						}
	});
	
	// Remove annoying and useless tooltips from the menu.
	jQuery('.menu a').each(function () {
		jQuery(this).attr('title','')
	});

	jQuery('.menu li ul').parent().addClass('has_children');
}
jQuery(document).ready( initMenu )
