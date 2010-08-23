<?php
/**
 * K2 main class.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

// Prevent users from directly loading this class file
defined('K2_CURRENT') or die ( __('Error: This file can not be loaded directly.', 'k2') );

class K2 {

	/**
	 * Initializes K2
	 *
	 * @uses do_action() Provides 'k2_init' action
	 */
	function init() {
		global $wp_version;

		// Load required classes and includes
		require_once(TEMPLATEPATH . '/app/classes/archive.php');
		require_once(TEMPLATEPATH . '/app/includes/info.php');
		require_once(TEMPLATEPATH . '/app/includes/display.php');
		require_once(TEMPLATEPATH . '/app/includes/media.php');
		require_once(TEMPLATEPATH . '/app/includes/widgets.php');

		if ( defined('K2_HEADERS') and K2_HEADERS == true )
			require_once(TEMPLATEPATH . '/app/classes/header.php');

		// Check installed version, upgrade if needed
		$k2version = get_option('k2version');

		if ( $k2version === false )
			K2::install();
		elseif ( version_compare($k2version, K2_CURRENT, '<') )
			K2::upgrade($k2version);

		// Register our scripts with script loader
		K2::register_scripts();

		// There may be some things we need to do before K2 is initialised
		// Let's do them now
		do_action('k2_init');

		// Finally load pluggable & deprecated functions
		require_once(TEMPLATEPATH . '/app/includes/pluggable.php');
		include_once(TEMPLATEPATH . '/app/includes/deprecated.php');

		// Register our sidebars with widgets
		k2_register_sidebars();

		if ( function_exists( 'add_theme_support' ) ) {
			// This theme uses post thumbnails
			add_theme_support( 'post-thumbnails' );

			// This theme uses wp_nav_menu()
			add_theme_support( 'nav-menus' );

		}

		// This theme uses wp_nav_menu() in one location.
		if ( function_exists('register_nav_menus') ) {
			register_nav_menus( array(
				'header' => __( 'Header Menu', 'k2' ),
			) );
		}

		// Add default posts and comments RSS feed links to head
		if ( version_compare( $wp_version, '3.0', '>=' ) ) {
			add_theme_support( 'automatic-feed-links' );
		} else {
			automatic_feed_links();
		}

		// This theme allows users to set a custom background
		if ( function_exists('add_custom_background') )
			add_custom_background();
	}


	/**
	 * Starts the installation process
	 *
	 * @uses do_action() Provides 'k2_install' action
	 */
	function install() {
		add_option('k2version', K2_CURRENT);

		add_option( 'k2optimjs', '0');
		add_option( 'k2advnav', '2');
		add_option( 'k2animations', '1' );
		add_option( 'k2usestyle', '3' );
		$defaultjs = "// Lightbox v2.03.3 - Adds new images to lightbox\nif (typeof myLightbox != 'undefined' && myLightbox instanceof Lightbox && myLightbox.updateImageList) {\n\tmyLightbox.updateImageList();\n}\n";
		add_option( 'k2ajaxdonejs', $defaultjs );

		add_option( 'k2archives', '0' );

		add_option( 'k2entrymeta1', __('Published by %author% on %date% in %categories%. %comments% %tags%', 'k2') );
		add_option( 'k2entrymeta2', '' );

		// Call the install handlers
		do_action('k2_install');
	}


	/**
	 * Starts the upgrade process
	 *
	 * @uses do_action() Provides 'k2_upgrade' action
	 * @param string $previous Previous version K2
	 */
	function upgrade($previous) {
		// Install options
		K2::install();

		// Call the upgrade handlers
		do_action('k2_upgrade', $previous);

		// Update the version
		update_option('k2version', K2_CURRENT);

		// Clean-up deprecated options
		delete_option('k2sidebarmanager');
		delete_option('k2sbm_modules');
	}


	/**
	 * Removes K2 options
	 *
	 * @uses do_action() Provides 'k2_uninstall' action
	 */
	function uninstall() {
		// Delete options
		delete_option('k2version');
		delete_option('k2advnav');
		delete_option('k2optimjs');
		delete_option('k2usestyle');
		delete_option('k2archives');
		delete_option('k2entrymeta1');
		delete_option('k2entrymeta2');
		delete_option('k2animations');
		delete_option('k2ajaxdonejs');

		// Call the uninstall handlers
		do_action('k2_uninstall');
	}


	/**
	 * Restores K2 to default settings
	 */
	function restore_defaults() {
		K2::uninstall();
		K2::install();
	}


	/**
	 *
	 */
	function admin_init() {
		// Inside K2 Options page
		if ( isset($_GET['page']) and ('k2-options' == $_GET['page']) and isset($_REQUEST['k2-options-submit']) ) {
			check_admin_referer('k2options');

			// Reset K2
			if ( isset($_REQUEST['restore-defaults']) ) {
				K2::restore_defaults();
				wp_redirect('themes.php?page=k2-options&defaults=true');
				die;

			// Reset Sidebars
			} elseif ( isset($_REQUEST['default-widgets']) ) {
				k2_default_widgets();
				wp_redirect('themes.php?page=k2-options&widgets=true');
				die;

				// Save Settings
			} elseif ( isset($_REQUEST['save']) and isset($_REQUEST['k2']) ) {
				K2::update_options();
				wp_redirect('themes.php?page=k2-options&saved=true');
				die;
			}
		}
	}


	/**
	 * Adds K2 Options to Appearance menu, adds actions for head and scripts
	 */
	function add_options_menu() {
		global $wp_version;

		// WP 3.0 new capability: edit_theme_options
		if ( version_compare( $wp_version, '3.0', '>=' ) ) {
			$page = add_theme_page( __('K2 Options', 'k2'), __('K2 Options', 'k2'), 'edit_theme_options', 'k2-options', array('K2', 'admin') );
		} else {
			$page = add_theme_page( __('K2 Options', 'k2'), __('K2 Options', 'k2'), 'edit_themes', 'k2-options', array('K2', 'admin') );
		}

		add_action( "admin_head-$page", array('K2', 'admin_head') );
		add_action( "admin_print_scripts-$page", array('K2', 'admin_print_scripts') );

		if ( function_exists('add_contextual_help') ) {
			add_contextual_help($page,
				'<a href="http://groups.google.com/group/k2-support/">' .  __('K2 Support Group', 'k2') . '</a><br />' .
				'<a href="http://code.google.com/p/kaytwo/issues/list">' .  __('K2 Bug Tracker', 'k2') . '</a><br />'
				);
		}
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
	function admin_head() {
		?>
		<script type="text/javascript" charset="utf-8">
		//<![CDATA[
			var defaults_prompt = "<?php _e('Do you want to restore K2 to default settings? This will remove all your K2 settings.', 'k2'); ?>";
		//]]>
		</script>
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/options.css" />
	<?php
	}


	/**
	 * Enqueues scripts. Called by action: admin_print_scripts
	 */
	function admin_print_scripts() {
		// Add our script to the queue
		wp_enqueue_script('k2options');
	}

	/**
	 * Updates options
	 *
	 * @uses do_action() Provides 'k2_update_options' action
	 */
	function admin_style_visual_editor($url) {

		if ( !empty($url) )
			$url .= ',';

		// Change the path here if using sub-directory
		$url .= trailingslashit( get_stylesheet_directory_uri() ) . 'css/visualeditor.css';

		return $url;
	}


	/**
	 * Updates options
	 *
	 * @uses do_action() Provides 'k2_update_options' action
	 */
	function update_options() {
		// Debug Mode
		if ( isset($_POST['k2']['debug']) )
			update_option('k2optimjs', '1');
		else
			update_option('k2optimjs', '0');

		// Advanced Navigation
		update_option('k2advnav', $_POST['k2']['advnav']);

		// JavaScript Animations
		if ( isset($_POST['k2']['advnav']) && $_POST['k2']['advnav'] == 2)
			update_option('k2animations', '1');
		else
			update_option('k2animations', '0');

		// How to style sidebars, and ehther to use K2's CSS at all
		update_option('k2usestyle', $_POST['k2']['usestyle']);

		/* print_r($_POST); */

		// Archives Page (thanks to Michael Hampton, http://www.ioerror.us/ for the assist)
		if ( isset($_POST['k2']['archives']) ) {
			update_option('k2archives', '1');
			K2Archive::add();
		} else {
			update_option('k2archives', '0');
			K2Archive::delete();
		}

		// Top post meta
		if ( isset($_POST['k2']['entrymeta1']) )
			update_option( 'k2entrymeta1', stripslashes($_POST['k2']['entrymeta1']) );

		// Bottom post meta
		if ( isset($_POST['k2']['entrymeta2']) )
			update_option( 'k2entrymeta2', stripslashes($_POST['k2']['entrymeta2']) );

		// Ajax Success JavaScript
		if ( isset($_POST['k2']['ajaxdonejs']) )
			update_option( 'k2ajaxdonejs', stripslashes($_POST['k2']['ajaxdonejs']) );

		// K2 Hook
		do_action('k2_update_options');
	}


	/**
	 * Adds k2dynamic into the list of query variables, used for dynamic content
	 */
	function add_custom_query_vars($query_vars) {
		$query_vars[] = 'k2dynamic';

		return $query_vars;
	}


	/**
	 * Filter to prevent redirect_canonical() from redirecting dynamic content
	 */
	function prevent_dynamic_redirect($redirect_url) {
		if ( strpos($redirect_url, 'k2dynamic=' ) !== false )
			return false;

		return $redirect_url;
	}


	/**
	 * Return the home page link, used for dynamic content
	 */
	function get_home_url() {
		if ( ('page' == get_option('show_on_front')) and ($page_id = get_option('page_for_posts')) ) {
			return get_page_link($page_id);
		}

		return get_bloginfo('url') . '/';
	}


	/**
	 * Handles displaying dynamic content such as LiveSearch, RollingArchives
	 *
	 * @uses do_action() Provides 'k2_dynamic_content' action
	 */
	function dynamic_content() {
		$k2dynamic = get_query_var('k2dynamic');

		if ( $k2dynamic ) {
			define('DOING_AJAX', true);

			// Send the header
			header('Content-Type: ' . get_bloginfo('html_type') . '; charset=' . get_bloginfo('charset'));

			// Include the content
			include(TEMPLATEPATH . '/app/display/theloop.php');

			if ( 'init' == $k2dynamic ) {
				$rolling_state = k2_get_rolling_archives_state();
			?>
				<script type="text/javascript">
				// <![CDATA[
					K2.RollingArchives.setState(
						<?php echo $rolling_state['curpage']; ?>,
						<?php echo $rolling_state['maxpage']; ?>,
						<?php echo json_encode( $rolling_state['query'] ); ?>,
						<?php echo json_encode( $rolling_state['pagedates'] ); ?>
					);
				// ]]>
				</script>

			<?php
			}

			// K2 Hook
			do_action('k2_dynamic_content', $k2dynamic);
			exit;
		}
	}


	/**
	 * Register K2 scripts with WordPress' script loader
	 */
	function register_scripts() {

		// If debug mode is off, load minimized scripts, else don't... Duh!
		if ( get_option('k2optimjs') == 1 ) {

			wp_register_script('k2functions',
				get_bloginfo('template_directory') . '/js/k2.min.js',
				array('jquery'), K2_CURRENT);

			wp_register_script('k2advnav',
				get_bloginfo('template_directory') . '/js/k2.advnav.min.js',
				array('jquery', 'k2functions'), K2_CURRENT);

			wp_register_script('k2options',
				get_bloginfo('template_directory') . "/js/k2.options.min.js",
				array('jquery', 'jquery-ui-sortable'), K2_CURRENT);

		} else {

			// Third-Party Scripts
			wp_register_script('bbq',
				get_bloginfo('template_directory') . '/js/uncompressed/jquery.bbq.js',
				array('jquery'), '1.2.1', true);

			wp_register_script('hoverintent',
				get_bloginfo('template_directory') . '/js/uncompressed/jquery.hoverintent.js',
				array('jquery'), '5');

			wp_register_script('superfish',
				get_bloginfo('template_directory') . '/js/uncompressed/jquery.superfish.js',
				array('jquery', 'hoverintent'), '1.4.8');

			wp_register_script('easing',
				get_bloginfo('template_directory') . '/js/uncompressed/jquery.easing.js',
				array('jquery'), '1.3', true);

			wp_register_script('hotkeys',
				get_bloginfo('template_directory') . '/js/uncompressed/jquery.hotkeys.js',
				array('jquery'), '0.8', true);

			wp_register_script('ui',
				get_bloginfo('template_directory') . '/js/uncompressed/jquery.ui.js',
				array('jquery'), '1.8.2', true);

			// K2 Scripts
			wp_register_script('k2functions',
				get_bloginfo('template_directory') . "/js/uncompressed/k2.functions.js",
				array('jquery', 'superfish'), K2_CURRENT);

			wp_register_script('k2options',
				get_bloginfo('template_directory') . "/js/uncompressed/k2.options.js",
				array('jquery', 'jquery-ui-sortable'), K2_CURRENT);

			wp_register_script('k2slider',
				get_bloginfo('template_directory') . "/js/uncompressed/k2.slider.js",
				array('jquery'), K2_CURRENT, true);

			wp_register_script('k2livesearch',
				get_bloginfo('template_directory') . "/js/uncompressed/k2.livesearch.js",
				array('jquery', 'bbq', 'hotkeys'), K2_CURRENT);

			wp_register_script('k2advnav',
				get_bloginfo('template_directory') . "/js/uncompressed/k2.rollingarchives.js",
				array('jquery', 'bbq', 'easing', 'ui', 'k2slider', 'hotkeys', 'k2livesearch'), K2_CURRENT);
		}
	}


	/**
	 * Enqueues scripts needed by K2
	 */
	function enqueue_scripts() {
		// Load our scripts
		if ( ! is_admin() ) {
			wp_enqueue_script('k2functions');

			if ( get_option('k2advnav') != '0' )
				wp_enqueue_script('k2advnav');

			// WP 2.7 threaded comments
			if ( is_singular() && get_option('thread_comments') )
				wp_enqueue_script( 'comment-reply' );
		}
	}


	/**
	 * Helper function to load all php files in given directory using require_once
	 *
	 * @param string $dir_path directory to scan
	 * @param array $ignore list of files to ignore
	 */
	function include_all($dir_path, $ignore = false) {
		// Open the directory
		$dir = @dir($dir_path) or die( sprintf( __('Could not open required directory' , 'k2'), $dir_path ) );

		// Get all the files from the directory
		while(($file = $dir->read()) !== false) {
			// Check the file is a file, and is a PHP file
			if(is_file($dir_path . $file) and (!$ignore or !in_array($file, $ignore)) and preg_match('/\.php$/i', $file)) {
				include_once($dir_path . $file);
			}
		}

		// Close the directory
		$dir->close();
	}


	/**
	 * Helper function to search for files based on given criteria
	 *
	 * @param string $path directory to search
	 * @param array $ext file extensions
	 * @param integer $depth depth of search
	 * @param mixed $relative relative to which path
	 * @return array paths of files found
	 */
	function files_scan($path, $ext = false, $depth = 1, $relative = true) {
		$files = array();

		// Scan for all matching files
		K2::_files_scan( trailingslashit($path), '', $ext, $depth, $relative, $files);

		return $files;
	}


	/**
	 * Recursive function for files_scan
	 *
	 * @param string $base_path
	 * @param string $path
	 * @param string $ext
	 * @param string $depth
	 * @param mixed $relative
	 * @param string $files
	 * @return array paths of files found
	 */
	function _files_scan($base_path, $path, $ext, $depth, $relative, &$files) {
		if (!empty($ext)) {
			if (!is_array($ext)) {
				$ext = array($ext);
			}
			$ext_match = implode('|', $ext);
		}

		// Open the directory
		if(($dir = @dir($base_path . $path)) !== false) {
			// Get all the files
			while(($file = $dir->read()) !== false) {
				// Construct an absolute & relative file path
				$file_path = $path . $file;
				$file_full_path = $base_path . $file_path;

				// If this is a directory, and the depth of scan is greater than 1 then scan it
				if(is_dir($file_full_path) and $depth > 1 and !($file == '.' or $file == '..')) {
					K2::_files_scan($base_path, $file_path . '/', $ext, $depth - 1, $relative, $files);

				// If this is a matching file then add it to the list
				} elseif(is_file($file_full_path) and (empty($ext) or preg_match('/\.(' . $ext_match . ')$/i', $file))) {
					if ( $relative === true ) {
						$files[] = $file_path;
					} elseif ( $relative === false ) {
						$files[] = $file_full_path;
					} else {
						$files[] = str_replace($relative, '', $file_full_path);
					}
				}
			}

			// Close the directory
			$dir->close();
		}
	}


	/**
	 * Move an existing file to a new path
	 *
	 * @param string $source original path
	 * @param string $dest new path
	 * @param boolean $overwrite if destination exists, overwrite
	 * @return string new path to file
	 */
	function move_file($source, $dest, $overwrite = false) {
		return K2::_copy_or_move_file($source, $dest, $overwrite, true);
	}

	function copy_file($source, $dest, $overwrite = false) {
		return K2::_copy_or_move_file($source, $dest, $overwrite, false);
	}

	function _copy_or_move_file($source, $dest, $overwrite = false, $move = false) {
		// check source and destination folder
		if ( file_exists($source) and is_dir(dirname($dest)) ) {

			// destination is a folder, assume move to there
			if ( is_dir($dest) ) {
				if ( DIRECTORY_SEPARATOR != substr($dest, -1) )
					$dest .= DIRECTORY_SEPARATOR;

				$dest = $dest . basename($source);
			}

			// destination file exists
			if ( is_file($dest) ) {
				if ($overwrite) {
					// Delete existing destination file
					@unlink($dest);
				} else {
					// Find a unique name
					$dest = K2::get_unique_path($dest);
				}
			}

			if ($move) {
				if ( rename($source, $dest) )
					return $dest;
			} else {
				if ( copy($source, $dest) )
					return $dest;
			}
		}
		return false;
	}

	function get_unique_path($source) {
		$source = pathinfo($source);

		$path = trailingslashit($source['dirname']);
		$filename = $source['filename'];
		$ext = $source['extension'];

		$number = 0;
		while ( file_exists($path . $filename . ++$number . $ext) );

		return $path . sanitize_title_with_dashes($filename . $number) . $ext;
	}
}


// Actions and Filters
add_action( 'admin_menu', 		array('K2', 'add_options_menu') );
add_action( 'admin_init', 		array('K2', 'admin_init') );
add_filter( 'mce_css', 			array('K2', 'admin_style_visual_editor') );
add_action( 'wp_print_scripts', 	array('K2', 'enqueue_scripts') );
add_action( 'template_redirect', 	array('K2', 'dynamic_content') );
add_filter( 'query_vars', 		array('K2', 'add_custom_query_vars') );

// Decrease the priority of redirect_canonical
remove_action( 'template_redirect', 'redirect_canonical' );
add_action( 'template_redirect', 'redirect_canonical', 11 );
