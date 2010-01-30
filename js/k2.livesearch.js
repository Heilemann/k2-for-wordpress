function LiveSearch(searchprompt) {
	var self = this;

	jQuery('body').addClass('livesearch');

	this.searchPrompt		= searchprompt;
	this.searchform			= jQuery('#searchform');
	this.searchField		= jQuery('#s');
	this.reset				= jQuery('#searchreset');
	this.loading			= jQuery('#searchload');
	this.searchLabel		= jQuery('#search-label');

	// Hide the submit button
	jQuery('#searchsubmit').addClass('hidden');

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

				if (self.prevSearch != '') {
					self.resetSearch(self);
				}
			}
		})
		.keydown(function(event) {
			if (self.searchField.val() == '') {
				self.searchLabel.show();

				if (self.prevSearch != '') {
					self.resetSearch(self);
				}
			}

			var code = event.keyCode;

			if (code == 27) { // Escape
				self.resetSearch(self);

			} else if (code != 13 && code != 9) { // Not Enter or TAB
				self.searchLabel.addClass('hide')

				if (self.timer) {
					clearTimeout(self.timer);
				}
				self.timer = setTimeout(function(){ self.doSearch(self); }, 500);
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
	if (self.searchField.val() == self.prevSearch) return; // Don't do the same search again.

	if (!self.active) {
		self.active = true;
		
		jQuery('body').removeClass('livesearchinactive').addClass('livesearchactive'); // Used to show/hide elements w. CSS.
	}

	self.prevSearch = self.searchField.val();

	K2.ajaxGet(self.searchform.serialize() + '&k2dynamic=init',
		function(data) {
			jQuery('#content').html(data);

			self.loading.fadeTo('fast', 0);

			self.reset.click(function(){
				self.resetSearch(self);
			}).fadeTo('fast', 1.0).css('cursor', 'pointer');
		}
	);
};

LiveSearch.prototype.resetSearch = function(self) {
	self.active = false;
	self.prevSearch = '';
	self.searchField.val('');
	self.searchLabel.removeClass('hide');
	self.loading.fadeTo('fast', 0);

	self.reset.unbind('click').fadeTo('fast', 0).css('cursor', 'default');

	if (jQuery('body').hasClass('rollingarchives') && K2.RollingArchives.restoreState) {
		K2.RollingArchives.restoreState();
	}
};