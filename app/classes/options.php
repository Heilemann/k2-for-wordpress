<?php

/**
 * K2 Options
 *
 * @package k2options
 */

class K2Options {

	/**
	 * Adds default options to database
	 */
	
	function install() {
		add_option('k2asidescategory', '0', 'A category which will be treated differently from other categories');
		add_option('k2livesearch', '1', "If you don't trust JavaScript and Ajax, you can turn off LiveSearch. Otherwise I suggest you leave it on"); // (live & classic)
		add_option('k2archives', '0', 'Set whether K2 has a Live Archive page');
		add_option('k2sidebarmanager', '0', 'Set whether to use K2 Sidebar Manager');
		add_option('k2livecommenting', '1', "If you don't trust JavaScript, you can turn off Live Commenting. Otherwise it is suggested you leave it on");
		add_option('k2styleinfo', '', 'Metadata of current style.');
		add_option('k2rollingarchives', '1', "If you don't trust JavaScript and Ajax, you can turn off Rolling Archives. Otherwise it is suggested you leave it on");
		add_option('k2blogornoblog', 'Blog', 'The text on the first tab in the header navigation.');
		add_option('k2columns', '2', 'Number of columns to display.');

		// Added 1.0-RC6
		add_option('k2style', '', 'Choose the Style you want K2 to use');
		add_option('k2dynamiccolumns', '1', 'Enable this to dynamically change the number of columns.');
		add_option('k2headerimage', '', 'Current Header Image');
	}


	/**
	 * Deletes options from database
	 */
	
	function uninstall() {
		delete_option('k2asidescategory');
		delete_option('k2livesearch');
		delete_option('k2archives');
		delete_option('k2sidebarmanager');
		delete_option('k2style');
		delete_option('k2livecommenting');
		delete_option('k2styleinfo');
		delete_option('k2rollingarchives');
		delete_option('k2blogornoblog');
		delete_option('k2columns');
		delete_option('k2dynamiccolumns');
		delete_option('k2headerimage');
	}


	/**
	 * Handles upgrades from previous versions
	 */

	function upgrade($previous) {
		if ( version_compare( $previous, '1.0-RC5', '<' ) ) {
			// Add new options
			add_option('k2style', '', 'Choose the Style you want K2 to use');
			add_option('k2dynamiccolumns', '1', 'Enable this to dynamically change the number of columns.');
			add_option('k2headerimage', '', 'Current Header Image');

			// Convert existing options

			// Header Images
			$image = get_option('k2header_picture');
			if ( $image != '') {
				if ( is_readable(K2_HEADERS_PATH . $image) ) {
					update_option( 'k2headerimage', str_replace(ABSPATH, '', K2_HEADERS_PATH . $style) );
				}
			}

			if ( '1' == get_option('k2imagerandomfeature') ) {
				update_option('k2headerimage', 'random');
			}

			// Styles
			$style = get_option('k2scheme');
			if ( $style != '' ) {
				if ( is_readable(K2_STYLES_PATH . $style) ) {
					update_option( 'k2style', str_replace(ABSPATH, '', K2_STYLES_PATH . $style) );
					update_style_info();
				}
			}

			// Delete depreciated options
			delete_option('k2advnav');
			delete_option('k2header_picture');
			delete_option('k2imagerandomfeature');
			delete_option('k2lastmodified');
			delete_option('k2scheme');
		}
	}

	/**
	 * Initialization
	 */
	
	function init() {
		if ( is_admin() ) {
			add_action('admin_menu', array('K2Options', 'add_menu'));

			// Inside K2 Options page
			if ( isset($_GET['page']) and ('k2-options' == $_GET['page']) ) {

				// Reset and Deactivate K2
				if ( isset($_REQUEST['uninstall']) ) {
					check_admin_referer('k2options');
					K2::uninstall();
				}

				// Setup ELA
				if ( isset($_REQUEST['configela']) ) {
					check_admin_referer('k2options');
					K2Archive::setup_archive();
				}

				// Save Settings
				if ( isset($_REQUEST['save']) and isset($_REQUEST['k2']) ) {
					check_admin_referer('k2options');
					K2Options::update();
				}
			}
		}
	}

	
	/**
	 * Adds K2 Options to Presentation menu, adds actions for head and scripts
	 */

