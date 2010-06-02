function confirmDefaults() {
	if (confirm(defaults_prompt) == true)
		return true;
	else
		return false;
}

jQuery(document).ready(function(){

	/* Make the Ajax Success field less of an eyesore when not in use. TEMP */
	jQuery('#k2ajax')
		.focus(function() {
			jQuery(this).css('height', '100px').animate({ height: '300px', opacity: '1' }, 50).addClass('active')
		})
		.blur(function() {
			jQuery(this).animate({ height: '50px', opacity: '.5' }, 250).removeClass('active')
		})


	jQuery('#k2-styles').sortable({
		items: 'tbody tr',
		update: function(event, ui) {
			jQuery('input.styles-order').each(function(index, element){
				jQuery(this).val(index);
			});
		}
	});

});