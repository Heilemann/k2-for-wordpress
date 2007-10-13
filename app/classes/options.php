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
		add_option('k2sidebarmanager', '1', 'Set whether to use K2 Sidebar Manager');
		add_option('k2scheme', '', 'Choose the Style you want K2 to use');
		add_option('k2livecommenting', '1', "If you don't trust JavaScript, you can turn off Live Commenting. Otherwise it is suggested you leave it on");
		add_option('k2styleinfo', '', 'Metadata of current style.');
		add_option('k2rollingarchives', '1', "If you don't trust JavaScript and Ajax, you can turn off Rolling Archives. Otherwise it is suggested you leave it on");
		add_option('k2blogornoblog', 'Blog', 'The text on the first tab in the header navigation.');
		add_option('k2columns', '2', 'Number of columns to display.');
	}


	/**
	 * Deletes options from database
	 */
	
	function uninstall() {
		delete_option('k2asidescategory');
		delete_option('k2livesearch');
		delete_option('k2archives');
		delete_option('k2sidebarmanager');
		delete_option('k2scheme');
		delete_option('k2livecommenting');
		delete_option('k2styleinfo');
		delete_option('k2rollingarchives');
		delete_option('k2blogornoblog');
		delete_option('k2columns');
	}


	/**
	 * Initialization
	 */
	
	function init() {
		if(is_admin()) {
			add_action('admin_menu', array('K2Options', 'add_menu'));

			// Check for K2 uninstallation. Do here to avoid header output.
			if($_GET['page'] == 'k2-options' and isset($_POST['uninstall'])) {
				K2::uninstall();
			}

			// Need to check if this changes...
			$sidebar_manager = get_option('k2sidebarmanager');

			K2Options::update();
			K2Header::update();

			// Ewww...
			if($sidebar_manager != get_option('k2sidebarmanager')) {
				header('Location: themes.php?page=k2-options&updated');
				exit;
			}
		}
	}

	
	/**
	 * Adds K2 Options to Presentation menu, adds actions for head and scripts
	 *
	 * @return void
	 * @author Steve
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
		if(!empty($_POST)) {
			if(isset($_POST['k2'])) {

				// Advanced Navigation
				if(isset($_POST['k2']['advnav'])) {
					update_option('k2livesearch', '1');
					update_option('k2rollingarchives', '1');
				} else {
					update_option('k2livesearch', '0');
					update_option('k2rollingarchives', '0');
				}

			   // Archives Page
			   if(isset($_POST['k2']['archives'])) {
			   	if( get_option('k2archives') != 'add_archive') {
			   		update_option('k2archives', '1');
			  		K2Archive::create_archive();
			   	}
			   } else {
					// thanks to Michael Hampton, http://www.ioerror.us/ for the assist
					update_option('k2archives', '0');
					K2Archive::delete_archive();
				}

				// Live Commenting
				if(isset($_POST['k2']['livecommenting'])) {
					update_option('k2livecommenting', '1');
				} else {
					update_option('k2livecommenting', '0');
				}

				// K2 Sidebar Manager
				if(isset($_POST['k2']['sidebarmanager'])) {
					update_option('k2sidebarmanager', '1');
				} else {
					update_option('k2sidebarmanager', '0');
				}

				// Set all the options
				foreach($_POST['k2'] as $option => $value) {
					update_option('k2' . $option, $value);
				}
				
				if(isset($_POST['k2']['scheme'])) {
					update_style_info();
				}
			}

			if(isset($_POST['configela']) and !K2Archive::setup_archive()) {
				unset($_POST['configela']);
			}
		}
	}
}


add_action('k2_install', array('K2Options', 'install'));
add_action('k2_uninstall', array('K2Options', 'uninstall'));
add_action('k2_init', array('K2Options', 'init'), 1);

?>
