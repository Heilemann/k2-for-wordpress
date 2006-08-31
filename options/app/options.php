<?php

class options {
	function init() {
		add_action('admin_menu', array('options', 'add_menu'));
	}

	function add_menu() {
		add_submenu_page('themes.php', __('K2 Options','k2_domain'), __('K2 Options','k2_domain'), 5, 'k2-options', array('options', 'admin'));
	}

	function admin() {
		include(TEMPLATEPATH . '/options/display/form.php');
	}

	function update() {
		if(!empty($_POST)) {
			if(isset($_POST['k2'])) {
				// Archives is a special case
				if(isset($_POST['k2']['archives'])) {
					archive::create_archive();
				} else {
					// thanks to Michael Hampton, http://www.ioerror.us/ for the assist
					$_POST['k2']['archives'] = '';
					archive::delete_archive();
				}

				// Set all the options
				foreach($_POST['k2'] as $option => $value) {
					update_option('k2' . $option, $value);
				}

				if(isset($_POST['k2']['scheme'])) {
					k2styleinfo_update();
				}

				if(isset($_POST['k2']['styleinfo_format'])) {
					k2styleinfo_update();
				}
			}

			if(isset($_POST['configela']) and !archive::setup_archive()) {
				unset($_POST['configela']);
			}

			if(isset($_POST['uninstall'])) {
				tools::uninstall();
			}
		}
	}
}

?>
