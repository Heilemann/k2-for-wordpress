function LiveSearch(searchprompt) {
	var self = this;

	jQuery('#search-form-wrap').addClass('livesearch');

	this.searchPrompt = searchprompt;
	this.searchform = jQuery('#searchform');
	this.searchField = jQuery('#s');

	// Hide the submit button
	jQuery('#searchsubmit').addClass('hidden');

	// Insert reset and loading elements
	this.reset = jQuery('#searchreset');
	this.loading = jQuery('#searchload');
	this.searchLabel = jQuery('#search-label');

	this.searchLabel.empty().text(searchprompt).addClass('overlabel-apply');

	this.loading.removeClass('hidden').hide();
	this.reset.removeClass('hidden').show().fadeTo('fast', 0);

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

				if (self.prevSearch != '')
					self.resetSearch(self);
			}

			var code = event.keyCode;

			if (code == 27) { // Escape
				self.resetSearch(self);
			} else if (code != 13) { // Not Enter
				self.searchLabel.hide()

				if (self.timer)
					clearTimeout(self.timer);
				self.timer = setTimeout(function(){ self.doSearch(self); }, 500);
			}
		})
		.keyup(function(event) {
			var code = event.keyCode;

			if (code != 13) { // Not Enter
				if (self.searchField.val() == '') {
					clearTimeout(self.timer);
					self.resetSearch(self);
				}
			}
		});
};


LiveSearch.prototype.doSearch = function(self) {
	if (self.searchField.val() == self.prevSearch) return;

	self.reset.fadeTo('fast', 0);
	self.loading.fadeIn('fast');

	if (!self.active) {
		self.active = true;

		if (typeof K2.RollingArchives != 'undefined' && K2.RollingArchives.saveState) {
			K2.RollingArchives.saveState();
		}
	}

	self.prevSearch = self.searchField.val();

	K2.ajaxGet(self.searchform.serialize() + '&k2dynamic=init',
		function(data) {
			jQuery('#current-content').hide();
			jQuery('#dynamic-content').html(data).show();

			self.loading.fadeOut('fast');

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
	self.searchLabel.css('text-indent', '0px');
	self.searchLabel.show();

	self.reset.unbind('click').fadeTo('fast', 0).css('cursor', 'default');

	if ( jQuery('#current-content').length ) {
		jQuery('#dynamic-content').hide().html('');
		jQuery('#current-content').show();
	}

	if (typeof K2.RollingArchives != 'undefined' && K2.RollingArchives.restoreState) {
		K2.RollingArchives.restoreState();
	}
};
