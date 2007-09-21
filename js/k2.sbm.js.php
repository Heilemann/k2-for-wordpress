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
				jQuery(drag)
					.clone()
					.attr('class', 'module marker')
					.css({ position: "static" })
					.append('<span class="type">'+jQuery(drag).children().text()+'</span>')
					.appendTo(jQuery(this).children());
			},
			onOut: function (drag) {
				// Remove temp 'result' markers
				jQuery(this).children().children('.marker').remove();
			},
			onDrop:	function (drag) {
				// Fetch the needed module info
				var module = jQuery(drag).children('span.name').text();
				var type = jQuery(drag).attr('id');
				var sidebar = jQuery(this).children('ul').attr('id');

				// Create new module
				var newModule = jQuery(drag).clone().empty()
									.html('<div><span class="name">'+jQuery(drag).children().text()+'</span><span class="type">'+jQuery(drag).children().text()+'</span><a href="#" class="optionslink"> </a></div>')
									.attr('id', 'module-' + (lastModuleID++))
									.attr('class', 'module ' + sidebar)
									.css({ position: "static" });

				// Show spinner on marker module
				jQuery('.marker').addClass('spinner');

				// Submit new module info
				jQuery.ajax({
					type: "POST",
					processData: false,
					url: sbm_baseUrl,
					data: "action=add&add_name=" + module + "&add_type=" + type + "&add_sidebar=" + sidebar,
					error: function(){
						// Remove temp markers
						jQuery('.marker').remove();

						// Show an error message
//						jQuery('#msg').text('An error occurred while adding module. Please try again.');
					},
					success: function(request, status){
						// Remove temp markers
						jQuery('.marker').remove();

						// Clone dropped module to new home
						jQuery('#'+sidebar).append(newModule);

						// Reinitialize the sortable lists
						destroySortables();
						initSortables();
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
				helperclass:	'sorthelper',
				tolerance:		'pointer',
				opacity:		0.5,
				onHover:		function(drag) {
					jQuery('.sorthelper')
						.removeAttr('style')
						.html( jQuery(drag).html() );
				},
				onChange:		function(serial) {
					// If something is being trashed
					var trashedModule = jQuery.SortSerialize('trash').o.trash[0];
					console.log(jQuery('#'+trashedModule+' .name').text());

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

		tabSystem();


		function destroySortables() {
			jQuery('ul.sortable').SortableDestroy();
		}

		initSortables();


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
					return false;
				});
			});

			// Set up options submit process 
			jQuery('#submit').unbind();
			jQuery('#submit').click(function() {
				closeVar = false;
				jQuery(this).parents('form').trigger('submit');
				return false;
			});

			jQuery('#submitclose').unbind();
			jQuery('#submitclose').click(function() {
				closeVar = true;
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
						console.log(closeOptions);
						if (closeVar == true) { closeOptions() };
						closeVar = false;
					}
				});

	        	return false;
	        });
		}


		// Auto-resize lists on window resize
		var secretFormula;
		function calculateSecretFormula() {
			// Calculate best width for lists
			secretFormula = parseInt(jQuery('.wrap').width() / jQuery('.container').length)
				- ( parseInt(jQuery('.wrap').css('paddingRight')) + parseInt(jQuery('.wrap').css('paddingLeft')) )
				- ( parseInt(jQuery('.container').css('borderRightWidth')) + parseInt(jQuery('.container').css('borderLeftWidth')) ) - 2;

			// Ensure minimum and maximum sizes
			if (secretFormula < 150 ) { secretFormula = 150 }
			else if (secretFormula > 270 ) { secretFormula = 270 }
		}
		calculateSecretFormula();

		function resizeLists() {
			calculateSecretFormula();
			jQuery('.container').width(secretFormula);
			cropTitles();
		}

		function cropTitles() {
			jQuery('.croppedname').remove();
			jQuery('.sortable>.module>div>.name').each(function() {
				var availableWidth = jQuery(this).parents('li').width() - parseInt(jQuery(this).parents('li').css('paddingRight')) - parseInt(jQuery(this).parents('li').css('paddingRight')) - jQuery(this).siblings('a.optionslink').width() - 10;
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
							var crank = jQuery(this).text();
							var life = '';
							
							// Resize name to fit
							while (life != 42) {
								crank = crank.substring(0, crank.length-1);
								jQuery(this).html(crank+'&hellip;');

								// Are we done yet?
								if (jQuery(this).width() < availableWidth) life = 42; 
							} // End While
						}); // End close & prep

				} // End if
			});
		} // End function
		
		jQuery(window).resize(resizeLists);
		jQuery('.container').width(secretFormula);
		cropTitles();

		

		// Options UI
		function openOptions(module) {
			var moduleID = '#' + module;

			var originalPosition = jQuery(moduleID).offset({ margin:false, border:false });
			var originalWidth = jQuery(moduleID).width()-8;
			var originalHeight = jQuery(moduleID).height();
			var optionsWidth = 400;
			var optionsHeight = 350;
			var optionsX = (jQuery(window).width()) / 2 - ((optionsWidth)/2);
			var optionsY = (jQuery(window).height()) / 2 - (optionsHeight/2);
			var originalName = jQuery(moduleID).children('.name').text();
			curOptModule = jQuery(moduleID).attr('id');
			curOptSidebar = jQuery(moduleID).parent().attr('id');

			// Dim screen
			jQuery('#overlay').css({ zIndex: '500' }).fadeTo('normal', 0.5);

			jQuery('#optionswindow')
				.addClass('optionsspinner')
				.show()
				.css({
					position: 'fixed',
					top: originalPosition.top,
					left: originalPosition.left,
					width: originalWidth,
					height: originalHeight,
					zIndex: '1000',
					opacity: '0'
				})
				.css({ top: optionsY, left: optionsX, width: optionsWidth, height: optionsHeight, opacity: 1 });

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
								jQuery('.checkbox-list > li > input').attr('checked', 'checked')
							} else {
								jQuery('.checkbox-list > li > input').attr('checked', '')
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
			jQuery('#overlay').fadeTo('normal', 0, function() { jQuery(this).css({ zIndex: '-100' }) });
			return false;
		}



		jQuery('#backupsbm').click(function() {
//			jQuery('#backupsbmwindow').slideDown()
			jQuery('#backupform').submit();
			return false;
		})

		jQuery('#restoresbm').click(function() {
			jQuery('#backupsbmwindow').slideToggle()
			return false;
		})




		// Ready overlay
		jQuery('#overlay').fadeTo('normal', 0);

		jQuery('#msg').hide();

		// Remove any new messages on load
/*		function messageHandler() {
			var messageContainer = jQuery('#msg');
			if (jQuery(messageContainer).text() == '') {
				jQuery(messageContainer).hide();
			} else {
				jQuery(messageContainer).fadeOut(10000).text()
			}
		}
		messageHandler();*/
	}
