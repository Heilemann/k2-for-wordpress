<?php
	// check to see if the user has enabled gzip compression in the WordPress admin panel
	if ( ob_get_length() === FALSE and !ini_get('zlib.output_compression') and ini_get('output_handler') != 'ob_gzhandler' and ini_get('output_handler') != 'mb_output_handler' ) {
		ob_start('ob_gzhandler');
	}

	// The headers below tell the browser to cache the file and also tell the browser it is JavaScript.
	header("Cache-Control: public");
	header("Pragma: cache");

	$offset = 60*60*24*60;
	$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT";
	$LmStr = "Last-Modified: ".gmdate("D, d M Y H:i:s",filemtime(__FILE__))." GMT";

	header($ExpStr);
	header($LmStr);
	header('Content-Type: text/javascript; charset: UTF-8');
?>

jQuery(document).ready(function() {
	if ( jQuery('#commentform').length ) {
		jQuery('#commentform').submit(function(){
			jQuery.ajax({
				url: K2.ajaxCommentsURL,
				data: jQuery('#commentform').serialize(),
				type: 'POST',

				beforeSend: function() {
					jQuery('#commenterror').hide();
					jQuery('#commentload').show();
				},

				error: function(request) {
					jQuery('#commentload').hide();

					jQuery('#commenterror').show().html(request.responseText);
				},

				success: function(data) {
			        jQuery('input,select,textarea', '#commentform').each(function(){
				        var t = this.type, tag = this.tagName.toLowerCase();
				        if (t == 'text' || t == 'password' || tag == 'textarea')
				            this.value = '';
				        else if (t == 'checkbox' || t == 'radio')
				            this.checked = false;
				        else if (tag == 'select')
				            this.selectedIndex = -1;
					});

					jQuery('#commenterror').hide().html();

					if ( jQuery('#leavecomment').length ) {
						jQuery('#leavecomment').remove();
					}

					jQuery('#comments').html(parseInt(jQuery('#comments').html()) + 1);

					if ( !jQuery('#commentlist').length ) {
						jQuery('#pinglist').before('<ol id="commentlist"></ol>');
					}

					jQuery('#commentlist').append(data);
					jQuery('#commentform :input').attr('disabled', true);
					jQuery('#commentformbox').slideUp();

					jQuery('#commentload').hide();

					setTimeout(function() {
						jQuery('#commentform :input').removeAttr('disabled');
						jQuery('#commentformbox').slideDown();
					}, 15000);
				}
			});

			return false;
		});
	}
});