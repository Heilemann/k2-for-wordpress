jQuery.noConflict();

var closeVar = false;

jQuery(document).ready(function(){
	initDroppables();
	initDraggables();
	initSortables();

	// Spool the FTL drive
	tabSystem();
	humanUndo.setup(sbm_baseUrl);

	// Bring Backup/Restore system online
	checkBackupLinks();

	// Ignition Sequence
	jQuery('.initloading').hide().remove();
	jQuery('#optionswindow').css({ visibility: 'visible', display: 'none' });
	resizeLists();

	jQuery(window).resize(resizeLists);
});

// Set up drop zones for adding available modules
function initDroppables() {
	jQuery('.droppable').droppable({
		accept:		'.new-widget', 
		tolerance:	'pointer',

		activate: function(e, ui) {
			jQuery(this).parent().addClass('active');
		},

		deactivate: function(e, ui) {
			jQuery(this).parent().removeClass('active');
		},

		over: function (e, ui) {
			// Show the temp 'result' marker
			var module = jQuery(ui.draggable).children().children('span.name').text();

			jQuery(ui.draggable)
				.clone()
				.attr('class', 'marker')
				.css({ position: "static" })
				.html('<div class="modulewrapper"></div>')
				.appendTo(jQuery(this).children())
		},

		out: function (e, ui) {
			// Remove temp 'result' markers
			jQuery(this).children().children('.marker').remove();
		},
		drop: function (e, ui) {
			// Fetch the needed module info
			var sidebar_id = jQuery(this).children('ul').attr('id');
			var module_id = jQuery(ui.draggable).attr('id');
			var is_multi = jQuery(ui.draggable).hasClass('multi-widget');

			// Create new module
			var newModule = jQuery(ui.draggable)
								.clone()
								.css({ position: "static" });

			// Activate Backup Button if needed
			checkBackupLinks();

			// Submit new module info
			jQuery.post(
				sbm_baseUrl,
				{
					action: 'k2sbm',
					sbm_action: 'add',
					sidebar: sidebar_id,
					module: module_id,
					is_multi: is_multi
				},
				function(data) {
					// Remove temp markers
					jQuery('.marker').remove();

					if (data.result) {
						if ( is_multi ) {
							jQuery('#' + sidebar_id).append(newModule).draggable('destroy');
						} else {
							jQuery('#' + module_id).appendTo('#' + sidebar_id).draggable('destroy').removeClass('new-widget');
						}

						// Tell the user what happened
						humanMsg.displayMsg('<strong>' + jQuery('#' + module_id + ' .name').text() + '</strong> added to <strong>' + jQuery('#' + sidebar_id).parent().siblings('h3').text() + '</strong>');
					} else {
						// Show an error message
						humanMsg.displayMsg('<strong class="humanMsg-hide humanMsg-error">Error:</strong> <strong>' + jQuery('#' + module_id + ' .name').text() + '</strong> was <strong>not</strong> added.');
					}
				},
				'json'
			);
		}
	});
};

// Set up available modules as draggable
function initDraggables() {
	jQuery('#available-widgets .widget').draggable({
		helper: 'clone',
		zIndex: 10000
	});
};

// Config sortable lists
function initSortables() {
	jQuery('ul.sortable').sortable("destroy");

	jQuery('ul.sortable').sortable({
		tolerance: 'pointer',
		connectWith: jQuery('ul.sortable'),
		zIndex: 10000,
		placeholder: 'marker',

		activate: function(e, ui) {
			jQuery(this).parent().parent().addClass('active');
		},

		deactivate: function(e, ui) {
			jQuery(this).parent().parent().removeClass('active');
		},

		update: function(e, ui) {
			var ordering = "";

			jQuery('ul.sortable').each(function() {
				var sidebar_id = jQuery(this).attr('id');
				var ids = jQuery(this).sortable("toArray");

				if (ordering.length) ordering += "&";

				ordering += sidebar_id + '[]=' + ids.join('&' + sidebar_id + '[]=');
			});

			// Submit new order to db
			jQuery.post(sbm_baseUrl,
				{
					action: "k2sbm",
					sbm_action: "reorder",
					sidebar_ordering: ordering
				},
				function(data) {
					if (data.result) {
						humanMsg.displayMsg('Module order <strong>saved</strong>');
					} else {
						humanMsg.displayMsg(data.error);
					}
				},
				"json"
			);
		}
	});

	// Initialize the option links for each module
	initOptionLinks();
};

