<?php require('gzip-header-js.php'); ?>

jQuery.noConflict();

var sbm_baseUrl = "";

function sbm_load(id, url) {
		// Next available module ID
		var lastModuleID = id;
		sbm_baseUrl = url;
		
		// Set class as 'current sidebar' hack
		jQuery('.sortable').children().attr('class', function () { return 'module ' + jQuery(this).parent().attr('id') });

		// Set up drop zones for adding available modules
		jQuery('.droppable').Droppable({
			accept:			'availablemodule', 
			activeclass:	'hovering', 
			tolerance:		'pointer',
			onHover:		function (drag) {
				// Show the temp 'result' marker
				var module = jQuery(drag).children().children('span.name').text();

				jQuery(drag)
					.clone()
					.attr('class', 'module marker')
					.css({ position: "static" })
					.html('<div class="slidingdoor"><span class="modulewrapper"></span></div>')
					.appendTo(jQuery(this).children())
			},
			onOut: 			function (drag) {
				// Remove temp 'result' markers
				jQuery(this).children().children('.marker').remove();
			},
			onDrop:			function (drag) {
				// Fetch the needed module info
				var module = jQuery(drag).children().children('span.name').text();
				var type = jQuery(drag).attr('id');
				var sidebar = jQuery(this).children('ul').attr('id');

				// Create new module
				var newModule = jQuery(drag)
									.clone()
									.html('<div class="slidingdoor"><span class="modulewrapper"><span class="name">'+module+'</span><span class="handle"></span><span class="type">'+module+'</span></span><a href="#" class="optionslink"> </a><a href="#" class="deletelink"> </a></div>')
									.attr('id', 'module-' + (lastModuleID++))
									.attr('class', 'module ' + sidebar)
									.css({ position: "static" })

				// Activate Backup Button if needed
				checkBackupLinks()

				// Submit new module info
				jQuery.ajax({
					type: "POST",
					processData: false,
					url: sbm_baseUrl,
					data: "action=k2sbm&sbm_action=add&add_name=" + module + "&add_type=" + type + "&add_sidebar=" + sidebar,
					error: function(){
						// Remove temp markers
						jQuery('.marker').remove()

						// Show an error message
						humanMsg.displayMsg('<strong class="humanMsg-hide humanMsg-error">Error:</strong> <strong>'+module+'</strong> was <strong>not</strong> added.');
					},
					success: function(request, status){
						// Remove temp markers
						jQuery('.marker').remove()

						// Clone dropped module to new home
						jQuery('#'+sidebar).append(newModule)

						// Tell the user what happened
						humanMsg.displayMsg('<strong>'+ module +'</strong> added to <strong>'+ jQuery('#'+sidebar).parent().siblings('h3').text() +'</strong>');

						// Reinitialize the sortable lists
						resizeLists();
					}
				});

			}
		});


		// Set up available modules as draggable
		jQuery('.availablemodule').Draggable({ ghosting: true, revert: true });

		// Config sortable lists
		var sortableLists = '';
		function initSortables() {
			sortableLists = jQuery('ul.sortable').Sortable({
				accept: 		'module',
				activeclass:	'hovering',
				helperclass:	'module marker',
				tolerance:		'pointer',
				opacity:		0.9,
				onStart: function() {
					// Need to re-position #trash for the sortable to work properly
					// jQuery('#trashcontainer').show().css({ zIndex: 1000 })
				},
				onStop: function() {
					// And re-position again.
//					jQuery('#trashcontainer').hide().css({ zIndex: -100 })
				}, 
				onHover: function(drag) {
					jQuery('#sortHelper').html( jQuery(drag).html() )
				},
				onChange: function(serial) {
					// Hide trash
//					jQuery('#trashcontainer').hide().css({ zIndex: -100 })

					// If something is being trashed
//					var trashedModule = jQuery('#trash li:visible');

/*					if (trashedModule.length != 0) {
						jQuery(trashedModule).hide()

						// Add the to-do item to the event queue.
						EVENT_QUEUE.push( jQuery(trashedModule).attr('id') )

						updateUndoLink();

						// Get module name
						var trashedModuleName = jQuery(trashedModule).children().children().children('.name').text();

					trashedModule = '';

					// If the order has changed
					} else {
*/						// Construct the 'orderdata' to reorder the lists
						var orderData = '';
						var lists = jQuery('.reorderable');
						for (var j = 0; j < lists.length; j++) {
							var modules = jQuery(lists[j]).children();

							for (var i = 0; i < modules.length; i++) {
								orderData += 'sidebar_ordering[' + jQuery(lists[j]).attr('id') + '][' + i + ']=' + jQuery(modules[i]).attr('id');

								if (i < modules.length - 1) orderData += "&";
							}

							if (j < lists.length - 1) orderData += "&";
						} // end for

						// Submit new order to db
						jQuery.ajax({
							type: "POST",
							processData: false,
							url: sbm_baseUrl,
							data: 'action=k2sbm&sbm_action=reorder&' + orderData,
							success: function() {
								humanMsg.displayMsg('Module order <strong>saved</strong>');
							},
							error: function(error) {
								humanMsg.displayMsg(error);
							}
							
						});
//					} // End if/else

					resizeLists();

				} // End onChange
			});

			// Initialize the option links for each module
			initOptionLinks();
		};

		// Aesthetic Systems
		function resizeLists() {
			var cols = jQuery('.container:visible').length;
			jQuery('.container:visible').width( (Math.floor(100 / cols) - 1) + '%' );

			// Get the current specified minimum height
			var highest = parseInt(jQuery('.wrap').css('minHeight'));

			// Calculate best height for columns
			jQuery('.container:visible').each(function() {
				var moduleHeight = 40;

				if (jQuery(this).attr('id') == 'availablemodulescontainer')
					moduleHeight = 30;

				var currentContainer = parseInt((jQuery(this).children('div').children('ul').children('li').length * moduleHeight + moduleHeight ));
				var currentHeader = parseInt(jQuery(this).children('h3').height() * 2);
				var currentColumn = currentContainer + currentHeader;

				if ( currentColumn > highest ) {
					highest = currentColumn;
				}
			})

			jQuery('.wrap').animate({ height: highest }, 200)
			jQuery('.container:visible').height(highest)

			// Hack: Clean up the mess, until we fix it :) - Michael
			jQuery('.wrap li').each(function() {
				if (jQuery(this).attr('id') == undefined)
					jQuery(this).remove()
			})

			// Spool the FTL drive
			initSortables();
		}

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
								moduletitle = trim(moduletitle.substring(0, moduletitle.length-1));
								jQuery(this).html(moduletitle+'&hellip;')

								if (jQuery(this).width() < availableWidth) life = 42; // If the shoe fits...
							} while (life != 42);
						});

				} // End if
			});
		}

		function trim(s) {
			s = s.replace(/(^\s*)|(\s*$)/gi,"")
			s = s.replace(/[ ]{2,}/gi," ")
			s = s.replace(/\n /,"\n")
			return s;
		}


