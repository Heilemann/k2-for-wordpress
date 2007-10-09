<?php require('header.php'); ?>

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
					.html('<span class="modulewrapper"><span class="name">'+module+'</span><span class="handle"></span><span class="type">'+module+'</span><a href="#" class="optionslink"> </a></span>')
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
									.html('<span class="modulewrapper"><span class="name">'+module+'</span><span class="handle"></span><span class="type">'+module+'</span><a href="#" class="optionslink"> </a></span>')
									.attr('id', 'module-' + (lastModuleID++))
									.attr('class', 'module ' + sidebar)
									.css({ position: "static" })

				// Submit new module info
				jQuery.ajax({
					type: "POST",
					processData: false,
					url: sbm_baseUrl,
					data: "action=add&add_name=" + module + "&add_type=" + type + "&add_sidebar=" + sidebar,
					error: function(){
						// Remove temp markers
						jQuery('.marker').remove()

						// Show an error message
//						jQuery('#msg').text('An error occurred while adding module. Please try again.');
					},
					success: function(request, status){
						// Remove temp markers
						jQuery('.marker').remove()

						// Clone dropped module to new home
						jQuery('#'+sidebar).append(newModule)

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
				opacity:		0.3,
				onStart: 		function() {
					// Need to re-position #trash for the sortable to work properly
					jQuery('#trashcontainer').hide().css({ zIndex: 1000 }).fadeIn();
				},
				onStop: 		function() {
					// And re-position again.
					jQuery('#trashcontainer').animate({ left: -250 }, 300, function() {
 						jQuery(this).css({ zIndex: -1, left: 0 })
					})
				}, 
				onHover: 		function(drag) {
					jQuery('#sortHelper').html( jQuery(drag).html() )
				},
				onChange: 		function(serial) {
					if (jQuery('#trashcontainer').css('zIndex') == 1000)
						jQuery('#trashcontainer').animate({ left: -250 }, 300, function() {
							jQuery(this).css({ zIndex: -1, left: 0 })
						})

					resizeLists();

					// If something is being trashed
					var trashedModule = jQuery.SortSerialize('trash').o.trash[0];

					// Show feedback
					if (trashedModule != undefined) {
						jQuery("#msg")
							.text("'" + jQuery('#'+trashedModule+' .name').text() + "' was trashed")
							.fadeIn(1000);

						setTimeout( function() { jQuery('#msg').fadeOut('3000') }, 4000);

						// Get the trashed module's parent list
						var trashedFromList = jQuery('#'+trashedModule).attr('class').split(' ')[1];

						// Fade trashed module
						jQuery('#trash').children()
							.fadeOut('fast', function() {
								jQuery('#trash').empty();
							});

						// Remove from database
						jQuery.post(sbm_baseUrl + "?action=remove", {
							action: "remove",
							module_id:		trashedModule,
							sidebar_id:		trashedFromList
						}, function() {
							jQuery("#loader").fadeOut(10000).empty();
						});

					// If the order has been changed
					} else {
						// Build New World Order
						var orderData = '';
						var lists = jQuery('.reorderable');
						for (var j = 0; j < lists.length; j++) {
							var modules = jQuery(lists[j]).children();

							for (var i = 0; i < modules.length; i++) {
								orderData += 'sidebar_ordering[' + jQuery(lists[j]).attr('id') + '][' + i + ']=' + jQuery(modules[i]).attr('id');

								if (i < modules.length - 1) orderData += "&";
							}

							if (j < lists.length - 1) orderData += "&";
						}

						// Submit New World Order to db
						jQuery.ajax({
							type: "POST",
							processData: false,
							url: sbm_baseUrl,
							data: 'action=reorder&' + orderData
					 	});
						
					}
				}
			});

			// Initialize the option links for each module
			initOptionLinks();
		};


		function tabSystem() {
			var tabContainer = jQuery('.tabs');
			
			jQuery(tabContainer)
				.children()
				.click(function() {
					jQuery(this).addClass('selected')
						.siblings().removeClass('selected');
					
					jQuery('.tabcontent').hide();
					
					// Show the tabs' content
					jQuery('#' + jQuery(this).attr('id') + '-content').show();

					return false;
				});

			jQuery('#closelink')
				.click(closeOptions);
		}



		// Options Stuff
		var curOptModule = '';
		var curOptSidebar = '';
		var curOptName = '';

		function initOptionLinks() {
			var closeVar = false;

			// Set up options buttons
			jQuery('a.optionslink').each(function() {
				jQuery(this).unbind();
				jQuery(this).click(function() {
					curOptModule = jQuery(this).parent().parent().attr('id');
					curOptSidebar = jQuery(curOptModule).parent().attr('id');
					curOptName = jQuery(this).siblings('.name').text();
					openOptions(curOptModule);
				});
			});

			// Set up options submit process 
			jQuery('#submit').unbind();
			jQuery('#submit').click(function() {
				closeVar = false;
				
				jQuery('#module-name').val( trim(jQuery('#module-name').val()) );

				jQuery(this).parents('form').trigger('submit');
				return false;
			});

			jQuery('#submitclose').unbind();
			jQuery('#submitclose').click(function() {
				closeVar = true;

				jQuery('#module-name').val( trim(jQuery('#module-name').val()) );

				jQuery(this).parents('form').trigger('submit');
				return false;
			});

			jQuery('#module-options-form').unbind();
			jQuery('#module-options-form').submit(function() {
				jQuery.ajax({
					type: "POST",
					processData: false,
					url: sbm_baseUrl,
					data: "action=update&sidebar_id=" + curOptSidebar + "&module_id=" + curOptModule + "&" + jQuery('#module-options-form').serialize(),
					success: function() {
						jQuery('#'+curOptModule+' .name').text(jQuery('#module-name').val());
						jQuery('#msg').text("Options for '" + jQuery("#"+curOptModule+" .name").text() + "' saved successfully").fadeIn('1000');
						setTimeout( function() { jQuery('#msg').fadeOut('3000'); }, 4000);
						cropTitles();
						if (closeVar == true) { closeOptions() };
						closeVar = false;
					}
				});

	        	return false;
	        });
		}


		// Auto-resize lists on window resize
		var secretWidthFormula = 200;

		function resizeLists() {
//			jQuery('ul.sortable').SortableDestroy()
			// Calculate best width for columns
			secretWidthFormula = parseInt(jQuery('.wrap').width()) / (jQuery('.container').size() - 1)
				- ( parseInt(jQuery('.wrap').css('paddingRight')) + parseInt(jQuery('.wrap').css('paddingLeft')) )
				- ( (!isNaN(value = parseInt(jQuery('.container').css('borderRightWidth'))) ? value : 1) + (!isNaN(value = parseInt(jQuery('.container').css('borderLeftWidth'))) ? value : 1) ) - 2;

			// Ensure minimum and maximum sizes
			if (secretWidthFormula < 150 ) { secretWidthFormula = 150 }
			else if (secretWidthFormula > 270 ) { secretWidthFormula = 270 }

			jQuery('.container').width(secretWidthFormula)
			jQuery('#trashcontainer').width(secretWidthFormula+20)
			jQuery('#sidebar-1container').css({ left: secretWidthFormula 		+ 35 })
			jQuery('#sidebar-2container').css({ left: secretWidthFormula * 2 	+ 55 })
			jQuery('#disabledcontainer').css({ left: secretWidthFormula * 3 	+ 80 })
			jQuery('.modulewrapper').width(secretWidthFormula-10)
			cropTitles();
			calculateSecretHeightFormula();
			initSortables();
		}

		function calculateSecretHeightFormula() {
			// Get the current specified minimum height
			var highest = parseInt(jQuery('.wrap').css('minHeight'));
			var highestContainer = 430;

			// Calculate best height for columns
			jQuery('#availablemodulescontainer, #sidebar-1container, #sidebar-2container, #disabledcontainer')
				.each(function() {
					var moduleHeight = '';

					if (jQuery(this).attr('id') != 'availablemodulescontainer') {
						moduleHeight = 37;
					} else {
						moduleHeight = 27;
					}

					var currentContainer = parseInt((jQuery(this).children('div').children('ul').children('li').length * moduleHeight + moduleHeight ));
					var currentHeader = parseInt(jQuery(this).children('h3').height() *2);
					var currentColumn = currentContainer + currentHeader;

					if ( currentColumn > highest ) {
						highest = currentColumn;
						highestContainer = currentContainer;
					}
				})

			jQuery('.wrap').animate({ height: highest })
			jQuery('.container').height(highest)
			jQuery('.container ul').height(highestContainer)
			jQuery('#trashcontainer').height(highest+13)

			// Hack: Clean up the mess, until we fix it :)
			jQuery('.wrap li').each(function() {
				if (jQuery(this).attr('id') == undefined)
					jQuery(this).remove()
			})
		}

		function cropTitles() {
			jQuery('.croppedname').remove();
			jQuery('.sortable .name').each(function() {

				var availableWidth = jQuery(this).parents('li').width() - parseInt(jQuery(this).parents('li').css('paddingRight')) - parseInt(jQuery(this).parents('li').css('paddingRight')) - jQuery(this).siblings('a.optionslink').width() - 20;
				var nameWidth = jQuery(this).width();

				// If name doesn't fit
				if (nameWidth > availableWidth) {

					// Prepare cropped name
					jQuery(this)
						.hide()
						.clone()
						.removeClass('name')
						.addClass('croppedname')
						.insertAfter( jQuery(this) )
						.show()
						.each(function() {
							var moduletitle = jQuery(this).text();
							var life = '';
							
							// Resize name to fit
							while (life != 42) {
								moduletitle = trim(moduletitle.substring(0, moduletitle.length-1));
								jQuery(this).html(moduletitle+'&hellip;');

								// Are we done yet?
								if (jQuery(this).width() < availableWidth) life = 42; 
							} // End While
						}); // End close & prep

				} // End if
			});
		} // End function

		function trim(s) {
			s = s.replace(/(^\s*)|(\s*$)/gi,"");
			s = s.replace(/[ ]{2,}/gi," ");
			s = s.replace(/\n /,"\n");
			return s;
		}
		
		jQuery(window).resize(resizeLists);
		
		jQuery(document).ready(function() {
			resizeLists();
			tabSystem();
			jQuery('.initloading').fadeOut().remove()
			jQuery('.container').animate({ opacity: 1 })
			jQuery('#overlay').fadeTo('normal', 0)
			jQuery('.wrap').append('<div id="darken"></div>')
			jQuery('#darken').css({ zIndex: 2, left: 1180 })
		});

		// Options UI
		function openOptions(module) {
			var moduleID = '#' + module;

			var originalPosition = jQuery(moduleID).offset({ margin:false, border:false });
			var originalWidth = jQuery(moduleID).width()-8;
			var originalHeight = jQuery(moduleID).height();
			var optionsWidth = 400;
			var optionsHeight = 350;
			var optionsX = (jQuery(window).width()) / 2 - ((optionsWidth)/2);
//			var optionsY = (jQuery(window).height()) / 2 - (optionsHeight/2);
			var optionsY = 100;
			var originalName = jQuery(moduleID).children('.name').text();
			curOptModule = jQuery(moduleID).attr('id');
			curOptSidebar = jQuery(moduleID).parent().attr('id');

			// Dim screen
			jQuery('#overlay')
				.css({ zIndex: 500, opacity: .5 })
				.click(function() {
					// Note to self: Consider checking whether the forms have been changed, and as if the user wants to save, or close and have an undo.
					closeOptions();
				})

			jQuery('#optionswindow')
				.addClass('optionsspinner')
				.show()
				.css({ top: optionsY, left: optionsX, width: optionsWidth, height: optionsHeight })

			// Get the options via AJAX
			jQuery.post( sbm_baseUrl, {
					action: 'control-show',
					module_id: jQuery(moduleID).attr('id')
				},
				function (data) {
					jQuery('#options').empty().append(data);
					jQuery('#module-name').focus();
					jQuery('#optionswindow').removeClass('optionsspinner');

					// Fetch static page list
					jQuery.post( sbm_baseUrl, {
						action: 'control-page-list-show',
						module_id: jQuery(moduleID).attr('id')
					},
					function (data) {
						jQuery('#specific-pages').empty().append(data)

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
					})


					// Dumbass caret fix. REMOVE ME FOR FF3.0
					if(jQuery.browser.mozilla) {
						jQuery('#options > *:has(input)').css('position', 'fixed').css('width', optionsWidth + 'px');
					}
				}
			);
			
		}

		function closeOptions() {
			// Reset the tab system
			jQuery('.tabs').children().removeClass('selected');
			jQuery('#optionstab').addClass('selected');
			
			jQuery('#options').empty();
			jQuery('#optionswindow').hide();
			// Dim overlay
			jQuery('#overlay').css({ opacity: 0, zIndex: -100 }) //fadeTo('normal', 0, function() { jQuery(this).css({ zIndex: '-100' }) });
			return false;
		}



		jQuery('#backupsbm').click(function() {
			jQuery('#backupform').submit();
			return false;
		})

		jQuery('#restoresbm').click(function() {
			jQuery('#backupsbmwindow').css({ top: 20, opacity: 0, zIndex: 700 }).animate({ top: 38, opacity: 1 }, 1000, 'easeOutSine')
			jQuery('#overlay').css({ zIndex: 600 }).fadeTo('normal', .5).click(function() {
				jQuery('#backupsbmwindow').animate({ top: 20, opacity: 0, zIndex: -1 }, 1000, 'easeOutSine')
				jQuery(this).fadeTo('normal', 0, function() {
					jQuery(this).css({ zIndex: -1})
				})
			})
			return false;
		})
	};