// Aesthetic Systems
function resizeLists() {
	var cols = jQuery('.container:visible').length;
	var wrap_width = jQuery('.wrap').width();
	var col_width = Math.floor( wrap_width / (cols * 20) ) * 20;
	if (cols > 3 || wrap_width < 980) {
		jQuery('.container:visible').width( col_width );
		jQuery('.container:visible li').width( col_width - 20 );
	}

	// Spool the FTL drive
	initSortables();
};

function cropTitles() {
	// Figure out how much space is available for the cropped name
	var boink = jQuery('.sortable .name').parents('li:first');
	var availableWidth = jQuery(boink).width() - parseInt(jQuery(boink).css('paddingRight')) - parseInt(jQuery(boink).css('paddingRight')) - jQuery(boink + ' a.optionslink').width() - 30;

	jQuery('.croppedname').remove() // Remove old cropped names

	jQuery('.sortable .name').each(function() { // Crop each name if necessary

		// If name doesn't fit
		if (jQuery(this).width() > availableWidth) {

			// Prepare cropped name
			jQuery(this)
				.hide()
				.clone()
				.attr('class', 'croppedname')
				.insertAfter( jQuery(this) )
				.show()
				.each(function() {
					var moduletitle = jQuery(this).text();
					var life = '';

					// Resize name to fit
					do {
						//moduletitle = trim(moduletitle.substring(0, moduletitle.length-1));
						jQuery(this).html(moduletitle+'&hellip;')

						if (jQuery(this).width() < availableWidth) life = 42; // If the shoe fits...
					} while (life != 42);
				});

		} // End if
	});
};

function tabSystem() {
	var tabContainer = jQuery('.tabs')

	jQuery(tabContainer)
		.children()
		.click(function() {
			jQuery(this).addClass('selected')
				.siblings().removeClass('selected')

			jQuery('.tabcontent').hide()

			// Show the tabs' content
			jQuery('#' + jQuery(this).attr('id') + '-content').show()

			return false;
		})

	jQuery('#closelink').click(closeOptions)
}

function initOptionLinks() {
	var closeVar = false;

	// Set up options buttons
	jQuery('a.optionslink').each(function() {
		jQuery(this).unbind().click(function() {
			var module_id = jQuery(this).parent().parent().attr('id');
			openOptions(module_id);

			return false;
		})
	});

	jQuery('a.deletelink').each(function() {
		jQuery(this).unbind().click(function() {
			// Prevent the user from double-clicking the link
			jQuery(this).unbind();

			// Get the module ID and its parent sidebar ID
			var module_id = jQuery(this).parent().parent().attr('id');
			var sidebar_id = jQuery('#' + module_id).parent().attr('id');
			var is_multi = jQuery('#' + module_id).hasClass('multi-widget');

			jQuery.post(
				sbm_baseUrl,
				{
					action: 'k2sbm',
					sbm_action: 'remove',
					sidebar: sidebar_id,
					module: module_id
				},
				function(data) {
					if (data.result) {
						if ( is_multi ) {
							jQuery('#' + module_id).remove();
						} else {
							jQuery('#' + module_id)
								.appendTo('#available-widgets')
								.addClass('new-widget')
								.draggable({
									helper: 'clone'
								});
						}

						humanMsg.displayMsg('<strong>' + jQuery('#' + module_id + ' .name').text() + '</strong> removed from <strong>' + jQuery('#' + sidebar_id).parent().siblings('h3').text() + '</strong>');
					} else {
						humanMsg.displayMsg(data.error);
					}
				},
				'json'
			);

			initOptionLinks();

			return false;
		});
	});
};

