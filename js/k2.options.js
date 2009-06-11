function confirmDefaults() {
	if (confirm(defaults_prompt) == true) {
		return true;
	} else {
		return false;
	}
}

jQuery(document).ready(function(){
	jQuery('.advanced-option').addClass('hidden');

	jQuery('#advanced-btn').toggle(
		function () {
			jQuery(this).val('Hide Advanced Options');
			jQuery('.advanced-option').removeClass('hidden');
		},
		function () {
			jQuery(this).val('Show Advanced Options');
			jQuery('.advanced-option').addClass('hidden');
		}
	);

	jQuery('.postbox h3, .postbox .handlediv').click( function() {
		var p = jQuery(this).parent('.postbox');
		p.toggleClass('closed');
	} );

	jQuery('#k2-styles').sortable({
		items: 'tbody tr'
	});
});