// Options GUI
		var curOptModule = '';
		var curOptSidebar = '';
		var curOptName = '';

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
					curOptModule = jQuery(this).parent().parent().attr('id')
					curOptSidebar = jQuery(curOptModule).parent().attr('id')
					curOptName = jQuery(this).siblings('.name').text()
					openOptions(curOptModule)
					return false;
				})
			});

			jQuery('a.deletelink').each(function() {
				jQuery(this).unbind().click(function() {
					// Prevent the user from double-clicking the link
					jQuery(this).unbind()

					// Get the module ID and its parent sidebar ID
					var moduleID = jQuery(this).parent().parent().attr('id')
					var sidebarID = jQuery('#'+moduleID).parent().attr('id')

					// Hide the module
					jQuery('#'+moduleID).slideUp('normal', function() {
						jQuery(this).css({ display: 'list-item', overflow: 'hidden' }).addClass('trashed')

						// Are there modules left?
						checkBackupLinks()
					})

					// Add module to undo stack
					humanUndo.addTrash(moduleID)
					
					initOptionLinks()

					return false;
				})
			})

			// Setup undo button
			jQuery("#undo").unbind().click( function() {
				jQuery('#'+humanUndo.trashList.pop()).removeClass('trashed').slideDown('normal', 'easeOutQuad')
				initOptionLinks()
				humanUndo.updateUndoLink()
				return false;
			})
		
			// Set up options submit process 
			jQuery('#submit').unbind();
			jQuery('#submit').click(function() {
				closeVar = false;

				jQuery('#module-name').val( trim(jQuery('#module-name').val()) )
				
				if (jQuery('#page-ids').children('li').children('input:checked').length == 0) {
					jQuery('#display-pages').attr('checked', '')
					jQuery('#page-ids').children('li').children('input').attr('disabled', 'disabled')
				}

				jQuery('#module-options-form').trigger('submit')
				return false;
			});

			jQuery('#submitclose').unbind();
			jQuery('#submitclose').click(function() {
				closeVar = true;

				jQuery('#module-name').val( trim(jQuery('#module-name').val()) )

				if (jQuery('#page-ids').children('li').children('input:checked').length == 0)
					jQuery('#display-pages').attr('checked', '')

				jQuery('#module-options-form').trigger('submit')
				return false;
			});

			jQuery('#module-options-form').unbind()
			jQuery('#module-options-form').submit(function() {
				jQuery.ajax({
					type: "POST",
					processData: false,
					url: sbm_baseUrl,
					data: "action=k2sbm&sbm_action=update&sidebar_id=" + curOptSidebar + "&module_id=" + curOptModule + "&" + jQuery('#module-options-form').serialize(),
					success: function() {

						// Inform the user the operation was successful
						humanMsg.displayMsg('<strong>'+ jQuery('#module-name').val() +'</strong> options saved');

						// Change the module's name
						jQuery('#'+curOptModule+' .name').text(jQuery('#module-name').val());

						// Close. Maybe.
						if (closeVar == true)
							closeOptions();

						closeVar = false;
					}
				});

	        	return false;
	        });
		}

		function openOptions(module) {
			var moduleID = '#' + module;

			var originalPosition = jQuery(moduleID).offset({ margin:false, border:false });
			var originalWidth = jQuery(moduleID).width()-8;
			var originalHeight = jQuery(moduleID).height();
			var optionsWidth = 460;
			var optionsHeight = 200;
			var optionsX = (jQuery(window).width()) / 2 - ((optionsWidth)/2);
//			var optionsY = (jQuery(window).height()) / 2 - (optionsHeight/2);
			var optionsY = 100;
			var originalName = jQuery(moduleID).children('.name').text();
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

			// Get the options via AJAX
			jQuery.post( sbm_baseUrl, {
					action: 'k2sbm',
					sbm_action: 'control-show',
					module_id: jQuery(moduleID).attr('id')
				},
				function (data) {
					jQuery('#options').hide().empty().append(data).fadeIn('fast', function() {
						jQuery('#module-name').focus()
						jQuery('#optionswindow').removeClass('optionsspinner')
					})

					// Fetch static page list
					jQuery.post( sbm_baseUrl, {
						action: 'k2sbm',
						sbm_action: 'control-page-list-show',
						module_id: jQuery(moduleID).attr('id')
					},
					function (data) {
						jQuery('#specific-pages').empty().append(data)
						
						// Check all page checkboxes if needed
						if ((jQuery('#display-pages').attr('checked') == true) && (jQuery('#page-ids').children('li').children('input:checked').length == 0))
							jQuery('#page-ids').children('li').children('input').attr('checked', 'checked')


						// Setup auto 'select all/select none'
						jQuery('#display-pages').click(function() {
							if (jQuery(this).attr('checked')) {
								jQuery('.checkbox-list > li > input')
									.attr('checked', 'checked')
									.attr('disabled', '')
							} else {
								jQuery('.checkbox-list > li > input')
									.attr('checked', '')
									.attr('disabled', 'disabled')
							}
						})
					}) // End jQuery.post
				})
		

					// Dumbass caret fix. REMOVE ME FOR FF3.0
					// Disabled, as it breaks the 'oveflow: auto'
					/*if (jQuery.browser.mozilla)
						jQuery('#options > *:has(input)').css('position', 'fixed').css('width', optionsWidth + 'px')*/
		}

		function closeOptions() {
			// Reset the tab system
			jQuery('.tabs').children().removeClass('selected')
			jQuery('#optionstab').addClass('selected')

			jQuery('#closelink').hide();
			jQuery('#optionswindow').hide('slow', function() {
				jQuery('#options').empty();
			});

			// Dim overlay
			jQuery('#overlay').hide().css({ opacity: 0 })
			return false;
		}
		
		
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
		}

		
		// Spool the FTL drive
		tabSystem();
		humanUndo.setup(sbm_baseUrl);
		jQuery(window).unload( humanUndo.emptyTrash );

		// Bring Backup/Restore system online
		checkBackupLinks();

		// Ignition Sequence
		jQuery('.initloading').hide().remove();
		jQuery('#optionswindow').css({ visibility: 'visible', display: 'none' });
		resizeLists();

		jQuery(window).resize(resizeLists);

	};