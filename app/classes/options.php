<?php
// Prevent users from directly loading this class file
defined( 'K2_CURRENT' ) or die ( 'Error: This file can not be loaded directly.' );

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
		add_option('k2rollingarchives', '1', "If you don't trust JavaScript and Ajax, you can turn off Rolling Archives. Otherwise it is suggested you leave it on");
		add_option('k2archives', '0', 'Set whether K2 has a Live Archive page');
		add_option('k2sidebarmanager', '1', 'Set whether to use K2 Sidebar Manager');
		add_option('k2styleinfo', '', 'Metadata of current style.');
		add_option('k2blogornoblog', 'Blog', 'The text on the first tab in the header navigation.');
		add_option('k2columns', '2', 'Number of columns to display.');

		// Added 1.0-RC6
		add_option('k2style', '', 'Choose the Style you want K2 to use');
		add_option('k2dynamiccolumns', '1', 'Enable this to dynamically change the number of columns.');
		add_option('k2headerimage', '', 'Current Header Image');

		K2Options::setup_widgets();
	}


	/**
	 * Deletes options from database
	 */
	
	function uninstall() {
		delete_option('k2asidescategory');
		delete_option('k2livesearch');
		delete_option('k2rollingarchives');
		delete_option('k2archives');
		delete_option('k2sidebarmanager');
		delete_option('k2style');
		delete_option('k2styleinfo');
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
			if ( '1' == get_option('k2imagerandomfeature') ) {
				update_option('k2headerimage', 'random');
			} else {
				$image = get_option('k2header_picture');
				if ( $image != '') {
					if ( is_readable(K2_HEADERS_DIR . "/$image") ) {
						update_option( 'k2headerimage', $image );
					}
				}
			}

			// Styles
			$style = get_option('k2scheme');
			if ( $style != '' ) {
				if ( is_readable(K2_STYLES_DIR . "/$style") ) {
					update_option( 'k2style', $style );
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

		if ( version_compare( $previous, '1.0-RC7.1', '<' ) ) {
			K2Options::setup_widgets();

			update_option( 'k2headerimage', str_replace(K2_HEADERS_DIR, '', get_option('k2headerimage')) );
			update_option( 'k2style', str_replace(K2_STYLES_DIR, '', get_option('k2style')) );
			update_style_info();
		}
	}

	/**
	* Restore K2 to defaults
	*/

	function restore_defaults() {
		K2Options::uninstall();
		K2Options::install();
	}

	function setup_widgets() {
		$sidebars_widgets = wp_get_sidebars_widgets();

		if ( empty($sidebars_widgets) )
			K2Options::restore_sbm_defaults();
	}


	/**
	* Restore K2 to defaults
	*/

	function restore_sbm_defaults() {
		$sidebar_widgets = array(
			'sidebar-1' => array( 'search', 'k2-about', 'recent-posts', 'recent-comments' ),
			'sidebar-2' => array( 'archives', 'links' )
		);

		wp_set_sidebars_widgets($sidebar_widgets);
	}


	/**
	 * Initialization
	 */
	
	function init() {
		if ( is_admin() ) {
			add_action('admin_menu', array('K2Options', 'add_menu'));

			// Inside K2 Options page
			if ( isset($_GET['page']) and ('k2-options' == $_GET['page']) ) {

				// Setup ELA
				if ( isset($_REQUEST['configela']) ) {
					check_admin_referer('k2options');
					K2Archive::setup_archive();
				}

				// Reset and Deactivate K2
				if ( isset($_REQUEST['restore-defaults']) ) {
					check_admin_referer('k2options');
					K2Options::restore_defaults();

 				// Save Settings
				} elseif ( isset($_REQUEST['save']) and isset($_REQUEST['k2']) ) {
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
		<script type="text/javascript">
		function confirmDefaults() {
			if (confirm("<?php _e('Do you want to restore K2 to default settings? This will remove all your K2 settings.', 'k2_domain'); ?>") == true) {
				return true;
			} else {
				return false;
			}
		}
		</script>
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
		// Sidebar Manager
		if ( isset($_POST['k2']['sidebarmanager']) ) {
			update_option('k2sidebarmanager', '1');
		} else {
			update_option('k2sidebarmanager', '0');
		}

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
				set_theme_mod('header_image', K2Header::get_header_image_url() );
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
