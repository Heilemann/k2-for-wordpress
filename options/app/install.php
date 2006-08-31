<?php
// Revision number of the options file. Updated by SVN whenever this file is changed.
$options_revision = intval(substr('$Revision$', 11, -2));

class installk2 {
	function installer() {
		global $options_revision, $current;

		// Add / update the version number
		if ( !get_option('k2installed') ) {
			add_option('k2installed', $curent, 'This options simply tells me if K2 has been installed before');
		} else {
			update_option('k2installed', $current);
		}

		// Add / update the options revision number
		if ( !get_option('k2optionsrevision') ) {
			add_option('k2optionsrevision', $options_revision, 'Revision number of K2 options');
		} else {
			update_option('k2optionsrevision', $options_revision);
		}

		add_option('k2aboutblurp', '', 'Allows you to write a small blurp about you and your blog, which will be put on the frontpage');
		add_option('k2asidescategory', '0', 'A category which will be treated differently from other categories');
		add_option('k2asidesposition', '0', 'Whether to use inline or sidebar asides');
		add_option('k2livesearch', '1', "If you don't trust JavaScript and Ajax, you can turn off LiveSearch. Otherwise I suggest you leave it on"); // (live & classic)
		add_option('k2asidesnumber', '3', 'The number of Asides to show in the Sidebar. Default is 3.');
		add_option('k2widthtype', '1', "Determines whether to use flexible or fixed width. Default is fixed."); // (flexible & fixed)
		add_option('k2archives', '', 'Set whether K2 has a Live Archive page');
		add_option('k2scheme', '', 'Choose the Scheme you want K2 to use');
		add_option('k2livecommenting', '1', "If you don't trust JavaScript, you can turn off Live Commenting. Otherwise it is suggested you leave it on");
		add_option('k2styleinfo_format', 'Current style is <a href="%stylelink%" title="%style% by %author%">%style% %version%</a> by <a href="%site%">%author%</a><br />', 'Format for displaying the current selected style info.');
		add_option('k2styleinfo', '', 'Formatted string for style info display.');
		add_option('k2rollingarchives', '1', "If you don't trust JavaScript and Ajax, you can turn off Rolling Archives. Otherwise it is suggested you leave it on");
		add_option('k2blogornoblog', 'Blog', 'The text on the first tab in the header navigation.');

		add_option('k2imagerandomfeature', '1', "Whether to use a random image in K2's header");
		add_option('k2header_picture', '', "The image to use in K2's header");
		add_option('k2headerbackgroundcolor', '', "K2's header background color");
		add_option('k2headertextalignment', 'left', "K2's header text alignment");
		add_option('k2headertextfontsize', '', "K2's header text font size");
		add_option('k2headertextcolor', '', "K2's header text font color");
		add_option('k2headertextcolor_bright', '', "K2's header text bright font colour");
		add_option('k2headertextcolor_dark', '', "K2's header text dark font colour");

		add_option('k2sbm_modules_active', array(), 'The active sidebar modules.');
		add_option('k2sbm_modules_disabled', array(), 'The disabled sidebar modules.');
		add_option('k2sbm_modules_next_id', 1, 'The ID for the next sidebar module.');

		// Add the SBM stub file to the plugins list
		$sbm_stub_path = '../themes/' . get_option('template') . '/options/app/sbm-stub.php';
		$plugins = (array)get_option('active_plugins');
		$found = false;

		// Check to see if the stub plugin is already there
		for($i = 0; !$found && $i < count($plugins); $i++) {
			if($plugins[$i] == $sbm_stub_path) {
				$found = true;
			}
		}

		// If not, we can add it
		if(!$found) {
			$plugins[] = $sbm_stub_path;
			update_option('active_plugins', $plugins);
		}
	}
}
?>
