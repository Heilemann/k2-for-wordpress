<?php
	require("../../../../wp-blog-header.php");

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

function rollArchive(direction, parms) {
	if (direction == 1 || direction == -1) {
		pagenumber += direction;
		new Effect.Appear('rollload', {duration: .1});
	} else if (direction == 'home') {
		pagenumber = 1;
	}

	checkRollingElements();
	new Ajax.Updater({success: 'content'}, '<?php bloginfo('template_url'); ?>/theloop.php', {method: 'get', parameters: '&s='+parms+'&paged='+pagenumber, onSuccess: rollSuccess, onFailure: rollError});
}


function rollGotoPage(gotopage, parms) {
	pagenumber = (gotopage - 1);
	rollArchive(1, parms);
}


function rollSuccess() {
	rollRemoveLoad();
	/*if (pagenumber > 1) {								// If we've moved into the archives, 
		setCookie('rollpage', pagenumber);				// set a cookie so we can return to that page.
	} else if (pagenumber = 1) {
		deleteCookie('rollpage');
	}*/
}


function rollError() {
	$('rollnotices').innerHTML = 'Error! <a href="javascript:initRollingArchives()">Reboot</a>';
}


function rollRemoveLoad() {
	new Effect.Fade('rollload', {duration: .1});
}


// Needs to be run when a direction is picked, but not when you click the link the notice provides. FIX IT
function rollRemoveNotices() { 
	new Effect.Fade($('rollnotices'));
	$('rollnotices').innerHTML = null;
}


function checkRollingElements() {
	if (pagenumber == 1) {
		$('rollprevious').className = null;
		$('rollprevious').onclick = function() { PageSlider.setValueBy(1); return false; };
		$('rollnext').className = 'inactive';
		$('rollnext').onclick = null;
		$('rollhome').className = 'inactive';
		$('rollhome').onclick = null;
	} else if (pagenumber > 1) {
		$('rollnext').className = null;
		$('rollnext').onclick = function() { PageSlider.setValueBy(-1); return false; };
		$('rollhome').className = null;
		$('rollhome').onclick = function() { rollArchive('home'); };
	}
	
	if (pagenumber >= pagecount) {
		$('rollprevious').className = 'inactive';
		$('rollprevious').onclick = null;
	} else {
		$('rollprevious').className = null;
		$('rollprevious').onclick = function() { PageSlider.setValueBy(1); return false; };
	}

	//alert('Pagecount: '+pagecount);
	if (pagecount != 0) {
		$('rollpages').innerHTML = 'Page '+pagenumber+' of '+pagecount;  // Insert page count
	} else {
		$('rollpages').innerHTML = 'No Pages';  // Insert page count
	}
}

function disableRollingArchives() {
	$('rollprevious').className = 'inactive';
	$('rollnext').className = 'inactive';
	PageSlider.setDisabled();
	Effect.Fade('pagetrack', { duration: .1, from: 1.0, to: 0.3 });
}

function initRollingArchives(currentpage, pages) {
	pagenumber = currentpage;
	pagecount = pages;

 	checkRollingElements(pagenumber);

	rollRemoveLoad();

	$('rollnotices').style.display = 'none';

	/*if (getCookie('rollpage') != null) {
		$('rollnotices').innerHTML = 'This session you were last seen on <a href="javascript:rollGotoPage('+getCookie('rollpage')+');">page '+getCookie('rollpage')+'</a>. <img src="<?php bloginfo('template_url'); ?>/images/transparent.gif" alt="Reset" onclick="Effect.Fade($(\'rollnotices\')); deleteCookie(\'rollpage\');" />';
		new Effect.Highlight('rollnotices');
	} else {
		$('rollnotices').style.display = 'none';
	}*/

}


// Initialize the Rolling Archives
//Event.observe(window, 'load', initRollingArchives, false);
