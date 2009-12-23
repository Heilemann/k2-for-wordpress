function confirmDefaults() {
	if (confirm(defaults_prompt) == true) {
		return true;
	} else {
		return false;
	}
}

jQuery(document).ready(function(){

	jQuery('#k2-styles').sortable({
		items: 'tbody tr'
	});

});