function openOptions(module) {
	var moduleID = '#' + module;

	var originalPosition = jQuery(moduleID).offset({ margin:false, border:false });
	var originalWidth = jQuery(moduleID).width()-8;
	var originalHeight = jQuery(moduleID).height();
	var optionsWidth = 460;
	var optionsHeight = 200;
	var optionsX = (jQuery(window).width()) / 2 - ((optionsWidth)/2);
//	var optionsY = (jQuery(window).height()) / 2 - (optionsHeight/2);
	var optionsY = 100;
	var originalName = jQuery(moduleID + ' .name').text();
	curOptModule = jQuery(moduleID).attr('id');
	curOptSidebar = jQuery(moduleID).parent().attr('id');

	// Dim screen
	jQuery('#overlay')
		.show()
		.css({ opacity: '.5' })
		.click( function() {
			// Note to self: Consider checking whether the forms have been changed, and as if the user wants to save, or close and have an undo.
			closeOptions();
		})

	jQuery('#optionswindow')
		.addClass('optionsspinner')
		.show('slow', function(){
			jQuery('#closelink').show();
		});

	jQuery('#widget-name').text(originalName);

	// Get the options via AJAX
	jQuery.post( sbm_baseUrl, {
			action: 'k2sbm',
			sbm_action: 'control-show',
			module: jQuery(moduleID).attr('id'),
			sidebar: jQuery(moduleID).parent().attr('id')
		},
		function (data) {
			jQuery('#options').hide().empty().append(data).fadeIn('fast', function() {
				jQuery('#module-name').focus();
				jQuery('#optionswindow').removeClass('optionsspinner');
			});

			// Check all page checkboxes if needed
			if ((jQuery('#display-pages').attr('checked') == true) && (jQuery('#page-ids').children('li').children('input:checked').length == 0))
				jQuery('#page-ids').children('li').children('input').attr('checked', 'checked');

			// Setup auto 'select all/select none'
			jQuery('#display-pages').click(function() {
				if (jQuery(this).attr('checked')) {
					jQuery('.checkbox-list > li > input')
						.attr('checked', 'checked')
						.attr('disabled', '');
				} else {
					jQuery('.checkbox-list > li > input')
						.attr('checked', '')
						.attr('disabled', 'disabled');
				}
			});
		},
		'html'
	);

	// Set up options submit process 
	jQuery('#submit').unbind().click(function() {
		if (jQuery('#page-ids').children('li').children('input:checked').length == 0) {
			jQuery('#display-pages').attr('checked', '');
			jQuery('#page-ids').children('li').children('input').attr('disabled', 'disabled');
		}

		closeVar = false;
		jQuery('#module-options-form').trigger('submit');

		return false;
	});

	jQuery('#submitclose').unbind().click(function() {
		if (jQuery('#page-ids').children('li').children('input:checked').length == 0) {
			jQuery('#display-pages').attr('checked', '');
		}

		closeVar = true;
		jQuery('#module-options-form').trigger('submit');

		return false;
	});

	jQuery('#module-options-form').unbind().submit(function() {
		jQuery.ajax({
			dataType: 'html',
			type: "POST",
			processData: false,
			url: sbm_baseUrl,
			data: "action=k2sbm&sbm_action=update&sidebar=" + curOptSidebar + "&module=" + curOptModule + "&" + jQuery('#module-options-form').serialize(),
			success: function(data, textStatus) {
				jQuery(moduleID + ' .display').html(data);

				// Inform the user the operation was successful
				humanMsg.displayMsg('<strong>'+ jQuery('#widget-name').val() +'</strong> options saved');

				// Close. Maybe.
				if (closeVar == true) {
					closeOptions();
				}

				closeVar = false;
			}
		});

		return false;
	});
};

function closeOptions() {
	// Reset the tab system
	jQuery('.tabs').children().removeClass('selected');
	jQuery('#optionstab').addClass('selected');

	jQuery('#closelink').hide();
	jQuery('#optionswindow').hide('slow', function() {
		jQuery('#options').empty();
	});

	// Dim overlay
	jQuery('#overlay').hide().css({ opacity: 0 })

	return false;
};


function checkBackupLinks() {
	// Are the modules to backup?
	if (jQuery('.sortable').children('li:not(.trashed)').length == 0) {
		jQuery('#backupsbm').unbind()
		jQuery('body').addClass('nomodules')
	} else {
		jQuery('#backupsbm').click(function() {
			jQuery('#backupform').submit();
			return false;
		})

		jQuery('body').removeClass('nomodules')
	}

	// Restore button behavior
	jQuery('#restoresbm').click(function() {
		jQuery('#backupsbmwindow').css({ top: 20, opacity: 0, zIndex: 700 }).animate({ top: 38, opacity: 1 }, 600, 'easeOutSine')
		jQuery('#overlay').show().css({ opacity: .5 }).click(function() {
			jQuery('#backupsbmwindow').animate({ top: 20, opacity: 0 }, 600, 'easeOutSine', function() {
				jQuery(this).css({ zIndex: -100 })
				jQuery('#overlay').hide().css({ opacity: 0 })
			})

		})
		return false;
	})
};
