function LiveSearch(searchprompt) {
	var self = this;

	jQuery('body').addClass('livesearch');

	this.searchPrompt		= searchprompt;
	this.searchForm			= jQuery('#searchform');
	this.searchField		= jQuery('#s');
	this.reset				= jQuery('#searchreset');
	this.loading			= jQuery('#searchload');
	this.searchLabel		= jQuery('#search-label');

	// Hide the submit button
	jQuery('#searchsubmit').addClass('hidden');
	
	// Inject reset and load containers.
	this.searchForm.append("<span id='searchreset'></span><span id='searchload'></span>")

	// Inlinize label
	this.searchLabel.empty().text(searchprompt).addClass('overlabel-apply');
	
	// Bind events to the search input
	this.searchField
		.focus(function(){
			self.searchLabel.addClass('fade');
		})
		.blur(function(){
			if (self.searchField.val() == '') {
				self.searchLabel.show().removeClass('fade');

				if (self.prevSearch != '')
					self.resetSearch(self);
			}
		})
		.keydown(function(event) {
			if (self.searchField.val() == '') {
				self.searchLabel.show();

				// 
				if (self.prevSearch != null)
					self.resetSearch(self);
			}

			var code = event.keyCode;

			if (code == 27) { // Escape
				self.resetSearch(self);

			} else if (code != 13 && code != 9) { // Not Enter or TAB
				self.searchLabel.addClass('hide')

				if (self.timer)
					clearTimeout(self.timer);

				self.timer = setTimeout(function() { self.doSearch(self); }, 1000);
			}
		})
		.keyup(function(event) {
			var code = event.keyCode;

			if (code != 13) { // Not Enter
				if (self.searchField.val() == '') {
					self.resetSearch(self);
					clearTimeout(self.timer);
				} else {
					self.reset.fadeTo('fast', 0);
					self.loading.fadeTo('fast', 1);
				}
			}
		});

	if (this.searchField.val() != '') { // If searchfield isn't empty when page is loaded.
		this.doSearch(self);
		this.searchLabel.addClass('hide');
	}

	self.loading.fadeTo('fast', 0);
	self.reset.fadeTo('fast', 0);
};


LiveSearch.prototype.doSearch = function(self) {
	if (self.searchField.val() == self.prevSearch) // Don't perform the same search twice
		return;

	if (self.prevSearch && (self.searchField.val() != self.prevSearch)) // Not an automated search
		jQuery.bbq.removeState('page');

	if (self.searchField.val() == '') { // A forced search, probably triggered by .ready()
		self.searchField.val(jQuery.deparam.fragment().search);
		self.searchLabel.addClass('hide')
	}

	if (!self.active) {
		self.active = true;
		
		jQuery('body').removeClass('livesearchinactive').addClass('livesearchactive'); // Used to show/hide elements w. CSS.

		// Tell RollingArchives to save the current state
		if ( K2.RollingArchives.saveState ) K2.RollingArchives.saveState();
	}

	self.prevSearch = self.searchField.val();

	// ...and scroll to the top if needed
	if (K2.Animations && self.pageNumber != 1 && jQuery('body').hasClass('smartposition'))
		jQuery('html,body').animate({ scrollTop: jQuery('.primary').offset().top }, 100);

	jQuery.bbq.pushState( 'search=' + self.searchField.val() ); // Update the hash/fragment

	K2.ajaxGet(self.searchform.serialize() + '&k2dynamic=init',
		function(data) {
			jQuery('.content').html(data);

			self.loading.fadeTo('fast', 0);

		}
	);

	self.reset
		.click( function() { self.resetSearch(self); })
		.fadeTo( 'fast', 1.0 )
		.css('cursor', 'pointer')
};

LiveSearch.prototype.resetSearch = function(self) {
	self.reset.unbind('click').fadeTo('fast', 0).css('cursor', 'default');

	delete K2.RollingArchives.query.s;

	self.active = false;
	self.prevSearch = '';
	self.searchField.val('');
	self.searchLabel.removeClass('hide');
	self.loading.fadeTo('fast', 0);

	var pos = jQuery(window).scrollTop(); // get scroll position
	jQuery.bbq.removeState('search');
	jQuery(window).scrollTop(pos); // set scroll position back

	
	// Tell RollingArchives to restore the previous state
	if ( K2.RollingArchives.restoreState ) K2.RollingArchives.restoreState();
};