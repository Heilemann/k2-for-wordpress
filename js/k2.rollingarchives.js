function RollingArchives(content, pagetext, older, newer, loading, trim, untrim) {
	this.pageText			= pagetext; // 'X of Y' for pagecounts.
	this.active				= false;

	// Insert the Rolling Archives UI
	jQuery(content).before('\
		<div id="rollingarchivesbg"></div>\
		<div id="rollingarchives">\
			<div id="rollnavigation">\
				<div id="pagetrackwrap"><div id="pagetrack"><div id="pagehandle"><div id="rollhover"><div id="rolldates"></div></div></div></div></div>\
				\
				<div id="rollpages"></div>\
				\
				<a id="rollprevious" title="' + older + '" href="#"><span>&laquo;</span> '+ older +'</a>\
				<div id="rollload" title="'+ loading +'"><span>'+ loading +'</span></div>\
				<a id="rollnext" title="'+ newer +'" href="#">'+ newer +' <span>&raquo;</span></a>\
				\
				<div id="texttrimmer">\
					<div id="trimmertrim"><span>'+ trim +'</span></div>\
					<div id="trimmeruntrim"><span>'+ untrim +'</span></div>\
				</div>\
			</div> <!-- #rollnavigation -->\
		</div> <!-- #rollingarchives -->\
	')
};							

RollingArchives.prototype.setState = function(pagenumber, pagecount, query, pagedates) {
	var self				= this;
							
	this.pageNumber			= pagenumber;
	this.pageCount 			= pagecount;
	this.query 				= query;
	this.pageDates 			= pagedates;

	if ( !jQuery('body').hasClass('rollingarchives') ) {
		// Add click events
		jQuery('#rollnext').click(function() {
			self.pageSlider.setValueBy(1);
			return false;
		});

		jQuery('#rollprevious').click(function() {
			self.pageSlider.setValueBy(-1);
			return false;
		});

		jQuery('#trimmertrim').click(function() {
			jQuery('body').addClass('trim');
			jQuery(this).hide()
			jQuery('#trimmeruntrim').show()
		})
	
		jQuery('#trimmeruntrim').click(function() {
			jQuery('body').removeClass('trim');
			jQuery(this).hide()
			jQuery('#trimmertrim').show()
		})

		jQuery('body').addClass('rollingarchives')
	}

	if ( this.validatePage(pagenumber) ) {
		jQuery('body').removeClass('hiderollingarchives').addClass('showrollingarchives')

		jQuery('#rollingarchives').show();

		jQuery('#rollload').hide();
		jQuery('#rollhover').hide();

		// Setup the page slider
		this.pageSlider = new K2Slider('#pagehandle', '#pagetrackwrap', {
			minimum: 1,
			maximum: self.pageCount,
			value: self.pageCount - self.pageNumber + 1,
			onSlide: function(value) {
				jQuery('#rollhover').show();
				self.updatePageText( self.pageCount - value + 1);
			},
			onChange: function(value) {
				self.updatePageText( self.pageCount - value + 1);
				self.gotoPage( self.pageCount - value + 1 );
			}
		});

		this.updatePageText( this.pageNumber );

		this.active = true;
	} else {
		jQuery('body').removeClass('showrollingarchives').addClass('hiderollingarchives');
	}
};


RollingArchives.prototype.saveState = function() {
	// this.prevQuery = this.query;
	this.originalContent = jQuery('#content').html();
};


RollingArchives.prototype.restoreState = function() {
	if (this.originalContent != '') {
		jQuery('body').removeClass('livesearchactive').addClass('livesearchinactive'); // Used to show/hide elements w. CSS.

		jQuery('#content').html(this.originalContent)

		jQuery.bbq.removeState('page');
		jQuery.bbq.removeState('search');

		initialRollingArchives();
	}
};


RollingArchives.prototype.updatePageText = function(page) {
	jQuery('#rollpages').html(
		(this.pageText.replace('%1$d', page)).replace('%2$d', this.pageCount)
	);
	jQuery('#rolldates').html(this.pageDates[page - 1]);
};


RollingArchives.prototype.validatePage = function(newpage) {
	if (this.pageCount > 1) {

		if (newpage >= this.pageCount) {
			jQuery('body').removeClass('onepageonly firstpage nthpage').addClass('lastpage');
			return this.pageCount;

		} else if (newpage <= 1) {
			jQuery('body').removeClass('onepageonly nthpage lastpage').addClass('firstpage');
			return 1;

		} else {
			jQuery('body').removeClass('onepageonly firstpage lastpage').addClass('nthpage');
			return newpage;
		}
	}

	jQuery('body').removeClass('firstpage nthpage lastpage').addClass('onepageonly');

	return 0;
};

RollingArchives.prototype.loading = function(gostop) {
	if (gostop == 'start') {
		jQuery('body')
			.addClass('rollload')
	} else {
		jQuery('body')
			.removeClass('rollload')
	}
};

RollingArchives.prototype.gotoPage = function(newpage) {
	var self = this;
	var page = this.validatePage(newpage);

	if ( (page != this.pageNumber) ) {
		this.pageNumber = page;

		jQuery.bbq.pushState( 'page='+page ); // Update the hash/fragment

		self.loading('start'); // Show the loading spinner

		scrollToContent(); // Scroll if needed

		jQuery.extend(this.query, { paged: this.pageNumber, k2dynamic: 1 });

		K2.ajaxGet(this.query,
			function(data) {

				jQuery('#rollhover').fadeOut('slow');
				self.loading('stop');
				jQuery('#content').html(data);
			}
		);
	}

	if (page == 1) { /* Reset trimmer setting */
		jQuery('body').removeClass('trim');
		jQuery('#trimmeruntrim').hide()
		jQuery('#trimmertrim').show()
	}
};
