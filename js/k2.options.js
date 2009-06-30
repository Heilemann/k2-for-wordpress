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

	jQuery('#k2-styles').sortable({
		items: 'tbody tr'
	});

/*
	jQuery('#k2-options').submit(function(){
		if (jQuery('#k2_ajax_complete_cp').length)
			jQuery('#k2_ajax_complete_cp').val(textareaid.getCode()).removeAttr('disabled');
	});
*/
});