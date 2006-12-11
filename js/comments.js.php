<?php
	require(dirname(__FILE__)."/../../../../wp-blog-header.php");

	// check to see if the user has enabled gzip compression in the WordPress admin panel
	if ( !get_settings('gzipcompression') and !ini_get('zlib.output_compression') and ini_get('output_handler') != 'ob_gzhandler' and ini_get('output_handler') != 'mb_output_handler' ) {
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

function AjaxComment(form) {
	var url = '<?php bloginfo("template_url"); ?>/comments-ajax.php';
	if (!$('commentlist')) { new Insertion.Before('pinglist', '<ol id="commentlist"></ol>'); };
	new Ajax.Updater( {
		success: 'commentlist',
		failure: 'error'
	}, url, {
		asynchronous: true,
		evalScripts: true,
		insertion: Insertion.Bottom,
		onLoading: function() { 
			$('commentload').show();
			$('error').update('');
			$('error').setStyle( { visibility: 'hidden' } );
			Form.disable('commentform');
		},
		onComplete: function(request) {
 			if (request.status == 200) {				
				if ($('leavecomment')) { $('leavecomment').remove(); }
				new Effect.Appear($('commentlist').lastChild, { duration: 1.0, afterFinish: function() { new Effect.ScrollTo($('commentlist').lastChild); } } );
				$('comments').innerHTML = parseInt($('comments').innerHTML) + 1;
				Field.clear('comment');
				Form.disable('commentform');
				setTimeout('Form.enable("commentform")',15000);
			}
  			Element.hide('commentload');
		},
		onFailure: function() {
			$('error').show();
			$('error').setStyle( { visibility: 'visible' } );
			Form.enable('commentform');
		},
		parameters: Form.serialize(form) 
		}
	);
}

function initComment() {
	if ( document.getElementById('commentform') ) {
		$('commentform').onsubmit = function() { AjaxComment(this); return false; };
		new Insertion.After('comment', '<span id="error"></span>');
		new Insertion.After('submit','<img src="<?php bloginfo("template_url"); ?>/images/spinner.gif" id="commentload" />');
		$('commentload').hide();
		$('error').hide();
	}
}

//Event.observe(window, 'load', initComment, false);
FastInit.addOnLoad(initComment);
