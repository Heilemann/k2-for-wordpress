function LiveSearch(searchprompt) {
	var self = this;

	jQuery('#search-form-wrap').addClass('livesearch');

	this.searchPrompt = searchprompt;
	this.input = jQuery('input#s');

	// Hide the submit button
	jQuery('#searchsubmit').addClass('hidden');

	// Insert reset and loading elements
	this.reset = jQuery('#searchreset');
	this.loading = jQuery('#searchload');
	this.searchLabel = jQuery('#search-label');

	this.searchLabel.empty().text(searchprompt).addClass('overlabel-apply');

	this.loading.removeClass('hidden').hide();
	this.reset.removeClass('hidden').show().fadeTo('fast', 0.3);

	// Bind events to the search input
	this.input
		.focus(function(){
			self.searchLabel.css('text-indent', '-1000px');
		})
		.blur(function(){
			if (self.input.val() == '') {
				self.searchLabel.css('text-indent', '0px');

				if (self.prevSearch != '') {
					self.resetSearch(self);
				}
			}
		})
		.keyup(function(event) {
			var code = event.keyCode;

			if (self.input.val() == '') {
				return false;
			} else if (code == 27) {
				self.input.val('');
			} else if (code != 13) {
				if (self.timer) {
					clearTimeout(self.timer);
				}
				self.timer = setTimeout(function(){ self.doSearch(self); }, 500);
			}
		});
};

LiveSearch.prototype.doSearch = function(self) {
	if (self.input.val() == self.prevSearch) return;

	self.reset.fadeTo('fast', 0.3);
	self.loading.fadeIn('fast');

	if (!self.active) {
		self.active = true;

		if (typeof K2.RollingArchives != 'undefined' && K2.RollingArchives.saveState) {
			K2.RollingArchives.saveState();
		}
	}

	self.prevSearch = self.input.val();

	K2.ajaxGet(self.input.serialize() + '&k2dynamic=init',
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

	self.input.val('');
	self.searchLabel.css('text-indent', '0px');

	self.reset.unbind('click').fadeTo('fast', 0.3).css('cursor', 'default');

	if ( jQuery('#current-content').length ) {
		jQuery('#dynamic-content').hide().html('');
		jQuery('#current-content').show();
	}

	if (typeof K2.RollingArchives != 'undefined' && K2.RollingArchives.restoreState) {
		K2.RollingArchives.restoreState();
	}
};
