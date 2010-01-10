jQuery.noConflict();

if (typeof K2 == 'undefined') var K2 = {};

K2.debug = false;


//K2.prototype.ajaxComplete = [];

K2.ajaxGet = function(data, complete_fn) {
	jQuery.ajax({
		url:		K2.AjaxURL,
		data:		data,
		dataType:	'html',

		error: function(request) {
			jQuery('#primary').prepend('<p id="rollingalert" class="alert">Error ' + request.status + ': ' + request.statusText + '</p>');
		},

		success: function() {
			jQuery('#rollingalert').remove();
		},

		complete: function(request) {

			// Disable obtrusive document.write
			document.write = function(str) {};

			if ( complete_fn ) {
				complete_fn( request.responseText );
			}

			/*
			if ( K2.callbacks && K2.callbacks.length > 0 ) { 
				for ( var i = 0; i < K2.callbacks.length; i++ ) {
					K2.callbacks[i]();
				}
			 }
			*/
		}
	});
}

function OnLoadUtils() {
	jQuery('#comment-personaldetails').hide();
	jQuery('#showinfo').show();
	jQuery('#hideinfo').hide();
};

function ShowUtils() {
	jQuery('#comment-personaldetails').slideDown();
	jQuery('#showinfo').hide();
	jQuery('#hideinfo').show();
};

function HideUtils() {
	jQuery('#comment-personaldetails').slideUp();
	jQuery('#showinfo').show();
	jQuery('#hideinfo').hide();
};


/* Fix the position of an element when it is about to be scrolled off-screen */
function smartPosition(obj, classname, edge) {
	if ( jQuery.browser.msie && parseInt(jQuery.browser.version, 10) < 7 ) return; /* No IE6 or lower */

	if (edge == 'bottom') { // Check Obj pos vs bottom edge
		jQuery(window)
			.scroll(function() { checkBottom(obj, classname); })
			.resize(function() { checkBottom(obj, classname); })
			.onload(function() { checkBottom(obj, classname); });
	} else {  // Check Obj pos vs top edge
		jQuery(window)
			.scroll(function() { checkTop(obj, classname); });
	}
};


function checkBottom(obj, classname) {
	if ( (document.documentElement.scrollTop + document.documentElement.clientHeight|| document.body.scrollTop + document.documentElement.clientHeight) >= jQuery(obj).offset().top ) {
		jQuery('body').addClass(classname);
	} else {
		jQuery('body').removeClass(classname);
	}
}

function checkTop(obj, classname) {
	if ( (document.documentElement.scrollTop || document.body.scrollTop) >= jQuery(obj).offset().top ) {
		jQuery('body').addClass(classname);
	} else {
		jQuery('body').removeClass(classname);
	}
}

// Set the number of columns based on window size
function dynamicColumns() {
	var window_width = jQuery(window).width();

	if ( window_width >= (K2.layoutWidths[2] + 20) ) {
		jQuery('body').removeClass('columns-one columns-two').addClass('columns-three');
	} else if ( window_width >= (K2.layoutWidths[1] + 20) ) {
		jQuery('body').removeClass('columns-one columns-three').addClass('columns-two');
	} else {
		jQuery('body').removeClass('columns-two columns-three').addClass('columns-one');
	}
};


// Enable moving labels into their input fields.
function initOverLabels () {
	if (!document.getElementById) return;

	var labels, id, field;

	// Set focus and blur handlers to hide and show 
	// labels with 'overlabel' class names.
	labels = document.getElementsByTagName('label');
	for (var i = 0; i < labels.length; i++) {

		if (labels[i].className == 'overlabel') {

			// Skip labels that do not have a named association
			// with another field.
			id = labels[i].htmlFor || labels[i].getAttribute('for');
			if (!id || !(field = document.getElementById(id))) {
				continue;
			} 

			// Change the applied class to hover the label 
			// over the form field.
			labels[i].className = 'overlabel-apply';

			// Hide any fields having an initial value.
			if (field.value !== '') {
				hideLabel(field.getAttribute('id'), true);
			}

			// Set handlers to show and hide labels.
			field.onfocus = function () {
				hideLabel(this.getAttribute('id'), true);
			};
			field.onblur = function () {
				if (this.value === '') {
					hideLabel(this.getAttribute('id'), false);
				}
			};

			// Handle clicks to label elements (for Safari).
			labels[i].onclick = function () {
				var id, field;
				id = this.getAttribute('for');
				if (id && (field = document.getElementById(id))) {
					field.focus();
				}
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


// Enable ARIA (http://www.w3.org/WAI/intro/aria)
function initARIA() {
	jQuery('#header').attr('role', 'banner');
	jQuery('#header .menu').attr('role', 'navigation');
	jQuery('#primary').attr('role', 'main');
	jQuery('#rollingcontent').attr('aria-live', 'polite').attr('aria-atomic', 'true');
	jQuery('.secondary').attr('role', 'complementary');
	jQuery('#footer').attr('role', 'contentinfo');
};


// Make menu awesome using Superfish
function initMenu() {
	jQuery('.menu ul').superfish({
		autoArrows:		false,									// Disable generation of arrow mark-up 
		speed:			80,										// Fade-in fast
		disableHI:		true,									// Don't use hoverIntent
		onBeforeShow:	function() {							// Make children inherit parents width
							jQuery(this).css('minWidth', jQuery(this).parent().width())
						},
		onHide:			function() {							// Force display levels 3+
							jQuery('.menu ul ul ul')
								.removeClass('sf-js-enabled')
								.css('visibility','visible')	
								.css('display', 'block')
						}
	});
	
	// Remove annoying and useless tooltips from the menu.
	jQuery('.menu a').each(function () {
		jQuery(this).attr('title','')
	});
}