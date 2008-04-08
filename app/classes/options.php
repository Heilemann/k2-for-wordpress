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
		add_option('k2style', '', 'Choose the Style you want K2 to use');
		add_option('k2livecommenting', '1', "If you don't trust JavaScript, you can turn off Live Commenting. Otherwise it is suggested you leave it on");
		add_option('k2styleinfo', '', 'Metadata of current style.');
		add_option('k2rollingarchives', '1', "If you don't trust JavaScript and Ajax, you can turn off Rolling Archives. Otherwise it is suggested you leave it on");
		add_option('k2blogornoblog', 'Blog', 'The text on the first tab in the header navigation.');
		add_option('k2columns', '2', 'Number of columns to display.');
		add_option('k2dynamiccolumns', '1', 'Enable this to dynamically change the number of columns.');
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
	}


	/**
	 * Initialization
	 */
	
	function init() {
		if ( is_admin() ) {
			add_action('admin_menu', array('K2Options', 'add_menu'));

			// Check for K2 uninstallation. Do here to avoid header output.
			if ( isset($_GET['page']) and ('k2-options' == $_GET['page']) and isset($_POST['uninstall']) )
				K2::uninstall();

			// Check for Form submit
			if ( isset($_GET['page']) and ('k2-options' == $_GET['page']) and isset($_REQUEST['action']) and ('save' == $_REQUEST['action']) and isset($_POST['k2']) )
				K2Options::update();
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

		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/options.css.php" />

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
		check_admin_referer('k2options');

		// Sidebar Manager
		if ( isset($_POST['k2']['sidebarmanager']) ) {
			update_option('k2sidebarmanager', '1');
			K2::install_sbm_loader();
		} else {
			update_option('k2sidebarmanager', '0');
			K2::remove_sbm_loader();
		}

		// Columns
		if ( isset($_POST['k2']['columns']) ) {
			update_option('k2columns', (int) $_POST['k2']['columns']);
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
		if ( isset($_POST['k2']['header_picture']) ) {
			// Update Custom Image Header
			if ( 'random' == $_POST['k2']['header_picture'] ) {
				set_theme_mod('header_image', 'random');
			} elseif ( '' == $_POST['k2']['header_picture'] ) {
				remove_theme_mod('header_image');
			} else {
				set_theme_mod('header_image', str_replace(ABSPATH, get_option('siteurl') . '/', $_POST['k2']['header_picture']));
			}
		}

		if ( isset($_POST['k2']['blogornoblog']) ) {
			
		}

		// K2 Hook
		do_action('k2_update_options');

		wp_redirect('themes.php?page=k2-options&updated=true');
		exit;
	}
}


add_action('k2_install', array('K2Options', 'install'));
add_action('k2_uninstall', array('K2Options', 'uninstall'));
add_action('k2_init', array('K2Options', 'init'), 1);

?>