	function add_menu() {
		$page = add_theme_page(__('K2 Options','k2_domain'), __('K2 Options','k2_domain'), 'edit_themes', 'k2-options', array('K2Options', 'admin'));

		add_action("admin_head-$page", array('K2Options', 'admin_head'));
		add_action("admin_print_scripts-$page", array('K2Options', 'admin_print_scripts'));
	}


	/**
	 * Displays K2 Options page
	 */
	
	function admin() {
		include(TEMPLATEPATH . '/app/display/options.php');
	}


	/**
	 * Displays content in HEAD tag. Called by action: admin_head
	 */
	
	function admin_head() { ?>

		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/options.css" />

<?php }


	/**
	 * Enqueues scripts. Called by action: admin_print_scripts
	 */
	
	function admin_print_scripts() {
		// Add our script to the queue
		wp_enqueue_script('k2functions');
	}


	/**
	 * Updates options
	 */
	
	function update() {
		/*
		// Sidebar Manager
		if ( isset($_POST['k2']['sidebarmanager']) ) {
			update_option('k2sidebarmanager', '1');
		} else {
			update_option('k2sidebarmanager', '0');
		}
		*/

		// Columns
		if ( isset($_POST['k2']['columns']) ) {
			update_option('k2columns', $_POST['k2']['columns']);
		}

		// Advanced Navigation
		if ( isset($_POST['k2']['advnav']) ) {
			update_option('k2livesearch', '1');
			update_option('k2rollingarchives', '1');
		} else {
			update_option('k2livesearch', '0');
			update_option('k2rollingarchives', '0');
		}

		// Archives Page (thanks to Michael Hampton, http://www.ioerror.us/ for the assist)
		if ( isset($_POST['k2']['archives']) ) {
			update_option('k2archives', '1');
			K2Archive::create_archive();
		} else {
			update_option('k2archives', '0');
			K2Archive::delete_archive();
		}

		// Live Commenting
		if ( isset($_POST['k2']['livecommenting']) ) {
			update_option('k2livecommenting', '1');
		} else {
			update_option('k2livecommenting', '0');
		}

		// Asides
		if ( isset($_POST['k2']['asidescategory']) ) {
			update_option('k2asidescategory', (int) $_POST['k2']['asidescategory']);
		}

		// Style
		if ( isset($_POST['k2']['style']) ) {
			update_option('k2style', $_POST['k2']['style']);
			update_style_info();
		} else {
			update_option('k2style', '');
			update_option('k2styleinfo', array());
		}

		// Header Image
		if ( isset($_POST['k2']['headerimage']) ) {
			update_option('k2headerimage', $_POST['k2']['headerimage']);

			// Update Custom Image Header
			if ( ('' == $_POST['k2']['headerimage']) or ('random' == $_POST['k2']['headerimage']) ) {
				remove_theme_mod('header_image');
			} else {
				set_theme_mod('header_image', get_option('siteurl') . '/' . $_POST['k2']['headerimage']);
			}
		}

		// Blog tab
		if ( isset($_POST['k2']['blogornoblog']) ) {
			update_option( 'k2blogornoblog', strip_tags( stripslashes($_POST['k2']['blogornoblog']) ) );
		}

		// K2 Hook
		do_action('k2_update_options');

		wp_redirect('themes.php?page=k2-options&updated=true');
		exit;
	}
}


add_action('k2_install', array('K2Options', 'install'));
add_action('k2_upgrade', array('K2Options', 'upgrade'));
add_action('k2_uninstall', array('K2Options', 'uninstall'));
add_action('k2_init', array('K2Options', 'init'), 1);
