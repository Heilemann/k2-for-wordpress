/*
	HUMANIZED UNDO 1.0
	idea - http://humanized.com/weblog/2007/09/21/undo-made-easy-with-ajax-part-15/
*/

var humanUndo = {
	setup: function(url) {
		// Where to send the trash?
		this.ajaxurl = url;

		// Init the list of trash
		this.trashList = [];
	},

	emptyTrash: function() {
		for (var i=0; i < humanUndo.trashList.length; i++) {

			var moduleID = humanUndo.trashList[i]
			var sidebarID = jQuery('#'+moduleID).parent().attr('id')
			
			// SJAX... I guess.
			jQuery.ajax({
				type: 	'POST',
				url: 	humanUndo.ajaxurl,
				data:	'action=k2sbm&sbm_action=remove&module_id='+moduleID+'&sidebar_id='+sidebarID,
				async:	false
			})

		}

		// Empty the queue
		humanUndo.trashList = []
	},
	
	addTrash: function(element) {
		// Add element to undo list
		humanUndo.trashList.push(element)
		
		this.updateUndoLink();
	},
	
	updateUndoLink: function () {
		// Show and update the #undo link if necessary, else hide it.
		if (humanUndo.trashList.length == 0) {
			setTimeout( 'jQuery("#undo").fadeOut();', 300 )

		} else if (humanUndo.trashList.length == 1) {
			jQuery("#levels").text( "" );

			if( jQuery("#undo").css("display") == "none" )
				setTimeout( 'jQuery("#undo").fadeIn()', 300 );

		} else if (humanUndo.trashList.length > 1) {
			jQuery("#levels").text( this.trashList.length );

		}
	}
}